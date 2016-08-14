<?php
namespace app\pay\model;

use think\model\Adv;

Class NotifyModel extends Adv{
    //创建订单
    public function create_order($dt_1, $dt_2)
    {
        $m = $this->db(1);
        $m->startTrans();

        $father = array(
            "order_id" => (string)$dt_1['order_id'],
            "uid" => get_user_id(),
            "username" => get_user_name(),
            "bus_type" => $dt_1['bus_type'] ,
            "name" => $dt_1['name'],
            "num" => $dt_1['num'],
            "price" => $dt_1['price'],
            "pay_status" => '1',
            "close_time" => $dt_1['close_time'],
            "origin" => $dt_1['origin'],
            "create_time" => NOW_TIME,
            "plat_form"=>$dt_1['plat_form']
        );

        //存储父级获取父级订单id
        $pid = $m->table('sp_order_list_parent')->add($father);
        $child = array();
        foreach ($dt_2 as $k => $v) {            //构建子菜单
            $child[$k]['pid'] = $pid;
            $child[$k]["order_id"] = (string)$v['order_id'];
            $child[$k]["bus_type"] = $v['bus_type'];
            $child[$k]["goods_id"] = $v['goods_id'];
            $child[$k]["goods_name"] = $v['goods_name'];
            $child[$k]["nper_id"] = $v['nper_id'];
            $child[$k]["num"] = $v['num'];
            $child[$k]["username"] = get_user_name();
            $child[$k]["uid"] = get_user_id();
            $child[$k]["exec_data"] = $v['exec_data'];
            $child[$k]["money"] = $v['money'];
            $child[$k]["status"] = '1';
            $child[$k]["pay_status"] = '1';
            $child[$k]["promoter_pos"] = $v['promoter_pos'];
            $child[$k]["luck_status"] = 'false';
            $child[$k]["create_time"] = NOW_TIME;
        }
        //获取子级菜单
        $c_list = $m->table('sp_order_list')->addAll($child);

        //获取子表单id
        $c_ids = $m->table('sp_order_list')->where(array("pid" => $pid))->field(array("id","nper_id"))->select();
        $ids = "";
        $nper_ids = "";
        foreach ($c_ids as $k => $v) {
            $ids = $ids . "," . $v["id"];
            $nper_ids = $nper_ids . "," . $v["nper_id"];
        }
        $ids = str_implode($ids);
        $nper_ids = str_implode($nper_ids);

        //更新到父级数据库
        $r = $m->table('sp_order_list_parent')->where(array("id" => $pid))->save(array("order_list" => $ids));
        $r = $r !== false;



        if ($pid && $c_list && $c_ids && $r) {
            $m->commit();
            return $dt_1['order_id'];
        } else {
            $m->rollback();
            return false;
        }
    }

}