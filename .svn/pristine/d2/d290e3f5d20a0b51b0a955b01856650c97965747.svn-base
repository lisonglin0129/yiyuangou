<?php
namespace app\core\controller;

use app\admin\model\RtRegularModel;
use app\core\model\CommonModel;
use app\core\model\RtModel;

/*** Rt模拟*/
Class Rt extends Common
{
    private $m_rt;
    private $m_common;

    public function __construct()
    {
        parent::__construct();
        $this->m_rt = new RtModel();
        $this->m_common = new CommonModel();
    }


    private function auth()
    {

    }

    /**
     *机器人模拟购买接口
     */
    public function rt_buy()
    {
        $uid = $goods_id = $num = null;

        $this->auth();
        $post = I("request.", "");
//        dump($post);
        !is_array($post) && die_result("-110", "rt:参数错误");
        extract($post);
        (empty($uid) || empty($goods_id) || empty($num)) && die_result("-120", "rt:参数错误1");
        //获取用户信息
        $u_info = $this->m_rt->get_user_info($uid);
        empty($u_info) && die_result("-130", "rt:用户信息获取失败");
        //限定用户组
        $u_info["type"] !== "-1" && die_result("-140", "用户组不正确");


        //根据商品id获取最新期数id
        $r = $this->m_rt->get_nper_id_by_goods($goods_id);
        $nper_id = $r['id'];

        empty($nper_id) && die_result("-150", "rt:获取商品期数失败");

        //获取期数信息
        $n_info = $this->m_rt->get_nper_info($nper_id);
        empty($n_info) && die_result("-150", "rt:期数信息获取失败");

        //校验商品数量
        $last_num = intval($n_info["sum_times"] - $n_info["participant_num"]);
        if ($last_num == 0) {
            die_result("-160", "商品数量不足");
        } else if ($num > $last_num) {
            $num = $last_num;
        }

        //获取商品信息
        $g_info = $this->m_rt->get_goods_info_by_id($n_info["pid"]);
        empty($g_info) && die_result("-170", "商品信息获取失败");

        //生成订单信息
        $price = floatval($n_info) * floatval($num);
        (!is_numeric($price) || $price <= 0) && die_result("-180", "订单金额不正确");

        $r = $this->create_order($u_info, $n_info, $g_info, $num, $price);
        ($r["code"] !== "1") && die_result("-190", "订单创建失败");
        $order_id = $r["value"];

        //充值余额
        $this->m_rt->add_money($u_info["id"], $price);
        //调用余额支付接口,显示调用结果
        $timestamp = NOW_TIME;
        $key = C("TOKEN_ACCESS");
        $sign = md5($order_id . $timestamp . $key);

        $r = R('pay/notify/balance_flow_p',"timestamp=" . $timestamp . "&order_id=" . $order_id . "&sign=" . $sign);
        $r = json_decode($r,true);
        $r['nper_id'] = $nper_id;
        die_json($r);
    }

    /**
     *机器人模拟购买接口
     * 远程调用
     */
    public function rt_buy_p($uid=null,$goods_id=null,$num=null,$join_type=0)
    {
        //$uid = $goods_id = $num = null;

        //C('default_return_type','json');
        $this->auth();
        $post = I("request.", "");
//        dump($post);
//        !is_array($post) && die_result("-110", "rt:参数错误");
//        extract($post);
        if(empty($uid) || empty($goods_id) || empty($num)) return ["-120", "rt:参数错误1"];
        //获取用户信息
        $u_info = $this->m_rt->get_user_info($uid);
        if(empty($u_info)) return ["-130", "rt:用户信息获取失败"];
        //限定用户组
        if($u_info["type"] !== "-1")return ["-140", "用户组不正确"];

        //1元夺宝
        if($join_type==0){
            //根据商品id获取最新期数id
            $r = $this->m_rt->get_nper_id_by_goods($goods_id,1);
            $nper_id = $r['id'];

            if(empty($nper_id)) return ["-150", "rt:获取商品期数失败"];

            //获取期数信息
            $n_info = $this->m_rt->get_nper_info($nper_id);
            if(empty($n_info))return ["-150", "rt:期数信息获取失败"];

            //校验商品数量
            $last_num = intval($n_info["sum_times"] - $n_info["participant_num"]);
            if ($last_num == 0) {
                return ["-160", "商品数量不足"];
            } else if ($num > $last_num) {
                $num = $last_num;
            }

            //获取商品信息
            $g_info = $this->m_rt->get_goods_info_by_id($n_info["pid"]);
            if(empty($g_info))return ["-170", "商品信息获取失败"];

            //生成订单信息
            $price = floatval($n_info) * floatval($num);
            if((!is_numeric($price) || $price <= 0))return ["-180", "订单金额不正确"];

            $r = $this->create_order($u_info, $n_info, $g_info, $num, $price);
            if(($r["code"] !== "1"))return ["-190", "订单创建失败"];
            $order_id = $r["value"];

            //充值余额
            $this->m_rt->add_money($u_info["id"], $price);
            //调用余额支付接口,显示调用结果
            $timestamp = NOW_TIME;
            $key = C("TOKEN_ACCESS");
            $sign = md5($order_id . $timestamp . $key);
//        $url = C("ORDER_TRIGGER_API") . "?timestamp=" . $timestamp . "&order_id=" . $order_id . "&sign=" . $sign;
            $r = R('pay/notify/balance_flow_p',"timestamp=" . $timestamp . "&order_id=" . $order_id . "&sign=" . $sign);
            $r['nper_id'] = $nper_id;
        }elseif($join_type==1 || $join_type==2){
            $is_odd = $join_type==1;
            //根据商品id获取最新期数信息
            $n_info = $this->m_rt->get_nper_id_by_goods($goods_id,2);
            $nper_id = $n_info['id'];

            if(empty($n_info))return ["-150", "rt:期数信息获取失败"];

            //校验商品数量
            $last_num = intval($n_info["sum_times"] - ($is_odd?$n_info["odd_join_num"]:$n_info["even_join_num"]));
            if ($last_num == 0) {
                return ["-160", "商品数量不足"];
            } else if ($num > $last_num) {
                $num = $last_num;
            }

            //获取商品信息
            $g_info = $this->m_rt->get_goods_info_by_id($n_info["pid"]);
            if(empty($g_info))return ["-170", "商品信息获取失败"];

            //生成订单信息
            $price = floatval($n_info) * floatval($num);
            if((!is_numeric($price) || $price <= 0))return ["-180", "订单金额不正确"];

            $r = $this->create_order($u_info, $n_info, $g_info, $num, $price,$join_type);
            if(($r["code"] !== "1"))return ["-190", "订单创建失败"];
            $order_id = $r["value"];

            //充值余额
            $this->m_rt->add_money($u_info["id"], $price);
            //调用余额支付接口,显示调用结果
            $timestamp = NOW_TIME;
            $key = C("TOKEN_ACCESS");
            $sign = md5($order_id . $timestamp . $key);
//        $url = C("ORDER_TRIGGER_API") . "?timestamp=" . $timestamp . "&order_id=" . $order_id . "&sign=" . $sign;
            $r = R('pay/notify/balance_flow_p',"timestamp=" . $timestamp . "&order_id=" . $order_id . "&sign=" . $sign);
            $r['nper_id'] = $nper_id;
        }
        return $r;
    }

    /**
     *创建订单
     */
    private function create_order($u_info = null, $n_info = null, $g_info = null, $num = 0, $price = 999,$join_type=0)
    {

        $uid = $u_info["id"];
        $username = $u_info["username"];

        //子订单信息
        $order_id = create_order_num();
        $order_child = array(
            "order_id" => $order_id,
            "goods_id" => $g_info['id'],        //商品id
            "bus_type" => 'buy',
            "uid" => $uid,
            "username" => $username,
            "goods_name" => $g_info['name'],    //商品名称
            "nper_id" => $n_info['id'],         //期数id
            "num" => $num,//数量
            "exec_data" => null,                //回调执行参数
            "money" => $price,                  //子订单总价
            "promoter_pos" => 0,                //当前推广比例
            "luck_status" => "false",
            'join_type'=>$join_type
        );


        /**生成支付回调执行的参数信息START*/
        $exec_data = array(
            "TYPE" => "BUY",
            "EXEC_DATA" => array(
                "VALUE" => $num,
                "UID" => $uid
            )
        );

        $token = C('TOKEN_ACCESS');
        //MD5= MD5(订单ID,订单类型,执行数据,密钥)
        $exec_data["MD5"] = md5($order_id . $exec_data["TYPE"] . json_encode($exec_data["EXEC_DATA"]) . $token);
        $order_child["exec_data"] = json_encode($exec_data);
        /**生成支付回调执行的参数信息END*/


        empty($price) && die_result("-200", "总价信息有误,请重新下单");

        //创建订单父级
        $order_id = create_order_num();
        $close_time = C('ORDER_CLOSE_TIME');
        $close_time = empty($close_time) ? 1800 : $close_time;

        $order_parent_data = array(
            "order_id" => $order_id,
            "name" => '用户' . $username . "的订单:" . $order_id,
            "num" => 1,
            "uid" => $uid,
            "username" => $username,
            "bus_type" => 'buy',
            "price" => empty($price) ? "99999" : $price,
            "origin" => empty(I("get.q", '')) ? 'web' : I("get.q", ''),
            "close_time" => NOW_TIME + (int)$close_time,
        );

        (empty($order_child) || empty($order_parent_data)) && die_result("-140", "订单信息有误");
        //创建订单
        $p_oid = $this->m_rt->create_order($order_parent_data, $order_child);
        if ($p_oid) {
            return rt("1", $order_id);
        }
        return rt("-1", $order_id);
    }

    //脚本执行
    public function auto_exec() {
        ignore_user_abort();
        set_time_limit(100);
        header("Content-type: text/html; charset=utf-8");
        $m = new RtRegularModel();
        $hour = date('H');
        $task_list = $m->get_exec_data($hour);
        $step = 0;
        $count = 0;
        foreach ( $task_list as $task ) {
            //判断是否达到脚本执行时间,购买次数是否符合要求
            $time_condition = $this->get_time_condition($task['exec_time']);
            if( $time_condition || ($task['exec_record_times'] >= $task['exec_times'] && $task['exec_times'] != -1 )) {
                $step +=1;
                continue;
            }

            $rt = $m->get_rand_rt();
            $rt_uid = $rt[0]['id'];
            //执行购买任务
            //$message = $this->rt_buy_goods($task['gid'],$rt_uid,$task['buy_times']);
            if($task['join_type']==3){
                $join_type = rand(0,2);
            }else if($task['join_type']==2){
                $join_type = rand(1,2);
            }else{
                $join_type = 0;
            }
            $message = [true,$this->rt_buy_p($rt_uid,$task['gid'],$task['buy_times'],$join_type)];
            $res = $message[0];
            $m->buy_log($rt,$message,$task['buy_times']);
            //检测是否开启作弊
            if ( $task['cogged'] == 1 ) {
                $m->open_cogged($task['gid']);
            }

            if ( $res ) {
                $m->init_data($task);
                $count++;
                echo "机器人({$rt[0]['nick_name']})购买成功<br>";
                //执行三次终止执行(缓解服务器压力不能全部都执行购买任务)
                if ($count == 3) {
                    break;
                }
            }


        }
        if ( $step == count($task_list) ) {
            echo '任务未执行:当前时间无任务列表';
        }

    }

    //获取时间判断
    private function get_time_condition($exec_time) {
        return (int) $exec_time > (int) time();

    }

    //模拟购买商品
    private function rt_buy_goods($goods_id,$uid,$num){
        //$m_conf = M('conf');
        //$api = $m_conf->where(['name'=>'RT_BUY_API'])->getField('value');
        //$api = 'http://www.yiyuangou.local/core/rt/rt_buy?';
        $param = "goods_id={$goods_id}&uid={$uid}&num={$num}";

        $r=R('core/rt/rt_buy_p',$param);
        return [true,$r];
        //var_dump($r);
        //die();

        $ch = curl_init($api.$param);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $ch_result = curl_exec($ch);
        $curl_error = curl_error($ch);
        if($curl_error==''){
            $json_result = json_decode($ch_result,true);
            if(json_last_error()==JSON_ERROR_NONE){
                return [true,$json_result];
            }else{
                return [false,['code'=>-1,'msg'=>$ch_result]];
            }
        }else{
            return [false,['code'=>-2,'msg'=>$curl_error]];
        }
    }



}