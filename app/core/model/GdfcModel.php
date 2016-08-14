<?php
namespace app\core\model;

use think\Db;
use think\Model;
use think\model\Adv;

/**开奖独立模型,本模型独立于其他任何模型*/
Class GdfcModel extends Adv
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取配置信息
    public function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }

    //设置配置信息
    public function set_conf($name, $value)
    {
        $m_conf = M('conf', 'sp_');
        return $m_conf->where(array('name' => $name))->save(array('value' => $value));
    }

    //根据id获取商品期数详情
    public function get_nper_info_by_id($id)
    {
        $m_nper_list = M('nper_list', 'sp_');
        $sql = 'SELECT n.*,de.name de_name FROM sp_nper_list n
                LEFT JOIN sp_deposer_type de ON n.deposer_type = de.id
                WHERE n.id=' . $id;

        $info = $m_nper_list->query($sql);
        return empty($info[0]) ? false : $info[0];
    }

    //获取已支付订单数量
    public function get_payed_num($nper_id)
    {
        $m_order_list = M('order_list', 'sp_');
        //$sql = 'SELECT SUM(num) num FROM sp_order_list WHERE status=1 AND nper_id=' . $nper_id;
        $sql = "SELECT join_type,SUM(num) num FROM sp_order_list WHERE status=1 AND nper_id={$nper_id} GROUP BY join_type";
        $info = $m_order_list->query($sql);
        $keys = array_column($info, 'join_type');
        return array_combine($keys, $info);
    }

    //记录日志
    public function log($post)
    {
        $m_log = M('log', '');
        extract($post);
        $data = array(
            "nper_id" => $nper_id,
            "log" => $log,
            "create_time" => date("Y-m-d H:i:s")
        );
        return $m_log->add($data);
    }

    //获取下一期开奖期数
    public function get_last_lottery_issue()
    {
        $m_lottery = M('lottery', 'sp_');
        $sql = 'SELECT MAX(issue) max_issue,open_time  FROM sp_lottery';
        $info = $m_lottery->query($sql);
        return $m_lottery->where(array("issue" => $info[0]['max_issue']))->order("id desc")->find();
    }

    //获取期数时间戳求和
    public function get_sum_time_stamp($nper_id)
    {
        $m_order_list = M('order_list', 'sp_');
        $sql = 'SELECT SUM(pay_time_format) sum_time FROM (SELECT pay_time_format from sp_order_list WHERE dealed="true" AND nper_id = ' . $nper_id . ' ORDER BY pay_time DESC LIMIT 50) temp';
        $info = $m_order_list->query($sql);

        return $info[0]['sum_time'];
    }

    //step2
    public function goto_step_2($post)
    {
        $m_order = M('nper_list', 'sp_');

        extract($post);
        $data = array(
            "status" => 2,
            "sum_timestamp" => $sum_timestamp,
            "trigger_time" => $trigger_time,
            "lottery_issue" => $lottery_issue,
            "open_time" => $open_time,
        );
        return $m_order->where(array('id' => $nper_id))->lock(false)->save($data) !== false;
    }


    //step3,正常开奖
    public function goto_step_3($post)
    {
        $m_order = M('nper_list', 'sp_');
        extract($post);
        $data = array(
            "status" => 3,
            "open_model" => 'common',//开奖模式
            "open_num" => $open_num,//时时彩号码
            "remainder" => $remainder,//求余数
            "luck_num" => $luck_num,//开奖计算号码
            "luck_user" => $luck_user,//幸运用户名称
            "luck_uid" => $luck_uid,//幸运用户id
            "luck_time" => (string)microtime_float(),//开奖时间
            "luck_order_id" => $luck_order_id,//幸运用户订单号
        );

        return $m_order->where(array('id' => $nper_id))->save($data) != false;
    }

    //step3,RT开奖
    public function goto_step_3_rt($post)
    {

        log_w('开奖号码:' . $post['open_num']);

        $m = M();
        $m_order = M('order_list', 'sp_');
        $m_nper = M('nper_list', 'sp_');
        extract($post);
        //查询当期是否有制定中奖的用户,如果存在制定中奖用户

        log_w('指定用户中奖id:' . $rt_point_uid);
        if (!empty($rt_point_uid)) {
            log_w('走流程指定用户中奖模式:' . $rt_point_uid);
            //查询该用户当期是否有购买的内容
            $sql = 'SELECT l.order_id, l.code_list,u.username u_username,u.id u_id
                FROM  sp_luck_num l
                LEFT JOIN sp_users u ON l.uid=u.id
                WHERE  l.uid=' . $rt_point_uid . ' AND l.nper_id=' . $nper_id . ' order by l.id LIMIT 1';
            $list = $m->query($sql);

            log_w('SQL:' . $sql);
            log_w('查询内容:' . json_encode($list));
        }
        //如果不存在查询的制定用户信息或者没有指定幸运用户
        if (empty($list) || empty($rt_point_uid)) {
            log_w('随机用户开奖模式:' . $rt_point_uid);
            //随机获取当期机器人的一个幸运数字
            $sql = 'SELECT l.order_id, l.code_list,u.username u_username,u.id u_id
                FROM  sp_luck_num l
                LEFT JOIN sp_users u ON l.uid=u.id
                WHERE  u.type=-1 AND l.nper_id=' . $nper_id . ' order by l.username LIMIT 1';
            $list = $m->query($sql);
        }

        if (!empty($list)) {
            $list = $list[0];
            $arr = str_to_arr($list['code_list']);
            //指定奇偶
            if ($rt_point_type == 1 || $rt_point_type == 2) {
                $code_filter = $rt_point_type == 1 ? array_filter($arr, 'is_odd') : array_filter($arr, 'is_even');
                if (empty($code_filter)) {
                    log_w('指定了' . ($rt_point_type == 1 ? '奇数' : '偶数') . '中奖却没有机器人购买' . ($rt_point_type == 1 ? '奇数' : '偶数'));
                } else {
                    $arr = $code_filter;
                }
            }
            $luck_num_key = array_rand($arr, 1);//随机返回一个幸运数字
            $luck_num = $arr[$luck_num_key];
//            log_w('真实余数是:' . $remainder);
//            log_w('假的开奖数字列表:' . $list['code_list']);
//            log_w('假的幸运号码是:' . $luck_num);

            $luck_user = $list['u_username'];
            $luck_uid = $list['u_id'];
            $luck_order_id = $list['order_id'];

            //根据数字反推
            /**step_1:算出余数*/
            $remainder_2 = $luck_num - 1;
//            log_w('假的余数是:' . $remainder_2);

            /**step_2:算真余数和时间额定余数的差 $remainder*/
            $cha = (int)$remainder - (int)$remainder_2;

//            log_w('真实余数-假的余数:' . $cha);

            /**step_3:获取最后一个订单的付款时间  负数加差  正数减差*/
            $sql = 'SELECT * FROM sp_order_list WHERE nper_id=' . $nper_id . ' ORDER BY pay_time DESC LIMIT 1';
            $list = $m->query($sql);

            if (!empty($list)) {
                $list = $list[0];
                $pay_time = $list['pay_time'];
                $pay_time = $pay_time - $cha + $sum_times;//计算后的时间,
                $sum_timestamp = $sum_timestamp - $cha + $sum_times;

//                log_w('修改前的时间:' . $list['pay_time_format']);
//                if (intval($cha) > 0) {
//                    $pay_time = $pay_time + $sum_times - $cha;//计算后的时间,
//                } else if (intval($cha) < 0) {
//                    $pay_time = $pay_time + $cha;//计算后的时间,
//                }
                $pay_time_format = microtime_format($pay_time, 2, 'is');
                /**4.写入时间戳信息*/
                $sql = 'UPDATE sp_order_list SET pay_time_format="' . $pay_time_format . '" , pay_time="' . $pay_time . '" WHERE id=' . $list['id'];
                $m->execute($sql);

                $li = $m_order->where(array("id" => $list['id']))->find();
//                log_w('修改后的时间:' . $li['pay_time_format']);
                $remainder = $remainder_2;
            }
        }

        //记录中奖信息
        $luck_time = strval(microtime_float());
        $data = array(
            "status" => 3,
            "open_num" => $open_num,//时时彩号码
            "open_model" => 'rt',//开奖模式
            "remainder" => $remainder,//求余数
            "luck_num" => $luck_num,//开奖计算号码
            "luck_user" => $luck_user,//幸运用户名称
            "luck_uid" => $luck_uid,//幸运用户id
            "sum_timestamp" => $sum_timestamp,//总时间戳
            "luck_time" => $luck_time,//开奖时间
            "luck_order_id" => $luck_order_id,//幸运用户订单号
        );


        $r = $m_nper->where(array('id' => $nper_id))->save($data) != false;
        //防止出现幸运时间为E+12类似的
        if ($r) {
            $sql = 'UPDATE sp_nper_list SET luck_time = "' . $luck_time . '" WHERE id = ' . $nper_id;
            return $m_nper->execute($sql) !== false;
        }
        else {
            return false;
        }
    }

    //更新期号信息
    public function update_nper_info($id,$data){
        $m_order = M('nper_list', 'sp_');
        return $m_order->where(['id'=>$id])->save($data);
    }

    //根据期数获取彩票详情内容
    public function get_lottery_info_by_issue($issue)
    {
        $m_lottery = M('lottery', 'sp_');
        return $m_lottery->where(array('issue' => $issue))->find();
    }

    //获取幸运用户号码信息,包含用户id,订单id,期数id
    public function get_luck_num_info($nper_id, $num, $join_type = 0)
    {
        $m_luck_num = M('luck_num', 'sp_');
        $result = $m_luck_num->where(['nper_id' => $nper_id, 'code_list' => ['like', '%,' . $num . ',%'], 'join_type' => $join_type])->find();
        return $result;
    }

    //写入用户夺宝币中奖记录
    public function add_zero_win_record($nper_id, $with_out_uid, $open_num, $luck_num, $luck_time, $goods_id, $join_type)
    {
        //return true;//测试重复
        $m_record = M('win_record', 'sp_');
        $time = time();
        //如果数据库有期数的中奖纪录则不插入
        $res = $m_record->where(array('nper_id'=>$nper_id,'qb_type'=>$join_type))->field('id')->find();
        if(!is_null($res)) {
            return true;
        }
        $sql = "INSERT INTO sp_win_record (nper_id,open_num,luck_uid,luck_num,luck_user,luck_time,create_time,goods_id,order_num,qb_type,luck_type,money)
	SELECT
	   '{$nper_id}',
	    '{$open_num}',
	    o.uid,
	    '{$luck_num}',
	    o.username,
	    '{$luck_time}',
	    {$time},
	    {$goods_id},
	    sum(o.num) order_num,
        {$join_type},
		2,
		sum(o.money) sum_money
	FROM
		sp_order_list o
	WHERE
		o.nper_id = '{$nper_id}'
	AND o.bus_type = 'buy'
	AND o.`status` = 1
	AND o.join_type = {$join_type}
	AND o.uid <> {$with_out_uid}
	GROUP BY
		o.uid";
        return $m_record->execute($sql);
    }

    //执行退款
    public function add_money_by_refund($nper_id)
    {
        $m_user = M('users', 'sp_');
        $sql = "UPDATE sp_users u
RIGHT JOIN (
	SELECT
		luck_uid,money
	FROM
		sp_win_record
	WHERE
		nper_id = '{$nper_id}'
    AND luck_type = 2
    AND logistics_status = 0
) w ON w.luck_uid = u.id
SET u.money = u.money + w.money ";
        return $m_user->execute($sql);
    }

    //标记退款完成
    public function win_record_sign_refund($nper_id)
    {
        $m_record = M('win_record', 'sp_');
        $sql = "UPDATE sp_win_record
            SET logistics_status=5
            WHERE logistics_status=0
            AND nper_id = '{$nper_id}'
            AND luck_type = 2";
        return $m_record->execute($sql);
    }

    //获取幸运订单详情
    public function get_order_info_by_id($order = '')
    {
        $m_luck_num = M('order_list', 'sp_');

        return $m_luck_num->where("order_id='" . $order . "'")->find();
    }

    //获取幸运用户详情
    public function get_user_info_by_id($id)
    {
        $m_luck_num = M('users', 'sp_');
        return $m_luck_num->where(array("id" => $id))->find();
    }

    //根据id获取商品详情
    public function get_goods_info_by_id($id)
    {
        $m_goods = M('goods', 'sp_');
        return $m_goods->where(array("status" => "1", "id" => $id))->find();
    }

    //根据id获取当前正在购买中的商品信息
    public function get_buying_goods_info($pid)
    {
        $m_order = M('nper_list', 'sp_');
        return $m_order->where(array('status' => 1, "pid" => $pid, "nper_type" => 1))->find();
    }

    //根据id获取正在购买种的0元商品信息
    public function get_buying_zero_goods_info($pid)
    {
        $m_order = M('nper_list', 'sp_');
        return $m_order->where(array('status' => 1, "pid" => $pid, "nper_type" => 2))->find();
    }

    //0元夺宝初始化期数
    public function init_zero_nper($post)
    {
        $pid = $category = $mid = $sec_open = $deposer_type = $init_times = $unit_price = $min_times = $sum_times = $odd_times = $even_times = null;
        extract($post);
        $data = array(
            "pid" => $pid,
            "category" => $category,
            "mid" => $mid,
            "status" => 1,
            "deposer_type" => $deposer_type,
            "init_times" => $init_times,
            "unit_price" => $unit_price,
            "min_times" => $min_times,
            "sum_times" => $sum_times,
            "odd_times" => $odd_times,
            "even_times" => $even_times,
            "sec_open" => $sec_open,
            "nper_type" => 2,
            "create_time" => NOW_TIME
        );

        $m = $this->db(1);
        $m->startTrans();

        $shuffle = array();
        for ($i = 1; $i <= (int)$sum_times; $i++) {
            array_push($shuffle, $i);
        }
        //数组自动打乱并分割存储到数据库
        shuffle($shuffle);
        $split_num = ((int)__SPLIT_CODE_NUM__ < 3000 || (int)__SPLIT_CODE_NUM__ > 10000) ? 3000 : __SPLIT_CODE_NUM__;
        $shuffle = array_chunk($shuffle, $split_num);//默认按3000条一个字段分组存储
        $shuffle_arr = array();


        //判断
        $s1 = $m->table('sp_nper_list')->add($data);
        foreach ($shuffle as $k => $v) {
            $tmp = array(
                'cid' => $k,
                'nper_id' => $s1,
                'len' => count($v),
                'code_list' => json_encode($v)
            );
            array_push($shuffle_arr, $tmp);
        }


        //以当前期数id,计算code_list表的数字,每100000个数据分为1个表存储
        $num = ceil((int)$s1 / 100000);//
        //表名字
        $table_name = 'sp_shuffle_list_' . $num;
        $sql = "SHOW TABLES LIKE '" . $table_name . "'";

        $s2 = true;
        if (count($m->table('sp_nper_list')->query($sql)) <= 0) {
            $sql = "CREATE TABLE " . $table_name . " LIKE sp_shuffle_list";
            $s2 = $m->table('sp_nper_list')->execute($sql);
        }

        //更新随机数 表明和分割数量到期数
        $data = array(
            'shuffle_name' => $table_name,
            'shuffle_split_num' => $split_num
        );

        $s3 = $m->table('sp_nper_list')->where(array('id' => $s1))->save($data);
        //如果表中当期的数据了,则不插入新的数据

        if (!$m->table($table_name)->where(array("nper_id" => $s1))->find()) {
            //插入随机数字数据到表中
            $s4 = $m->table($table_name)->addAll($shuffle_arr);
        } else {
            $s4 = true;
        }

        if ($s1 && $s2 && $s3 && $s4) {
            $m->commit();
            return true;
        } else {
            $m->rollback();
            return false;
        }
    }

    //初始化新的期数
    public function init_new_nper($post)
    {
        $pid = $category = $max_times = $sec_open = $mid = $deposer_type = $init_times = $unit_price = $min_times = $sum_times = null;
        extract($post);
        $data = array(
            "pid" => $pid,
            "category" => $category,
            "mid" => $mid,
            "status" => 1,
            "deposer_type" => $deposer_type,
            "init_times" => $init_times,
            "unit_price" => $unit_price,
            "min_times" => $min_times,
            "max_times" => $max_times,
            "sum_times" => $sum_times,
            "sec_open" => $sec_open,
            "create_time" => NOW_TIME
        );

        $m = $this->db(1);
        $m->startTrans();

        $shuffle = array();
        for ($i = 1; $i <= (int)$sum_times; $i++) {
            array_push($shuffle, $i);
        }
        //数组自动打乱并分割存储到数据库
        shuffle($shuffle);
        $split_num = ((int)__SPLIT_CODE_NUM__ < 3000 || (int)__SPLIT_CODE_NUM__ > 10000) ? 3000 : __SPLIT_CODE_NUM__;
        $shuffle = array_chunk($shuffle, $split_num);//默认按3000条一个字段分组存储
        $shuffle_arr = array();


        //判断
        $s1 = $m->table('sp_nper_list')->add($data);
        foreach ($shuffle as $k => $v) {
            $tmp = array(
                'cid' => $k,
                'nper_id' => $s1,
                'len' => count($v),
                'code_list' => json_encode($v)
            );
            array_push($shuffle_arr, $tmp);
        }


        //以当前期数id,计算code_list表的数字,每100000个数据分为1个表存储
        $num = ceil((int)$s1 / 100000);//
        //表名字
        $table_name = 'sp_shuffle_list_' . $num;
        $sql = "SHOW TABLES LIKE '" . $table_name . "'";

        $s2 = true;
        if (count($m->table('sp_nper_list')->query($sql)) <= 0) {
            $sql = "CREATE TABLE " . $table_name . " LIKE sp_shuffle_list";
            $s2 = $m->table('sp_nper_list')->execute($sql);
        }

        //更新随机数 表明和分割数量到期数
        $data = array(
            'shuffle_name' => $table_name,
            'shuffle_split_num' => $split_num
        );

        $s3 = $m->table('sp_nper_list')->where(array('id' => $s1))->save($data);
        //如果表中当期的数据了,则不插入新的数据

        if (!$m->table($table_name)->where(array("nper_id" => $s1))->find()) {
            //插入随机数字数据到表中
            $s4 = $m->table($table_name)->addAll($shuffle_arr);
        } else {
            $s4 = true;
        }

        if ($s1 && $s2 && $s3 && $s4) {
            $m->commit();
            return true;
        } else {
            $m->rollback();
            return false;
        }
    }

