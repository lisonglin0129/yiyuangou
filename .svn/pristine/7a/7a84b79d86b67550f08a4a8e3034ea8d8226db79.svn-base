<?php
namespace app\core\controller;

use think\Controller;

Class Auth extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_domain();
        $this->set_conf();
        $this->init();
    }
    //设置配置
    private function set_conf(){
        //配置不存在
        if(empty(S('config_cache'))){
            /* 读取站点配置 */
            $m = M('conf','sp_');
            $r = $m->select();
            foreach($r as $k=>$v){
                $r[$k]['name'] = strtoupper($r[$k]['name']);
            }
            $r = array_column($r,'value','name');
            S('config_cache',$r);
        }
        //取配置,赋值
        C(S('config_cache')); //添加配置
    }

    //检测域名
    private function check_domain(){
        //CLI模式下绕过
        if(IS_CLI){
            return true;
        }
//        !preg_match('/.*qs710\.com.*/',$_SERVER['HTTP_HOST'])&&wrong_return('domain error');
    }


    //初始化
    private function init(){
        if(C('WEB_SITE_CLOSE')=='true'){
            die('站点已经关闭，请稍后访问~');
        }
    }
}