<?php
namespace app\yyg\controller;

use app\admin\model\ArticleModel;
use app\admin\model\SpreadModel;
use app\core\controller\Api;
use app\core\controller\Common;
use app\core\model\RewardModel;
use app\lib\oAuth\Oauth;
use app\lib\oAuth\QC;
use app\lib\oAuth\SaeTClientV2;
use app\lib\oAuth\SaeTOAuthV2;
use app\lib\oAuth\WeChat;
use app\core\lib\RegExp;
use app\core\model\CommonModel;
use app\core\model\PayModel;
use app\core\model\UserModel;
use think\Exception;
use think\Db;

Class User extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 登录
     */
    public function login()
    {
        if (is_user_login()) $this->redirect('/');
        return $this->display("user:login");
    }

    /**
     * 注册
     */
    public function reg()
    {
        session("q",I('get.q',''));
        $spread_phone = session('spread_userid') ? session('spread_userid') : I('get.spread_userid');
        $this->assign('spread_phone', !empty($spread_phone) ? $spread_phone : '');
        return $this->display("user:reg");
    }

    //检测手机号是否被注册和用户状态
    public function check_phone($phone){
        $user_data = M("users")->where(array("phone" => $phone))->select();
        foreach($user_data as $key=>$value){
            if($value['status']==1){
                return true;
            }
        }
            return false;
    }
    /**注册执行*/
    public function reg_do()
    {
    	
        $post = I("post.");
        
        !is_array($post) && die_json("-90");//参数错误
        extract(remove_arr_xss($post));
        empty($agree) && die_json("-99");//请先勾选用户协议!
        $rex = new RegExp();
        $phone = empty($phone) ? "" : $phone;
        $password = empty($password) ? "" : $password;
        $re_password = empty($re_password) ? "" : $re_password;
        $phone_code = empty($phone_code) ? "" : $phone_code;
        $spread_phone = empty($spread_phone) ? "" : $spread_phone;

        !$rex->exec(array('type' => 'phone', 'val' => $phone)) && die_json("-100");//手机号格式错误
        !$rex->exec(array('type' => 'password', 'val' => $password)) && die_json("-110");//密码错误
        !$rex->exec(array('type' => 'phone_code', 'val' => $phone_code)) && die_json("-120");//手机验证码格式不正确
        $re_password !== $password && die_json("-130");//重复密码和原密码不同

        //校验手机是否已注册
        $m_user = new UserModel();
        $info = $this->check_phone($phone);
        $info && die_json("-140");//该手机号已经注册

        //如果邀请人存在,检查邀请人是否为正确用户
        if (!empty($promoter)) {
            !$m_user->get_user_info_by_filed('id', $promoter) && $promoter = '';

        } else $promoter = '';

        //校验验证码
        $m_api = new Api();
        $r = $m_api->check_phone_code(array(
            'phone' => $phone,
            'phone_code' => $phone_code,
            'expire' => true,
            'use_times' => 1,
        ));

        /** 检测验证码
         * -200 手机号不能为空
         * -210 手机验证码不能为空
         * -220 手机验证码不存在
         * -230 手机验证码已过期
         * -240 使用次数超出规定范围
         * -250 验证码不可用
         * -260 设置验证码失败
         * -270 验证码已失效
         */
        $m_api->rt_phone_code($r);

        $q = session('q');
        //执行注册
        $str = rand_str('r', 6);
        $data = array(
            "username" => $phone . $str,
            "password" => md6($password),
            "phone" => $phone,
            "nick_name" => microtime_float() . $str,
            "origin" => empty($q) ? 'other' : $q,
            "reg_ip" => get_client_ip(),
            "promoter" => $promoter,
        );
        $id = $m_user->add_user($data);
        //注册成功
        if ($id) {
            //注册推广
            $this->register_reward_deal($spread_phone, $id);
            //注册返现
            $this->register_reward($id);
            //注册返积分
            $this->register_win_score($spread_phone,$id);
            session("countdown", null);
            $this->v_login($id, "uid");
            try {
                //注册之后获取第三方的用户信息并填入
                $result = $this->union_register($id);
                if ($result) {
                    $this->v_login($id, "uid");
                }
            } catch (\Exception $exp) {
            }
            die_json(array("code" => "1", "id" => $id));
        }
        die_json("-1");
    }

    //注册返积分
    private function register_win_score($spread_uid="",$uid=""){
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
    private function get_uids_by_level($level,$uid,&$spread_uids=[]){
        $u_model=M('users','sp_');
        $spread_uid=$u_model->where(['id'=>$uid])->getField('spread_userid');
        if(!empty($spread_uid)&&(count($spread_uids)<$level)){
            $spread_uids[]=$spread_uid;
            $this->get_uids_by_level($level,$spread_uid,$spread_uids);
        }
        return $spread_uids;
    }

    //注册返现
    private function register_reward($id)
    {
        $c_model = new CommonModel();
        $reg_reward_start = $c_model->get_conf('register_money');
        if (!empty($reg_reward_start) && $reg_reward_start == 1) {
            $reg_reward_money = $c_model->get_conf('register_money_reward');
            if (!empty($reg_reward_money) && !empty($id)) {
                M('users')->save(['id' => $id, 'money' => floatval($reg_reward_money)]);
            }
        }
    }

    //推广收益
    private function register_reward_deal($spread_userid, $id)
    {
        $res = M('reg_reward')->where(['reg_ip' => get_client_ip()])->select();
        if ((!empty($spread_userid))) {
            $s_model = new SpreadModel();
            $spread = $s_model->get_spread_by_type(1);
            if (!empty($spread)) {
                if ($spread['status'] == 1) {
                    $u_model = M('users');
                    $r_model = new RewardModel();
                    $d = [
                        'uid' => $id,
                        'spread_uid' => $spread_userid,
                        'reward' => $spread['money'],
                        'create_time' => time(),
                        'reg_ip' => get_client_ip()
                    ];
                    if ($r_id = $r_model->add_reg_reward($d)) {
                        $u_model->save(['id' => $id, 'spread_userid' => $spread_userid]);
                    }

                }
            }
        }
    }

    /**根据id模拟登录*/
    private function v_login($val, $type = 'uid')
    {
        if (empty($val)) return false;
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
                if (startWith($type, 'union_')) {
                    $field = substr($type, 6);
                    $u_info = $m_user->get_user_info_by_filed($field, $val);
                } else {
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
        session(['expire' => 3600]);
        session('user', $u_info);
        //记录最后登录ip
        $m_user->record_last_ip();
        //纪录最后的登录时间
        $m_user->record_last_time();
        return true;
    }

    /**
     * 用户登陆
     * @param username 用户名
     * @param password 用户密码
     * @param geetest [arr]及验证的一些信息
     * @return json
     */
    public function user_login_do()
    {
        $post = remove_arr_xss(I("post."));

        extract($post);
        $rex = new RegExp();

        (empty($username) || !$rex->exec(array('type' => 'username', 'val' => $username))) && die_json("-100");//用户名格式不正确
        (empty($password) || !$rex->exec(array('type' => 'password', 'val' => $password))) && die_json("-110");//用户密码格式不正确

        //如果包含极验参数,则走极验流程,否则走验证码
        $need_verify = session("need_geetest");
        if (!empty($need_verify)) {
            if (!empty($geetest_challenge)) {
                $geetest = new Api();
                !$geetest->check_geetest($username, $geetest_challenge, $geetest_validate, $geetest_seccode) && die_json("-120");//验证码错误,请重新获取
            } else {
                !sp_check_verify_code() && die_json("-130");//验证码错误
            }
        }
        //获取用户信息
        $m_user = new UserModel();
        $info = $m_user->get_user_info_by_condition(" status = 1 AND (username = '" . $username . "' OR phone = '" . $username . "' OR email='".$username."')");
        empty($info) && die_json("-140");//用户信息不存在或已被禁用

        //存在则校验密码
        if ((string)md6($password) !== (string)$info["password"] && (string)md5($password) !== (string)$info["password"]) {
            //标记需要验证标记
            session("need_geetest", "true");
            //用户名密码错误
            die_json("-150");
        }
        session("need_geetest", null);//清除需要极验的标记

        //是否记住密码
        if (!empty($remember)) {
            cookie("username", $username);
            cookie("remember", "1");
        } else {
            cookie("username", null);
            cookie("remember", null);
        }

        //密码正确则模拟登录
        if ($this->v_login($info, 'info')) {
            //尝试添加到购物车
            try {
                $this->add_local_cart_to_db();
            } catch (Exception $e) {

            }

            try {
                //第三方登录关联
                $this->union_account($info['id']);
            } catch (Exception $e) {

            }
            die_json('1');
        }
        //登录成功
        die_json("-1");
    }

    /**检测是否需要输入验证码*/
    public function check_need_verify()
    {
        $need_verify = session("need_geetest");
        $need_verify && die_json("1");//需要
        die_json("-1");//不需要
    }

    /**检查倒计时*/
    public function chk_countdown()
    {
        $sec = session("countdown");
        !empty($sec) && is_numeric($sec) && die_json(array("code" => "1", 'sec' => $sec));
        die_json("-1");
    }

    /**写倒计时*/
    public function write_countdown()
    {
        $sec = I("post.sec");
        if (empty($sec) || (intval($sec) < 0)) {
            session("countdown", null);
            die_json(array("code" => "1", "sec" => $sec));
        }
        session("countdown", $sec);
        die_json(array("code" => "1", "sec" => $sec));
    }

    /**根据id写用户注册手机和ip归属地,注册完成后异步完成*/
    public function set_user_location()
    {
        $id = I("post.id");
        !chk_id($id) && die_json(array("code" => "-2", "msg" => "id error"));
        $m_user = new UserModel();
        $u_info = $m_user->get_user_info_by_filed("id", $id);
        empty($u_info) && die_json(array("code" => "-1", "msg" => "can not find user"));
        if ($u_info['zip_code']) die_json(array("code" => "1", "msg" => "record has"));

        //获取用户ip和手机归属地
        $m_api = new Api();
        $ip_info = $m_api->get_ip_location($u_info['reg_ip']);
        //ip地区
        $ip_area = $ip_info['data']['country'] .
            $ip_info['data']['region'] .
            $ip_info['data']['city'] .
            $ip_info['data']['isp'];
        //邮编
        $zip_code = $ip_info['data']['city_id'];
        usleep(10);
        $phone_info = $m_api->get_phone_location($u_info['phone']);
        //手机归属地
        $phone_area = $phone_info['carrier'];

        $data = array(
            'ip_area' => $ip_area,
            'zip_code' => $zip_code,
            'phone_area' => $phone_area,
        );
        //写入到用户信息
        $m_user->save_user_info_by_filed_value("id", $id, $data) && die_json("1");
    }

    /**退出登录*/
    public function login_out()
    {
        session(null);
        die_json("1");
    }

    /**判断用户登录状态*/
    public function get_user_login_info()
    {
        $user_info = session("user");
        $user_info && die_json(array("code" => 1, 'info' => $user_info));
        die_json("-1");
    }

    //div登陆层
    public function show_login()
    {
        $data = array(
            "code" => "1",
            "html" => $this->fetch()
        );
        die_json($data);
    }

    //检测cookie有没有未登录时候添加的商品,如果有转移到数据库中去
    private function add_local_cart_to_db()
    {

        if (!is_user_login()) return false;//用户没有登录
        //获取本地购物车是否有商品
        $local_list = cookie('local_cart');
        if (empty($local_list)) return false;
        //存在,校验商品是否存在,以及数量是否足够购买
        $ids = '';
        foreach ($local_list as $k => $v) {
            if (is_numeric($k) && !empty($k)) {
                $ids = $ids . ',' . $k;
            }
        }

        $ids = str_implode($ids);

        //获取可用的期数信息
        $m_pay = new PayModel();
        $list = $m_pay->get_nper_info_by_ids($ids);


        //准备写入数据库的数组
        if ($list) {
            $add_arr = array();
            foreach ($list as $k => $v) {
                $last_num = $v['sum_times'] - $v['participant_num'];
                $last_num = $last_num < 0 ? 0 : $last_num;
                $last_num = ((int)$local_list[$v['id']] > $last_num) ? $last_num : $local_list[$v['id']];
                $arr = array(
                    "nper_id" => $v['id'],
                    "num" => $last_num
                );
                array_push($add_arr, $arr);
            }
        } else {
            //如果没有可用的就返回不能添加的信息
            return false;
        }

        //如果正确,获取用户当前数据库中的商品
        $cart_list = $m_pay->get_user_cart_list();

        if (empty($cart_list)) {
            $c_0 = array();
            foreach ($add_arr as $k => $v) {
                $c_0[$v['nper_id']] = $v['num'];
            }
            $m_pay->add_list_to_cart($c_0);
            return true;
        } else {
            $c_1 = array();
            $c_2 = array();
            foreach ($add_arr as $k => $v) {
                $c_1[$v['nper_id']] = $v['num'];
            }
            foreach ($cart_list as $k => $v) {
                $c_2[$v['nper_id']] = $v['num'];
            }

            foreach ($c_1 as $k => &$v) {
                isset($c_2[$k]) AND $v += $c_2[$k];
            }

            //合并后的值
            $add_arr = $c_1 + $c_2;
            //清空该用户全部的购物车
            if ($m_pay->clear_user_cart()) {
                //添加用户本地商品到购物车
                $m_pay->add_list_to_cart($add_arr) && cookie('local_cart', null);
            }
        }
    }

    //保存用户昵称
    public function save_nick_name()
    {
        $nick_name = remove_xss(I("post.nick_name", ""));
        $nick_name = trim($nick_name);
        $r = preg_match('/^.{3,30}$/', $nick_name);
        !$r && die_result("-1", "用户昵称格式错误,请输入正常的字符或汉字");

        $m_user = new UserModel();
        $r = $m_user->save_login_user_info_by_filed_value(array("nick_name" => $nick_name));
        if ($r !== false) {
            session('user.nick_name', $nick_name);
            die_result("1", $nick_name);
        }
        die_json("-1");
    }

    //保存用户修改手机号
    public function save_phone()
    {
        $phone = I("post.phone", "");
        $receive_sms = I("post.receive_sms", "");
        $phone_code = I("post.phone_code", "");

        extract(remove_arr_xss(I("post.")));
        $rex = new RegExp();
        empty($phone) && die_json("-100");//手机不能为空
        empty($phone_code) && die_json("-110");//验证码不能为空
        !$rex->exec(array('type' => 'phone', 'val' => $phone)) && die_json("-120");//手机号格式错误

        //校验验证码
        $m_api = new Api();
        $r = $m_api->check_phone_code(array(
            'phone' => $phone,
            'phone_code' => $phone_code,
            'expire' => true,
            'use_times' => 1,
        ));

        /** 检测验证码
         * -200 手机号不能为空
         * -210 手机验证码不能为空
         * -220 手机验证码不存在
         * -230 手机验证码已过期
         * -240 使用次数超出规定范围
         * -250 验证码不可用
         * -260 设置验证码失败
         * -270 验证码已失效
         */
        $m_api->rt_phone_code($r);

        //校验手机是否已注册
        $m_user = new UserModel();
        $info = $m_user->get_user_info_by_filed("phone", $phone);
        $info && die_json("-130");//该手机号已经注册

        if (!empty($receive_sms)) {
            $m_user->save_login_user_info_by_filed_value(array("receive_sms" => 'true'));
        }

        //保存当前登录用户手机号
        $s = $m_user->save_login_user_info_by_filed_value(array("phone" => $phone));
        ($s !== false) && die_json("1");
        die_json("-1");

    }

    //设置短信接收
    public function set_receive()
    {
        $val = I("post.value", null);
        empty($val) && die_json("-100");
        $m_user = new UserModel();

        $s = $m_user->save_login_user_info_by_filed_value(array("receive_sms" => $val));
        ($s !== false) && die_json("1");
        die_json("-1");
    }

    //忘记密码
    public function forgot()
    {
        return $this->display();
    }

    public function forgot_check_phone()
    {
        $phone = I('post.phone', '', 'trim');
        $phone_code = I('post.code', '', 'trim');
        $m_api = new Api();
        $r = $m_api->check_phone_code(array(
            'phone' => $phone,
            'phone_code' => $phone_code,
            'expire' => true,
            'use_times' => 1,
        ));
        $m_api->rt_phone_code($r);
        $m_user = new UserModel();
        $user_info = $m_user->get_user_info_by_condition(['phone' => $phone]);
        empty($user_info) && die_json("-290");
        $token = md5($user_info['id'] . 'rst_passwd');
        session('reset_passwd_user', $user_info['id']);
        session('reset_passwd_token', $token);
        return json_encode(['code' => 1, 'msg' => '', 'token' => $token]);
    }

    public function forgot_set_passwd()
    {
        $token = I('post.token', '', 'trim');
        $passwd = I('post.passwd', '', 'trim');
        if (session('reset_passwd_token') == $token) {
            $m_user = new UserModel();
            $r = $m_user->update_info_by_id(intval(session('reset_passwd_user')), ['password' => md6($passwd)]);
            if ($r) {
                die_json('1');
            } else {
                die_json('-220');
            }
        } else {
            die_json('-210');
        }
    }



    //联合登录统一入口
    public function union_login($plat = null)
    {
        $m_misc = new CommonModel();
        switch ($plat) {
            case 'qq':
                $config = $m_misc->get_conf_by_keys(['UNION_QQ_APPID', 'UNION_QQ_APPKEY']);
                $oauth = new Oauth();
                $oauth->init($config['union_qq_appid'], $config['union_qq_appkey'], U('user/union_login_do', ['plat' => 'qq'], '.html', true));
                $oauth->qq_login();

                break;
            case 'sina_weibo':
                $config = $m_misc->get_conf_by_keys(['UNION_SINA_WEIBO_ID', 'UNION_SINA_WEIBO_KEY']);
                $url = U('user/union_login_do', ['plat' => 'sina_weibo'], '.html', true);
                $oauth = new SaeTOAuthV2($config['union_sina_weibo_id'], $config['union_sina_weibo_key']);
                $oauth = $oauth->getAuthorizeURL($url);
                header('Location: ' . $oauth);
                break;
            case 'wechat_open':
                $config = $m_misc->get_conf_by_keys(['UNION_WECHAT_OPEN_APPID', 'UNION_WECHAT_OPEN_APPSEC']);
                $wechat = new WeChat();
                $callback = U('user/union_login_do', ['plat' => 'wechat_open'], '.html', true);
                $wechat->init($config['union_wechat_open_appid'], $config['union_wechat_open_appsec']);
                $wechat->get_code($callback);
                break;
//            case 'wechat_mp':
//                break;
            default:
                return false;
        }
    }

    //联合登录统一回调
    public function union_login_do($plat = null)
    {
        //
        $success_return = '/';
        $m_misc = new CommonModel();
        $openid = null;
        $access_token = null;
        switch ($plat) {
            case 'qq':
                //请求accesstoken
                $config = $m_misc->get_conf_by_keys(['UNION_QQ_APPID', 'UNION_QQ_APPKEY']);
                $oauth = new Oauth();
                $oauth->init($config['union_qq_appid'], $config['union_qq_appkey'], U('user/union_login_do', ['plat' => 'qq'], '.html', true));
                $access_token = $oauth->qq_callback();
                $openid = $oauth->get_openid();

                if ($this->v_login($openid, 'union_qq_id')) {
                    return $this->redirect($success_return);
                } else {
                }
                break;
            case 'sina_weibo':
                $code = I('get.code', '', 'trim');
                $keys['code'] = $code;
                $keys['redirect_uri'] = U('user/union_login_do', ['plat' => 'sina_weibo'], '.html', true);;
                $config = $m_misc->get_conf_by_keys(['UNION_SINA_WEIBO_ID', 'UNION_SINA_WEIBO_KEY']);
                $oauth = new SaeTOAuthV2($config['union_sina_weibo_id'], $config['union_sina_weibo_key']);
                $result = $oauth->getAccessToken($keys);


                $openid = $result['uid'];
                $access_token = $result['access_token'];

                if ($this->v_login($openid, 'union_weibo_id')) {
                    return $this->redirect($success_return);
                } else {
                }
                break;
            case 'wechat_open':
                $code = I('get.code', '', 'trim');
                $config = $m_misc->get_conf_by_keys(['UNION_WECHAT_OPEN_APPID', 'UNION_WECHAT_OPEN_APPSEC']);
                $wechat = new WeChat();
                $wechat->init($config['union_wechat_open_appid'], $config['union_wechat_open_appsec']);
                $result = $wechat->get_access_token($code);

                $openid = $result['openid'];
                $access_token = $result['access_token'];

                //获取union_id 不用open_id了
                $userinfo = $wechat->get_user_info($access_token, $openid);
                if(!isset($userinfo['errcode'])){
                    $union_id = $userinfo['unionid'];
                    if ($this->v_login($union_id, 'union_unionid')) {
                        return $this->redirect($success_return);
                    }
                    session("unionid",$union_id);
                    break;
                }

                if ($this->v_login($openid, 'union_weixin_id')) {
                    return $this->redirect($success_return);
                }
                break;
//            case 'wechat_mp':
//                break;
            default:
                return false;
        }
        session('union_type', $plat);
        session('union_openid', $openid);
        session('union_token', $access_token);

        $this->assign('union_type', $plat);
        //return $this->fetch('user/login');
        $this->redirect(U('user/quick_reg'));
    }

    //与登录的帐号绑定
    private function union_account($uid)
    {
        $union_type = session('union_type');
        $openid = session('union_openid');
        session('union_type', null);
        session('union_openid', null);
        $m_user = new UserModel();
        switch ($union_type) {
            case 'qq':
                return $m_user->save_login_user_info_by_filed_value(['qq_id' => $openid]);
                break;
            case 'sina_weibo':
                return $m_user->save_login_user_info_by_filed_value(['weibo_id' => $openid]);
                break;
            case 'wechat_open':
                return $m_user->save_login_user_info_by_filed_value(['weixin_id' => $openid]);
                break;
            case 'wechat_mp':
                break;
            default:
                return false;
        }
    }

    //注册之后获取第三方的用户信息并填入
    private function union_register($uid)
    {
        $union_type = session('union_type');
        $openid = session('union_openid');
        $token = session('union_token');
        $union_id = session("unionid")?session("unionid"):"";
        session("unionid",null);
        session('union_type', null);
        session('union_openid', null);
        session('union_token', null);
        $m_user = new UserModel();
        $m_misc = new CommonModel();
        switch ($union_type) {
            case 'qq':
                $config = $m_misc->get_conf_by_keys(['UNION_QQ_APPID', 'UNION_QQ_APPKEY']);
                $appid = $config['union_qq_appid'];
                $qc = new QC($token, $openid, $appid);
                $userinfo = $qc->get_user_info();
                if ($userinfo) {
                    $ext_data = [];
                    if (key_exists('nickname', $userinfo)) $ext_data['nick_name'] = $userinfo['nickname'];
                    if (key_exists('gender', $userinfo)) $ext_data['sex'] = $userinfo['gender'] == '男' ? 'man' : 'woman';
                    if (key_exists('province', $userinfo) && key_exists('city', $userinfo)) $ext_data['phone_area'] = $userinfo['province'] . $userinfo['city'];
                    if (key_exists('year', $userinfo)) {
                        $age = date('Y') - intval($userinfo['year']);
                        $ext_data['age'] = $age > 0 ? $age : 0;
                    }
                    //figureurl_qq_1 头像
                    if (key_exists('figureurl_qq_1', $userinfo)) {
                        $ext_data['user_pic'] = strlen($userinfo['figureurl_qq_1']) > 0 ?
                            $this->fetch_image($userinfo['figureurl_qq_1'], $uid)
                            : intval(C('WEBSITE_AVATAR_DEF'));
                    }
                    $ext_data['qq_id'] = $openid;
                    return $m_user->save_login_user_info_by_filed_value($ext_data);
                }
                break;
            case 'sina_weibo':
                $config = $m_misc->get_conf_by_keys(['UNION_SINA_WEIBO_ID', 'UNION_SINA_WEIBO_KEY']);
                $sae_client = new SaeTClientV2($config['union_sina_weibo_id'], $config['union_sina_weibo_key'], $token);
                $userinfo = $sae_client->show_user_by_id($openid);
                if ($userinfo) {
                    $ext_data = [];
                    $gender_map = ['m' => 'man', 'f' => 'woman', 'n' => null];
                    if (key_exists('screen_name', $userinfo)) $ext_data['nick_name'] = $userinfo['screen_name'];
                    if (key_exists('gender', $userinfo)) $ext_data['sex'] = $gender_map[$userinfo['gender']];
                    //if(key_exists('province',$userinfo) && key_exists('city',$userinfo))$ext_data['phone_area'] = $userinfo['province'].$userinfo['city'];

                    //profile_image_url 头像
                    if (key_exists('profile_image_url', $userinfo)) {
                        $ext_data['user_pic'] = strlen($userinfo['profile_image_url']) > 0 ?
                            $this->fetch_image($userinfo['profile_image_url'], $uid)
                            : intval(C('WEBSITE_AVATAR_DEF'));
                    }
                    $ext_data['weibo_id'] = $openid;
                    return $m_user->save_login_user_info_by_filed_value($ext_data);
                }
                break;
            case 'wechat_open':
                $config = $m_misc->get_conf_by_keys(['UNION_WECHAT_OPEN_APPID', 'UNION_WECHAT_OPEN_APPSEC']);
                $wechat = new WeChat();
                $wechat->init($config['union_wechat_open_appid'], $config['union_wechat_open_appsec']);
                $userinfo = $wechat->get_user_info($token, $openid);
                if ($userinfo) {
                    $ext_data = [];
                    if (key_exists('nickname', $userinfo)) $ext_data['nick_name'] = $userinfo['nickname'];
                    if (key_exists('sex', $userinfo)) $ext_data['sex'] = $userinfo['sex'] == 1 ? 'man' : 'woman';
                    //if(key_exists('province',$userinfo) && key_exists('city',$userinfo))$ext_data['phone_area'] = $userinfo['province'].$userinfo['city'];

                    //headimgurl 头像

                    if (key_exists('headimgurl', $userinfo)) {
                        $ext_data['user_pic'] = strlen($userinfo['headimgurl']) > 0 ?
                            $this->fetch_image($userinfo['headimgurl'], $uid)
                            : intval(C('WEBSITE_AVATAR_DEF'));
                    }
                    $ext_data['unionid'] = $union_id;
                    $ext_data['weixin_id'] = $openid;
                    return $m_user->save_login_user_info_by_filed_value($ext_data);
                }
                break;
            case 'wechat_mp':
                break;
            default:
                return false;
        }
    }

    /**
     *获取用户注册协议
     * @return mixed
     */
    public function user_agreement()
    {
        $category = new ArticleModel();
        $agreement = $category->get_agreement();
        $this->assign('agreement', $agreement);
        return $this->display("user:user_agreement");
    }

    //获取用户头像
    private function fetch_image($url, $uid, $file_prefix = 'head_img_')
    {
        try {
            $save_path = 'data/fetch/';
            @mkdir($save_path, 0777, true);
            @chmod($save_path, 0777);
            $img_content = curl_get($url);
            $filename = $file_prefix . md5(time() . $uid) . '.jpg';
            file_put_contents($save_path . $filename, $img_content);
            $img_path = '/' . $save_path . $filename;
            $m_img = M('image_list', 'sp_');
            $img_id = $m_img->data([
                'name' => $filename,
                'uid' => $uid,
                'img_path' => $img_path,
                'create_time' => time(),
                'origin' => 'fetch'
            ])->add();
            return $img_id>0?$img_id:intval(C('WEBSITE_AVATAR_DEF'));
        } catch (Exception $e) {
            return intval(C('WEBSITE_AVATAR_DEF'));
        }
    }

    //快速注册
    public function quick_reg()
    {
        $union_type = session('union_type');
        $openid = session('union_openid');
        $union_id = session("unionid")?session("unionid"):"";
        $token = session('union_token');
        if (empty($union_type))
            return $this->redirect('/');
        $str = rand_str('r', 6);
        if(!empty($union_id)){
            //只是为了生成用户名
            $openid = $union_id;
        }
        $user_name = substr(md5($openid . $str), 0, 16);
        $data = [
            "username" => $user_name,
            "password" => md6($str),
            "nick_name" => microtime_float() . $str,
            "origin" => I('get.q', $_SERVER['HTTP_HOST'], 'trim'),
            "reg_ip" => get_client_ip(),
            "unionid"=>$union_id
        ];
        $m_user = new UserModel();
        $id = $m_user->add_user($data);
        if ($id > 0) {
            $this->v_login($id);
            $union_reg = $this->union_register($id);
            $this->v_login($id);
        }
        return $this->redirect('/');
    }
}