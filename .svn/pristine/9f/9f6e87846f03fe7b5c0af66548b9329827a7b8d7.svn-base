<?php
namespace app\yyg\controller;

use app\core\controller\Common;
use app\core\lib\Condition;
use app\core\model\CategoryModel;
use app\core\model\CommonModel;
use app\core\model\GoodsModel;
use app\core\model\ImageModel;
use app\core\model\OrderModel;
use app\core\lib\Page;
use app\core\model\UserModel;

class Goods extends Common
{
    private $page_num;

    public function __construct()
    {
        parent::__construct();
    }
    //0元购
    /**
     * 商品展示详情页
     * @ param string id :商品id-商品期数id
     * @ tips 如果商品读取错误或商品期数读取错误,跳转到首页
     */
    //商品推荐
    public function detail()
    {
        $id = remove_xss(I("get.id", null));
        empty($id) && $this->jump_to_index();
        //得到商品id,当前期数id
        $arr = explode('-', $id);

        $gid =(int) $arr[0];//商品id
        $nid =(int) $arr[1];//期数id
        //参数不符合跳转到首页
        //!preg_match('/[1-9][0-9]+/', $gid) || !preg_match('/[1-9][0-9]+/', $nid) && $this->jump_to_index();
        if ( $gid <= 0 || $nid <=0 )
            $this->jump_to_index();



        //开奖固定数值
        $m_comm = new CommonModel();
        $lottory_base = num_base_mask(0,1,1);
        $this->assign('lottory_base', $lottory_base);


        $m_goods = new GoodsModel();
        //获取商品信息
        $g_info = $m_goods->get_goods_info_by_id($gid);
        empty($g_info) && $this->jump_to_index();

        //获取商品图片信息
        $m_img = new ImageModel();
        $pic_index = $m_img->get_img_list_by_id($g_info['index_img']);
        $pic_list = $m_img->get_img_list_by_id($g_info['pic_list']);

        //获取当前商品分类
        if (!empty($g_info['category'])) {
            $m_cat = new CategoryModel();
            $cat_name = $m_cat->get_category_name_by_id($g_info['category']);
            $this->assign('cat_name', $cat_name);
        }

        //图片信息赋值
        $pic_index && $this->assign("pic_index", $pic_index[0]['img_path']);
        $pic_list && $this->assign("pic_list", $pic_list);

        //获取商品期数信息
        $n_info = $m_goods->get_nper_info_by_id($nid);
        empty($n_info) && $this->jump_to_index();
        //当前期数
        $this->assign('now_issue', $n_info['id']);

        //校验期数是否属于商品
        !empty($n_info['pid']) && !empty($g_info['id']) && $n_info['pid'] !== $g_info['id'] && $this->jump_to_index();

        $m_order = new OrderModel();


        //当前用户当期购买的幸运号码和号码数量
        $luck_num_arr = array();
        $luck_list = $m_order->get_now_user_luck_num_by_nper_id($n_info['id']);
        foreach ($luck_list as $k => $v) {
            $tmp_arr = explode(",", $v["code_list"]);
            $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
        }

        $luck_num_arr = array_filter($luck_num_arr);

        if ($luck_list) {
            $this->assign("now_user_num", count($luck_num_arr));
            $this->assign("luck_list", $luck_num_arr);
        }



        //状态为未开奖的时候,执行括号里的脚本 1
        if ($n_info['status'] == '1') {
            //剩余购买次数
            $last_times = (int)$n_info['sum_times'] - (int)$n_info['participant_num'];
            $this->assign("last_times", $last_times);

            //已完成进度百分比
            $precent = floor(((int)$n_info['participant_num']  / (int)$n_info['sum_times']) * 10000) / 100;
            $this->assign("precent", $precent);

        }


        //状态为已开奖的时候,执行括号里的脚本 2,3
        if ($n_info['status'] == '2' || $n_info['status'] == '3') {
            //倒计时秒
            $open_time = $n_info['open_time'];
            $sec = $open_time - NOW_TIME;
            $sec = ((int)$sec < 0) ? 0 : $sec;

            $this->assign("sec", $sec*1000);
        }

        if ($n_info['status'] == '3') {
            if (!empty($n_info['luck_uid'])) {
                //获取得奖用户信息
                $m_user = new UserModel();
                $u_info = $m_user->get_user_info_by_filed('id', $n_info['luck_uid']);
                $this->assign('u_info', $u_info);

                //获奖订单信息
                $m_order = new OrderModel();
                $luck_info= $m_order->get_luck_info_by_nper_id_num($nid,$n_info["luck_num"]);

                //获取幸运订单信息
                $luck_order_info = $m_order->get_c_order_info_by_filed('order_id',$luck_info['order_id']);
                $this->assign("luck_time",$luck_order_info['pay_time']);

                //幸运号码数量
                $luck_num_arr = array();
                $luck_list = $m_order->get_user_luck_num_by_nper_id($u_info['id'],$luck_info['nper_id']);

                foreach ($luck_list as $k => $v) {
                    $tmp_arr = explode(",", str_implode($v["code_list"]));
                    $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
                }


                $this->assign("luck_num_count",count($luck_num_arr));
//                //本期参与人数
//                $tmp_luck = explode(",",$luck_info['code_list']);
//                $tmp_luck = array_filter($tmp_luck);
//
//                $this->assign('nper_buyer_count', count($tmp_luck));
            }

        }

        //获取当前正在购买中的本商品期数信息,状态2,3用到START
        $new_nper_info = $m_goods->get_no_lottory_nper_info_by_pid($g_info['id']);
        //如果存在最新一期的内容
        if ($new_nper_info) {
            $this->assign('new_nper_info', $new_nper_info);

            //最新一期剩余购买次数
            $new_last_times = (int)$new_nper_info['sum_times'] - (int)$new_nper_info['participant_num'];
            $this->assign("new_last_times", $new_last_times);

            //最新一期已完成进度百分比
            $new_precent = floor(((int)$new_nper_info['participant_num']  / (int)$new_nper_info['sum_times']) * 10000) / 100;
            $this->assign("new_precent", $new_precent);
        }
        //获取当前正在购买中的本商品期数信息,状态2,3用到END

        //如果当期已开奖,获取当期开奖信息,劈分开奖号码到数组
        $n_info['open_num'] && $this->assign('open_num', $n_info['open_num']);//彩票开奖号码
        $n_info['remainder'] && $this->assign('remainder', $n_info['remainder']);//余数
        $n_info['luck_num'] && $this->assign('luck_num', $n_info['luck_num']);//开奖号码


        $this->assign("g_info", $g_info);
        $this->assign("n_info", $n_info);

//        //开奖地址
//        $url = $m_comm->get_conf('NPER_OPEN_API').$n_info['id'];
//        $this->assign("nper_open_api", $url);

        return $this->display();
    }

