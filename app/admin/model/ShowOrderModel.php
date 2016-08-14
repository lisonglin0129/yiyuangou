<?php
namespace app\admin\model;

Class ShowOrderModel extends BaseModel
{
    public $order_model;

    public function __construct()
    {
        $this->order_model = M('show_order',null);
    }

    //获取晒单列表
    public function get_show_order_list($post)
    {

        $m = M('show_order', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_show_order
        WHERE complete = 1 AND  status <> '-1' AND create_time <".time() . $post->wheresql .
            " ORDER BY  create_time desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $goods = M('goods',null);

        foreach($info as $key=>$value) {
            $info[$key]['goods_name'] = $goods->field('name')->where(array('id' =>$value['goods_id']))->find()['name'];
            $info[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
        }


        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }
    //机器人晒单
    public function rt_update_win_record($post){
      $pid=$order_id=$goods_id=$nper_id=$luck_num=$uid=$username=$pic_list=$title=$content=$create_time=$complete=null;
        extract($post);
        $data=[
            'pid'=>$pid,
            'order_id'=>$order_id,
            'goods_id'=>$goods_id,
            'nper_id'=>$nper_id,
            'luck_num'=>$luck_num,
            'uid'=>$uid,
            'username'=>$username,
            'pic_list'=>trim($pic_list,','),
            'title'=>$title,
            'content'=>$content,
            'create_time'=>$create_time,
            'complete'=>$complete
        ];
        return $this->order_model->add($data);
    }


    //获取晒单详情
    public function get_info_by_id($id)
    {
        $goods = M('goods',null);
        $show_order_info = $this->order_model->where(array("id" => $id))->find();
        $show_order_info['pic_list'] = $this->get_more_image_src($show_order_info['pic_list']);
        $show_order_info['goods_name'] = $goods->field('name')->where(array('id' =>$show_order_info['goods_id']))->find()['name'];
        $show_order_info['create_time'] = date('Y-m-d H:i:s',$show_order_info['create_time']);
        return $show_order_info;
    }

    public function del_select($ids) {
        return $this->order_model->where(array('id'=>['in',$ids]))->save(array('status'=>-1));
    }



}