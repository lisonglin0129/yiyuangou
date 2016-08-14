<?php
namespace app\admin\controller;

use app\admin\model\AppManageModel;
use app\admin\model\AppIndexModel;
use app\lib\Condition;
use app\lib\Page;
use think\Controller;

Class Appmanage extends Common
{

    public function __construct()
    {
        parent::__construct();
    }

    //APP版本管理INDEX页面
    public function index(){
        return $this->fetch();
    }

    //列表
    public function show_list()
    {
        $app_version_list=new AppManageModel();
        $res=$app_version_list->get_version_list();
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('version_list', $res['data']);
        return $this->fetch();

    }
    //查看/编辑/新增
    public function exec()
    {
        $type = $_GET['type'];
        $m = new AppManageModel();
            if ($type !== 'add') {
                $id = $_GET['id'];
                //获取该ID对应的版本信息
                $res = $m->get_one_data($id);
                $this->assign('ver_data',$res);
            } else {
                $max_code = $m->get_max_code() + 1;
                $this->assign('max_code',$max_code);
            }
        $this->assign('type',$type);
        return $this->fetch('form');
    }

    //执行添加菜单
    public function update()
    {
        //获取表单信息
        $post = I("post.", []);
        extract($post);

        //标题
        empty($plantform) && wrong_return('系统平台不能为空');
        empty($version) && wrong_return('版本不能为空');
        empty($code) && wrong_return('版本2不能为空');
        empty($desc) && wrong_return('描述不能为空');
        empty($url) && wrong_return('url不能为空');

        //添加进库
        $m=new AppManageModel();
        $res = $m->update($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    //删除
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new AppManageModel();
        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }

    //APP引导页面管理index
    public function guide(){
        return $this->fetch();
    }

    //APP引导页面详情列表页
    public function guide_show_list(){
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'plat',
                'value' => I('post.keywords')
            ),
        );
        $guide = new AppIndexModel();

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $res = $guide->get_app_guide($model);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('guide_list', $res['data']);
        return $this->fetch();
    }

    //app引导页面表单
    //查看/编辑/新增
    public function guide_exec()
    {
        $type = $_GET['type'];

        if($type!=="add"){
            $id = $_GET['id'];
            //获取该ID对应的分类信息
            $m = new AppIndexModel();
            $res = $m->get_guide_data($id);
            $this->assign('guide_data',$res);

        }
        $this->assign('type',$type);
        return $this->fetch('guide_form');

    }

    //APP引导页面添加/编辑操作
    //执行添加菜单
    public function guide_update()
    {
        //获取表单信息
        $post = I("post.", []);
        extract($post);


        //标题
        empty($plat) && wrong_return('系统平台不能为空');
        empty($guide_type) && wrong_return('引导类型不能为空');
        empty($img_path) && wrong_return('图片URL不能为空');
        empty($order) && wrong_return('排序号不能为空');

        //添加进库
        $m=new AppIndexModel();
        $res = $m->update2($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }
    public function guide_del(){
            $id = I("post.id");
            (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
            $m = new AppIndexModel();
            $m->del($id) !== false && ok_return('删除成功');
            wrong_return('删除操作失败');
    }




}