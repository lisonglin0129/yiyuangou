<?php

namespace app\mobile\model;

use app\core\model\CategoryModel;
use think\Model;
use think\model\Adv;
use app\core\model\OrderModel;

class OrderList extends Adv
{

    //获奖商品状态
    private $prize_status = array('get_goods', 'confirm_address', 'send_goods', 'confirm_receipt', 'already_sign');
    private $prize_status_info = array('获得商品', '确认收货地址', '商品派发', '确认收货', '已签收');

    /**
     * 个人夺宝记录
     * @param $uid
     * @return array
     */
    public function person_indiana($type = "ing", $page = 0)
    {
        $uid = session('user')['id'];
        $map = array();
        $map['o.uid'] = $uid;
        $map['n.status'] = 1;
        $map['o.dealed'] = 'true';

        $page = $page * 10;


        if ($type == "ing") {
            //查询正在进行中的商品，即status为1或者2
            $ing_page = $page * 10;
            $indianaArrayIng = $this->get_ing_goods($map, $page);
            //处理数据
            $indianaArrayIng = $this->deal_ing_goods($indianaArrayIng);
            $result_array = $indianaArrayIng;
        } else if( $type == 'doing' ) {
            $result_array = $this->get_doing_goods($map, $page);
        } else {
            //查询已经揭晓出来的商品
            $indianaArrayDone = $this->get_done_goods($uid, $page);
            //处理数据
            $indianaArrayDone = $this->deal_done_goods($indianaArrayDone);
            $result_array = $indianaArrayDone;
        }

        return $result_array;
    }

    //处理数据
    public function deal_done_goods($indianaArrayDone)
    {
        foreach ($indianaArrayDone as $key => $value) {
            //转化号码格式

            if ($value['nper_type'] == 1) {
                $indianaArrayDone[$key]['progress'] = $value['status'] == '1' ? round((float)$value['participant_num'] / (float)$value['sum_times'], 2) * 100 : 0;
            } else {

                if ($value['join_type'] == 1) {
                    $indianaArrayDone[$key]['progress'] = $value['status'] == '1' ? round((float)$value['odd_join_num'] / (float)$value['sum_times'], 2) * 100 : 0;
                } else {
                    $indianaArrayDone[$key]['progress'] = $value['status'] == '1' ? round((float)$value['even_join_num'] / (float)$value['sum_times'], 2) * 100 : 0;
                }
            }
            $indianaArrayDone[$key]['luck_num'] = num_base_mask(intval($indianaArrayDone[$key]['luck_num']), 1);
            $indianaArrayDone[$key]['open_time'] = date("Y-m-d H:i:s", $indianaArrayDone[$key]['open_time']);

        }

        return $indianaArrayDone;
    }


    public function deal_ing_goods($indianaArrayIng)
    {
        //处理数据
        foreach ($indianaArrayIng as $key => $value) {
            if ($value['nper_type'] == 1) {
                $indianaArrayIng[$key]['progress'] = ($value['status'] == '1') ? (round((float)$value['participant_num'] / (float)$value['sum_times'], 2) * 100) : 0;
            } else {
                if ($value['join_type'] == 1) {
                    $indianaArrayIng[$key]['progress'] = $value['status'] == '1' ? round((float)$value['odd_join_num'] / (float)$value['sum_times'], 2) * 100 : 0;
                } else {
                    $indianaArrayIng[$key]['progress'] = $value['status'] == '1' ? round((float)$value['even_join_num'] / (float)$value['sum_times'], 2) * 100 : 0;
                }
            }

        }
        return $indianaArrayIng;
    }


    //获取正在进行的商品列表
    public function get_ing_goods($condition, $page)
    {
        $uid = $condition['o.uid'];
        $indianaArrayIng = $this->alias('o')
            ->join('nper_list n', 'o.nper_id = n.id', 'LEFT')
            ->join('goods g', 'n.pid = g.id', 'LEFT')
            ->join('image_list i', 'i.id = g.index_img', "left")
            ->join("deposer_type d", "d.id = n.deposer_type", "left")
            ->field('d.code,n.id,o.join_type,n.odd_join_num,n.even_join_num,n.nper_type,i.img_path,n.id as nper_id,n.`status`,n.participant_num,n.sum_times,g.id as good_id,g.name,g.index_img,MAX(o.create_time) max_create_time,SUM(success_num) join_num')
            ->where(array('o.uid' => $uid, 'n.status' => 1))
            //->where('n.`status` => 1 OR n.`status` => 2')
            ->where('o.success_num > 0 ')
            //->group('o.nper_id,o.join_type')
            ->group('o.nper_id')
            ->order('max_create_time desc')
            ->limit($page,10)
            ->select();
        return $indianaArrayIng;
    }
    //获取即将进行的商品列表
    public function get_doing_goods($condition, $page)
    {
        $uid = $condition['o.uid'];
        $indianaArrayIng = $this->alias('o')
            ->join('nper_list n', 'o.nper_id = n.id', 'LEFT')
            ->join('goods g', 'n.pid = g.id', 'LEFT')
            ->join('image_list i', 'i.id = g.index_img', "left")
            ->join("deposer_type d", "d.id = n.deposer_type", "left")
            ->field('d.code,n.id,o.join_type,n.odd_join_num,n.even_join_num,n.nper_type,i.img_path,n.id as nper_id,n.`status`,n.participant_num,n.sum_times,g.id as good_id,g.name,g.index_img,MAX(o.create_time) max_create_time,SUM(success_num) join_num')
            ->where(array('o.uid' => $uid, 'n.status' => 2))
            //->where('n.`status` => 1 OR n.`status` => 2')
            ->where('o.success_num > 0 ')
            //->group('o.nper_id,o.join_type')
            ->group('o.nper_id')
            ->order('max_create_time desc')
            ->limit($page,10)
            ->select();
        return $indianaArrayIng;
    }

