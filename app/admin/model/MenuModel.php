<?php
namespace app\admin\model;

Class MenuModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('menu', "admin_");
    }

    //根据pid获取菜单列表
    public function get_menu_list($post)
    {
        $dev_sql = '';
        if (!in_array(get_user_name(), array("bqf9979", "sa", "root", "admin"))) {
            $dev_sql = " AND m1.is_dev <> 1 ";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS m1.id m1_id,m1.*,m2.id m2_id,m2.title m2_title FROM sp_menu m1
        LEFT JOIN sp_menu m2 ON m1.pid=m2.id
                WHERE m1.status<>'-1' " . $dev_sql . $post->wheresql .
            ' ORDER BY id DESC' . $post->ordersql . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";

        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];

        return $rt;


    }

    //获取全部菜单列表
    public function get_all_menu_list()
    {
        return $this->m->where(['status'=>['neq','-1']])->order("sort desc")->select();
    }
    //获取超级管理员的菜单列表
    public function get_admin_menu_list(){
        return $this->m->where(['status'=>['neq','-1'],'is_admin'=>1])->order("sort desc")->select();
    }

    //返回list_to_tree的菜单列表
//    public function menu_list_tree($uid)
//    {
//        $list = $this->get_user_auth_tree($uid);
//        session("menu", $list);
//        return $list;
//    }

    //根据用户获取用户有权限的菜单
    public function get_user_auth_tree($uid)
    {
        $role_list = M("users")->where(array("id"=>$uid))->find()["role_list"];
        $role_arr = explode(",",$role_list);

        $map['id'] = array('IN',$role_arr);
        $flag_arr = M("role_list","admin_")->field("is_super")->where($map)->select();
        $flag = false;
        foreach($flag_arr as $key=>$val){
            if($val['is_super']=="true"){
                $flag = true;
                break;
            }
        }


        $sa = array('bqf9979', 'admin', 'sa', 'root');
        if (in_array(get_user_name(), $sa) || preg_match('/demo.*/', get_user_name())||$flag) {
            $list = $this->get_admin_menu_list();
            if (!empty($list)) {
                return list_to_tree($list);
            }
            return false;
        }
        $m_user = new UserModel();
        $user_info = $m_user->get_user_info_by_id($uid);
        if (empty($user_info["role_list"])) return false;
        $role_list = $user_info["role_list"];
        if (empty($role_list)) return false;
        $role_list = str_implode($role_list);
        //获取用户组的权限
        $m_role = new RoleListModel();
        $privilege_list = $m_role->get_role_list_by_ids($role_list);
        if (empty($privilege_list)) return false;
        //拼合权限列表
        $sp = "";
        foreach ($privilege_list as $k => $v) {
            $sp .= $v['auth_list'] . ',';
        }
        $sp = str2arr($sp);
        $sp = array_filter($sp);
        $sp = implode(',', $sp);
        //获取全部菜单列表
        $m_menu = new MenuModel();
        $list = $m_menu->get_menu_list_by_ids($sp);
        if (!empty($list)) {
            return list_to_tree($list);
        } else {
            return false;
        }
    }

    //根据ids获取菜单
    public function get_menu_list_by_ids($ids)
    {
        if (empty($ids)) return false;
        $m = M('menu', 'sp_');
      //  $where['hide']=false;
        $where['status']=['neq',-1];
        $where['id']=['in',$ids];
        return $m->where($where)->select();
    }

    //添加菜单
    public function update($post)
    {
        $id = $title = $pid = $sort = $ico = $url = $tip = $status = $level = $param = $target = $is_admin=null;
        extract($post);
        $data = array(
            "title" => $title,
            "pid" => empty($pid) ? 0 : $pid,
            "sort" => $sort,
            "ico" => $ico,
            "url" => empty($url) ? null : strtolower($url),
            "tip" => $tip,
            "status" => $status,
            "level" => $level,
            "param" => $param,
            "target" => $target,
            "is_admin"=>$is_admin
        );

        if (!empty($id) && is_numeric($id)) {
            $r = $this->m->where(array("id" => $id))->save($data);
            if ($r === false) return false;
        } else {
            $r = $this->m->add($data);
            if ($r) $id = $r;
        }
        //写入当前path
        if ($id) {
            $this->set_path($id);
            return true;
        }

        return false;
    }

    //删除菜单
    public function del_menu($id)
    {
        return $this->m->where(array("id" => $id))->save(array("status" => "-1"));
    }

    //获取菜单详情
    public function get_menu_info_by_id($id)
    {
        return $this->m->where(array("id" => $id))->find();
    }

    //获取菜单pid
    public function get_info_by_pid($pid)
    {
        return $this->m->where(array("pid" => $pid, 'status' => array("neq", "-1")))->find();
    }

    //写入菜单到session
    public function write_menu_session()
    {
//        $list = $this->$this->menu_list_tree();
//        $list = $this->get_user_auth_tree();
//        session("menu", $list);
//        return true;
    }

    //设置数据库中path,递归下面全部
    private function set_path($id)
    {
        //设置主帐号的
        if (empty($id)) return;
        //获取该菜单下的全部子菜单
        $info = $this->get_menu_info_by_id($id);
        $p_info = $this->get_menu_info_by_id($info['pid']);

        if (empty($info['pid'])) {
            $info['path'] = $info['id'];
            $this->m->where(array("id" => $info['id']))->save(array("path" => $info['path']));
        } else {
            $info['path'] = $p_info['path'] . '-' . $info['id'];
            $this->m->where(array("id" => $info['id']))->save(array("path" => $info['path']));
        }
        //递归设置子账号的
        $this->digui_menu($id);
    }

    //根据id递归该菜单下全部的菜单的路径
    private function digui_menu($id)
    {
        if (empty($id)) return;
        //获取该菜单下的全部子菜单
        $list = $this->m->where(array("pid" => $id))->select();
        $info = $this->get_menu_info_by_id($id);
        foreach ($list as $k => $v) {
            //获取子菜单父级菜单信息,上级为空则直接等于id
            if (empty($v['pid'])) {
                $this->m->where(array("id" => $v['id']))->save(array("path" => $v['id']));
            } else {
                $this->m->where(array("id" => $v['id']))->save(array("path" => $info['path'] . '-' . $v['id']));

            }

            $this->digui_menu($v['id']);
        }
    }

    //获取选中地址
    public function get_select_menu($path_info,$param)
    {

        $where = array(
            'url' => $path_info,
            'status' => 1

        );
        if ( !empty($param) )
            $where['param'] = $param;
        $path = $this->m->field('path')->where($where)->find()['path'];
        //查找不到菜单,进行非精确查找
        if ( is_null($path) ) {
            //判断是否为分类菜单
            $path_info = $this->format_path_info($path_info);
            $path = $this->m->field('pid')->where(array('url' => $path_info,
                'status' => 1))->find();
            $path = is_null($path)?array():$path;
            return array(
                'select_path'=>'',
                'open_path' => $path,
            );
        }

        $path_arr = explode('-', $path);
        $select_path = array_pop($path_arr);
        return array(
            'select_path' => $select_path,
            'open_path' => $path_arr
        );
    }

    private function format_path_info($path_info) {
        $path_info = isset($_GET['mid'])?$path_info.'/mid/'.$_GET['mid']:$path_info;
        $path_info = isset($_GET['form_zero'])?$path_info.'/form_zero':$path_info;

        return $path_info;

    }

    //得到用户权限管理中的菜单列表
    public function get_auth_menu_list() {

        return $this->m->field('url,title as name,id,pid')->where("status <> '-1'")->order("sort desc")->select();

    }

}