    /**
     * 商品展示详情页
     * @ param string id :商品id-商品期数id
     * @ tips 如果商品读取错误或商品期数读取错误,跳转到首页
     */
    //商品推荐
    public function detail_zero()
    {
        $id = remove_xss(I("get.id", null));
        empty($id) && $this->jump_to_index();
        //得到商品id,当前期数id
        $arr = explode('-', $id);

        $gid = $arr[0];//商品id
        $nid = $arr[1];//期数id
        //参数不符合跳转到首页
        !preg_match('/[1-9][0-9]+/', $gid) || !preg_match('/[1-9][0-9]+/', $nid) && $this->jump_to_index();



        //开奖固定数值
        $m_comm = new CommonModel();
        $lottory_base = num_base_mask(0,1,1);
        $this->assign('lottory_base', $lottory_base);


        $m_goods = new GoodsModel();
        //获取商品信息
        $g_info = $m_goods->get_goods_info_by_id($gid);
        empty($g_info) && $this->jump_to_index();

        //获取商品图片信息
        $m_img = new ImageModel();
        $pic_index = $m_img->get_img_list_by_id($g_info['index_img']);
        $pic_list = $m_img->get_img_list_by_id($g_info['pic_list']);

        //获取当前商品分类
        if (!empty($g_info['category'])) {
            $m_cat = new CategoryModel();
            $cat_name = $m_cat->get_category_name_by_id($g_info['category']);
            $this->assign('cat_name', $cat_name);
        }

        //图片信息赋值
        $pic_index && $this->assign("pic_index", $pic_index[0]['img_path']);
        $pic_list && $this->assign("pic_list", $pic_list);

        //获取商品期数信息
        $n_info = $m_goods->get_nper_info_by_id($nid);
        empty($n_info) && $this->jump_to_index();
        //当前期数
        $this->assign('now_issue', $n_info['id']);

        //校验期数是否属于商品
        !empty($n_info['pid']) && !empty($g_info['id']) && $n_info['pid'] !== $g_info['id'] && $this->jump_to_index();

        $m_order = new OrderModel();


        //当前用户当期购买的幸运号码和号码数量
        $luck_num_arr = array();
        $luck_list = $m_order->get_now_user_luck_num_by_nper_id($n_info['id']);
        foreach ($luck_list as $k => $v) {
            $tmp_arr = explode(",", $v["code_list"]);
            $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
        }

        $luck_num_arr = array_filter($luck_num_arr);

        if ($luck_list) {
            $this->assign("now_user_num", count($luck_num_arr));
            $this->assign("luck_list", $luck_num_arr);
        }



        //状态为未开奖的时候,执行括号里的脚本 1
        if ($n_info['status'] == '1') {
            //剩余购买次数
            $last_times = (int)$n_info['sum_times'] - (int)$n_info['participant_num'];
            $this->assign("last_times", $last_times);

            //已完成进度百分比
            $precent = floor(((int)$n_info['participant_num']  / (int)$n_info['sum_times']) * 10000) / 100;
            $this->assign("precent", $precent);

        }


        //状态为已开奖的时候,执行括号里的脚本 2,3
        if ($n_info['status'] == '2' || $n_info['status'] == '3') {
            //倒计时秒
            $open_time = $n_info['open_time'];
            $sec = $open_time - NOW_TIME;
            $sec = ((int)$sec < 0) ? 0 : $sec;

            $this->assign("sec", $sec*1000);
        }

        if ($n_info['status'] == '3') {
            if (!empty($n_info['luck_uid'])) {
                //获取得奖用户信息
                $m_user = new UserModel();
                $u_info = $m_user->get_user_info_by_filed('id', $n_info['luck_uid']);
                $this->assign('u_info', $u_info);

                //获奖订单信息
                $m_order = new OrderModel();
                $luck_info= $m_order->get_luck_info_by_nper_id_num($nid,$n_info["luck_num"]);

                //获取幸运订单信息
                $luck_order_info = $m_order->get_c_order_info_by_filed('order_id',$luck_info['order_id']);
                $this->assign("luck_time",$luck_order_info['pay_time']);

                //幸运号码数量
                $luck_num_arr = array();
                $luck_list = $m_order->get_user_luck_num_by_nper_id($u_info['id'],$luck_info['nper_id']);

                foreach ($luck_list as $k => $v) {
                    $tmp_arr = explode(",", str_implode($v["code_list"]));
                    $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
                }


                $this->assign("luck_num_count",count($luck_num_arr));
//                //本期参与人数
//                $tmp_luck = explode(",",$luck_info['code_list']);
//                $tmp_luck = array_filter($tmp_luck);
//
//                $this->assign('nper_buyer_count', count($tmp_luck));
            }

        }

        //获取当前正在购买中的本商品期数信息,状态2,3用到START
        $new_nper_info = $m_goods->get_no_lottory_nper_info_by_pid($g_info['id']);
        //如果存在最新一期的内容
        if ($new_nper_info) {
            $this->assign('new_nper_info', $new_nper_info);

            //最新一期剩余购买次数
            $new_last_times = (int)$new_nper_info['sum_times'] - (int)$new_nper_info['participant_num'];
            $this->assign("new_last_times", $new_last_times);

            //最新一期已完成进度百分比
            $new_precent = floor((int)($new_nper_info['participant_num']  / (int)$new_nper_info['sum_times']) * 10000) / 100;
            $this->assign("new_precent", $new_precent);
        }
        //获取当前正在购买中的本商品期数信息,状态2,3用到END

        //如果当期已开奖,获取当期开奖信息,劈分开奖号码到数组
        $n_info['open_num'] && $this->assign('open_num', $n_info['open_num']);//彩票开奖号码
        $n_info['remainder'] && $this->assign('remainder', $n_info['remainder']);//余数
        $n_info['luck_num'] && $this->assign('luck_num', $n_info['luck_num']);//开奖号码


        $this->assign("g_info", $g_info);
        $this->assign("n_info", $n_info);

//        //开奖地址
//        $url = $m_comm->get_conf('NPER_OPEN_API').$n_info['id'];
//        $this->assign("nper_open_api", $url);

        return $this->display();
    }

