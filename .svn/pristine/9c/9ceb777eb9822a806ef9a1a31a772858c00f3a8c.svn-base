<?php
namespace app\mobile\controller;
use app\core\model\CommonModel;
use app\lib\oAuth\WeChat;//微信类
use app\lib\oAuth\Recorder;//qq登录配置信息
use app\lib\oAuth\QC;//qq登录操作类
use app\lib\oAuth\Oauth;//qq登录类
use app\lib\oAuth\SaeTClientV2;//weibo操作类
use app\lib\oAuth\SaeTOAuthV2;//weibo登录类
use app\mobile\model\Common;
use \think\Controller;


class Login extends Controller
{
    private $c;
    public function __construct(){
        $this->c=new CommonModel();
    }
    //登录请求登录页面
    public function login($type)
    {
        //$type = I('get.type');
        if ($type == 'qq') {
            $oauth = new Oauth();
            $oauth->qq_login();
        }
        if ($type == 'wechat') {
            //微信登录
                $m = new CommonModel();
                $appid = $m->get_conf('UNION_WECHAT_MP_APPID');
                $redirect_uri = urlencode(U('mobile/other_users/wechat_auth',null,true,true));
                $this->redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=from_wechat_menu#wechat_redirect");
        }
        if ($type == 'weibo') {
            $url = "http://".$_SERVER['HTTP_HOST']."/index.php/mobile/Login/callback/type/weibo";
            $oauth = new SaeTOAuthV2($this->c->get_conf('UNION_SINA_WEIBO_ID'), $this->c->get_conf('UNION_SINA_WEIBO_KEY'));
            $oauth = $oauth->getAuthorizeURL($url);
            header('Location: ' . $oauth);
        }
    }

    //登录回调地址
    public function callback()
    {
        $type = I('get.type');
        if ($type == 'qq') {
            //请求accesstoken
            $oauth = new Oauth();
            $accesstoken = $oauth->qq_callback();
            $openid = $oauth->get_openid();
          //  echo $openid;
            setcookie('qq_accesstoken', $accesstoken, time() + 86400);
            setcookie('qq_openid', $openid, time() + 86400);
            $qc = new QC($accesstoken, $openid);
            $userinfo = $qc->get_user_info();
            $Users = D('users');
            $result = $Users->qqlogin($openid, $userinfo);
            if ($result) {
                $this->redirect('Users/personal_center');
            }
        }
        if ($type == 'wechat') {
            $code = I('get.code');
            $WeChat = new WeChat();
            $result = $WeChat->get_access_token($code);
            $openid = $result['openid'];
            $token = $result['access_token'];

            $userinfo = $WeChat->get_user_info($token, $openid);

            $union_id = "";
            if(!isset($userinfo['errcode'])){
                $union_id = $userinfo['unionid'];
            }


            setcookie('accesstoken', $result['access_token'], time() + 86400);
            $Users = D('users');
            $result = $Users->weChatLogin($openid,$union_id);
            if ($result) {
                $this->redirect('Users/personalCenter');
            }

        }
        if ($type == 'weibo') {
            $code = I('get.code');
            $keys['code'] = $code;
            $keys['redirect_uri'] = U('login/callback',['type'=>'weibo'],'.html',true);
            $oauth = new SaeTOAuthV2($this->c->get_conf('UNION_SINA_WEIBO_ID'), $this->c->get_conf('UNION_SINA_WEIBO_KEY'));
            $oauth = $oauth->getAccessToken($keys);
            setcookie('access_token', $oauth['access_token'], time() + $oauth['expires_in']);
            $id=$oauth['uid'];
            $Users=D('users');
            $c_model=new Common();
            $sae=new SaeTClientV2($c_model->get_conf('UNION_SINA_WEIBO_ID'),$c_model->get_conf('UNION_SINA_WEIBO_KEY'),$oauth['access_token']);
            $info=$sae->show_user_by_id($id);
            $result=$Users->weiboLogin($id,$info);
            if($result){
                $this->redirect('Users/personal_center',['access_token'=>$oauth['access_token']]);
            }
        }

    }
}