//设置幸运订单标记
    public function set_order_luck_flag($order_id)
    {
        $m_order = M("order_list", "sp_");
        return $m_order->where(array("order_id" => $order_id))->save(array("luck_status" => "true"));
    }

    //设置幸运数字表中奖标记
    public function set_luck_num_flag($nper_id, $order_id, $luck_num)
    {
        $m_luck = M("luck_num", "sp_");
        return $m_luck->where(array("nper_id" => $nper_id, "order_id" => $order_id))->save(array("status" => "true", "luck_num" => $luck_num));
    }

    //获取已经超时的没有触发过的已开奖的期数
    public function get_timeout_nper_id()
    {
        $sql = "SELECT * FROM sp_nper_list WHERE  status ='2' AND open_time <" . NOW_TIME . " ORDER BY id DESC";

        $m_nper = M('nper_list', 'sp_');
        $info = $m_nper->query($sql);
        if (!empty($info[0]["id"])) return $info[0]["id"];
    }

    //开奖触发
    public function trigger_nper($nper_id)
    {
        $m_nper = M('nper_list', 'sp_');
        return $m_nper->where(array("id" => $nper_id))->save(array("trigger_flag" => "true"));
    }

    //设置幸运数字表中奖标记
    public function set_win_record($post)
    {
        $order_id = $order_num = $goods_id = $nper_id = $open_num = $remainder = $luck_num = $luck_user = $luck_uid = $luck_time = '';
        $m_win = M("win_record", "sp_");
        if (empty($post)) {
            log_w("通知开奖错误");
            return false;
        }
        extract($post);

        $data = array(
            "order_id" => $order_id,
            "order_num" => $order_num,
            "goods_id" => $goods_id,
            "nper_id" => $nper_id,
            "open_num" => $open_num,
            "remainder" => $remainder,
            "luck_num" => $luck_num,
            "luck_user" => $luck_user,
            //"luck_user" => '重复位置1',
            "luck_uid" => $luck_uid,
            "luck_time" => $luck_time,
            "create_time" => NOW_TIME,
        );

        //检测是否已经有存在的记录
        $rt = $m_win->where(array('nper_id' => $nper_id))->find();
        if (empty($rt)) {
           // $r = $m_win->add($data);
            //$rt = $m_win->where(array('nper_id' => $nper_id))->select();

            $sql = "INSERT INTO sp_win_record(order_id, order_num,goods_id,nper_id,open_num,remainder,luck_num,luck_user,luck_uid,luck_time,create_time) 
              SELECT '".$order_id."', '".$order_num."', '".$goods_id."', '".$nper_id."', '".$open_num."', '".$remainder."', '".$luck_num."','".$luck_user."', '".$luck_uid."', '".$luck_time."', '".NOW_TIME."'
               FROM DUAL WHERE NOT EXISTS(SELECT nper_id FROM sp_win_record WHERE nper_id = '".$nper_id."') limit 1";
            $r = M()->execute($sql);
            return $r;
        }
        return true;
    }

    public function set_packet($post)
    {
        $p_model = M('packet');
        if (empty($post)) {
            log_w("微信红包错误");
            return false;
        }
        $rt = $p_model->where(['nper_id' => $post['nper_id']])->find();
        if (empty($rt)) {
            $r = $p_model->add($post);
            return $r;
        }
        return true;
    }

    //获取一个状态为2,已经到开奖时间,没有触发标记的期数
    public function get_trigger_one()
    {
        $m_nper_list = M("nper_list", "sp_");
        return $m_nper_list->where('status=2 AND open_trigger="false" AND open_time<' . NOW_TIME)->find();
    }

    //保存触发结果
    public function save_trigger_result($nper_id, $result)
    {
        $m_nper_list = M("nper_list", "sp_");
        return $m_nper_list->where(array("id" => $nper_id))->save(array("open_trigger" => "true", "open_trigger_result" => $result));
    }

    //获取rt配置根据商品id
    public function get_rt_config($goods_id = null)
    {
        $m = M("rt_presets", "sp_");
        return $m->where(array('gid' => $goods_id))->find();
    }

    //获取当前期数最大的已经购买的号码的坐标值
    public function get_max_site_info($nper_id)
    {
        $m = M('order_list', 'sp_');
        $sql = 'select max(index_end) num FROM sp_order_list WHERE nper_id=' . $nper_id;
        return $m->query($sql);
    }

    public function get_goods_cheat($goods_id)
    {
        $m = M("goods", "sp_");
        $r = $m->where(array("id" => $goods_id))->field("is_cheat")->find();
        return $r['is_cheat'];
    }
}