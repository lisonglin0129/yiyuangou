<?php
namespace app\admin\model;

Class UsersModel extends BaseModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('users',null);
    }

    //获取用户列表
    public function get_users_list($post)
    {
        $m = M('users', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_users
        WHERE user_group <> '1' AND status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        foreach($info as $key=>$value) {
            $info[$key]['user_face'] = $this->get_one_image_src($value['user_pic']);
        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }
    //获取商家用户列表
    public function get_shop_users_list($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  id,username,phone,real_name,status,money,score,create_time
        FROM  sp_users
        WHERE  status<>'-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }

    //获取用户详情
    public function get_info_by_id($id)
    {
        $user_info = $this->m->where(array("id" => $id))->find();
        $user_info['user_face'] = $this->get_one_image_src($user_info['user_pic']);

        return $user_info;
    }
    //获取用户类型
    public function get_user_type_by_id($id){
        return M('users','sp_')->where(['id'=>intval($id)])->getField('type');
    }

    //添加用户
    public function update_users($post)
    {
        $shop_id=$status = $type = $username = $phone = $password = $re_password = $role_list = $user_group = $nick_name = $user_pic = $origin = $age = $sex = $money = $score = $email = $real_name = $qq = $ip_area = $reg_ip = $zip_code = $update_time = $create_time= null;
        extract($post);

        //如果全部正确,执行写入数据
        $data = array(
            "id" => !empty($id) ? $id : "",
            "status" => $status,
            "type" => $type,
            "username" => $username,
            "role_list" => !empty($role_list) ? $role_list : '',
            "phone" => $phone,
            "nick_name" => $nick_name,
            "origin" => $origin,
            "age" => $age,
            "sex" => $sex,
            "money" => $money,
            "score" => $score,
            "email" => $email,
            'user_pic' => empty($user_pic)?intval(C('WEBSITE_AVATAR_DEF')):$user_pic,
            "qq" => $qq,
            'zip_code' => $zip_code,
            "real_name" => $real_name,
            "ip_area" => $ip_area,
            "reg_ip" => $reg_ip,
            'user_group' => !empty($user_group) ? $user_group : 0,
            "update_time" => !empty($update_time)?$update_time:'',
            "create_time" => !empty($create_time)?$create_time:'',
            "shop_id"=>!empty($shop_id)?$shop_id:''

        );

        if(!empty($password) && $re_password == $password) {
            $data['password'] = md6($password);
        }elseif(!empty($password) && $re_password != $password) {
            wrong_return('重复密码与密码不一致');
        }
        if (!empty($id) && is_numeric($id)) {
            $r = $this->m->where(array("id" => $id))->save($data);
            return $r !== false;
        } else {
            return $this->m->add($data);
        }
    }

    //删除用户
    public function del_users($id)
    {
        return $this->m->where(array("id" => $id))->save(array("status" => "-1"));
    }

    //检测是否已存在
    public function check_exist($username='',$phone=''){
        $sql = 'SELECT * FROM sp_users WHERE 1=1';
        if($username&&!$phone){
            $sql .= ' AND username="'.$username.'"';
        }elseif($phone&&!$username){
            $sql .= ' AND phone="'.$phone.'"';
        }elseif($username&&$phone){
            $sql .= ' AND (phone="'.$phone.'" OR username="'.$username.'")';
        }
        return $this->m->query($sql);
    }
    //获取后台用户列表
    public function get_admin_users_list($post)
    {
        $m = M('users', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_users
        WHERE user_group = 1 AND status <> '-1' AND type <> 2" . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        foreach($info as $key=>$value) {
            $info[$key]['user_face'] = $this->get_one_image_src($value['user_pic']);
        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }
    //获取商家列表
    public function get_shop_list($post){
        $m = M('users', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_users
        WHERE user_group = 1 AND status <> '-1' AND type = 2" .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);
        $order_list=M('order_list');
        //消费总金额
        $m_goods=new GoodsModel();
        foreach($info as $key=>$value) {
            $g_ids=$m_goods->get_gids_by_shop_id($value['id']);
                $info[$key]['total_money']=$g_ids?$order_list->where('status=1 AND pay_status=3 AND goods_id IN ('.$g_ids.') '.$post->wheresql)->sum('money'):'';
                $info[$key]['charge_money']=$g_ids?$order_list->where('status=1 AND pay_status=3 AND bus_type="recharge" AND goods_id IN ('.$g_ids.') '.$post->wheresql)->sum('money'):'';
                $info[$key]['goods_sale_count']=$g_ids?$order_list->where('status=1 AND pay_status=3  AND goods_id IN ('.$g_ids.') '.$post->wheresql)->sum('num'):'';
            $info[$key]['user_face'] = $this->get_one_image_src($value['user_pic']);
        }
        //获取所以商家汇总
        //获取所以商家id
        $shop_ids=$this->m->field('id')->where(['status'=>1,'type'=>2])->select();
        $res=[];
        foreach($shop_ids as $v){
            $res[]=$v['id'];
        }
        //获取商家的产品id
        $all_g_ids=$m_goods->get_gids_by_shop_id(implode(',',$res));
        if(!empty($all_g_ids)){
            $rt['total']=$order_list->where('status=1 AND pay_status=3 AND goods_id IN ('.$all_g_ids.') '.$post->wheresql)->sum('money');
            $rt['charge']=$order_list->where('status=1 AND bus_type="recharge" AND pay_status=3 AND goods_id IN ('.$all_g_ids.') '.$post->wheresql)->sum('money');
            $rt['sale']=$order_list->where('status=1 AND pay_status=3 AND goods_id IN ('.$all_g_ids.') '.$post->wheresql)->sum('num');
            $rt["data"] = $info;
            $rt["count"] = $num[0]["num"];
            return $rt;
        }


    }

    public function del_select($ids) {
        return $this->m->where(array('id'=>['in',$ids]))->save(array('status'=>-1));
    }

    public function import_users($users){
        return $this->m->addAll($users);
    }








    
}