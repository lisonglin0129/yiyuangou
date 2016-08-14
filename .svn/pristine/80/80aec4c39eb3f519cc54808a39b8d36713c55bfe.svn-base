<?php
namespace app\mobile\controller;

use app\admin\model\ArticleModel;
use app\core\model\CommonModel;
use app\core\model\UserModel;
use app\lib\iHttp;
use app\lib\Wechat;
use app\mobile\model\OrderList;
use app\mobile\model\ShowOrder;

use app\lib\oAuth\WeChat as wexin;//微信类

use app\mobile\model\Users;
use app\mobile\model\Common;
use app\yyg\controller\User;
use \think\Controller;
use think\Exception;


class OtherUsers extends Base {

    /**
     * 其他用户/移动端 晒单详情
     * @return mixed|void
     */
    public function share_detail()
    {

        $share_id = (int)I('get.share_id');
        if (empty($share_id)) {
            return $this->redirect('index/index');
        }
        $share_order = new ShowOrder();
        $my_share_detail = $share_order->get_my_share_detail($share_id);


        if (empty($my_share_detail)) {
            return $this->redirect('index/index');
        }

        $this->assign('my_share_detail', $my_share_detail);

        $this->assign('imgList', $my_share_detail['imgList']);


        return $this->fetch('share/my_share_detail');
    }

    /**
     * 其他用户的个人中心
     * @return mixed
     */
    public function other_person_center()
    {
        $uid = (int)I('get.uid');

        if (empty($uid)) {
            $this->redirect('index/index');
            return false;
        }

        $Users = new Users();
        $OrderList = new OrderList();
        $ShowOrder = new ShowOrder();

        //个人信息
        $user_info = $Users->other_user_info($uid);
        //夺宝记录
        list($indiana_record,$num) = $OrderList->other_indiana($uid);
        //中奖记录
        $win_record = $OrderList->other_win_record($uid);
        //晒单记录
        $share_order_record = $ShowOrder->other_share_order($uid);

        //分别注入模板
        $this->assign('user_info', $user_info);
        $this->assign('indiana_record', $indiana_record);
        $this->assign('win_record', $win_record);
        $this->assign('num',$num);
        $this->assign('share_order_record', $share_order_record);

        return $this->fetch('users/other_person_center');
    }


    /**
     * 展示登录页面/完成登录功能
     * @return mixed
     */
    public function login()
    {
        //接收登录数据进行登录操作
        if (IS_POST) {
            //判断是否已经登录,如果已经登录调往个人中心
            //调用模型登录方法

            $Users = new Users();
            $m_user_model = new UserModel();
            $result = $Users->login(I('post.phone'), I('post.password'));
            if($result['code']=='200'){
                //微信公众平台
//                if($wechat_mp_info = session('wechat_mp_info')){
//                    $Users->wx_mp_union($result['uid'],$wechat_mp_info);
//                    session('wechat_mp_info',null);
//                }
            }
            //记录最后登录ip
            $m_user_model->record_last_ip();
            //纪录最后的登录时间
            $m_user_model->record_last_time();
            die_json($result);

        } else {
            //展示登录界面
            $url = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:U('/mobile/users/personal_center');
            $this->assign('url',$url);
            return $this->fetch('users/login');
        }
    }

