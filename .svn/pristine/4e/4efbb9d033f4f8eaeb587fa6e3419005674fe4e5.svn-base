<?php
namespace app\yyg\controller;
use app\core\controller\Common;
use app\core\model\ArticleModel;
use app\core\model\UserModel;
/**
 * 帮助
 *
 *
 * @author(s)	xiaoyu <9@simple.moe>;
 * @version		1.0
 * @since 		2016年4月19日 15:49:03
 */

class Help extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * index
     * 用户信息首页/夺宝记录
     *
     *
     * @author(s)	xiaoyu <9#simple.moe>;
     * @since 		2016年4月19日 10:06:10
     */
    public function index(){

    }

    public function read($id="",$name="")
    {
        $conf = M("conf")->select();
        $conf = array_column($conf,'value','name');

        $m_article = new ArticleModel();
        if(empty($name)){
            $article_detail = $m_article->get_article_detail_by_id($id,'content_mob',true);
        }else{
            $article_detail = $m_article->get_article_detail_by_name($name,'content_mob',true);
        }
        if(empty($article_detail)){
            $this->error('您要查看的文章找不到了');
        }
        $mid = M('category_list')->field('id')->where("code='system_notice'")->find();
        $gonggao_detail = M('article')->order('update_time desc')->where(array("category" => $mid['id']))->find();

        $this->assign('gonggao_detail',$gonggao_detail);
        $this->assign('article_detail',$article_detail);
        $this->assign('conf',$conf);
        return $this->display();
    }
}