    //获取已经揭晓的商品列表
    public function get_done_goods($uid, $page)
    {
        $indianaArrayDone = $this->alias('o')
            ->join('luck_num c', 'c.order_id = o.order_id', "left")
            ->join('nper_list n', 'o.nper_id = n.id', "left")
            ->join('goods g', 'n.pid = g.id', "left")
            ->join('image_list i', 'i.id = g.index_img', "left")
            ->join("deposer_type d", "d.id = n.deposer_type", "left")
            ->join('users u', 'u.id=n.luck_uid', 'left')
            ->field('n.luck_uid,d.code,o.create_time,o.join_type,n.odd_join_num,u.nick_name,n.even_join_num,n.nper_type,i.img_path,o.order_id,o.nper_id,n.status,n.participant_num,n.sum_times,n.luck_num,n.luck_order_id,n.luck_user,n.open_time,g.id as good_id,g.name,g.index_img')
            ->where("o.uid=" . $uid . " AND n.status = 3 AND o.success_num > 0")
            ->group('o.nper_id,o.join_type')
            ->order("o.create_time desc")
            ->limit($page,10)
            ->select();

        if (!empty($indianaArrayDone)) {
            $indianaArrayDone = $this->get_other_data($indianaArrayDone);
        }
        return $indianaArrayDone;
    }

    //查询中间者的次数 和自己的次数和号码
    public function get_other_data($data)
    {
        //获取幸运用户和期数LIST
        $nper_array = array_column($data,"nper_id");
        $luck_user_array = array_column($data,"luck_uid");

        //中奖者参与次数list
        $where['uid'] = array("IN",$luck_user_array);
        $where['nper_id'] = array("IN",$nper_array);
        $win_num =  M("order_list")->where($where)->field("sum(success_num) as win_num,nper_id,uid")->group("nper_id,uid")->select();

        //自己的夺奖号码LIST
        $map['uid'] = get_user_id();
        $map['nper_id'] = array("IN",$nper_array);
        $code_list_array = M("luck_num")->field("code_list,nper_id,uid")->where($map)->select();

        foreach($data as $key=>$value){
            //添加中奖者参数次数
            foreach($win_num as $k=>$v){
                if($v['nper_id']==$value['nper_id']&&$v['uid']==$value['luck_uid']){
                    $data[$key]['win_num']=$v['win_num'];
                }
            }
            $luck_num_arr = array();
            //计算每期自己的参与号码和次数
            foreach($code_list_array as $k2=>$v2){
                if($value['nper_id']==$v2['nper_id']){

                    //处理数据
                    $tmp_arr = explode(",", $v2["code_list"]);
                    $luck_num_arr = array_merge($luck_num_arr, $tmp_arr);
                    $luck_num_arr = array_filter($luck_num_arr);
                    foreach($luck_num_arr as $k3=>$v3){
                        $luck_num_arr[$k3] = num_base_mask(intval($v3),1,0);
                    }


                    $data[$key]['code_list'] = $luck_num_arr;
                    $data[$key]['join_num'] = count($luck_num_arr);

                }
            }
        }

        return $data;
    }


    /**
     * 商品详情页面下方所有的购买者记录
     * @param $nper_id
     * @return array
     */
    public function goods_details_order($nper_id, $offset = 0)
    {

        $Users = M('Users');
        $uid_arr = $this->where("dealed = 'true' AND status = 1 AND nper_id = " . $nper_id)->limit($offset, 10)->order('create_time','desc')->select();


        if (!$uid_arr) {
            return array();
        }

        $order_record = array();
        $count = 0;

        foreach ($uid_arr as $key => $value) {
            $map['id'] = $value['id'];
            $count = $this->field('num')->where($map)->find()['num'];

            $user_info = $Users->field('nick_name,username,reg_ip,user_pic,ip_area')->where(array("id" =>  $value['uid']))->find();

            if (!$user_info) {
                continue;
            }


            $user_face = $this->get_one_image_src($user_info['user_pic']);


            $value['pay_time'] = microtime_format($value['pay_time'], 3, 'Y-m-d H:i:s');

            $order_record[] = array_merge($user_info, array('count' => $count, 'uid' => $value['uid'], 'user_face' => $user_face, 'pay_time' => $value['pay_time']));
        }
        if ($order_record) {
            foreach ($order_record as $key => $value) {
                $data[$key] = $value['pay_time'];
            }
            array_multisort($data, SORT_DESC, $order_record);
        } else {
            return array();
        }


        return $order_record;


    }

