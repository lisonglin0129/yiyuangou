<?php
namespace app\mobile\controller;

use \think\Controller;

//支付类
class Alipay extends AccountBase
{

    /**
     * 个人中心余额充值表单提交
     * @return mixed
     */
    public function personal_charge()
    {
        $money = I('post.money');
        $uid = session('user')['id'];

        $order_list = M('order_list');


        if (empty($money)||$money<=0) {
            return $this->error('充值钱为空');
        }

        //查询出用户名
        $Users = M('users');
        $username = $Users->field('username')->where(array('id' => $uid))->find()['username'];

        if (empty($username)) {
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
        $exec_data = array(
            "TYPE" => "CHARGE",
            "EXEC_DATA" => array(
                "VALUE" => $money,
                "UID" => $uid
            )
        );
        $exec_data['MD5'] = md5($order_id . $exec_data['TYPE'] . json_encode($exec_data['EXEC_DATA']) . $token);

        $son_order['exec_data'] = json_encode($exec_data);

        //子订单id
        $son_order_id = $order_list->add($son_order);

        $parent_order_id = NULL;
        $p_order_id = NULL;
        $insert_result = NULL;


        if ($son_order_id) {
            //创建支付父级订单
            $p_order_id = create_order_num();
            $order_parent_data = array(
                "order_id" => $p_order_id,
                "name" => '用户' . $username . '充值' . $money . '元',//商品名称
                'username' => $username,
                "uid" => $uid,
                "num" => 1,
                "order_list" => $son_order_id,
                "bus_type" => 'recharge',
                "price" => $money,
                "origin" => 'mobile',
                'create_time' => time(),
                "close_time" => NOW_TIME + (int)$close_time,
                "plat_form" => "alipay"
            );
            $order_list_parent = M('order_list_parent');
            $parent_order_id = $order_list_parent->add($order_parent_data);

            //把父订单id插入到子订单id里面
            $insert_result = $order_list->where(array('id'=> $son_order_id))->setField('pid', $parent_order_id);
        }

        if ($son_order_id && $parent_order_id && $insert_result) {


            //构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => 'alipay.wap.create.direct.pay.by.user',
                "partner" => $this->get_conf('ALIPAY_PARTNER'),
                "seller_id" => $this->get_conf('ALIPAY_PARTNER'),
                "payment_type" => "1",
                "notify_url" => $this->get_conf('ALIPAY_NOTIFY_URL'),
                "return_url" => $this->get_conf('ALIPAY_MOBILE_RETURN_URL'),
                "_input_charset" => 'utf-8',
                "out_trade_no" => $p_order_id,
                "subject" => '用户' . $username . '充值' . $money . '元',
                "total_fee" => $money,
                //            "total_fee"	=> '0.01',
                "show_url" => 'www.baidu.com',
                "body" => '',
            );

            //建立请求
            $url = $this->create_pay_url($parameter);
            $this->go_alipay($url);

        } else {
            return $this->error('支付失败');
        }
    }

