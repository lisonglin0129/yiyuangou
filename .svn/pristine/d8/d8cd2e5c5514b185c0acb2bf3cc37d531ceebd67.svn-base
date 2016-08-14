<?php
namespace app\pay\controller;

use app\core\model\CommonModel;
use app\lib\beecloud\rest\api;
use app\pay\model\ExecModel;
use app\pay\model\PublicModel;
use think\Controller;
use think\Exception;

/**
 * Beecloud
 */
Class Bee extends Controller
{
    private $m_pub;
    protected $money;


    public function __construct()
    {
        parent::__construct();
        $this->m_pub = new PublicModel();
    }

    public function index(){

    }
    /*
     *支付宝下单
     */
    public function ali_web()
    {
        list($order_info,$bill_data) = $this->init_bill('ALI_WEB');
        $api = new api();
        $result = $api->bill($bill_data);
        if(property_exists($result,'url')){
            if(APP_DEBUG){
                $m = M('notify','log_');
                $m->add([
                    'order_id'=>'bee:ali_web:下单调试',
                    'create_time'=>date('Y-m-d H:i:s'),
                    'log'=>json_encode($result)
                ]);
            }
            $this->redirect($result->url);
        }else{
            $m = M('notify','log_');
            $m->add([
                'order_id'=>'bee:ali_web:下单失败',
                'create_time'=>date('Y-m-d H:i:s'),
                'log'=>json_encode($result)
            ]);
        }
    }

    /*
     * 微信下单
     */
    public function wx_native()
    {
        list($order_info,$bill_data) = $this->init_bill('WX_NATIVE');
        $api = new api();
        $result = $api->bill($bill_data);
        if(property_exists($result,'code_url')){
            if(APP_DEBUG){
                $m = M('notify','log_');
                $m->add([
                    'order_id'=>'bee:wx_native:下单调试',
                    'create_time'=>date('Y-m-d H:i:s'),
                    'log'=>json_encode($result)
                ]);
            }
            $this->redirect(U('yyg/pay/charge_wxscan',['code'=>base64_encode(serialize([$result->code_url,$order_info['order_id'],$bill_data['total_fee']]))]));
        }else{
            $m = M('notify','log_');
            $m->add([
                'order_id'=>'bee:wx_native:下单失败',
                'create_time'=>date('Y-m-d H:i:s'),
                'log'=>json_encode($result)
            ]);
        }
    }

    /*
     * 同步通知
     * TODO:显示订单支付结果
     */
    public function notify()
    {
        $order_id = I('request.out_trade_no',null,'trim');
        empty($order_id) ||
        $this->redirect('yyg/pay/pay_result',['id'=>$order_id]);
    }

    /*
     * 异步通知
     * 需要在BEECLOUD配置界面填写本方法的访问地址
     */
    public function webhook_live(){
        $raw_post_data = file_get_contents('php://input', 'r');
        $m = M('notify','log_');
        $m->add([
            'order_id'=>'webhook_live：raw',
            'create_time'=>date('Y-m-d H:i:s'),
            'log'=>$raw_post_data
        ]);
        $request = json_decode($raw_post_data,true);
        if(!$this->bee_sign_check($request['timestamp'],$request['sign'])){
            die('非法请求；');
        }
        if($request['trade_success']!='true'){
            $m->add([
                'order_id'=>'',
                'create_time'=>date('Y-m-d H:i:s'),
                'log'=>'支付状态错误'
            ]);
            $this->response_success();
        }
        $order_id = $request['transaction_id'];
        $order_info = $this->m_pub->get_p_order_info($order_id);
        if (empty($order_info)) {
            $m->add([
                'order_id'=>$order_id,
                'create_time'=>date('Y-m-d H:i:s'),
                'log'=>'无效订单(没有该订单)'
            ]);
            $this->response_success();
        }

        if ($order_info["pay_status"] !== "1") {
            $m->add([
                'order_id'=>$order_id,
                'create_time'=>date('Y-m-d H:i:s'),
                'log'=>'无效订单(状态错误)'
            ]);
            $this->response_success();
        }

        $m_exec = new ExecModel();
        $m_exec->set_order_status($order_id, "2", "1");
        $start_token = 'start_flow_p_'.md5($order_id.'_'.time());
        S($start_token,$order_id,['expire'=>10]);


        try{
            R('pay/notify/start_flow_p',['sess'=>$start_token]);
        }catch(Exception $x){
            $m->add([
                'order_id'=>$order_id,
                'create_time'=>date('Y-m-d H:i:s'),
                'log'=>$x->getMessage().$x->getFile().$x->getLine().$x->getCode()
            ]);
        }
        $this->response_success();
    }

    /*
     * 异步通知(测试)
     * 仅进行记录并且响应"success"
     */
    public function webhook_test(){
        $raw_post_data = file_get_contents('php://input', 'r');
        $m = M('notify','log_');
        $m->add([
            'order_id'=>'webhook_test：raw',
            'create_time'=>date('Y-m-d H:i:s'),
            'log'=>$raw_post_data
        ]);
        $request = json_decode($raw_post_data,true);
        if(!$this->bee_sign_check($request['timestamp'],$request['sign'])){
            die('非法请求；');
        }
        die('success');
    }

    /*
     * 读取配置
     * 首先尝试从缓存获取
     */
    private function getConf($key){
        if(null==$config=S('beecloud_conf')){
            $m = new CommonModel();
            $config = $m->get_conf_by_keys('BEE\_%');
            S('beecloud_conf',$config,['expire' => 3600]);
        }
        if(!key_exists('bee_'.strtolower($key),$config))
            throw new WxPayException("读取配置{$key}错误;");
        return $config['bee_'.strtolower($key)];
    }


    /*
     * 客户端签名
     * 根据时间戳返回签名
     */
    private function bee_sign($timestamp){
        $app_id=$this->getConf('APP_ID');
        $app_sec=$this->getConf('APP_SEC');
        return md5($app_id.$timestamp.$app_sec);
    }

    /*
     * 服务器签名验证
     * 注意参数顺序和前面不一样（被坑了一次）
     */
    private function bee_sign_check($timestamp,$sign){
        $app_id=$this->getConf('APP_ID');
        $app_sec=$this->getConf('APP_SEC');
        return md5($app_id.$app_sec.$timestamp)==$sign;
    }

    /*
     * 根据POST来的参数初始化下单参数
     */
    private function init_bill($channel){
        $order_id = I("request.order_id",0,'trim');
        $timestamp = I("request.timestamp",0,'trim');
        $sign = I("request.sign",0,'trim');
        $r = $this->m_pub->auth($order_id, $timestamp, $sign);
        if($r["code"]!=="1"){
            if(IS_AJAX){
                die_result('-100',"本请求失效或超时,请重新发起请求".$sign);
            }else{
                $this->redirect('/');
            }
        }
        $info = $this->m_pub->get_p_order_info($order_id);
        if(empty($info))die_result('-101',"订单不存在");
        $timestamp = floor(microtime(true) * 1000);
        $bill = [
            'app_id' => $this->getConf('APP_ID'),
            'timestamp' => $timestamp,
            'app_sign' => $this->bee_sign($timestamp),
            'channel' => $channel,
            //'total_fee' => APP_DEBUG?1:floatval($info['price'])*100,
            'total_fee' => (int) floatval($info['price'])*100,
            'bill_no' => $order_id,
            'title' => $info['name'],
//            'optional'=>['msg'=>'Testing 1 2 3 ...'],
            'return_url' => U('pay/bee/notify',null,true,true),
            'bill_timeout' => 1800,
        ];
        if(strtolower(C('OPEN_TEST_ENV'))=='true'){
            $bill['total_fee'] = 0.01;
        }
        return [$info,$bill];
    }

    /*
     * 通知服务器已经正确处理了请求并继续执行
     * (服务器不关心业务状态)
     */
    private function response_success(){
        ob_clean();
        $response = 'success';
        ignore_user_abort(true);
        header('Content-Length:'.strlen($response));
        header('Connection:close');
        echo $response;
        flush();
        ob_flush();
    }
}