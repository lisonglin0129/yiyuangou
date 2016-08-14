<?php
namespace app\mobile\model;
use app\core\model\CategoryModel;
use think\Model;
//夺宝客服相关页面
class Article extends Model {

    private $m;
    public function __construct()
    {
        parent::__construct();
        $this->m = M('article');
    }

    public function show_home_page(){
        $Article=M('article');
        $m_cate = new CategoryModel();
        return $m_cate->get_articl_by_cate_code('novice_directory');

    }

    public function get_category($id){
        $Category_list=M('category_list');
        $result=$Category_list->field('name,id')->where(array("pid"=>$id))->select();
        return $result;

    }

    public function get_title($id){
        $Category_list=M('category_list');
        $result=$Category_list->alias('c')->join('article a','c.id=a.category')->field('a.title,a.category,a.id')->where(array("pid"=>$id))->select();
        return $result;
    }

    public function show_article($id){
        $result=$this->m->field('title,content_mob')->where(array('id'=>$id))->find();
        return $result;
    }

    public function get_content($name) {
        $data = $this->m->where(array('name'=>$name,'state'=>1))->find();
        return $data['content_mob'];
    }








}