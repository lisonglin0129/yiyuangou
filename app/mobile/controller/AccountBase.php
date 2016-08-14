<?php
namespace app\mobile\controller;

use \think\Controller;

//控制器基类
class AccountBase extends Base
{

    public function __construct()
    {
    	
        parent::__construct();
        if (!in_array(strtolower(ACTION_NAME), array('return_url'))){
            if (IS_AJAX) {
                !is_user_login() && die_json(array(
                    'code' => '199',
                    'status' => 'fail',
                    'message' => '请先登录'
                ));
            } elseif (I('get.origin') == 'IOS') {
                //用于ios调用支付宝页面
            } else {
                if (!is_user_login()) {
                    session('login_refer', $_SERVER['REQUEST_URI']);
                    $this->redirect('otherUsers/login');
                    die();
                }
            }
        }
    }

    //获取配置信息
    public function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }


}
