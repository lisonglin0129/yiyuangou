<?php
namespace app\admin\controller;

use app\admin\model\CategoryModel;
use app\admin\model\CommonModel;
use app\admin\model\ConfModel;
use app\core\controller\Auth;
use app\core\model\LogBehaviorModel;
use think\Controller;

Class Common extends Auth
{
    public $page;
    public $page_num;
    public $del_model;

    public function __construct()
    {
        parent::__construct();
        $this->chk_conf();
        $this->chk_login();
        //第几页
        $this->page = (int)I('post.page', '1');
        $this->page_num = 20;//每页显示条数
        $data = $this->get_notice_data();
        $links = $this->get_links();
        $this->assign('links',$links);
        $this->assign('data',$data);
        //记录日志
        if(C('ADMIN_LOG_BEHAVIOR')=='1'){
            if(!in_array(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,C('NO_LOG_URL'))){
                $l_model=new LogBehaviorModel();
                $l_model->log_behavior_add();
            }
        }
    }


    public function chk_conf(){
        $conf = new ConfModel();
        $info = $conf->check_remark_conf();
        if($info['remark'] == "1"){
            return true;
        }
        $path = './data/key/conf.lock';
        if(file_exists($path)){
            $this->redirect(U('config/get_config_info'));
            die();
        }
    }
    private function chk_login()
    {
        //CLI模式下绕过
        if(IS_CLI){
            return true;
        }
        $allow_list = [
            'admin/rt_presets/run_task_1s',
            'admin/rt_presets/run_task_1min',
            'admin/rt_regular/auto_exec',
        ];
        $path = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        if ( in_array($path,$allow_list) ) {
            return true;
        }
//        if(strtolower(MODULE_NAME) == 'admin' && strtolower(CONTROLLER_NAME) == 'rt_presets'
//            || strtolower(MODULE_NAME) == 'admin' && strtolower(CONTROLLER_NAME) =='rt_regular' )
//        {
//            if(strtolower(ACTION_NAME) == 'run_task_1s'||strtolower(ACTION_NAME) == 'run_task_1min' || 'auto_exec' == strtolower(ACTION_NAME))
//            //本地访问此方法不需要登录
//            //if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
//                return true;
//            //}
//        }
        $user_type=cookie('user_type');
        $this->assign('user_type',!empty($user_type)?cookie('user_type'):'');
        if (!is_user_login() || login_group() != '1') {
            if((!empty($user_type))&&($user_type==2)){
                $this->redirect(U('account/shop_login'));
                die();
            }
            $this->redirect(U('account/index'));
            die();
        }
    }

    public function get_links() {
        $m_cate = new CategoryModel();
        return $m_cate->get_category_by_code('youqinglianjie');

    }

    private function get_notice_data()
    {
        $m_common = new CommonModel();
        $data['num'] = $m_common->get_rt_show_order('num');
        $data['info'] = $m_common->get_rt_show_order('info');
        return $data;

    }

    public function del_select() {
        $m = $this->del_model;
        $ids = I('post.');
        $ids = $ids['id'];
        empty($ids) && wrong_return('删除失败,请选择要删除的数据');
        !method_exists($m,'del_select') && wrong_return('未找到相应的删除功能,批量删除失败。');
        $m->del_select($ids) !== false && ok_return('批量删除成功');
        wrong_return('批量删除失败');

    }

}