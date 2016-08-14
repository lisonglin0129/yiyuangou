<?php
namespace app\admin\model;

Class OrderList extends BaseModel
{
    public $order_model;

    public function __construct()
    {
        $this->order_model = M('order_list_parent',null);
    }

    //获取订单列表
    public function get_order_list($post)
    {

        $m = M('order_list_parent', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_order_list_parent
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);


        $users = M('users');

        //查询用户名
        if(!empty($info)) {
            foreach($info as $key=>$value) {
                $info[$key]['username'] = $users->field('username')->where(array("id"=>$value['uid']))->find()['username'];
                $info[$key]['pay_time'] = microtime_format($value['pay_time'],3,'Y-m-d H:i:s');
            }
        }


        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }

    //获取订单列表
    public function get_son_order_list($post)
    {

        $m = M('order_list', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_order_list
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);


        $users = M('users');

        //查询用户名
        if(!empty($info)) {
            foreach($info as $key=>$value) {
                $info[$key]['username'] = $users->field('username')->where(array('id'=>$value['uid']))->find()['username'];
                $info[$key]['pay_time'] = microtime_format($value['pay_time'],3,'Y-m-d H:i:s');
            }
        }


        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }


    //删除订单
    public function del_users($id)
    {
        return $this->users_model->where(array("id" => $id))->save(array("status" => "-1"));
    }

    //获取总参与人数
    public function get_total_join() {
        $num =  M('order_list','sp_')->field('SUM(success_num) num')->where(array('bus_type'=>'buy','status'=>1,'dealed'=>'true'))->select();
        return $num;
    }



}