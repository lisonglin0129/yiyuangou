<?php
namespace app\admin\model;

Class OriginModel extends BaseModel
{
    public $Origin_model;

    public function __construct()
    {
        $this->Origin_model = M('origin', null);
    }

    //获取订单列表
    public function get_origin_list($post)
    {

        $m = M('origin', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_origin  WHERE 1 " . $post->wheresql .
            " ORDER BY id asc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }

    //获取一条信息
    public function get_one_data($id)
    {
        return $this->Origin_model->where(array('id' =>$id))->find();
    }

    //执行更新操作
    public function update($post)
    {
        $name = $origin_url = $code = null;
        extract($post);
        $data = array(
            "name" => trim($name),
            "code" => trim($code),
            "num" => 0,
            "recharge" => 0,
            "pay" => 0,
        );
        //执行之前先判断是否有重复数据
        $sql = "select * from sp_origin where name = '" . $post['name'] . "'";
        $check_res = $this->Origin_model->query($sql);
        if ($check_res) {
            return 2;
        }
        if ($post['type'] == "edit") {
            $res = $this->Origin_model->where(array("id" => $post['id']))->save($data);
            return $res !== false;
        } else if ($post['type'] == "add") {
            $res = $this->Origin_model->add($data);
        } else {
            return false;
        }
        return $res;
    }

    public function del($id)
    {
        return $this->Origin_model->where(array("id" => $id))->delete();
    }

    //一键刷新
    public function flush_data()
    {
        //注册人数查询
        $sql = " SELECT o.id,o.code,COUNT(s.id) as num FROM sp_origin o ";
        $sql .= "LEFT JOIN sp_users s on o.code = s.origin ";
        $sql .= " GROUP BY o.code";
        $sql .= " ORDER BY o.id";

        $res = $this->Origin_model->query($sql);

        //消费金额查询
        $sql = " SELECT o.id,o.code,SUM(l.price) as money,l.bus_type FROM sp_origin o ";
        $sql .= " LEFT JOIN sp_users s on o.code = s.origin ";
        $sql .= " LEFT JOIN sp_order_list_parent l on s.id = l.uid AND l.pay_status = 3";
        $sql .= " GROUP BY o.code,l.bus_type";
        $sql .= " ORDER BY o.id ";

        $res2 = $this->Origin_model->query($sql);


        foreach ($res as $key => $val) {
            $data = array();
            $data['id'] = intval($val['id']);
            $data['num'] = intval($val['num']);
            $data['pay']=0;
            $data['recharge']=0;
            foreach ($res2 as $key2 => $val2) {
                if ($val['id'] == $val2['id']) {
                    if ($val2['bus_type'] == "buy") {
                        $data['pay'] += floatval($val2['money']);
                    } elseif ($val2['bus_type'] == "recharge") {
                        $data['recharge'] += floatval($val2['money']);
                    } else {
                        $data['recharge'] += floatval(0);
                        $data['pay'] += floatval(0);
                    }
                }
            }

            $sql = "UPDATE `sp_origin` SET `num`=" . $data['num'] . ",`pay`=" . $data['pay'] . ",`recharge`=" . $data['recharge'] . " WHERE id =" . $data['id'];
            $this->Origin_model->execute($sql);
        }
    }


}