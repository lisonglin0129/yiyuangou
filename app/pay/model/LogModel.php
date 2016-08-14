<?php
namespace app\pay\model;
/**
 * Created by PhpStorm.
 * User: phil
 * Date: 2016/4/12
 * Time: 10:52
 */

Class LogModel
{
    private $m_log;

    public function __construct()
    {
        $this->notify_log = M("notify", "log_");
    }

    public function log_notify($order_id, $log_text)
    {
        $data = array(
            "order_id" => $order_id,
            "log" => $log_text,
            "create_time" => date("Y-m-d H:i:s"),
        );
        $this->notify_log->add($data);
    }

    /**
     * 余额操作日志
     * @param string $type 日志记录类型:default默认  add充值 reduce扣费 back退款
     * @param float $money 操作金额
     * @param string $uid 用户id
     * @param string $order_id 订单id
     * @param string $desc 详情
     * @return bool
     */
    public function balance_log($type = 'default', $money = 0.00, $desc = '', $uid = '0', $order_id = '0')
    {
        $m_log = M('balance', 'log_');
        $data = array(
            "uid" => $uid,
            "order_id" => $order_id,
            "type" => $type,
            "money" => $money,
            "desc" => $desc,
            "create_time" => date("Y-m-d H:i:s")
        );
        return $m_log->add($data);
    }

    /**
     * 业务流程操作日志
     * @param int $uid 用户id
     * @param string $order_id 订单id
     * @param string $desc 详情
     * @return bool
     */
    public function open_log($desc = '', $uid = 0, $order_id = '')
    {
        $m_log = M('open', 'log_');
        $data = array(
            "uid" => $uid,
            "order_id" => $order_id,
            "desc" => $desc,
            "create_time" => date("Y-m-d H:i:s")
        );
        return $m_log->add($data);
    }
}