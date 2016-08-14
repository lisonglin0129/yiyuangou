<?php

namespace app\mobile\model;

use app\admin\model\SpreadModel;
use app\core\model\CommonModel;
use app\core\model\RewardModel;
use app\lib\oAuth\SaeTClientV2;
use think\Model;
use think\Db;
class Users extends Base {

    private $check_phone_error = array(
        'code' => '152',
        'status' => 'fail',
        'message' => '帐号不符合规范'
    );

    /**
     * 用户登录
     * @param $phone
     * @param $password
     * @return array
     */
    public function login($phone,$password) {
        if(empty($phone) || empty($password) ){
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        if(!$this->check_phone($phone)) {
            return $this->check_phone_error;
        }


        //验证密码
        $user_info = $this->where("status = 1 AND (username = '" . $phone . "' OR phone = '" . $phone . "' OR email='".$phone."')")->find();

        if(!$user_info) {



            return array(
                'code' => '405',
                'status' => 'fail',
                'message' => '账号或者密码不存在'
            );

        }

        if ($user_info['password'] == md6($password) || $user_info['password'] == md5($password)) {

            //修改用户表最后登录id
            $user_map['id'] = $user_info['id'];
            $user_data['last_login_ip'] = get_client_ip();
            $this->where($user_map)->save($user_data);

            //判断是否存在cookie购物车信息，并进行存储

            $cart_info = cookie('cart_info');
            if(isset($cart_info) && !empty($cart_info)) {

                $ShopCart = M('shop_cart');
                $NperList = M('nper_list');

                foreach($cart_info as $key=>$value) {
                    $nper_arr = $NperList->field('sum_times,participant_num')->where(array("id" =>$key))->find();
                    $remain_num = $nper_arr['sum_times'] - $nper_arr['participant_num'];

                    //如果购物车的数量大于该期数的剩余数量，直接放弃添加
                    if($remain_num < $value) {
                        continue;
                    }


                    //判断购物车是否有该用户的该期商品，有的话直接修改数量，没有的话再添加到数据库
                    $exist_cart = $ShopCart->field('id,num')->where('nper_id ='.$key.' AND uid ='.$user_info['id'])->find();

                    if($exist_cart) {
                        //改变数量
                        $ShopCart->where(array('id'=>$exist_cart['id']))->setInc('num',$value);

                    }else{
                        $cart_arr = array();
                        $cart_arr['uid'] = $user_info['id'];
                        $cart_arr['nper_id'] = $key;
                        $cart_arr['num'] = $value;
                        $cart_arr['create_time'] = time();
                        $ShopCart->add($cart_arr);
                    }

                }

                //清除cookie cart_info信息
                cookie('cart_info',null);

            }

            unset($user_info['password']);
            //存储用户session信息
			session(['expire'=>3600]);
           session('user',$user_info);





            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功',
                'uid' => $user_info['id']
            );




        } else {
            return array(
                'code' => '403',
                'status' => 'fail',
                'message' => '密码错误'
            );

        }

    }

    /**
     * 个人中心
     * @return mixed
     */
    public function personal_center() {
        $user_session = session('user');
        $user_info = $this->field('nick_name,user_pic,money,score')->where(array('id' =>$user_session['id']))->find();
        if($user_info['user_pic']) {
            $user_info['user_face'] = $this->get_one_image_src($user_info['user_pic']);
        }
        return $user_info;
    }

    /**
     * 个人资料
     * @return mixed
     */
    public function personal_data() {
        $user_session = session('user');
        $user_info = $this->field('id as user_id,nick_name,phone,username,user_pic,money,create_time')->where(array('id' =>$user_session['id']))->find();

        $user_info['user_face'] = $this->get_one_image_src($user_info['user_pic']);

        return $user_info;
    }

    /**
     * 地址列表
     * @return mixed
     */
    public function address_list() {
        $user_session = session('user');


        $UserAddress = M('user_addr');

        $addressList = $UserAddress->field('id as address_id,name,phone,provice,city,area,address,type,provice_id,city_id,area_id')->where(array("uid" =>$user_session['id']))->select();

        return $addressList;
    }

