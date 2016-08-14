<?php
namespace app\core\model;
Class ArticleModel extends CommonModel
{
    private $m_article;
    public function __construct()
    {
        parent::__construct();
        $this->m_article = M('article','sp_');
    }
    //根据分类列表获取名字，标题
    public function get_article_list_by_categorys($category){
        if(empty($category)) return false;
        $result = $this->m_article
            ->table('sp_article a')
            ->field('a.id,a.name,a.category,a.title,a.outlink,a.is_hide')
            ->where(['a.category'=>['in',$category],'a.state'=>1])
            ->select();
        return $result;
    }
    /**
     * some_func
     * 函数的含义说明
     *
     * @access		public
     * @param int $id 文章ID
     * @param mixed $fields 获取/排除的字段名
     * @param bool $field_deny 标记获取还是排除
     * @return		mixed
     *
     * @author(s)	xiaoyu <9#simple.moe>;
     * @since 		2016年4月20日 10:38:13
     */
    public function get_article_detail_by_id($id,$fields=true,$field_deny=false){
        return $this->m_article->field($fields,$field_deny)->where(['id'=>$id,'state'=>1])->find();
    }
    public function get_article_detail_by_name($name,$fields=true,$field_deny=false){
        return $this->m_article->field($fields,$field_deny)->where(['name'=>$name,'state'=>1])->find();
    }
}