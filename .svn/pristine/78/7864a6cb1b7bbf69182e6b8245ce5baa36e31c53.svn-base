<?php
namespace app\mobile\controller;
use \think\Controller;

//微信支付类
class Weixin extends AccountBase{


    /**
     * 微信支付
     * @return mixed
     */
    public function pay() {

        require_once APP_PATH.'lib/Wxpay/lib/WxPay.Api.php';
        require_once APP_PATH."lib/Wxpay/example/WxPay.JsApiPay.php";

        //所有关于订单的操作都和支付宝提交订单那里一模一样，代码冗余，不过在不同的控制器里，先这样吧
        //根据微信支付方法，需要调用两次该方法，第一次用于获取用户openid，第二次进行支付流程.


        $order_id =I('post.order_num');
        //判断是否是支付宝与余额支付一起支付
        $pay_type = (int)I('post.pay_type');
        $use_balance = (int)I('post.use_balance');


        $data = array(
            'order_id' => $order_id,
            'pay_type' => $pay_type,
            'use_balance' => $use_balance
        );

        $data = urlencode(json_encode($data));

        //①、获取用户openid
        $tools = new \JsApiPay();

        $openId = '';


        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            //$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
            $baseUrl = U('mobile/weixin/pay',null,true,true);
            $url = $tools->__CreateOauthUrlForCode($baseUrl);
            $url = str_replace("STATE", $data, $url);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $state = $_GET['state'];
            $openId = $tools->getOpenidFromMp($code);
        }

        $data_arr = json_decode(urldecode($state),true);



        $order_id =$data_arr['order_id'];
        //判断是否是支付宝与余额支付一起支付
        $pay_type = $data_arr['pay_type'];
        $use_balance = $data_arr['use_balance'];





        $order_list_parent = M('order_list_parent');
        $order_list = M('order_list');

        $Users = M('users');

        $order_info = $order_list_parent->field('price,name,close_time,username,uid')->where('order_id ='.$order_id)->find();

        if(empty($order_info)) {
            return $this->error('该订单不存在');
        }

        if($order_info['close_time'] < time()) {
            return $this->error('该订单已过期');
        }

        //定义支付变量
        $pay_order_id = $order_id;
        $pay_subject = $order_info['name'];
        $pay_money = $order_info['price'];