    //检测手机号是否被注册和用户状态
    public function check_phone_status($phone){
        $user_data = M("users")->where(array("phone" => $phone))->select();
        foreach($user_data as $key=>$value){
            if($value['status']==1){
                return true;
            }
        }
        return false;
    }


    /**
     * 用户注册
     */

    public function register($phone,$password,$rePassword,$code,$origin='wap',$agree,$nper_id,$spread_userid,$openid) {
        if(empty($agree)) {
            return array(
                'code' => '169',
                'status' => 'fail',
                'message' => '同意用户协议方可进行注册'
            );
        }

        if ( empty($origin) ) {
            $origin = 'wap';
        }


        if(empty($phone) || empty($password) || empty($code)){
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        if($rePassword != $password) {
            return array(
                'code' => '103',
                'status' => 'fail',
                'message' => '密码与确认密码不同'
            );
        }


        if(!$this->check_phone($phone)) {
            return $this->check_phone_error;
        }
        //判断是否重复注册

        $user_id = $this->check_phone_status($phone);
        if($user_id) {
            return array(
                'code' => '104',
                'status' => 'fail',
                'message' => '用户重复注册'
            );
        }
        //判断验证码是否失效
        $ShopCode = M('phone_code');
        $map['phone'] = $phone;
        $map['phone_code'] = $code;
        $map['type'] = 'reg';
        $code_info = $ShopCode->field('enable,expire_time')->where($map)->order('id desc')->find();

        if(!$code_info) {
            return array(
                'code' => '106',
                'status' => 'fail',
                'message' => '验证码错误'
            );
        }
        $code_time = $code_info['expire_time'];
        if(time() - $code_time > 60*10 || $code_info['enable'] != true) {
            return array(
                'code' => '103',
                'status' => 'fail',
                'message' => '验证码已失效'
            );
        }



        //注册用户
        $str = rand_str('r',6);
        $data['phone'] = $phone;
        $data['user_pic'] = empty($user_pic) ? intval(C('WEBSITE_AVATAR_DEF')) : $user_pic;
        $data['password'] = md6($password);
        $data['origin'] = $origin;
        $data['reg_ip'] = get_client_ip();
        $data['username'] = $phone.rand(1000,9999);
        $data['nick_name'] = microtime_float().rand_str('r',4);
        $data['origin']=$origin;
        $data['wx_mp_openid']=!empty($openid)?$openid:'';
        if(!empty($nper_id)){
            $data['money']=1;
        }
        if($id = $this->add($data)) {
            //注册推广
            $this->reg_reward_deal($spread_userid,$id);
            //注册返现
            $this->register_reward($id);
            //注册返积分
            $this->register_win_score($spread_userid,$id);
            //存储session
            $session_info = array(
                'phone' => $phone,
                'id' => $id
            );
			session(['expire'=>3600]);
            session('user',$session_info);

            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '注册成功',
                'uid'=>$id
            );
        }else{
            return array(
                'code' => '105',
                'status' => 'fail',
                'message' => '注册失败'
            );
        }



    }

    //注册返积分
    public function register_win_score($spread_uid="",$uid=""){
        if(!empty($spread_uid)){
            $s_model=M('spread','sp_');
            $u_model=M('users','sp_');

            $spread_set=$s_model->where(['type'=>2])->find();
            if((!empty($spread_set))&&$spread_set['status']==1) {
                $spread_uids=$this->get_uids_by_level($spread_set['level'],$uid);
                if($spread_uids){
                    $score_reward=explode(',',$spread_set['score_reward']);
                    Db::startTrans();
                    try{
                        foreach($spread_uids as $k=>$v){
                            $user_status=$u_model->where(['id'=>$v])->getField('status');
                            if($user_status==1){
                                $u_model->where(['id'=>$v])->setInc("score",$step=$score_reward[$k]);
                            }
                        }
                        Db::commit();
                    }catch (Exception $e){
                        Db::rollback();
                    }
                }
            }

        }
    }

