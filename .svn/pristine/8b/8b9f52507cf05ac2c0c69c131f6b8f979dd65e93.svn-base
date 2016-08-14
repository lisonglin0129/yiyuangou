<?php
namespace app\lib\oAuth;
use think\Model;

class WeChat{
    //配置APP参数
    private $appid         = 'wxc469a3fd1e82968b';
    private $secret        = '64ec0bccf888099fc551ff5fcb4d0fba';
    //private $state         = 'state';
    private $access_token  = '';
    private $openid        = '';

    public function init($appid,$secret)
    {
        $this->appid=$appid;
        $this->secret=$secret;
    }

    public function get_code($callback)
    {
        //$this->get_state();
        $url = 'https://open.weixin.qq.com/connect/qrconnect?appid='.$this->appid.'&redirect_uri='.urlencode($callback).'&response_type=code&scope=snsapi_login#wechat_redirect';
        header('Location: '.$url);
    }

    public function get_info($code,$state){
        $this->get_access_token($code,$state);
        $userinfo = $this->get_user_info();
        return  $userinfo;
    }

    /**
     * [get_access_token 获取access_token]
     * @param [string] $code [登陆后返回的$_GET['code']]
     * @return [array] [expires_in 为有效时间 , access_token 为授权码 ; 失败返回 error , error_description ]
     */
    public function get_access_token($code)
    {

        //$this->is_state($state);
        //获取access_token
        $token_url           = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
        $result              = json_decode($this->_curl_get_content($token_url),true);
        $this->access_token  = $result['access_token'];
        $this->openid        = $result['openid'];
        return $result;
    }
    /**
     * [get_user_info 获取用户信息]
     * @param [string] $token [授权码]
     * @param [string] $openid [用户唯一ID]
     * @return [array] [ret：返回码，为0时成功。msg为错误信息,正确返回时为空。...params]
     */
    public function get_user_info($token,$openid)
    {
        $url              = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$token.'&openid='.$openid;
        $info             = json_decode($this->_curl_get_content($url), TRUE);
//        $info1['name']    = $info['nickname'];
//        $info1['sex']     = $info['sex'];
//        $info1['img']     = $info['headimgurl'];
//        $info1['openid']  = $info['openid'];
//        $info1['unid']    = $info['unionid'];

        return $info;
    }

    private function _curl_get_content($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置超时时间为3s
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

//    //生成随机参数
//    private function get_state() {
//        $str               = str_shuffle('qazxswedcvfrtgbnhyujmkiol123456789') . time();
//        $_SESSION['state'] = md5(md5($str));
//    }
//
//    //判断随机数
//    private function is_state($state) {
//       if($state!==$_SESSION[$this->state]){
//            exit('随机数检验失败，疑似csrf攻击');
//        }
//    }
}