<?php
/**
 * Created by PhpStorm.
 * User: liuchao
 * Date: 16/6/25
 * Time: 下午2:54
 */
namespace app\core\model;
class PacketModel extends CommonModel
{
    private $m;

    public function __construct()
    {
        parent::__construct();
        $this->m = M('packet');
    }
    //获取未使用的红包

    public function get_no_send_packets($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
        FROM  sp_packet
        WHERE  status=1 AND is_send=0 AND end_time >=".time()." AND uid=".get_user_id() . $post->wheresql .
            " ORDER BY id DESC" . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //获取过期或者使用过的红包

    public function get_used_packets($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
        FROM  sp_packet
        WHERE  status=1 AND (is_send=1 OR (is_send=0 AND end_time <".time().")) AND uid=".get_user_id() . $post->wheresql .
            " ORDER BY id DESC" . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    public function get_packet($id){
        return $this->m->where(['id'=>intval($id)])->find();
    }


}