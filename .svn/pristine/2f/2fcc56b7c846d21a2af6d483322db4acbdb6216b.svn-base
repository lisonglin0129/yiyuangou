<?php
namespace app\admin\controller;

use app\admin\model\AdminRoleModel;
use app\admin\model\MenuModel;
use app\lib\Condition;
use app\lib\Tree;
use think\Controller;
use app\lib\Page;

Class Auth extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //入口首页
    public function role()
    {
        return $this->fetch();
    }
    //子用户管理
    public function child_user()
    {
        return $this->fetch();
    }
    //审核管理
    public function audit()
    {
        return $this->fetch();
    }

    //角色列表
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
        $admin_role = new AdminRoleModel();
        $admin_role_list = $admin_role->get_role_list($model);

        /*生成分页html*/
        $my_page = new Page($admin_role_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('admin_role_list', $admin_role_list['data']);
        return $this->fetch();
    }


    //查看/编辑/新增
    public function exec()
    {
        $m = new AdminRoleModel();

        $menu = new MenuModel();

        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);

        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $m->get_info_by_id($id);
            empty($info) && die('获取信息失败');
            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
            } else {
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }

        //所有角色列表
        $all_role_list = $m->get_all_role_list();
        $tree = new Tree();
        $all_role = $tree->toFormatTree($all_role_list,'name');

        //所有菜单
        $menu_list = $menu->get_auth_menu_list();
        //用于
        $index_menu_list = $tree->toFormatTree($menu_list,'name');
        //如果是修改，设定选中的选项（用于权限列表）
        //形成zTree所需要的数据格式
        $auth_list_arr = array();
        if(isset($info) && !empty($info)) {
            $auth_list_arr = explode(',',$info['auth_list']);
        }
        if(!empty($menu_list)) {
            foreach($menu_list as $key=>$value) {
                if(in_array($value['id'],$auth_list_arr)) {
                    $menu_list[$key]['checked'] = true;
                }
                if(is_null($value['url'])) {
                    $menu_list[$key]['open'] = true;
                }
                $menu_list[$key]['pId'] = $value['pid'];
                unset($menu_list[$key]['pid']);
                unset($menu_list[$key]['url']);
            }
        }

        $this->assign('index_menu_list',$index_menu_list);
        $this->assign('menu_list',json_encode($menu_list));
        $this->assign('all_role',$all_role);
        return $this->fetch('form');
    }

    //执行添加角色
    public function update()
    {

        $post = I("post.");
        extract($post);
        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        $m = new AdminRoleModel();


        $rt = $m->update_role($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //删除角色
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new AdminRoleModel();
        $m->del_role($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }




}