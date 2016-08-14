<?php
namespace app\mobile\controller;

use app\mobile\model\OrderList;
use \think\Controller;

//订单类
class Order extends AccountBase
{

    /**
     * 确认订单
     * @return array
     */
    public function confirm_order()
    {
        $OrderList = new OrderList();
        $result = $OrderList->confirm_order(I('post.cart_id/a'));
        if (empty($result)) {
            $this->redirect('Index/index');
        }
        $this->assign('cart_info', $result['cart_info']);
        $this->assign('total_price', $result['total_price']);
        $this->assign('money', $result['money']);
        return $this->fetch();
    }


    /**
     * 提交订单
     * @return array
     */
    public function submit_order()
    {
        $OrderList = new OrderList();
        $result = $OrderList->submit_order(I('post.cart_id/a'));
        if (empty($result)) {
            $this->redirect('Index/index');
        }
        //获取配置的支付方式
        $pay_type = M('conf')->where("category = '支付配置'")->select();
        $pay_type = array_column($pay_type,'value','name');
        $this->assign('pay_type',$pay_type);
        $this->assign('order_info', $result);
        return $this->fetch();
    }


    /**
     * 个人余额支付
     * @return array
     */
    public function personal_pay()
    {
        $OrderList = new OrderList();
        $result = $OrderList->personal_pay(I('post.order_num'));
        if (empty($result)) {
            return $this->fetch('order/pay_fail');
        } else {
            return $this->fetch('order/pay_success');
        }
    }

    //延时请求
    public function check_result(){
        $transdata = isset($_GET['transdata'])?$_GET['transdata']:'';

        if ( !empty($transdata) ) {
            $data = json_decode($_GET['transdata'],true);
            $order_id = $data['cporderid'];
            $this->assign('order_id',$order_id);
            $this->assign('transdata',$transdata);
        }
        return $this->fetch();


    }

    //判断支付成功或者失败
    public function check_res(){
        $data = json_decode($_GET['transdata'],true);
        $order_id = $data['cporderid'];
        $res = M("order_list_parent")->where("order_id = ".$order_id)->find()['pay_status'];
        if($res=="3"||$res=="2"){
            //return $this->redirect('order/pay_success');
            die_json('100');
        }else{
            //return $this->redirect('order/pay_fail');
            die_json('-100');
        }
    }

    public function pay_success() {
        return $this->fetch('order/pay_success');
    }

    public function pay_fail() {
        return $this->fetch('order/pay_fail');

    }


    /**
     * 充值记录
     * @return array
     */
    public function recharge_record()
    {
        $OrderList = new OrderList();
        $result = $OrderList->recharge_record();
        $this->assign('rechargeRecord', $result);
        return $this->fetch();
    }

    /**
     * 充值页面
     * @return array
     */
    public function recharge()
    {
        //获取配置的支付方式
        $pay_type = M('conf')->where("category = '支付配置'")->select();
        $pay_type = array_column($pay_type,'value','name');
        $this->assign('pay_type',$pay_type);
        return $this->fetch();


    }


}