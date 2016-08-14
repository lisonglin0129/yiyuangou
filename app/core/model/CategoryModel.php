<?php
namespace app\core\model;

Class CategoryModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //根据分类id返回分类名称
    public function get_category_name_by_id($id){
        $m_cat = M('category_list','sp_');
        $info = $m_cat->where(array("id"=>$id))->field(array('name'))->find();
        return $info['name'];
    }

    //获取分类列表
    public function get_category_list_by_mid($mid,$fields=true){
        $m_category = M('category_list','sp_');
        $result = $m_category->field($fields)->where(array('mid'=>$mid,'status'=>1))->order('orders')->select();
        return $result;
    }
    //获取分类列表
    public function get_category_list_by_name($mid,$fields='id,code,name,desc,code,imgid'){
        $m_category = M('category_list','sp_');
        $result = $m_category->field($fields)->where(array('mid'=>$mid,'status'=>1))->order('orders')->select();
        return $result;
    }
    public function get_category_info_by_code($code)
    {
        $m_category = M('category','sp_');
        return $m_category->where(array("code" => $code))->find();
    }
    //根据父级id获取列表
    public function get_list_by_pid($pid)
    {
        $m_category = M('category_list','sp_');
        return $m_category->where(array("status" => "1", "pid" => $pid))->select();
    }

    //根据code获取分类一级列表
    public function get_category_by_code($code,$fields=true)
    {
        //获取父级id
        $m_category = M("category", "sp_");
        $m_category_list = M("category_list", "sp_");
        $p_info = $m_category->where(array("code" => $code))->field("id")->find();

        //获取该父级id下的一级列表
        return $m_category_list->field($fields)->where('status <> -1 AND mid = '. $p_info['id'])->order('id desc,`orders` desc')->select();
    }

    public function get_category_list_info_by_code($code)
    {
        $m_category = M('category_list','sp_');
        return $m_category->where(array("code" => $code))->find();
    }

    function get_articl_by_cate_code($code) {
        $m_category = M('article    ','sp_');
        $articles = $m_category->alias('a')
            ->field('a.id,a.title,a.outlink,a.name,is_hide')
            ->join('sp_category_list c','c.id=a.category')
            ->where(array('c.code'=>$code,'a.state'=>1))
            ->select();
        return $articles;
    }

    //获取物流信息
    public function get_logistics() {
        $pid =  M('category')->where(array('code' =>'logistics' ))->field('id')->find()['id'];
        $cates = M('category_list')->where(array('mid'=>$pid))->field('code,name')->select();
        $cates = $this->format_cates($cates);
        return $cates;
    }
    private function format_cates($cates) {
        $arr = array();
        if ( empty($cates) ) {
            return $arr;
        }

        foreach ( $cates as $cate ) {
            $arr[$cate['code']] = $cate['name'];
        }

        return $arr;

    }
}