    //跳转到首页
    private function jump_to_index()
    {
//        die('跳转到首页了');
        $this->redirect('index/index');
    }

    //根据期数获取夺宝参与记录
    public function get_deposer_list()
    {
        $id = I('post.id');
        (empty($id) || !is_numeric($id)) && die_json("-100");//期数id错误

        //第几页
        $page = I('post.page', '1');
        $page_num = 50;//每页显示条数

        $condition_rule = array(
            array(
                'field' => 'o.nper_id',
                'value' => $id
            ),
            array(
                'field' => 'o.dealed',
                'value' => 'true'
            ),
        );
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();

        //获取订单列表
        $m_order = new OrderModel();
        $rt = $m_order->get_payed_order_by_nper_id($model);
        $this->assign('list', $rt['data']);

        /*生成分页html*/
        $my_page = new Page($rt["count"], $page_num, $page, U('get_deposer_list'), 2);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $rt = array(
            "code" => '1',
            'html' => $this->fetch()
        );
        die_json($rt);
    }

    public function get_code_list() {
        $id = I('post.id');
        $uid = I('post.uid');
        $oid = I('post.oid');
        $m = new GoodsModel();
        $code_list = $m->get_code_list($id,$uid,$oid);
        $code = [
            'code' => '1',
            'data' => $code_list,
         ];
        return json_encode($code);


        
    }


