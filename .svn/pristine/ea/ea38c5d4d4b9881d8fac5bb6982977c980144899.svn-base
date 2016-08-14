<?php
namespace app\core\controller;

use app\core\model\CommonModel;
use app\core\model\GdfcModel;
use app\core\controller\Api;
use app\api\controller\Base;
use think\Exception;


/**
 * 开奖控制器,本类控制器和模型单独处理
 */
class Gdfc extends Common
{
    private $m_gdfc;
    private $nper_id;//本期的id
    private $nper_info;//本期的详情信息
    private $payed_num;//本期的已支付订单数
    private $open_rt_flag = false;//开启RT模式
    private $msg_flag = 0; //判断短信是否发送1是0否
    private $win_flag = 0; //判断是否已经写入中奖记录

    public function __construct()
    {
        parent::__construct();
        $this->m_gdfc = new GdfcModel();
        $this->nper_id = I("request.nper_id", null);
    }
    public function open_by_nper($nper_id = null)
    {
        $this->init($nper_id);
        empty($nper_id) && die_json(json_encode($this->chk_state_lottery_criteria()), 1);
        return json_encode($this->chk_state_lottery_criteria());
    }
    public function gateway($nper_id = null)
    {
        $nper_id = empty($nper_id) ? $this->nper_id : $nper_id;//期数id
        $this->init($nper_id);
        $this->exec();
    }
    /**扫描已经到时间但是还没有开奖的期数*/
    public function scan_timeout_nper()
    {
        $id = $this->m_gdfc->get_timeout_nper_id();
        empty($id) && die_result("-100", "No lottery order");
        //触发开奖
        $r = $this->m_gdfc->trigger_nper($id);
        $this->gateway($id);
        $r = json_encode($r);
        die_result("1", $r);
    }
    /**初始化*/
    private function init($nper_id = null)
    {
        $nper_id = empty($nper_id) ? $this->nper_id : $nper_id;
        if (empty($nper_id) || !is_numeric($nper_id)) return show_result('-110', '期数id错误');
        $this->nper_id = $nper_id;//期数id


        $this->get_nper_info();//获取当前期数开奖信息

        $this->get_payed_num();//当前期数已付款订单数量


        //获取是否开启RT
        /**检测当前商品是否开启RT机制S*/
        $nper_info = $this->nper_info;
        if (!empty($nper_info['pid'])) {
            $goods_id = $nper_info['pid'];
            //获取当前商品是否开启RT模式
            $is_cheat = $this->m_gdfc->get_goods_cheat($goods_id);
            if ($is_cheat == 'true') {
                $this->open_rt_flag = true;
            }
        }
        /**检测当前商品是否开启RT机制E*/
    }
    /**执行扫描程序*/
    private function exec()
    {
        $nper_info = $this->nper_info;

        if ($nper_info['status'] == '1') {
            $this->chk_state_and_prepare_lottery();
        } else if ($nper_info['status'] == '2') {
            $this->chk_state_lottery_criteria();
        } else {
            return show_result('1', '当前期数已开奖');
        }
    }
    /**
     * 检测是否达到第二步 开奖中状态,达到则进入第二步
     * @param int $nper_id 期数id
     * @return bool 是否达到开奖中标准
     */
    private function chk_state_and_prepare_lottery()
    {
        $nper_info = $this->nper_info;

        /**0校验期数状态是否为1*/
        if ($nper_info['status'] != '1') return show_result('-130', '当前期数状态不正确');

        /**1.检测购买人数是否达标*/
        if ($nper_info['nper_type'] == 1) {
            //期数里面记录的值,允许有5%的误差
            $condition_1 = ((int)$nper_info['sum_times'] - (int)$nper_info['participant_num'] > ($nper_info['sum_times'] * 0.05));

            //订单里的最大值
            $max_info = $this->m_gdfc->get_max_site_info($nper_info['id']);
            $condition_2 = !empty($max_info) ? (((int)$nper_info['sum_times'] - 2) >= (int)$max_info[0]['num']) : true;

            if ($condition_1 || $condition_2) return show_result('-140', '购买人数没有达到设定开奖人数');
        } else {/*zero*/
            if ((int)$nper_info['sum_times'] > (int)$nper_info['odd_join_num'] ||
                (int)$nper_info['sum_times'] > (int)$nper_info['even_join_num']
            )
                return show_result('-140', '购买人数没有达到设定开奖人数');
        }


        /**2.再次校验购买人数是否有误*/
        if ($nper_info['nper_type'] == 1) {
            if ((int)$nper_info['participant_num'] !== $this->payed_num['0']['num']) {
                $this->log(
                    '警告:累加购买人数[' . $nper_info['participant_num'] .
                    ']和实际订单购买人数[' . $this->payed_num['0']['num']
                    . ']相差:' . ($this->payed_num['0']['num'] - (int)$nper_info['participant_num']));
            }
        } else {/*zero*/
            if ((int)$nper_info['odd_join_num'] !== $this->payed_num['1']['num'] || (int)$nper_info['even_join_num'] !== $this->payed_num['2']['num']) {
                $this->log(
                    '警告:（零元）累加购买人数和实际订单人数不一致');
            }
        }

        /**3.获取最后一次开奖号码和开奖时间成功->last_lottery_issue*/
        $last_lottery_issue = $this->m_gdfc->get_last_lottery_issue();

        $max_issue = $last_lottery_issue['issue'];
        $open_time = strtotime($last_lottery_issue['open_time']);//下一期开奖时间(int),彩票接口采集


        //上期彩票的期数
        $issue = (int)substr($max_issue, strlen($max_issue) - 3, strlen($max_issue));
        //上期彩票的开奖日期
        $date = (int)substr($max_issue, 0, strlen($max_issue) - 3);

        //RT模式
        if ($this->open_rt_flag) {
            $next_lottery_issue = $last_lottery_issue['issue'];
            $hour  = date('H',time());
            $minute  = date('i',time());
            if ( $hour >= 22) {
                $rt_date = date('Y-m-d',strtotime("+1 day"));
                $rt_date =$rt_date.' 10:10:00';
                $next_open_time = strtotime($rt_date);

            } else if( $hour < 10  || ( $hour = 10 && $minute < 10 )) {
                $rt_date = date('Y-m-d');
                $rt_date =$rt_date.' 10:10:00';
                $next_open_time = strtotime($rt_date);
            } else {
                $next_open_time = $open_time + 600;
            }
        } //正常模式 开启秒开
        elseif ($this->nper_info['sec_open'] == 1) {
            $next_lottery_issue = $max_issue;

        } else {//正常模式 未开启秒开
            /**计算下一期的期数*/
            $next_lottery_issue = 0;
            //期数为1-119时候,正常+1
            if ($issue > 0 && $issue < 120) {
                $next_issue = $issue + 1;
                $next_lottery_issue = $max_issue + 1;
            } //期数为120时候,天数+1
            else if ($issue == 120) {
                $next_issue = '001';
                $date = date('Ymd', strtotime("+1 days", strtotime($date))) . $next_issue;
                $next_lottery_issue = $date;
            } else {
                $this->log('开奖期数异常');
                return show_result('-150', '开奖期数异常');
            }//期数不正确

            /**计算下一期的开奖时间*/
            $next_issue = intval($next_issue);
            switch ($next_issue) {
                case ($next_issue > 24 && $next_issue < 96):
                    $next_open_time = $open_time + 660;//比官方时间慢一分钟开奖
                    break;
                case (($next_issue > 0 && $next_issue < 24) || ($next_issue > 95 && $next_issue <= 120)):
                    $next_open_time = $open_time + 480;//比官方时间慢一分钟开奖
                    break;
                case $next_issue == '24':
                    $next_open_time = date("Y-m-d", $open_time) . ' 10:12:00';//当天上午10点15开奖
                    $next_open_time = strtotime($next_open_time);
                    break;
                case $next_issue == '96':
                    $next_open_time = date("Y-m-d", $open_time) . ' 22:07:00';//当天下午10点开奖
                    $next_open_time = strtotime($next_open_time);
                    break;
                default:
                    return show_result('-160', '下一期开奖时间计算错误' . $next_issue);
            }
        }

        /**4.订单时间戳求和*/
        $sum_timestamp = $this->m_gdfc->get_sum_time_stamp($this->nper_id);
        if (empty($sum_timestamp)) {
            $this->log('时间戳计算错误');
            return show_result('-170', '时间戳计算错误');
        }
        if ((int)$nper_info['sum_timestamp_compare'] !== (int)$sum_timestamp) {
            $this->log('警告:订单累加结果[' . $nper_info['sum_timestamp_compare'] . ']和即时计算结果[' . $sum_timestamp . ']不一致,数据有可能被篡改');
        }

        //判断该期是够开启秒开
        if ($nper_info['sec_open'] == 1) {
            $next_open_time = NOW_TIME;

        } else if (abs((int)$next_open_time - NOW_TIME) < 600) { //判断:如果下一期开奖时间减去当前时间小于10分钟,则下一期开奖时间等于11分钟,如果超过10分钟,则为原来的开奖时间
            $next_open_time = NOW_TIME + 660;
        }


        /**写入待开奖信息,进入等待开奖程序*/
        //以上信息都OK,则转到开奖中
        $data = array(
            "nper_id" => $this->nper_id,
            "sum_timestamp" => $sum_timestamp,
            "trigger_time" => microtime_float(),
            "lottery_issue" => $next_lottery_issue,
            "open_time" => $next_open_time,
        );

        if ($this->m_gdfc->goto_step_2($data)) {
            log_w("用户:" . get_user_id() . "触发开奖,当前期数:" . $this->nper_id);


            /**2.再次校验购买人数是否有误*/
            if ($nper_info['nper_type'] == 1) {
                if ($this->init_new_nper($this->nper_info['pid'])) {
                    $this->open_by_nper($this->nper_id);
                    return show_result('1', '初始化下一期成功');
                }
            } else {/*zero*/
                if ($this->init_zero_nper($this->nper_info['pid'])) {
                    $this->open_by_nper($this->nper_id);
                    return show_result('1', '初始化下一期成功');
                }
            }

            return show_result('-1', '初始化下一期失败');
        }
        return show_result('-180', '进入等待开奖程序失败');
    }
    /**
     * 检测是否达到 已"开奖"状态标准
     * @param int $nper_id 期数id
     * @return bool 是否达到已开奖标准
     */
    private function chk_state_lottery_criteria()
    {
        $nper_info = $this->nper_info;

        if (empty($nper_info)) return show_result('1', '期数信息不存在');

        $sum_timestamp = $this->m_gdfc->get_sum_time_stamp($this->nper_id);//最后50个时间戳的和
        if (intval($nper_info['open_time']) > NOW_TIME) {
//            wrong_return('还没到开奖时间');
        }

        /**已开奖直接返回已开奖*/
        if ($nper_info['status'] == '3') return show_result('1', '彩票已经开奖');

        /**0校验期数状态是否为2*/
        if ($nper_info['status'] != '2') return show_result('-190', '当前期数状态不正确');

        /*获取当前期数依赖彩票期数信息*/
        if (empty($nper_info['lottery_issue'])) return show_result('-200', '依赖彩票期数错误');

        /**是否达到开奖时间*/
        if (!empty($nper_info['open_time'])) {
            if ($nper_info['open_time'] > NOW_TIME) return show_result('-205', '还没到开奖时间');
        } else return show_result('-206', '下一期开奖时间错误');

        $lottery_issue = $nper_info['lottery_issue'];
        //根据期数获取彩票采集数据库内容
        $lottery_info = $this->m_gdfc->get_lottery_info_by_issue($lottery_issue);

        /*2016-07-05 新增需求 如果预定开奖时间之后5分钟还没有获取到彩票信息则将期号改为最新的一期*/
        if (empty($lottery_info)){
            if(NOW_TIME>$nper_info['open_time']+300){
                $last_lottery_issue = $this->m_gdfc->get_last_lottery_issue();

                $max_issue = $last_lottery_issue['issue'];
                $open_time = strtotime($last_lottery_issue['open_time']);
                if($update_r = $this->m_gdfc->update_nper_info($nper_info['id'],['lottery_issue'=>$max_issue,'open_time'=>$open_time])){
                    $nper_info['lottery_issue'] = $max_issue;
                    $nper_info['open_time'] = $open_time;
                    $lottery_info=$last_lottery_issue;
                }

            }else{
                return show_result('-210', '彩票还没开奖');
            }
        }
        /**彩票已开奖,进行开奖运算*/

        /** >>1.开奖号码*/
        $open_num = $lottery_info['num'];
        if (empty($open_num)) return show_result('-220', '开奖号码获取失败');

        /** >>2.求余数=(时间戳和+彩票开奖号码)%购买次数*/

        //获取当前最后50个开奖号码的时间戳

        if (empty($sum_timestamp)) return show_result('-230', '时间戳求和不能为空');
        if (empty($nper_info['sum_times'])) return show_result('-240', '购买次数不能为空');

        $remainder = (intval($sum_timestamp) + intval($open_num)) % intval($nper_info['sum_times']);
        if (empty($remainder) && $remainder !== 0) return show_result('-250', '余数不能为空');

        //幸运号码为余数+1
        $luck_num = $remainder + 1;

        if ($nper_info['nper_type'] == 1) {
            //获取幸运号码信息
            $luck_num_info = $this->m_gdfc->get_luck_num_info($this->nper_id, $luck_num);//这里是 幸运数字 为中奖用户信息
        } else {/*zero*/
            $is_odd = $luck_num % 2 == 1;
            //获取幸运号码信息
            $luck_num_info = $this->m_gdfc->get_luck_num_info($this->nper_id, $luck_num, $is_odd ? 1 : 2);//这里是 幸运数字 为中奖用户信息
        }

        if (empty($luck_num_info)) return show_result('-270', '获取幸运用户信息失败');

        //获取订单信息
        $luck_order_info = $this->m_gdfc->get_order_info_by_id($luck_num_info['order_id']);
        $luck_user_info = $this->m_gdfc->get_user_info_by_id($luck_num_info['uid']);


        if (empty($luck_order_info)) return show_result('-280', '幸运订单信息获取失败');
        if (empty($luck_user_info)) return show_result('-290', '幸运用户信息不能为空');

        //幸运用户
        $luck_user = $luck_user_info['username'];
        if (empty($luck_user)) return show_result('-300', '幸运用户获取失败');

        //幸运用户id
        $luck_uid = $luck_user_info['id'];
        if (empty($luck_uid)) return show_result('-310', '幸运用户id获取失败');

        //幸运付款时间
        $luck_time = $luck_order_info['pay_time'];
        if (empty($luck_time)) return show_result('-320', '幸运付款时间获取失败');

        //幸运订单号
        $luck_order_id = $luck_order_info['order_id'];
        if (empty($luck_order_id)) return show_result('-330', '幸运订单号获取失败');

        //商品id
        $goods_id = $nper_info['pid'];
        if (empty($goods_id)) return show_result('-340', '商品id获取失败');

        $data = array(
            "order_id" => (string)$luck_order_info['order_id'],//订单id
            "order_num" => $luck_order_info['num'],//订单购买数量
            "goods_id" => $goods_id,//商品id
            "nper_id" => $this->nper_id,//期数id
            "open_num" => $open_num,//时时彩号码
            "remainder" => $remainder,//求余数
            "sum_times" => $nper_info['sum_times'],//购买总人数
            "luck_num" => $luck_num,//开奖计算号码
            "luck_user" => $luck_user,//幸运用户名称
            "luck_uid" => $luck_uid,//幸运用户id
            "luck_time" => $luck_time,//开奖时间
            "rt_point_uid" => empty($nper_info['rt_point_uid']) ? null : $nper_info['rt_point_uid'],//制定中奖的机器人id
            "rt_point_type" => empty($nper_info['rt_point_type']) ? null : $nper_info['rt_point_type'],
            "sum_timestamp" => $nper_info['sum_timestamp'],//期数总时间戳
            "luck_order_id" => $luck_order_id,//幸运用户订单号
        );

        /**万事具备,开奖,开奖成功后初始化下一期!*/
        log_w('开奖参数:' . json_encode($data));
        if (empty($this->nper_info['pid'])) return show_result("-350", "商品id获取失败");

        if ($this->open_rt_flag) {
            $r_step_3 = $this->m_gdfc->goto_step_3_rt($data);
        } else {
            $r_step_3 = $this->m_gdfc->goto_step_3($data);
        }

        if ($r_step_3) {
            //重新获取期数开奖后的用户信息
            $nper_info = $this->m_gdfc->get_nper_info_by_id($nper_info['id']);

            $luck_num = $nper_info["luck_num"];//开奖计算号码
            $luck_order_id = $nper_info["luck_order_id"];//幸运用户订单号


            //重新获取订单信息
            $order_info = $this->m_gdfc->get_order_info_by_id($luck_order_id);
            //重新赋值$data

            $data['order_id'] = $luck_order_id;
            $data['order_num'] = empty($order_info['num']) ? 1 : $order_info['num'];
            $data['remainder'] = $nper_info['remainder'];
            $data['luck_num'] = $nper_info['luck_num'];
            $data['luck_user'] = $nper_info['luck_user'];
            $data['luck_uid'] = $nper_info['luck_uid'];
            $data['sum_timestamp'] = $nper_info['sum_timestamp'];
            $data['luck_time'] = $nper_info['luck_time'];
            $data['luck_order_id'] = $nper_info['luck_order_id'];


            //标记订单号已中奖
            $this->m_gdfc->set_order_luck_flag($luck_order_id);
            //标记幸运号码表开奖信息
            $this->m_gdfc->set_luck_num_flag($this->nper_id, $luck_order_id, $luck_num);

            //写中奖用户信息
            if(!$this->win_flag){
                $this->m_gdfc->set_win_record($data);
                $this->win_flag = 1;
            }

            /**
             * 一元购微信红包
             * lc
             */
            if($this->nper_info['nper_type']==1){
                //判断商品是否配置红包
                $goods_info=M('goods')->field('name,is_packet,packet_money,packet_num')->where(['id'=>$goods_id])->find();
                if($goods_info['is_packet']==1){
                    if(!empty($goods_info['packet_money'])&&!empty($goods_info['packet_num'])){
                        $data=[
                            'goods_name'=>$goods_info['name'],
                            'uid'=>$nper_info['luck_uid'],
                            'goods_id'=>$goods_id,
                            'nper_id'=>$this->nper_id,
                            'create_time'=>time(),
                            'end_time'=>time()+intval(C('PACKET_LIFE_TIME'))*24*3600,
                            'money'=>$goods_info['packet_money'],
                            'num'=>$goods_info['packet_num']
                        ];
                        $this->m_gdfc->set_packet($data);
                    }
                }
            }



            //退款操作
            if ($this->nper_info['nper_type'] == 2) {
                //记录退款
                //$refund_record = $this->m_gdfc->count_order_list_by_nper($nper_info['id'],$is_odd?1:2);
                //写入中奖记录（夺宝币中奖）（获得商品的用户除外）
                //$nper_id,$with_out_uid,$open_num,$luck_user,$luck_time,$goods_id,$order_num,$join_type
                $refund_record = $this->m_gdfc->add_zero_win_record($nper_info['id'], $luck_uid, $open_num, $luck_num, $luck_time, $goods_id, $is_odd ? 1 : 2);
                if ($refund_record) {
                    $add_money_result = $this->m_gdfc->add_money_by_refund($nper_info['id']);
                    if ($add_money_result) {
                        $this->m_gdfc->win_record_sign_refund($nper_info['id']);
                    } else {
                        return show_result('-410', '退款失败');
                    }
                } else {
                    return show_result('-400', '退款失败');
                }
            }

            //给有用手机号注册的用户发送短信
            if(!$this->msg_flag){
                $data = M('users')->field('phone,type')->where(array('id'=>  $nper_info['luck_uid']))->find();
                if (!empty($data['phone']) && ($data['type'] == 1 || $data['type'] == 2) ){
                    $this->send_win_msg($data['phone'], $nper_info['id']);
                }
            }
            return show_result('1', '开奖成功');
        }
        return show_result('-340', '开奖失败');
    }
    /**发送中奖信息*/
    private function send_win_msg($phone, $nper_id)
    {
            $mod = new Api();
            $res = $mod->send_win_msg($phone, $nper_id,C('TOKEN_ACCESS'));
            $this->msg_flag = 1;
            log_w(json_encode($res));
    }
    /**获取期数信息*/
    private function get_nper_info()
    {
        $this->nper_info = $this->m_gdfc->get_nper_info_by_id($this->nper_id);
        if (empty($this->nper_info)) return show_result('-350', '期数信息获取失败');
    }
    /**获取已付款订单数量*/
    private function get_payed_num()
    {
        $this->payed_num = (int)$this->m_gdfc->get_payed_num($this->nper_id);
    }
    /**初始化下一期彩票信息*/
    public function init_new_nper($pid = null)
    {
//        $this->nper_info['pid']

//        //浏览器请求还是内部使用 true 浏览器a
//        $flag = I('request.goods_id', null);
        $flag = empty($pid) ? true : false;

        if ($flag) {
            $pid = !empty(I('request.goods_id')) ? I('request.goods_id') : null;
        } else {
            if (empty($pid)) {
                $pid = !empty($this->nper_info['pid']) ? $this->nper_info['pid'] : null;
            }
        }

        if (empty($pid)) {
            show_result('-360', '商品id不能为空', $flag);

        } else if (empty($pid)) {
            $this->log('商品id[' . $pid . ']:不能为空');
            return false;
        }

        //判断当前商品是否已经有正在购买中的,当前有正在进行中的商品
        if ($this->m_gdfc->get_buying_goods_info($pid)) {
            if ($flag) {
                $this->log('来自浏览器,商品id[' . $pid . ']:当前已有正在进行中的商品,无需再次创建');
                return show_result('-370', '当前已有正在进行中的商品,无需再次创建', $flag);
            } else {
                $this->log('来自内部,商品id[' . $pid . ']:当前已有正在进行中的商品,无需再次创建');
                return false;
            }
        }

        $g_info = $this->m_gdfc->get_goods_info_by_id($pid);

        if (empty($g_info)) {
            $this->log('商品id[' . $pid . ']:初始化商品错误!商品不存在或已禁用');
            if ($flag) return false;
            return show_result('-380', '初始化商品错误!商品不存在或已禁用', $flag);

        }

        $data = array(
            "pid" => $g_info['id'],
            "category" => $g_info['category'],
            "mid" => $g_info['mid'],
            "nper_id" => $this->nper_id,
            "deposer_type" => $g_info['deposer_type'],
            "init_times" => $g_info['init_times'],
            "unit_price" => $g_info['unit_price'],
            "min_times" => $g_info['min_times'],
            "sum_times" => $g_info['sum_times'],
            "max_times" => $g_info['max_times'],
            "sec_open" => $g_info['sec_open'],
        );

        $f = true;
        empty($data['pid']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数pid不完整');
        empty($data['category']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数category不完整');
        empty($data['mid']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数mid不完整');
        empty($data['deposer_type']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数deposer_type不完整');
        empty($data['init_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数init_times不完整');
        empty($data['unit_price']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数unit_price不完整');
        empty($data['min_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数min_times不完整');
        empty($data['sum_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数sum_times不完整');


        if (!$f && $flag) {
            return show_result('-390', '参数错误', $flag);
        } else if (!$f) {
            return false;
        }

        if ($flag == 1) {
            //浏览器访问
            if ($this->m_gdfc->init_new_nper($data)) return show_result('1', '新一期成功', $flag);
            return show_result('-1', '失败', $flag);
        } else {
            //内部访问
            return $this->m_gdfc->init_new_nper($data);
        }

    }
    //0元抢宝新一期
    public function init_zero_nper($pid = null)
    {
        $flag = empty($pid) ? true : false;

        if ($flag) {
            $pid = !empty(I('request.goods_id')) ? I('request.goods_id') : null;
        } else {
            if (empty($pid)) {
                $pid = !empty($this->nper_info['pid']) ? $this->nper_info['pid'] : null;
            }
        }

        if (empty($pid)) {
            show_result('-360', '商品id不能为空', $flag);

        } else if (empty($pid)) {
            $this->log('商品id[' . $pid . ']:不能为空');
            return false;
        }

        //判断当前商品是否已经有正在购买中的,当前有正在进行中的商品
        if ($this->m_gdfc->get_buying_zero_goods_info($pid)) {
            if ($flag) {
                $this->log('来自浏览器,商品id[' . $pid . ']:当前已有正在进行中的商品,无需再次创建');
                return show_result('-370', '当前已有正在进行中的商品,无需再次创建', $flag);
            } else {
                $this->log('来自内部,商品id[' . $pid . ']:当前已有正在进行中的商品,无需再次创建');
                return false;
            }
        }

        $g_info = $this->m_gdfc->get_goods_info_by_id($pid);

        if (empty($g_info)) {
            $this->log('商品id[' . $pid . ']:初始化商品错误!商品不存在或已禁用');
            if ($flag) return false;
            return show_result('-380', '初始化商品错误!商品不存在或已禁用', $flag);

        }

        $data = array(
            "pid" => $g_info['id'],
            "category" => $g_info['category'],
            "mid" => $g_info['mid'],
            "nper_id" => $this->nper_id,
            "deposer_type" => $g_info['deposer_type'],
            "init_times" => $g_info['init_times'],
            "unit_price" => $g_info['unit_price'],
            "min_times" => $g_info['min_times'],
            "sum_times" => $g_info['sum_times'],
            "odd_times" => $g_info['sum_times'],
            "even_times" => $g_info['sum_times'],
            "sec_open" => $g_info['sec_open'],
        );

        $f = true;
        empty($data['pid']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数pid不完整');
        empty($data['category']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数category不完整');
        empty($data['mid']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数mid不完整');
        empty($data['deposer_type']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数deposer_type不完整');
        empty($data['init_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数init_times不完整');
        empty($data['unit_price']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数unit_price不完整');
        empty($data['min_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数min_times不完整');
        empty($data['sum_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数sum_times不完整');
        empty($data['odd_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数odd_times不完整');
        empty($data['even_times']) && ($f = false) && $this->log('商品id[' . $pid . ']:初始化期数参数even_times不完整');


        if (!$f && $flag) {
            return show_result('-390', '参数错误', $flag);
        } else if (!$f) {
            return false;
        }

        if ($flag == 1) {
            //浏览器访问
            if ($this->m_gdfc->init_zero_nper($data)) return show_result('1', '0元抢宝新一期成功', $flag);
            return show_result('-1', '失败', $flag);
        } else {
            //内部访问
            return $this->m_gdfc->init_zero_nper($data);
        }

    }
    //记录日志
    private function log($log)
    {
        $data = array(
            "nper_id" => $this->nper_id,
            "log" => $log
        );
        $this->m_gdfc->log($data);
    }
    public function trigger_open()
    {
        //取已经达到开奖时间,开奖状态为2,没有标记已经检测的记录一条
        $trigger_one = $this->m_gdfc->get_trigger_one();
        empty($trigger_one["id"]) && die_result("-100", "暂无需要开奖的期数");
        //尝试开奖
        $this->init($trigger_one["id"]);
        //1->2
        $this->chk_state_lottery_criteria();
        //2->3
        $result = $this->chk_state_lottery_criteria();
        //写入开奖结果
        if ($result["code"] == "1") {
            $result = "1";

        } else {
            $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        $r = $this->m_gdfc->save_trigger_result($this->nper_id, $result);
        ($r !== false) && die_result("1", "完成开奖,期数:['" . $this->nper_id . "'],结果['" . $result . "']");
        show_result("-1", "完成失败:结果:" . $result);
    }
    /**
     * '-110','期数id错误'
     * '1','当前期数已开奖'
     * '-130','当前期数状态不正确'
     * '-140','购买人数没有达到设定开奖人数'
     * '-150','开奖期数异常'
     * '-160','开奖期数异常'
     * '-170','时间戳计算错误'
     * '-180','进入等待开奖程序失败'
     * '-190','当前期数状态不正确'
     * '-200','依赖彩票期数错误'
     * '-210','彩票还没开奖'
     * '-220','开奖号码获取失败'
     * '-230','时间戳求和不能为空'
     * '-240','购买次数不能为空'
     * '-250','余数不能为空'
     * '-260','固定值获取失败'
     * '-270','获取幸运用户信息失败'
     * '-280','幸运订单信息获取失败'
     * '-290','幸运用户信息不能为空'
     * '-300','幸运用户获取失败'
     * '-310','幸运用户id获取失败'
     * '-320','幸运付款时间获取失败'
     * '-330','幸运订单号获取失败'
     * '-340','开奖成功后初始化下一期失败'
     * '-350','期数信息获取失败'
     * '-360','商品id不能为空'
     * '-370','当前已有正在进行中的商品,无需再次创建'
     * '-380','初始化商品错误!商品不存在或已禁用'
     * '-390','参数错误'
     */
}