        if($pay_type == 3 && $use_balance == 1) {
            //创建充值订单

            //查询用户余额，计算出充值金额
            $user_money = $Users->field('money')->where('id = '.$order_info['uid'])->find()['money'];
            $recharge_money = round(floatval($order_info['price'])-floatval($user_money),2);

            if($recharge_money <= 0) {
                return $this->error('提交订单失败');

            }

            $promote_percent = $this->get_conf("PROMOTE_PRECENT");
            $close_time = $this->get_conf('ORDER_CLOSE_TIME');
            $token = $this->get_conf('TOKEN_ACCESS');


            //创建父订单

            $p_order_id = create_order_num();
            $order_parent_data = array(
                "order_id" => $p_order_id,
                "name" => '用户' . $order_info['username'] . '充值' . $recharge_money . '元',//商品名称
                "uid" => $order_info['uid'],
                "num" => 1,
                "order_list" => '',
                'username' => $order_info['username'],
                "bus_type" => 'recharge',
                "price" => $recharge_money,
                "origin" => 'mobile',
                'create_time' => time(),
                "close_time" => NOW_TIME + (int)$close_time,
                "plat_form" => "wxpay"
            );

            $parent_order_id = $order_list_parent->add($order_parent_data);

            //创建子订单
            //子订单信息
            $s_order_id = create_order_num();
            $son_order = array(
                "pid" => $parent_order_id,
                "order_id" => $s_order_id,
                "goods_id" => 0,//商品id
                "uid" => $order_info['uid'],
                "goods_name" => '用户' . $order_info['username'] . '充值' . $recharge_money . '元',//商品名称
                "nper_id" => null,//期数id
                "bus_type" => 'recharge',
                "num" => 1,//数量
                "exec_data" => null,//回调执行参数
                "money" => $recharge_money,//子订单总价
                "promoter_pos" => $promote_percent,//当前推广比例
                "luck_status" => "false",
                "create_time" => time()
            );

            //生成exec_data字段
            $exec_data =array(
                "TYPE"=>"CHARGE",
                "EXEC_DATA"=>array(
                    "VALUE"=> $recharge_money,
                    "NEXT_ORDER" => $order_id,
                    "UID" => $order_info['uid']
                )
            );
            $exec_data['MD5'] = md5($s_order_id.$exec_data['TYPE'].json_encode($exec_data['EXEC_DATA']).$token);

            $son_order['exec_data'] = json_encode($exec_data);

            //子订单id
            $son_order_id = $order_list->add($son_order);

            //将子订单的订单id修改到父订单order_list字段里
            $save_son_order = $order_list_parent->where('id ='.$parent_order_id)->setField('order_list',$son_order_id);


            if(!$parent_order_id || !$son_order_id || !$save_son_order) {

                return $this->error('支付失败');

            }

            //重新定义支付变量
            $pay_order_id = $p_order_id;
            $pay_subject = '用户' . $order_info['username'] . '的订单';
            $pay_money = $recharge_money;

        }


        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($pay_subject);
        $input->SetAttach($pay_subject);
        $input->SetOut_trade_no($pay_order_id);
//        $input->SetTotal_fee($pay_money*100);
//        $input->SetBody('test');
//        $input->SetAttach('test');
//        $input->SetOut_trade_no('12324235fgd45');
        $recharge_money = floatval($pay_money)*100;
        if(strtolower(C('OPEN_TEST_ENV'))=='true'){
            $recharge_money = 1;
        }
        $input->SetTotal_fee($recharge_money);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
//        $input->SetGoods_tag("test");
        //$input->SetNotify_url(U('mobile/other_users/notify',null,true,true));
        $input->SetNotify_url(U('pay/notify/wxpay',null,true,true));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);


        $jsApiParameters = $tools->GetJsApiParameters($order);
//获取共享收货地址js函数参数
//        $editAddress = $tools->GetEditAddressParameters();

        //dump($jsApiParameters);
        $this->assign('jsApiParameters',$jsApiParameters);
//        $this->assign('pay_money',$pay_money*100);
        $this->assign('pay_money',$pay_money);
