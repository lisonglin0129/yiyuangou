<?php
namespace app\admin\controller;
use app\admin\model\NperListModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;

Class Nper extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->del_model= new NperListModel();
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }


    //期数列表
    public function show_list()
    {
        //获取列表
        $condition_rule = array(
            array(
                'field' => I('post.field'),
                'value' => I('post.keywords')
            ),
        );
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $nper = new NperListModel();
        $nper_list = $nper->get_nper_list($model);
        /*生成分页html*/
        $my_page = new Page($nper_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('nper_list', $nper_list['data']);
        return $this->fetch();
    }

    //删除
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new NperListModel();
        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }








}