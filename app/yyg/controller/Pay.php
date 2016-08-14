<?php
namespace app\yyg\controller;

use app\core\controller\Common;
use app\pay\model\PublicModel;
use app\core\model\CommonModel;
use app\core\model\GoodsModel;
use app\core\model\OrderModel;
use app\core\model\PayModel;
use app\core\model\UserModel;

class Pay extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->chk_login();
    }

    //判断是否有登录
    private function chk_login()
    {
        if (!is_user_login()) {
            $this->redirect(U("user/login"));
        }
    }

    //根据勾选确认订单信息
    public function info()
    {
        //获取勾选商品的信息
        $ids = remove_xss(I("post.ids"));
        empty($ids) && die_json("-100");
        $arr = str_to_arr($ids, '|');

        $ids = '';
        foreach ($arr as $k => $v) {
            if (is_numeric($v)) {
                $ids = $ids . ',' . $v;
            }
        }
        $ids = str_implode($ids);
        empty($ids) && die_json("-105");//ids不能为空

        //根据ids获取购物车内容
        // $m_goods = new GoodsModel();
        $m_pay = new PayModel();
        //$list = $m_goods->get_cart_list_by_ids($ids);
        //格式化为有用的信息
        //$core_pay = new \app\core\controller\Pay();
        //$list = $core_pay->format_cart_list($list);
        //获取列表信息
        $cart_list = null;
        //$list && ($cart_list = $m_pay->get_cart_list_info($list));
        $cart_list=$m_pay->get_cart_list($ids);
        $sum_price = 0;
        $ids = '';
        if ($cart_list) {
            foreach ($cart_list as $k => $v) {
                if($v['num']<=0){
                    $cart_list[$k]['num']=1;

                    $map['num'] = 1;
                    M("shop_cart")->where(array("id"=>$v['id']))->save($map);
                }

                $unit_price = empty(intval($v['unit_price'])) ? 1 : intval($v['unit_price']);
                $num = empty(intval($v['num'])) ? 1 : intval($v['num']);
                (empty($unit_price) || empty($num)) && die_json("-110");//参数不完整
                $cart_list[$k]['sum_price'] = $unit_price * $num;
                $sum_price = $sum_price + $unit_price * $num;
                $ids = $ids . '|' . $v['id'];
            }
        }

        empty($cart_list) && die_json("-120");//订单已全部失效,刷新购物车
        $this->assign('list', $cart_list);
        $this->assign('sum_price', $sum_price);
        $this->assign('ids', $ids);

        $data = array(
            "code" => "1",
            "html" => $this->fetch()
        );
        die_json($data);
    }



    //显示付款页面
    public function charge()
    {
        $order_id = I("request.id");
        (empty($order_id) || !is_numeric($order_id) || !is_user_login()) && $this->redirect('/');
        //检测订单状态是否正常
        $m_order = new  OrderModel();
        $order_info = $m_order->get_p_order_info_by_filed("order_id", $order_id);
        empty($order_info) && $this->redirect('/');//出错跳转首页

        //订单已支付,跳转到支付成功页面
        in_array($order_info["pay_status"], array("2", "3")) && $this->redirect(U("pay_result") . "?id=" . $order_id);
        //订单关闭时间
        $sec = ((int)$order_info['close_time'] - NOW_TIME) < 0 ? 0 : ((int)$order_info['close_time'] - NOW_TIME);

        //如果订单超时关闭订单
        if (($sec <= 0) && ($order_info['status'] == '1')) {
            $m_order->change_p_order_status_by_filed("order_id", $order_id, array("status" => "-2"));
            $order_info['status'] = '-2';

        }
        //获取用户余额
        $m_user = new UserModel();
        $u_info = $m_user->get_login_user_info();
        $this->assign("user_money", $u_info['money']);
        //使用余额后的价格
        $still_money = (int)$u_info['money'] - (int)$order_info['price'];
        $still_money = $still_money > 0 ? 0 : $still_money;
        //获取配置的支付方式
        $pay_type = M('conf')->where("category = '支付配置'")->select();
        $pay_type = array_column($pay_type,'value','name');
        $this->assign('pay_type',$pay_type);
        //渲染订单信息到网页
        $this->assign("sec", $sec);
        $this->assign("info", $order_info);
        $this->assign("still_money", round(abs($still_money), 2));
//        $this->assign("u_info", $u_info);
        //支付宝接口地址
        $m_com = new CommonModel();
        $alipay_url = U('pay/alipay/index');
        $timestamp = NOW_TIME;
        $this->assign("timestamp", $timestamp);

        $key = C("TOKEN_ACCESS");
        $sign = md5($order_id . $timestamp . $key);
        $this->assign("sign", $sign);

        $this->assign("alipay_url", $alipay_url);
        return $this->fetch();
    }

    //成功支付,显示成功失败的购买信息
    public function pay_result($id=null)
    {
        $order_id = empty(I("request.id",$id,'trim')) ? I("request.out_trade_no", "") : I("request.id",$id,'trim');

        //爱贝回调
        $transdata =json_decode(I('request.transdata',''),true);
        if(!empty($transdata['cporderid'])){
            $order_id = $transdata['cporderid'];
        }

        (empty($order_id) || !is_numeric($order_id)) && die("跳转到首页1") && $this->redirect('/');

        //检测登录状态是否有改变
        $m_order = new  OrderModel();
        $check_res = $m_order->check_order($order_id,session('user.id'));
        if(!$check_res){
            $this->redirect(U("user/login"));
        }
        //检测订单状态是否正常

        $order_info = $m_order->get_p_order_info_by_filed("order_id", $order_id);
        empty($order_info) && die("跳转到首页2") && $this->redirect('/');//出错跳转首页
        //检测是否为充值后付款

        if ($order_info["bus_type"] == 'recharge') {

            //获取充值后的信息
            $c_order = $m_order->get_c_order_info_by_filed("pid", $order_info["id"]);
            $exec = empty($c_order['exec_data']) ? "" : json_decode($c_order['exec_data'], true);
            $next_order = empty($exec['EXEC_DATA']['NEXT_ORDER']) ? "" : $exec['EXEC_DATA']['NEXT_ORDER'];
            if (!empty($next_order)) {
                $this->redirect(U('pay_result') . '?id=' . $next_order);
            }
        }

        //如果订单支付不成功
        !in_array($order_info["pay_status"], array("2", "3")) && die("订单支付不成功,跳转到首页") && $this->redirect('/');//出错跳转首页

        //根据父级id获取子订单详情
        $order_list = $m_order->get_order_list_by_pid($order_info["id"]);

        $this->assign("info", $order_info);
        $this->assign("list", $order_list);

        switch ($order_info['bus_type']) {
            case "buy":
                return $this->fetch();
                break;
            case "recharge":
                $this->assign("add_money", $order_info["price"]);
                return $this->fetch("recharge_result");
                break;
            default:
                return $this->fetch();
        }
    }



    //充值
    public function recharge()
    {
        //获取充值方式配置
        $pay_type = M('conf')->where("category = '支付配置'")->select();
        $pay_type = array_column($pay_type,'value','name');
        $this->assign('pay_type',$pay_type);
        return $this->fetch();
    }

    //充值提交
    public function recharge_do()
    {
        $money = I("request.money", '100');
        $pay_type = I("request.pay_type", 'alipay','trim,strtolower');
        empty($money) && show_result("-100", '金额不能为空');
//        $m_com = new PublicModel();
        $promote_precent = C("PROMOTE_PRECENT");
        $cloase_time = C('ORDER_CLOSE_TIME');
        //创建支付父级订单
        $p_order_id = create_order_num();
        $order_parent_data = array(
            "order_id" => $p_order_id,
            "name" => '用户' . get_user_name() . '充值' . $money . '元',//商品名称
            "num" => 1,
            "bus_type" => 'recharge',
            "price" => empty($money) ? "100" : $money,
            "origin" => empty(I("get.q", '')) ? 'web' : I("get.q", ''),
            "close_time" => NOW_TIME + (int)$cloase_time,
            "plat_form" =>$pay_type
        );
        $order_arr = array();

        //子订单信息
        $order_id = create_order_num();
        $order_temp = array(
            "order_id" => $order_id,
            "goods_id" => 'recharge',//商品id
            "goods_name" => '用户' . get_user_name() . '充值' . $money . '元',//商品名称
            "nper_id" => null,//期数id
            "bus_type" => 'recharge',
            "num" => 1,//数量
            "exec_data" => null,//回调执行参数
            "money" => $money,//子订单总价
            "promoter_pos" => $promote_precent,//当前推广比例
            "luck_status" => "false"
        );
        /**生成支付回调执行的参数信息START*/
        $exec_data = array(
            "TYPE" => "CHARGE",
            "EXEC_DATA" => array(
                "VALUE" => $money
            )
        );

        $token = C('TOKEN_ACCESS');
        //MD5= MD5(订单ID,订单类型,执行数据,密钥)
        $exec_data["MD5"] = md5($order_id . $exec_data["TYPE"] . json_encode($exec_data["EXEC_DATA"]) . $token);
        $order_temp["exec_data"] = json_encode($exec_data);
        /**生成支付回调执行的参数信息END*/

        array_push($order_arr, $order_temp);

        $m_pay = new PayModel();

        //创建订单
        $m_pay->create_order($order_parent_data, $order_arr);

        //支付宝接口地址
//        $m_com = new CommonModel();

        switch ($pay_type) {
            case "alipay":
                $pay_url = U('pay/alipay/index');
                break;
            case "wxsm":
            	$pay_url = U('pay/wx/index').'?t=1&order_id='. $order_id;
           //  $control->redirect(U('wx/index') . "?t=1&order_id=" . $order_id );
                	break;
            case "wxpay":
                $pay_url = U('pay/wxpay/index') ;
                break;
            case "bee-alipay":
                $pay_url = U('pay/bee/ali_web') ;
                break;
            case "bee-wechat":
                $pay_url = U('pay/bee/wx_native') ;
                break;
            case "aipay":
                $pay_url = U('pay/aipay/index') ;
                break;
            default:
                $pay_url = U('pay/alipay/index');
        }

        $timestamp = NOW_TIME;
        $this->assign("timestamp", $timestamp);
        $key = C('TOKEN_ACCESS');
        $sign = md5($p_order_id . $timestamp . $key);
        $this->assign("pay_url", $pay_url);
        $this->assign("sign", $sign);
        $this->assign("order", $p_order_id);

        return $this->fetch();
    }

    //微信扫码页面
    public function charge_wxscan($code)
    {
        list($url,$order_id,$fee)=unserialize(base64_decode($code));
        $m_order = new OrderModel();
        $order_info = $m_order->get_p_order_info_by_filed("order_id", $order_id);
        $this->assign('code_url',$url);
        $this->assign('order_id',$order_id);
        $this->assign('total_fee',$fee*0.01);
        return $this->fetch();
    }
    //购物车页面
    public function cart()
    {
        //获取用户余额
        $m_user = new UserModel();
        $u_info = $m_user->get_login_user_info();
        empty($u_info) && $this->redirect('/');

        //获取当前购物车的列表内容
        $m_pay = new PayModel();
        $list = $m_pay->get_cart_list();
        //删掉无用的选项
//        $core_pay = new \app\core\controller\Pay();
//        $list && ($list = $core_pay->format_cart_list($list));
        //dump($list);
        //获取列表信息
//        $cart_list = null;
//        $list && ($cart_list = $m_pay->get_cart_list_info($list));

        $list && $this->assign('list', $list);
//        dump($cart_list);

        $this->assign('money', (int)$u_info['money']);
        return $this->fetch();
    }

    //显示随机商品
    public function show_rand_goods()
    {
        //获取随机推荐商品
        $groom_list = $this->rand_echo_nper_list();
        empty($groom_list) && die_json("-1");//获取失败
        $this->assign('groom_list', $groom_list);
        $data = array(
            "code" => "1",
            "html" => $this->fetch()
        );
        die_json($data);
    }
    //随机输出推荐商品
    private function rand_echo_nper_list($num = 5)
    {
        if (!is_numeric($num)) return false;
        $m_pay = new PayModel();
        $list = $m_pay->get_nper_list_ings_limit($num);
        shuffle($list);
        return $list;
    }

    public function settlement(){
        //获取用户余额
        $m_user = new UserModel();
        $u_info = $m_user->get_login_user_info();
        empty($u_info) && $this->redirect('/');

        //获取当前购物车的列表内容
        $m_pay = new PayModel();
        $list = $m_pay->get_cart_list();
        //删掉无用的选项
//        $core_pay = new \app\core\controller\Pay();
//        $list && ($list = $core_pay->format_cart_list($list));
        //dump($list);
        //获取列表信息
//        $cart_list = null;
//        $list && ($cart_list = $m_pay->get_cart_list_info($list));
        $list && $this->assign('list', $list);
//        dump($cart_list);

        $this->assign('money', (int)$u_info['money']);
        return $this->fetch();

    }


}