    /**
     * 注册
     * @return mixed
     */
    public function register()
    {
        //接收登录数据进行登录操作
        if (IS_AJAX) {

            //调用模注册方法
            $Users = new Users();
            $result = $Users->register(I('post.phone'), I('post.password'), I('post.rePassword'), I('post.code'), I('post.origin'), I('agree'), I('post.nper_id'), intval(I('post.spread_userid')), I("post.origin"),session('openid'));
            if ($result['code'] == '200') {
                //获取微信信息
//                if($wechat_mp_info = session('wechat_mp_info')){
//                    if(key_exists('headimgurl',$wechat_mp_info)){
//                        $wechat_mp_info['img_id']=$this->fetch_image($wechat_mp_info['headimgurl'],$result['uid']);
//                    }
//                    $Users->wx_mp_union($result['uid'],$wechat_mp_info,true);
//                    session('wechat_mp_info',null);
//                }
            }
            die_json($result);
        }else{
            //展示登录界面
            $nper_id=I('get.nper_id');
            //推广人id
            $spread_userid=I('get.spread_userid')?I('get.spread_userid'):session('spread_userid');
            if(empty($spread_userid)&&(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')!==false)){
                //微信授权获取openid
                $c_model=new Common();
               $wechat=new Wechat([
                   'token'=>$c_model->get_conf('WECHAT_TOKEN'),
                   'encodingaeskey'=>$c_model->get_conf('WECHAT_ENCODING_KEY'),
                   'appid'=>$c_model->get_conf('UNION_WECHAT_MP_APPID'),
                   'appsecret'=>$c_model->get_conf('UNION_WECHAT_MP_APPSEC')]);
                if(!session('openid')){
                    //当前访问链接
                    $cur_url="http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                   session('openid',$wechat->getOauthAccessToken()['openid']) ;
                    $code_url = $wechat->getOauthRedirect($cur_url);
                    if (empty($_GET['code']) && empty($_GET['state'])) {
                        header("Location: $code_url");
                        exit;
                    }
                }
                if(session('openid')){
                    $spread_userid=M('scene')->where(['openid'=>session('openid')])->order('id desc')->limit(1)->getField('scene');
                }
            }
            //获取来源
            if(isset($_GET['q'])){
                $origin = $_GET['q'];
            }else{
                $origin = "";
            }
            return $this->fetch('users/register',['nper_id'=>!empty($nper_id)?$nper_id:'','spread_userid'=>!empty($spread_userid)?$spread_userid:'','origin'=>$origin]);
        }
    }

    /**
     * 用户注册协议
     */
    public function user_agreement() {
        $category  = new ArticleModel();
        $agreement = $category->get_agreement();
        $this->assign('agreement',$agreement);
        return $this->fetch('users/user_agreement');
    }

    /**
     * 注册时获取验证码接口
     * @return array
     */
    public function get_code()
    {
        $this->ajax_request();
        $Message = new Message();
        $res = $Message->send(I('post.phone'), I('post.type'));
        echo json_encode($res);
    }

    /**
     * 忘记密码
     * @return mixed
     */
    public function forget_password()
    {
        if (IS_AJAX) {
            $Users = new Users();
            $phone = I('post.phone');
            $password = I('post.password');
            $code = I('post.code');
            $result = $Users->get_password($phone, $password, $code);
            return json_encode($result);
        }
        return $this->fetch('users/forget_password');
    }

    /**
     * 微信登录
    **/
    public function wechat_auth(){
        $ref = session('login_refer')?session('login_refer'):U('Index/index');
        session('login_refer',null);
        $code = I('get.code',null,'trim');
        $auth_data = $this->get_wechat_access_token($code);

        $open_id = $auth_data['openid'];
        $m_users = new Users();
        $m_user_model = new UserModel();

        $union_id = isset($auth_data['unionid'])?$auth_data['unionid']:"";
            //尝试用openid登录
            $login_result = $m_users->wx_mp_login($open_id,$union_id);
            if ($login_result) {
                //写入用户最后一次登录ip
                $m_user_model->record_last_ip();
                //写入用户最后一次登录时间
                $m_user_model->record_last_time();
            
                return $this->redirect('http://m.xiangchang.com'.$ref);
            }else{//直接注册
                $Users = new Users();
                $str = rand_str('r', 6);
                $user_name = substr(md5($str),0,16);
                $data = [
                    "username" =>  $user_name,
                    "password" => md6($str),
                    "nick_name" => microtime_float().$str,
                    "origin" => I('get.q',$_SERVER['HTTP_HOST'],'trim'),
                    "reg_ip" => get_client_ip(),
                    "unionid"=> $union_id
                ];
                $m_user = new UserModel();
                $id = $m_user->add_user($data);
                if($id>0){
                    $this->v_login($id);
                    $token = $auth_data['access_token'];
                    $wechat_mp_info = $this->get_wechat_user_info($token,$open_id);
                    if(!empty($wechat_mp_info) && key_exists('headimgurl',$wechat_mp_info)){
                        $wechat_mp_info['img_id']=$this->fetch_image($wechat_mp_info['headimgurl'],$id);
                    }
                    $Users->wx_mp_union($id,$wechat_mp_info,true);
                    $Users->reg_reward_deal(session('spread_userid'),$id);
                }
                return $this->redirect($ref);
            }
        }



    public function wechat_spread($uid=null){
        session('spread_userid',$uid);
        session('login_refer',U('Index/index'));
        $m = new CommonModel();
        $appid = $m->get_conf('UNION_WECHAT_MP_APPID');
        $redirect_uri = urlencode(U('mobile/other_users/wechat_auth',null,true,true));
        $this->redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=from_wechat_menu#wechat_redirect");

    }

    /**
     * 微信授权
     * 通过code换取网页授权access_token等信息
     */
    private function get_wechat_access_token($code){
        $m = new Common();
        $appid = $m->get_conf('UNION_WECHAT_MP_APPID');
        $appsec = $m->get_conf('UNION_WECHAT_MP_APPSEC');
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsec}&code={$code}&grant_type=authorization_code";


        //$raw = curl_get($url);
        $http_util = new iHttp();
        $raw = $http_util->get($url);
        if(APP_DEBUG){
            $m_log = M('notify','log_');
            $m_log->add([
                'order_id'=>'get_wechat_access_token',
                'create_time'=>date('Y-m-d H:i:s'),
                'log'=>$raw
            ]);
        }
        if(strlen($raw)>0){
            $data = json_decode($raw,true);
            if(json_last_error()==JSON_ERROR_NONE){
                if(key_exists('access_token',$data)){
                    return $data;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /*
     * 微信授权
     * 通过access_token获取用户基本信息
     */
    private function get_wechat_user_info($token,$open_id){
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$open_id}&lang=zh_CN";
        //$raw = curl_get($url);
        $http_util = new iHttp();
        $raw = $http_util->get($url);
        if(strlen($raw)>0){
            if(APP_DEBUG){
                $m_log = M('notify','log_');
                $m_log->add([
                    'order_id'=>'get_wechat_user_info',
                    'create_time'=>date('Y-m-d H:i:s'),
                    'log'=>$raw
                ]);
            }
            $data = json_decode($raw,true);
            if(json_last_error()==JSON_ERROR_NONE){
                if(key_exists('openid',$data)){
                    return $data;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //获取用户头像
    private function fetch_image($url,$uid,$file_prefix='head_img_'){
        try{
            $save_path = 'data/fetch/';
            $img_content = curl_get($url);
            $filename = $file_prefix.md5(time().$uid).'.jpg';
            file_put_contents($save_path.$filename,$img_content);
            $img_path = '/'.$save_path.$filename;
            $m_img = M('image_list', 'sp_');
            $img_id = $m_img->data([
                'name'=>$filename,
                'uid'=>$uid,
                'img_path'=>$img_path,
                'create_time'=>time(),
                'origin'=>'fetch'
            ])->add();
            return $img_id>0?$img_id:intval(C('WEBSITE_AVATAR_DEF'));
        }catch(Exception $e){
            return intval(C('WEBSITE_AVATAR_DEF'));
        }
    }

    /**根据id模拟登录*/
    private function v_login($val,$type ='uid')
    {
        if(empty($val))return false;
        $m_user = new UserModel();
        switch ($type) {
            case "uid":
                $u_info = $m_user->get_user_info_by_filed("id", $val);
                break;
            case "phone":
                $u_info = $m_user->get_user_info_by_filed("phone", $val);
                break;
            case "username":
                $u_info = $m_user->get_user_info_by_filed("username", $val);
                break;
            //直接传登陆信息
            case "info":
                $u_info = $val;
                break;
            default:
                if(startWith($type,'union_')){
                    $field = substr($type,6);
                    $u_info = $m_user->get_user_info_by_filed($field, $val);
                }else{
                    $u_info = null;
                }
        }

        if (empty($u_info)) return false;
        unset($u_info['password']);
        unset($u_info['money']);
        unset($u_info['score']);
        unset($u_info['create_time']);
        unset($u_info['update_time']);
        unset($u_info['user_group']);
		session(['expire'=>3600]);
        session('user', $u_info);
        //写入用户最后一次登录ip
        $m_user->record_last_ip();
        //写入用户最后一次登录时间
        $m_user->record_last_time();
        return true;
    }


}