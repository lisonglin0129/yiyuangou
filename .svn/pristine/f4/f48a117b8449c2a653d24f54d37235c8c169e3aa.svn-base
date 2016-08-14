<?php
namespace app\mobile\controller;
use app\core\model\CategoryModel;
use app\mobile\model\Goods;
use app\mobile\model\ShopCart;
use app\mobile\model\ShowOrder;
use app\core\model\MiscModel;
use \think\Controller;


class Index extends Base{


    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $spread_userid=I('get.spread_userid');
        $spread_userid&&session('spread_userid',$spread_userid);
        $goods = new Goods();
        $shop_cart = new ShopCart();
        //首页轮播图片
        //轮播图
        $m_misc = new MiscModel();
        $home_promo_list = $m_misc->get_home_promo(2);

        //最新揭晓
        $latest_announcement = $goods->latest_announcement();
        //上架新品
        $new_goods = $goods->get_index_new_goods();
        //今日热门商品
        $hot_goods = $goods->get_index_hot_goods();
        //购物车数量
        $cart_num = $shop_cart->get_cart_num();
        //查询用户是否中奖
        $win_notice = $goods->win_notice();
        //底部下载地址
        $down_url = $this->get_conf("ANDROID_DOWNLOAD_URL");

        //分别赋值于模板
        $this->assign('down',$down_url);
        $this->assign('home_promo_list',$home_promo_list);
        $this->assign('new_goods',$new_goods);
        $this->assign('hot_goods',$hot_goods);
        $this->assign('latest_announcement',$latest_announcement);
        $this->assign('cart_num',$cart_num);
        $this->assign('win_notice',$win_notice);
        //红色下划线
        $this->assign('index',true);
        return $this->fetch();
    }


    /**
     * 全部商品
     * @return mixed
     */
    public function all_goods() {

        $Goods = new Goods();
        $shop_cart = new ShopCart();

        $url_info = explode('-',I('get.cate'));

        //如果为空，默认为全部商品下按人气排名
        if(empty($url_info[0]) && empty($url_info[1])) {
            $url_info = array(0,1);
        }

        $cate = (int)$url_info[0];
        $type = (int)$url_info[1];

        //获取分类的id
        $m_cat = new CategoryModel();
        $cate_info = $m_cat->get_category_info_by_code('xiangchang');
        //查询商品所有种类
        $cate_list = $m_cat->get_category_list_by_mid($cate_info['id']);

        //根据种类和排序类型查询商品
        $all_goods =$Goods->all_goods($cate,$type,$offset = 0,$cate_info['id']);


        //购物车数量
        $cart_num = $shop_cart->get_cart_num();

        //总需人次4与5相互切换
        $need_type_array = array(4=>5,5=>4);
        if(in_array($type,$need_type_array)) {
            $this->assign('need_type',$need_type_array[$type]);
        }
        //底部下载地址
        $down_url = $this->get_conf("ANDROID_DOWNLOAD_URL");
        //分别赋值于模板
        $this->assign('down',$down_url);
        //全部商品
        $this->assign('all_goods',$all_goods['goods_info']);
        $this->assign('cart_num',$cart_num);
        //排序类型
        $this->assign('type',$all_goods['search_type']);
        //种类列表
        $this->assign('cate_list',$cate_list);
        //当前访问的商品种类
        $this->assign('cate',$cate);
        //红色下划线
        $this->assign('all_goods_cate',true);
        return $this->fetch();
    }

    /**
     * ajax查询商品数量
     */
    public function ajax_goods_count() {

        $this->ajax_request();

        $Goods = new Goods();

        $url_info = explode('-',I('get.cate'));
        //如果为空，默认为全部商品下按人气排名
        if(empty($url_info[0]) && empty($url_info[1])) {
            $url_info = array(0,1);
        }
        $cate = (int)$url_info[0];
        $type = (int)$url_info[1];
        //根据种类和排序类型查询商品
        $count =$Goods->all_goods_count($cate,$type);
        echo ceil($count / 10);

    }

    /**
     * ajax加载商品
     */
    public function ajax_all_goods() {
        $this->ajax_request();
        $Goods = new Goods();

        $url_info = explode('-',I('post.cate_type'));
        $offset = I('post.offset');
        //如果为空，默认为全部商品下按人气排名
        if(empty($url_info[0]) && empty($url_info[1])) {
            $url_info = array(0,1);
        }
        $cate = (int)$url_info[0];
        $type = (int)$url_info[1];
        //根据种类和排序类型查询商品
        $all_goods =$Goods->all_goods($cate,$type,$offset);
        $this->assign('all_goods',$all_goods['goods_info']);
        return $this->fetch();
    }


    /**
     * 首页全部晒单
     * @return mixed
     */
    public function all_share_order() {

        $share_order = new ShowOrder();
        $shop_cart = new ShopCart();

        $share_list = $share_order->all_share_order($offset = 0);
        //购物车数量
        $cart_num = $shop_cart->get_cart_num();
        $this->assign('cart_num',$cart_num);
        $this->assign('share_list',$share_list);
        //红色下划线
        $this->assign('shareOrder',true);
        return $this->fetch();
    }


    /**
     * ajax查询晒单数量
     * @return mixed
     */
    public function all_share_count() {
        $this->ajax_request();
        $share_order = M('show_order');
        $share_count= $share_order->field('id')->where('status = 1 AND complete = 1 AND create_time <'.time())->count();
        echo ceil($share_count / 10);
    }


    /**
     * ajax加载晒单
     * @return mixed
     */
    public function ajax_share_goods(){
        $this->ajax_request();
        $share_order = new ShowOrder();
        $offset = I('post.offset');
        $share_list = $share_order->all_share_order($offset);
        $this->assign('share_list',$share_list);
        return $this->fetch();

    }

}
