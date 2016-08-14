<?php

namespace app\mobile\model;

use think\Model;
class ShopCart extends Base {

    /**
     * ajax增加购物车数量
     * @param $nper_id
     */
    public function ajax_add_cart($nper_id,$join_type=0) {
        if(empty($nper_id)) {
            return array(
                'code' => '343',
                'status' => 'fail',
                'message' => '加入购物车失败'
            );
        }
        //取出该期期数下的最小购买数量
        $Nper_List = M('nper_list');

        $min_times = $Nper_List->field('min_times')->where(array('id' =>$nper_id))->find()['min_times'];

        if(!$min_times) {
            return array(
                'code' => '343',
                'status' => 'fail',
                'message' => '加入购物车失败'
            );
        }

        $exist_flag = true;

        //判断是否的登录，采取不同的方式
        if(session('user')) {
            //登录，所以存储到数据库
            $uid = session('user')['id'];
            //调用本类方法
            return $this->add_cart($uid,$nper_id,$min_times,$join_type);

        }else{
            //未登录，存储到cookie

            //判断是否第一次第一次存储
            if(cookie('cart_info')){
                $cart_info = cookie('cart_info');
                if(isset($cart_info[$nper_id]) && !empty($cart_info[$nper_id])) {
                    //未判断是否数量超过商品剩余数量，是因为在购物车界面进行检查,修改
                    $cart_info[$nper_id] += $min_times;
                    $exist_flag = true;
                }else{
                    $cart_info[$nper_id] = $min_times;
                    $exist_flag = false;
                }
                cookie('cart_info',$cart_info);

            }else{
                $cart_info = array();
                $cart_info[$nper_id] = $min_times;
                cookie('cart_info',$cart_info);
                $exist_flag = false;
            }
        }
        return array('exist_flag'=>$exist_flag);



    }








