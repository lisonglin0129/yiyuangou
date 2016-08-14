<?php
namespace app\admin\model;

Class UserModel
{
    private $u;
    private $member;

    function __construct()
    {
        $this->u = M("users", "sp_");
//        $this->member = M("pcvpn_member", null, 'RADIUS');
    }

    //获取全部用户列表
    public function get_user_list($post)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
        FROM  sp_users
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $user_info = $this->m_model->query($sql);
        $num = $this->m_model->query($sql_count);

        $rt["data"] = $user_info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //检测用户名
    public function get_user_info_by_user_login($user_login)
    {
        return $this->u->where(array("user_login" => $user_login))->where("status<>-1")->find();
    }

    //根据id获取用户名
    public function get_user_info_by_id($id)
    {
        return $this->u->where(array("id" => $id))->where("status<>-1")->find();
    }

    //添加新用户
    public function update($post)

    {
        $role_list=$status = $user_login = $user_pass = $type = $phone = $nick_name = $from_domain = $section = $sex = null;
        extract($post);
        $data = array(
            "status" => $status,
            "user_login" => strtolower($user_login),

            "type" => $type,
            "phone" => $phone,
            "nick_name" => $nick_name,
            "from_domain" => $from_domain,
            "section" => $section,
            "role_list" => $role_list,
            "sex" => $sex,
            "create_time" => NOW_TIME
        );
        !empty($user_pass) && $data["user_pass"] = md6($user_pass);
        if (!empty($id) && is_numeric($id)) {
            $info = $this->u->where(array("id" => $id))->save($data);
            return $info !== false;
        } else {
            return $this->u->add($data);
        }
    }
    //保存用户
    public function save_user($post)
    {
        extract($post);
        $data = array(
            "status" => $status,
            "type" => $type,
            "phone" => $phone,
            "nick_name" => $nick_name,
            "sex" => $sex,
            "section" => $section,
            "update_time" => NOW_TIME
        );
        !empty($user_pass) && $data["user_pass"] = md6($user_pass);
        return $this->u->where(array("id" => $id))->save($data);
    }

    //删除菜单
    public function del_user($id)
    {
        return $this->u->where(array("id" => $id))->save(array("status" => "-1"));
    }

    //保存用户最后一次登录ip和时间
    public function save_last_ip_and_time($user_login)
    {
        $data = array(
            "last_login_ip" => get_client_ip(),
            "last_login_time" => NOW_TIME,
        );
        $this->add_login_log($user_login);
        return $this->u->where(array("user_login" => $user_login))->save($data);

    }

    //添加登录日志
    public function add_login_log($user_login)
    {
        $m_login_log = M('login', "log_");
        $data = array(
            "user_login" => $user_login,
            "ip" => get_client_ip(),
            "login_time" => date("Y-m-d H:i:s"),
        );
        return $m_login_log->where(array("user_login" => $user_login))->add($data);
    }


    //根据列保存列的值
    public function save_user_info_by_uid($uid, $field, $value)
    {
        if (empty($uid)) return false;
        $sql = "UPDATE sp_users SET ".$field."='".$value."' WHERE id=".$uid;
        $m_user = M("users", "sp_");

        return $m_user->execute($sql);
    }

    //获取角色列表
    public function get_role_list()
    {
        $m_role = M("role", "sp_");
        return $m_role->select();
    }

    //执行添加角色
    public function role_add_do($post)
    {
        $m_role = M("users", "sp_");
        $id = $post['id'];
        $role_list = implode(",", $post['role_list']);
        return $m_role->where(array("id" => $id))->save(array("role_list" => $role_list));
        //die($m_role->getLastSql());
    }

    //获取管理员信息
    public function get_user_info($id)
    {
        $m_user = M("users", "sp_");
        return $m_user->where(array("id" => $id))->find();
    }

    //根据管理员信息获取角色信息
    public function get_role_info($role_list)
    {
        $m_role = M("role", "sp_");
        return $m_role->where(array("id" => array("in", $role_list)))->select();
    }

    //根据角色信息字符串获取菜单权限
    public function get_menu_info($auth_list)
    {
        $m_menu = M("menu", "sp_");
        return $m_menu->where(array("id" => array("in", $auth_list)))->order("`sort`,id")->select();
    }

    //获取数据分类树结构
    public function get_tree($data, $pid = 0, $level = 0, $isClear = TRUE)
    {
        static $ret = array();
        if ($isClear)
            $ret = array();
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level;
                $ret[] = $v;
                $this->get_tree($data, $v['id'], $level + 1, FALSE);
            }
        }
        return $ret;
    }

    /**
     * 获取指定中奖用户
     */
    public function get_set_user($username){
        $user_arr = array();
        $where = array(
            'username' => array('like','%'.$username.'%'),
        );
        $users = $this->u->where($where)->limit(0,5)->select();
        foreach ($users as $key => $user) {
            $user_arr[$key]['id']  = $user['id'];
            $user_arr[$key]['name'] = $user['username'];
        }
        return $user_arr;

    }
}