    //递归获取n级推广用户id
    public function get_uids_by_level($level,$uid,&$spread_uids=[]){
        $u_model=M('users','sp_');
        $spread_uid=$u_model->where(['id'=>$uid])->getField('spread_userid');
        if(!empty($spread_uid)&&(count($spread_uids)<$level)){
            $spread_uids[]=$spread_uid;
            $this->get_uids_by_level($level,$spread_uid,$spread_uids);
        }
        return $spread_uids;
    }
    //注册返现
    public function register_reward($id){
        $c_model=new Common();
        $reg_reward_start=$c_model->get_conf('register_money');
        if(!empty($reg_reward_start)&&$reg_reward_start==1){
            $reg_reward_money=$c_model->get_conf('register_money_reward');
            if(!empty($reg_reward_money)&&!empty($id)){
                $this->save(['id'=>$id,'money'=>floatval($reg_reward_money)]);
            }
        }
    }
    public function reg_reward_deal($spread_userid,$id){
        //判断ip是否注册过
        $res=M('reg_reward')->where(['reg_ip'=>get_client_ip()])->select();
        //注册推广
        if((!empty($spread_userid))&&empty($res)){
            $s_model=new SpreadModel();
            $spread=$s_model->get_spread_by_type(1);
            if(!empty($spread)){
                if($spread['status']==1){
                        $r_model=new RewardModel();
                        $d=[
                            'uid'=>$id,
                            'spread_uid'=>$spread_userid,
                            'reward'=>$spread['money'],
                            'create_time'=>time(),
                            'reg_ip'=>get_client_ip()
                        ];
                        if($r_id=$r_model->add_reg_reward($d)){
                            $this->save(['id'=>$id,'spread_userid'=>$spread_userid]);
                        }
                }
            }
        }
    }

    /**
     * 其他人的个人信息
     * @param $uid
     * @return mixed
     */
    public function other_user_info($uid) {

        $user_info = $this->field('id as user_id,nick_name,phone,username,user_pic,money')->where(array('id' =>$uid))->find();
        if(empty($user_info)) {
            return array();
        }

        $user_info['user_face'] = $this->get_one_image_src($user_info['user_pic']);

        return $user_info;

    }





    /**
     * 修改昵称
     * @param $phone
     * @param $nick_name
     * @return array
     */
    public function modify_nickname($uid,$nick_name) {
        if(empty($uid) || empty($nick_name)) {
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        $user_info = $this->field('id,nick_name')->where(array("id" => $uid))->find();

        if($nick_name == $user_info['nick_name']) {
            return array(
                'code' => '114',
                'status' => 'fail',
                'message' => '昵称不得与原昵称相同'
            );
        }

        $data['nick_name'] = $nick_name;

        $res = $this->where(array("id" => $uid))->save($data);

        if($res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '修改成功'
            );
        }else{
            return array(
                'code' => '115',
                'status' => 'fail',
                'message' => '修改昵称失败'
            );
        }

    }






