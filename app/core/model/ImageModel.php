<?php
namespace app\core\model;

Class ImageModel
{
    private $m_img;

    public function __construct()
    {
        $this->m_img = M('image_list', 'sp_');
    }

    //根据id,或以逗号分割的id获取图片列表地址
    public function get_img_list_by_id($id)
    {
        $arr = str_to_arr($id);
        $ids = '';
        //检测分割id中是否有不是数字的剔除掉
        foreach ($arr as $k => $v) {
            if (is_numeric($v)) {
                $ids = $ids . $v . ',';
            }
        }
        $ids = trim($ids, ',');
        if (empty($ids)) return false;
        return $this->m_img->where('status = 1 AND id IN(' . $ids . ')')->field(array('img_path', 'thumb_path', 'img_path'))->select();
    }
    //根据id获取图片列表; 返回以图片id为索引的数组
    public function get_img_map_by_ids($ids,$field='id,img_path'){
        if(is_string($ids)){
            //剔除两侧分割分隔符','
            $ids = trim($ids,',');
            $ids = explode(',',$ids);
        }
        if(empty($ids)) return false;
        if(is_array($ids)){
            array_unique($ids);
            $query_result = $this->m_img->where(['status'=>1,'id'=>['in',$ids]])->field($field)->select();
            if(empty($query_result)){
                return false;
            }else{
                $result=[];
                foreach($query_result as $each_img){
                    $result[$each_img['id']]=$each_img;
                }
                return $result;
            }
        }else{
            return false;
        }
    }
    //填充图片id
    //TODO:
//    public function fill_images(){
//        $objs = func_get_args();
//        $imgid = [];
//        if(empty($objs) || !is_array($objs)) return $objs;
//        foreach($objs){
//            if(is_array($objs) && key_exists('img_id')){
//
//            }else if(is_array($objs) && key_exists('pic_list')){
//
//            }
//
//        }
//    }
}