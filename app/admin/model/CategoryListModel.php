<?php
namespace app\admin\model;

Class CategoryListModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('category_list',null);
    }

    //获取活动列表页
    public function get_act_list($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_activity
        WHERE  flag != -1 " . $post->wheresql .
            " ORDER BY sort desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $act_info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $act_info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //获取点击的活动详情
    function get_one_data($id){
        $res = $this->m->where(array("id"=>$id))->select();
        return $res;
    }

    //执行更新操作
    public function update($post)
    {
        $data=array(
            "name"   => $post['name'],
            "desc"   => $post['desc'],
            "link"   => $post['link'],
            "src"    => $post['src'],
            "flag"   => $post['flag'],
            "sort"   => $post['sort']
        );
        if($post['type'] == "edit"){
            $res = $this->m->where(array("id"=>$post['id']))->save($data);
            return $res!==false;
        }else if($post['type'] == "add"){
            $res = $this->m->add($data);
            return $res;
        }else{
            return false;
        }
    }

    //删除操作
    public function del($id)
    {
        return $this->m->where(array("id" => $id))->save(array("flag" => "-1"));
    }


}