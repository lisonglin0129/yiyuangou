<?php
namespace app\mobile\controller;

use \think\Controller;

//支付类
class Aipay extends AccountBase
{

    public function index(){
        $order_id = I('order_num');
        if (empty($order_id)) {
            return $this->error('订单号为空');
        }

        $order_list_parent = M('order_list_parent');
        $order_list = M('order_list');

        $Users = M('users');
        $order_info = $order_list_parent->field('price,name,close_time,username,uid')->where(array('order_id' => $order_id))->find();

        if (empty($order_info)) {
            return $this->error('该订单不存在');
        }

        if ($order_info['close_time'] < time()) {
            return $this->error('该订单已过期');
        }

        //创建充值订单

        //查询用户余额，计算出充值金额
        $user_money = $Users->field('money')->where(array('id' => $order_info['uid']))->find()['money'];
        $recharge_money = round(floatval($order_info['price']) - floatval($user_money), 2);

        if ($recharge_money <= 0) {
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
            "plat_form" => "aipay"
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
        $exec_data = array(
            "TYPE" => "CHARGE",
            "EXEC_DATA" => array(
                "VALUE" => $recharge_money,
                "NEXT_ORDER" => $order_id,
                "UID" => $order_info['uid']
            )
        );
        $exec_data['MD5'] = md5($s_order_id . $exec_data['TYPE'] . json_encode($exec_data['EXEC_DATA']) . $token);

        $son_order['exec_data'] = json_encode($exec_data);

        //子订单id
        $son_order_id = $order_list->add($son_order);

        //将子订单的订单id修改到父订单order_list字段里
        $save_son_order = $order_list_parent->where(array('id' => $parent_order_id))->setField('order_list', $son_order_id);


        if (!$parent_order_id || !$son_order_id || !$save_son_order) {

            return $this->error('支付失败');

        }

        //下单等流程
        $url = $this->order($p_order_id);
        $this->redirect($url);
    }

    /**
     * 爱贝充值
     * @return array
     */
    public function recharge(){
        $money =I('post.money');
        $uid = session('user')['id'];

        $order_list = M('order_list');

        if(!is_numeric($money)||$money<=0) {
            return $this->error('充值钱为空');
        }

        //查询出用户名
        $Users = M('users');
        $username = $Users->field('username')->where(array('id' =>$uid))->find()['username'];

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
            	'username' => $username,
                "price" => $money,
                "origin" => 'mobile',
                'create_time' => time(),
                "close_time" => NOW_TIME + (int)$close_time,
                "plat_form" => "aipay"
            );
            $order_list_parent = M('order_list_parent');
            $parent_order_id = $order_list_parent->add($order_parent_data);

            //把父订单id插入到子订单id里面
            $insert_result = $order_list-> where(array('id'=>$son_order_id))->setField('pid',$parent_order_id);
        }

        if($son_order_id && $parent_order_id && $insert_result) {
            //下单等流程
            $url = $this->order($p_order_id);
            $this->redirect($url);
        }else{
            return $this->error('支付失败');
        }
    }

    /**
     * 爱贝下单
     * @return array
     */
    public function order($order_id){

        $OrderListParent = M('order_list_parent');

        $order_info = $OrderListParent->field('id,order_id,price,name,close_time,username,uid')->where(['order_id'=>$order_id])->find();
        $orderUrl = "http://ipay.iapppay.com:9999/payapi/order";
        $appkey = $this->get_conf("AIPAY_PRIVATE_KEY");
        $platpkey = $this->get_conf("AIPAY_PLAT_KEY");
        $appid = $this->get_conf("AIPAY_APPID");
        //下单接口
        /*$orderReq['appid'] = "$appid";
        $orderReq['waresid'] = 1;
        $orderReq['waresname'] = "123";
        $orderReq['cporderid'] = "16041822164353176"; //确保该参数每次 都不一样。否则下单会出问题。
        $orderReq['price'] = 0.01;   //单位：元
        $orderReq['currency'] = 'RMB';
        $orderReq['appuserid'] = 'blueshow@vip.qq.com';
        $orderReq['cpprivateinfo'] = '11qwe123r23q2321ss11';
        $orderReq['notifyurl'] = $this->get_conf("AIPAY_NOTIFY_URL");*/
        //下单接口
        $orderReq['appid'] = $appid;
        $orderReq['waresid'] = 1;
        $orderReq['waresname'] = $order_info['name'];
        $orderReq['cporderid'] = $order_id; //确保该参数每次 都不一样。否则下单会出问题。
        $orderReq['price'] = floatval($order_info['price']);   //单位：元
        $orderReq['cpprivateinfo'] = "";
        $orderReq['appuserid'] = $order_info['uid'];
        $orderReq['currency'] = 'RMB';
        $orderReq['notifyurl'] = U('pay/Notify/aipay_result',null,true,get_conf_domain());

        if (strtolower(C('OPEN_TEST_ENV')) == 'true') {
            $orderReq['price'] = 0.01;
        }

        //组装请求报文  对数据签名
        $aipay = new \app\pay\controller\Aipay();
        $reqData = $aipay->composeReq($orderReq, $appkey);

        //发送到爱贝服务后台请求下单
        $respData = $aipay->request_by_curl($orderUrl, $reqData, 'order test');

        //验签数据并且解析返回报文
        if(!$aipay->parseResp($respData, $platpkey, $respJson)) {
            $this->redirect("order/pay_fail");
        }else{
            //     下单成功之后获取 transid
            $pcurl         = "https://web.iapppay.com/h5/exbegpay?";

            $url_data['transid']=$respJson->transid;
            $url_data['cpurl']       = 'aaa';
            $url_data['redirecturl'] = U('mobile/order/check_result',null,true,get_conf_domain('WAP_WEBSITE_URL'));
            $reqData                 = $aipay->composeReq($url_data, $appkey);
            $url=$pcurl.$reqData;//我们的常连接版本 有PC 版本 和移动版本。 根据使用的环境不同请更换相应的URL:$h5url,$pcurl.
            return $url;
        }
    }

}