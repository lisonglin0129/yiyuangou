<?php
namespace app\core\model;
/**
 * Created by PhpStorm.
 * User: phil
 * Date: 2016/4/13
 * Time: 16:42
 */

Class UploadModel
{
    private $img_up;

    public function __construct()
    {
        $this->img_up = M('image_list', "sp_");
    }


    public function save_img_info($post)
    {
        extract($post);
        $data = array(
            "name" => empty($name) ? "" : $name,
            "uid" => get_user_id(),
            "type" => empty($type) ? "" : $type,
            "status" => "1",
            "img_path" => empty($img_path) ? "" : $img_path,
            "thumb_path" => empty($thumb_path) ? "" : $thumb_path,
            "width" => empty($width) ? "" : $width,
            "height" => empty($height) ? "" : $height,
            "ext" => empty($ext) ? "" : $ext,
            "hash" => empty($hash) ? "" : $hash,
            "sha1" => empty($sha1) ? "" : $sha1,
            "update_time" => NOW_TIME,
            "create_time" => NOW_TIME
        );

        return $this->img_up->add($data);
    }


    public function get_img_info_by_hash($hash)
    {
        $info = $this->img_up->where(array("hash" => $hash))->find();
        !empty($info) && $info["status"] = "1";
        return $info;
    }

    public function get_img_path_by_id($id, $field = 'img_path')
    {
        if (is_array($id)) {
            return $this->img_up->where(array('id' => array('in', $id)))->field($field)->select();
        } else {
            return $this->img_up->where(array('id' => $id))->getField($field);
        }

    }

}