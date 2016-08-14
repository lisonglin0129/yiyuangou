<?php
namespace app\admin\controller;

use app\admin\model\MenuModel;
use app\lib\Tree;
use think\Controller;

Class Menu extends Common
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
    //树形菜单
    public function show_list()
    {
        $tree = new Tree();
        $m_menu = new MenuModel();
        $menus = $m_menu->get_all_menu_list();

        $menus = $tree->toFormatTree($menus, "title");
        $menus = array_merge(array(0 => array('id' => 0, 'title_show' => '顶级菜单')), $menus);
        $this->assign('list', $menus);
        return $this->fetch();
    }

    //删除菜单
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m_menu = new MenuModel();
        $r=$m_menu->get_info_by_pid($id);
        $r && wrong_return('此菜单子级菜单不为空,不能删除');
        $m_menu->del_menu($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }



    //查看/编辑/新增
    public function exec()
    {
        $m_menu = new MenuModel();
        //查看,编辑
        $id = I("get.id",null);
        $type = I("get.type",null);
        //获取全部菜单
        $menus = $m_menu->get_all_menu_list();
        $tree = new Tree();
        $menus = $tree->toFormatTree($menus);
        $menus = array_merge(array(0 => array('id' => 0, 'title_show' => '顶级菜单')), $menus);
        $this->assign('menus', $menus);

        if(!empty($id)){
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $m_menu = new MenuModel();
            $info = $m_menu->get_menu_info_by_id($id);
            empty($info) && die('获取信息失败');
            $this->assign("info", $info);
            if($type=='edit'){
                $this->assign('type', 'edit');//编辑
            }
            else{
                $this->assign('type', 'see');//查看
            }
        }
        //新增
        else{
            $this->assign('type', 'add');//新增
        }

        return $this->fetch('form');
    }

    //执行添加菜单
    public function update()
    {
        $pid = $hide = $is_dev = $title = $sort = $ico = $url = $hide = $tip = $is_dev = $param = $target = $is_admin= 0;
        $post = I("post.");
        extract($post);
        //初始化
        !empty($sort) && $sort = intval($sort);//转换排序号

        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        empty($title) && wrong_return('标题不能为空');
//        empty($url) && wrong_return('url不能为空');
        (empty($pid) && $pid != 0 && $pid != '0') && wrong_return('上级菜单不能为空');
        (empty($hide) && $hide != 0 && $hide != '0') && wrong_return('隐藏选择不能为空');
        $m_menu = new MenuModel();
        //获取父级级别信息
        $p_info = $m_menu->get_menu_info_by_id($pid);

        //如果全部正确,执行写入数据
        $data = array(
            "id" => !empty($id) ? $id : "",
            "title" => $title,
            "status" => '1',
            "pid" => $pid,
            "level" => empty($p_info) ? 0 : ((int)$p_info['level'] + 1),
            "sort" => $sort,
            "ico" => $ico,
            "url" => $url,
            "hide" => $hide,
            "tip" => $tip,
            "param" => $param,
            "target" => $target,
            "is_admin"=>$is_admin
        );
        $rt = $m_menu->update($data);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }


}