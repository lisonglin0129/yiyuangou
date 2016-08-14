<?php
namespace app\install\controller;

use app\pay\model\PublicModel;
use think\Controller;
use think\Db;
use think\Exception;

// 检测环境是否支持可写
define('IS_WRITE', APP_MODE !== 'sae');

/**
 * 安装类
 */
Class Index extends Controller
{
    public function __construct()
    {
        file_exists('./data/db/lock') && die('请先删除./data/db/lock文件后再进行安装,如果您已经安装完成,建议删除app目录下的install文件夹');
        parent::__construct();
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }

    //step2
    public function step2()
    {
        /**
         * 功能：返回当前PHP所运行的系统的信息
         * @param string $mode
         *       'a':  返回所有信息
         *       's':  操作系统的名称，如FreeBSD
         *       'n':  主机的名称,如cnscn.org
         *       'r':  版本名，如5.1.2-RELEASE
         *       'v':  操作系统的版本号
         *       'm': 核心类型，如i386
         * @return string
         */


        $data = array();
        //PHP版本号
        $data['phpversion'] = @ phpversion();
        //当前操作系统
        $data['os'] = PHP_OS;
        $tmp = function_exists('gd_info') ? gd_info() : array();

        //当前软件环境
        $server = $_SERVER["SERVER_SOFTWARE"];
        //服务器地址
        $host = (empty($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_HOST"] : $_SERVER["SERVER_ADDR"]);
        //服务器名称
        $name = $_SERVER["SERVER_NAME"];
        //最长执行时间
        $max_execution_time = ini_get('max_execution_time');
        //允许获取
        $allow_reference = (ini_get('allow_call_time_pass_reference') ? true : false);
        //允许打开url的内容
        $allow_url_fopen = (ini_get('allow_url_fopen') ? true : false);
        //安全模式是否关闭
        $safe_mode = (ini_get('safe_mode') ? true : false);


        $err = 0;
        if (empty($tmp['GD Version'])) {
            $data['gd'] = false;
            $err++;
        } else {
            $data['gd'] = $tmp['GD Version'];
        }


        if (class_exists('pdo')) {
            $data['pdo'] = true;
        } else {
            $data['pdo'] = false;
            $err++;
        }


        if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = true;
        } else {
            $data['pdo_mysql'] = false;
            $err++;
        }


        if (ini_get('file_uploads')) {
            $data['upload_size'] = ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = false;
        }
        if (function_exists('session_start')) {
            $data['session'] = true;
        } else {
            $data['session'] = false;
            $err++;
        }


        $folders = array(
            '../',//根目录
            '../app',
            '../app/runtime',
            '../app/runtime/cache',
            '../app/runtime/log',
            '../app/runtime/temp',
            'data',
        );
        $new_folders = array();
        foreach ($folders as $dir) {
            $testdir = "./" . $dir;
            sp_dir_create($testdir);
            if (sp_testwrite($testdir)) {
                $new_folders[$dir]['w'] = true;
            } else {
                $new_folders[$dir]['w'] = false;
                $err++;
            }
            if (is_readable($testdir)) {
                $new_folders[$dir]['r'] = true;
            } else {
                $new_folders[$dir]['r'] = false;
                $err++;
            }
        }
        $data['folders'] = $new_folders;

        //当前主机名称
        $this->assign('d', $data);
        $this->assign('err_num', $err);
//        //当前操作系统
//        $this->assign('now_sys', php_uname('s'));
//        //当前版本名
//        $this->assign('now_sys', php_uname('r'));
//        //当前操作系统的版本号
//        $this->assign('now_sys', php_uname('v'));
//        //当前操作系统核心类型
//        $this->assign('now_sys', php_uname('m'));
//        //当前操作系统环境
//        $now_sys_env=strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'WINDOWS' : 'LINUX/UNIX';
//        //当前操作系统
//        $this->assign('now_sys_env', $now_sys_env);

        return $this->fetch();
    }

    //step3
    public function step3()
    {
        //服务器地址
        $host = $_SERVER["SERVER_NAME"];
        $this->assign('host', $host);
        return $this->fetch();
    }

    //step4安装
    public function step4()
    {
        ini_set('memory_limit', '256M');
        echo $this->fetch();
        $this->install();
    }

    //step5
    public function step5()
    {
        file_put_contents('./data/db/lock', date('Y-m-d H:i:s'));
        return $this->fetch();
    }


    public function chk_step3()
    {
        $post = remove_arr_xss(I("post.", []));
        $password = $re_password = $username = $web_name = $domain_name = $keywords = $desc = null;
        extract($post);

        empty($username) && wrong_return('用户名信息不能为空');
        empty($password) && wrong_return('用户密码不能为空');
        ($password !== $re_password) && wrong_return('两次密码输入不同');

        empty($db_user) && wrong_return('数据库用户不能为空');
        empty($db_pass) && wrong_return('数据库密码不能为空');
        empty($db_host) && wrong_return('数据库链接地址不能为空');
        empty($db_port) && wrong_return('数据库端口号不能为空');
        empty($db_name) && wrong_return('数据库名称不能为空');


        $db_user = empty($db_user) ? '' : $db_user;
        $db_pass = empty($db_pass) ? '' : $db_pass;
        $db_host = empty($db_host) ? '' : $db_host;
        $db_port = empty($db_port) ? '' : $db_port;
        $db_name = empty($db_name) ? '' : $db_name;

        try {
            mysqli_connect($db_host . ':' . $db_port, $db_user, $db_pass);
        } catch (Exception $e) {
            wrong_return('数据库链接错误');//数据库连接错误
        }

        $db = array(
            'user' => $db_user,
            'pass' => $db_pass,
            'host' => $db_host,
            'port' => $db_port,
            'name' => $db_name,
        );
        $r = 'mysql://' . $db_user . ':' . $db_pass . '@' . $db_host . ':' . $db_port . '/' . $db_name . '#utf8';
        session('conn', $r);
        session('db', $db);

        //用户信息,保存
        $user_info = array(
            'username' => $username,
            'password' => $password,
            'web_name' => $web_name,
            'domain_name' => $domain_name,
            'keywords' => $keywords,
            'desc' => $desc,
        );
        session('user_conf', $user_info);

        try {
            $db = Db::connect($r);
            $list = $db->query('show tables');
        } catch (Exception $e) {
            wrong_return('数据库没有权限');
        }
        if (!empty($list)) {
            die_json(array('code' => '2'));
        }

        ok_return('请等待,正在准备为您安装数据库');
    }


    /**
     * 及时显示提示信息
     * @param  string $msg 提示信息
     */
    public function show_msg($msg, $class = '')
    {
        echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
        flush();
        ob_flush();
    }

    /**
     *
     *
     * /**
     * 更新数据表
     * @param  resource $db 数据库连接资源
     * @author lyq <605415184@qq.com>
     */
    public function install()
    {
        $r = session('conn');
        $db_arr = session('db');
        $db = mysqli_connect($db_arr['host'] . ':' . $db_arr['port'], $db_arr['user'], $db_arr['pass']);

        $data = [];

        $result = mysqli_query($db, 'show databases;');
        While ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['Database'];
        }
        unset($result, $row);
        mysqli_close($db);

        if (!in_array($db_arr["name"], $data)) {
            die("<script type=\"text/javascript\">show_db_error('" . $db_arr["name"] . "')</script>");
        }

        $db = Db::connect($r);

        set_time_limit(0);
        //读取SQL文件
        $sql = file_get_contents('./data/db/install.db');
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql);

        //开始安装
        $this->show_msg('开始升级数据库...');
        $msg = '';
        foreach ($sql as $value) {
            $value = trim($value);
            if (empty($value)) continue;
            if (substr($value, 0, 12) == 'CREATE TABLE') {
                $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
                $msg = "创建数据表{$name}";
                if (false !== $db->execute($value)) {
                    $this->show_msg($msg . '...成功');
                } else {
                    $this->show_msg($msg . '...失败！', 'error');
                    session('error', true);
                }
            } else {
                if (substr($value, 0, 8) == 'UPDATE `') {
                    $name = preg_replace("/^UPDATE `(\w+)` .*/s", "\\1", $value);
                    $msg = "更新数据表{$name}";
                } else if (substr($value, 0, 11) == 'ALTER TABLE') {
                    $name = preg_replace("/^ALTER TABLE `(\w+)` .*/s", "\\1", $value);
                    $msg = "修改数据表{$name}";
                } else if (substr($value, 0, 11) == 'INSERT INTO') {
                    $name = preg_replace("/^INSERT INTO `(\w+)` .*/s", "\\1", $value);
                    $msg = "写入数据表{$name}";
                }
                if (($db->execute($value)) !== false) {
//                    $this->show_msg($msg . '...成功');
                } else {
                    $this->show_msg($msg . '...失败！', 'error');
                    session('error', true);
                }
            }
        }

        //执行基本配置信息写入

        $this->show_msg('请稍等...');
        $this->write_config($db_arr);
        $this->write_user_conf();
        echo "<script type=\"text/javascript\">go_step5()</script>";
    }


    //写入用户配置信息
    public function write_user_conf()
    {
        $username = $password = $web_name = $domain_name = $keywords = $desc = null;
        $session = session('user_conf');
        extract($session);

        $domain_name = trim($domain_name, '/') . '/';


        //用户信息,保存
        $m = M('users', 'sp_');
        $user_arr = array(
            "username" => $username,
            "password" => md6($password),
            "nick_name" => '超级管理员',
            "user_group" => '1',

        );
        $r = $m->where(array('username' => $username))->find();
        if (!empty($r)) {
            $m->where(array('username' => $username))->save($user_arr);
        } else {
            $m->add($user_arr);
        }
        $this->show_msg('写入用户信息成功');

        //写入用户配置信息

        $conf_arr = array(
            array(
                "title" => "积分转香肠比列",
                "category" => "积分配置",
                "type" => "input",
                "editable" => "true",
                "name" => "SCORE_TRANS_XC",
                "value" => "1"
            ),
            array(
                "title" => "消费一元返积分数",
                "category" => "积分配置",
                "type" => "input",
                "editable" => "true",
                "name" => "SCORE_REWARD",
                "value" => "1"
            ),
            array(
                "title" => "搜索关键词",
                "category" => "wap配置",
                "type" => "input",
                "editable" => "true",
                "name" => "SET_KEYWORDS",
                "value" => "apple,三星,算盘"
            ),
            array(
                "title" => "推广页面提示语3",
                "category" => "推广配置",
                "type" => "input",
                "editable" => "true",
                "name" => "PROMOTE_NOTICE",
                "value" => "您的推广ID为您注册时唯一用户ID号！"
            ),
            array(
                "title" => "推广页面提示语2",
                "category" => "推广配置",
                "type" => "input",
                "editable" => "true",
                "name" => "PROMOTE_NOTICE",
                "value" => "冻结金额为正在审核提现的金额"
            ),
            array(
                "title" => "推广页面提示语1",
                "category" => "推广配置",
                "type" => "input",
                "editable" => "true",
                "name" => "PROMOTE_NOTICE",
                "value" => "账户余额可提现，也可以转为香肠币 参与夺宝"
            ),
            array(
                "title" => "wap端虚拟币名称",
                "category" => "wap配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WAP_WEB_MONEY_NAME",
                "value" => "香肠"
            ),
            array(
                "title" => "是否开启调试",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "false",
                "name" => "OPEN_TEST_ENV",
                "value" => "false"
            ),
            array(
                "title" => "短信发送人(模版替换)",
                "category" => "短信配置",
                "type" => "input",
                "editable" => "true",
                "name" => "SMS_TEMP_NAME",
                "value" => "香肠"
            ),
            array(
                "title" => "wap端标题",
                "category" => "wap配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WAP_WEB_TITLE",
                "value" => "一元夺宝-香肠一元购"
            ),

            array(
                "title" => "appid",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_MP_APPID",
                "value" => ""
            ),
            array(
                "title" => "商户号",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_MP_MCHID",
                "value" => ""
            ),
            array(
                "title" => "商户支付密钥",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_MP_KEY",
                "value" => ""
            ),
            array(
                "title" => "公众帐号secert",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_MP_APPSECRET",
                "value" => ""
            ),
            array(
                "title" => "证书地址(cert)",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "false",
                "name" => "WXPAY_MP_SSLCERT_PATH",
                "value" => "../cert/apiclient_cert.pem"
            ),
            array(
                "title" => "证书地址(key)",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "false",
                "name" => "WXPAY_MP_SSLKEY_PATH",
                "value" => "../cert/apiclient_key.pem"
            ),
            array(
                "title" => "代理服务器",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "false",
                "name" => "WXPAY_MP_CURL_PROXY_HOST",
                "value" => "0.0.0.0"
            ),
            array(
                "title" => "代理端口",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_MP_CURL_PROXY_PORT",
                "value" => ""
            ),
            array(
                "title" => "上报等级",
                "category" => "微信支付(公众号)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_MP_REPORT_LEVENL",
                "value" => ""
            ),
            array(
                "title" => "appid",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_APP_APPID",
                "value" => ""
            ),
            array(
                "title" => "商户号",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_APP_MCHID",
                "value" => ""
            ),
            array(
                "title" => "商户支付密钥",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_APP_KEY",
                "value" => ""
            ),
            array(
                "title" => "公众帐号secert",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_APP_APPSECRET",
                "value" => ""
            ),
            array(
                "title" => "证书地址(cert)",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "false",
                "name" => "WXPAY_APP_SSLCERT_PATH",
                "value" => "../cert/apiclient_cert.pem"
            ),
            array(
                "title" => "证书地址(key)",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "false",
                "name" => "WXPAY_APP_SSLKEY_PATH",
                "value" => "../cert/apiclient_key.pem"
            ),
            array(
                "title" => "代理服务器",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "false",
                "name" => "WXPAY_APP_CURL_PROXY_HOST",
                "value" => "0.0.0.0"
            ),
            array(
                "title" => "代理端口",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_APP_CURL_PROXY_PORT",
                "value" => ""
            ),
            array(
                "title" => "上报等级",
                "category" => "微信支付(APP)",
                "type" => "input",
                "editable" => "true",
                "name" => "WXPAY_APP_REPORT_LEVENL",
                "value" => ""
            ),
            array(
                "title" => "欢迎标语",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_WELCOME",
                "value" => "welcome"
            ),
            array(
                "title" => "本站域名",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_URL",
                "value" => $domain_name
            ),
            array(
                "title" => "站长统计代码",
                "category" => "统计配置",
                "type" => "textarea",
                "editable" => "true",
                "name" => "WEBSITE_TONGJI_CNZZ",
                "value" => ""
            ),
            array(
                "title" => "百度统计代码",
                "category" => "统计配置",
                "type" => "textarea",
                "editable" => "true",
                "name" => "WEBSITE_TONGJI_BD",
                "value" => ""
            ),
            array(
                "title" => "统计代码(备)",
                "category" => "统计配置",
                "type" => "textarea",
                "editable" => "true",
                "name" => "WEBSITE_TONGJI",
                "value" => ""
            ),
            array(
                "title" => "头部微博链接",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_SINA_WEIBO",
                "value" => ""
            ),
            array(
                "title" => "底部二维码",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_QRCODE",
                "value" => "http://o7djuqrn7.bkt.clouddn.com/qrc_code_1.png"
            ),
            array(
                "title" => "QQ群链接",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_QQ_GROUP",
                "value" => ""
            ),
            array(
                "title" => "网站标题",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_NAME",
                "value" => "香肠一元购"
            ),
            array(
                "title" => "网站LOGO2",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_LOGO_SUB",
                "value" => "http://o7djuqrn7.bkt.clouddn.com/logo2.png"
            ),
            array(
                "title" => "网站LOGO",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_LOGO",
                "value" => "http://o7djuqrn7.bkt.clouddn.com/logo1.png"
            ),
            array(
                "title" => "SEO选项-网站关键字",
                "category" => "网站信息",
                "type" => "textarea",
                "editable" => "true",
                "name" => "WEBSITE_KEYWORD",
                "value" => "一元,一元购,一元云购,一元夺宝,一元抢购,1元购物,1元夺宝,1元抢购"
            ),
            array(
                "title" => "SEO选项-网站描述",
                "category" => "网站信息",
                "type" => "textarea",
                "editable" => "true",
                "name" => "WEBSITE_DESC",
                "value" => ""
            ),
            array(
                "title" => "公司名称",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_COMPANY_NAME",
                "value" => "公司名称"
            ),
            array(
                "title" => "备案号",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "WEBSITE_BEIAN",
                "value" => "苏ICP备16018502号-4"
            ),
            array(
                "title" => "版本号",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "false",
                "name" => "WEB_VERSION_NUM",
                "value" => __version__
            ),
            array(
                "title" => "站点是否关闭",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "false",
                "name" => "WEB_SITE_CLOSE",
                "value" => "FALSE"
            ),
            array(
                "title" => "服务时间",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WEB_SERVICE_TIME",
                "value" => "周一至周五：9:00-18:00"
            ),
            array(
                "title" => "服务热线",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WEB_SERVICE_TEL",
                "value" => "服务热线：0516-**** ****"
            ),
            array(
                "title" => "底部版权",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WEB_INC_INFO",
                "value" => "Powered by LingJiang tech. &copy; 2016"
            ),
            array(
                "title" => "微信端域名",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "false",
                "name" => "WAP_WEBSITE_URL",
                "value" => $domain_name . "index.php/mobile/index/index"
            ),
            array(
                "title" => "wap端备案",
                "category" => "wap配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WAP_WEB_RECORD_NUM",
                "value" => "苏公网安2000001号"
            ),
            array(
                "title" => "wap端logo",
                "category" => "wap配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WAP_WEB_LOGO",
                "value" => "http://o7djuqrn7.bkt.clouddn.com/logo1.png"
            ),
            array(
                "title" => "wap端版权",
                "category" => "wap配置",
                "type" => "input",
                "editable" => "true",
                "name" => "WAP_WEB_COPYRIGHT",
                "value" => "一元香肠版权©2016"
            ),
            array(
                "title" => "用户同意（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "USER_AGGREMENT",
                "value" => $domain_name . "index.php/mobile/Article/user_agreement"
            ),
            array(
                "title" => "更新时间",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "false",
                "name" => "UPDATE_TIME",
                "value" => "2423959384"
            ),
            array(
                "title" => "上传图片配置",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "true",
                "name" => "UP_IMG_BASE_URL",
                "value" => ""
            ),
            array(
                "title" => "微信开放平台密钥",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_WECHAT_OPEN_APPSEC",
                "value" => ""
            ),
            array(
                "title" => "微信开放平台appid",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_WECHAT_OPEN_APPID",
                "value" => ""
            ),
            array(
                "title" => "微信公众平台密钥",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_WECHAT_MP_APPSEC",
                "value" => ""
            ),
            array(
                "title" => "微信公众平台appid",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_WECHAT_MP_APPID",
                "value" => ""
            ),
            array(
                "title" => "新浪微博联合登录KEY",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_SINA_WEIBO_KEY",
                "value" => ""
            ),
            array(
                "title" => "新浪微博联合登录ID",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_SINA_WEIBO_ID",
                "value" => ""
            ),
            array(
                "title" => "QQ联合登录APPKEY",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_QQ_APPKEY",
                "value" => ""
            ),
            array(
                "title" => "QQ联合登录APPID",
                "category" => "第三方登录",
                "type" => "input",
                "editable" => "true",
                "name" => "UNION_QQ_APPID",
                "value" => ""
            ),
            array(
                "title" => "平台全局加密统一拼接密钥",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "false",
                "name" => "TOKEN_ACCESS",
                "value" => "UW4RMqDCFjyVk3tQCCbM"
            ),
            array(
                "title" => "淘宝获取手机归属地详情接口",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "false",
                "name" => "TAOBAO_PHONE_API",
                "value" => "https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel="
            ),
            array(
                "title" => "淘宝获取ip详情接口",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "false",
                "name" => "TAOBAO_IP_API",
                "value" => "http://ip.taobao.com/service/getIpInfo.php?ip="
            ),
            array(
                "title" => "分享详情（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "SHARE_DETAIL_URL",
                "value" => $domain_name . "index.php/mobile/other_users/share_detail/share_id"
            ),
            array(
                "title" => "客服电话",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "SERVICE_TELEPHONE",
                "value" => "400-688-8888"
            ),
            array(
                "title" => "服务协议（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "SERVICE_PROVISION",
                "value" => $domain_name . "index.php/mobile/Article/service_provision"
            ),
            array(
                "title" => "最后获取机器人的id",
                "category" => "机器人配置",
                "type" => "input",
                "editable" => "false",
                "name" => "RT_LAST_GET_ID",
                "value" => "1"
            ),
            array(
                "title" => "获取机器人的位置ID",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "false",
                "name" => "RT_ID_LAST_SITE",
                "value" => "1"
            ),
            array(
                "title" => "获取机器人头像保存的位置",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "false",
                "name" => "RT_HEAD_SAVE_ROOT",
                "value" => "data"
            ),
            array(
                "title" => "获取机器人用户的接口",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "false",
                "name" => "RT_GET_USER_API",
                "value" => "http://auth.mengdie.com/index.php/core/rtuser/get_user"
            ),
            array(
                "title" => "模拟购买地址 nper_id,uid,num",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "true",
                "name" => "RT_BUY_API",
                "value" => $domain_name . "index.php/core/rt/rt_buy?"
            ),
            array(
                "title" => "隐私保护(移动端使用)",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "PROTECT_PRIVATE",
                "value" => $domain_name . "index.php/mobile/Article/protect_private"
            ),
            array(
                "title" => "推广分成比例n%",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "PROMOTE_PRECENT",
                "value" => "2"
            ),
            array(
                "title" => "分销推广比例",
                "category" => "推广配置",
                "type" => "input",
                "editable" => "true",
                "name" => "PROMOTE_BILI",
                "value" => ""
            ),
            array(
                "title" => "极验证KEY",
                "category" => "短信配置",
                "type" => "input",
                "editable" => "true",
                "name" => "PRIVATE_KEY",
                "value" => ""
            ),
            array(
                "title" => "其他终端余额付款后的提交触发回调处理接口",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "true",
                "name" => "ORDER_TRIGGER_API",
                "value" => $domain_name . "index.php/pay/notify/balance_flow"
            ),
            array(
                "title" => "订单关闭时间(秒),默认半小时",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "true",
                "name" => "ORDER_CLOSE_TIME",
                "value" => "1800"
            ),
            array(
                "title" => "期数开奖接口(作废勿用)",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "NPER_OPEN_URL",
                "value" => $domain_name . "index.php/lottery/gdfc/gateway?nper_id="
            ),
            array(
                "title" => "期数开奖接口",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "true",
                "name" => "NPER_OPEN_API",
                "value" => $domain_name . "index.php/core/gdfc/open_by_nper?nper_id="
            ),
            array(
                "title" => "期数基数,和期数累加的",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "true",
                "name" => "NPER_BASE_NUM",
                "value" => "8000000"
            ),
            array(
                "title" => "商品期数增加接口",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "true",
                "name" => "NPER_ADD_API",
                "value" => $domain_name . "index.php/core/gdfc/init_new_nper?goods_id="
            ),
            array(
                "title" => "阿里大鱼注册短信模版",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "true",
                "name" => "MSG_REG_ALIDAYU",
                "value" => ""
            ),
            array(
                "title" => "阿里大鱼身份验证模版",
                "category" => "网站配置",
                "type" => "input",
                "editable" => "true",
                "name" => "MSG_AUTH_ALIDAYU",
                "value" => ""
            ),
            array(
                "title" => "螺丝帽KEY",
                "category" => "短信配置",
                "type" => "input",
                "editable" => "true",
                "name" => "LUOSIMAO_KEY",
                "value" => ""
            ),
            array(
                "title" => "ICP备案号",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "ICP",
                "value" => "ICP备16018502号"
            ),
            array(
                "title" => "关于一元购（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "HELP_URL",
                "value" => $domain_name . "index.php/mobile/Article/help/origin/other"
            ),
            array(
                "title" => "图文详情（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "GRAPHIC_DETAILS",
                "value" => $domain_name . "index.php/mobile/goods/graphic_details/origin/other/goods_id/"
            ),
            array(
                "title" => "商品下的晒单（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "GOODS_SHARE_ORDER",
                "value" => $domain_name . "index.php/mobile/goods/goods_share_order/goods_id/"
            ),
            array(
                "title" => "企业邮箱",
                "category" => "网站信息",
                "type" => "input",
                "editable" => "true",
                "name" => "COMPANY_EMAIL",
                "value" => "service@gmail.com"
            ),
            array(
                "title" => "常见问题（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "COMMON_PROBLEM",
                "value" => $domain_name . "index.php/mobile/Article/common_problem"
            ),
            array(
                "title" => "默认显示哪个分类下的商品[yiyuanyungou/laoniuduobao]",
                "category" => "网站接口",
                "type" => "input",
                "editable" => "false",
                "name" => "CATEGORY_GOODS",
                "value" => "xiangchang"
            ),
            array(
                "title" => "极验证ID",
                "category" => "短信配置",
                "type" => "input",
                "editable" => "true",
                "name" => "CAPTCHA_ID",
                "value" => ""
            ),
            array(
                "title" => "计算详情（移动端使用）",
                "category" => "移动端接口",
                "type" => "input",
                "editable" => "true",
                "name" => "CALCULATION_DETAILS",
                "value" => $domain_name . "index.php/mobile/goods/calculation_details/origin/other/nper_id/"
            ),
            array(
                "title" => "Test Secret",
                "category" => "BeeCloud",
                "type" => "input",
                "editable" => "true",
                "name" => "BEE_TEST_SEC",
                "value" => ""
            ),
            array(
                "title" => "Master Secret",
                "category" => "BeeCloud",
                "type" => "input",
                "editable" => "true",
                "name" => "BEE_MASTER_SEC",
                "value" => ""
            ),
            array(
                "title" => "APP Secret",
                "category" => "BeeCloud",
                "type" => "input",
                "editable" => "true",
                "name" => "BEE_APP_SEC",
                "value" => ""
            ),
            array(
                "title" => "APP ID",
                "category" => "BeeCloud",
                "type" => "input",
                "editable" => "true",
                "name" => "BEE_APP_ID",
                "value" => ""
            ),
            array(
                "title" => "百度API",
                "category" => "短信配置",
                "type" => "input",
                "editable" => "false",
                "name" => "BAIDU_KEY",
                "value" => ""
            ),
            array(
                "title" => "支付宝订单超时时间",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "false",
                "name" => "ALIPAY_TIME_OUT",
                "value" => "2h"
            ),
            array(
                "title" => "支付宝签名方式",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "false",
                "name" => "ALIPAY_SIGN_TYPE",
                "value" => "MD5"
            ),
            array(
                "title" => "支付宝-默认商品展示页面",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_SHOP_URL",
                "value" => $domain_name
            ),
            array(
                "title" => "卖家email",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_SELLER_EMAIL",
                "value" => ""
            ),
            array(
                "title" => "支付宝同步通知接口",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_RETURN_URL",
                "value" => $domain_name . "index.php/yyg/pay/pay_result.html"
            ),
            array(
                "title" => "支付宝扫码支付方式",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_QR_PAY_MODE",
                "value" => "1"
            ),
            array(
                "title" => "支付宝公钥,用于RSA验证",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_PUBLIC_KEY",
                "value" => ""
            ),
            array(
                "title" => "合作者身份ID",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_PARTNER",
                "value" => ""
            ),
            array(
                "title" => "支付宝异步通知接口",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_NOTIFY_URL",
                "value" => $domain_name . "index.php/pay/notify/alipay"
            ),
            array(
                "title" => "手机端支付宝同步通知接口",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_MOBILE_RETURN_URL",
                "value" => $domain_name . "index.php/mobile/Alipay/return_url"
            ),
            array(
                "title" => "手机端支付宝接口名称",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "false",
                "name" => "ALIPAY_MOBILE_API_NAME",
                "value" => "alipay.wap.create.direct.pay.by.user"
            ),
            array(
                "title" => "MD5签名密钥",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_MD5_TOKEN",
                "value" => ""
            ),
            array(
                "title" => "HTTPS形式消息验证地址",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_HTTPS_GATEWAY",
                "value" => "https://mapi.alipay.com/gateway.do?"
            ),
            array(
                "title" => "HTTP形式消息验证地址",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "false",
                "name" => "ALIPAY_HTTP_GATEWAY",
                "value" => "http://notify.alipay.com/trade/notify_query.do?"
            ),
            array(
                "title" => "支付宝请求错误同步接口",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_ERROR_NOTIFY_URL",
                "value" => $domain_name . "index.php/pay/error"
            ),
            array(
                "title" => "字符集编码",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "false",
                "name" => "ALIPAY_CHAR_SET",
                "value" => "utf-8"
            ),
            array(
                "title" => "支付宝接口地址",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIPAY_API_URL",
                "value" => $domain_name . "index.php/pay/alipay"
            ),
            array(
                "title" => "支付宝接口名称",
                "category" => "支付宝配置",
                "type" => "input",
                "editable" => "false",
                "name" => "ALIPAY_API_NAME",
                "value" => "create_direct_pay_by_user"
            ),
            array(
                "title" => "阿里大鱼SECRET",
                "category" => "短信配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIDAYU_SECRET",
                "value" => ""
            ),
            array(
                "title" => "阿里大鱼KEY",
                "category" => "短信配置",
                "type" => "input",
                "editable" => "true",
                "name" => "ALIDAYU_KEY",
                "value" => ""
            ),
        );
        usleep(1000);
        $m = M('conf', 'sp_');
        foreach (array_reverse($conf_arr) as $k => $v) {
            //检测是否存在,存在更新不存在新增
            $r = $m->where(array('name' => $v['name']))->find();
            if (!empty($r)) {
                $m->where(array('name' => $v['name']))->save($v);
            } else {
                $m->add($v);
            }
        }
        try {
        } catch (Exception $e) {
            $this->show_msg('写入用户信息失败');
        }


    }

    /**
     * 写入配置文件
     * @param  array $config 配置信息
     *
     */
    public function write_config($config = [])
    {
        if (is_array($config)) {
            //读取配置内容
            $conf = file_get_contents('./data/db/db.tpl');
            //替换配置项
            foreach ($config as $name => $value) {
                $conf = str_replace("[{$name}]", $value, $conf);
            }

            //写入应用配置文件
            if (!IS_WRITE) {
                return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>/app/database.php</p>
            <textarea name="" style="width:650px;height:185px">' . $conf . '</textarea>';
            } else {
                if (file_exists('./../app/database.php')) {
                    $tmp = file_get_contents('./../app/database.php');
                    if (!is_dir('./data/db/')) mkdir('./data/db/', 777, true);
                    file_put_contents('./data/db/database.bak.' . date('ymdhis'), $tmp);
                }
                if (file_put_contents('./../app/database.php', $conf)) {
                    $this->show_msg('配置文件写入成功');
                } else {
                    $this->show_msg('配置文件写入失败！', 'error');
                    session('error', true);
                }
                return '';
            }

        }
    }
}