<?php
namespace app\core\lib;

use app\core\model\AuthModel;
use app\core\model\CommonModel;
use app\lib\tb_sdk\top\TopClient;
use app\lib\tb_sdk\top\request;
use think\Exception;

/**
 * 手机验证码
 * User: phil
 * Date: 2016/3/24
 * Time: 14:03
 */
Class PhoneCode
{
    public function __construct()
    {
        $m_auth = new AuthModel();
        $m_auth->init();
    }

    //$flag=true时允许发送,重试的时候防止多发
    public function send_sms_luosimao($mobile, $phone_code, $plat_name = '')
    {
        $m_com = new CommonModel();
        $luosi_key = C('LUOSIMAO_KEY');
        $content = "验证码:" . $phone_code  . $plat_name ;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-' . $luosi_key);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $mobile, 'message' => $content));

        $res = curl_exec($ch);
        curl_close($ch);
        //file_put_contents("sms.txt", "luosimao send success ! " . $mobile . " : " . $res, FILE_APPEND);
        $json = json_decode($res, true);
        $json['plat'] = 'luosimao';
        return $json;
    }

    //获奖螺丝帽发送短信
    public function luosimao_win($mobile, $nper_id)
    {
        $base_model = new CommonModel();
        $web_name = $base_model->get_conf("WEBSITE_NAME");
        $plat_name = $base_model->get_conf("LUOSIMAO_SIGN");

        $luosi_key = C('LUOSIMAO_KEY');
        $content = "恭喜您在第" . $nper_id . "期中获奖,请登录" . $web_name . "官网查看【" . $plat_name . "】";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-' . $luosi_key);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $mobile, 'message' => $content));

        $res = curl_exec($ch);
        curl_close($ch);
        //file_put_contents("sms.txt", "luosimao send success ! " . $mobile . " : " . $res, FILE_APPEND);
        $json = json_decode($res, true);
        $json['plat'] = 'luosimao';
        return $json;
    }

    public function alidayu_win($phone){

        $top = new TopClient();
        $m_com = new CommonModel();

        $appkey = $m_com->get_conf("ALIDAYU_KEY");
        $secret = $m_com->get_conf("ALIDAYU_SECRET");
        $ali_tmp = $m_com->get_conf("ALIDAYU_WIN");
        $msg_sign = $m_com->get_conf("ALIDAYU_WIN_SIGN");
        if(empty($ali_tmp))return false;

        $top->appkey = $appkey;
        $top->secretKey = $secret;
        $req = new request\AlibabaAliqinFcSmsNumSendRequest();
        $req->setExtend("654321");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($msg_sign);
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($ali_tmp);
        $obj = $top->execute($req);
        return obj_to_arr($obj);
    }

    //鼎信企业通短信接口
    public function dingxintong_send($mobile, $phone_code, $product)
    {
        if (empty($mobile) || empty($phone_code)) return false;

        $base_model = new CommonModel();
        $username = $base_model->get_conf("DINGXIN_USERNAME");
        $password = $base_model->get_conf("DINGXIN_PASSWORD");
        $appid = $base_model->get_conf("DINGXIN_APIID");
        $product = $base_model->get_conf("DINGXIN_SIGN");
        $ch = curl_init();
        $url = "http://121.40.160.86:7890/msgapiv2.aspx?action=send&username=$username&password=$password&apiid=$appid&mobiles=" . $mobile . "&text=【" . $product . "】您申请的注册验证码是:" . $phone_code;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        $res = json_encode(simplexml_load_string(curl_exec($ch)), JSON_UNESCAPED_UNICODE);
        return $res;
    }

    //鼎信企业通中奖短信接口
    public function dingxintong_win($mobile, $nper_id)
    {
        if (empty($mobile) || empty($nper_id)) return false;

        $base_model = new CommonModel();
        $web_name = $base_model->get_conf("WEBSITE_NAME");
        $username = $base_model->get_conf("DINGXIN_USERNAME");
        $password = $base_model->get_conf("DINGXIN_PASSWORD");
        $appid = $base_model->get_conf("DINGXIN_APIID");
        $product = $base_model->get_conf("DINGXIN_SIGN");
        $ch = curl_init();
        $url = "http://121.40.160.86:7890/msgapiv2.aspx?action=send&username=$username&password=$password&apiid=$appid&mobiles=" . $mobile . "&text=【" . $product . "】恭喜您在第" . $nper_id . "期中获奖,请登录" . $web_name . "官网查看";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        try {
            $res = json_encode(simplexml_load_string(curl_exec($ch)), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $res = false;
        }
        return $res;
    }


    public function alidayu_reg_sms($phone, $phone_code, $product = '')
    {
        $top = new TopClient();
        $m_com = new CommonModel();

        $appkey = $m_com->get_conf("ALIDAYU_KEY");
        $secret = $m_com->get_conf("ALIDAYU_SECRET");
        $ali_tmp = $m_com->get_conf("MSG_REG_ALIDAYU");
        if (empty($ali_tmp)) return false;

        $top->appkey = $appkey;
        $top->secretKey = $secret;
        $req = new request\AlibabaAliqinFcSmsNumSendRequest();
        $req->setExtend("654321");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("注册验证");
        $param = '{"code":"' . $phone_code . '","product":"' . $product . '"}';
        $req->setSmsParam($param);
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($ali_tmp);
        $obj = $top->execute($req);
        return obj_to_arr($obj);
    }

    //身份验证验证码
    public function alidayu_forgot_sms($phone, $phone_code, $product = '')
    {
        $top = new TopClient();
        $m_com = new CommonModel();

        $appkey = $m_com->get_conf("ALIDAYU_KEY");
        $secret = $m_com->get_conf("ALIDAYU_SECRET");
        $ali_tmp = $m_com->get_conf("MSG_AUTH_ALIDAYU");
        $product = empty(C('SMS_TEMP_NAME')) ? $product : C('SMS_TEMP_NAME');
        if (empty($ali_tmp)) return false;

        $top->appkey = $appkey;
        $top->secretKey = $secret;
        $req = new request\AlibabaAliqinFcSmsNumSendRequest();
        $req->setExtend("654321");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("注册验证");
        $param = '{"code":"' . $phone_code . '","product":"' . $product . '"}';
        $req->setSmsParam($param);
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($ali_tmp);
        $obj = $top->execute($req);
        return obj_to_arr($obj);
    }
}