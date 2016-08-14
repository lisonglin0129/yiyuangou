<?php
namespace app\yyg\controller;

use app\core\controller\Common;
use app\core\model\CategoryModel;
use app\core\model\CommonModel;
use app\core\model\GoodsModel;
use app\core\model\OrderModel;

class Lists extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //分类列表
    public function index($category = 0, $page = 1, $sort = 0)
    {
        $page_size = 12;
        $m_goods = new GoodsModel();
        list($goods_list, $count_all) = $m_goods->get_list_by_category($category, $page_size, $page, $sort);
        //计算剩余和百分比
        $this->calc_process($goods_list);

        $this->assign('count_all', $count_all);
        $this->assign('count_page', count($goods_list));
        $this->assign('page_current', $page);
        $this->assign('page_count', floor($count_all / $page_size) + 1);
        $this->assign('goods_list', $goods_list);
        return $this->display();
    }

    //搜索
    public function search($keyword = "")
    {
        $keyword = trim($keyword);
        empty($keyword) && $this->redirect('/');
        $m_goods = new GoodsModel();
        list($goods_list, $count_all) = $m_goods->search_goods_by_name($keyword);
        //计算剩余和百分比
        $this->calc_process($goods_list);

        list($hot_goods, $null) = $m_goods->get_list_by_category(0, 8, 1, 0);
        $this->calc_process($hot_goods);
        $this->assign('hot_goods', $hot_goods);

        $this->assign('count_all', $count_all);
        $this->assign('goods_list', $goods_list);
        return $this->display();
    }

    //十元专区
    public function ten()
    {
        $m_goods = new GoodsModel();
        list($goods_list, $count_all) = $m_goods->get_list_by_category(0, 0, 0, 0, 2);
        //计算剩余和百分比
        $this->calc_process($goods_list);
        $this->assign('count_all', $count_all);
        $this->assign('goods_list', $goods_list);
        return $this->display();
    }

    //最新揭晓 nper_list.status=2/3
    public function results()
    {
        //最快揭晓 nper_list.status = 1
        $m_goods = new GoodsModel();
        list($remain_goods, $null) = $m_goods->get_list_by_category(0, 24, 1, 1);
        $this->calc_process($remain_goods);
        $this->assign('remain_goods', $remain_goods);
        return $this->display();
    }

    //最新揭晓 拉取...
    public function pull_results()
    {
        $page = I('get.page', 1, 'intval');
        $size = I('get.size', 20, 'intval');
        $m_order = new OrderModel();
        list($opening_list, $count) = $m_order->get_opening_list($page, $size);

        if ($opening_list) {
            foreach ($opening_list as &$each_opening) {
                $count_down = intval($each_opening['open_time']) - time();
                $each_opening['count_down'] = $count_down > 0 ? $count_down . '000' : '0';
            }

            $this->assign("list", $opening_list);

            $data = array(
                'code' => 1,
                'msg' => '',
                'data' => array(
                    'html' => $this->fetch(),
                    'count' => intval($count),
                    'length' => count($opening_list)
                )
            );
            return json_encode($data);
        } else {
            return json_encode(['code' => -404, 'msg' => 'no more data...']);
        }
    }

    //最新揭晓 根据期数返回当前nper_id
    public function refresh_results()
    {
        $nper_id = I('get.id', 0, 'intval');
        $m_goods = new GoodsModel();
        $result = $m_goods->get_nper_detail_by_id($nper_id);
        if ($result) {
            $this->assign('vo',$result);
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
            $each_goods['percent'] = $each_goods['sum_times'] > 0 ? number_format($each_goods['participant_num'] * 100 / $each_goods['sum_times'], 2) : '0.00';
        }
        return $good_list;
    }
}
