<?php
namespace app\admin\model;

Class BaseModel
{

    //获取配置信息
    public function get_conf($name) {
        $m_conf =  M('conf','sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }

    /**
     * 得到单个图片地址
     * @param $image_id
     * @return string
     */
    protected function get_one_image_src($image_id) {

        //$website_url = $this->get_conf('WEBSITE_URL');

        if(empty($image_id)) {
//            return $website_url.'/data/img/noPicture.jpg';
            return '/data/img/noPicture.jpg';
        }

        $ImageList = M('image_list');
        $path_info = $ImageList->field('img_path')->where(array("id"=>$image_id))->find();


        $path = '';

//        $path = $path_info['img_path'] ? $website_url.$path_info['img_path'] : $website_url.'/data/img/noPicture.jpg';

        $path = $path_info['img_path'] ? $path_info['img_path'] : '/data/img/noPicture.jpg';

        return $path;
    }


    /**得到多个图片地址
     * @param $image_id_str
     * @return array|string
     */
    protected function get_more_image_src($image_id_str) {
        $ImageList = M('image_list');

        $website_url = $this->get_conf('WEBSITE_URL');

        $map['id']  = ['in',$image_id_str];
        $path_info = $ImageList->field('img_path')->where($map)->select();
        if($path_info) {
            $result_path = array();
            foreach($path_info as $key=>$value) {

                $result_path[] = $value['img_path'] ? $website_url.$value['img_path'] : $website_url.'/data/img/noPicture.jpg';

            }
            return $result_path;
        }
        return '';
    }

}