<?php
namespace app\admin\model;

Class AppIndexModel extends BaseModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('app_index',null);
    }

    //获取活动列表页
    public function get_app_guide($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_app_index
        WHERE  1 " . $post->wheresql .
            " ORDER BY 'order' desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //获取点击的活动详情
    function get_guide_data($id){
        $res = $this->m->find($id);
        return $res;
    }

    //执行更新操作
    public function update2($post)
    {
        $data=array(
            "plat"   => $post['plat'],
            "type"   => $post['guide_type'],
            "img_path"   => $post['img_path'],
            "order"    => $post['order'],
            "url"   => $post['url']
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
        return $this->m->where(array("id" => $id))->delete();
    }


}