<?php
namespace app\admin\controller;


use app\admin\model\ConfModel;
use app\admin\model\OrderListModel;
use app\admin\model\ShowOrderModel;
use app\admin\model\UsersModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;

Class ShowOrder extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->del_model = new ShowOrderModel();
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }


    //晒单列表
    public function show_list()
    {
        //获取列表
        $condition_rule = array(
            array(
                'field' => I('post.field'),
                'value' => I('post.keywords')
            ),
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $order = new ShowOrderModel();
        $order_list = $order->get_show_order_list($model);

        /*生成分页html*/
        $my_page = new Page($order_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);



        $this->assign('show_order_list', $order_list['data']);

        return $this->fetch();


    }



    /**
     * 修改晒单状态
     */
    public function update_show_order_status() {
        $id = I('post.id');
        $status = I('post.status');

        $m = M('show_order','sp_');

        $res = $m->where(array("id"=>$id))->setField('status',$status);
        if($res){
            if($status==1){
                $this->show_order_reward($id);
            }
            ok_return('修改成功');
        }else{
            wrong_return('修改失败');
        }
    }


    public function exec() {
        $show_order = new ShowOrderModel();
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);

        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $show_order->get_info_by_id($id);

            empty($info) && die('获取信息失败');
            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
            } else {
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }
        return $this->fetch('form');
    }

    /**
     * 批量审核晒单
     */
    public function audit_all_status() {
        $id = I('post.id');
        $status = I('post.status');
        $m = M('show_order','sp_');
        $res = $m->where(['id'=>['in',$id]])->setField('status',$status);
        if($res){
            if($status==1){
                $this->show_order_reward($id);
            }
            ok_return('修改成功');
        }else{
            wrong_return('修改失败');
        }
    }
    //晒单送礼
   private function show_order_reward($id){
        //获取用户id
       $uids=M('show_order')->field('uid')->where(['id'=>['in',$id]])->select();
       //返虚拟币
       $c_model=new ConfModel();
       $show_order_money_start=$c_model->get_conf('show_order_money_start');
       if(!empty($show_order_money_start)&&$show_order_money_start==1){
           $show_order_money=$c_model->get_conf('show_order_money');
           if(!empty($show_order_money)&&!empty($uids)){
               foreach($uids as $v){
                   if(!empty($v['uid'])){
                       M('users')->where(['id'=>$v['uid']])->setInc('money',floatval($show_order_money));
                   }
               }
           }
           M('show_order')->where(['id'=>['in',$id]])->save(['reward_money'=>floatval($show_order_money)]);
       }
       //返积分
       $show_order_score_start=$c_model->get_conf('show_order_score_start');
       if(!empty($show_order_score_start)&&$show_order_score_start==1){
           $show_order_score=$c_model->get_conf('show_order_score');
           if(!empty($show_order_score)&&!empty($uids)){
               foreach($uids as $v){
                   if(!empty($v['uid'])){
                       M('users')->where(['id'=>$v['uid']])->setInc('score',intval($show_order_score));
                   }
               }
               M('show_order')->where(['id'=>['in',$id]])->save(['reward_score'=>floatval($show_order_score)]);
           }
       }
   }

    //晒单送礼页面
    public function win_conf(){
        $c_model=new ConfModel();
        $show_order_money_start=$c_model->get_conf('show_order_money_start');
        $show_order_money=$c_model->get_conf('show_order_money');
        $this->assign("status",$show_order_money_start);
        $this->assign("money",$show_order_money);
        return $this->fetch();
    }
    //更新晒单送礼配置
    public function update_win_conf(){

        $status = I("post.status");
        $money = I("post.money");

        if(!is_numeric($status)||!is_numeric($money)||!in_array($status,[0,1,2])){
            wrong_return('请输入正确的值');
        }

        $res = M("conf")->where(array("name"=> 'show_order_money_start'))->setField("value","$status");
        $res2 = M("conf")->where(array("name" => 'show_order_money'))->setField("value","$money");
        if($res!==false&&$res2!==false){
            ok_return('修改成功');
        }else{
            wrong_return('修改失败');
        }
    }


}