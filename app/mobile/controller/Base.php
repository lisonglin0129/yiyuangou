<?php
namespace app\mobile\controller;
use app\core\controller\Auth;
use app\core\model\LogBehaviorModel;
use \think\Controller;

//控制器基类
class Base extends Auth
{

    public function __construct()
    {
        parent::__construct();
        $info =  $this->get_info();
        $this->assign('wap_config_info',$info);
        $this->assign('zero_start',$this->get_conf('zero_start'));
        //添加行为日志
        if(C('MOBILE_LOG_BEHAVIOR')=='1'){
            if(!in_array(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,C('NO_LOG_URL'))){
                $l_model=new LogBehaviorModel();
                $l_model->log_behavior_add();
            }
        }

        //需要登录
        if (IS_GET && I('get.need_login','0','trim')=='1' && !is_user_login()) {
            session('login_refer', $_SERVER['REQUEST_URI']);
            if(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')!==false && C('UNION_WEICHAT_SWITH') == 1){
                $this->redirect(U('Login/login',array('type'=>'wechat')));
            }else{
                $this->redirect('otherUsers/login');
            }
            die();
        }
    }


    //获取配置信息
    public function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }

    /**
     * 判断是否登录
     */
    public function is_login()
    {
        if (!is_user_login()) {
            $this->redirect('other_users/login');
            die();
        }

    }

    /**
     * ajax判断是否登录
     */
    public function ajax_is_login()
    {
        !is_user_login() && die_json(array(
            'code' => '199',
            'status' => 'fail',
            'message' => '请先登录'
        ));
    }

    //判断是否通过Ajax访问
    public function ajax_request()
    {
        !IS_AJAX && $this->redirect('Index/index');
    }

    //获取公共配置信息
    public function get_info()
    {
        $info = array();
        $m = new \app\mobile\model\Base();
        //获得LOGO配置
        $info['logo'] = $m->get_conf('WAP_WEB_LOGO');
        //获取备案配置
        $info['record_num'] = $m->get_conf('WAP_WEB_RECORD_NUM');
        //获取版权配置
        $info['copy_right'] = $m->get_conf('WAP_WEB_COPYRIGHT');
        $info['title'] = $m->get_conf('WAP_WEB_TITLE');
        return $info;
    }


}
