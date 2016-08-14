<?php
namespace app\admin\model;

Class AdminRoleModel extends BaseModel
{
    public $admin_role_list_model;

    public function __construct()
    {
        $this->admin_role_list_model = M('admin_role_list','');
    }

    //获取角色列表
    public function get_role_list($post)
    {
        $m = M('admin_role_list', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  admin_role_list
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);



        if($info) {
            foreach($info as $key=>$value) {
                if($value['pid'] != 0) {
                    $info[$key]['parent_user'] = $this->admin_role_list_model->field('name')->where(array("id"=>$value['pid']))->find()['name'];
                }
                $info[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
                $info[$key]['update_time'] = date('Y-m-d H:i:s',$value['update_time']);
            }
        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }

    //获取用户详情

    public function get_info_by_id($id)
    {
        $user_info = $this->admin_role_list_model->where(array("id" => $id))->find();
        return $user_info;
    }


    public function get_all_role_list() {
        return $this->admin_role_list_model->where("status <> '-1'")->order("sort desc")->select();
    }

    /**
     * 用于后台管理员选择角色
     * @return mixed
     */
    public function get_admin_role_list() {
        return $this->admin_role_list_model->field('id,pid,name')->where("status <> '-1'")->select();
    }

    public function update_role($post) {

        $status = $name = $pid = $index_id = $sort = $desc = $is_super = $id = $auth_list = null;

        extract($post);

        //如果全部正确,执行写入数据
        $data = array(
            "id" => !empty($id) ? $id : "",
            "status" => $status,
            "name" => $name,
            "pid" => $pid,
            "sort" => $sort,
            "desc" => $desc,
            "is_super" => $is_super,
            "auth_list" => $auth_list,
            'index_id' => $index_id,
            "update_time" => time(),
        );

        if(empty($id)) {
            $data['create_time'] = time();
        }

        if (!empty($id) && is_numeric($id)) {
            $r = $this->admin_role_list_model->where(array("id" => $id))->save($data);
            return $r !== false;
        } else {
            return $this->admin_role_list_model->add($data);
        }
    }

    //删除角色
    public function del_role($id)
    {
        return $this->admin_role_list_model->where(array("id" => $id))->save(array("status" => "-1"));
    }







}