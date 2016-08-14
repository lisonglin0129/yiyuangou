<?php
namespace app\yyg\controller;

use app\core\controller\Common;
use app\core\model\GoodsModel;
use app\core\model\ImageModel;
use app\core\model\MiscModel;
use app\core\model\OrderModel;

class Index extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->chk_mobile();
    }

    //判断是否为手机版本,是则跳转到手机版本
    private function chk_mobile()
    {
        //配置等于1的时候开启跳转,默认开启
        $open_jump_mobile = empty(C('JUMP_MOBILE_URL_OPEN'))?'1':C('JUMP_MOBILE_URL_OPEN');
        if(strval($open_jump_mobile)=='-1')return false;

        if (mobile_device_detect()) {
            //获取手机配置地址
            $mobile_path = empty(C('WAP_WEBSITE_URL')) ? U('mobie/index/index') : C('WAP_WEBSITE_URL');
            $this->redirect($mobile_path);
            die();
        }
    }

    /**
     * 首页
     */
    public function index()
    {
        //首页接受推广人手机号码
        $spread_userid = I('get.spread_userid');
        $spread_userid && session('spread_userid', $spread_userid);
        $m_goods = new GoodsModel();
        $m_order = new OrderModel();
        $m_image = new ImageModel();
        //轮播图
        $m_misc = new MiscModel();
        $home_promo_list = $m_misc->get_home_promo(1);
        $this->assign('home_promo_list', $home_promo_list);
        //公告
        $mid = M('category_list')->field('id')->where("code='system_notice'")->find();

        $gonggao_detail = M('article')->order('update_time desc')->where("state=1 AND category = '" . $mid['id'] . "'")->find();

        $this->assign('gonggao_detail', $gonggao_detail);

        //推荐夺宝
        $top_promo = $m_goods->get_promo_goods('new_recommend');
//        $top_promo_one_info = array_shift($top_promo);
//        $top_promo_one = $m_goods->get_last_nper_info_by_gid($top_promo_one_info['gid']);

        //推荐夺宝
        $top_promo_one = array();
        $top_promo_one = $m_goods->get_recomment_indiana();
        $top_promo_one = $m_goods->get_last_nper_info_by_gid($top_promo_one['gid']);
        if (isset($top_promo_one['sum_times'])) {
            $top_promo_one['percent'] = $top_promo_one['sum_times'] > 0 ? floor(((int)$top_promo_one['participant_num']  / (int)$top_promo_one['sum_times']) * 10000) / 100 : '0.00';
        }
        $this->assign('top_promo_one', $top_promo_one);

        $top_promo_new_ids = [];
        foreach ($top_promo as $each_top_promo) {
            array_push($top_promo_new_ids, $each_top_promo['gid']);
        }
        $top_promo_new_list = $m_goods->get_simple_goods_by_gid($top_promo_new_ids);
        $this->assign('top_promo_new_list', $top_promo_new_list);

        //最热商品
        list($hot_goods, $null) = $m_goods->get_list_by_category(0, 8, 1, 0);
        $this->calc_process($hot_goods);
        $this->assign('hot_goods', $hot_goods);

        //即将揭晓
        list($opening_list, $null) = $m_order->get_opening_list(1, 6);

        if ($opening_list) {
            foreach ($opening_list as &$each_opening) {
                $each_opening['open_time'] = intval($each_opening['open_time']) - time();
                $each_opening['open_time'] = $each_opening['open_time'] > 0 ? $each_opening['open_time'] . '000' : '0';
            }
        }
        $this->assign('opening_list', $opening_list);

        //获取首页分类左侧推荐商品
        $category_promo_goods = $m_goods->get_promo_goods('index_left_recommend');
        $category_promo_goods_order = [];
        for ($i = count($category_promo_goods) - 1; $i >= 0; $i--) {
            $category_promo_goods_order[$category_promo_goods[$i]['category']] = $category_promo_goods[$i];
        }
        unset($category_promo_goods);
        $this->assign('category_promo', $category_promo_goods_order);

        //中部分类商品
        $goods_list = [];
        foreach ($this->category_list as $each_category) {
            //每个分类获取几条商品信息
            list($goods, $count) = $m_goods->get_list_by_category($each_category['id'], 4);
            $this->calc_process($goods);
//            foreach($goods as &$each_goods){
//                $each_goods['remain'] = $each_goods['sum_times'] - $each_goods['participant_num'];
//                $each_goods['percent'] = number_format($each_goods['participant_num'] * 100 / $each_goods['sum_times'],2);
//            }
            $goods_list[$each_category['id']] = $goods;
        }

        $this->assign('goods_list', $goods_list);

        //最新上架
        list($new_goods, $null) = $m_goods->get_list_by_category(0, 5, 1, 2);

        $this->calc_process($new_goods);
        $this->assign('new_goods', $new_goods);

        //一元传奇
        list($legendary_list, $null) = $m_order->get_one_legendary();
        $this->assign('legendary_list', $legendary_list);

        //首页晒单分享

        list($share_list, $null) = $m_order->get_share_list();


        $share_imgs = [];
        foreach ($share_list as &$each_share) {
            $each_share['pic_list'] = trim($each_share['pic_list'], ',');
            if (strlen($each_share['pic_list']) > 0) {
                $each_img_list = explode(',', $each_share['pic_list']);
                $each_share['share_img'] = array_shift($each_img_list);
                array_push($share_imgs, $each_share['share_img']);
            }
        }

        $share_img_map = $m_image->get_img_map_by_ids($share_imgs);
        foreach ($share_list as &$each_share) {
            $each_share['share_image'] = $share_img_map[$each_share['share_img']]['img_path'];
        }


        $this->assign('share_list', $share_list);

        return $this->display();
    }

    //刷新开奖结果
    public function refresh_results()
    {
        $nper_id = I('get.id', 0, 'intval');
        $m_goods = new GoodsModel();
        $result = $m_goods->get_nper_detail_by_id($nper_id);
        if ($result) {
            $this->assign('each_opening', $result);
            return json_encode(['code' => 1, 'msg' => '', 'html' => $this->fetch()]);
        } else {
            return json_encode(['code' => -404, 'msg' => 'not found']);
        }
    }

    //计算剩余和百分比
    private function calc_process(&$good_list)
    {
        foreach ($good_list as &$each_goods) {
            $each_goods['remain'] = $each_goods['sum_times'] - $each_goods['participant_num'];
            $each_goods['percent'] = $each_goods['sum_times'] > 0 ? floor(((int)$each_goods['participant_num']  / (int)$each_goods['sum_times']) * 10000) / 100 : '0.00';
        }
        return $good_list;
    }

    public function get_goods_key()
    {
        $goodsname = $_POST['keyword'];
        $where = array(
            'name' => ['like', '%' . $goodsname . '%'],
            'status' => 1,
        );
        $goods = M('goods');
        $goods = $goods->where($where)->limit(0, 5)->select();
        $arr = array();
        foreach ($goods as $key => $good) {
            $arr['data'][]['title'] = $good['name'];
        }
        return json_encode($arr);
    }

    public function te()
    {
        $weiShu = 345;
        $wei = '';
        $w = array(16 => '万', 8 => '千', 4 => '百', 2 => '十', 1 => '个');
        foreach ($w as $p => $v) {
            if ($weiShu & $p) $wei .= $v;
        }
        $wei .= '：';
        dump($wei);

    }

    public function join_history()
    {
        return $this->display();
    }
}
