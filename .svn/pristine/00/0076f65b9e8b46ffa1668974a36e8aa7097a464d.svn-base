<?php
namespace app\core\model;
use think\model\Adv;

/**
 * Created by PhpStorm.
 * User: phil
 * Date: 2016/4/20
 * Time: 21:41
 */
Class RtModel extends Adv
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取用户信息
    public function get_user_info($uid)
    {
        $m_user = M("users", "sp_");
        return $m_user->where(array("id" => $uid))->find();
    }

    //获取期数信息
    public function get_nper_info($nper_id)
    {
        $m_user = M("nper_list", "sp_");
        return $m_user->where(array("id" => $nper_id))->find();
    }

    //获取商品信息
    public function get_goods_info_by_id($id){
        $m_user = M("goods", "sp_");
        return $m_user->where(array("id" => $id))->find();
    }


    //创建订单
    public function create_order($dt_1, $dt_2)
    {
        $m = $this->db(1);
        $m->startTrans();

        $father = array(
            "order_id" => (string)$dt_1['order_id'],
            "uid" => $dt_1['uid'],
            "username" => $dt_1['username'],
            "bus_type" => $dt_1['bus_type'] ,
            "name" => $dt_1['name'],
            "num" => $dt_1['num'],
            "price" => $dt_1['price'],
            "pay_status" => '1',
            "close_time" => $dt_1['close_time'],
            "origin" => $dt_1['origin'],
            "create_time" => NOW_TIME,
        );

        //存储父级获取父级订单id
        $pid = $m->table('sp_order_list_parent')->add($father);
        $child['pid'] = $pid;
        $child["order_id"] = (string)$dt_2['order_id'];
        $child["bus_type"] = $dt_2['bus_type'];
        $child["goods_id"] = $dt_2['goods_id'];
        $child["goods_name"] = $dt_2['goods_name'];
        $child["nper_id"] = $dt_2['nper_id'];
        $child["num"] = $dt_2['num'];
        $child["username"] = $dt_2['username'];
        $child["uid"] = $dt_2['uid'];
        $child["exec_data"] = $dt_2['exec_data'];
        $child["money"] = $dt_2['money'];
        $child["status"] = '1';
        $child["pay_status"] = '1';
        $child["promoter_pos"] = $dt_2['promoter_pos'];
        $child["luck_status"] = 'false';
        $child["create_time"] = NOW_TIME;
        $child["join_type"] = $dt_2['join_type'];
        //获取子级菜单
        $c_list = $m->table('sp_order_list')->add($child);

        //获取子表单id
        $c_ids = $m->table('sp_order_list')->where(array("pid" => $pid))->field(array("id","nper_id"))->find();
        $ids = $c_ids['id'];

        //更新到父级数据库
        $r = $m->table('sp_order_list_parent')->where(array("id" => $pid))->save(array("order_list" => $ids));
        $r = ($r !== false);

        if ($pid && $c_list && $c_ids && $r) {
            $m->commit();
            return $dt_1['order_id'];
        } else {
            $m->rollback();
            return false;
        }
    }

    //充值用户余额
    public function add_money($uid=0,$money=0){
        $m = $this->db(1);
        $m->startTrans();

        $r = $this->table("sp_users")->where(array("id"=>$uid))->setInc("money",$money);
        if ($r) {
            $m->commit();
            return true;
        } else {
            $m->rollback();
            return false;
        }
    }

    //根据商品获得最新期数
    public function get_nper_id_by_goods($goods_id,$nper_type=1){
        $m_nper = M("nper_list", "sp_");
        return $img = $m_nper->where(array("pid"=>$goods_id,"status"=>"1",'nper_type'=>$nper_type))->find();
    }
}