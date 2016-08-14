<?php
namespace app\mobile\controller;

use app\core\controller\Common;
use app\lib\beecloud\rest\api;
use \think\Controller;
use \app\mobile\model\Article as ArticleModel;
use app\core\model\CommonModel;

//网站文章
class Article extends Controller
{
    /**
     *常见问题
     * @return mixed
     */


    public function common_problem()
    {
        $code='common_problem';
        $m = new ArticleModel();
        $content = $m->get_content($code);
        empty($content)&&$content='请根据此识别码："common_problem"去后台配置常见问题文章';
        $c_model = new CommonModel();
        $money_name = $c_model->get_conf('MONEY_NAME');
        $money_count = $c_model->get_conf('MONEY_UNIT');
        $this->assign('money_name', $money_name);
        $this->assign('money_count', $money_count);
        $this->assign('content', $content);
        return $this->fetch();
    }

    /**
     * 服务条款
     * @return mixed
     */
    public function service_provision()
    {
        $code='service_provision';
        $m = new ArticleModel();
        $content = $m->get_content($code);
        empty($content)&&$content='请根据此识别码：service_provision去后台配置服务条款文章';
        $this->assign('content', $content);
        return $this->fetch();
    }

    /**
     *  用户协议
     * @return mixed
     */
    public function user_agreement()
    {
        $code='user_agreement';
        $m = new ArticleModel();
        $content = $m->get_content($code);
        empty($content)&&$content='请根据此识别码：user_agreement去后台配置用户协议文章';
        $this->assign('content', $content);
        return $this->fetch();
    }

    /**
     * 什么是一元多宝
     * @return mixed
     */
    public function help()
    {
        //移动端也要使用该页面
        $code='help';
        $m = new ArticleModel();
        $content = $m->get_content($code);
        empty($content)&&$content='请根据此识别码：help去后台配置帮助页面文章';
        $this->assign('content', $content);
        return $this->fetch();
    }

    /**
     * 隐私保护（只用于IOS）
     * @return mixed
     */
    public function protect_private()
    {
        $code='protect_private';
        $m = new ArticleModel();
        $content = $m->get_content($code);
        empty($content)&&$content='请根据此识别码：protect_private去后台配置隐私保护文章';
        $this->assign('content', $content);
        return $this->fetch();
    }

    /**
     * 客服主页
     * @return mixed
     */
    public function home_page()
    {
        $Article = new ArticleModel();

        $result = $Article->show_home_page();
        $this->assign('article', $result);
        return $this->fetch();
    }

    /**
     * 问题查询页面
     * @return mixed
     */
    public function category()
    {
        $Article = new ArticleModel();
        $id = I('get.id');

        $category = $Article->get_category($id);
        $title = $Article->get_title($id);
        $this->assign('category', $category);
        $this->assign('title', $title);

        return $this->fetch();
    }


    /**
     * 问题解答详情页面
     * @return mixed
     */
    public function article()
    {
        $Article = new ArticleModel();
        $id = I('get.id');
        $result = $Article->show_article($id);
        $this->assign('article', $result);
        return $this->fetch();
    }

    //微信图文消息详情
    public function msg_info()
    {
        $m = M('wechat_message')->where(['id' => I('get.id')])->find();
        $this->assign('m', $m);
        return $this->fetch();
    }
    //根据文章code显示文章

    public function show_arc(){
       $code=I('get.code');
        empty($code)&&wrong_return('参数错误');
        $content=M('article')->where(['name'=>$code])->getField('content_mob');
        empty($content)&&$content='请根据此识别码：'.$code.'去后台配置文章';
        $this->assign('content', $content);
        return $this->fetch();
    }

}




