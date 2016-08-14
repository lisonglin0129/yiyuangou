<?php
namespace app\admin\model;


Class AppManageModel
{
    private $m;

    function __construct()
    {
        $this->m= M("appversion", "sp_");
    }
    public function get_version_list(){
        $data=array();
        $version_list=$this->m->where("status <> -1")->select();
        $sql="SELECT FOUND_ROWS() as num";
        $count=$this->m->query($sql);
        $data['data']=$version_list;
        $data['count']=$count[0]['num'];
        return $data;
    }

    function get_one_data($id){
        $res = $this->m->find($id);
        return $res;
    }

    public function get_max_code() {
       return $this->m->field('MAX(code) max_code')->where(array('status'=>1))->select()[0]['max_code'];
    }

    //执行更新操作
    public function update($post)
    {
        $data=array(
            "status"      => 1,
            "plantform"   => $post['plantform'],
            "version"     => $post['version'],
            "code"        => $post['code'],
            "desc"        => $post['desc'],
            'package_name'=> $post['package_name'],
            "url"         => $post['url']
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

    public function del($id)
    {
         return $this->m->where(array("id" => $id))->save(array("status" => "-1"));
    }

}