    //0元购订单记录
    public function zero_goods_detail_order($nper_id, $offset = 0, $join_type)
    {
        $Users = M('Users');
        $uid_arr = $this->where("dealed = 'true' AND status = 1 AND nper_id = " . $nper_id . ' AND join_type=' . (int)$join_type)->limit($offset, 10)->select();
        if (!$uid_arr) {
            return array();
        }

        $order_record = array();
        $count = 0;

        foreach ($uid_arr as $key => $value) {
            $map['id'] = $value['id'];
            $count = $this->field('num')->where($map)->find()['num'];

            $user_info = $Users->field('nick_name,username,reg_ip,user_pic,ip_area')->where(array("id" =>  $value['uid']))->find();

            if (!$user_info) {
                continue;
            }


            $user_face = $this->get_one_image_src($user_info['user_pic']);


            $value['pay_time'] = microtime_format($value['pay_time'], 3, 'Y-m-d H:i:s');

            $order_record[] = array_merge($user_info, array('count' => $count, 'uid' => $value['uid'], 'user_face' => $user_face, 'pay_time' => $value['pay_time']));
        }
        if ($order_record) {
            foreach ($order_record as $key => $value) {
                $data[$key] = $value['pay_time'];
            }
            array_multisort($data, SORT_DESC, $order_record);
        } else {
            return array();
        }


        return $order_record;

    }


    /**
     * 其他人个人中心的夺宝记录
     * @param $uid
     * @return array|mixed
     */
    public function other_indiana($uid)
    {


        $OrderList = M('OrderList');

        $order_info = $this->alias('o')
            ->join('nper_list n', 'o.nper_id = n.id')
            ->join('users u', 'u.id = n.luck_uid')
            ->join('goods g', 'n.pid = g.id')
            ->field('n.id as nper_id,n.status,n.participant_num,n.sum_times,n.luck_uid,n.luck_order_id,n.luck_user,n.luck_num,n.open_time,g.id as good_id,g.name,g.index_img,o.num as join_num,u.nick_name')
            ->where("o.dealed = 'true' AND o.status = 1 AND n.nper_type=1 AND o.uid=" . $uid)
            ->order("o.create_time desc")
            ->select();
        $m_order = new OrderModel();
        if ($order_info) {

            foreach ($order_info as $key => $value) {

                $order_info[$key]['img_path'] = $this->get_one_image_src($value['index_img']);

                $order_info[$key]['open_time'] = date('Y-m-d H:i:s', $value['open_time']);
                $order_info[$key]['luck_num'] = num_base_mask(intval($value['luck_num']), 1, 0);
                //求出幸运者购买次数
                if ($value['status'] == 3) {
                    // $order_info[$key]['luck_join_num'] = $OrderList->field('num')->where('order_id =' . $value['luck_order_id'])->find()['num'];
                    $order_info[$key]['progress'] = '';
                    $order_info[$key]['remain_num'] = '';
                }
                if ($value['status'] == 1 || $value['status'] == 2) {
                    $order_info[$key]['progress'] = round((float)$value['participant_num'], (float)$value['sum_times'], 2) * 100;
                    $order_info[$key]['remain_num'] = $value['sum_times'] - $value['participant_num'];
                }
            }
            $res = $m_order->format_data($order_info, 'status', 'nper_id');

            return [$res['data'], $res['num']];

        } else {
            return [array(), array()];
        }


    }


    /**
     * 其他人个人中心中奖记录
     * @param $uid
     * @return array
     */
    public function other_win_record($uid)
    {

        $order_info = $this->alias('o')->join('nper_list n', 'o.nper_id = n.id')->join('goods g', 'n.pid = g.id')->field('n.id as nper_id,n.status,n.participant_num,n.sum_times,n.luck_uid,n.luck_user,n.luck_num,n.open_time,g.index_img,g.id as good_id,g.name,o.num as join_num')->where("o.uid=" . $uid . " AND o.luck_status =true AND n.nper_type=1")->order("o.create_time desc")->select();


        if ($order_info) {

            foreach ($order_info as $key => $value) {

                $order_info[$key]['img_path'] = $this->get_one_image_src($value['index_img']);

                $order_info[$key]['open_time'] = date('Y-m-d H:i:s', $value['open_time']);
                $order_info[$key]['luck_num'] = num_base_mask(intval($value['luck_num']), 1, 0);
            }


            return $order_info;


        } else {
            return array(
                'code' => '139',
                'status' => 'fail',
                'message' => '中奖记录为空'
            );
        }
    }


    /**
     * 计算详情
     * @param $nper_id
     * @return array
     */
    public function calculation_details($nper_id)
    {
        //查询该期状态
        $NperList = M('nper_list');

        $nper_info = $NperList->field('status,open_num,lottery_issue,luck_num,nper_type')->where(array('id' => $nper_id))->find();

        if (!$nper_info || $nper_info['status'] == 1) {
            return array();
        }

        //查询出最后50条参与记录
        $sql = '';
        if ($nper_info['nper_type'] == 1) {
            $sql = ' AND o.join_type=0';
        } else {
            $sql = ' AND o.join_type>0';
        }
        $record = $this->alias('o')
            ->field('u.nick_name username,o.pay_time')
            ->join('users u', 'u.id=o.uid')
            ->where("o.dealed = 'true' AND o.status = 1 AND nper_id = $nper_id" . $sql)
            ->order('o.pay_time DESC')
            ->limit(50)
            ->select();

        $total_num = 0;


        //格式化时间戳，并生成计算数字
        if ($record) {

            foreach ($record as $key => $value) {
                $record[$key]['join_time'] = microtime_format($value['pay_time'], 3, 'Y-m-d H:i:s');
                $join_num = microtime_format($value['pay_time'], 2, 'is');
                $record[$key]['join_num'] = $join_num;
                $total_num += (int)$join_num;
            }
        }

        //生成查询福利彩票期数url
        $query_url = 'http://caipiao.163.com/award/cqssc/' . substr($nper_info['lottery_issue'], 0, -3) . '.html';

        return array(
            'nper_info' => $nper_info,
            'record' => $record,
            'total_num' => $total_num,
            'query_url' => $query_url
        );


    }

