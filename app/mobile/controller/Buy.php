<?php
namespace app\mobile\controller;

use app\core\model\CategoryModel;
use app\mobile\model\OrderList;
use app\mobile\model\ShowOrder;
use \think\Controller;



//控制器基类
class Buy extends AccountBase
{
    /**
     * 个人夺宝记录
     * @return mixed
     */
    public function person_indiana()
    {
        return $this->fetch();
    }

    /**
     * ajax个人夺宝记录
     * @return mixed
     */
    public function ajax_person_indiana()
    {
        $type = I("post.cate_type");
        $page = I('post.page');
        $order_list = new OrderList();
        $result = $order_list->person_indiana($type,$page);

        if(!$result){
            return false;
        }


        if($type=="ing"){
            $this->assign('indiana_array_ing', $result);
            return $this->fetch('ing_person_indiana');
        } else if( $type == 'doing' ) {
            $this->assign('indiana_array_doing', $result);
            return $this->fetch('doing_person_indiana');

        }else{
            $this->assign('indiana_array_done', $result);
            return $this->fetch('done_person_indiana');
        }
    }



    /**
     *  我的晒单详情
     * @return mixed
     */
    public function my_share_detail()
    {
        $share_id = (int)I('get.share_id');
        if (empty($share_id)) {
            $this->redirect('Index/index');
        }
        $share_order = new ShowOrder();
        $my_share_detail = $share_order->get_my_share_detail($share_id);
        if (empty($my_share_detail)) {
            $this->redirect('Index/index');
        }
        $this->assign('my_share_detail', $my_share_detail);
        $this->assign('imgList', $my_share_detail ['imgList']);
        return $this->fetch('share/my_share_detail');
    }




    /**
     * 我的晒单列表（个人中心，需要登录）
     * @return mixed|void
     */
    public function my_share_list()
    {
        $share_order = new ShowOrder();
        $my_share_list = $share_order->my_share_list();
        $this->assign('my_share_list', $my_share_list);
        return $this->fetch('share/my_share_list');
    }

    /**
     * 提交晒单页面
     * @return mixed|void
     */
    public function submit_share_order()
    {
        $share_id = I('get.share_id');
        if (empty($share_id)) {
            $this->redirect('Index/index');
        }
        //得到我要晒单下的商品信息
        $share_order = new ShowOrder();
        $share_goods = $share_order->submit_share_goods($share_id);
        $this->assign('share_goods', $share_goods);
        return $this->fetch('share/submit_share_order');
    }

    /**
     * 晒单提交
     */
    public function submit_share_form()
    {
        if (IS_AJAX) {
            $ShowOrder = new ShowOrder();
            $res = $ShowOrder->submit_share_form(I('post.share_id'), I('post.title'), I('post.content'), I('pic_list'));
            die_json($res);
        }

    }

    /**
     * 个人中奖纪录
     * @return mixed|void
     */
    public function personal_win_record()
    {
        $order_list = new OrderList();
        $result = $order_list->personal_win_record();
        $this->assign('orderList', $result);
        return $this->fetch('users/personal_win_record');
    }

    public function ajax_personal_win_record() {
        $page = I('post.page');
        $page = isset($page)?$page:0;
        $order_list = new OrderList();
        $result = $order_list->personal_win_record($page);
        $this->assign('orderList', $result);
        return $this->fetch('users/ajax_personal_win_record');

    }

    /**
     * 获奖之后的奖品信息确认,点击进去查看具体流程
     * @return array
     */
    public function prize_info_confirm()
    {
        $win_record_id = (int)I('get.win_record_id');
        if (empty($win_record_id)) {
            return $this->fetch('index/index');
        }
        $order_list = new OrderList();
        $result = $order_list->prize_info_confirm($win_record_id);
        if (empty($result)) {
            return $this->fetch('index/index');
        }
       

        $this->assign('win_status', $result);
        return $this->fetch('users/prize_info_confirm');
    }

    /**
     * 确认收货地址
     * @return mixed|void
     */
    public function confirm_send_address()
    {
        if (IS_AJAX) {
            $order_list = new OrderList();
            $result = $order_list->confirm_send_address(I('post.address_id'), I('post.win_record_id'));
            die_json($result);
        }
    }

    /**
     * 确认收货
     * @return mixed|void
     */
    public function confirm_receipt()
    {
        if (IS_AJAX) {
            $order_list = new OrderList();
            $result = $order_list->confirm_receipt(I('post.win_record_id'));
            die_json($result);
        }

    }


}
