<?php
namespace app\his\model;
Class LogModel extends CommonModel
{
    //获取全部列表
    public function get_log_behavior_list($post)
    {
        $m = M('','sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
        FROM  log_behavior
        WHERE  status =1 " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //根据id获取
    public function get_behavior_info_by_id($id)
    {
        $m = M('behavior','log_');
        return $m->where(array("id" => $id,"status"=>1))->find();
    }

    //根据是否存在id,执行增加或更新
    public function update($post)
    {
        $m = M('','sp_');
        $status =null;
        extract($post);
        $data = array(
            "status" => $status,
            "create_time" => NOW_TIME
        );

        if (!empty($id) && is_numeric($id)) {
            $info = $m->where(array("id" => $id))->save($data);
            return $info !== false;
        } else {
            return $m->add($data);
        }
    }


    //根据id删除
    public function del_behavior($id)
    {
        $m = M('behavior','log_');
        return $m->where(array("id" => $id))->save(array("status"=>"-1"));
    }
}