    /**
     * 确认订单
     * @param $cart_id
     * @param $uid
     * @return array
     */
    public function confirm_order($cart_id)
    {


        if (empty($cart_id)) {
            return array();
        }
        $uid = session('user')['id'];
        $ShopCart = M('shop_cart');
        $User = M('users');
        $true_cart = array();
        foreach ($cart_id as $key => $value) {
            $map['n.status'] = 1;
            $map['c.id'] = $value;
            $cart_info = $ShopCart->alias('c')->join('nper_list n', 'c.nper_id = n.id')->join('goods g', 'n.pid = g.id')->field('g.min_times,n.odd_join_num,n.even_join_num,n.nper_type,n.id as nper_id,n.unit_price,n.status,n.participant_num,n.sum_times,g.id as good_id,g.name,g.max_times,c.num,c.id as cart_id,c.join_type')->where($map)->find();

            if($cart_info['num']<=0){
                $cart_info['num']=1;

                $map['num'] = 1;
                M("shop_cart")->where(array("id"=>$value))->save($map);
            }

            if ($cart_info) {
                $true_cart[] = $cart_info;
            }
        }

        if (empty($true_cart)) {
            return array();
        }

        $remain_num = 0;
        $data = array();
        $total_price = 0;
        foreach ($true_cart as $k => $v) {
            if ((int)$v['nper_type'] == 2) {
                if (!empty($v['join_type']) && $v['join_type'] == 1) {
                    $remain_num = $v['sum_times'] - $v['odd_join_num'];
                } elseif (!empty($v['join_type']) && $v['join_type'] == 2) {
                    $remain_num = $v['sum_times'] - $v['even_join_num'];
                }
            } else {
                $remain_num = $v['sum_times'] - $v['participant_num'];
            }
            //求出两个中的最小数
            $compare_num = $v['max_times'] >= $remain_num ? $remain_num : $v['max_times'];

            if ($compare_num < $v['num']) {
                $true_cart[$k]['num'] = $compare_num;
                $data['id'] = $v['cart_id'];
                $data['num'] = $compare_num;
                $ShopCart->save($data);
            }

            $total_price += $v['num'] * $v['unit_price'];
        }

        $money = $User->field('money')->where(array("id" =>  $uid))->find()['money'];

        return array(
            'cart_info' => $true_cart,
            'total_price' => $total_price,
            'money' => $money
        );


    }