    /**
     * 商品购买表单提交
     * 不限制数据提交方式，用于IOS使用
     */
    public function alipay_api()
    {


        $order_id = I('order_num');
        if (empty($order_id)) {
            return $this->error('订单号为空');
        }


        $order_list_parent = M('order_list_parent');
        $order_list = M('order_list');

        $Users = M('users');
        $order_info = $order_list_parent->field('price,name,close_time,username,uid,bus_type')->where(array('order_id' => $order_id))->find();

        if (empty($order_info)) {
            return $this->error('该订单不存在');
        }

        if ($order_info['close_time'] < time()) {
            return $this->error('该订单已过期');
        }

        //定义支付变量
        $pay_order_id = $order_id;
        $pay_subject = $order_info['name'];
        $pay_money = $order_info['price'];

        //如果为充值订单则不创建新订单
        if ($order_info['bus_type'] != 'recharge') {
            //判断是否是支付宝与余额支付一起支付
            $pay_type = (int)I('pay_type');
            $use_balance = (int)I('use_balance');

            if ($pay_type == 3 && $use_balance == 1) {
                //创建充值订单

                //查询用户余额，计算出充值金额
                $user_money = $Users->field('money')->where(array('id' => $order_info['uid']))->find()['money'];
                $recharge_money = round(floatval($order_info['price']) - floatval($user_money), 2);

                if ($recharge_money <= 0) {
                    return $this->error('提交订单失败');

                }
            } else {
                $recharge_money = round(floatval($order_info['price']), 2);
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
                "origin" => 'IOS',
                'create_time' => time(),
                "close_time" => NOW_TIME + (int)$close_time,
                "plat_form" => "alipay"
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

            //重新定义支付变量
            $pay_order_id = $p_order_id;
            $pay_subject = '用户' . $order_info['username'] . '的订单';
            $pay_money = $recharge_money;
        }


        $data = array(
            //基本参数
            "service" => 'alipay.wap.create.direct.pay.by.user',//接口名称****
            "partner" => $this->get_conf('ALIPAY_PARTNER'),//合作者身份ID****
            "_input_charset" => 'utf-8',//参数编码字符集****
            "notify_url" => $this->get_conf("ALIPAY_NOTIFY_URL"),//服务器异步通知页面路径
            "return_url" => $this->get_conf("ALIPAY_MOBILE_RETURN_URL"),//页面跳转同步通知页面路径
            "error_notify_url" => $this->get_conf("ALIPAY_ERROR_NOTIFY_URL"),//请求出错时的通知页面路径
            //业务参数
            "out_trade_no" => $pay_order_id,//商户网站唯一订单号****
            "subject" => $pay_subject,//商品名称****
            "payment_type" => "1",//支付类型****
            "total_fee" => empty($pay_money) ? "10000.00" : $pay_money,//交易金额精确到小数点后两位。****
            "seller_id" => $this->get_conf('ALIPAY_PARTNER'),//卖家支付宝用户号****
            "seller_email" => $this->get_conf('ALIPAY_SELLER_EMAIL'),//卖家支付宝账号****
            "seller_account_name" => $this->get_conf('ALIPAY_SELLER_EMAIL'),//卖家别名支付宝账号****
//            "price" => $price,//商品单价****
//            "quantity" => $quantity,//购买数量
//            "body" => $body,//商品描述****
//            "show_url" => $show_url,//商品展示网址
            "it_b_pay" => '2h',//超时时间
        );
        if (strtolower(C('OPEN_TEST_ENV')) == 'true') {
            $data['total_fee'] = '0.01';
        }

        //建立请求
        $url = $this->create_pay_url($data);

        $this->go_alipay($url);

    }

    /**
     * 同步通知
     */
    public function return_url()
    {
        $order_list_parent = M('order_list_parent');
        $order_list = M('order_list');

        $order_id = I('get.out_trade_no');

        if (empty($order_id)) {
            return $this->fetch('order/pay_fail');
        }

        //查询订单pay_status状态是否为3
        $parent_order_info = $order_list_parent->field('id,origin,pay_status')->where(array('order_id' =>$order_id))->find();
        //检测订单是否为ios订单
        if (strtolower($parent_order_info['origin']) == 'ios') {
            return $this->fetch('order/return_ios');
        }

        if ($parent_order_info['pay_status'] == '3') {
            //判断子订单是否有next_order，并判断其状态
            $exec_data = $order_list->field('exec_data')->where(array('pid' => $parent_order_info['id']))->find()['exec_data'];
            $exec_data = json_decode($exec_data, true);

            if (isset($exec_data['EXEC_DATA']['NEXT_ORDER']) && !empty($exec_data['EXEC_DATA']['NEXT_ORDER'])) {
                $next_order = $exec_data['EXEC_DATA']['NEXT_ORDER'];
                $next_order_status = $order_list_parent->field('pay_status')->where(array('order_id' => $next_order))->find();
                if ($next_order_status) {
                    if ($next_order_status['pay_status'] != 3) {
                        return $this->fetch('order/pay_fail');
                    }
                }
            }

        } else {
            return $this->fetch('order/pay_fail');
        }

        return $this->fetch('order/pay_success');
    }


    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
    public function build_request_form($url, $button_name)
    {
        //待请求参数数组

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='$url' method='get'>";


        $data = explode("?", $url);
        $url = $data[0];
        $param = $data[1];

        //构造form表单的数组
        $param = explode("&", $param);

        foreach ($param as $k => $v) {
            $arr = explode("=", $v);
            $sHtml .= "<input type='hidden' name='" . $arr[0] . "' value='" . $arr[1] . "'/>";
        }


        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit'  value='" . $button_name . "' style='display:none;'></form>";

        $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }


    /**跳转到支付宝*/
    private function go_alipay($url)
    {
        //微信防屏蔽
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $this->assign("url", $url);
            die($this->fetch('iframe_pay'));
        }
        $data = explode("?", $url);
        $url = $data[0];
        $param = $data[1];

        //构造form表单的数组
        $param = explode("&", $param);
        $param_const = array();
        foreach ($param as $k => $v) {
            $arr = explode("=", $v);
            $param_const[$arr[0]] = $arr[1];
        }

        $this->assign("url", $url);
        $this->assign("list", $param_const);
        die($this->fetch('form_submit'));
    }

    /**md5加密签名参数*/
    private function md5_sign($post)
    {
        $data = paraFilter($post);//删除全部空值
        $data = argSort($data);
        $str = createLinkstring($data);
        $str = trim($str, '&') . $this->get_conf('ALIPAY_MD5_TOKEN');
        $rt = array(
            'param' => $data,
            'sign' => md5($str)
        );
        return $rt;
    }

    /**生成付款链接*/
    private function create_pay_url($post)
    {
        $arr = $this->md5_sign($post);

        $data = $arr["param"];
        $data["sign"] = $arr['sign'];
        $data['sign_type'] = 'MD5';
        // 升序排列数组
        argSort($data);
        $url = 'https://mapi.alipay.com/gateway.do?' . createLinkstring($data);

        return $url;
    }
}