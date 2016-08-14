<?php
/**
 * 微信第三方处理类
 */
namespace app\pay\controller;

use app\core\controller\Common;
use app\pay\model\PublicModel;
use think\Controller;
class Wx extends Common {
	private $m_pub;
	protected $money;
	private $success;
	private $wx_order;
	
	public function __construct()
	{
		parent::__construct();
		$this->m_pub = new PublicModel();
	}
	/**
	 * 微信第三方
	 * @order_id request 订单id
	 * @timestamp request 时间戳
	 * @sign request 签名
	 */
	public function index()
	{
		//--订单ID
		$order_id = I("request.order_id", null); 
		$timestamp = I("request.timestamp", null);
		$sign = I("request.sign", null);

		
		$r = $this->m_pub->auth($order_id, $timestamp, $sign);
	
		if ($r["code"] !== "1") {
			if (IS_AJAX) {
				die_result('-100', "本请求失效或超时,请重新发起请求" . $sign);
			} else {
				$this->redirect('/');
			}
		}

		$OrderListParent = M('order_list_parent');
		
		$order_info = $OrderListParent->field('id,order_id,price,name,close_time,username,uid')->where(['order_id'=>$order_id])->find();
		
		$price = floatval($order_info['price']);
		
	   	$wx_json =  $this->construct_param($order_id,$price);

	   	$wx = json_decode(str_replace('\\', '', $wx_json));
	   	//00拿到订单信息
	   	$this->assign('order_id',$order_id);
	   	
	   	$this->assign('price',$price);
	   	
	   	$this->assign('wx_order_id',$this->wx_order);
	   	
	   	$this->assign('Wx' , $wx);
		
	    die($this->fetch('wxpay/WxSm'));
	    
	}
	public function import_lib($packge = '') {
		
		$files =  str_replace('/pay/controller/Wx.php', '', str_replace('\\', '/', __FILE__));
		
		$flod = '/' .str_replace('.', '/', $packge). '.php';		
		
		require_once  $files.$flod;
	}
	/**构建提交参数信息*/
	private function construct_param($order_id ,$price =0)
	{
		 $this->import_lib("lib.wx.wxpay");
		 $this->wx_order = mt_rand(time(),time()+rand());
		 $port = new \wxpay();
		 $total_fee = $price*100;  
	
		 $info = $this->m_pub->get_p_order_info($order_id);
		 $dat = array(
		 		'out_trade_no'=> $this->wx_order,
		 		'body'  =>  $info['name'],
		 		'attach' => '附加信息',
		 		'total_fee' => $total_fee,
		 		'mch_create_ip' => '127.0.0.1',
		 		'time_start' =>'' ,
		 		'time_expire' =>'',
		 		"it_b_pay" =>'2h',
		 		'callback' => U('Notify/wxThreePay',null,null,get_conf_domain()).'?order_id='.$order_id.'&total_fee='.$total_fee
		 );
	
		return $port->index('submitOrderInfo',$dat);
		  
	}
    
    
}