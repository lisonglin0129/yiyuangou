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
			'url' =>  '',
			'mchId'=> '',
			'key' =>  '',
			'version'=> '1.0'
	);
	public function __construct() {
		$this->cfg['url']    =   getConfigs('WX_THREE_INFO_URL')['value'];
		$this->cfg['mchId']  =  getConfigs('WX_THREE_MCHID')['value'];
		$this->cfg['key']    =   getConfigs('WX_THREE_KEY')['value'];
	}
	public function C($cfgName){
		return $this->cfg[$cfgName];
	}
}
?>