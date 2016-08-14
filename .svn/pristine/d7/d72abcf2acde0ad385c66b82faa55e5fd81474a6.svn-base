<?php
namespace app\core\controller;

use app\core\model\GoodsModel;
use app\core\model\OrderModel;
use app\core\model\PayModel;
use app\core\model\CouponModel;

class Pay extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //添加到购物车全部
    function add_to_cart_all()
    {
        $list = I("post.list", []);
        $arr = json_decode($list, true);
        foreach ($arr as $k => $v) {
            $this->add_to_cart($v['nper_id'], $v['num'], true);
        }
        ok_return("添加全部到购物车成功");
    }
    //添加到购物车
    public function add_to_cart($nper_id = null, $num = null, $flag = false)
    {
        $join_type=I('post.join_type')?I('post.join_type'):'';
        if (empty($nper_id)) {
            $post = remove_arr_xss(I("post."));
            extract($post);
        }

        (empty($nper_id) || empty($num) ||
            !is_numeric($nper_id) || !is_numeric($num))||$num<=0 && die_json('-100');//参数错误

        //获取当前期数剩余购买次数
        $m_nper = new GoodsModel();
        $nper_info = $m_nper->get_nper_info_by_id($nper_id);
        empty($nper_info) && die_json("-1");
        if($join_type==1){
            $last_num=(int)$nper_info['odd_times'] - (int)$nper_info['odd_join_num'];
        }elseif($join_type==2){
            $last_num=(int)$nper_info['even_times'] - (int)$nper_info['even_join_num'];
        }else{
            $last_num = (int)$nper_info['sum_times'] - (int)$nper_info['participant_num'];
        }
        //本期最大购买次数限制
        $max_times = $nper_info['max_times'];

        if (is_user_login()) {
            $r = $this->add_to_cart_login($nper_id, $num, $last_num, $max_times,$join_type);
        } else {
            $r = $this->add_to_cart_no_login($nper_id, $num, $last_num, $max_times);
        }
        if ($flag) {
            return $r;
        }
        $r && die_json($r);
        die_json("-1");
    }

    //登陆后添加到购物车
    private function add_to_cart_login($nper_id, $num, $last_num, $max_times = 1000,$join_type='')
    {

        $m_nper = new GoodsModel();
        //获取当前用户当前购物车商品期数为nperid的记录
        $cart_info = $m_nper->get_user_cart_info_by_nper_id($nper_id,!empty($join_type)?$join_type:0);
        //限制用户购买最大次数
        $num = ($num < $max_times) ? $num : $max_times;
        if (empty($cart_info)) {
            $num = ($num > $last_num) ? $last_num : $num;
        } else {
            //原来购物车有,计算数量并添加到购物车
            $num = $num + (int)$cart_info["num"];
            $num = ($num > $last_num) ? $last_num : $num;
        }

        //直接添加到购物车
        $arr = array(
            "nper_id" => $nper_id,
            "num" => $num,
            "join_type"=>!empty($join_type)?$join_type:''
        );

        //添加到购物车
        if ($m_nper->update_cart_info($arr)) {
            $rt = array(
                "code" => "1",
            );
            ($last_num == $num) && $rt["flag"] = '1';
            //获取用户购物车内的商品总数
            $num = $m_nper->get_cart_num();
            $rt["num"] = $num;
            return $rt;
        }
        return false;
    }

    //未登录添加到购物车
    private function add_to_cart_no_login($nper_id, $num, $last_num, $max_times = 1000)
    {

        $local_cart = cookie('local_cart');
        $local_cart = is_array($local_cart) ? $local_cart : array();

        if (!empty($local_cart[$nper_id])) {
            $local_cart[$nper_id] = $local_cart[$nper_id] + (int)$num;
        } else {
            $local_cart[$nper_id] = (int)$num;
        }

        $flag = false;//标记购物车是否可以添加
        ($last_num < (int)$local_cart[$nper_id]) && ($local_cart[$nper_id] = $last_num) && ($flag = true);
        (int)$local_cart[$nper_id] > $max_times && ((int)$local_cart[$nper_id] = $max_times) && ($flag = true);

        //检查购物车中是否有失效商品,有则剔除
        $m_pay = new PayModel();
        $local_cart = $m_pay->fiter_useful_cart($local_cart);


        cookie('local_cart', $local_cart);
        $r = cookie('local_cart');

        $count = count($r);
        if ($r) {
            if ($flag) return array("code" => "1", "flag" => "1", "num" => $count);
            return array("code" => "1", "num" => $count);
        }

        return false;
    }

    //检测cookie购物车,把本地购物车的东西添加到登陆用户购物车
    public function add_local_cart_on_line()
    {
        $cart = cookie('local_cart');
        cookie('local_cart', null);
        dump($cart);
    }

    //购物车列表ajax请求
    public function cart_list_div()
    {
        $m_pay = new PayModel();
        $list = null;
        //用户未登录
        if (!is_user_login()) {
            $cart = cookie('local_cart');
            if (!empty($cart)) {
                $ids = '';
                foreach ($cart as $k => $v) {
                    $ids = $ids . ',' . $k;
                }
                $ids = str_implode($ids);
                $list = $m_pay->get_cart_list_by_nper_ids($ids);
                if (!empty($list)) {
                    foreach ($list as $k => $v) {
                        $key = intval($v["nper_id"]);
                        if (!empty($cart[$key])) $list[$k]['num'] = $cart[$key];
                        else $list[$k]['num'] = 0;
                    }
                }
            }


        } else {
            $list = $m_pay->get_cart_list();
        }
        $sum_money = 0;
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $sum_money = $sum_money + (int)$v['num'] * floatval($v['unit_price']);

            }
        }

        $count = count($list);
        $this->assign('sum_money', $sum_money);
        $this->assign('count', $count);
        $this->assign('list', $list);
        //获取购物车商品内容
        $data = array(
            "code" => "1",
            "html" => $this->fetch(),
            "count" => $count,
            "sum_money" => $sum_money,
        );
        die_json($data);
    }

    //通过nper_id删除购物车内的商品
    public function del_cart_by_nper_id()
    {
        $nper_id = remove_xss(I('post.nper_id'));
        $cart_id=remove_xss(I('post.id'));
        (empty($nper_id) || !is_numeric($nper_id)) && die_json("-100");//$nper_id不能为空
        $m_pay = new PayModel();
        $m_pay->del_cart_by_nper_id(!empty($cart_id)?$cart_id:$nper_id) && die_json("1");
        die_json("-1");
    }

    //根据id删除购物车内的商品
    public function del_cart_by_ids()
    {
        $ids = remove_xss(I("post.ids"));
        empty($ids) && die_json("-100");
        $arr = str_to_arr($ids, '|');

        $ids_1 = '';
        foreach ($arr as $k => $v) {
            if (is_numeric($v)) {
                $ids_1 = $ids_1 . ',' . $v;
            }
        }

        $ids_1 = str_implode($ids_1);
        $m_pay = new PayModel();

        $m_pay->del_cart_by_ids($ids_1) && die_json("1");
        die_json("-1");
    }

    //更新购物车的数量
    public function update_cart_num()
    {
        $nper_id = null;
        $num = null;
        $post = I("post.");
        empty($post) && die_json("-99");
        $post = remove_arr_xss($post);
        extract($post);

        ((empty($nper_id) || !is_numeric($nper_id)) || (empty($num) || !is_numeric($num)) || ($num<0)) && die_json("-100");//参数不完整
        //更新购物车信息
        $m_pay = new PayModel();
        $m_pay->update_cart_num($nper_id, $num) && die_json("1");
        die_json("-1");
    }


    private function auth(){
        if(!is_user_login()){
            die('您还没有登录');
        }
    }

    /**下面是需要授权登录后的操作*/
    //生成订单信息
    public function create_order()
    {
        $this->auth();//判断是否登录
        //获取勾选商品的信息
        $ids = remove_xss(I("post.ids"));
        empty($ids) && die_json("-100");//参数不完整无法提交
        $arr = str_to_arr($ids, '|');
      //  $arr=explode('|',ltrim($ids,'|'));
        $ids = '';
        foreach ($arr as $k => $v) {
            if (is_numeric($v)) {
                $ids = $ids . ',' . $v;
            }
        }
        $ids = str_implode($ids);
        empty($ids) && die_json("-110");//ids不能为空

        //根据ids获取购物车内容
       // $m_goods = new GoodsModel();
        $m_pay = new PayModel();
       //$list = $m_goods->get_cart_list_by_ids($ids);
       // empty($list) && die_json("-120");//获取不到您的订单信息应该是已经生成订单
        //格式化为有用的信息
       // $list = $this->format_cart_list($list);
        //获取列表信息
        $cart_list = null;
       // $list && ($cart_list = $m_pay->get_cart_list_info($list));
        $cart_list=$m_pay->get_cart_list($ids);
        empty($cart_list) && die_json("-120");//获取不到您的订单信息应该是已经生成订单
        $sum_price = 0;
        $ids = '';
        //子订单存放数组
        $order_arr = array();
        if ($cart_list) {
            $promote_precent = C('PROMOTE_PRECENT');
            ((int)$promote_precent <= 0 || (int)$promote_precent > 100) && $promote_precent = 0;
            //创建子订单
            $i=0;
            foreach ($cart_list as $k => $v) {

                $unit_price = empty(intval($v['unit_price'])) ? 1 : intval($v['unit_price']);
                $num = (empty(intval($v['num']))||intval($v['num'])<=0) ? 1 : intval($v['num']);

                $unit_sum_price = $unit_price * $num;//子订单总价
                $sum_price = $sum_price + $unit_price * $num;
                $ids = $ids . ',' . $v['nper_id'];

                //子订单信息
                $order_id = create_order_num($i++);
                $order_temp = array(
                    "order_id" => $order_id,
                    "goods_id" => $v['g_id'],//商品id
                    "bus_type" => 'buy',
                    "goods_name" => $v['g_name'],//商品名称
                    "nper_id" => $v['nper_id'],//期数id
                    "num" => $v['num'],//数量
                    "exec_data" => null,//回调执行参数
                    "money" => $unit_sum_price,//子订单总价
                    "promoter_pos" => $promote_precent,//当前推广比例
                    "luck_status" => "false",
                    "join_type"=>$v['join_type']
                );


                /**生成支付回调执行的参数信息START*/
                $exec_data = array(
                    "TYPE" => "BUY",
                    "EXEC_DATA" => array(
                        "VALUE" => (string)$v['num'],
                        "UID" => get_user_id()
                    )
                );
                $token = C('TOKEN_ACCESS');
                //MD5= MD5(订单ID,订单类型,执行数据,密钥)
                $exec_data["MD5"] = md5($order_id . $exec_data["TYPE"] . json_encode($exec_data["EXEC_DATA"]) . $token);
                $order_temp["exec_data"] = json_encode($exec_data);
                /**生成支付回调执行的参数信息END*/

                array_push($order_arr, $order_temp);
            }
        }



        empty($sum_price) && die_json("-130");//订单信息有误,请重新下单
        empty($cart_list) && die_json("-140");//本期已经售空
        //创建订单

        $order_id = create_order_num();
        $cloase_time = C('ORDER_CLOSE_TIME');
        $cloase_time = empty($cloase_time) ? 1800 : $cloase_time;

        $order_parent_data = array(
            "order_id" => $order_id,
            "name" => '用户' . get_user_name() . "的订单:" . $order_id,
            "num" => count($cart_list),
            "bus_type" => 'buy',
            "price" => empty($sum_price) ? "99999" : $sum_price,
            "origin" => empty(I("get.q", '')) ? 'web' : I("get.q", ''),
            "close_time" => NOW_TIME + (int)$cloase_time,
        );

        (empty($order_arr) || empty($order_parent_data)) && die_json("-150");//订单信息有误
        //创建订单
        $p_oid = $m_pay->create_order($order_parent_data, $order_arr);
        if ($p_oid) {
            die_json(array("order_id" => $p_oid, "code" => "1"));
        }
        die_json("-1");
    }

    //格式化购物车列表
    public function format_cart_list($list)
    {
        $this->auth();//判断是否登录

        if (empty($list)) return false;
        $arr = array();
        foreach ($list as $k => $v) {
            $arr[$v['nper_id']] = $v['num'];
        }
        $m_pay = new PayModel();
        return $m_pay->fiter_useful_cart($arr);
    }
    //检测付款状态
    public function check_pay_status()
    {
        $this->auth();//判断是否登录

        $order_id = I("post.order", "");
        empty($order_id) && die_json("-100");
        $m_order = new OrderModel();
        $info = $m_order->get_p_order_info_by_filed("order_id", $order_id);
        empty($info) && die_json("-110");
        in_array($info["pay_status"], array("3")) && die_json("1");
        die_json("-1");
    }
}