    /**
     * 提交订单
     * @param $cart_id
     * @param $uid
     * @return array
     */
    public function submit_order($cart_id)
    {
        if (empty($cart_id)) {
            return array();
        }

        $uid = session('user')['id'];
        $ShopCart = M('shop_cart');
        $User = M('users');

        $true_cart = array();

        //检验是否有已过期产品
        foreach ($cart_id as $key => $value) {
            $map['n.status'] = 1;
            $map['c.id'] = $value;
            $cart_info = $ShopCart->alias('c')->join('nper_list n', 'c.nper_id = n.id')->join('goods g', 'n.pid = g.id')->field('n.id as nper_id,n.unit_price,n.status,n.participant_num,n.sum_times,g.id as goods_id,g.name,c.num,c.id as cart_id,c.join_type')->where($map)->find();
            if (!empty($cart_info)) {
                $true_cart[] = $cart_info;
            }
        }

        if (empty($true_cart)) {
            return array();
        }


        $total_price = 0;

        //获取父订单关闭时长

        $Common = D('common');
        $close_time = C('ORDER_CLOSE_TIME');

        //获取平台全局加密统一拼接密钥
        $token = C('TOKEN_ACCESS');

        //$pay_type  1  个人中心金额支付，不需要调用支付接口
        $pay_type = 1;
        $user_info = $User->field('username,money')->where(array("id" =>  $uid))->find();

        $OrderListParent = M('order_list_parent');

        if (count($true_cart) == 1) { //只有一个购物车
            //开启事务
            $this->startTrans();

            //插入订单表
            $data = array();
            $data['order_id'] = create_order_num();
            $data['goods_id'] = $true_cart[0]['goods_id'];
            $data['goods_name'] = $true_cart[0]['name'];
            $data['nper_id'] = $true_cart[0]['nper_id'];
            $data['num'] = empty($true_cart[0]['num']) ? 1 : $true_cart[0]['num'];
            $data['username'] = $user_info['username'];
            $data['uid'] = $uid;
            $data['money'] = $true_cart[0]['unit_price'] * $true_cart[0]['num'];
            $data['status'] = 1;
            $data['pay_status'] = 1;
            $data['create_time'] = time();
            $data['join_type'] = !empty($true_cart[0]['join_type']) ? $true_cart[0]['join_type'] : 0;

            //生成exec_data字段
            $exec_data = array(
                "TYPE" => "BUY",
                "EXEC_DATA" => array(
                    "VALUE" => $data['money'],
                    'UID' => $uid
                )
            );
            $exec_data['MD5'] = md5($data['order_id'] . $exec_data['TYPE'] . json_encode($exec_data['EXEC_DATA']) . $token);

            $data['exec_data'] = json_encode($exec_data);

            $son_order_id = $this->add($data);

            //插入父订单表
            $parent_data = array();
            $parent_data['order_id'] = create_order_num();
            $parent_data['name'] = '用户' . $user_info['username'] . '的订单:' . $parent_data['order_id'];
            $parent_data['uid'] = $uid;
            $parent_data['username'] = $user_info['username'];
            $parent_data['order_list'] = $son_order_id;
            $parent_data['num'] = 1;
            $parent_data['price'] = $data['money'];
            $parent_data['pay_status'] = 1;
            $parent_data['close_time'] = time() + $close_time;
            $parent_data['origin'] = 'mobile';
            $parent_data['create_time'] = time();
            $parent_data['status'] = 1;
            $parent_order_id = $OrderListParent->add($parent_data);
            $total_price = $data['money'];

            //把父订单id插入到子订单id里面
            $insert_result = $this->where(array('id'=>  $son_order_id))->setField('pid', $parent_order_id);

            if ($son_order_id && $parent_order_id && $insert_result) {

                $this->commit();
            } else {
                $this->rollback();
                return array(
                    'code' => '154',
                    'status' => 'fail',
                    'message' => '插入订单失败'
                );
            }
        } else {
            //开启事务
            $this->startTrans();
            $total_num = 0;
            $son_order = array();
            foreach ($true_cart as $key => $value) { //涉及父订单
                $data = array();
                $data['order_id'] = create_order_num();
                $data['goods_id'] = $value['goods_id'];
                $data['goods_name'] = $value['name'];
                $data['nper_id'] = $value['nper_id'];
                $data['num'] = empty($value['num']) ? 1 : $value['num'];
                $data['username'] = $user_info['username'];
                $data['uid'] = $uid;
                $data['money'] = $value['unit_price'] * $value['num'];
                $data['status'] = 1;
                $data['pay_status'] = 1;
                $data['create_time'] = time();
                $data['join_type'] = !empty($value['join_type']) ? $value['join_type'] : 0;
                //生成exec_data字段
                $exec_data = array(
                    "TYPE" => "BUY",
                    "EXEC_DATA" => array(
                        "VALUE" => $data['money'],
                        'UID' => $uid
                    )
                );
                $exec_data['MD5'] = md5($data['order_id'] . $exec_data['TYPE'] . json_encode($exec_data['EXEC_DATA']) . $token);

                $data['exec_data'] = json_encode($exec_data);

                $son_order_id = $this->add($data);

                if ($son_order_id) {
                    $son_order[] = $son_order_id;
                }
                $total_price += $data['money'];
                $total_num += 1;
            }

            if (empty($son_order)) {
                $this->rollback();
                return array(
                    'code' => '150',
                    'status' => 'fail',
                    'message' => '插入子订单失败'
                );
            }

            $son_order_str = implode(',', $son_order);

            $parent_data = array();
            $parent_data['order_id'] = create_order_num();
            $parent_data['name'] = '用户' . $user_info['username'] . '的订单:' . $parent_data['order_id'];
            $parent_data['uid'] = $uid;
            $parent_data['username'] = $user_info['username'];
            $parent_data['order_list'] = $son_order_str;
            $parent_data['num'] = $total_num;
            $parent_data['price'] = $total_price;
            $parent_data['pay_status'] = 1;
            $parent_data['close_time'] = time() + $close_time;
            $parent_data['origin'] = 'mobile';
            $parent_data['status'] = 1;
            $parent_data['create_time'] = time();

            $parent_order_id = $OrderListParent->add($parent_data);

            //把父订单id插入到子订单id里面
            $insert_result_flag = true;
            foreach ($son_order as $key => $value) {
                $map = array();
                $map['id'] = $value;
                $res = $this->where($map)->setField('pid', $parent_order_id);
                $insert_result_flag = $res ? true : false;

            }

            if ($son_order && $parent_order_id && $insert_result_flag) {
                $this->commit();
            } else {
                $this->rollback();
                return array(
                    'code' => '154',
                    'status' => 'fail',
                    'message' => '插入订单失败'
                );
            }
        }

        //个人中心支付或者调用支付接口
        // pay_type 1 余额支付  2 支付宝支付  3 进行选择

        $user_money = (float)$user_info['money'];
        if ($user_money <= 0) {
            $pay_type = 2;
        } elseif ($user_money < (float)$total_price) {
            $pay_type = 3;
        } else {
            $pay_type = 1;
        }


        if ($parent_order_id) {

            //已插入订单，删除该用户购物车
            // $ShopCart->where("uid =".$uid)->delete();
            if (!empty($cart_id)) {
                $ShopCart->where(['id' => ['in', $cart_id]])->delete();
            }

            return array(
//                'order_id' => $parent_order_id,
                'total_price' => $total_price,
                'user_money' => $user_info['money'],
                'pay_type' => $pay_type,
                'order_num' => $parent_data['order_id'],
                'remain_price' => round(floatval($total_price) - floatval($user_money), 2)
//                'order_name' => $parent_data['name'],
//                'goods_link' => 'www.baidu.com'
            );
        } else {
            return array();
        }


    }

