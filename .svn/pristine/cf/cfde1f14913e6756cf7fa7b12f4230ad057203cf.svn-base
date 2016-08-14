<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/24
 * Time: 14:14
 */
namespace app\core\model;
use think\model\Adv;
use app\core\model\LogModel as core_log;

class WithdrawsCashModel extends Adv{
    private $m;
    private $core_log;
    public function __construct(){
        parent::__construct();
        $this->core_log = new core_log();
        $this->m=M('withdraws_cash','sp_');
    }
    //提现申请
    public function add_withdraws_cash($get){
        $uid = !empty($get['uid'])?$get['uid']:get_user_id();

        $pre_data = M("users")->field("id,username,score,money,cash")->where(array("id"=>$uid))->find();
        $data=[
            'uid'=>$uid,
            'username'=>M('users')->where(['id'=>$uid])->getField('username'),
            'money'=>!empty($get['money'])?$get['money']:'',
            'account_id'=>!empty($get['account_id'])?$get['account_id']:'',
            'need'=>!empty($get['money'])?ceil($get['money']/100):'',
            'create_time'=>time()
        ];
        $total=intval($data['money'])+intval($data['need']);
        $cash=M('users')->where(['id'=>$data['uid']])->getField('cash');
        //提现金额大于余款退出
        if($total>floatval($cash)){
            return false;
        }
        //添加日志
        M('log','')->add(['user'=>$uid,'log'=>'申请提现金额：'.intval($data['money']).' 手续费：'.intval($data['need']),'create_time'=>time()]);

        $this->startTrans();

        $r1=$this->m->add($data);
        //扣除用户先进余款
        !empty($data['uid'])&&$r2=M('users')->where(['id'=>$data['uid'],'cash'=>['egt',$total]])->setDec('cash',$total);
        if($r1&&$r2){
            $this->commit();
            $after_data = M("users")->field("score,money,cash")->where(array("id"=>$uid))->find();
            $this->core_log->record($pre_data,$after_data,4,1);
            return true;
        }else{
            $this->rollback();
            $after_data = M("users")->field("score,money,cash")->where(array("id"=>$uid))->find();
            $this->core_log->record($pre_data,$after_data,4,2);
            return false;
        }
    }
    public function get_withdraws_cash_list($post){
        $sql = "SELECT w.*,a.bank_no,a.bank_name,a.account_type
        FROM  sp_withdraws_cash w
        LEFT JOIN sp_account a ON a.id=w.account_id
        WHERE  w.uid=".get_user_id() . $post->wheresql .
            " ORDER BY w.create_time DESC " . $post->limitData;
        $info = $this->m->query($sql);

        //用户隐私处理
        $info = array_map(array($this,'privacy'), $info);

        $rt["data"] = $info;
        $rt["count"] = $this->m->alias('w')->where('w.uid='.get_user_id().$post->wheresql)->count();
        return $rt;
    }

    //用户隐私处理

    private function privacy($info)
    {
        $bank_no = $info['bank_no'];
        $str = substr($bank_no,3,strlen($bank_no)-6 );
        $len = strlen($str);
        $rep = str_repeat('*',$len);
        $info['bank_no'] = str_replace($str,$rep , $bank_no);
        return $info;

    }

    //提现删除
    public function withdraws_del($id){
        if(!empty($id)){
            $withdraw_info = $this->m->field('uid,money,need')->where(array('id' => $id))->find();
            M('users')->where(['id'=>$withdraw_info['uid']])->setInc('cash',floatval($withdraw_info['money'])+intval($withdraw_info['need']));
            return $this->m->where(['id'=>$id])->delete();
        }else{
            return false;
        }
    }
    //获取提现记录页数
    public function get_withdraw_pages(){
        $total=$this->m->where(['uid'=>get_user_id()])->count();
        return ceil($total/10);
    }
    public function m_withdraw_list(){
        return $this->m->where(['uid'=>get_user_id()])->limit(10)->order('create_time desc')->select();
    }
    public function m_ajax_withdraw_list($offset=0,$start='',$end=''){
        $where=[];
        if($start){
            $where['create_time']=['egt',strtotime($start)];
        }
        if($end){
            $where['create_time']=['elt',strtotime($end)];
        }
        $where['uid']=get_user_id();
        $list=$this->m->where($where)->limit($offset,10)->order('create_time desc')->select();
        return $list;
    }
    //获取用户冻结金额
    public function get_verified_money($uid=""){
        if(empty($uid)){
            $uid=get_user_id();
        }
        return $this->m->where(['uid'=>$uid,'status'=>1])->sum('money+need');
    }
    //api获取用户冻结金额
    public function api_verified_money($uid){
        return $this->m->where(['uid'=>$uid,'status'=>1])->sum('money+need');
    }

    //获取用户提现记录
    public function get_extract_list($start,$end,$page="0",$uid){
        if(empty($uid)){
            return "1";
        }

        if(!empty($start)&&!empty($end)){
            $map['create_time'] = array("GT",$start);
            $map['create_time'] = array('LT',$end);
        }
        $map['uid'] = $uid;

        $res = $this->m->where($map)->limit("$page,10")->select();
        return $res;
    }

}