<?php
namespace app\admin\model;

use Think\Db;
use Think\Model;
use think\model\Adv;
use app\admin\model\GoodsModel;

Class SystemModel extends BaseModel
{


    /**
     * 管理员信息
     * @return array
     */
    public function get_admin_info() {
        $log_login = M('log_login','');
        $username = get_user_name();
        $uid = get_user_id();
        $last_login_time = $log_login->field('login_time')->where(array("user_login" => $username))->order('id desc')->limit(1,1)->find()['login_time'];
        if($last_login_time) {
            $last_login_time = date('Y-m-d H:i:s',$last_login_time);
        }
        //后台用户角色
        $users = M('users');
        $admin_role_list = M('role_list','admin_');
        $role_list = $users->field('role_list,last_login_ip')->where(array('id' =>$uid))->find()['role_list'];

        $role_name = '';
        if(!empty($role_list)) {
            if(strpos($role_list,',')){
                $role_id = substr($role_list,0,strpos($role_list,','));
            }else{
                $role_id=$role_list;
            }
            $role_name = $admin_role_list->field('name')->where(array('id' =>$role_id))->find()['name'];
        }


        $admin_info = array(
            'username' => $username,
            'role_name' => $role_name,
            'last_login_time' => $last_login_time,
            'last_login_ip' => session('user.last_login_ip')
        );
        return $admin_info;
    }


    /**
     * 系统信息
     * @return array
     */
    public function get_system_info() {
        $model = new Model();
        $mysql_version = $model->query("select VERSION() as ver");


        $system_info = array(
            'os' => PHP_OS,
            'php_uname' => php_uname('r'),
            'php_version' => PHP_VERSION,
            'mysql_version' => $mysql_version[0]['ver'],
            'upload_limit' => ini_get('upload_max_filesize'),
            'support_gd' => function_exists('imagecreate'),
            'post_limit' => ini_get('post_max_size'),
            'script_out_time' => ini_get('max_execution_time'),
            'support_fsockopen' => function_exists('fsockopen'),
            'support_zend' => ''

        );

        return $system_info;

    }

    /**
     * 网站信息
     * @return mixed
     */
    public function get_web_info() {
        $conf = M('conf',null);
        $web_conf = $conf->field('title,name,value,remark')->where("category = '网站信息' ")->select();

        //需要的信息
        $select_conf = array('WEBSITE_NAME','WEBSITE_KEYWORD','WEBSITE_URL','WAP_WEBSITE_URL');
        $select_conf_arr = array();

        foreach($web_conf as $key=>$value) {
            if(in_array($value['name'],$select_conf)){
                array_push($select_conf_arr,$value);
            }
        }


        return $select_conf_arr;
    }
    public function get_shop_data_statistics($shop_id){
        $today_time = strtotime(date('Y-m-d'));
        //商家商品数量
        $m_goods=new GoodsModel();
        $g_ids=$m_goods->get_gids_by_shop_id($shop_id);
        $goods=M('goods');
        $goods_count=$goods->where(['shop_id'=>$shop_id,'status'=>1])->count();
        //商家用户数量
        $users=M('users');
        $users_count=count($m_goods->get_uids_by_shop_id($shop_id,2));
        //新增用户数量
        $map = array();
        $map['status'] = 1;
        $map['create_time'] = ['between',"$today_time,".NOW_TIME];
        $map['shop_id']=$shop_id;
        $today_user_add_count = $users->field('id')->where($map)->count();
        $order_list=M('order_list');
        $map=[];
        $map['status'] = 1;
        $map['pay_status'] = 3;
        $map['goods_id']=['in',$g_ids];
        //消费总金额
        $total_money=$order_list->where($map)->sum('money');
        //用户充值金额
        $map['bus_type']='recharge';
        $charge_money=$order_list->where($map)->sum('money');
        //香肠消费金额
        $map['bus_type']='buy';
        $buy_money=$order_list->where($map)->sum('money');
        unset($map['bus_type']);
        $map['create_time'] = ['between',"$today_time,".NOW_TIME];
        //今日消费金额
        $today_money=$order_list->where($map)->sum('money');
        //中奖用户
        $win_users_count=M('win_record')->field('id')->where(['goods_id'=>['in',$g_ids]])->count();
        //商品总销量
        $goods_sale_count=$order_list->where(['goods_id'=>['in',$g_ids],'status'=>1,'pay_status'=>3])->sum('num');
        //晒单总数
        $show_order_count=M('show_order')->field('id')->where(['goods_id'=>['in',$g_ids],'complete'=>1,'status'=>['neq','-1'],'create_time'=>['LT',time()]])->count();
        //揭晓商品总数
        $nper_list_count=M('nper_list')->field('id')->where(['pid'=>['in',$g_ids],'status'=>3])->count();
        return [
            'goods_count'=>$goods_count,
            'users_count'=>$users_count,
            'today_user_add_count'=>$today_user_add_count,
            'total_money'=>$total_money,
            'charge_money'=>$charge_money,
            'buy_money'=>$buy_money,
            'today_money'=>$today_money,
            'win_users_count'=>$win_users_count,
            'goods_sale_count'=>$goods_sale_count,
            'show_order_count'=>$show_order_count,
            'nper_list_count'=>$nper_list_count
        ];
    }

    /**
     * 数据统计
     * @return array
     */
    public function get_data_statistics() {

//        $order_list = M('order_list');
//        $show_order = M('show_order');
        $nper_list = M('nper_list');
        $users = M('users');
        $goods = M('goods');
        $order_list_parent = M('order_list_parent');
        $show_order = M('show_order');

        $today_time = strtotime(date('Y-m-d'));

        //今日新增订单数
        $map = array();
        $map['o.status'] = "1";
        $map['o.pay_status']= "3";
        $map['o.create_time'] = ['between',"$today_time,".NOW_TIME];
        $today_order_count = $order_list_parent->alias("o")
            ->join("users u","u.id = o.uid","left")
            ->field('u.type,SUM(num) as num')
            ->group("u.type")
            ->order("u.type")
            ->where($map)
            ->select();

        //今日新增用户数
        $map = array();
        $map['status'] = 1;
        $map['create_time'] = ['between',"$today_time,".NOW_TIME];
        $today_user_add_count = $users->field('id')->where($map)->count();

        //今日新增商品数量
        $map = array();
        $map['status'] = 1;
        $map['create_time'] = ['between',"$today_time,".NOW_TIME];
        $today_goods_add_count = $goods->field('id')->where($map)->count();

        //今日开奖数量
        $today_micro_time = $today_time * 1000;
        $map = array();
        $map['status'] = 3;
        $map['luck_time'] = ['between',"$today_micro_time,".m_sec_timestamp()];
        $today_nper_open_count = $nper_list->field('id')->where($map)->count();


        //今日账户总收入
        $map = array();
        $map['status'] = 1;
        $map['pay_status'] = 3;
        $map['bus_type'] = "recharge";
        $map['create_time'] = ['between',"$today_time,".NOW_TIME];
        $order_info = $order_list_parent->field('sum(price) as total_money')->where($map)->select();

        $today_order_money = empty($order_info) ? 0 : $order_info[0]['total_money'];


        //商品总数量
        $goods_count = $goods->field('id')->where('status = 1')->count();

        //会员数量
        $users_count = $users->field('id')->where('status = 1')->count();

        //揭晓商品数量
        /*$open_goods_count = $nper_list->field('id')->where('status = 3')->count();*/

        //晒单总数
       /* $show_order_count = $show_order->field('id')->where('complete = 1 AND status != -1 AND create_time<'.time())->count();*/

        //机器人开奖期数
        /*$rt_open_count = $nper_list->field('id')->where("status = 3 AND open_model = 'rt' ")->count();*/
        return array(
            'today_user_add_count' => $today_user_add_count,
            'today_goods_add_count' => $today_goods_add_count,
            'today_nper_open_count' => $today_nper_open_count,
            'today_order_money' => $today_order_money,
            'goods_count' => $goods_count,
            'users_count' => $users_count,
            "today_true_order_count" =>empty($today_order_count[1]['num']) ? 0 :$today_order_count[1]['num'],
            "rt_order_add_count" =>empty($today_order_count[0]['num']) ? 0 :$today_order_count[0]['num'],
        );

    }

    /**
     * 最新订单
     * @return Model
     */
    public function get_new_order() {
        $order_list_parent = M('order_list_parent','sp_');
        $order_list = $order_list_parent->field('name,price,pay_status')->order('id desc')->limit(6)->select();
        return $order_list;
    }




}