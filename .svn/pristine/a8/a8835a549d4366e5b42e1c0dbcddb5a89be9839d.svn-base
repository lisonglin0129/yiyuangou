<?php
namespace app\admin\model;

use Think\Db;
use think\model\Adv;
use app\core\model\LogModel as core_log;

Class WithdrawsCashModel extends Adv
{
    public $withdraw_model;
    private $core_log;

    public function __construct()
    {
        parent::__construct();
        $this->withdraw_model = M('withdraws_cash',null);
        $this->core_log = new core_log();
    }

    //获取提现列表
    public function get_withdraw_list($post)
    {

        $m = M('withdraw_cash', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  w.* ,a.account_type,a.bank_no,a.name account_name
        FROM  sp_withdraws_cash w
        LEFT JOIN sp_account a
        ON a.id = w.account_id
        WHERE status <> '-1' " . $post->wheresql .
            " ORDER BY w.id desc " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        if(!empty($info)) {
            foreach($info as $key=>$value) {
                if($value['audit_time']) {
                    $info[$key]['audit_time'] = date('Y-m-d H:i:s',$value['audit_time']);
                }
                $info[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);

            }
        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }


    //修改提现状态
    public function update_withdraw_status($id,$status) {
        if(empty($id) || empty($status)) {
            return array(
                'code' => '-1',
                'msg' => '请求参数为空'
            );
        }

        $withdraw_info = $this->withdraw_model->field('uid,money,need,audit_time')->where(array('id' =>$id))->find();

        $pre_data = M("users")->field("id,username,money,score,cash")->where(array("id"=>$withdraw_info['uid']))->find();
        if(empty($withdraw_info)) {
            return array(
                'code' => '-1',
                'msg' => '不存在该提现'
            );
        }
        if(!empty($withdraw_info['audit_time'])){
            return array(
                'code'=>'-1',
                'msg'=>'该提现已被处理'
            );
        }

        if($status == -2) {
            $update_res = $this->withdraw_model->where(array('id' =>$id))->setField('status',$status);
            $fan_res=M('users')->where(['id'=>$withdraw_info['uid']])->setInc('cash',floatval($withdraw_info['money'])+intval($withdraw_info['need']));
            //禁用状态退还金额
            if($update_res !== false&&$fan_res!==false){
                $after_data  = M("users")->field("money,score,cash")->where(array("id"=>$withdraw_info['uid']))->find();
                $this->core_log->record($pre_data,$after_data,5,1);
              return array(
                  'code'=>1,
                  'msg'=>'修改成功'
              );
            }
        }
        if($status == 2) {
            $users = M('users',null);
            //查询用户余额
            $uid = $withdraw_info['uid'];
            $user_money = $users->field('cash')->where(array('id' =>$uid))->find()['cash'];

            $user_money = (string)$user_money;
            $withdraw_money = (string)$withdraw_info['money'];

            if(floatval($user_money)<floatval($withdraw_money)) {
                return array(
                    'code' => '-1',
                    'msg' => '余额不足'
                );
            }

            //进行事务操作

            $this->startTrans();
            //提现前money＝用户当前余款＋提现金额＋手续费
           // $last_money = round(floatval($user_money)-floatval($withdraw_money)-intval($withdraw_info['need']),2);

            //改变用户余额
            //$user_res = $users->where('id = '.$uid)->setField('cash',$last_money);
            $pre_money=round(floatval($user_money)+floatval($withdraw_money)+intval($withdraw_info['need']),2);
            //改变提现表
            $save_data = array();

            $save_data['id'] = $id;
            $save_data['pre_money'] =$pre_money ;
            $save_data['last_money'] = $user_money;
            $save_data['status'] = 2;
            $save_data['audit_time'] = time();
            $withdraw_res = $this->withdraw_model->save($save_data);

            if($withdraw_res) {
                $this->commit();

                $after_data  = M("users")->field("money,score,cash")->where(array("id"=>$withdraw_info['uid']))->find();
                $this->core_log->record($pre_data,$after_data,5,1);
                return array(
                    'code' => '1',
                    'msg' => '成功'
                );
            }else{
                $this->rollback();
                $after_data  = M("users")->field("money,score,cash")->where(array("id"=>$withdraw_info['uid']))->find();
                $this->core_log->record($pre_data,$after_data,5,0);

                return array(
                    'code' => '-1',
                    'msg' => '修改失败'
                );
            }

        }

        $after_data  = M("users")->field("money,score,cash")->where(array("id"=>$withdraw_info['uid']))->find();
        $this->core_log->record($pre_data,$after_data,5,0);
        return array(
            'code' => '-1',
            'mag' => '修改失败'
        );


    }

    /**
     * 批量修改
     * @param $id
     * @param $status
     * @return bool
     */
    public function update_more_withdraw_status($id,$status) {
        if(empty($id) || empty($status)) {
            return array(
                'code' => '-1',
                'msg' => '缺少参数'
            );
        }

        $success_flag = true;

        $id_arr = explode(',',$id);

        foreach($id_arr as $key=>$value) {
            $res = $this->update_withdraw_status($value,$status);
            if($res['code'] == '-1') {
                $success_flag = true;
            }
        }
        return $success_flag;
    }






}