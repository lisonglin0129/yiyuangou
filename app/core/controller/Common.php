<?php
namespace app\core\controller;


use app\admin\model\SpreadModel;
use app\core\model\AuthModel;
use app\core\model\CategoryModel;
use app\core\model\CommonModel;
use app\core\model\GoodsModel;
use app\core\model\ArticleModel;
use app\core\model\LogBehaviorModel;
use app\core\model\OrderModel;
use app\pay\model\PublicModel;
use think\Controller;

Class Common extends Controller
{
    protected $view;
    protected $category_list;
    protected $help_category;
    protected $m_auth;

    //获取配置信息
    public function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }

    public function __construct()
    {
        parent::__construct();

        $keywords = $this->get_key_words();
        $this->assign(array('keywords' => $keywords));
    
        
        
        //鉴权
        $this->m_auth = new AuthModel();
        $this->m_auth->init();

//        $this->init_category();
        if (!IS_AJAX) {
            //初始化mid分类

            $cate = new \app\admin\model\CategoryModel();
            $friend_link = $cate->get_list_by_code_mid('youqinglianjie');
		
            $m_conf = new CommonModel();
            $this->assign('zero_start', C('ZERO_START') ? C('ZERO_START') : '');
            $conf = $m_conf->get_all_conf();

            $conf = array_column($conf, 'value', 'name');

            $this->assign('friend_link', $friend_link);
            $this->assign('conf', $conf);
//            $this->assign('website_name', $conf['WEBSITE_NAME']);
//            $this->assign('nper_base', $conf['NPER_BASE_NUM']);
            $this->get_cart_info();
            $m_cat = new CategoryModel();
            $info = $m_cat->get_category_info_by_code('xiangchang');
            $this->category_list = $m_cat->get_category_list_by_mid($info["id"]);

            $this->assign('category_list', $this->category_list);
            //推广
            $s_model = new SpreadModel();
            $this->assign('promote_spread', $s_model->get_spread_by_type(2));
            $this->assign('reg_spread', $s_model->get_spread_by_type(1));

            //读取缓存中的配置如果不存在则获取并缓存3600秒
            if (empty($site_config = S('lingjiang_site_config'))) {
                $site_config = $m_conf->get_conf_website();
                S('lingjiang_site_config', $site_config, ['expire' => 3600]);
            }
            $this->assign('site_config', $site_config);


            //获取底部的几个分类
            //新手指南
            $novice_directory = $m_cat->get_articl_by_cate_code('novice_directory');
            //商品配送
            $goods_send = $m_cat->get_articl_by_cate_code('goods_send');
            //夺宝保障
            $indiana_security = $m_cat->get_articl_by_cate_code('indiana_security');
           //友情链接
            $youqinglianjie = $m_cat->get_category_by_code('youqinglianjie');

//            foreach ($help_category as $each_category) {
//                array_push($help_category_id, $each_category['id']);
//            }
//            $m_article = new ArticleModel();
//            $help_list = $m_article->get_article_list_by_categorys($help_category_id);
//                foreach ($help_category as &$each_category) {
//                $articles = [];
//                foreach ($help_list as $each_help) {
//                    if ($each_help['category'] == $each_category['id']) {
//                        array_push($articles, $each_help);
//                    }
//                }
//                $each_category['articles'] = $articles;
//            }
            $this->assign('novice_directory', $novice_directory);
            $down_url = $this->get_conf("ANDROID_DOWNLOAD_URL");
            $this->assign('down_url', $down_url);
            $this->assign('goods_send',$goods_send);
            $this->assign('indiana_security',$indiana_security);
            $this->assign('youqinglianjie',$youqinglianjie);

            //获取用户中奖通知
            if (is_user_login()) {

                //略过中奖详情页
                if (strtolower(CONTROLLER_NAME) == 'ucenter' && strtolower(ACTION_NAME) == 'luck_detail') {
                    $this->assign('notify_list', null);
                } else {
                    $m_order = new OrderModel();
                    list($notify_list, $null) = $m_order->get_lucky_duang_by_uid(get_user_id(), 0);
                    $this->assign('notify_list', $notify_list);
                }
            }
        }
      //添加行为日志
        if(C('PC_LOG_BEHAVIOR')=='1'){
            if(!in_array(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,C('NO_LOG_URL'))){
                $l_model=new LogBehaviorModel();
                $l_model->log_behavior_add();
            }
        }

    }

    private function get_cart_info()
    {
        if (is_user_login()) {
            //获取购物车商品数量
            $m_goods = new GoodsModel();
            $cart_num = $m_goods->get_cart_num();
            is_numeric($cart_num) && $this->assign("cart_num", $cart_num);
        } else {
            $cart_list = cookie('local_cart');
            $cart_num = count($cart_list);
            $this->assign("cart_num", $cart_num);
        }
    }

    public function get_key_words()
    {
        $m_pub = new PublicModel();
        $keywords = $m_pub->get_conf('SET_KEYWORDS');
        $keywords = explode(',', $keywords, 4);
        if (count($keywords) >= 4)
            array_pop($keywords);

        return $keywords;

    }

    /**
     * 定义本套模版使用的是哪个分类的商品
     */
    private function init_category()
    {
        $category = C('category_goods');
        !in_array($category, array('xiangchang', 'yiyuanduobao', 'laoniuduobao', 'shishangyiyuangou', 'yiyuanmaimi', 'juziduobao'))
        && $category = 'xiangchang';
        //获取当前分类的mid
        $mid = M('category', 'sp_')->where(array('code' => $category))->field('id')->find();
        $mid = $mid['id'];
        empty($mid) && die('mid 获取失败');
//        define('__MID__',$mid);
    }

    public function _empty()
    {
        return $this->fetch();
    }

    //标记用户已经点击关闭duang中奖的提示
    public function flag_trigger()
    {
        $id = I("post.id", '');
        $m = new CommonModel();
        $m->flag_trigger($id) && ok_return('成功!');
        wrong_return('失败!');
    }
}