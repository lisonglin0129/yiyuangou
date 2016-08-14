<?php
namespace app\mobile\controller;
use \think\Controller;
use app\pay\model\ExecModel;
use app\admin\controller\Config;
use \app\admin\model\ConfModel;
//微信支付类
class Wxpay extends AccountBase{
	
	private $config = array(
			'access_token' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
			
			'token'		   => 'https://api.weixin.qq.com/cgi-bin/token',
			
			'grant_type'   => 'authorization_code'
	
	);
	private $wx_order;
	/**
	 * 购买商品调用微信
	 */
	public function goodsPay($param = '')
	{
		$paramter = json_decode($param);
    	
    	if(!isset($paramter->openid)&&!isset($paramter->order_id))
    	{
    		print_r($paramter);
    		echo '支付失败';
    		exit;
    	}
		
    	
    	
		require_once APP_PATH.'lib/wx/mobile/Request.php';
		$order_list_parent = M('order_list_parent');
		$order_list = M('order_list');
		$Users = M('users');
		 
		$order_info = $order_list_parent->field('price,name,close_time,username,uid')->where('order_id ='.$paramter->order_id)->find();
		
		if(empty($order_info)) {
			return $this->error('该订单不存在');
		}
		 
		if($order_info['close_time'] < time()) {
			return $this->error('该订单已过期');
		}
		
		//定义支付变量
  	 	$pay_order_id = $paramter->order_id;
    	$pay_subject = $order_info['name'];
    	$pay_money = $order_info['price'];
    	$pay_type = $paramter->pay_type;
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
		$this->wx_order = $pay_order_id;
		$total_fee = $pay_money*100;
		$wxData = array(
				'out_trade_no'=>$this->wx_order,
				'body'=>'试购买商品',
				'total_fee' =>$total_fee,
				'mch_create_ip'=>'127.0.0.1',
				'callback' => U('/pay/Notify/HwxAjaxPay',null,null,true).'?order_id='.$pay_order_id.'&total_fee='.$total_fee,
				'callback_url' => U('/pay/Notify/HwxThreePay',null,null,true).'?order_id='.$pay_order_id.'&total_fee='.$total_fee,
				'sub_openid' => $paramter->openid
		
		);
		$wxData['body'] = $pay_subject;
		//        $wxData['total_fee'] = $order_info['price']*100;
		$wx = new \wx\request\Request();
		$token = json_decode($wx->index($wxData));
		
		if(isset($token->token_id)) {
			try{
				$url = 'https://pay.swiftpass.cn/pay/jspay?token_id='.$token->token_id.'&showwxtitle=1';
				header('Location:'.$url);
				exit;
			}catch (\Exception $e){
				return $this->error($token->msg.'支付失败');
			}
		
		}else {
			return $this->error($token->msg.'支付失败');
		}
		
	}
	/**
	 * 请求微信支付
	 */
	public function openid()
	{
		$openid = isset($_REQUEST['openid']) ? $_REQUEST['openid'] : '';
		$order_id =I('get.order_num');
		$pay_type = (int)I('get.pay_type');
		$use_balance = (int)I('get.use_balance');
		$uid = I('get.uid');
		
		$success = array(
				'openid'  => $openid ,
				'uid'     => $uid,
				'use_balance' => $use_balance,
				'pay_type' => $pay_type,
				'order_id' => $order_id
		);
		$success = json_encode($success);
		$this->goodsPay($success);
	}
	/**
     * 微信支付
     * @return mixed
     */
    public function pay() {
    	$order_id =I('post.order_num');
    	$pay_type = (int)I('post.pay_type');
    	$use_balance = (int)I('post.use_balance');
    	$uid = session('user')['id'];
    	$url  = 'http://www.51db888.com/index.php?s=pay/Notify/getPaycode&use_balance='.$use_balance.'&pay_type='.$pay_type.'&order_num='.$order_id.'&uid='.$uid;
    	$this->sendPost('https://open.weixin.qq.com/connect/oauth2/authorize',array(
    			'__' => '?',
    			'appid' => $this->getConfigs('APPID')['value'],
    			'redirect_uri' => $this->setUrl($url),
    			'response_type' => 'code',
    			'scope' => 'snsapi_base',
    			'state' => '123',
    			'__end' => '#wechat_redirect'
    	));
    }
    
   
    
    public function payresult() {
    	 
    	$total_fee =  isset($_GET['total_fee']) ? $_GET['total_fee'] : '';
		//--订单Id
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
    	//$this->assign('pay_money',sprintf('%0.2f',$total_fee/100));
    	//        $this->assign('editAddress',$editAddress);
    	$this->assign('order_id', $order_id);
    	
    	return $this->fetch();
    
    }
    /**
     * 充值回调
     * @param string $param
     * @return mixed|mixed[]|integer[]|string[]|number[]|string|string[]
     */
    public function rgePay($param = '')
    {
   
  
   		$paramter = json_decode($param);

   		if(isset($paramter->openid))
   		{
   			echo '支付失败';
   		}
   		
    	require_once APP_PATH.'lib/wx/mobile/Request.php';
    	
    	$money = $paramter->money*100;
    	 
    	$uid = $paramter->uid;
    	
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
    				"uid" => $uid,
    				'username' => $username,
    				"num" => 1,
    				"order_list" => $son_order_id,
    				"bus_type" => 'recharge',
    				"price" => $money/100,
    				"origin" => 'mobile',
    				'create_time' => time(),
    				"close_time" => NOW_TIME + (int)$close_time,
    				"plat_form" => "wxpay"
    		);
    		 
    		$order_list_parent = M('order_list_parent');
    	
