<?php
namespace app\pay\controller;

use app\core\controller\Common;
use app\pay\model\PublicModel;
use think\Controller;

/**
 * 支付宝web处理类
 */
Class Alipay extends Common
{
    private $m_pub;
    protected $money;


    public function __construct()
    {
        parent::__construct();
        $this->m_pub = new PublicModel();
    }

    /**
     * 支付接口入口
     * @order_id request 订单id
     * @timestamp request 时间戳
     * @sign request 签名
     */
    public function index()
    {
        $order_id = I("request.order_id", null);
        $timestamp = I("request.timestamp", null);
        $sign = I("request.sign", null);
		

        $r = $this->m_pub->auth($order_id, $timestamp, $sign);

        $info = $this->m_pub->get_p_order_info($order_id);
        
        if ($r["code"] !== "1") {
            if (IS_AJAX) {
                die_result('-100', "本请求失效或超时,请重新发起请求" . $sign);
            } else {
                $this->redirect('/');
            }
        }
        $this->construct_param($order_id);
        die($this->fetch('alipay/go_alipay'));
    }

    /**构建提交参数信息*/
    private function construct_param($order_id)
    {
        $info = $this->m_pub->get_p_order_info($order_id);
        $price = empty(floatval($info["price"])) ? 100 : round(floatval($info["price"]), 2);

        (!is_array($info) || empty($info)) && die_result("-100", "提交参数不正确");
        extract($info);
        $data = array(
            //基本参数
            "service" => 'create_direct_pay_by_user',//接口名称****
            "partner" => $this->m_pub->get_conf('ALIPAY_PARTNER'),//合作者身份ID****
            "_input_charset" => 'utf-8',//参数编码字符集****
            "notify_url" => U('pay/notify/alipay',null,true,get_conf_domain()),//$this->m_pub->get_conf("ALIPAY_NOTIFY_URL"),//服务器异步通知页面路径
            "return_url" => U('yyg/pay/pay_result',null,true,get_conf_domain()),//$this->m_pub->get_conf("ALIPAY_RETURN_URL"),//页面跳转同步通知页面路径
            "error_notify_url" => U('pay/error',null,true,get_conf_domain()),//$this->m_pub->get_conf("ALIPAY_ERROR_NOTIFY_URL"),//请求出错时的通知页面路径
            //业务参数
            "out_trade_no" => $order_id,//商户网站唯一订单号****
            "subject" => $info['name'],//商品名称****
            "payment_type" => empty($payment_type) ? "1" : $payment_type,//支付类型****
            "total_fee" => empty($price) ? "10000.00" : $price,//交易金额精确到小数点后两位。****
            "seller_id" => $this->m_pub->get_conf('ALIPAY_PARTNER'),//卖家支付宝用户号****
            "seller_email" => $this->m_pub->get_conf('ALIPAY_SELLER_EMAIL'),//卖家支付宝账号****
            "seller_account_name" => $this->m_pub->get_conf('ALIPAY_SELLER_EMAIL'),//卖家别名支付宝账号****
//            "price" => $price,//商品单价****
//            "quantity" => $quantity,//购买数量
//            "body" => $body,//商品描述****
//            "show_url" => $show_url,//商品展示网址
            "it_b_pay" =>'2h',//超时时间
//            "qr_pay_mode" => empty($qr_pay_mode) ? '3' : $this->m_pub->$this->get_conf('QR_PAY_MODE'),//扫码支付方式
        );

        if (strtolower(C('OPEN_TEST_ENV')) == 'true') {
            $data['total_fee'] = '0.01';
        }

        $url = $this->create_pay_url($data);
        $this->go_alipay($url);
    }

    /**跳转到支付宝*/
    private function go_alipay($url)
    {
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
    }

    /**md5加密签名参数*/
    private function md5_sign($post)
    {
        $data = paraFilter($post);//删除全部空值
        $data = argSort($data);
        $str = createLinkstring($data);
        $str = trim($str, '&') . $this->m_pub->get_conf('ALIPAY_MD5_TOKEN');
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
        echo '<pre/>';
		
        $data = $arr["param"];
        $data["sign"] = $arr['sign'];
        $data['sign_type'] = 'MD5';
        // 升序排列数组

        $url = 'https://mapi.alipay.com/gateway.do?' . createLinkstring($data);

        return $url;
    }

}