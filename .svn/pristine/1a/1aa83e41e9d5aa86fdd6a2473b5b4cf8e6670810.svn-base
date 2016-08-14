<?php
namespace app\admin\model;

Class NperListModel extends BaseModel
{
    public $nper_list_model;

    public function __construct()
    {
        $this->nper_list_model = M('nper_list',null);
    }

    //获取订单列表
    public function get_nper_list($post)
    {

        $m = M('nper_list', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_nper_list
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);
        $goods = M('goods');
        $deposer_type = M('deposer_type');
        //格式字段，方便前端显示
        if(!empty($info)) {
            foreach($info as $key=>$value) {
                $info[$key]['goods_name'] = $goods->field('name')->where(array("id"=>$value['pid']))->find()['name'];
                if($value['open_time']) {
                    $info[$key]['open_time'] = date('Y-m-d H:i:s',$value['open_time']);
                }
                $info[$key]['deposer_name'] = $deposer_type->field('name')->where(array('id' => $value['deposer_type']))->find()['name'];
                if($value['luck_num']) {
                    $info[$key]['luck_num'] = num_base_mask(intval($value['luck_num']),1,0);
                }
                //判断是否需要手动出发开奖
                if($value['status'] == 2 && $value['open_time'] < time()) {
                    $info[$key]['trigger'] = true;
                }else{
                    $info[$key]['trigger'] = false;
                }
            }
        }
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    public function del($id)
    {
        return $this->nper_list_model->where(array("id" => $id))->save(array("status" => "-1"));
    }

    public function del_form_goods($id)
    {
        $this->nper_list_model->where(array('pid'=>$id,'status'=>1))->save(array('status'=>'-1'));

    }

    /**
     * 获得指定用户的期数
     */
    public function get_set_user_nper($post) {
        $m = M('nper_list', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS n.*,g.name,u.username,u.nick_name,g.id goods_id
        FROM  sp_nper_list n
        JOIN sp_goods g ON g.id = n.pid
        JOIN sp_users u ON u.id = n.rt_point_uid
        WHERE  n.rt_point_uid IS NOT NULL AND n.status=1 AND rt_point_uid <> ''
AND rt_point_uid <> 0".$post->wheresql.$post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $res =  $m->query($sql);
        $count = $m->query($sql_count);
        $info['list'] = $res;
        $info['count'] = $count[0]['num'];
        return $info;

    }

    /**
     * 删除指定中奖用户
     * @param $id
     * @return bool
     * @throws \think\Exception
     */
    public function del_set_user($id) {
        $data = array(
            'rt_point_uid' => null
        );

        return $this->nper_list_model->where(array('id'=>$id))->save($data);

    }

    public function del_select($ids) {
        return $this->nper_list_model->where(array("id"=>['in',$ids]))->save(array("status" => "-1"));

    }



}