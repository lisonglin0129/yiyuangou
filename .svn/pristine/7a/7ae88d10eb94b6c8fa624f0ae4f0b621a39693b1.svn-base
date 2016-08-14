<?php
namespace app\admin\controller;

use app\admin\model\CategoryModel;
use app\admin\model\CategoryListModel;
use app\lib\Page;
use app\lib\Condition;
use app\lib\Tree;
use think\Controller;

Class Category extends Common
{

    public function __construct()
    {
        parent::__construct();
    }

    //分类管理INDEX页面
    public function index(){
        return $this->fetch();
    }

    //分类详情列表页
    public function show_list(){

        //获取列表
        $condition_rule = array(
            array(
                'field' => 'name',
                'value' => I('post.keywords')
            ),
        );
        $cate_list = new CategoryModel();
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $res = $cate_list->get_category($model);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('cate_list', $res['data']);
        return $this->fetch();
    }
    //编辑/新增
    public function exec()
    {
        $type = $_GET['type'];

        if($type!=="add"){
            $id = $_GET['id'];
            //获取该ID对应的分类信息
            $m = new CategoryModel();
            $res = $m->get_class_info_by_id($id);
            $this->assign('cate_data',$res);

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
        empty($orders) && wrong_return('排序号不能为空');
        empty($status) && wrong_return('状态不能为空');
        empty($name) && wrong_return('分类名称不能为空');
        empty($tips) && wrong_return('分类标签不能为空');
        empty($code) && wrong_return('菜单类型不能为空');
        empty($type) && wrong_return('url不能为空');

        //添加进库
        $m=new CategoryModel();
        $res = $m->update2($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    //删除
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new CategoryModel();
        !empty($m->get_category_list($id)) && wrong_return('含有子分类,无法删除。');
        $m->del_category($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }

    public function son_index(){
        $id = $_GET['id'];
        $cate_name = M("category")->where(array("id"=>$id))->find()['name'];

        $this->assign('cate_name',$cate_name);
        $this->assign('id',$id);
        return $this->fetch();
    }

    //子分类列表
    public function son_show_list(){
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'name',
                'value' => I('post.keywords')
            ),
            array(
                'field' => 'mid',
                'value' => I('get.id')
            ),
        );
        $son_cate_list = new CategoryModel();
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $res = $son_cate_list->get_son_cate_list($model);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('son_show_list'), 5);
        $pages = $my_page->myde_write();

        $data = $res['data'];
        //获取分类DATA
        $cate_data=M('category')->where(array('status'=>'1'))->select();
        foreach($cate_data as $key=>$value){
            foreach($res['data'] as $key2=>$value2){
                if($value['id'] == $value2['mid']){
                    $res['data'][$key2]['mid'] = $value['name'];
                }
            }
        }

        $tree = new Tree();

        $data = $tree->toFormatTree($data, "name");

        foreach($res['data'] as $key=>$value){
            foreach($res['data'] as $key2=>$value2){
                if($value['id'] == $value2['pid']){
                    $res['data'][$key2]['pid'] = $value['name'];
                }
            }
        }

        $this->assign('mid',I('get.id'));
        $this->assign('pages', $pages);
        $this->assign('son_cate_list',$data);
        return $this->fetch();
    }

    //编辑/新增
    public function son_exec()
    {
        $where = "";
        $type = $_GET['type'];
        $mid = $where['mid'] = $_GET['mid'];

        $m_cate  = new CategoryModel();
        $son_cates = array();
        if($type!=="add"){
            $id = $_GET['id'];
            //获取该ID对应的分类信息
            $m = new CategoryListModel();

            $where['id'] = ['neq',$id];
            //获取下级子分类
            $son_cates = $m_cate->get_son_cates($id);


            $res = $m->get_one_data($id);
            $this->assign('son_cate_data',$res[0]);
        }


        //获取category列表
        $cate_data = M('category')->where(array('status'=>'1'))->select();
        //获取子分类列表
        $son_cate_list = M('category_list')->field('id,name,pid')->where($where)->select();
        $cate_type = $m_cate->get_cate_type($mid);

        //获得非下级分类的所有栏目
        $son_cate_list = $this->diif_son_cates($son_cate_list,$son_cates);
        $tree = new Tree();
        $son_cate_list = $tree->toFormatTree($son_cate_list, "name");

        $this->assign('son_cate_list',$son_cate_list);
        $this->assign('cate_data',$cate_data);
        $this->assign('cate_type',$cate_type);
        $this->assign('type',$type);
        return $this->fetch('son_form');
    }

    //获取非子类的所有分类
    private function diif_son_cates($son_cate_list,$son_cates) {
        foreach ( $son_cate_list as $key => $cate_list ) {
            foreach ( $son_cates as $cate ) {
                if ( $cate_list === $cate ) {
                    unset($son_cate_list[$key]);
                }
            }
        }
        return $son_cate_list;

    }


    //执行添加子分类
    public function son_update()
    {
        //获取表单信息
        $post = I("post.", []);
        extract($post);

        //标题
        empty($name) && wrong_return('分类名称不能为空');
        empty($mid) && wrong_return('所属分类不能为空');



        //添加进库
        $m=new CategoryModel();
        $res = $m->update3($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    //删除
    public function son_del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new CategoryModel();
        !empty($m->get_list_by_pid($id)) && wrong_return('含有子分类,无法删除。');
        $m->del_category_child($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }




}