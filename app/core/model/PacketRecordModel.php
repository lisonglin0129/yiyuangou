<?php
/**
 * Created by PhpStorm.
 * User: liuchao
 * Date: 16/6/25
 * Time: 下午8:21
 */
namespace app\core\model;
class PacketRecordModel{
    private  $m;
    public function __construct(){
        $this->m=M('packet_record');
    }
    //获取记录总数量
    public function get_num($packet_id){
        return $this->m->where(['packet_id'=>$packet_id,'status'=>1])->count();
    }
    //获取记录总金额
    public function get_total($packet_id){
        return $this->m->where(['packet_id'=>$packet_id,'status'=>1])->sum('money');
    }
    //获取记录列表
    public function get_records($packet_id){
      return $this->m->where(['packet_id'=>$packet_id,'status'=>1])->limit(0,10)->order('create_time desc')->select();
    }
    public function ajax_records($packet_id,$offset){
        return $this->m->where(['packet_id'=>$packet_id,'status'=>1])->limit($offset,10)->order('create_time desc')->select();
    }
    //添加记录
    public function add_record($data){
        return $this->m->add($data);
    }
    //验证是否领取
    public function check_record($packet_id,$openid){
        return $this->m->where(['packet_id'=>$packet_id,'openid'=>$openid])->find();
    }
}