    //获取0元购夺宝记录
    public function get_deposer_list_by_join_type(){
        $id = I('post.id');
        $type=I('post.type');
        (empty($id) || !is_numeric($id)) && die_json("-100");//期数id错误

        //第几页
        $page = I('post.page', '1');
        $page_num = 20;//每页显示条数

        $condition_rule = array(
            array(
                'field' => 'o.nper_id',
                'value' => $id
            ),
            array(
                'field' => 'o.dealed',
                'value' => 'true'
            ),
        );
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();

        //获取订单列表
        $m_order = new OrderModel();
        $rt = $m_order->get_zero_payed_order_by_nper_id($model,$type);
        $this->assign('list', $rt['data']);
        /*生成分页html*/
        $my_page = new Page($rt["count"], $page_num, $page, U('get_deposer_list_by_join_type'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('type',$type);
        $rt = array(
            "code" => '1',
            'html' => $this->fetch()
        );
        die_json($rt);
    }

    //根据商品id获取晒单记录
    public function get_delivery_list()
    {

        $id = I('post.id');
        (empty($id) || !is_numeric($id)) && die_json("-100");//期数id错误

        //第几页
        $page = (int)I('post.page', '1');
        $page_num = 10;//每页显示条数

        $condition_rule = array(
            array(
                'field' => 'goods_id',
                'value' => $id
            ),
        );

        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();

        //获取订单列表
        $m_order = new OrderModel();
        list($result, $count) = $m_order->get_delivery_list_by_goods_id($model);


        //填充图片列表
        $pic_list = [];
        foreach ($result as $each_row) {
            $pic_list = array_merge($pic_list, explode(',', trim($each_row['pic_list'], ',')));
        }
        if (!empty($pic_list) && is_array($pic_list)) {
            $m_image = new ImageModel();
            $pic_list_map = $m_image->get_img_map_by_ids($pic_list);
        }
        foreach ($result as &$each_row) {
            $each_row['picture_list'] = [];
            $each_pic_list = explode(',', trim($each_row['pic_list'], ','));
            foreach ($each_pic_list as $each_pic) {
                array_push($each_row['picture_list'], $pic_list_map[$each_pic]['img_path']);
            }
        }

        /*生成分页html*/
        $my_page = new Page($count, $page_num, $page, U('get_delivery_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);

        $this->assign('list', $result);

        $rt = array(
            "code" => '1',
            'html' => $this->fetch()
        );
        die_json($rt);


    }

    //根据商品id获取往期已开奖/关闭的夺宝列表
    public function get_history_list()
    {
        $id = I('post.id');
        (empty($id) || !is_numeric($id)) && die_json("-100");//期数id错误

        //第几页
        $page = (int)I('post.page', '1');
        $page_num = 10;//每页显示条数

        $condition_rule = array(
            array(
                'field' => 'n.pid',
                'value' => $id
            ),
        );

        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();

        //获取订单列表
        $m_order = new OrderModel();
        $rt = $m_order->get_history_list($model);


        /*生成分页html*/
        $my_page = new Page($rt["count"], $page_num, $page, U('get_delivery_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);

        $this->assign('list', $rt['data']);

        $rt = array(
            "code" => '1',
            'html' => $this->fetch()
        );
        die_json($rt);

    }
    //往期夺宝列表
    public function get_history_list_p(){
        $id = I('post.id');
        (empty($id) || !is_numeric($id)) && die_json("-100");//期数id错误
        //第几页
        $page = I('post.page', '1','intval');
        $page_num = 10;//每页显示条数

        $m_order = new OrderModel();
        list($history_list,$count_all) = $m_order->get_history_list_p($id,$page,$page_num);

        $this->assign('history_list',$history_list);
        $this->assign('count_page',count($history_list));
        $this->assign('page_current',$page);
        $this->assign('page_count',floor($count_all/$page_num)+1);

        $html = $this->fetch();
        echo json_encode(['code'=>1,'html'=>$html]);
    }

    //根据期数id获取夺宝详情订单列表和计算结果
    public function get_calc_result_list()
    {
        $id = I('post.id');
        (empty($id) || !is_numeric($id)) && die_json("-100");//期数id错误

        //第几页
        $page = (int)I('post.page', '1');
        $page_num = 50;//每页显示条数

        //开奖固定数值
        $lottory_base = num_base_mask(0,1,1);
        $this->assign('lottory_base', $lottory_base);

        //获取期数详情
        $m_goods = new GoodsModel();
        $m_order = new OrderModel();
        $n_info = $m_goods->get_nper_info_by_id($id);
        empty($n_info) && die_json('-110');//期数获取失败
        $this->assign('n_info', $n_info);

        //获取当期最后一个用户的订单信息
        $last_order =$m_order->get_last_user_order_by_nper($id);
        //最后一个用户的付款时间
        $this->assign('last_pay_time',$last_order['pay_time']);

        //如果当期已开奖,劈分开奖号码到数组
        if ($n_info['open_num']) {
            $open_num = str_split($n_info['open_num'], 1);
            if (count($open_num) == 5) {
                $this->assign('open_num', $open_num);
                //余数号码
                $remainder = str_split($n_info['remainder'], 1);
                $remainder &&
                $this->assign('remainder', $remainder);
                //开奖号码
                $luck_num = str_split(num_base_mask(intval($n_info['remainder']),1,1), 1);
                $luck_num &&
                $this->assign('luck_num', $luck_num);
            }

        }

        //获取列表
        $condition_rule = array(
            array(
                'field' => 'o.nper_id',
                'value' => $id
            ),
            array(
                'field' => 'o.dealed',
                'value' => 'true'
            ),
        );
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();
        //获取订单列表
        $m_order = new OrderModel();
        $rt = $m_order->get_payed_order_by_nper_id($model);
       //file_put_contents('./menu.txt',var_export($rt['data'],true));
        /*生成分页html*/
        $my_page = new Page($rt["count"], $page_num, $page, U('get_calc_result_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);
        $this->assign('list', $rt['data']);
        $html = $this->fetch();
        $rt = array(
            "code" => '1',
            'html' => $html
        );
        die_json($rt);
    }

    //查看用户购买期数的幸运号码
    public function see_luck_num()
    {
        $nper_id = I('post.nper_id');
        $uid = I('post.uid');
        $join_type=I('post.join_type');
        (empty($nper_id) || empty($uid)) && die_json("-100");//参数不完整

        $m_order = new OrderModel();
        $luck_num_arr = array();
        $luck_list = $m_order->get_zero_user_luck_num_by_nper_id($uid,$nper_id);
        foreach ($luck_list as $k => $v) {
            $tmp_arr = explode(",", $v["code_list"]);
            $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
        }
        $luck_num_arr = array_filter($luck_num_arr);
        //获取本期幸运号码信息
        $luck_num_nper = $m_order->get_luck_info_by_nper_id($nper_id);

        $this->assign('luck_num', $luck_num_nper["luck_num"]);
        $this->assign('num', count($luck_num_arr));
        $this->assign('list', $luck_num_arr);
        $arr = array(
            "code" => "1",
            "html" => $this->fetch()
        );
        die_json($arr);
    }

    //查看用户购买期数的幸运号码(用在'夺宝参与记录'查看'所有夺宝号码')
    public function see_luck_num_json()
    {
        $nper_id = I('post.nper_id');
        $uid = I('post.uid');
        (empty($nper_id) || empty($uid)) && die_json("-100");//参数不完整
        $m_order = new OrderModel();
        $list = $m_order->get_luck_num_list_by_nper_uid($nper_id, $uid);
        empty($list) && die_json("-1");
        $arr = array(
            "code" => "1",
            "data" => $list
        );
        die_json($arr);
    }

    //根据商品id跳转到当前商品正在购买的期数
    public function jump_to_goods_buying()
    {
        $gid = I("get.gid");
        empty($gid) && $this->redirect('/');//id错误跳转到首页
        //获取当前正在购买的商品的期数信息
        $m_goods = new GoodsModel();
        $info = $m_goods->get_no_lottory_nper_info_by_pid($gid);

        if($info==NULL){
            $this->redirect('Index/index');
            die();
        }
        $this->redirect('/detail/'.$gid . '-' . $info['id'].'.html');
    }


    //00元购商品详情页
    public function zero_detail(){
        $gid = remove_xss(I("get.gid", null));
        $nid=remove_xss(I("get.nid", null));
        empty($gid) && $this->jump_to_index();
        $m_goods = new GoodsModel();
        //得到商品id,当前期数id
        empty($nid)&&$nid =$m_goods->get_zero_no_lottory_nper_id($gid) ;//期数id
        //参数不符合跳转到首页
        !preg_match('/[1-9][0-9]+/', $gid) || !preg_match('/[1-9][0-9]+/', $nid) && $this->jump_to_index();
        //开奖固定数值
       // $m_comm = new CommonModel();
        $lottory_base = num_base_mask(0,1,1);
        $this->assign('lottory_base', $lottory_base);



        //获取商品信息
        $g_info = $m_goods->get_goods_info_by_id($gid);
        empty($g_info) && $this->jump_to_index();

        //获取商品图片信息
        $m_img = new ImageModel();
        $pic_index = $m_img->get_img_list_by_id($g_info['index_img']);
        $pic_list = $m_img->get_img_list_by_id($g_info['pic_list']);

        //获取当前商品分类
        if (!empty($g_info['category'])) {
            $m_cat = new CategoryModel();
            $cat_name = $m_cat->get_category_name_by_id($g_info['category']);
            $this->assign('cat_name', $cat_name);
        }

        //图片信息赋值
       // $pic_index && $this->assign("pic_index", $pic_index[0]['img_path']);
        $pic_list && $this->assign("pic_list", array_merge($pic_index,$pic_list));

        //获取商品期数信息
        $n_info = $m_goods->get_nper_info_by_id($nid);
        empty($n_info) && $this->jump_to_index();
        //当前期数
        $this->assign('now_issue', $n_info['id']);

        //校验期数是否属于商品
        !empty($n_info['pid']) && !empty($g_info['id']) && $n_info['pid'] !== $g_info['id'] && $this->jump_to_index();

        $m_order = new OrderModel();
        //当前用户当期购买的幸运号码和号码数量
        $luck_num_arr = array();
        $luck_list = $m_order->get_now_user_luck_num_by_nper_id($n_info['id']);
        foreach ($luck_list as $k => $v) {
            $tmp_arr = explode(",", $v["code_list"]);
            $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
        }

        $luck_num_arr = array_filter($luck_num_arr);

        if ($luck_list) {
            $this->assign("now_user_num", count($luck_num_arr));
            $this->assign("luck_list", $luck_num_arr);
        }



        //状态为未开奖的时候,执行括号里的脚本 1
        if ($n_info['status'] == '1') {
            //奇数剩余购买次数
            $odd_last_times = (int)$n_info['odd_times'] - (int)$n_info['odd_join_num'];
            $this->assign("odd_last_times", $odd_last_times);
            //偶数剩余次数
            $even_last_times = (int)$n_info['even_times'] - (int)$n_info['even_join_num'];
            $this->assign("even_last_times", $even_last_times);
            //已完成进度百分比-奇数
            $odd_precent = $n_info['odd_times']==0?0:round(100 * (int)$n_info['odd_join_num'] / (int)$n_info['odd_times'], 1);
            $this->assign("odd_percent", $odd_precent);
            //已完成进度百分比-偶数
            $even_precent = $n_info['even_times']==0?0:round(100 * (int)$n_info['even_join_num'] / (int)$n_info['even_times'], 1);
            $this->assign("even_percent", $even_precent);

        }


        //状态为已开奖的时候,执行括号里的脚本 2,3
        if ($n_info['status'] == '2' || $n_info['status'] == '3') {
            //倒计时秒
            $open_time = $n_info['open_time'];
            $sec = $open_time - NOW_TIME;
            $sec = ((int)$sec < 0) ? 0 : $sec;

            $this->assign("sec", $sec*1000);
        }

        if ($n_info['status'] == '3') {
            if (!empty($n_info['luck_uid'])) {
                //获取得奖用户信息
                $m_user = new UserModel();
                $u_info = $m_user->get_user_info_by_filed('id', $n_info['luck_uid']);
                $this->assign('u_info', $u_info);

                //获奖订单信息
                $m_order = new OrderModel();
                $luck_info= $m_order->get_luck_info_by_nper_id_num($nid,$n_info["luck_num"]);

                //获取幸运订单信息
                $luck_order_info = $m_order->get_c_order_info_by_filed('order_id',$luck_info['order_id']);
                $this->assign("luck_time",$luck_order_info['pay_time']);

                //幸运号码数量
                $luck_num_arr = array();
                $luck_list = $m_order->get_zero_user_luck_num_by_nper_id($u_info['id'],$luck_info['nper_id']);

                foreach ($luck_list as $k => $v) {
                    $tmp_arr = explode(",", str_implode($v["code_list"]));
                    $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
                }
                $this->assign('join_type',$luck_order_info['join_type']);
                $this->assign("luck_num_count",count($luck_num_arr));
//                //本期参与人数
//                $tmp_luck = explode(",",$luck_info['code_list']);
//                $tmp_luck = array_filter($tmp_luck);
//
//                $this->assign('nper_buyer_count', count($tmp_luck));
            }

        }

        //获取当前正在购买中的本商品期数信息,状态2,3用到START
        $new_nper_info = $m_goods->get_zero_no_lottory_info($g_info['id']);
        //如果存在最新一期的内容
        if ($new_nper_info) {
            $this->assign('new_nper_info', $new_nper_info);
            $odd_last_times = (int)$new_nper_info['odd_times'] - (int)$new_nper_info['odd_join_num'];
            $this->assign("odd_last_times", $odd_last_times);
            //偶数剩余次数
            $even_last_times = (int)$new_nper_info['even_times'] - (int)$new_nper_info['even_join_num'];
            $this->assign("even_last_times", $even_last_times);
            //最新一期已完成进度百分比-奇数
            $odd_precent = round(100 * (int)$new_nper_info['odd_join_num'] / (int)$new_nper_info['odd_times'], 1);
            $this->assign("odd_precent", $odd_precent);
            //最新一期已完成进度百分比-偶数
            $even_precent = round(100 * (int)$new_nper_info['even_join_num'] / (int)$new_nper_info['even_times'], 1);
            $this->assign("even_precent", $even_precent);
        }
//        获取当前正在购买中的本商品期数信息,状态2,3用到END

//        如果当期已开奖,获取当期开奖信息,劈分开奖号码到数组
        $n_info['open_num'] && $this->assign('open_num', $n_info['open_num']);//彩票开奖号码
        $n_info['remainder'] && $this->assign('remainder', $n_info['remainder']);//余数
        $n_info['luck_num'] && $this->assign('luck_num', $n_info['luck_num']);//开奖号码


        $this->assign("g_info", $g_info);
        $this->assign("n_info", $n_info);
        return $this->display();
    }
}
