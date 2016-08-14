<?php
namespace app\admin\model;
Class RoleListModel
{
    //获取列表
    public function get_list($post)
    {
        $m = M('role_list', 'admin_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS p.*,m.title m_title
        FROM  sp_role_list p
        LEFT JOIN sp_menu m ON p.index_id = m.id
        WHERE  p.status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //获取全部列表
    public function get_list_all()
    {
        $m = M('role_list', 'admin_');
        return $m->where(array("status"=>"1"))->select();
    }

    //根据id获取
    public function get_info_by_id($id)
    {
        $m = M('role_list', 'admin_');
        return $m->where(array("id" => $id))->where("status<>-1")->find();
    }

    //根据是否存在id,执行增加或更新
    public function update($post)
    {
        $id = $status = $pid = $name = $sort = $index_id = $auth_list = $desc = null;
        $m = M('role_list', 'admin_');
        extract($post);
        $data = array(
            "id" => $id,
            "status" => $status,
            "pid" => $pid,
            "name" => $name,
            "sort" => $sort,
            "index_id" => $index_id,
            "auth_list" => $auth_list,
            "desc" => $desc,
            "create_time" => NOW_TIME
        );

        if (!empty($id) && is_numeric($id)) {
            $data['update_time'] = NOW_TIME;
            $info = $m->where(array("id" => $id))->save($data);
            return $info !== false;
        } else {
            $data['create_time'] = NOW_TIME;
            return $m->add($data);
        }
    }


    //根据id删除
    public function del($id)
    {
        $m = M('role_list', 'admin_');
        return $m->where(array("id" => $id))->save(array("status" => "-1"));
    }

    //根据ids获取详情
    public function get_role_list_by_ids($ids){
        if(empty($ids))return false;
        $m = M('role_list', 'admin_');
        return $m->where(array("id"=>array("in",$ids)))->select();
    }
}