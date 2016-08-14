<?php
namespace app\pay\model;

use think\model\Adv;

Class PayModel extends Adv
{

    public function __construct()
    {
        parent::__construct();
    }

    //根据订单 order_id获取父级订单详情
    public function get_p_order_info_by_filed($name, $value)
    {
        $m_order = M('order_list_parent', 'sp_');
        return $m_order->where(array($name => $value))->find();
    }

    //根据字段获取用户信息
    public function get_user_info_by_filed($filed, $value)
    {
        $m_user = M('users', 'sp_');
        $sql = 'SELECT u.*,i.img_path,i.thumb_path,i.name image_name FROM sp_users u
                LEFT JOIN sp_image_list i ON u.user_pic = i.id
                WHERE u.' . $filed . ' = "' . $value . '"';
        $info = $m_user->query($sql);
        return $info[0];
    }

    //写入支付信息
    public function write_pay_info($post){
        extract($post);
        if(empty($order_id))return false;
        $m_order_list = M("order_list_parent","sp_");
        $data = array(
            "plat_form" => empty($plat_form) ? "" : $plat_form,
            "pay_sn" => empty($pay_sn) ? "" : $pay_sn,
            "pay_time" => empty($pay_time) ? "" : $pay_time,
            "pay_time_format" => empty($pay_time_format) ? "" : $pay_time_format
        );
        return $m_order_list->where(array("order_id"=>$order_id))->save($data);
    }
}