    /**
     * 加入购物车
     * @param $uid
     * @param $nper_id
     * @return array
     */
    private  function add_cart($uid,$nper_id,$num,$join_type) {

        if(empty($uid) || empty($nper_id) || empty($num)) {
            return array(
                'code' => '126',
                'status' => 'fail',
                'message' => '缺少参数'
            );
        }

        $map = array();
        $data = array();
        $exist_flag = true;

        $NperList = M('nper_list');
        $Goods = M('goods');


        $map['uid'] = $uid;
        $map['nper_id'] = $nper_id;
        $map['join_type']=!empty($join_type)?intval($join_type):0;
        $exist_cart = $this->field('id as cart_id,num')->where($map)->find();

        //查询出该期剩余量
        $nper_info = $NperList->field('pid as goods_id,status,sum_times,participant_num,odd_join_num,even_join_num,nper_type')->where(array("id"=>$nper_id))->find();
       if($nper_info['nper_type']==1){
           if($nper_info['status'] != 1 || $nper_info['sum_times'] == $nper_info['participant_num']) {
               return array(
                   'code' => '131',
                   'status' => 'fail',
                   'message' => '该期的状态已不为可购买'
               );
           }
       }
        if($nper_info['nper_type']==2){
            if($join_type==1){
                if($nper_info['status'] != 1 || $nper_info['sum_times'] == $nper_info['odd_join_num']) {
                    return array(
                        'code' => '131',
                        'status' => 'fail',
                        'message' => '该期的状态已不为可购买'
                    );
                }
            }else{
                if($nper_info['status'] != 1 || $nper_info['sum_times'] == $nper_info['even_join_num']) {
                    return array(
                        'code' => '131',
                        'status' => 'fail',
                        'message' => '该期的状态已不为可购买'
                    );
                }
            }
        }
        //求出该商品的max_times
        $max_times = $Goods->field('max_times')->where(array('id' =>$nper_info['goods_id']))->find()['max_times'];
        //求出剩余数量
        if($nper_info['nper_type']==1){
            $remain_num = $nper_info['sum_times'] - $nper_info['participant_num'];
            //求出两个中的最小数
            $compare_num = $max_times >= $remain_num ? $remain_num : $max_times;
        }
        if($nper_info['nper_type']==2){
            $odd_remain_num = $nper_info['sum_times'] - $nper_info['odd_join_num'];
            $odd_compare_num=$max_times>=$odd_remain_num?$odd_remain_num:$max_times;

            $even_remain_num = $nper_info['sum_times'] - $nper_info['even_join_num'];
            $even_compare_num=$max_times>=$even_remain_num?$even_remain_num:$max_times;
        }

        if($exist_cart) {
            //已经存在该用户该期数的购物车
            if($nper_info['nper_type']==1){
                if($compare_num > $num + $exist_cart['num']) {
                    $data['num'] = $exist_cart['num'] + $num;
                }else{
                    $data['num'] = $compare_num;
                }

            }
            if($nper_info['nper_type']==2){
                //奇数
                if($odd_compare_num > $num + $exist_cart['num']) {
                    $data['num'] = $exist_cart['num'] + $num;
                }else{
                    $data['num'] = $odd_compare_num;
                }
                //偶数
                if($even_compare_num > $num + $exist_cart['num']) {
                    $data['num'] = $exist_cart['num'] + $num;
                }else{
                    $data['num'] = $even_compare_num;
                }
            }

            $res = $this->where(array("id"=>$exist_cart['cart_id']))->save($data);

            if(!$res) {
                return array(
                    'code' => '132',
                    'status' => 'success',
                    'message' => '增加购物车数量失败，购物车数量已超可买量'
                );
            }

        }else{
            //没有存在该用户该期数的购物车
            if($nper_info['nper_type']==1){
                if($compare_num > $num) {
                    $data['num'] = $num;
                }else{
                    $data['num'] = $compare_num;
                }
            }
            if($nper_info['nper_type']==2){
                if(!empty($join_type)){
                    if($join_type==1){
                        if($odd_compare_num>$num){
                            $data['num'] = $num;
                        }else{
                            $data['num'] = $odd_compare_num;
                        }
                    }else{
                        if($even_compare_num>$num){
                            $data['num'] = $num;
                        }else{
                            $data['num'] = $even_compare_num;
                        }
                    }
                }
            }
            $data['uid'] = $uid;
            $data['nper_id'] = $nper_id;
            $data['create_time'] = time();
            $data['join_type']=!empty($join_type)?intval($join_type):0;
            $res = $this->add($data);
            $exist_flag = false;

            if(!$res) {
                return array(
                    'code' => '132',
                    'status' => 'success',
                    'message' => '增加该用户购物车失败'
                );
            }

        }

        return array('exist_flag'=>$exist_flag);

    }


    /**
     * 获取购物车列表
     * @return mixed
     */
    public function cart_list() {

        $uid = session('user')['id'];

        $cart_list = $this->alias('c')->join('nper_list n','c.nper_id = n.id')->join('goods g','n.pid = g.id')->join('image_list i','i.id = g.index_img')
            ->field('i.img_path,c.id as cart_id,c.num,c.nper_id,c.join_type,n.min_times,g.name,g.index_img,g.max_times,n.sum_times,n.participant_num,n.min_times,n.deposer_type,n.unit_price,n.nper_type,n.odd_join_num,n.even_join_num')->where('c.uid = '.$uid .' AND n.status = 1')->select();

        $cart_price = 0;
        $cart_num = 0;
        if($cart_list) {

            $website_url = '';//$this->get_conf('WEBSITE_URL');
            foreach($cart_list as $key=>$value) {
                $cart_list[$key]['modify_flag'] = 0;
                if($value['nper_type']==1) {
                    $cart_list[$key]['remain_num'] = $value['sum_times'] - $value['participant_num'];
                    //如果购物车的数量大于较小量，直接为剩余量
                    $compare_num = $cart_list[$key]['remain_num'] >= $value['max_times'] ? $value['max_times'] : $cart_list[$key]['remain_num'];

                    if ($value['num'] > $compare_num) {
                        $cart_list[$key]['num'] = $compare_num;
                        $this->where(array('id'=> $value['cart_id']))->setField('num', $compare_num);
                        $cart_list[$key]['modify_flag'] = 1;
                    }
                }
                if($value['nper_type']==2){
                    if(isset($value['join_type'])){
                        if($value['join_type']==1){
                            $cart_list[$key]['remain_num'] = $value['sum_times'] - $value['odd_join_num'];
                        }else{
                            $cart_list[$key]['remain_num'] = $value['sum_times'] - $value['even_join_num'];
                        }
                    }
                    //如果购物车的数量大于较小量，直接为剩余量
                    $compare_num = $cart_list[$key]['remain_num'] >= $value['max_times'] ? $value['max_times'] : $cart_list[$key]['remain_num'];
                    if ($value['num'] > $compare_num) {
                        $cart_list[$key]['num'] = $compare_num;
                        $this->where(array('id'=> $value['cart_id']))->setField('num', $compare_num);
                        $cart_list[$key]['modify_flag'] = 1;
                    }
                }
                $cart_list[$key]['img_path'] = $value['img_path'] ? $website_url.$value['img_path'] : $website_url.'/data/img/noPicture.jpg';
                $cart_price += $value['num'] * $value['unit_price'];
            }

            $cart_num = count($cart_list);

        }
        return array(
            'cart_list' => $cart_list,
            'cart_price' => $cart_price,
            'cart_num' => $cart_num
        );

    }






