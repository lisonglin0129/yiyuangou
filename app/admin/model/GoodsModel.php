<?php
namespace app\admin\model;

use app\core\controller\Gdfc;

Class GoodsModel
{
    private $m;

    public function __construct()
    {
        $this->m = M('goods', 'sp_');
    }
    //获取0元抢宝商品
    public function get_zero_goods_list($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  g.*,i.img_path,de.name deposer_name,ca.name category_name
        FROM  sp_goods g
        LEFT JOIN sp_image_list i ON i.id = g.index_img
        LEFT JOIN sp_deposer_type de ON de.id = g.deposer_type
        LEFT JOIN sp_category_list ca ON ca.id = g.category
        WHERE  g.status <> '-1' AND g.is_zero='1' " . $post->wheresql .
            " ORDER BY g.id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
   //根据商家id获取用户id
    public function get_uids_by_shop_id($shop_id,$type=1){
        $res=[];
        $u_ids=$this->m->alias('g')->distinct(true)->field('o.uid')
            ->join('sp_order_list o ON o.goods_id=g.id ')
            ->where(['g.shop_id'=>$shop_id,'g.status'=>1])->select();
        $u_ids_copy=M('users','sp_')->field('id as uid')->where(['shop_id'=>$shop_id,'status'=>['neq','-1']])->select();
        $u_ids=$this->get_district_uids($u_ids,$u_ids_copy);
        foreach($u_ids as $v){
            $res[]=$v['uid'];
        }
        if($type==2){
            return $res;
        }
        return implode(',',$res);
    }
    //根据产品名称 和商家id获取用户id'
    public function get_uids_by_gname_shop_id($keyowrds,$shop_id){
        $res=[];
        $u_ids=$this->m->alias('g')->distinct(true)->field('o.uid')
            ->join('sp_order_list o ON o.goods_id=g.id ')
            ->where(['g.shop_id'=>$shop_id,'g.status'=>1,'g.name'=>['like','%'.$keyowrds.'%']])->select();
        foreach($u_ids as $v){
            $res[]=$v['uid'];
        }
        return implode(',',$res);
    }
    //根据商家id获取产品id
    public function get_gids_by_shop_id($shop_id){
        $res=[];
        $goods_ids=$this->m->field('id')->where(['shop_id'=>['in',$shop_id]])->select();
        foreach($goods_ids as $v){
            $res[]=$v['id'];
        }
        return implode(',',$res);
    }
     //获取所以商品
    public function get_all_goods(){
        return $this->m->field('id,name')->where(['status'=>1])->select();
    }
    //列表
    public function get_list($post)
    {
        $m = M('goods', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  g.*,i.img_path,de.name deposer_name,ca.name category_name
        FROM  sp_goods g
        LEFT JOIN sp_image_list i ON i.id = g.index_img
        LEFT JOIN sp_deposer_type de ON de.id = g.deposer_type
        LEFT JOIN sp_category_list ca ON ca.id = g.category
        WHERE  1=1 " . $post->wheresql .
            " ORDER BY g.id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        //获取手机端产品详情链接
        foreach($info as $k=>$v){
            $nper=$this->get_last_nper_info_by_gid($v['id']);
            $info[$k]['last_nper_id']=$nper['id'];
        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //获取最新一期商品详情
    public function get_last_nper_info_by_gid($gid)
    {
        $result = $this->m
            ->table('sp_goods g')
            ->field('SQL_CALC_FOUND_ROWS n.id,n.pid,g.name,g.id gid,n.sum_times,n.participant_num,(n.sum_times-n.participant_num) remain,i.img_path,g.sub_title,n.init_times,n.unit_price,n.min_times')
            ->join('sp_nper_list n ON g.id = n.pid', 'LEFT')
            ->join('sp_image_list i ON i.id = g.index_img', 'LEFT')
            ->where(['g.id' => $gid])
            ->order('n.id desc')
            ->find();
        return $result;
    }
    public function get_info_by_id($id)
    {
        return $this->m->where(array('id' => $id/*, 'status' => array('neq', '-1')*/))->find();
    }

    //添加菜单
    public function update($post)
    {
        $status = $name = $sub_title = $category = $sec_open=$mid = $deposer_type = $init_times = $unit_price = $min_times = $sum_times = $max_times = $num = $index_img = $pic_list =  $price = $detail = $remarks =$shop_id=$is_zero=$reward_type=$reward_data=$is_packet=$packet_num=$packet_money= null;
        extract($post);
        $data = array(
            "status" => $status,
            "name" => $name,
            "sub_title" => $sub_title,
            "category" => $category,
            "mid" => $mid,
            "deposer_type" => $deposer_type,
            "init_times" => $init_times,
            "unit_price" => $unit_price,
            "min_times" => $min_times,
            "sum_times" => $sum_times,
            "max_times" => $max_times,
            "num" => $num,
            "index_img" => $index_img,
            "pic_list" => $pic_list,
            "price" => $price,
            "buy_price" => $price,
            "detail" => $detail,
            "remarks" => $remarks,
            "shop_id"=>empty($shop_id)?0:intval($shop_id),
            "is_zero"=>empty($is_zero)?0:1,
            "reward_type"=>empty($reward_type)?0:$reward_type,
            "reward_data"=>empty($reward_data)?0:$reward_data,
            "sec_open" => $sec_open,
            "is_packet"=>!empty($is_packet)?$is_packet:0,
            "packet_num"=>!empty($packet_num)?intval($packet_num):0,
            "packet_money"=>!empty($packet_money)?floatval($packet_money):0
        );
        $gdfc = new Gdfc();
        if (!empty($id) && is_numeric($id)) {
            $data["update_time"] = NOW_TIME;
            $r = $this->m->where(array("id" => $id))->save($data);
            if(!empty($is_zero)){
                $gdfc->init_zero_nper($id);
            }
            return $r !== false;
        } else {
            $data["create_time"] = NOW_TIME;
            $r = $this->m->add($data);
            if ($r) {
                //触发第一期
                $gdfc->init_new_nper($r);
                if(!empty($is_zero)){
                    $gdfc->init_zero_nper($r);
                }
                return $r;
            }
            return false;
        }
    }

    //获取夺宝类型
    public function get_deposer_type_list()
    {
        $m = M('deposer_type', 'sp_');
        return $m->select();
    }

    //获取夺宝信息
    public function get_deposer_info_by_id($id)
    {
        $m = M('deposer_type', 'sp_');
        return $m->where(array("id" => $id))->find();
    }

    //获取夺宝信息
    public function get_plat_list($type = 'plat')
    {
        $m = M('category', 'sp_');
        return $m->where(array("type" => $type))->order('orders desc')->select();
    }

    public function get_goods_list($category='')
    {
        $m = M('goods', 'sp_');
        return $m->where(array("category" => $category,'status'=>'1'))->select();
    }


    //获取图片列表
    public function get_img_list($ids)
    {
        $m_img = M("image_list", "sp_");
        return $m_img->where("status =1 AND id IN(" . $ids . ")")->select();
    }


    //删除订单
    public function del($id)
    {
        return $this->m->where(array("id" => $id))->save(array("status" => "-1"));
    }

    public function del_select($ids)
    {
        $res1 =  $this->m->where(array("id" => ['in',$ids]))->save(array("status" => "-1"));
        $res2 = $this->del_select_nper($ids);
        return ($res1 !== false) && ($res2 !== false);
    }

    private function del_select_nper($ids) {
        $m = M('nper_list');
        return $m->where(array('pid'=>['in',$ids],'status'=>1))->save(array('status'=>'-1'));


    }

    //获取商品分类下的商品
    public function get_in_cate_goods($cate_id,$field='*') {
        return $this->m->field($field)->where(array('category'=>$cate_id,'status'=>1))->select();

    }

    public function get_field_by_gid($id,$field='*') {
        $data = $this->m->field($field)->where(array('id'=>$id))->find();
        if ( is_null($data) )
            return '';
        return $data;


    }



    //去重uid
    private function get_district_uids($a,$b){
        $arr = array_merge($a,$b);
        $key = 'uid';
        $tmp_arr = array();
        foreach($arr as $k => $v)
        {
            if(in_array($v[$key], $tmp_arr))
            {
                unset($arr[$k]);
            }
            else {
                $tmp_arr[] = $v[$key];
            }
        }
       return $arr;
    }
}