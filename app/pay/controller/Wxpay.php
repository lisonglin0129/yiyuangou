<?php
namespace app\pay\controller;

use app\core\model\CommonModel;
use app\pay\model\PublicModel;
use think\Controller;

/**
 * 微信处理类
 */
Class WxPay extends Controller
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
        $order_id = I("request.order_id",null);
        $timestamp = I("request.timestamp",null);
        $sign = I("request.sign",null);
        $r = $this->m_pub->auth($order_id, $timestamp, $sign);
        if($r["code"]!=="1"){
            if(IS_AJAX){
                die_result('-100',"本请求失效或超时,请重新发起请求".$sign);
            }else{
                $this->redirect('/');
            }
        }
        //微信统一订单
        import("app.lib.Wxpay.WxPayApi",null,'.php');

//        $m_conf = new CommonModel();
//        $conf = $m_conf->get_conf_by_keys('WXPAY\_%');
//        if(!key_exists('wxpay_appid',$conf) || empty($conf['wxpay_appid'])
//            || !key_exists('wxpay_mchid',$conf) || empty($conf['wxpay_mchid'])
//            || !key_exists('wxpay_key',$conf) || empty($conf['wxpay_key']))
//            return ['code' => '180',
//                'status' => 'fail',
//                'message' => '服务器接口无效配置'];

        $info = $this->m_pub->get_p_order_info($order_id);
        (!is_array($info) || empty($info)) && die_result("-100", "提交参数不正确");

        $order = new \WxPayUnifiedOrder();
        $order->SetAppid(\WxPayConfig::getConf('APPID'));
        $order->SetMch_id(\WxPayConfig::getConf('MCHID'));

        //商户订单号
        //$out_trade_no = date('YmdHis').rand(100000,999999);
        $order->SetOut_trade_no($order_id);
        //商品描述
        $order->SetBody($info['name']);
        //总金额
        $recharge_money = floatval($info['price'])*100;
        if(strtolower(C('OPEN_TEST_ENV'))=='true'){
            $recharge_money = 1;
        }
        $order->SetTotal_fee($recharge_money);
        //交易类型
        $order->SetTrade_type('NATIVE');//JSAPI
        //
        $order->SetProduct_id($order_id);

        $order->SetNotify_url(U('pay/notify/wxpay',null,true,true));

        $wxpai = new \WxPayApi();
        $result = $wxpai->unifiedOrder($order);
        if($result){
            if(key_exists('return_code',$result)&&$result['return_code']=='SUCCESS'){
                if(key_exists('result_code',$result)&&$result['result_code']=='SUCCESS'){
                    $pay_money =floatval($info['price'])*100;
                    if(strtolower(C('OPEN_TEST_ENV'))=='true'){
                        $pay_money = 0.01;
                    }
                    return $this->redirect(U('yyg/pay/charge_wxscan',['code'=>base64_encode(serialize([$result['code_url'],$order_id,$pay_money]))]));
                }else{
                    echo $result['err_code'].':'.$result['err_code_des'];
                }
            }else{
                echo $result['return_msg'];
            }
        }
    }

}