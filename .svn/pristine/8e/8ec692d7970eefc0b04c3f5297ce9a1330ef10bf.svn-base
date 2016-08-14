<?php
/**
 * Created by PhpStorm.
 * User: bb3201
 * Date: 2016/5/17
 * Time: 11:49
 */
namespace app\admin\model;

class PromotModel {
    private $m;
    public function __construct()
    {
        $this->m = M('promo','sp_');
    }

    public function get_list($post){
        $m = M('sp_promo', 'sp_');
        $sql = 'SELECT p.*,c.name category_name,g.name good_name,i.img_path
          FROM sp_promo p
          LEFT JOIN sp_category_list c ON p.category=c.id
          LEFT JOIN sp_image_list i ON p.img=i.id
          LEFT JOIN sp_goods g ON p.gid=g.id WHERE  1=1 '.$post->wheresql;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $num = $this->m->query($sql_count);

        $info = $m->query($sql);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //执行更新操作
    public function update($post)
    {
        $data=array(
            "type"   => $post['type'],
            "gid"   => $post['gid'],
            "category"   => $post['category'],
            "img"    => $post['img'],
            "url"    => $post['url'],
            "mid"    => $post['mid'],
        );
        if(!empty($post['id'])){
            $res = $this->m->where(array("id" => $post['id']))->save($data);
            return $res!==false;
        }else {
            $res = $this->m->add($data);
        }
        return $res;
    }

    public function get_info_by_id($id)
    {
        return $this->m->where(array('id' => $id))->find();
    }

    //获取图片列表
    public function get_img_list($id)
    {
        if (is_null($id)) {
            return '';
        }
        $m_img = M("image_list", "sp_");
        return $m_img->where(array("status" =>1, "id"=>$id ))->find();
    }

    /**
     * 获取商品分类
     * @param string $type
     * @return mixed
     */
        public function get_cate_list()
    {
        $m = M('category_list', 'sp_');
        $cates = $m->alias('cl')
            ->field('cl.id,cl.name')
            ->join('category c','c.id=cl.mid')
            ->where(array("c.code" => 'xiangchang'))->select();

        return $cates;

    }

    //删除商品推荐
    public function del($id)
    {
        return $this->m->where(array("id" => $id))->delete();
    }
}