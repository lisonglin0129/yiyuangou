<?php
namespace app\pay\model;


use app\core\controller\Gdfc;
use app\core\model\LogModel as core_log;
use app\core\model\UserModel;
use think\Exception;
use think\model\Adv;

Class ExecModel extends Adv
{
    private $log;
    private $m;
    private $charge_log;

    public function __construct()
    {
        parent::__construct();
        $this->log = new LogModel();
        $this->charge_log = new core_log();
        $this->m = $this->db(1);

    }

    /**
     * 减去用户金额
     * @param int $uid 用户id
     * @param float $money 减去的金额
     * @param string $order_id 订单id
     * @return bool
     */
    public function reduce_money($uid = 0, $money = 0.00, $order_id = '')
    {
        $this->m->startTrans();
        //查询用户支付前的余额
        $r_0 = $this->m->table('sp_users')->where(array("id" => $uid))->field(array("money","score","cash,id,username"))->find();
        $user_money = floatval($r_0['money']);
        if ($user_money < $money) return false;
        //用户付款减余额
        $r_1 = $this->m->table('sp_users')->where(array("id" => $uid))->setDec("money", $money);

        //校验余额
        $r_2 = $this->m->table('sp_users')->where(array("id" => $uid))->field(array("money","score","cash"))->find();
        $last_money = floatval($r_2['money']);
        //和付款前校验余额
        $check = (round(floatval($last_money + $money), 2) === round(floatval($user_money), 2));

        //校验余额是否为负数
        $check_2 = (floatval($last_money) >= 0);
        //用户减去金额成功,并且付费校验成功,提交

        if ($check && $check_2 && $r_1) {
            //更新用户余额信息
            $this->m->commit();
            //记录用户扣款成功日志
            $this->log->balance_log('reduce', $money, "成功:扣除用户余额", $uid, $order_id);
            $this->charge_log->record($r_0,$r_2,2,1);

            return true;
        } else {
            $this->m->rollback();
            //记录用户扣款失败日志
            $this->log->balance_log('default', $money, "失败:扣除用户余额", $uid, $order_id);
            $this->charge_log->record($r_0,$r_2,2,0);
            return false;
        }


    }

    /**
     * 增加用户金额
     * @param int $uid 用户id
     * @param float $money 减去的金额
     * @param string $order_id 订单id
     * @return bool
     */
    public function add_money($uid = 0, $money = 0.00, $order_id = '')
    {
        $this->m->startTrans();

        //查询用户支付前的余额
        $r_0 = $this->m->table('sp_users')->where(array("id" => $uid))->field(array("money","score","cash","id","username"))->find();
        $user_money = floatval($r_0['money']);
        //用户加余额
        $r_1 = $this->m->table('sp_users')->where(array("id" => $uid))->setInc("money", $money);

        //校验余额
        $r_2 = $this->m->table('sp_users')->where(array("id" => $uid))->field(array("money","score","cash"))->find();
        $last_money = floatval($r_2['money']);
        //和付款前校验余额
        $m1 = round($last_money - $money, 2);
        $m2 = round($user_money, 2);

        $check = ($m1 == $m2);

        if ($check && $r_1) {
            $this->m->table('sp_order_list_parent')->where(array("order_id" => $order_id))->save(array("pay_time" => (string)microtime_float()));
            //更新用户余额信息
            $this->m->commit();
            //记录用户扣款成功日志
            $this->log->balance_log('add', $money, "成功:增加用户余额", $uid, $order_id);
            $this->charge_log->record($r_0,$r_2,1,1);
            return true;
        } else {
            $this->m->rollback();
            //记录用户扣款失败日志
            $this->log->balance_log('default', $money, "失败:增加用户余额", $uid, $order_id);
            $this->charge_log->record($r_0,$r_2,1,0);
            return false;
        }
    }

    private function add_money_direct($uid = 0, $money = 0.00){
        $m_user = M('users', 'sp_');
        return $m_user->where(array("id" => $uid))->setInc("money", $money);
    }

    /**
     * 开通执行主流程,任何地方出错,返回错误码并回滚
     * @param int $o_id 用户主订单
     * @return bool
     */
    public function open_main($o_id)
    {
        $this->m->startTrans();
        //获取用户子订单列表
        $c_list = $this->m->table("sp_order_list")->where(array("pid" => $o_id))->select();
        if (empty($c_list)) return rt("-100", "", "用户子订单不能为空");

        //遍历子订单,执行业务操作
        foreach ($c_list as $k => $v) {
            /**更新子订单的业务状态S*/
            $data = array(
                "pay_time" => (string)microtime_float(),
                "pay_time_format" => microtime_format(microtime_float(), 2, 'is'),
            );
            $r_0 = $this->m->table("sp_order_list")->where(array("id" => $v["id"]))->save($data);
            /*标记失败*/
            if ($r_0 === false) {
                $this->m->rollback();
                return rt("-110", "", "用户子订单标记支付幸运时间失败");
            }
            /**更新子订单的业务状态E*/

            //用户执行数据
            $exec_data = json_decode($v['exec_data'], true);
            if (!is_array($exec_data)) {
                $this->m->rollback();
                return rt("-120", "", "用户执行数据错误");
            }

            //判断用户执行数据类型
            $type = $exec_data["TYPE"];
            //签名
            $md5 = strtolower($exec_data["MD5"]);
            //全局加密码
            $m_com = new PublicModel();
            $token = C('TOKEN_ACCESS');
            $exec_data_sign = json_encode($exec_data["EXEC_DATA"]);//执行数据
            $sign = md5($v['order_id'] . $type . $exec_data_sign . $token);
            //校验签名
            if ($md5 !== $sign) {
                $this->m->rollback();
                return rt("-130", "", "订单执行数据签名错误");
            };

            /**下面开始执行幸运号码分配流程*/
            $exec_data = json_decode($exec_data_sign, true);
            // $num = $exec_data["VALUE"];
            $num = $v['num'];
            $nper_id = $v['nper_id'];
            $goods_id = $v['goods_id'];
            $order_id = $v['order_id'];
            $uid = $v['uid'];
            $username = $v['username'];
            $money = $v['money'];
            $index_start = $v['index_start'];
            $index_end = $v['index_end'];
            $join_type = $v['join_type'];

            /*校验*/
            if (empty($num) || empty($nper_id) || empty($order_id) || empty($uid) || empty($money)) {
                $this->m->rollback();
                if (empty($num)) return rt("-140", "", "订单数量不能为空");
                if (empty($nper_id)) return rt("-150", "", "期数信息不能为空");
                if (empty($order_id)) return rt("-160", "", "订单号码不能为空");
                if (empty($uid)) return rt("-170", "", "用户id不能为空");
                if (empty($money)) return rt("-180", "", "金额不能为空");
            }

            //期数信息
            $n_info = $this->m->table('sp_nper_list')->where(array("id" => $nper_id))->find();
            if (empty($n_info['shuffle_name'])) {
                $this->m->rollback();
                return rt("-190", "", "期数信息不能为空");
            }

            $shuffle_name = $n_info['shuffle_name'];

            //构造幸运数字分配参数
            $param = array(
                "shuffle_name" => $shuffle_name,
                "index_start" => $index_start,
                "index_end" => $index_end,
                "nper_id" => $nper_id,
                "order_id" => $order_id,
                "goods_id" => $goods_id,
                "uid" => $uid,
                "username" => $username,
                "num" => $num,
                "join_type" => $join_type
            );

            /**分配幸运数字*/
            try {
                $r_x = $this->pop_lottery_num($param);
                if (!is_array($r_x)) {
                    $this->m->rollback();
                    return $r_x;//分配幸运数字返回值错误
                }
                //写入订单成功失败次数
                switch ($r_x['code']) {
                    case "1":

                        $data = array(
                            "success_num" => $r_x["succ_num"],
                            "failed_num" => $r_x["err_num"],
                            "result" => ($r_x["err_num"] == 0) ? "true" : "false"
                        );
                        break;
                    case "-340":
                        $r_x["err_num"]=1;//标记错误数量>1为了下面的退款
                        log_w('此处是-340的触发了');
                        $data = array(
                            "success_num" => 0,
                            "failed_num" => $num,
                            "result" => "false"
                        );
                        break;
                    default:
                        $this->m->rollback();
                        return $r_x;//分配幸运数字返回代码错误
                }
                log_w("调试:" . json_encode($param));

            } catch (Exception $e) {

                //运行错误金额全部退还
                $r_x["err_num"]=1;//标记错误数量>1为了下面的退款
                $data = array(
                    "success_num" => 0,
                    "failed_num" => $num,
                    "result" => "false"
                );
                log_w('此处是出错catch的触发了金额全部退还');
            }

            $r_1 = true;

            $r_2 = $this->m->table('sp_order_list')->where(array("order_id" => $order_id))->save($data);
            $r_2 = ($r_2 !== false);
            /**失败了就退钱*/
            if ($data["failed_num"] > 0) {
                //检测当前订单是否已经有退款
                $back_info = $this->m->table('log_balance')->where(array("order_id" => $order_id, "type" => "add"))->find();
                log_w(json_encode($back_info));
                //有退款信息,回去
                if (!empty($back_info)) {
                    $this->m->rollback();
                    return rt("-200", "", "有退款信息_退款失败回滚");
                }
                //计算退款价格
                $back_money = $data["failed_num"] * $n_info["unit_price"];
                $back_money = round($back_money,2);

                if ($back_money <= $money) {
                    //直接加钱
                    $this->add_money($uid, $back_money, $order_id);
                } else {
                    $this->m->rollback();
                    return rt("-210", "", "用户加款失败");
                }
            }


            //检测是否满员触发开奖

            if ($r_0 && $r_1 && $r_2) {
                //写入订单状态3
                $r_0 = $this->set_order_status($o_id, '3', '2', true);
                if ($r_0 !== false) {
                    $p_info = $this->get_p_info_by_id($o_id);
                    if ($p_info["pay_status"] != '3') {
                        $this->m->rollback();
                        return array("code" => "-405", "msg" => "设置订单为3失败");
                    }
                }
                //检测是否开奖
                $m_gdfc = new Gdfc();
                //需要重写开奖
                if (empty($join_type)) {
                    //非0元购开奖
                    $m_gdfc->gateway($nper_id);
                } else {
                    $m_gdfc->gateway($nper_id);
                    //0元购开奖
                }
            } else {
                $this->m->rollback();
                return rt("-220", "", "执行开通用户业务失败");
            }
        }

        $this->m->commit();
        return rt("1", "", "执行开通用户业务成功");
    }

    /**分配幸运号码*/
    private function pop_lottery_num($post)
    {

        $join_type = null;
        $this->m->startTrans();
        extract($post);
        if (empty($shuffle_name)) return rt("-230", "", "随机数字表名字为空");
        if (empty($goods_id) || !is_numeric($goods_id)) return rt("-240", "", "商品id不能为空");
        if (empty($nper_id) || !is_numeric($nper_id)) return rt("-250", "", "期数id不能为空");
        if (empty($order_id) || !is_numeric($order_id)) return rt("-260", "", "订单不能为空");
        if (empty($uid) || !is_numeric($uid)) return rt("-270", "", "用户id不能为空");
        if (empty($username)) return rt("-280", "", "用户名不能为空");
        if (empty($num) || !is_numeric($num)) return rt("-290", "", "数量不能为空");

        $join_type_sql = empty($join_type) ? ' (join_type= 0 OR join_type= null)' : ' join_type=' . $join_type;

        //取上一个最大的数字位置,用于分配该用户的号码位置
        $sql = 'SELECT MAX(index_end) num FROM sp_order_list WHERE  ' . $join_type_sql . ' AND  nper_id = ' . $nper_id . ' for update';
        $big_num = $this->m->lock(true)->query($sql);
        $big_num = $big_num[0]["num"];


        //检查存放幸运数字的分组表是否存在
        $r_0 = $this->m->query("SHOW TABLES LIKE '" . $shuffle_name . "'");
        if (empty($r_0)) {
            $this->m->rollback();
            return rt("-300", "", "存放幸运数字的分组不存在");
        }

        $order_info = $this->m->table("sp_order_list")->where(array("order_id" => $order_id))->field(array("dealed"))->find();


        $deal = $order_info['dealed'];

        if (!($deal === "false") || isset($index_end)) {
            $this->m->rollback();
            return rt("-310", "", "已经分配过幸运数字");
        }
        if (!isset($big_num)) {
            $index_start = 0;
        } else {
            $index_start = intval($big_num) + 1;
        }
        $index_end = $index_start + $num - 1;
        //更新坐标到订单表
        $r_1 = $this->m->table("sp_order_list")->where("ISNULL(index_start) AND ISNULL(index_end) AND  order_id=" . $order_id)->save(array("index_start" => $index_start, "index_end" => $index_end));
        $r_1 = $r_1 !== false;
        if (!$r_1) {
            $this->m->rollback();
            return rt("-320", "", "更新坐标到订单表失败");
        }


        //根据index_start和index_end计算需要用到的表
        $split_code = empty(__SPLIT_CODE_NUM__) ? 3000 : __SPLIT_CODE_NUM__;

        $arr_1 = get_cid_by_num($index_start, $split_code);
        $cid_1 = $arr_1[0];//首行行数
        $position_1 = $arr_1[1];//首行坐标
        $arr_2 = get_cid_by_num($index_end, $split_code);
        $cid_2 = $arr_2[0];//尾行行数
        $position_2 = $arr_2[1];//尾行坐标
        //第一行的位置
        $head = $this->m->table($shuffle_name)->where(array("nper_id" => $nper_id, "cid" => $cid_1))->find();

        //该行的数组
        $arr_head = json_decode($head['code_list'], true);
        if (empty($arr_head)) {
            $this->m->rollback();
            return rt("-330", "", "获取首行幸运数字列表失败");
        }


        //如果存在大于1行
        if ($cid_2 > $cid_1) {
            //首行最后一位的坐标
            $end_pos = $head['len'];
            //第一行数组
            $arr_head = array_splice($arr_head, $position_1, ($end_pos - $position_1));
            log_w(json_encode($arr_head));
            if (empty($arr_head)) {
                $this->m->rollback();
                return rt("-340", "", "获取第一行数组失败");
            }

            /**中间的N行*/
            $arr_middle = array();

            //如果存在中间N行
            if (($cid_2 - $cid_1) > 1) {

                for ($i = ($cid_1 + 1); $i < $cid_2; $i++) {
                    $middle = $this->m->table($shuffle_name)->where(array("nper_id" => $nper_id, "cid" => $i))->find();

                    $middle = json_decode($middle["code_list"], true);
                    if (empty($middle)) {
                        $this->m->rollback();

                        return rt("-350", "", "获取中间行数组失败");
                    }
                    $arr_middle = array_merge($arr_middle, $middle);
                }

            }
            /**最后一行*/
            $end = $this->m->table($shuffle_name)->where(array("nper_id" => $nper_id, "cid" => $cid_2))->find();
            //该行的数组
            $arr_end = json_decode($end['code_list'], true);
            if (empty($arr_end)) {
                $this->m->rollback();
                return rt("-360", "", "获取尾行数组失败");
            }

            //截取尾行数组(3)
            $arr_end = array_splice($arr_end, 0, $position_2 + 1);

            /**合并数组,此处是用户的全部幸运号码*/
            $arr_sum = array_merge($arr_head, $arr_middle, $arr_end);

        } //如果 都在一行,则取出开始位置往后N位的数字
        else if ($cid_2 == $cid_1) {
            $start = $position_1;
            $len = $position_2 - $position_1 + 1;
            $arr_sum = array_splice($arr_head, $start, $len);
        } //出错
        else {
            $this->m->rollback();
            return rt("-365", "", "幸运数字库首行和尾行的位置数据错误");
        }
        $arr_sum = array_filter($arr_sum, 'is_empty_but_zero');//去空
        $arr_sum = array_unique($arr_sum);//去重
        $arr_sum_num = count($arr_sum);//总数


        if (empty($arr_sum)) {
            log_w("用户全部幸运号码为空");
            $this->m->rollback();
            return array(
                "err_num" => $num,
                "succ_num" => 0,
                "code" => "1",
                "order_id" => $order_id
            );

//            return rt("-370","","用户全部幸运号码为空");
        }

        $err_num = 0;
        $succ_num = $arr_sum_num;

        if ($arr_sum_num < $num) {
            $err_num = $num - $arr_sum_num;//失败数量
            $succ_num = $arr_sum_num;//成功数量
        }


        $split_num = ((int)$split_code < 3000 || (int)$split_code > 10000) ? 3000 : $split_code;
        $arr_sum = array_chunk($arr_sum, $split_num);//默认按3000条一个字段分组存储
        $luck_num_arr = array();


        foreach ($arr_sum as $k => $v) {
            $tmp = array(
                "uid" => $uid,
                "status" => "false",
                "username" => $username,
                "nper_id" => $nper_id,
                "order_id" => $order_id,
                "goods_id" => $goods_id,
                "num" => count($v),
                "luck_num" => '',
                "code_list" => "," . implode($v, ",") . ",",
                "create_time" => microtime_float(),
                "join_type" => $join_type
            );
            array_push($luck_num_arr, $tmp);
        }

        /*一次插入到数据库去*/
        $r_2 = $this->m->table("sp_luck_num")->addAll($luck_num_arr);
        if (!$r_2) {
            $this->m->rollback();
            return rt("-380", "", "写入幸运号码到lucknum表失败");
        }

        //写入期数的状态 participant_num
        //奇数抢宝
        if ($join_type == 1) {
            $r_3 = $this->m->table("sp_nper_list")->where(array("id" => $nper_id))->setInc("odd_join_num", $succ_num);
        } elseif ($join_type == 2) {
            $r_3 = $this->m->table("sp_nper_list")->where(array("id" => $nper_id))->setInc("even_join_num", $succ_num);
        } else {
            $r_3 = $this->m->table("sp_nper_list")->where(array("id" => $nper_id))->setInc("participant_num", $succ_num);
        }
        if (!$r_3) {
            $this->m->rollback();
            return rt("-390", "", "写入期数购买数表失败");
        }
        //写入商品的状态 buy_times
        $r_4 = $this->m->table("sp_goods")->where(array("id" => $goods_id))->setInc("buy_times", $succ_num);
        if (!$r_4) {
            $this->m->rollback();
            return rt("-400", "", "写入商品购买总数失败");
        }
        if ($r_2) {
            //设置为已经分配幸运数字,下次来不分配了
            $r_5 = $this->m->table("sp_order_list")->where(array("order_id" => $order_id))->save(array("dealed" => "true"));

            if ($r_1 && $r_2 && $r_3 && $r_4 && $r_5) {
                $this->m->commit();
                return array(
                    "err_num" => $err_num,
                    "succ_num" => $succ_num,
                    "code" => "1",
                    "order_id" => $order_id
                );
            }
        }
        $this->m->rollback();
        return rt("-410", "", "存在不成功的提交");
    }

    //获取父级订单信息根据id
    public function get_p_info_by_id($id)
    {
        return $this->m->table('sp_order_list_parent')->where(array("id" => $id))->find();
    }

    //获取父级订单信息根据order_id
    public function get_p_info_by_order_id($order_id)
    {
        return $this->m->table('sp_order_list_parent')->where(array("order_id" => $order_id))->find();
    }

    //设置订单状态,type=true 按照id 默认按照订单
    public function set_order_status($order_id = null, $pay_status = null, $limit_status = null, $type = false)
    {
        $m_p_order = M('order_list_parent', 'sp_');
        if (empty($order_id) || empty($pay_status)) return false;

        if ($type == true) {
            $where = array(
                "id" => $order_id
            );
        } else {
            $where = array(
                "order_id" => $order_id
            );
        }
        if ($limit_status) {
            $where['pay_status'] = (string)$limit_status;
        }
        return $m_p_order->where($where)->save(array("pay_status" => $pay_status));
    }


    //获取充值子订单内容
    public function get_charge_order_by_pid($pid)
    {
        $m_order = M('order_list', 'sp_');
        return $m_order->where(array("pid" => $pid))->find();
    }

    //设置父级订单的付款时间
    public function set_p_order_pay_time($order_id)
    {
        $m_p_order = M('order_list_parent', 'sp_');
        $time = microtime_float();
        $data = array(
            'pay_time' => $time,
            'pay_time_format' => microtime_format($time, 1, 'His'),
        );
        return $m_p_order->where(array("order_id" => $order_id))->save($data);
    }

    //设置子级订单的付款时间
    public function set_c_order_pay_time($order_id)
    {
        $m_p_order = M('order_list', 'sp_');
        $time = microtime_float();
        $data = array(
            'pay_time' => $time,
            'pay_time_format' => microtime_format($time, 1, 'His')
        );
        return $m_p_order->where(array("order_id" => $order_id))->save($data);
    }
}