    /**
     * 购物车页面修改数量
     * @param $cart_id
     * @param $num
     * @return array
     */
    public function modify_cart_num($cart_id,$num) {
        if(empty($cart_id) || (intval($num)<=0)) {
            return array(
                'code' => '126',
                'status' => 'fail',
                'message' => '缺少参数'
            );
        }

        $NperList = M('nper_list');
        $Goods = M('goods');

        $cart_info=$this->field('nper_id,join_type')->where(array("id" =>$cart_id))->find();
        $nper_id = $cart_info['nper_id'];

        $nper_info = $NperList->field('pid as goods_id,sum_times,participant_num,odd_join_num,even_join_num,nper_type')->where(array('id' =>$nper_id))->find();


        if($nper_info['nper_type']==1){
            $remain_num = $nper_info['sum_times'] - $nper_info['participant_num'];
        }else{
            if(!empty($cart_info['join_type'])&&$cart_info['join_type']==1){
                $remain_num = $nper_info['sum_times'] - $nper_info['odd_join_num'];
            }elseif(!empty($cart_info['join_type'])&&$cart_info['join_type']==2){
                $remain_num = $nper_info['sum_times'] - $nper_info['even_join_num'];
            }
        }

        //求出该商品的max_times
        $max_times = $Goods->field('max_times')->where(array('id' =>$nper_info['goods_id']))->find()['max_times'];

        //求出两个中的最小数
        $compare_num = $max_times >= $remain_num ? $remain_num : $max_times;


        $cart_num = $num >= $compare_num ? $compare_num : $num;

        $this->where(array('id'=>$cart_id))->setField('num',$cart_num);

        return array(
            'code' => '200',
            'status' => 'success',
            'message' => '成功'
        );

    }


    /**
     * 删除购物车
     * @param $cart_id
     * @return array
     */
    public function delete_cart($cart_id) {
        if(empty($cart_id)) {
            return array(
                'code' => '126',
                'status' => 'fail',
                'message' => '缺少参数'
            );
        }

        $cart_id = (int)$cart_id;

        $res = $this->where(array("id" =>$cart_id))->delete();

        if($res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '删除购物车成功'
            );
        }else{
            return array(
                'code' => '201',
                'status' => 'fail',
                'message' => '删除购物车失败'
            );
        }


    }

    /**
     * 得到购物车数量
     * @return int
     */
    public function get_cart_num() {
        //根据用户是否登录返回购物车的数量
        if(session('user')) {
            return $this->cart_list()['cart_num'];
        }else{
            if(cookie('cart_info')) {
                return count(cookie('cart_info'));
            }else{
                return 0;
            }
        }

    }






}