<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/6/13
 * Time: 15:44
 */
namespace app\admin\model;
class WechatMessageModel{
    private $m;
    public function __construct(){
        $this->m=M('wechat_message');
    }
    public function show_list($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  m.*,i.img_path as img_face
        FROM  sp_wechat_message m
        LEFT JOIN sp_image_list i ON i.id=m.img_url
        WHERE  m.status != -1  " . $post->wheresql .
            " ORDER BY m.id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    public function get_m_info_by_id($id){
        return $this->m->alias('m')->field('m.*,i.img_path as img_face')->join('sp_image_list i on i.id=m.img_url','left')->where(array('m.id'=>intval($id)))->find();
    }
    //获取图文消息
    public function get_img_messages(){
        return $this->m->where('status <> -1 AND type=2')->select();
    }
    public function get_excepted_img_messages($post,$id){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_wechat_message m
        WHERE  status != -1  AND id !={$id} AND type=2" . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //文本消息更新
    public function update_text($post){
        $id=$keyword=$content=$type=null;
        extract($post);
        $data=[
            'id'=>!empty($id)?intval($id):'',
            'keyword'=>$keyword,
            'content'=>$content,
            'type'=>$type,
            'create_time'=>time()
        ];
        if(empty($id)){
            $res=$this->m->add($data);
        }else{
            $res=$this->m->save($data);
        }
        return $res;
    }
    //图文消息保存
    public function update_img($post){
        $id=$keyword=$content=$type=$title=$img_url=$link=null;
        extract($post);
        $data=[
            'id'=>!empty($id)?intval($id):'',
            'keyword'=>$keyword,
            'content'=>$content,
            'type'=>$type,
            'title'=>$title,
            'img_url'=>!empty($img_url)?$img_url:'',
            'create_time'=>time(),
            'link'=>$link
        ];
        if(empty($id)){
            $res=$this->m->add($data);
        }else{
            $res=$this->m->save($data);
        }
        return $res;
    }
    //多图文保存
    public function update_imgs($post){
        $id=$keyword=$content=$type=$index_mid=null;
        extract($post);
        $data=[
            'id'=>!empty($id)?intval($id):'',
            'keyword'=>$keyword,
            'content'=>$content,
            'type'=>$type,
            'index_mid'=>!empty($index_mid)?$index_mid:'',
            'create_time'=>time()
        ];
        if(empty($id)){
            $res=$this->m->add($data);
        }else{
            $res=$this->m->save($data);
        }
        return $res;
    }
    //删除
    public function update_status($id){
        return $this->m->where(['id'=>intval($id)])->save(['status'=>-1]);
    }
    //获取多图文内容
    public function get_content($mid){
        return $this->m->where(['id'=>$mid])->getField('content');
    }
    public function key_valid($key){
        return $this->m->where(['keyword'=>$key,'status'=>1])->count();
    }

}