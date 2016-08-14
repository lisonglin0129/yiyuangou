<?php
namespace app\yyg\controller;

use app\core\controller\Common;
use app\core\model\CategoryModel;
use app\core\model\GoodsModel;
use app\core\model\ImageModel;
use app\core\model\OrderModel;

class Share extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //
    public function index()
    {
        $count = M('show_order')->where("status = 1 AND complete = 1 AND create_time < ".time())->count();
        $this->assign('count',$count);
        return $this->display();
    }

    public function detail($id)
    {
        $m_order = new OrderModel();
        $share_info = $m_order->get_share_info_by_id($id);

        if (empty($share_info)) {
            return $this->error('参数错误', '/');
        }
        $this->assign('share_info', $share_info);
        $pic_list = $share_info['pic_list'];
        $m_image = new ImageModel();
        $pic_list = $m_image->get_img_map_by_ids($pic_list);
        $this->assign('image_list', $pic_list);
        return $this->fetch();
    }

    public function pull_list()
    {
        $page = I('get.page', 1, 'intval');
        $size = I('get.size', 20, 'intval');
        $m_order = new OrderModel();
        list($share_list, $count) = $m_order->get_share_list($page, $size);

        $this->assign("list",$share_list);
//        return $this->fetch();
        if ($share_list) {
            $arr = array(
                'code' => 1,
                'msg' => '',
                'data' => array(
                    'html' => $this->fetch(),
                    'count' => intval($count),
                    'length' => count($share_list)
                )
            );
            die_json($arr);
        } else {
            $arr = array(
                'code' => -404,
                'msg' => 'no more data...'
            );
            die_json($arr);
        }
    }
}
