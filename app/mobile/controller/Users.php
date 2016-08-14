<?php

namespace app\mobile\controller;
use app\admin\model\ConfModel;
use app\admin\model\SpreadModel;
use app\core\model\CommonModel;
use app\core\model\UserModel;
use app\lib\oAuth\SaeTClientV2;
use \think\Controller;
class Users extends AccountBase
{



    /**
     * 个人中心
     * 判断是否登录，否则跳转到登录页面
     * @return mixed
     */
    public function personal_center()
    {
        $Users = D('Users');
        $s_modle=new SpreadModel();
        $result = $Users->personal_center();
//        $user=session('user');
//        if(!empty($user['weibo_id'])){
//            $result['user_face']=$user['profile_image_url'];
//            $result['nick_name']=$user['screen_name'];
//        }elseif(!empty($user['qq_id'])){
//            $result['user_face']=$user['img_url'];
//            $result['nick_name']=$user['nickname'];
//        }
        $this->assign('level',$s_modle->get_spread_by_type(2));
        $this->assign('register',$s_modle->get_spread_by_type(1));
        $this->assign('personal_center', $result);
        return $this->fetch();
    }


    /**
     * 个人资料
     * @return mixed
     */
    public function personal_data()
    {
        $Users = D('Users');
        $result = $Users->personal_data();
        $this->assign('personal_data', $result);
        return $this->fetch();
    }

    /**
     * 退出登录
     * @return mixed
     */
    public function logout()
    {
        session('user', null);
        session(['expire'=>60]);
        session('logout_flag',true);
        $this->redirect('other_users/login');
    }


    /**
     * 收货地址列表
     * @return mixed
     */
    public function address_list()
    {
        $Users = D('Users');
        $result = $Users->address_list();
        $this->assign('address_list', $result);
        return $this->fetch();
    }


    /*
     * 修改昵称
     */
    public function modify_nickname()
    {
        if (IS_AJAX) {
            $Users = D('Users');
            $uid = session('user')['id'];
            $nick_name = I('post.nickname');
            $result = $Users->modify_nickname($uid, $nick_name);
            echo json_encode($result);
        } else {
            return $this->fetch();
        }

    }

    /**
     * 修改手机号
     * @return mixed
     */
    public function modify_phone()
    {
        if (IS_AJAX) {
            $Users = D('Users');
            $uid = session('user')['id'];
            $phone = I('post.phone');
            $code = I('post.code');
            $result = $Users->modify_phone($uid, $phone, $code);
            echo json_encode($result);

        } else {
            return $this->fetch();
        }
    }

    /**
     * 新增收货地址
     * @return mixed
     */
    public function add_address()
    {
        if (IS_AJAX) {
            $Users = D('users');
            $uid = session('user')['id'];
            $name = I('post.name');
            $sn_id = I('post.sn_id');
            $phone = I('post.phone');
            $code = I('post.code');
            $province = I('post.province');
            $city = I('post.city');
            $area = I('post.area');
            $address = I('post.address');
            $type = I('post.type');
            $result = $Users->add_address($uid, $name, $sn_id, $code, $phone, $province, $city, $area, $address, $type);
            echo json_encode($result);
        } else {
            return $this->fetch();
        }
    }

    /**
     *  获取收货地址详情
     */
    public function address_details()
    {
        if (IS_GET) {
            $address_id = I('get.address_id');
            $Users = D('users');
            $result = $Users->get_address_details($address_id);
            $this->assign('address', $result);
            return $this->fetch();
        }
    }


    /**
     * 修改收货地址
     */
    public function modify_address()
    {
        if (IS_AJAX) {
            $Users = D('users');
            $uid = session('user')['id'];
            $address_id = I('post.address_id');
            $name = I('post.name');
            $phone = I('post.phone');
            $province = I('post.province');
            $city = I('post.city');
            $area = I('post.area');
            $address = I('post.address');
            $type = I('post.type');
            $result = $Users->modify_address($uid, $address_id, $name, $phone, $province, $city, $area, $address, $type);
            echo json_encode($result);
        }
    }


    /**
     * 删除地址
     */
    public function delete_address()
    {
        $this->ajax_request();
        $Users = D('users');
        $address_id = I('post.address_id');
        $result = $Users->delete_address($address_id);
        echo json_encode($result);

    }

     //上传头像
    public function upload_portrait()
    {
        if (IS_AJAX) {
            $Users = D('Users');
            $uid = session('user')['id'];
            $nick_name = I('post.nickname');
            $result = $Users->modify_nickname($uid, $nick_name);
            echo json_encode($result);
        } else {
            $user = new UserModel();
            $user = $user->get_headshot();
            $this->assign('headshot',$user['img_path']);
            return $this->fetch();
        }

    }

}


















