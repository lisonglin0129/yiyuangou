<?php
namespace app\core\model;

use think\Model;
use think\model\Adv;

/**支付model*/
Class PayModel extends Adv
{
    public function __construct()
    {
    }
    //获取购物车列表
    public function get_cart_list($ids=''){
        $where=[];
        $where['c.uid']=get_user_id();
        $where['n.status'] =1;
        if($ids){
            $where['c.id']=['in',$ids];
        }
        $c_model=M('shop_cart');
        $list=$c_model->alias('c')->field('c.id,sum(c.num) as num,c.nper_id,c.join_type,n.unit_price,d.name as deposer_type,g.name as g_name,n.participant_num,n.odd_join_num,n.even_join_num,i.img_path as i_img_path,g.id as g_id,n.sum_times,n.nper_type,g.price,n.min_times')
            ->join('sp_nper_list n on n.id=c.nper_id','left')
            ->join('sp_goods g on g.id=n.pid','left')
            ->join('sp_image_list i on i.id=g.index_img','left')
            ->join('sp_deposer_type d on d.id=g.deposer_type','left')
            ->where($where)->group('c.nper_id,c.join_type')->select();
        return $list;
    }
    //根据购物车的id获取商品列表
    public function get_cart_list_by_nper_ids($ids)
    {
        $m_nper = M('nper_list', 'sp_');
        $sql = 'SELECT n.id nper_id,n.unit_price,
                g.name g_name,
                i.img_path i_img_path
                FROM sp_nper_list n
                LEFT JOIN sp_goods g ON g.id = n.pid
                LEFT JOIN sp_image_list i ON i.id = g.index_img
                WHERE n.id IN(' . $ids . ') AND n.status=1';
        return $m_nper->query($sql);
    }

    //删除购物车车nper_id
    public function del_cart_by_nper_id($nper_id)
    {
        if (is_user_login()) {
            $m_cart = M('shop_cart', 'sp_');
            return $m_cart->where(array('id' => $nper_id))->delete();
        } else {
            $arr = cookie('local_cart');
            if (!empty($arr[$nper_id])) {
                unset($arr[$nper_id]);
                cookie('local_cart', $arr);
                return true;
            }
            return false;
        }
    }

    //根据id获取商品期数详情
    public function get_nper_info_by_ids($ids)
    {
        $m_nper = M('nper_list', 'sp_');
        return $m_nper->where("id IN (" . $ids . ")")->select();
    }

    //获取用户购物车商品列表
    public function get_user_cart_list()
    {
        $m_cart = M('shop_cart', 'sp_');
        return $m_cart->where(array("uid" => get_user_id()))->select();
    }


    //添加商品列表到购物车
    public function add_list_to_cart($post)
    {
        $m_cart = M('shop_cart', 'sp_');
        $arr = array();
        foreach ($post as $k => $v) {
            if (is_numeric($k) && $v && is_numeric($v)) {
                $tmp = array(
                    "uid" => get_user_id(),
                    "num" => $v,
                    "nper_id" => $k,
                    "create_time" => NOW_TIME
                );
                array_push($arr, $tmp);
            }
        }

        if (!empty($arr)) {
            return $m_cart->addAll($arr);
        }
        return false;
    }

    //删除用户下的购物车的内容
    public function clear_user_cart()
    {
        $m_cart = M('shop_cart', 'sp_');
        return $m_cart->where(array('uid' => get_user_id()))->delete();
    }

    //过滤掉列表中失效的内容
    public function fiter_useful_cart($arr)
    {
        $ids = '';
        foreach ($arr as $k => $v) {
            $ids = $ids . ',' . $k;
        }
        $ids = str_implode($ids);
        $m_nper = M('nper_list', 'sp_');
        $info = $m_nper->where('status = 1 AND sum_times >= participant_num AND  id IN (' . $ids . ')')->select();

        $new_arr = array();
        foreach ($info as $k => $v) {
            $last_num = (int)$v['sum_times'] - (int)$v['participant_num'];
            $n_id = $v['id'];
            if (isset($arr[$n_id])) {
                if ($last_num < $arr[$n_id]) {
                    $new_arr[$n_id] = $last_num;
                } else {
                    $new_arr[$n_id] = $arr[$n_id];
                }

            }
        }


        return $new_arr;
    }

    /*获取购物车列表,格式为array(nper_id=>num)*/
    public function get_cart_list_info($post)
    {
        $ids = '';
        foreach ($post as $k => $v) {
            $ids = $ids . ',' . $k;
        }
        $ids = str_implode($ids);


        $sql = 'SELECT n.id nper_id,n.unit_price,n.min_times,n.max_times,n.participant_num,n.sum_times,
                d.name deposer_type,
                g.id g_id,g.name g_name,g.price,
                i.img_path i_img_path 
                FROM sp_nper_list n
                LEFT JOIN sp_deposer_type d ON d.id = n.deposer_type
                LEFT JOIN sp_goods g ON g.id = n.pid
                LEFT JOIN sp_image_list i ON i.id = g.index_img
                WHERE n.id IN(' . $ids . ')';

        $m_nper = M('nper_list', 'sp_');
        $list = $m_nper->query($sql);

        foreach ($list as $k => $v) {
            $last_num = $v['sum_times'] - $v['participant_num'];
            if ($last_num >= 1) {

                $nper_id = $v['nper_id'];
                $list[$k]["num"] = empty($post[$nper_id]) ? 1 : (int)$post[$nper_id];
                $list[$k]["last_num"] = empty($last_num) ? 1 : $last_num;
                //超出最大限制等于最大限制
                $list[$k]["num"] = $list[$k]["num"] > $list[$k]["max_times"] ? $list[$k]["max_times"] : $list[$k]["num"];
            } else {

                unset($list[$k]);
            }
        }

        return $list;
    }

    //根据nperid删除购物车内容
    public function del_cart_by_ids($ids)
    {
        $m_cart = M('shop_cart', 'sp_');
        return $m_cart->where("uid = " . get_user_id() . " AND id IN (" . $ids . ")")->delete();
    }

    //更新购物车数量
    public function update_cart_num($nper_id, $num)
    {
        $m_cart = M('shop_cart', 'sp_');
        $r = $m_cart->where("uid = " . get_user_id() . " AND nper_id =" . $nper_id)->save(array('num' => $num));
        return $r !== false;
    }

    //获取限制的几个正在开奖的商品信息,根据销售数量排序
    public function get_nper_list_ings_limit($num = 5)
    {

        $m_nper = M('nper_list', 'sp_');
        $total =(int) $m_nper->where('status=1')->count()-5;
        $rand = rand(0,$total);
        $sql = "SELECT n.id nper_id,n.unit_price,n.min_times,n.participant_num,n.sum_times,
                d.name deposer_type,
                g.id g_id,g.name g_name,g.price,
                i.img_path i_img_path
                FROM sp_nper_list n
                LEFT JOIN sp_deposer_type d ON d.id = n.deposer_type
                LEFT JOIN sp_goods g ON g.id = n.pid
                LEFT JOIN sp_image_list i ON i.id = g.index_img
                WHERE n.status=1 AND g.status = 1 ORDER BY n.participant_num DESC LIMIT {$rand},{$num}";

        return $m_nper->query($sql);
    }

    //创建订单
    public function create_order($dt_1, $dt_2)
    {
        $m = $this->db(1);
        $m->startTrans();

        $father = array(
            "order_id" => (string)$dt_1['order_id'],
            "uid" => get_user_id(),
            "username" => get_user_name(),
            "bus_type" => $dt_1['bus_type'],
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
            $child[$k]["join_type"]=!empty($v['join_type'])?$v['join_type']:'';
        }
        //获取子级菜单
        $c_list = $m->table('sp_order_list')->addAll($child);

        //获取子表单id
        $c_ids = $m->table('sp_order_list')->where(array("pid" => $pid))->field(array("id", "nper_id","join_type"))->select();
        $ids = "";
        $carts = [];
        foreach ($c_ids as $k => $v) {
            $ids = $ids . "," . $v["id"];
          //  $nper_ids = $nper_ids . "," . $v["nper_id"];
            $carts[]=[
                'nper_id'=>$v['nper_id'],
                'join_type'=>!empty($v['join_type'])?$v['join_type']:0
            ];
        }
        $ids = str_implode($ids);
      //  $nper_ids = str_implode($nper_ids);

        //更新到父级数据库
        $r = $m->table('sp_order_list_parent')->where(array("id" => $pid))->save(array("order_list" => $ids));
        $r = $r !== false;

        //删除用户购物车中期数内容
        if (!empty($carts)) {
            //file_put_contents('./menu.txt',var_export($carts,true));
            $this->del_carts($carts);
        }

        if ($pid && $c_list && $c_ids && $r) {
            $m->commit();
            return $dt_1['order_id'];
        } else {
            $m->rollback();
            return false;
        }
    }
    //删除购物骑车
    public function del_carts($carts){
        $m_cart = M('shop_cart', 'sp_');
        foreach($carts as $v){
            $v['uid']=get_user_id();
            $m_cart->where($v)->delete();
        }
    }
}