    /**
     * 修改手机号
     * @param $session_id
     * @param $phone
     * @param $code
     * @return array
     */
    public function modify_phone($uid,$phone,$code) {

        if(empty($uid) || empty($phone)) {
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }


        //检测手机号
        if(!$this->check_phone($phone)) {
            return $this->check_phone_error;
        }

        $phone_arr = $this->field('phone')->where(array("id" => $uid))->find();

        if($phone == $phone_arr['phone']) {
            return array(
                'code' => '116',
                'status' => 'fail',
                'message' => '原手机号码与新手机号码不得相同'
            );
        }

        //验证数据库中是否已经有该手机号
        $exist_phone = $this->field('id')->where(array("phone" => $phone))->find();

        if($exist_phone) {
            return array(
                'code' => '116',
                'status' => 'fail',
                'message' => '数据库中已有该手机号'
            );
        }


        //判断验证码
        $ShopCode = M('phone_code');
        $code_map['phone'] = $phone;
        $code_map['phone_code'] = $code;
        $code_map['type'] = 'modify_phone';
        $code_info = $ShopCode->field('enable,expire_time')->where($code_map)->order('id desc')->find();

        if(!$code_info) {
            return array(
                'code' => '106',
                'status' => 'fail',
                'message' => '验证码错误'
            );
        }
        $code_time = $code_info['expire_time'];
        if(time() > $code_time || $code_info['enable'] != true) {
            return array(
                'code' => '103',
                'status' => 'fail',
                'message' => '验证码已失效'
            );
        }

        $res = $this->where(array("id" => $uid))->setField('phone',$phone);

        if($res) {
            // 修改session里手机号
            $session_info = array(
                'phone' => $phone,
                'id' => $uid
            );
			session(['expire'=>3600]);
            session('user',$session_info);
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功'
            );
        }else{
            return array(
                'code' => '117',
                'status' => 'fail',
                'message' => '手机修改失败'
            );
        }




    }


    /**
     * 检验手机号是否符合规范
     * @param $phone
     * @return bool
     */
    private function check_phone($phone) {
        $preg = '/(^[^\d_][0-9a-zA-Z\x{4e00}-\x{9fa5}_]{4,20}$)|(^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$)|(^1\\d{10}$)/u';
        if(preg_match("$preg", $phone)){
            return true;
        }else{
            return false;
        }
    }







    /**
     * 新增地址
     * @param $uid
     * @param $name
     * @param $phone
     * @param $provice
     * @param $city
     * @param $area
     * @param $address
     * @param $type
     * @return array
     */

    public function add_address($uid, $name,$sn_id,$code, $phone, $province, $city, $area, $address, $type){

        if(empty($uid) || empty($name) || empty($code) ||empty($phone) || empty($province) || empty($city) || empty($area) || empty($address)) {
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        $UserAddress = M('user_addr');

        if($type == '1') {
            $addressList = $UserAddress->field('id')->where(array("uid" => $uid))->select();
            if($addressList) {
                $UserAddress->where(array("uid" => $uid))->setField('type','0');
            }
        }
        $data['uid'] = $uid;
        $data['name'] = $name;
        $data['sn_id']=empty($sn_id)?$sn_id:'';
        $data['code']=$code;
        $data['phone'] = $phone;
        $data['provice'] = $province;
        $data['city'] = $city;
        $data['area'] = $area;
        $data['address'] = $address;
        $data['type'] = $type;
        if($address_id = $UserAddress->add($data)) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功',
                'address_id' => $address_id
            );
        } else {
            return array(
                'coed' => '119',
                'status' => 'fail',
                'message' => '新增地址失败',
            );
        }

    }


    /**
     * 收货地址详情
     * @param $address_id
     * @return bool|mixed
     */
   public  function get_address_details($address_id){
       if(empty($address_id)){
           return false;
       }
       $UserAddress = M('user_addr');
       $result=$UserAddress->where(array("id"=>$address_id))->find();
       return $result;
   }




    /**
     * 修改地址
     * @param $uid
     * @param $address_id
     * @param $name
     * @param $phone
     * @param $provice
     * @param $city
     * @param $area
     * @param $address
     * @param $type
     * @return array
     */

    public function modify_address($uid,$address_id,$name,$phone,$province,$city,$area,$address,$type) {
        if(empty($uid) || empty($address_id) || empty($name) || empty($phone) || empty($province) || empty($city) || empty($area) || empty($address)) {
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        $UserAddress = M('user_addr');

        if($type == '1') {
            $addressList = $UserAddress->field('id')->where(array("uid" => $uid))->select();
            if($addressList) {
                $UserAddress->where(array("uid" => $uid))->setField('type','0');
            }
        }

        $data['uid'] = $uid;
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['provice'] = $province;
        $data['city'] = $city;
        $data['area'] = $area;
        $data['address'] = $address;
        $data['type'] = $type;
        $res = $UserAddress->where(array("id" => $address_id))->save($data); // 根据条件更新记录

        if($res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功',
                'address_id' => $address_id
            );
        }else{
            return array(
                'coed' => '120',
                'status' => 'fail',
                'message' => '修改地址失败',
            );
        }

    }

    /**
     * 删除地址
     * @param $address_id
     * @return array
     */
    public function delete_address($address_id) {
        if(empty($address_id)) {
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        $UserAddress = M('user_addr');
        $res = $UserAddress->where(array("id" => $address_id))->delete();

        if($res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功',
            );
        }else{
            return array(
                'code' => '121',
                'status' => 'fail',
                'message' => '删除地址失败',
            );
        }


    }

    /**
     * 忘记密码
     * @param $phone
     * @param $password
     * @param $code
     * @return array
    */
    public function get_password($phone,$password,$code) {
        if(empty($phone) || empty($password) || empty($code)) {
            return array(
                'code' => '102',
                'status' => 'fail',
                'message' => '确少参数'
            );
        }

        //判断验证码是否失效
        $ShopCode = M('phone_code');
        $map['phone'] = $phone;
        $map['phone_code'] = $code;
        $map['type'] = 'rst_pass';
        $code_info = $ShopCode->field('enable,expire_time')->where($map)->order('id desc')->find();

        if(!$code_info) {
            return array(
                'code' => '106',
                'status' => 'fail',
                'message' => '验证码错误'
            );
        }
        $code_time = $code_info['expire_time'];
        if(time()  > $code_time || $code_info['enable'] != true) {
            return array(
                'code' => '103',
                'status' => 'fail',
                'message' => '验证码已失效'
            );
        }

        //判断是否该用户已经注册

        $user_id = $this->field('id')->where(array("phone" => $phone))->find();


        if(!$user_id) {
            return array(
                'code' => '104',
                'status' => 'fail',
                'message' => '用户尚未注册'
            );
        }

        //修改密码

        $data['password'] = md6($password);
        $condition['phone']=$phone;
        $res = $this->where($condition)->save($data);


        if($res){
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '修改成功'
            );
        }else{
            return array(
                'code' => '105',
                'status' => 'fail',
                'message' => '修改密码失败'
            );
        }
    }
    //微博第三方登录
    public function weiboLogin($id,$info){
        if(empty($id)){
            return false;
        }
        $condition['weibo_id']=$id;
        $result=$this->field('id,phone,weibo_id,nick_name,user_pic')->where($condition)->find();
        //如果该微博用户已经注册 就直接调出进入个人中心
       // var_dump($result);
        if($result){
            if($result['nick_name']!=$info['screen_name']){
                $this->save(['id'=>$result['id'],'nick_name'=>$info['screen_name'],'username'=>$info['screen_name']]);
            }
            $img_path=M('image_list')->where(['id'=>$result['user_pic']])->getField('img_path');
            if($img_path!=$info['profile_image_url']){
                M('image_list')->save(['id'=>$result['user_pic'],'img_path'=>$info['profile_image_url'],'thumb_path'=>$info['profile_image_url']]);
            }
            $session_info=array(
                'phone' => $result['phone'],
                'id' => $result['id'],
                'username'=>$info['screen_name'],
                'nick_name'=>$info['nick_name']
            );
        }else{
            $data['weibo_id']=$id;
            $data['username']=$info['screen_name'];
            $data['nick_name']=$info['screen_name'];
            $data['user_pic']=M('image_list')->add(['img_path'=>$info['profile_image_url'],'thumb_path'=>$info['profile_image_url'],'type'=>2,'origin'=>1]);
            $userId=$this->add($data);
            $session_info=array(
                'phone' =>'',
                'id' => $userId,
                'username'=>$info['screen_name'],
                'nick_name'=>$info['nick_name']
            );
        }
		session(['expire'=>3600]);
        session('user',$session_info);
        return true;
    }
    //QQ第三方登录
    public function qqlogin($openid,$userinfo){
        if(empty($openid)||empty($userinfo)){
            return false;
        }
        $condition['qq_id']=$openid;
        $result=$this->field('id,phone,qq_id,nick_name,user_pic')->where($condition)->find();
        //如果该qq用户已经注册 就直接调出进入个人中心
        if($result){
            if($result['nick_name']!=$userinfo['nickname']){
               $this->save(['id'=>$result['id'],'nick_name'=>$userinfo['nickname'],'username'=>$userinfo['nickname']]);
            }
            $img_path=M('image_list')->where(['id'=>$result['user_pic']])->getField('img_path');
            if($img_path!=$userinfo['figureurl_qq_1']){
                M('image_list')->save(['id'=>$result['user_pic'],'img_path'=>$userinfo['figureurl_qq_1'],'thumb_path'=>$userinfo['figureurl_qq_1']]);
            }
            $session_info=array(
                'phone' => $result['phone'],
                'id' => $result['id'],
                'username'=>$result['qq_id'],
                'nick_name'=>$userinfo['nickname']
            );
        }else{
            $data['qq_id']=$openid;
            $data['username']=$userinfo['nickname'];
            $data['nick_name']=$userinfo['nickname'];
            $data['user_pic']=M('image_list')->add(['img_path'=>$userinfo['figureurl_qq_1'],'thumb_path'=>$userinfo['figureurl_qq_1'],'type'=>2,'origin'=>1]);
            $userId=$this->add($data);
            $session_info=array(
                'phone' =>'',
                'id' => $userId,
                'username'=>$result['qq_id'],
                'nick_name'=>$userinfo['nickname']
            );
        }
		session(['expire'=>3600]);
        session('user',$session_info);
        return true;
    }


    // 微信第三方登录
    public function weChatLogin($openid,$union_id){
        if(empty($openid)){
            return false;
        }
        if(empty($union_id)){
            $condition['weixin_id']=$openid;
            $result=$this->field('id,phone')->where($condition)->find();
            $data['weixin_id']=$openid;
        }else{
            $condition['unionid']=$union_id;
            $result=$this->field('id,phone')->where($condition)->find();
            $data['unionid']=$union_id;
        }

        //如果该微信用户已经注册 就直接调出进入个人中心
        if($result){
            $session_info=array(
                'phone' => $result['phone'],
                'id' => $result['id'],
                'username'=>!empty($union_id)?$union_id:$openid
            );
            session('user_info',$session_info);
            return true;
        }else{

            if(!empty($union_id)){
                //只是为了生成用户名
                $openid = $union_id;
            }
            $str = rand_str('r', 6);
            $data['username']=substr(md5($openid . $str), 0, 16);
            $userId=$this->add($data);
            $session_info=array(
                'phone' =>'',
                'id' => $userId,
                'username'=>$data['username']
            );
            session('user_info',$session_info);
            return true;
        }
    }

    /*
     * 微信公众平台关联登录
     */
    public function wx_mp_login($openid,$union_id=""){
        if(empty($union_id)){
            $user_info = $this->where(['wx_mp_openid'=>$openid])->find();
        }else{
            $user_info = $this->where(['unionid'=>$union_id])->find();
        }

        if($user_info){

            //修改用户表最后登录id
            $user_map['id'] = $user_info['id'];
            $user_data['last_login_ip'] = get_client_ip();
            $this->where($user_map)->save($user_data);

            //存储用户session信息
            $session_info = array(
                'phone' => $user_info['phone'],
                'id' => $user_info['id']
            );
			session(['expire'=>3600]);
            session('user',$user_info);
            return true;
        }else{
            return false;
        }
    }

    /*
     * 微信公众平台关联
     */

    public function wx_mp_union($uid,$wechat_mp_info,$ext_data=false){
        $sex_map = ['','man','woman'];
        if($wechat_mp_info){
            $openid = $wechat_mp_info['openid'];
            if(!$ext_data){
                $result = $this->where(['id'=>$uid])->data(['wx_mp_openid'=>$openid])->save();
                return $result;
            }else{
                $data = [
                    'wx_mp_openid'=>$openid,
                    'nick_name'=>$wechat_mp_info['nickname'],
                    'sex'=>$sex_map[$wechat_mp_info['sex']],
                    'ip_area'=>$wechat_mp_info['province'].$wechat_mp_info['city'],
                    'origin'=>'wechat_mp',
                ];
                if(key_exists('img_id',$wechat_mp_info)){
                    $data['user_pic'] = $wechat_mp_info['img_id'];
                }
                $result = $this->where(['id'=>$uid])->data($data)->save();
                return $result;
            }
        }
    }









}