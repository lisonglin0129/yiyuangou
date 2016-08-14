<?php
namespace app\admin\model;

Class ArticleModel extends BaseModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('article',null);
    }

    //获取文章列表
    public function get_art_list($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  a.*
        FROM  sp_article a
        WHERE  state != -1  " . $post->wheresql .
            " ORDER BY a.id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $art_info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        //处理时间戳
        foreach($art_info as $key=>$value) {
            $art_info[$key]['create_time'] = $value['create_time'] ? date('Y-m-d H:i:s',$value['create_time']) : '';
            $art_info[$key]['update_time'] = $value['update_time'] ? date('Y-m-d H:i:s',$value['update_time']) : '';

        }

        $rt["data"] = $art_info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //获取当前文章详情页
    function get_one_data($id){
        $res = $this->m->find($id);
        return $res;
    }

    //执行更新操作
    public function update($post)
    {
        $data=array(
            "category" =>trim($post['category_list']),
            "title"   => trim($post['title']),
            "outlink"   => trim($post['outlink']),
            "state"    => trim($post['state']),
            "sort"   => trim($post['sort']),
            "content"   => trim($post['content']),
            "name"   => trim($post['name']),
            "content_mob"   => trim($post['content_mob'])
        );
        if($post['type'] == "edit"){
            $res = $this->m->where(array("id"=>$post['id']))->save($data);
            return $res!==false;
        }else if($post['type'] == "add"){
            $res = $this->m->add($data);
        }else{
            return false;
        }
        return $res;
    }

    /**
     * 获取用户注册协议(固化name='useragreement')
     */
    public function get_agreement() {
        return $this->m->where(array('name'=>'useragreement','state'=>1))->find();

    }

    public function del($id)
    {
        return $this->m->where(array("id" => $id))->save(array("state" => "-1"));
    }
    public function del_select($ids)
    {
        return $this->m->where(array("id" =>['in',$ids]))->save(array("state" => "-1"));
    }



}