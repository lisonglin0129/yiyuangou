<?php
namespace app\admin\controller;

use app\admin\model\AppManageModel;
use app\admin\model\AppIndexModel;
use app\admin\model\AppConfModel;
use app\lib\Condition;
use app\lib\Page;
use think\Controller;

Class AppConf extends Common
{

    public function __construct()
    {
        parent::__construct();
    }

    //APP配置管理INDEX页面
    public function index(){
        return $this->fetch();
    }

    //列表
    public function show_list()
    {
        //获取所有包名字
        $data = M("app_conf")->field("name,id")->where("status<>-1")->select();
        $this->assign("name",$data);
        return $this->fetch();

    }
    //查看/编辑/新增
    public function exec()
    {
        $type = $_GET['type'];
        if ($type !== 'add') {
            $id = $_GET['id'];
            $data = M("app_conf")->where(array("id"=>$id))->find();
            $this->assign("conf_data",$data);
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

        empty($name) && wrong_return('包名字不能为空');
        //标题
        /*empty($plantform) && wrong_return('系统平台不能为空');
        empty($version) && wrong_return('版本不能为空');
        empty($code) && wrong_return('版本2不能为空');
        empty($desc) && wrong_return('描述不能为空');
        empty($url) && wrong_return('url不能为空');*/

        //添加进库
        $m=new AppConfModel();
        $res = $m->update($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    //删除
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new AppConfModel();
        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }


}