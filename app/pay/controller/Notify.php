<?php
namespace app\pay\controller;

use app\admin\model\SpreadModel;
use app\core\model\CommonModel;
use app\core\model\CouponModel;
use app\pay\model\ExecModel;
use app\pay\model\NotifyModel;
use app\pay\model\PublicModel;
use think\Controller;
use think\Db;
use think\Exception;
use think\Model;
use app\core\model\UserModel;
/**业务开通流程*/
Class Notify
{
    public $m_pub;
    private $pay_type = 'alipay';
	private $resHandler;
    public function __construct()
    {
        set_time_limit(10000);
        ignore_user_abort(true);
        $this->m_pub = new PublicModel();
    }

    /**
     * 获取用户code
     * @param string $url
     * @param array $param
     * @return mixed
     */

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
    /**
     *将参数转换成 key=value
     * @param unknown $para
     * @return string
     */
    public function getlinkParamter($para) {
    
    	$arg  = "";
    	while (list ($key, $val) = each ($para)) {
    		$arg.=$key."=".$val."&";
    	}
    	//去掉最后一个&字符
    	$arg = substr($arg,0,count($arg)-2);
    
    	//如果存在转义字符，那么去掉转义
    	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
    
    	return $arg;
    }
    /**
     * 获取code
     */
    public function getcode()
    {
    	$code = isset($_REQUEST['code']) ? $_REQUEST['code'] : '';
    
    	$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : '';
    	$money = isset($_REQUEST['money']) ? $_REQUEST['money'] : '';
    	$appid_json = $this->getHTTPS('https://api.weixin.qq.com/sns/oauth2/access_token',array(
    			'__'=>'?',
    			'appid'  => $this->getConfigs('APPID')['value'],
    			'secret' => $this->getConfigs('SECRET')['value'],
    			'code' =>  $code,
    			'grant_type' => 'authorization_code'
    	));
    	$appid_json = json_decode($appid_json,true);
    	
    	if(isset($appid_json['openid'])){ 		
    		//--检车用户身份是否合格
    		
    		$users = M('users')->where(array('id'=>$uid))->select();
    		
    		if($users) {
    		
    			session('user',$users[0]);
    		
    		}
    		
    		$urls = $this->getlinkParamter($appid_json);
    		
    		$contorl = new Controller();
    		
    	
    		
    		$contorl->redirect(U('mobile/Wxpay/Appid') . "?" . $urls.'&money='.$money);
    		
    	
    	}else{
    		
    		echo '支付失败';
    	}
		exit;
    }
    /**
     * 获取paycode
     */
    public function getPaycode()
    {
    	$code = isset($_REQUEST['code']) ? $_REQUEST['code'] : '';
    	$order_id =I('get.order_num');
    	$pay_type = (int)I('get.pay_type');
    	$use_balance = (int)I('get.use_balance');
    	$uid = I('get.uid');
    	$appid_json = $this->getHTTPS('https://api.weixin.qq.com/sns/oauth2/access_token',array(
    			'__'=>'?',
    			'appid'  =>  $this->getConfigs('APPID')['value'],
    			'secret' =>  $this->getConfigs('SECRET')['value'],
    			'code' => $code,
    			'grant_type' => 'authorization_code'
    	));
    	
    	$appid_json = json_decode($appid_json,true);
    
    	if(isset($appid_json['openid'])){
    		//--检车用户身份是否合格
    		$users = M('users')->where(array('id'=>$uid))->select();
    		if($users) {
    			session('user',$users[0]);
    		}
    		$urls = $this->getlinkParamter($appid_json);
    		$contorl = new Controller();
    		$contorl->redirect(U('mobile/Wxpay/openid') . "?" . $urls."&order_num=".$order_id."&pay_type=".$pay_type."&use_balance=".$use_balance."&uid=".$uid);
    	}else{
    
    		echo '支付失败';
    	}
    	exit;
    }
    
    /**
     * 流程***订单支付页面提交流程
     */
    public function order_flow()
    {
        $sign = $timestamp = $order_id = $balance_flag = null;
        $post = I("request.");
        if (!is_array($post)) {
            die_result("-100", "参数不正确" . $order_id);
        }
        extract($post);
        //初始化支付类型
        $this->pay_type = empty($pay_type) ? "alipay" : $pay_type;

        if (empty($order_id) || empty($timestamp) || empty($sign)) {
            die_result("-100", "参数不正确2" . $order_id);
        }
        //校验token
        $r = $this->auth($order_id, $timestamp, $sign);

        if ($r["code"] !== "1") {
            die_result("-100", "TOKEN校验失败" . $order_id);
        }

        //初始化余额=订单总价
        $info = $this->m_pub->get_p_order_info($order_id);

        if($info['status']!=1){
            $ctl = new Controller();
            //$ctl->assign('info',$info);
            return $ctl->redirect(U('yyg/pay/charge').'?id='.$info['order_id']);
            die_result("-100", "订单已经失效" . $order_id);
        }

        empty((float)$info["price"]) && die_result("获取订单信息失败");

        $money = (float)$info["price"];

        if (!empty($balance_flag)) {
            $r = $this->check_balance_pay($order_id);
            empty($r["code"]) && die_result("-100", "余额获取失败");
            //如果足够支付,尝试支付
            if ($r["code"] == "1") {
                //扣费
                $r = $this->reduce_balance($order_id);
                // dump($r);
                if ($r["code"] !== "1") {
                    die_result("-100", "扣费失败" . $order_id);
                }
                //执行开通
                $r = $this->start_flow($order_id);

                if ($r["code"] !== "1") {
                    //开通失败
                    die_json($r);
                }
                $contorl = new Controller();
                $contorl->redirect(U('yyg/pay/pay_result') . "?id=" . $order_id);
                die_result("1", "开通成功");
            } //不够用走支付宝充值流程
            else if ($r["code"] == "-2") {
                $money = $r["value"];
                //金额不为空
                empty($money) && die_result("-100", "差额计算错误");
            }
        }
        //创建订单id
        $r = $this->create_recharge_order($order_id, $money,$this->pay_type);
        ($r["code"] !== "1") && die_result("-100", "订单创建失败");
        //获取订单id
        $order_id = $r["value"];
        empty($order_id) && die_result("-100", "获取附加订单号失败");

        //跳转到支付平台
        $this->jump($order_id);

    }

    /**
     * 流程***其他端请求余额支付
     */
    public function balance_flow($post =array())
    {
        set_time_limit(10000);
        ignore_user_abort(true);
        $sign = $timestamp = $order_id = $flag=null;
        if(empty($post)){
            $post = I("request.");
        }
        if (!is_array($post)) {
            die_result("-100", "参数不正确" . $order_id);
        }
        extract($post);
        if (empty($order_id) || empty($timestamp) || empty($sign)) {
            die_result("-100", "参数不正确2" . $order_id);
        }
        //校验token
        $r = $this->auth($order_id, $timestamp, $sign);
        if ($r["code"] !== "1") {
            die_result("-100", "TOKEN校验失败" . $order_id);
        }
        //计算余额
        $r = $this->check_balance_pay($order_id);
        if ($r["code"] !== "1") {
            die_result("-100", "余额不足或已支付" . $order_id);
        }
        //扣费
        $r = $this->reduce_balance($order_id);
        if ($r["code"] !== "1") {
            die_result("-100", "扣费失败" . $order_id);
        }
        //执行开通
        $r = $this->start_flow($order_id);
        if ($r["code"] !== "1") {
            die_result("-100", "开通失败" . $order_id);
        }
        return die_result("1", "success",$flag);
    }
    /**
     * 流程***其他端请求余额支付
     * 接受参数以用来远程调用
     */
    public function balance_flow_p($sign=null,$timestamp=null,$order_id=null)
    {
        set_time_limit(10000);
        ignore_user_abort(true);
//        $sign = $timestamp = $order_id = null;
//        $post = I("request.");
//        if (!is_array($post)) {
//            die_result("-100", "参数不正确" . $order_id);
//        }
//        extract($post);
        if (empty($order_id) || empty($timestamp) || empty($sign)) {
            return ["-100", "参数不正确2" . $order_id];
        }
        //校验token
        $r = $this->auth($order_id, $timestamp, $sign);
        if ($r["code"] !== "1") {
            return ["-100", "TOKEN校验失败" . $order_id];
        }
        //计算余额
        $r = $this->check_balance_pay($order_id);
        if ($r["code"] !== "1") {
            return ["-100", "余额不足或已支付" . $order_id];
        }
        //扣费
        $r = $this->reduce_balance($order_id);
        if ($r["code"] !== "1") {
            return ["-100", "扣费失败" . $order_id];
        }
        //执行开通
        $r = $this->start_flow($order_id);
        if ($r["code"] !== "1") {
            return ["-100", "开通失败" . $order_id];
        }
        return["1", "success"];
    }

    /**
     * 流程***充值完毕后,需要执行下一步时候的操作,内部使用
     */
    protected function next_order($order_id)
    {
        //检测余额是否够支付
        $r = $this->check_balance_pay($order_id);
        if ($r["code"] !== "1") {
            return rt("-100", "", "余额不够支付");
        }
        //扣款,标记支付
        $r = $this->reduce_balance($order_id);
        if ($r["code"] !== "1") {
            return rt("-100", "", "充值扣款失败");
        }
        //开通业务
        $r = $this->start_flow($order_id);
        if ($r["code"] !== "1") {
            return rt("-100", "", "开通业务失败");
        }
        return rt("1", "", "next_order开通成功了");
    }

    /**
     * 鉴定权限签名
     * @param string $order_id 订单号id
     * @param string $timestamp 时间戳
     * @param string $sign 签名
     * @return bool
     */
    private function auth($order_id, $timestamp, $sign)
    {
        //获取系统公钥
        $key = C('TOKEN_ACCESS');
        //鉴权密码
        $token = md5($order_id . $timestamp . $key);
        //权限校验失败
        if ($sign !== $token) return rt("-200", "", "鉴定权限签名失败");
        //时间戳超时
        if (NOW_TIME - $timestamp > 7200) return rt("-200", "", "鉴定权限签名超时");
        return rt("1", "", "验证成功");
    }

    /**
     * 支付宝回调方法
     */
    public function alipay()
    {
        /**支付宝信息校验开始***************/
        $trade_status = null;
        $out_trade_no = null;
        $sign_type = null;
        //校验参数
        $info = I("post.");
        empty($info) && $info = I("get.");
        empty($info) && die_result("-1", "支付宝通知参数为空");
        extract($info);
        $info_sign = empty($info['sign']) ? '' : $info['sign'];
        if (IS_GET) {
            $info_sign = urldecode(str_replace(" ", "%2B", $info_sign));
        }

        /**支付宝通知签名*/
        if ($sign_type == 'MD5') {
            /**获取参数签名*/
            $result = $this->md5_sign($info, $info_sign);
        } else if ($sign_type == 'RSA') {
            /**获取参数签名*/
            $result = $this->rsa_sign($info, $info_sign);
        } else {
            log_w("签名校验失败!:" . json_encode($info));
            die("SUCCESS");
        }


        /**比较数字签名是否正确*/
        if (!$result) {
            log_w("微信参数:" . json_encode($info));
            log_w("验证签名结果:" . json_encode($result));
            log_w("微信签名参数错误!-" . $out_trade_no . '-' . $trade_status);
            die("SUCCESS");
        } else {
            log_w("微信验证通过!" . json_encode($result));
        }
        /**支付宝信息校验结束***************/

        /**拦截交易状态不成功的*/
        if ($trade_status !== 'TRADE_SUCCESS') {
            log_w("微信通知结果:交易不成功!-" . $out_trade_no . '-' . $trade_status);
            die("SUCCESS");
        }


        /**检测订单号*/
        if (empty($out_trade_no)) {
            log_w("微信订单号为空获取失败");
            die("SUCCESS");
        }
        /**初始化信息*/
        $order_id = $out_trade_no;
        $this->pay_type = "alipay";
        /**获取父级订单信息*/
        $info = $this->m_pub->get_p_order_info($order_id);
        if (empty($info)) {
            log_w("父级订单信息查询失败");
            die("SUCCESS");
        }
        /**校验订单状态是否为1*/
        $pay_status = $info["pay_status"];
        if ($pay_status !== "1") {
            log_w("父级订单标记不为未开通,有可能是您重复执行通知");
            die("SUCCESS");
        }
        /**标记支付成功*/
        $m_exec = new ExecModel();
        $res=$m_exec->set_order_status($order_id, "2", "1");
        /**全部校验完毕,开始走下一步流程*/
        $m = M('notify','log_');
        try{
            if($res){
                $ali_result = $this->spread_reward_deal($order_id);
            }
        }catch(Exception $ex){
            if(APP_DEBUG){
                $m->add([
                    'order_id'=>'ali_spread:'.$order_id,
                    'create_time'=>date('Y-m-d H:i:s'),
                    'log'=>$ex->getMessage()."\n".$ex->getFile()."\n".$ex->getLine()."\n".$ex->getCode()."\n"
                ]);
            }
        }
        $this->start_flow($order_id);
        // R('yyg/ucenter/spread_reward_deal',array($order_id));
    }
    public function import_lib($packge = '') {
    
    	$files =  str_replace('/pay/controller/Notify.php', '', str_replace('\\', '/', __FILE__));
    
    	$flod = '/' .str_replace('.', '/', $packge). '.php';
    	
   
    	require_once  $files.$flod;
    }
    /**
     * 微信第三方支付 回调
     */
    public function wxThreePay() {
    	$this->import_lib("lib.wx.wxpay");
    	$this->resHandler =  new \wxpay();
    	//--订单ID
    	$order_id = I('get.order_id'); 
    	//--订单价格
    	$total_fee = I('get.total_fee');
  		$success = $this->resHandler->callback();
  		$this->pay_type = "WX_THREE_PAY";
  		if($success['success'] == 'success') {
  			$info = $this->m_pub->get_p_order_info($order_id);
  			/**校验订单状态是否为1*/
  			$pay_status = $info["pay_status"];
  			if ($pay_status !== "1") {
  				log_w("父级订单标记不为未开通,有可能是您重复执行通知");
  				die("SUCCESS");
  			}
  			
  			
  		}
  		/**标记支付成功*/
  		$m_exec = new ExecModel();
  		$res=$m_exec->set_order_status($order_id, "2", "1");
  		$m = M('notify','log_');
  		try{
  			if($res){
  				$ali_result = $this->spread_reward_deal($order_id);
  			}
  		}catch(Exception $ex){
  			if(APP_DEBUG){
  				$m->add([
  						'order_id'=>'ali_spread:'.$order_id,
  						'create_time'=>date('Y-m-d H:i:s'),
  						'log'=>$ex->getMessage()."\n".$ex->getFile()."\n".$ex->getLine()."\n".$ex->getCode()."\n"
  				]);
  			}
  		}
  		$this->start_flow($order_id);
    }
    
    
    public function HwxAjaxPay()
    {
    	$order_id = I('get.order_id');
    	$this->import_lib("lib.wx.mobile.Request");
    	$this->resHandler =  new \wx\request\Request();
    	$this->resHandler->callback();
    	$this->pay_type = "H5";
    	if(isset($order_id)) {
    	
    		//--订单价格
    		$total_fee = I('get.total_fee');
    	
    		$info = $this->m_pub->get_p_order_info($order_id);
    		/**校验订单状态是否为1*/
    		$pay_status = $info["pay_status"];
    		 
    	}
    	/**标记支付成功*/
    	$m_exec = new ExecModel();
    	$res=$m_exec->set_order_status($order_id, "2", "1");
    	$this->start_flow($order_id);
    }
    /***
     * 充值 回调
     */
    public function rechargeBack()
    {
    
    	$order_id = I('get.order_id');
		M('order_list_parent')->where(array('order_id'=>$order_id))->save(array('pay_status' => 2 ,'pay_time'=>time()));
		$this->start_flow($order_id);
    }
    /**
     * 微信第三方H5支付 回调
     */
    public function HwxThreePay() {
    
    	//--订单ID
    	$order_id = I('get.order_id');
    	$this->import_lib("lib.wx.mobile.Request");
    	$this->resHandler =  new \wx\request\Request();
    
    	$this->resHandler->callback();
    	$this->pay_type = "H5";
    	if(isset($order_id)) {

	    	//--订单价格
	    	$total_fee = I('get.total_fee');
	 
	    	$info = $this->m_pub->get_p_order_info($order_id);
	    	/**校验订单状态是否为1*/
	    	$pay_status = $info["pay_status"];
    	
    	}
    	$control = new Controller();
      	$this->start_flow($order_id);
		$control->redirect(U('/mobile/wxpay/payresult') . "?order_id=" .$order_id . "&total_fee=" . $total_fee);
    }
    
    
    
    /**
     * 支付宝的签名校验函数
     * @param array $post 支付宝签名
     * @return string 签名后的值
     */
    private function md5_sign($post, $info_sign)
    {
        // 验证sign
        unset($post['sign']);
        unset($post['sign_type']);
        // 升序排列数组
        $data = paraFilter($post);//删除全部空值
        $data = argSort($data);
        $str = createLinkstring($data);
        $token = $this->m_pub->get_conf('ALIPAY_MD5_TOKEN');
        $str = trim($str, '&') . $token;
        return md5($str) === $info_sign;
    }


    /**
     * 支付宝的RSA签名校验函数
     * @param array $post 支付宝签名
     * @param string $sign 支付宝签名
     * @return string 签名后的值
     */
    private function rsa_sign($post, $sign)
    {
        $private_key_path = $this->m_pub->get_conf('ALIPAY_PUBLIC_KEY');
//        log_w('公钥:'.$private_key_path);
        //除去待签名参数数组中的空值和签名参数
        $para_filter = paraFilter($post);
//        log_w('除去待签名参数数组中的空值和签名参数:'.json_encode($para_filter));
        //对待签名参数数组排序
        $para_sort = argSort($para_filter);
//        log_w('对待签名参数数组排序:'.json_encode($para_sort));
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($para_sort);
//        log_w($prestr);
//        return rsaVerify($prestr, trim('./alipay_public_key.pem'), $sign);
        return rsaVerify($prestr, $private_key_path, $sign);
    }

    /**
     * 根据订单号,开通业务流程
     * @param string $order_id 订单id
     * @return string
     */
    private function start_flow($order_id,$plat_form="")
    {


        $info = $this->m_pub->get_p_order_info($order_id);

        if (empty($info)) {
            return rt("-100", "", "开通业务-订单信息获取失败:" . $order_id);
        }

        /**判断订单类型:charge充值,buy开通*/
        $bus_type = $info['bus_type'];
        if (!in_array($bus_type, array("buy", "recharge"))) {
            return rt("-100", "order type error", "开通业务-订单类型不正确:" . $order_id . '-' . $bus_type);
        }
        /**业务状态*/
        $pay_status = $info["pay_status"];
        if ($pay_status !== '2') {
            return rt("-100", "order type error1", "开通业务-订单状态不为2:" . $order_id . '-' . $pay_status);
        }

        $m_exec = new ExecModel();
        //设置订单状态为3
        $m_exec->set_order_status($order_id, "3", "2");
        // $this->spread_reward_deal($order_id);
        //更新父级订单支付时间***
        $m_exec->set_p_order_pay_time($order_id);
        switch ($bus_type) {
            case "buy":
                //开通业务
                $r_1 = $this->open($order_id,$plat_form);
                break;
            case "recharge":
                //充值
                $r_1 = $this->charge($order_id);
                break;
            default:
                return rt("-100", "", "开通业务-订单类型错误1:" . $order_id . '-' . $pay_status);
        }

        if (empty($r_1["code"])) return rt("-100", "返回值错误", json_encode($r_1["code"]));

        if ($r_1["code"] === '1') {
            // $m_coupon = new CouponModel();
            //$m_coupon->sign_coupon_used($info['order_id']);
            return rt("1", "success", "开通成功");
        } else if (!is_array($r_1)) {
            return rt($r_1["code"], "failed", "开通返回值不是数组返回值错误" . json_encode($r_1));
        } else {
            return rt($r_1["code"], "failed", json_encode($r_1));
        }

    }

    /**
     * 开通业务
     * @param string $order_id 订单id
     * @return string
     */
    public function open($order_id,$plat_form)
    {
        M("order_list_parent")->where("order_id = $order_id")->setField("plat_form",$plat_form);
        $m_exec = new ExecModel();

        $info = $this->m_pub->get_p_order_info($order_id);
        $p_id = $info['id'];
        $uid = $info["uid"];
        if (empty($p_id)) {
            log_w("主订单id不能为空:订单ID" . $order_id);
            return "-100";
        }
        //执行开通业务,成功返回1,不成功返回错误码
        $r = $m_exec->open_main($p_id);
        if (empty($r["code"])) return rt("-1", "开通返回值获取失败");
        switch ($r["code"]) {
            case "1":
                return rt("1", "", "成功");
            default:
                return rt($r["code"], "", json_encode($r));
        }
    }

    /**
     * 充值金额
     * @param string $order_id
     * @return string "1"成功
     */
    private function charge($order_id)
    {
        $m_exec = new ExecModel();
        $info = $this->m_pub->get_p_order_info($order_id);

        if ($info["pay_status"] !== '3') {
            log_w('订单状态不为3,上一步设置订单状态失败');
            return "-100";
        }

        $uid = $info["uid"];
        //子订单信息
        $c_info = $m_exec->get_charge_order_by_pid($info['id']);
        if (empty($c_info)) {
            log_w('充值子订单获取失败');
            return "-100";
        }

        //订单执行数据
        $exec_data = json_decode($c_info['exec_data'], true);
        if (empty($exec_data)) {
            log_w("充值子订单执行信息获取失败");
            return "-100";
        }

        //判断用户执行数据类型
        $type = $exec_data["TYPE"];
        //签名
        $md5 = strtolower($exec_data["MD5"]);

        //全局加密码
        $token = C('TOKEN_ACCESS');
        $exec_data_sign = json_encode($exec_data["EXEC_DATA"]);//执行数据
        $sign = md5($c_info['order_id'] . $type . $exec_data_sign . $token);
        //校验签名
        if ($md5 !== $sign) {
            log_w("校验签名信息失败");
            return "-100";
        };

        //余额执行充值
        $money = $exec_data["EXEC_DATA"]["VALUE"];

        $r = false;
        if (is_numeric($money) && floatval($money) > 0) {
            //  $m_coupon = new CouponModel();
            /*检测充值活动*/
//            if($act_recharge = $m_coupon->get_act_recharge_by_money($money)){
//                log_w($money.'======jiance==');
//                //增加余额操作
//                if($act_recharge['type'] ==1 || $act_recharge['type'] ==3){
//                    $money += $act_recharge['bonus'];
//                }
//                //发放优惠券操作
////                if($act_recharge['type'] ==2 || $act_recharge['type'] ==3){
////                    if(intval($act_recharge['coupon'])>0){
////                        if($coupon_info = $m_coupon->get_coupon_category_by_id($act_recharge['coupon'])){
////                            $coupon_data = [
////                                'pid' => $coupon_info['id'],
////                                'uid' => $uid,
////                                'create_time' => time(),
////                                'expire_time' => $coupon_info['expire_type']==0
////                                    ?$coupon_info['expire']+time()
////                                    :$coupon_info['expire'],
////                                'status'=>1
////                            ];
////                            $coupon_id = $m_coupon->add_coupon_list($coupon_data);
////                        }
////                    }
////                }
//            }

            $r = $m_exec->add_money($uid, $money, $order_id) !== false;
        }
        if (!$r) {
            log_w("执行充值失败" . json_encode($r));
            return "-100";
        }

        //写入子订单充值时间
        $m_exec->set_c_order_pay_time($c_info['order_id']);

        if($info['status']!=1){
            return rt("-100", "", "订单状态已经失效".$info['order_id']);
        }

        //检测是否有需要下一步执行的订单信息
        $order_id = $exec_data["EXEC_DATA"]["NEXT_ORDER"];
        if (!empty($order_id)) {
            log_w("开始执行下一个订单");
            $r = $this->next_order($order_id);
            if ($r["code"] == "1") {
                //开通成功,跳转到
                $contorl = new Controller();
                $contorl->redirect(U('yyg/pay/pay_result') . "?id=" . $order_id);
                die_result("1", "开通成功");
            } else {
                return rt("-100", "", "下一个执行订单号为空");
            }
        }
        return rt("-100", "", "下一个执行订单号为空");
    }

    /**
     * 检查余额是否足够支付
     */
    public function check_balance_pay($order_id)
    {
        $info = $this->m_pub->get_p_order_info($order_id);
        //检测订单状态是否为1
        if ($info['pay_status'] !== "1") {
            return rt("-100", $info['pay_status'], "开通订单的状态不能为1,重复支付不开通");
        }
        //获取商品价格
        $money = $info["price"];
        if (empty($money)) {
            return rt("-100", $money, "订单金额不正确");
        }
        //获取用户账户余额
        $u_info = $this->m_pub->get_user_info_by_id($info["uid"]);
        if (empty($u_info["money"])) {
            return rt("-100", $u_info["money"], "用户余额为0,不能支付");
        }
        //比较价格
        if (($money) > $u_info["money"]) {
            return rt("-2", ($money - $u_info["money"]), "用户余额不足");
        }
        //足够支付
        return rt("1");
    }

    /**
     * 扣除余额.标记已支付
     * @param string $order_id
     * @return string "1"成功
     */
    public function reduce_balance($order_id)
    {
        $info = $this->m_pub->get_p_order_info($order_id);
        $price = $info["price"];
        $u_info = $this->m_pub->get_user_info_by_id($info["uid"]);
        $money = $u_info["money"];
        $uid = $u_info["id"];
        //校验用户名是否存在
        if (empty($uid)) {
            return rt("-100", "", "用户名不存在" . $order_id);
        }
        //扣除订单款,成功直接返回true,失败返回false
        if ($price > $money) {
            return rt("-100", "", "用户余额不足" . $order_id);
        }
        //尝试扣款执行
        $m_exec = new ExecModel();
        $r = $m_exec->reduce_money($uid, $price, $order_id);

        //扣款失败
        if (!$r) {
            return rt("-100", "", "扣款失败,有可能是账户余额不足:订单ID" . $order_id);
        }

        //标记为已支付
        $r = $m_exec->set_order_status($order_id, "2", "1");
        //标记已支付失败
        if ($r === false) {
            return rt("-100", "", "标记已支付 1->2 失败:订单ID" . $order_id);
        }
        return rt("1");
        //扣款成功
    }

    /**
     * 余额不足时创建支付订单
     */
    private function create_recharge_order($order_id, $money,$pay_type)
    {
        empty($order_id) && show_result("-100", '订单号不能为空');
        empty($money) && show_result("-100", '金额不能为空');
        $info = $this->m_pub->get_p_order_info($order_id);

        $promote_precent = $this->m_pub->get_conf("PROMOTE_PRECENT");
        $close_time =C('ORDER_CLOSE_TIME');
        //创建支付父级订单
        $p_order_id = create_order_num();
        $username = $info["username"];
        $uid = $info["uid"];
        $order_parent_data = array(
            "order_id" => $p_order_id,
            "name" => '用户' . $username . '充值' . $money . '元',//商品名称
            "num" => 1,
            "bus_type" => 'recharge',
            "price" => empty($money) ? "100" : $money,
            "origin" => 'web',
            "close_time" => NOW_TIME + (int)$close_time,
            "plat_form" =>$pay_type
        );
        $order_arr = array();

        //子订单信息
        $c_order_id = create_order_num();
        $order_temp = array(
            "order_id" => $c_order_id,
            "goods_id" => 'recharge',//商品id
            "goods_name" => '用户' . $username . '充值' . $money . '元',//商品名称
            "nper_id" => null,//期数id
            "bus_type" => 'recharge',
            "num" => 1,//数量
            "exec_data" => null,//回调执行参数
            "money" => $money,//子订单总价
            "promoter_pos" => $promote_precent,//当前推广比例
            "luck_status" => "false"
        );
        /**生成支付回调执行的参数信息START*/
        $exec_data = array(
            "TYPE" => "CHARGE",
            "EXEC_DATA" => array(
                "VALUE" => $money,
                "NEXT_ORDER" => $order_id,
                "UID" => $uid
            )
        );

        $token = C('TOKEN_ACCESS');
        //MD5= MD5(订单ID,订单类型,执行数据,密钥)
        $exec_data["MD5"] = md5($c_order_id . $exec_data["TYPE"] . json_encode($exec_data["EXEC_DATA"]) . $token);
        $order_temp["exec_data"] = json_encode($exec_data);
        /**生成支付回调执行的参数信息END*/

        array_push($order_arr, $order_temp);

        $m_pay = new NotifyModel();

        //创建订单
        $p_oid = $m_pay->create_order($order_parent_data, $order_arr);
        if (empty($p_oid)) {
            return rt("-100", "", "创建订单失败");
        }
        return rt("1", $p_oid, "创建附加充值订单成功");
    }

    /**跳转到支付宝*/
    private function jump($order_id)
    {
        $control = new Controller();
        $timestamp = NOW_TIME;
        $sign = $this->m_pub->sign($order_id, $timestamp);

        switch ($this->pay_type) {
            case "alipay":
                $control->redirect(U('alipay/index') . "?t=1&order_id=" . $order_id . "&timestamp=" . $timestamp . "&sign=" . $sign);
                break;
            case "wxsm":
                	$control->redirect(U('wx/index') . "?t=1&order_id=" . $order_id . "&timestamp=" . $timestamp . "&sign=" . $sign);
                	break;
            case "wxpay":
                $control->redirect(U('wxpay/index') . "?t=1&order_id=" . $order_id . "&timestamp=" . $timestamp . "&sign=" . $sign);
                break;
            case "bee-alipay":
                $control->redirect(U('bee/ali_web') . "?t=1&order_id=" . $order_id . "&timestamp=" . $timestamp . "&sign=" . $sign);
                break;
            case "bee-wechat":
                $control->redirect(U('bee/wx_native') . "?t=1&order_id=" . $order_id . "&timestamp=" . $timestamp . "&sign=" . $sign);
                break;
            case "aipay":
                $control->redirect(U('aipay/index') . "?t=1&order_id=" . $order_id . "&timestamp=" . $timestamp . "&sign=" . $sign);
                break;
                
            default:
                $control->redirect(U('alipay/index') . "?t=2&order_id=" . $order_id . "&timestamp=" . $timestamp . "&sign=" . $sign);
        }
        die();
    }


    /**
     * 微信支付异步回调
     */
    public function wxpay(){
        C('default_return_type','xml');
        //引入SDK
        import("app.lib.Wxpay.WxPayApi",null,'.php');
        //返回结果
        $response_result = new \WxPayNotifyReply();
        //RAW
        $raw_post_data = file_get_contents('php://input', 'r');
        $m = M('notify','log_');
        $m->add([
            'order_id'=>'微信回调：raw',
            'create_time'=>date('Y-m-d H:i:s'),
            'log'=>$raw_post_data
        ]);

        //微信签名
        $notify = new \WxPayNotify();
        $notify->FromXml($raw_post_data);
        $m->add([
            'order_id'=>'微信回调：result',
            'create_time'=>date('Y-m-d H:i:s'),
            'log'=>json_encode($notify->GetValues())
        ]);
        $result = $notify->GetValues();
        if(key_exists('trade_type',$result) && $result['trade_type']=='APP'){
            define('WXPAY_CONF_PREFIX','wxpay_app_');
        }else{
            define('WXPAY_CONF_PREFIX','wxpay_mp_');
        }
        $key=I('get.key');
        if($notify->MakeSign($key)!=$notify->GetSign()){
            $response_result->SetReturn_code('FAIL');
            $response_result->SetReturn_msg('签名无效');
            return $response_result->ToXml();
            //签名无效
        }
        if($notify->GetReturn_code()!='SUCCESS'){
            $response_result->SetReturn_code('FAIL');
            $response_result->SetReturn_msg('1');
            return $response_result->ToXml();
            //返回错误
        }
        if($result['result_code']!='SUCCESS'){
            $response_result->SetReturn_code('FAIL');
            $response_result->SetReturn_msg('2');
            return $response_result->ToXml();
            //业务错误
        }

        /**初始化信息*/
        $order_id = $result['out_trade_no'];
        $this->pay_type = "wxpay";
        /**获取父级订单信息*/
        $info = $this->m_pub->get_p_order_info($order_id);
        if (empty($info)) {
            $response_result->SetReturn_code('FAIL');
            $response_result->SetReturn_msg('无效订单');
            return $response_result->ToXml();
        }
        /**校验订单状态是否为1*/
        $pay_status = $info["pay_status"];
        if ($pay_status !== "1") {
            $response_result->SetReturn_code('SUCCESS');
            $response_result->SetReturn_msg('重复开通');
            return $response_result->ToXml();
        }
        /**标记支付成功*/
        $m_exec = new ExecModel();
        $res=$m_exec->set_order_status($order_id, "2", "1");
        try{
            if($res){
                $spread_result = $this->spread_reward_deal($order_id);
            }
        }catch(Exception $ex){
            if(APP_DEBUG){
                $m->add([
                    'order_id'=>'spread_reward:'.$order_id,
                    'create_time'=>date('Y-m-d H:i:s'),
                    'log'=>$ex->getMessage()."\n".$ex->getFile()."\n".$ex->getLine()."\n".$ex->getCode()."\n"
                ]);
            }
        }
        /**全部校验完毕,开始走下一步流程*/
        // R('yyg/ucenter/spread_reward_deal',array($order_id));
        try{
            $flow_result = $this->start_flow($order_id);
        }catch(Exception $ex){
            if(APP_DEBUG){
                $m->add([
                    'order_id'=>'start_flow:'.$order_id,
                    'create_time'=>date('Y-m-d H:i:s'),
                    'log'=>$ex->getMessage()."\n".$ex->getFile()."\n".$ex->getLine()."\n".$ex->getCode()."\n"
                ]);
            }
        }
        $response_result->SetReturn_code('SUCCESS');
        ob_clean();
        echo $response_result->ToXml();
    }
    //使用SESSION中的订单号执行流程
    //远程调用
    public function start_flow_p($sess){
        $order_id=S($sess);
        empty($order_id) ||
        $this->start_flow($order_id);
    }
//    public function test(){
//        $this->spread_reward_deal('16052709100703932');
//        echo 'ko';
//    }
    //推广收益处理
    private function spread_reward_deal($order_id){
        //根据订单id获取总金额
        $total=$uid=null;
        if(!empty($order_id)){
            $info=M('order_list_parent','sp_')->field('price,uid,pay_status')->where(['order_id'=>$order_id])->find();
            if($info['pay_status']!='2'){
                wrong_return('订单状态不对');
            }
            $uid=$info['uid'];
            $total=$info['price'];
        }
        //获取后台推广设置
        if(!empty($total)){
            $c_model=new CommonModel();
            $s_model=M('spread','sp_');
            $u_model=M('users','sp_');
            //消费金额返积分
            if($score_reward=$c_model->get_conf('SCORE_REWARD')){
                $u_model->where(['id'=>$uid])->setInc('score',$total*$score_reward);
            }
            $spread_set=$s_model->where(['type'=>2])->find();
            if((!empty($spread_set))&&$spread_set['status']==1){
                $spread_uids=$this->get_uids_by_level($spread_set['level'],$uid);
                if($spread_uids){
                    $reward_per=explode(',',$spread_set['reward_per']);
                    $score_reward=explode(',',$spread_set['score_reward']);
                    $r_model=M('reward','sp_');
                    Db::startTrans();
                    try{
                        foreach($spread_uids as $k=>$v){
                            $user_status=$u_model->where(['id'=>$v])->getField('status');
                            if($user_status==1){
                                $r_model->add(['uid'=>$uid,'reward'=> round($total*$reward_per[$k]/100,2),'reward_score'=>$score_reward[$k],'create_time'=>time(),'total'=>$total,'level'=>$k+1,'spread_uid'=>$v,'month_time'=>intval(strtotime(date('Y-m-01',time())))]);
                            }
                        }
                        Db::commit();
                    }catch (Exception $e){
                        Db::rollback();
                    }
                }
            }
        }
    }
    //递归获取n级推广用户id
    private function get_uids_by_level($level,$uid,&$spread_uids=[]){
        $u_model=M('users','sp_');
        $spread_uid=$u_model->where(['id'=>$uid])->getField('spread_userid');
        if(!empty($spread_uid)&&(count($spread_uids)<$level)){
            $spread_uids[]=$spread_uid;
            $this->get_uids_by_level($level,$spread_uid,$spread_uids);
        }
        return $spread_uids;
    }
    /**
     * 爱贝回调
     * @return array
     */

    function aipay_result()
    {
        $aipay = new \app\pay\controller\Aipay();
        $platpkey = $this->get_conf("AIPAY_PLAT_KEY");
        $string = $_REQUEST;//接收post请求数据
        $cporderid;
        if ($string == null) {
            echo "请使用post方式提交数据";
        } else {
            $transdata = $string['transdata'];
            if (stripos("%22", $transdata)) { //判断接收到的数据是否做过 Urldecode处理，如果没有处理则对数据进行Urldecode处理
                $string = array_map('urldecode', $string);
            }
            $respData = 'transdata=' . $string['transdata'] . '&sign=' . $string['sign'] . '&signtype=' . $string['signtype'];//把数据组装成验签函数要求的参数格式
            //  验签函数parseResp（） 中 只接受明文数据。数据如：transdata={"appid":"3003686553","appuserid":"10123059","cporderid":"1234qwedfq2as123sdf3f1231234r","cpprivate":"11qwe123r23q232111","currency":"RMB","feetype":0,"money":0.12,"paytype":403,"result":0,"transid":"32011601231456558678","transtime":"2016-01-23 14:57:15","transtype":0,"waresid":1}&sign=jeSp7L6GtZaO/KiP5XSA4vvq5yxBpq4PFqXyEoktkPqkE5b8jS7aeHlgV5zDLIeyqfVJKKuypNUdrpMLbSQhC8G4pDwdpTs/GTbDw/stxFXBGgrt9zugWRcpL56k9XEXM5ao95fTu9PO8jMNfIV9mMMyTRLT3lCAJGrKL17xXv4=&signtype=RSA
            /*echo "进入了2".$respData;*/
            if (!$aipay->parseResp($respData, $platpkey, $respJson)) {
                //验签失败
                echo 'failed' . "\n";
            } else {
                //验签成功
                echo "success"."\n";
                //以下是 验签通过之后 对数据的解析。
                $transdata = $string['transdata'];
                $arr = json_decode($transdata);
                $data['cporderid'] = $arr->cporderid;

                /**检测订单号*/
                if (empty($data['cporderid'])) {
                    log_w("爱贝订单号为空获取失败");
                    die("SUCCESS");
                }
                /**初始化信息*/
                $order_id = $data['cporderid'];
                $this->pay_type = "aipay";
                /**获取父级订单信息*/
                $info = $this->m_pub->get_p_order_info($order_id);
                if (empty($info)) {
                    log_w("父级订单信息查询失败");
                    die("SUCCESS");
                }
                /**校验订单状态是否为1*/
                $pay_status = $info["pay_status"];
                if ($pay_status !== "1") {
                    log_w("父级订单标记不为未开通,有可能是您重复执行通知");
                    die("SUCCESS");
                }
                /**标记支付成功*/
                $m_exec = new ExecModel();
                $res = $m_exec->set_order_status($order_id, "2", "1");
                /**全部校验完毕,开始走下一步流程*/
                $m = M('notify', 'log_');
                try {
                    if ($res) {
                        $ali_result = $this->spread_reward_deal($order_id);
                    }
                } catch (Exception $ex) {
                    if (APP_DEBUG) {
                        $m->add([
                            'order_id' => 'ali_spread:' . $order_id,
                            'create_time' => date('Y-m-d H:i:s'),
                            'log' => $ex->getMessage() . "\n" . $ex->getFile() . "\n" . $ex->getLine() . "\n" . $ex->getCode() . "\n"
                        ]);
                    }
                }
                $this->start_flow($order_id,"aipay");
            }
        }
    }

    //获取配置信息
    private function get_conf($name) {
        $m_conf =  M('conf','sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }



}