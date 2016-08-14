<?php
namespace app\admin\model;

Class AppConfModel extends BaseModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('app_conf',null);
    }

    //更新 添加
    public function update($post)
    {

        if($post['type'] == "edit"){
            unset($post['type']);
            $res = $this->m->where(array("id"=>$post['id']))->save($post);
            return $res!==false;
        }else if($post['type'] == "add"){
            unset($post['type']);

            //检查是否有相同的包名字
            $result = $this->m->where(array("name"=>$post['name']))->select();
            if($result){
                return false;
            }

            $res = $this->m->add($post);
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