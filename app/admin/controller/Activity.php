<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\ActivityModel;
use app\lib\Condition;
use app\lib\Page;


Class Activity extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //活动管理index
    public function index(){
        return $this->fetch();
    }

    //活动详情列表页
    public function show_list(){
        $keywords = null;
        $post = remove_arr_xss(I("post.", []));
        extract($post);
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'name',
                'value' => I('post.keywords')
            ),
        );
        $pay_list = new ActivityModel();
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $res = $pay_list->get_act_list($model);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('act_list', $res['data']);
        return $this->fetch();
    }

    //编辑/新增
    public function exec()
    {
        $type = $_GET['type'];

        if ($type == 'edit') {
            $id = $_GET['id'];

            $m = new ActivityModel();
            //获取该ID对应的版本信息
            $res = $m->get_one_data($id);
            $this->assign('act_data',$res);
            $this->assign('type', 'edit');//编辑
        }else if($type == "see"){
            $id = $_GET['id'];
            $m = new ActivityModel();
            //获取该ID对应的版本信息
            $res = $m->get_one_data($id);
            $this->assign('act_data',$res);
            $this->assign('type', 'see');//查看
        } else {
            $this->assign('type', 'add');//新增
        }
        return $this->fetch('form');
    }

    //执行添加/修改
    public function update()
    {
        //获取表单信息
        $post = I("post.", []);
        extract($post);

        //标题
        empty($name) && wrong_return('活动名称不能为空');
        empty($desc) && wrong_return('活动描述不能为空');
        empty($link) && wrong_return('活动链接不能为空');
        empty($src) && wrong_return('图片地址不能为空');
        (!isset($flag)) && wrong_return('显示标志位不能为空');
        empty($sort) && wrong_return('排列序号不能为空');

        //添加进库
        $m=new ActivityModel();
        $res = $m->update($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    //删除
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new ActivityModel();
        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }




}