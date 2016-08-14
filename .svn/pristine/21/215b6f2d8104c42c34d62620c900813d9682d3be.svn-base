<?php
namespace app\mobile\controller;

use app\core\controller\Api;
use app\mobile\model\Common;

class Message extends Api
{
    public function send($phone, $type = 'reg')
    {

        $conf = new Common();

        //如果类型是reg，判断该手机号是否已注册
        if ($type == 'reg') {
            $Users = M('users');
            $user_id = $Users->field('id')->where(array("phone" => $phone))->find();
            if ($user_id) {
                return array(
                    'code' => '104',
                    'status' => 'fail',
                    'message' => '该手机号码已经注册过'
                );
            }
        }
        if ($type == 'modify_phone') {
            $Users = M('users');
            $user_id = $Users->field('id')->where(array("phone" => $phone))->find();
            if ($user_id) {
                return array(
                    'code' => '104',
                    'status' => 'fail',
                    'message' => '该手机号码已经注册'
                );
            }
        }

        //判断该手机号该注册类型在一分钟之内是否已经接受过验证码
        $PhoneCode = M('phone_code');
        $exist_code = $PhoneCode->field('create_time')->where(array('phone' => $phone, 'type' => $type))->order('id DESC')->limit(1)->find();

        if ($exist_code) {
            if (time() - $exist_code['create_time'] < 60) {
                return array(
                    'code' => '164',
                    'status' => 'fail',
                    'message' => '一分钟之内不可再次请求发送验证码'
                );
            }
        }

        $code = mt_rand(1000, 9999);
        $res = $this->get_phone_code_auto($phone, $code);
        $res = json_encode($res);
        return $this->add_code($code, $res, $phone, $type);
        //螺丝帽发送
//        $luosimao_key = $conf->get_conf('LUOSIMAO_KEY');

//        $code = mt_rand(1000,9999);
//
//        $res = send_sms_luosimao($phone,$code,$luosimao_key);
//
//        if(isset($res['msg']) && $res['msg']== 'ok') {
//
//        }else{
//            $res = $this->get_phone_code_auto($phone,$code);
//            //阿里大鱼发送
//            $alidayu_key = $conf->get_conf('ALIDAYU_KEY');
//            $alidayu_secret = $conf->get_conf('ALIDAYU_SECRET');
//            $res = TbSend($phone,$code,$alidayu_key,$alidayu_secret);

//            $res = json_encode($res);
//
//            $res_arr = json_decode($res,true);
//
//            if(isset($res_arr['result']['success']) && $res_arr['result']['success'] == 'true') {
//                return $this->add_code($code,$res,$phone,$type);
//            }else{
//                return array(
//                    'code' => '199',
//                    'status' => 'fail',
//                    'message' => '验证码发送失败'
//                );
//            }
//        }
    }

    /**
     * 插入验证码
     * @param $code
     * @param $res
     * @param $phone
     * @param $type
     * @return array
     */
    public function add_code($code, $res, $phone, $type)
    {
        $PhoneCode = M('phone_code');
        $data['session'] = md5(time());
        $data['use_times'] = 0;
        $data['phone_code'] = $code;
        $data['data'] = $res;
        $data['phone'] = $phone;
        $data['type'] = $type;
        $data['enable'] = 'true';
        $data['expire_time'] = time() + 60 * 10;
        $data['create_time'] = time();
        $add_res = $PhoneCode->add($data);

        if ($add_res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '发送成功'
            );
        } else {
            return array(
                'code' => '198',
                'status' => 'fail',
                'message' => '手机验证码数据库插入失败'
            );
        }
    }
}