//        $this->assign('editAddress',$editAddress);

        return $this->fetch();

    }

    public function pay_success() {
        $this->assign('plat','weichat');    
        return $this->fetch('order/pay_success');
    }

    public function pay_fail() {
        return $this->fetch('order/pay_fail');
    }

    /*
     * 微信充值
     */
    public function recharge(){
        $money =I('post.money');
        $uid = session('user')['id'];

        $order_list = M('order_list');


        if(empty($money)||$money<=0) {
            return $this->error('充值钱为空');
        }

        //查询出用户名
        $Users = M('users');
        $username = $Users->field('username')->where('id ='.$uid)->find()['username'];

        if(empty($username)) {
            return array(
                'code' => '167',
                'status' => 'fail',
                'message' => '该用户不存在'
            );
        }


        $promote_precent = $this->get_conf("PROMOTE_PRECENT");
        $close_time = $this->get_conf('ORDER_CLOSE_TIME');
        $token = $this->get_conf('TOKEN_ACCESS');

        //子订单信息
        $order_id = create_order_num();
        $son_order = array(
            "order_id" => $order_id,
            "goods_id" => 0,//商品id
            "uid" => $uid,
            "goods_name" => '用户' . $username . '充值' . $money . '元',//商品名称
            "nper_id" => null,//期数id
            "bus_type" => 'recharge',
            "num" => 1,//数量
            "exec_data" => null,//回调执行参数
            "money" => $money,//子订单总价
            "promoter_pos" => $promote_precent,//当前推广比例
            "luck_status" => "false",
            'create_time' => time()
        );

        //生成exec_data字段
        $exec_data =array(
            "TYPE"=>"CHARGE",
            "EXEC_DATA"=>array(
                "VALUE"=> $money,
                "UID" => $uid
            )
        );
        $exec_data['MD5'] = md5($order_id.$exec_data['TYPE'].json_encode($exec_data['EXEC_DATA']).$token);

        $son_order['exec_data'] = json_encode($exec_data);

        //子订单id
        $son_order_id = $order_list->add($son_order);

        $parent_order_id = NULL;
        $p_order_id = NULL;
        $insert_result = NULL;


        if($son_order_id) {
            //创建支付父级订单
            $p_order_id = create_order_num();
            $order_parent_data = array(
                "order_id" => $p_order_id,
                "name" => '用户' . $username . '充值' . $money . '元',//商品名称
                "uid" => $uid,
                "num" => 1,
                "order_list" => $son_order_id,
                "bus_type" => 'recharge',
                "price" => $money,
                "origin" => 'mobile',
                'create_time' => time(),
                "close_time" => NOW_TIME + (int)$close_time,
                "plat_form" => "wxpay"
            );
            $order_list_parent = M('order_list_parent');
            $parent_order_id = $order_list_parent->add($order_parent_data);

            //把父订单id插入到子订单id里面
            $insert_result = $order_list-> where('id='.$son_order_id)->setField('pid',$parent_order_id);
        }

        if($son_order_id && $parent_order_id && $insert_result) {
            session('jsapi_bill_data',$order_parent_data);
            $this->redirect(U('weixin/jsapi_bill'));
        }else{
            return $this->error('支付失败');
        }
    }


    public function jsapi_bill($order=null){

        require_once APP_PATH.'lib/Wxpay/lib/WxPay.Api.php';
        require_once APP_PATH."lib/Wxpay/example/WxPay.JsApiPay.php";

        //①、获取用户openid
        $tools = new \JsApiPay();

        $openId = '';
        if (!isset($_GET['code'])){
            //触发微信返回code码
            //$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
            $baseUrl = U('mobile/weixin/jsapi_bill',null,true,true);
            $url = $tools->__CreateOauthUrlForCode($baseUrl);
            //$url = str_replace("STATE", '', $url);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $state = $_GET['state'];
            $openId = $tools->getOpenidFromMp($code);
        }

        $bill_data = session('jsapi_bill_data');
        if(empty($bill_data)){
            return $this->error('支付失败');
        }
        session('jsapi_bill_data',null);

        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($bill_data['name']);
        $input->SetAttach($bill_data['name']);
        $input->SetOut_trade_no($bill_data['order_id']);
//        $input->SetTotal_fee($pay_money*100);
//        $input->SetBody('test');
//        $input->SetAttach('test');
//        $input->SetOut_trade_no('12324235fgd45');
        $recharge_money = floatval($bill_data['price'])*100;
        if(strtolower(C('OPEN_TEST_ENV'))=='true'){
            $recharge_money = 1;
        }
        $input->SetTotal_fee($recharge_money);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
//        $input->SetGoods_tag("test");
        //$input->SetNotify_url(U('mobile/other_users/notify',null,true,true));
        $input->SetNotify_url(U('pay/notify/wxpay',null,true,true));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);


        $jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
//        $editAddress = $tools->GetEditAddressParameters();

        //dump($jsApiParameters);
        $this->assign('jsApiParameters',$jsApiParameters);
//        $this->assign('pay_money',$pay_money*100);
        $this->assign('pay_money',$bill_data['price']);
//        $this->assign('editAddress',$editAddress);

        return $this->fetch('weixin/pay');
    }




    /**
     * curl
     * @param $url
     * @return mixed
     */
    private  function curl($url) {
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);

        return $output;
    }

}