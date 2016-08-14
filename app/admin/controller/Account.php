<?php
namespace app\admin\controller;

use app\admin\model\AccountModel;
use app\admin\model\MenuModel;
use org\Verify;
use think\Controller;

Class Account extends Controller
{
    public $u;

    public function __construct()
    {
        parent::__construct();
    }

    public function verify()
    {
        ob_clean();
        $config = [
            'seKey' => '', // 验证码加密密钥
            'codeSet' => '23456789', // 验证码字符集合
            'expire' => 1800, // 验证码过期时间（s）
            'useZh' => false, // 使用中文验证码
            'useImgBg' => false, // 使用背景图片
            'fontSize' => 30, // 验证码字体大小(px)
            'useCurve' => false, // 是否画混淆曲线
            'useNoise' => false, // 是否添加杂点
            'imageH' => 0, // 验证码图片高度
            'imageW' => 0, // 验证码图片宽度
            'length' => 4, // 验证码位数
            'fontttf' => '', // 验证码字体，不设置随机获取
        ];
        $verify = new Verify($config);
        $verify->entry();
        die();
    }

    //获取配置信息
    public function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }

    /**
     *登录
     */
    public function index()
    {
        $logo = $this->get_conf('WEBSITE_LOGO');
        $mail = $this->get_conf('COMPANY_EMAIL');
        $qq   = $this->get_conf('COMPANY_QQ');
        $beian = $this->get_conf('WEBSITE_BEIAN');
        $phone = $this->get_conf('WEB_SERVICE_TEL');
        $this->assign('logo',$logo);
        $this->assign('mail',$mail);
        $this->assign('qq',$qq);
        $this->assign('beian',$beian);
        $this->assign('phone',$phone);
        return $this->fetch();
    }
    public function login()
    {
        return $this->fetch();
    }
    public function shop_login(){
        cookie('user_type',null);
        return $this->fetch();
    }

    /**
     * 执行登录
     */
    public function login_do()
    {

        $password = $username = null;
        $post = I("post.", array());
        $post = remove_arr_xss($post);
        extract($post);
        !sp_check_verify_code() && wrong_return('验证码错误');
        $m_acc = new AccountModel();
        $m_menu = new MenuModel();

        empty($username) && wrong_return('用户名不能为空');
        $user_info = $m_acc->get_user_info_by_username($username);
        !in_array($user_info['status'], array(1)) && wrong_return('用户不存在或已经禁用');

        !$user_info && wrong_return('用户不存在');
        if ($user_info["password"] === md6($password)) {
            if (!empty($remember)) {
                $data = array(
                    "username" => $username,
                    "password" => $password,
                    "remember" => $remember
                );
                cookie("login", $data);
            } else {
                cookie("login", null);
            }
            //用户类型存cookie
            cookie('user_type',$user_info['type']);
            //用户登录时间
            $user_info["login_time"] = NOW_TIME;
			session(['expire'=>3600]);
            session("user", $user_info);
            session("sign", data_auth_sign($user_info));//登录签名
            //$this->set_user_auth_by_id($user_info["id"]);
            //写入菜单到session
            $r = $m_menu->get_user_auth_tree($user_info["id"]);
            session('admin_menu', $r);
            ok_return("登录成功");
        } else {
            wrong_return("密码错误");
        }
    }
    //点击商家跳转
    public function redirect_shop(){
        session(null);
        cookie(null);
        $password = $username = null;
        $post=I('get.',[]);
        extract($post);
        $m_acc = new AccountModel();
        $m_menu = new MenuModel();
        $user_info = $m_acc->get_user_info_by_username($username);
        !in_array($user_info['status'], array(1)) && wrong_return('用户不存在或已经禁用');
        if ($user_info["password"] === $password) {
            if (!empty($remember)) {
                $data = array(
                    "username" => $username,
                    "password" => $password,
                    "remember" => $remember
                );
                cookie("login", $data);
            } else {
                cookie("login", null);
            }
            //用户类型存cookie
            cookie('user_type', $user_info['type']);
            //用户登录时间
            $user_info["login_time"] = NOW_TIME;
			session(['expire'=>3600]);
            session("user", $user_info);
            session("sign", data_auth_sign($user_info));//登录签名
            //$this->set_user_auth_by_id($user_info["id"]);
            //写入菜单到session
            $r = $m_menu->get_user_auth_tree($user_info["id"]);
            session('admin_menu', $r);
            $this->redirect('admin/shop/index');
        }

    }

    /**
     * 退出
     */
    public function quit()
    {
        session(null);
        ok_return("退出成功");
    }

}