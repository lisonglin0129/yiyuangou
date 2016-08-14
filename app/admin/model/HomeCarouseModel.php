<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/17
 * Time: 9:12
 */
namespace app\admin\model;

use app\core\controller\Gdfc;
use app\admin\model\BaseModel;
class HomeCarouseModel extends BaseModel{
    private $m;
    public function __construct(){
        $this->m=M('home_carouse','sp_');
    }
    //获取商品推荐列表
    public function get_recommend_list($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  g.*,i.img_path
        FROM  sp_home_carouse g
        LEFT JOIN sp_image_list i ON i.id = g.image_id
        WHERE  g.flag <> '-1' " . $post->wheresql .
            " ORDER BY g.id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    public function update_recommend($post){
        $id=$image_id=$content=$platform=$title=$sort=$type=$flag=$mid=null;
        extract($post);
        $data=array(
            'id'=>!empty($id) ? $id : "",
            'image_id'=>$image_id,
            'content'=>$content,
            'title'=>$title,
            'sort'=>$sort,
            'type'=>$type,
            'flag'=>intval($flag),
            'mid'=>$mid,
            'platform'=>$platform,
        );
        if($id){
            return $this->m->save($data);
        }else{
            return $this->m->add($data);
        }
    }
    public function del_rec($id){
        return $this->m->where(['id'=>$id])->delete();
    }
    public function get_recommend($id){
        if(empty($id)){
            die('出错');
        }
        return $this->m->alias('h')->field('h.*,i.img_path')->join('sp_image_list i on h.image_id=i.id')->where(['h.id'=>intval($id)])->find();
    }
}