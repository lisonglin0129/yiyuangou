<?php
namespace app\admin\controller;

use app\admin\model\OriginModel;
use app\admin\model\OrderListParentModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;

Class Origin extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }

    //订单列表
    public function show_list()
    {
        $field = I('post.field');
        //获取列表
        $condition_rule = array(
            array(
                'field' => $field,
                'value' => I('post.keywords')
            ),
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $origin = new OriginModel();
        $origin_list = $origin->get_origin_list($model);

        /*生成分页html*/
        $my_page = new Page($origin_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);
        $this->assign('origin_list', $origin_list['data']);
        return $this->fetch();

    }

    //查看/编辑/新增
    public function exec()
    {
        $type = $_GET['type'];
        if ($type !== 'add') {
            $id = $_GET['id'];
            $m = new OriginModel();
            //获取该ID对应的版本信息
            $res = $m->get_one_data($id);
            $this->assign('origin_data',$res);
        }
        $this->assign('type',$type);
        return $this->fetch('form');
    }

    //执行添加/修改
    public function update()
    {
        //获取表单信息
        $post = I("post.", []);
        $name = $code = '';
        extract($post);

        //标题
        empty(trim($name)) && wrong_return('渠道名称不能为空');
//        empty(trim($origin_url)) && wrong_return('渠道链接不能为空');
        empty(trim($code)) && wrong_return('渠道类型不能为空');

        //添加进库
        $m=new OriginModel();
        $res = $m->update($post);
        if($res===2){
            wrong_return('渠道名称重复');
        }
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    //删除
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new OriginModel();
        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }

    //一键刷新
    public function flush_form(){

        //获取渠道对应的数据
        $origin = new OriginModel();
        $origin->flush_data();
        return $this->redirect("index");
    }


}