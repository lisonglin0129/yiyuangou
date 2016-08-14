<?php
use \app\admin\model\ConfModel;
function getConfigs($flag) {
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



class Config{
	private $cfg = array(
			'url' =>  'https://pay.swiftpass.cn/pay/gateway',
			'mchId'=> '',
			'key' =>  '',
			'version'=> '1.0',
			'Auth' =>'https://open.weixin.qq.com/connect/oauth2/authorize',
			'access_token' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
			'grant_type' => 'authorization_code'
	);
	public function __construct() {
		$this->cfg['mchId']  =  getConfigs('WX_H5')['value'];
		$this->cfg['key']    =  getConfigs('WX_H%_KEY')['value'];
		$this->cfg['Auth']   =  'https://open.weixin.qq.com/connect/oauth2/authorize';
	}
	public function C($cfgName){
		return $this->cfg[$cfgName];
	}
}
?>