    /**
     * 个人余额支付
     * @param $order_id
     * @param $uid
     * @return array
     */
    public function personal_pay($order_id)
    {
        if (empty($order_id)) {
            return array();
        }
        $NperList = M('nper_list');
        $Users = M('users');
        $OrderListParent = M('order_list_parent');

        $uid = session('user')['id'];

        $son_order_id = $OrderListParent->field('order_list')->where("status = '1' AND pay_status = '1'AND order_id = '$order_id' ")->find()['order_list'];

        if (empty($son_order_id)) {
            return array();
        }
        $map['id'] = ['in', $son_order_id];
        $result_order = $this->field('order_id,num,id,nper_id,money,goods_id,join_type')->where($map)->select();
        if (empty($result_order)) {
            return array();
        }


        //检验订单中的每期状态，剩余量
        $total_price = 0;
        $delete_flag = false;

        foreach ($result_order as $key => $value) {
            $nper_info = $NperList->field('participant_num,sum_times,status,odd_join_num,even_join_num,nper_type')->where(array("id" => $value['nper_id']))->find();
            if ((int)$nper_info['nper_type'] == 1) {
                if ($nper_info['status'] != '1' || $nper_info['sum_times'] - $nper_info['participant_num'] < $value['num']) {
                    $delete_flag = true;
                    $this->where(array("id" => $value['id']))->delete();
                    unset($result_order[$key]);
                    continue;
                }
            }
            if ((int)$nper_info['nper_type'] == 2) {
                if (intval($value['join_type']) == 1) {
                    if ($nper_info['status'] != '1' || $nper_info['sum_times'] - $nper_info['odd_join_num'] < $value['num']) {
                        $delete_flag = true;
                        $this->where(array("id" => $value['id']))->delete();
                        unset($result_order[$key]);
                        continue;
                    }
                } else {
                    if ($nper_info['status'] != '1' || $nper_info['sum_times'] - $nper_info['even_join_num'] < $value['num']) {
                        $delete_flag = true;
                        $this->where(array("id" => $value['id']))->delete();
                        unset($result_order[$key]);
                        continue;
                    }
                }
            }

            $total_price += $value['money'];
        }

        //查看是否有订单被删除，进而修改父订单表
        if ($delete_flag == true) {
            $son_order_str = '';
            foreach ($result_order as $value) {
                $son_order_str .= $value['id'] . ',';
            }
            if ($son_order_str) {
                $son_order_str = substr($son_order_str, 0, -1);
                $OrderListParent->where(array("order_id" => $order_id))->setField('order_list', $son_order_str);
            } else {
                $OrderListParent->where(array("order_id" => $order_id))->delete();
            }
        }

        if (empty($result_order)) {
            return array();
        }

        //重建数组索引
        $result_order = array_merge($result_order);

        //判断订单总金额与个人中心余额
        $user_info = $Users->field('money')->where(array("id" => $uid))->find();

        if ($user_info['money'] < $total_price) {
            return array();
        }

        //取出调用路径
        $Common = D('common');

        $token = C('TOKEN_ACCESS');


        //调用接口，完成剩余操作
        $parent_order_num = $order_id;

        $timestamp = time();

        $sign = md5($parent_order_num . $timestamp . $token);
        $request = array(
            'order_id' => $order_id,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'flag' => 1
        );
        $event = new \app\pay\controller\Notify;
        $out = $event->balance_flow($request);

        if ($out['code'] == 1) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功',
                'order_id' => $order_id
            );
        } else {
            return array();
        }

    }


    /**
     * 个人中奖记录
     * @param $uid
     * @return array
     */
    public function personal_win_record($limit = 0)
    {
        $limit = $limit * 10;

        $uid = session('user')['id'];
        $m_win = M('win_record');
        $order_info = $m_win->alias('w')
            ->join('nper_list n', 'n.id=w.nper_id', 'LEFT')
            ->join('goods g', 'n.pid = g.id', 'LEFT')
            ->field(
                'n.id as nper_id,n.participant_num,n.sum_times,n.luck_num,n.luck_num,n.luck_uid,
                n.luck_user,n.open_time,g.index_img,g.id as good_id,g.name,n.sum_times as join_num,
                w.id as win_record_id,w.logistics_status as prize_status,n.nper_type,w.qb_type,w.luck_type,g.reward_type,g.reward_data'
            )
            ->limit($limit, 10)
            ->order('n.open_time', 'desc')
            ->where(array('w.luck_uid' => $uid, 'g.status' => 1))
            ->select();
        $order_info = array_map(array($this, 'get_index_image_src'), $order_info);

        return $order_info;
    }


    /**
     * 充值记录
     * @param $uid
     * @return array
     */
    public function recharge_record()
    {

        $user_info = session('user');

        $uid = $user_info['id'];


        $OrderListParent = M('order_list_parent');

        $recharge_info = $OrderListParent->field('plat_form,price,pay_time,pay_status')->where("pay_status = 3 AND bus_type = 'recharge' AND uid =" . $uid)->order('pay_time', 'desc')->select();

        //转化时间格式
        if ($recharge_info) {
            foreach ($recharge_info as $key => $value) {
                if ($value['pay_time']) {
                    $recharge_info[$key]['pay_time'] = microtime_format($value['pay_time'], 1, 'Y-m-d H:i:s');
                }
            }
        }
        return $recharge_info;


    }


    /**
     * 获奖之后的奖品信息确认,点击进去查看具体流程
     * @param $luck_id
     * @param $nper_id
     * @param $uid
     * @return array
     */
    public function prize_info_confirm($win_record_id)
    {

        //假数据

        $uid = session('user')['id'];


        //查询出该期幸运奖品的状态经历过程

        $PrizeStatus = M('prize_status');
        $UserAddress = M('user_addr');
        $NperList = M('nper_list');
        $WinRecord = M('win_record');

        $status_info = $PrizeStatus->field('id as prize_status_id,status,status_info,create_time')->where('win_record_id =' . $win_record_id)->order('id ASC')->select();


        //判断是否存在该中奖纪录
        $luck_exist = $WinRecord->field('nper_id,luck_type,create_time,money')->where(array('id' => $win_record_id))->find();

        if (empty($status_info) && !$luck_exist) {
            return array(
                'code' => '156',
                'status' => 'fail',
                'message' => '没有获奖商品的状态信息'
            );
        }
        //如果是已中奖并且没有中奖状态记录，那么模拟第一条中奖记录，方便以下流程。

        $nper_id = $luck_exist['nper_id'];
        //查询该期开奖时间
        $open_time = $NperList->field('open_time')->where(array('id'=> $nper_id))->find()['open_time'];
        $first_status_info = array(
            'prize_status_id' => 0, //因为是假的，所以设为0，并无实际意义
            'status' => 'get_goods',
            'status_info' => '获得商品',
            'create_time' => $open_time
        );


        array_unshift($status_info, $first_status_info);

        //转化时间格式
        foreach ($status_info as $key => $value) {
            $status_info[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
        }


        //最后返回的数组
        $result_array = array();

        $status_count = count($status_info);

        //将该商品的状态信息放到结果数组里面
        $result_array['status_info'] = $status_info;
        $result_array['now_status'] = $status_count;


        //将该中奖的商品信息放入到结果数组里面
        $goods_info = $this->alias('o')->join('nper_list n', 'o.nper_id = n.id')->join('goods g', 'n.pid = g.id')->field('n.id as nper_id,n.status,n.participant_num,n.sum_times,n.luck_num,n.luck_uid,n.luck_user,g.index_img,g.id as good_id,g.name,o.num as join_num')->where("o.uid=" . $uid . " AND n.id = " . $nper_id)->find();

        $goods_info['img_path'] = $this->get_one_image_src($goods_info['index_img']);
        $goods_info['luck_type'] = $luck_exist['luck_type'];
        $goods_info['win_create_time'] = date("Y-m-d H:i:s", $luck_exist['create_time']);
        $goods_info['money'] = $luck_exist['money'];
        $result_array['goods_info'] = $goods_info;


        //根据状态的数量（即进行到哪一步）返回不同的商品状态信息
        switch ($status_count) {

            case 1:
                //状态只有一个，说明只是刚中奖，下一步是确认收货地址。
                //检测是否有该用户的默认地址，有的话，返回（包括address_id），没有的话，再次查询用户是否有其他地址，只要最开始的那个，再没有返回空字符串
                $address_list = array();
                $default_address = $UserAddress->field('id as address_id,name,phone,area,address,provice,city')->where('type = 1 AND uid = ' . $uid)->find();

                //查询地址列表
                $address_list = $UserAddress->field('id as address_id,name,phone,area,address,provice,city')->where('uid = ' . $uid)->order('create_time DESC')->select();

                $result_array['default_address'] = $default_address ? $default_address : $address_list ? $address_list[0] : '';
                $result_array['address_list'] = $address_list;


                break;
            case 2:
                //状态有2个，说明已经确认收货地址，所以要取出该收货地址并返回
                //客户端显示商品派发请等待提示

                //从luck_num表中得到address_id
                $address_info = $WinRecord->field('address_data')->where(array('id' => $win_record_id))->find()['address_data'];


                $result_array['default_address'] = $address_info ? json_decode($address_info, true) : '';

                break;
            case 3:
                //收货地址有3个，说明商品已经派发了，即已经有了物流公司，物流订单这两个信息，同时仍有收货地址信息
                $result_array = array_merge($result_array, $this->prize_info_confirm_supplement($win_record_id));


                break;
            case 4:
                //收货地址有3个，商品已经派发，显示内容和3一样，客户端显示确认收货的按钮


                $result_array = array_merge($result_array, $this->prize_info_confirm_supplement($win_record_id));


                break;
            case 5:

                //显示和4一样，

                $result_array = array_merge($result_array, $this->prize_info_confirm_supplement($win_record_id));


                break;
            default:
                return array();
        }

        //界面仍然需要win_record_id
        $result_array['win_record_id'] = $win_record_id;
        return $result_array;


    }

    /**
     * 确认收货地址
     * @param $address_id
     * @param $luck_id
     * @return array
     */
    public function confirm_send_address($address_id, $win_record_id)
    {
        if (empty($address_id) || empty($win_record_id)) {
            return array(
                'code' => '126',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        $LuckNum = M('luck_num');
        $UserAddr = M('user_addr');
        $WinRecord = M('win_record');

        //存储该地址具体信息

        $addr_info = $UserAddr->field('name,code,address,phone,provice,city,area')->where(array('id' => $address_id))->find();

        if (empty($addr_info)) {
            return array(
                'code' => '177',
                'status' => 'fail',
                'message' => '该地址不存在'
            );
        }

        $addr_info = json_encode($addr_info);


        /*$WinRecord->where('id ='.$win_record_id)->setField('address_data',$addr_info);

        //改变状态值
        $WinRecord->where('id ='.$win_record_id)->setField('logistics_status',2);
        $WinRecord->where('id ='.$win_record_id)->setField('notice',1);*/
        $data = array(
            'address_data' => $addr_info,
            'logistics_status' => 2,
            'notice' => 1,

        );
        $WinRecord->where(array('id' => $win_record_id))->data($data)->save();


        //插入到sp_prize_status表中,更新状态
        $PrizeStatus = M('prize_status');
        $status_data = array();
        $status_data['win_record_id'] = $win_record_id;
        $status_data['status'] = $this->prize_status[1];
        $status_data['status_info'] = $this->prize_status_info[1];
        $status_data['create_time'] = time();

        $res = $PrizeStatus->add($status_data);

        if ($res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功'
            );
        } else {
            return array(
                'code' => '158',
                'status' => 'fail',
                'message' => '确认收货地址失败'
            );
        }

    }


    /**
     * 确认收货
     * @param $luck_id
     * @return array
     */
    public function confirm_receipt($win_record_id)
    {
        if (empty($win_record_id)) {
            return array(
                'code' => '126',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        //插入到sp_prize_status表中,更新状态
        $PrizeStatus = M('prize_status');
        $WinRecord = M('win_record');

        $status_data = array(
            array(
                'win_record_id' => $win_record_id,
                'status' => $this->prize_status[3],
                'status_info' => $this->prize_status_info[3],
                'create_time' => time()
            ),
            array(
                'win_record_id' => $win_record_id,
                'status' => $this->prize_status[4],
                'status_info' => $this->prize_status_info[4],
                'create_time' => time()
            )
        );


        $prize_res = $PrizeStatus->addAll($status_data);


        //确认收货之后插入到sp_show_order(晒单表)新的数据

        $show_order_info = array();

        $ShowOrder = M('show_order');

        $show_order_info = $WinRecord->alias('w')->join('nper_list n', 'w.nper_id = n.id')->field('w.luck_uid as uid,w.luck_user as username,w.nper_id,w.order_id,w.luck_num,n.pid as goods_id')->where('w.id =' . $win_record_id . ' AND n.nper_type=1')->find();

        $show_order_info['status'] = -2;

        $show_order_info['complete'] = 0;

        $show_order_res = $ShowOrder->add($show_order_info);

        //更新sp_win_record中的logistics_status

        $WinRecord->where(array('id' => $win_record_id))->setField('logistics_status', 5);


        if ($prize_res && $show_order_res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功'
            );
        } else {
            return array(
                'code' => '159',
                'status' => 'fail',
                'message' => '确认收货失败'
            );
        }


    }


    /**
     * 为prizeInfoConfirm方法简化代码
     * @param $uid
     * @param $nper_id
     * @param $luck_id
     * @return array
     */
    private function prize_info_confirm_supplement($win_record_id)
    {

        $result_array = array();


        $WinRecord = M('win_record');

        $address_info = array();


        $address_info = $WinRecord->field('address_data')->where(array('id' => $win_record_id))->find()['address_data'];


        $result_array['default_address'] = $address_info ? json_decode($address_info, true) : '';


        //查出物流信息
        $logistics_info = $WinRecord->field('logistics_company,logistics_number')->where(array('id' => $win_record_id))->find();
        $result_array['logistics_info'] = $this->get_human_read_logistics_info($logistics_info);
        return $result_array;
    }

    private function get_human_read_logistics_info($logistics_info)
    {
        $category = new CategoryModel();

        $logistics_company = $logistics_info['logistics_company'];
        $logistics = $category->get_logistics();
        $logistics_info['logistics_company'] = isset($logistics[$logistics_company])?$logistics[$logistics_company]:'';

        return $logistics_info;

    }


    private function curl($url)
    {
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);

        return $output;
    }


    /**
     * 得到单个图片地址
     * @param $image_id
     * @return string
     */
    protected function get_one_image_src($image_id)
    {

        $website_url = '';//$this->get_conf('WEBSITE_URL');

        if (empty($image_id)) {
            return $website_url . '/data/img/noPicture.jpg';
        }

        $ImageList = M('image_list');
        $path_info = $ImageList->field('img_path')->where(array("id" =>  $image_id))->find();

        $path = $path_info['img_path'] ? $website_url . $path_info['img_path'] : $website_url . '/data/img/noPicture.jpg';


        return $path;
    }

    protected function get_index_image_src($data)
    {
        $image_id = $data['index_img'];
        $website_url = '';//$this->get_conf('WEBSITE_URL');

        if (empty($image_id)) {
            return $website_url . '/data/img/noPicture.jpg';
        }


        $ImageList = M('image_list');
        $path_info = $ImageList->field('img_path')->where(array("id" =>  $image_id))->find();

        $data['img_path'] = $path_info['img_path'] ? $website_url . $path_info['img_path'] : $website_url . '/data/img/noPicture.jpg';
        //格式化时间
        $data['open_time'] = date('Y-m-d H:i:s');
        //计算幸运号码
        $data['luck_num'] = num_base_mask(intval($data['luck_num']), 1, 0);


        return $data;
    }


    /**得到多个图片地址
     * @param $image_id_str
     * @return array|string
     */
    protected function get_more_image_src($image_id_str)
    {
        $ImageList = M('image_list');

        $website_url = '';//$this->get_conf('WEBSITE_URL');

        $map['id'] = ['in', $image_id_str];
        $path_info = $ImageList->field('img_path')->where($map)->select();
        if ($path_info) {
            $result_path = array();
            foreach ($path_info as $key => $value) {

                $result_path[] = $value['img_path'] ? $website_url . $value['img_path'] : $website_url . '/data/img/noPicture.jpg';

            }
            return $result_path;
        }
        return '';
    }

    //获取配置信息
    private function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }


}