<?php
namespace app\admin\controller;

use app\admin\model\SystemModel;
use Think\Model;

Class Index extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //入口首页
    public function index()
    {
        $system = new SystemModel();

        //管理员信息
        $admin_info = $system->get_admin_info();
        //系统信息
        $system_info = $system->get_system_info();
        //网站信息
//        $web_info = $system->get_web_info();

        //数据统计
        $data_statistics = $system->get_data_statistics();

        //最新订单
        $new_order = $system->get_new_order();

        $this->assign('admin_info',$admin_info);
        $this->assign('system_info',$system_info);
//        $this->assign('web_info',$web_info);
        $this->assign('data_statistics',$data_statistics);
        $this->assign('new_order',$new_order);
        return $this->fetch();
    }

    //入口首页
    public function home()
    {
        $system = new SystemModel();

        //管理员信息
        $admin_info = $system->get_admin_info();
        //系统信息
        $system_info = $system->get_system_info();
        //网站信息
        $web_info = $system->get_web_info();

        //数据统计
        $data_statistics = $system->get_data_statistics();

        //最新订单
        $new_order = $system->get_new_order();

        $this->assign('admin_info',$admin_info);
        $this->assign('system_info',$system_info);
        $this->assign('web_info',$web_info);
        $this->assign('data_statistics',$data_statistics);
        $this->assign('new_order',$new_order);
        return $this->fetch();
    }

    //
    public function index_layout()
    {
        return $this->fetch();
    }
    public function main()
    {
        return $this->fetch();
    }
}