    		$parent_order_id = $order_list_parent->add($order_parent_data);
    		 
    		//把父订单id插入到子订单id里面
    		$insert_result = $order_list->where(array('id'=> $son_order_id))->setField('pid', $parent_order_id);
    	}
    	 
    	if ($son_order_id && $parent_order_id && $insert_result) {
    		$this->wx_order = $p_order_id;
    		$total_fee = $money;
    		$wxData = array(
    				'out_trade_no'=>$this->wx_order,
    				'body'=>'111',
    				'total_fee' =>$total_fee,
    				'mch_create_ip'=>'127.0.0.1',
    				'callback' => U('/pay/Notify/rechargeBack',null,null,true).'?order_id='.$this->wx_order.'&total_fee='.$total_fee,
    				'callback_url' => U('/pay/Notify/HwxThreePay',null,null,true).'?order_id='.$this->wx_order.'&total_fee='.$total_fee,
    				'sub_openid' => $paramter->openid
    		);
    	
    		$wx = new \wx\request\Request();
    	
    		$token = json_decode($wx->index($wxData));
		
    		/**标记支付成功*/
    		 
    		if(isset($token->token_id))
    		{
    			$url = 'https://pay.swiftpass.cn/pay/jspay?token_id='.$token->token_id.'&showwxtitle=1';
    			header('Location:'.$url);
    			exit;
    		}else {
    			echo '<h1>支付失败'.$token->msg.'</h1>';
    			exit;
    		}
    		 
    	} else {
    		return $this->error('支付失败');
    	}
    }
    /**
     * 编码后的URL
     * @param string $url
     * @return string
     */
    public function setUrl($url = '')
    {
    	return urlencode(mb_convert_encoding($url, 'utf-8', 'gb2312'));
    }
    /**
     * 请求
     * @param string $url
     * @param array $param
     */
    public function sendPost($url ='' ,$param = array())
    {
    	$u_str = '';
    	$urls = '';
    	foreach($param as $key => $value)
    	{
    		if($key == '__' || $key == '__end')
    		{
    			continue;
    		}
    		$u_str =$u_str. $key.'='.$value.'&';
    	}
    	if(isset($param['__end'])){
    		$urls = $url.$param['__'].substr($u_str, 0,(strlen($u_str)-1)).$param['__end'];
    	}else {
    		$urls = $url.$param['__'].substr($u_str, 0,(strlen($u_str)-1));
    	}
    	header('Location:'.$urls);
    }
    /**
     * 充值调用
     */
    public function rechargePay()
    {
    	$money = I('post.money');
    	$uid = session('user')['id'];
    	$url  = 'http://www.51db888.com/index.php?s=pay/Notify/getcode&money='.$money.'&uid='.$uid;
    	$this->sendPost('https://open.weixin.qq.com/connect/oauth2/authorize',array(
    			'__' => '?',
    			'appid' =>  $this->getConfigs('APPID')['value'],
    			'redirect_uri' => $this->setUrl($url),
    			'response_type' => 'code',
    			'scope' => 'snsapi_base',
    			'state' => 'lisonglin',
    			'__end' => '#wechat_redirect'
    	));
    }

    
    public function getHTTPS($url ='' ,$param = array()) {
   
    	$u_str = '';
    	$urls = '';
   	  	foreach($param as $key => $value)
   	  	{
   	  		if($key == '__' || $key == '__end')
   	  		{
   	  			continue;
   	  		}
   	  		$u_str =$u_str. $key.'='.$value.'&';
   	  	}
   	  	if(isset($param['__end'])){
   	  		$urls = $url.$param['__'].substr($u_str, 0,(strlen($u_str)-1)).$param['__end'];
   	  	}else 
   	  	{
   	  		$urls = $url.$param['__'].substr($u_str, 0,(strlen($u_str)-1));
   	  		
   	  	}

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_HEADER, false);
    	curl_setopt($ch, CURLOPT_URL, $urls);
    	curl_setopt($ch, CURLOPT_REFERER, $urls);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	return $result;
    }
	/**
	 * 请求微信支付
	 */
    public function Appid()
    { 
    	$openid = isset($_REQUEST['openid']) ? $_REQUEST['openid'] : '';
    	$money = isset($_GET['money']) ? $_GET['money'] : '';
    	$uid = isset(session('user')['id']) ?  session('user')['id'] : '';
    	$success = array(
    			'openid'  => $openid ,
    			'money'   => $money,
    			'uid'     => $uid,
    	);
    	$success = json_encode($success);
    	$this->rgePay($success);
    }
    /**
     * 获得peizhi
     * @param unknown $flag
     * @return unknown|mixed[]|unknown[]
     */
    public function getConfigs($flag) {
		$conf = new ConfModel();
		$conf_list = $conf->get_conf_list();
	
		foreach($conf_list['category_arr'] as $k =>$key ) {
			foreach($key as $ks) {
				if($ks['name'] == $flag) {
					return $ks;
				}
			}
		}
		exit;
	}
    
    public function getToken()
    {
    	 $resuccess =  $this->getHTTPS('https://api.weixin.qq.com/cgi-bin/token',array(
    	 		'__' => '?',
    	 		'grant_type'=>'client_credential',
    	 		'appid' => 'wx33b73244240fdf0d',
    	 		'secret'=>'79fa96c8e6ad7ed0c2b50d89bd91ae28'
    	 ));
    	 $token = json_decode($resuccess);
    
    	 if(isset($token->access_token))
    	 {
    	    return $this->getAppId($token->access_token);
    	 }else {
    	 	$this->error('微信账号出现了问题，或则服务器连接超时'.$token->errmsg);
    	 }
    }
}