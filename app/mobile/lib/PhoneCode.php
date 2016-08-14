<?php
namespace app\mobile\lib;

use app\lib\tb_sdk\top\TopClient;
use app\core\model\CommonModel;
use app\lib\tb_sdk\top\request;

/**
 * 手机验证码
 * User: phil
 * Date: 2016/3/24
 * Time: 14:03
 */
Class PhoneCode
{
    //$flag=true时允许发送,重试的时候防止多发
    public function send_sms_luosimao($mobile, $phone_code, $plat_name = '香肠')
    {
        $m_com = new CommonModel();
        $luosi_key = $m_com->get_conf('LUOSIMAO_KEY');
        $content = "验证码:" . $phone_code . ",关注微信公众号送体验金！【" . $plat_name . "】";
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
        file_put_contents("sms.txt", "luosimao send success ! " . $mobile . " : " . $res, FILE_APPEND);
        $json = json_decode($res, true);
        $json['plat'] = 'luosimao';

        return $json;

    }



    public function alidayu_reg_sms($phone, $phone_code, $product = '香肠购')
    {
        $top = new TopClient();
        $m_com = new CommonModel();

        $appkey = $m_com->get_conf("ALIDAYU_KEY");
        $secret = $m_com->get_conf("ALIDAYU_SECRET");

        $top->appkey = $appkey;
        $top->secretKey = $secret;
        $req = new request\AlibabaAliqinFcSmsNumSendRequest();
        $req->setExtend("654321");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("注册验证");
        $param = '{"code":"' . $phone_code . '","product":"' . $product . '"}';
        $req->setSmsParam($param);
        $req->setRecNum($phone);
        $req->setSmsTemplateCode("SMS_6740183");
        $obj = $top->execute($req);
        return obj_to_arr($obj);
    }
}