<?php
namespace app\mobile\controller;
use app\mobile\model\ShopCart;
use \think\Controller;

//购物车类
class Cart extends Base{


    /**
     * Ajax增加购物车数量
     * @return mixed
     */
    public function ajax_add_cart() {
        $this->ajax_request();
        $shop_cart = new ShopCart();
        $result = $shop_cart->ajax_add_cart(I('post.nper_id'),I('post.join_type'));
        die_json($result);
    }



    /**
     * 获取购物车列表
     * @return array
     */
    public function cart_list() {
        $this->is_login();
        $shop_cart = new ShopCart();
        $result = $shop_cart->cart_list();
        $this->assign('cart_list',$result['cart_list']);
        $this->assign('cart_price',$result['cart_price']);
        $this->assign('cart_num',$result['cart_num']);

        //用于显示下方被选中
        $this->assign('cart_select',true);
        return $this->fetch();

    }

    /**
     * 获取购物车列表
     * @return array
     */
    public function modify_cart_num() {
        $this->ajax_request();
        $this->ajax_is_login();
        $shop_cart = new ShopCart();

        $num = I('post.num','1');
        (empty($num)|| intval($num)<=0)&&$num=1;

        $res = $shop_cart->modify_cart_num(I('post.cart_id'),$num);
        die_json($res);
    }


    /**
     * 删除购物车
     * @return array
     */
    public function delete_cart() {
        $this->ajax_request();
        $this->ajax_is_login();
        $shop_cart = new ShopCart();
        $result = $shop_cart->delete_cart(I('post.cart_id'));
        die_json($result);
    }




}