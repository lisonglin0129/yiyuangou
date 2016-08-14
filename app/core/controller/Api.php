<?php
namespace app\core\controller;


use app\lib\Geetest;
use app\core\lib\PhoneCode;
use app\core\model\ApiModel;
use app\core\model\LogModel;
use app\core\model\UserModel;

Class Api extends Common
{

    public function __construct()
    {
        parent::__construct();
    }

    //极验证sessionkey
    public function gee_test()
    {
        $user = I("get.user");
        empty($user) && $user = "xiangchang";

        $CAPTCHA_ID = C('CAPTCHA_ID');
        $PRIVATE_KEY = C('PRIVATE_KEY');
        $gee_test = new Geetest($CAPTCHA_ID, $PRIVATE_KEY);

        $user_id = $user;
        $status = $gee_test->pre_process($user_id);
        session('gtserver', $status);
        session('user_id', $user_id);

        return $gee_test->get_response_str();
    }

    /**
     *校验极验证的模版
     * @param user [string]用户名
     * @param geetest_challenge [string]极验证参数1
     * @param geetest_validate [string]极验证参数2
     * @param geetest_seccode [string]极验证参数3
     * @return result 返回结果
     */
    public function check_geetest($user, $geetest_challenge, $geetest_validate, $geetest_seccode)
    {
        //防止刷验证码
        $tmp_1 = session('geetest_challenge');
        ($geetest_challenge == $tmp_1) && wrong_return('验证失效');
        session('geetest_challenge', $geetest_challenge);

        empty($user) && $user = "xiangchang";

        $CAPTCHA_ID = C('CAPTCHA_ID');
        $PRIVATE_KEY = C("PRIVATE_KEY");
        $gee_test = new Geetest($CAPTCHA_ID, $PRIVATE_KEY);

        if (session('gtserver') == 1) {
            $result = $gee_test->success_validate($geetest_challenge, $geetest_validate, $geetest_seccode, $user);
            return $result;
        } else {
            $result = $gee_test->fail_validate($geetest_challenge, $geetest_validate, $geetest_seccode);
            return $result;
        }
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
    //获取手机验证码
    public function get_phone_code()
    {
        $post = I("post.");
       // extract($post);
        $phone = $post['tel'];
        if(!preg_match('/^1[3-9][0-9]{9}$/', $phone))
        {
        	//手机号码格式不正确
        	echo json_encode(array(
        		'code' => '-120'
        	));
        	exit;
        }
        //如果是注册,检查手机号是否已经注册
        if (!empty($reg)) {

            if($this->check_phone($phone)){//该手机已注册
            	
		            echo json_encode(array(
		            		'code' => '-105'
		            ));
		            exit;
            }
        }

        //如果包含极验参数,则走极验流程,否则走验证码
        if (!empty($geetest_challenge)) {
        	
            if($this->check_geetest($phone, $geetest_challenge, $geetest_validate, $geetest_seccode))
            {	//验证码错误,请重新获取
            	echo json_encode(array(
            			'code' => '-105'
            	));
            	exit;
            }
            
        } else {
            if(sp_check_verify_code())
            {//验证码错误
            	echo json_encode(array(
            			'code' => '-120'
            	));
            	exit;
           	}
        }
        $rand_num = rand(100000, 999999);
        $r = $this->get_phone_code_auto($phone, $rand_num);
        $session = md6($phone . $rand_num . NOW_TIME);
        //记录短信发送情况
        $data = array(
            "session" => $session,
            "username" => '',
            "plat" => $r['plat'],
            "use_times" => 0,
            "phone_code" => $rand_num,
            "data" => json_encode($r['r']),
            "phone" => $phone,
            "type" => 'reg',
            "enable" => 'true',
            "expire_time" => (NOW_TIME + 1200),
            'create_time' => time_format()
        );
        $m_api = new ApiModel();
        $rt = array(
            "code" => "1",
            "plat" => $r['plat'],
            "session" => $session,
            "phone_code" => $rand_num,
            "data" => json_encode($r),
            "phone" => $phone,
            "type" => 'reg',
            "expire_time" => NOW_TIME + 1200,
        );

        if ($m_api->add_phone_code($data)) {
            unset($rt['data'], $rt['phone_code']);
            die_json($rt);
        }
    }

    //获取手机验证码(找回密码)(阿里大鱼)
    public function get_phone_code_forgot()
    {
        $post = I("post.");
        extract($post);
        !preg_match('/^1[3-9][0-9]{9}$/', $phone) && die_json("-100");//手机号码格式不正确
        //如果是注册,检查手机号是否已经注册

        $m_user = new UserModel();
        $m_user->get_user_info_by_filed('phone', $phone) || die_json("-105");//该手机已注册


        //如果包含极验参数,则走极验流程,否则走验证码
        if (!empty($geetest_challenge)) {
            !$this->check_geetest($phone, $geetest_challenge, $geetest_validate, $geetest_seccode) && die_json("-110");//验证码错误,请重新获取
        } else {
            !sp_check_verify_code() && die_json("-120");//验证码错误
        }
        $rand_num = rand(100000, 999999);
        $m_sms = new PhoneCode();
        $send_result = $m_sms->alidayu_forgot_sms($phone, $rand_num);
        $r = ['r' => $send_result, 'plat' => 'alidayu'];
//        dump($r);
        $session = md6($phone . $rand_num . NOW_TIME);
        //记录短信发送情况
        $data = array(
            "session" => $session,
            "username" => '',
            "plat" => $r['plat'],
            "use_times" => 0,
            "phone_code" => $rand_num,
            "data" => json_encode($r['r']),
            "phone" => $phone,
            "type" => 'rst_pass',
            "enable" => 'true',
            "expire_time" => (NOW_TIME + 1200),
            'create_time' => time_format()
        );
        $m_api = new ApiModel();
        $rt = array(
            "code" => "1",
            "plat" => $r['plat'],
            "session" => $session,
            "phone_code" => $rand_num,
            "data" => json_encode($r),
            "phone" => $phone,
            "type" => 'rst_pass',
            "expire_time" => NOW_TIME + 1200,
        );
        if ($m_api->add_phone_code($data)) {
            unset($rt['data'], $rt['phone_code']);
            die_json($rt);
        }
    }

    /**
     *功能:通过手机号码检测验证码是否正确,正确则增加使用次数,并且设置为不可用
     * @param phone [string]必须 手机号
     * @param code [string]必须 验证码
     * @param expire [bool]选填 是否校验过期时间
     * @param use_time [int]选填 使用次数
     * @return 结果
     */
    public function check_phone_code($post)
    {
        extract($post);
        if (empty($phone)) return ("-100");//手机号不能为空
        if (empty($phone_code)) return ("-110");//手机验证码不能为空
        $m_api = new ApiModel();
        $info = $m_api->get_info_by_phone_and_code($phone, $phone_code);
        if (empty($info)) return ("-120");//手机验证码不存在
        //如果检测过期时间
        if (isset($expire) && $info['expire_time'] > 0 && $info['expire_time'] < NOW_TIME) return ("-130");//已过期
        //检测使用次数
        if (isset($use_times) && $info['use_times'] >= $use_times) {
            return ("-140");//使用次数超出规定范围
        }
        //检测是否可用
        if ($info['enable'] != 'true') return ("-150");//验证码不可用
        //可用,设置验证码已使用
        $r = $m_api->set_phone_code_used($phone, $phone_code);
        if ($r !== false) return ("1");//验证通过
        return ("-1");//设置验证码失败
    }

    public function rt_phone_code($r)
    {
        switch ($r) {
            case "1":
                break;
            case "-100":
                die_json("-200");//手机号不能为空
                break;
            case "-110":
                die_json("-210");//手机验证码不能为空
                break;
            case "-120":
                die_json("-220");//手机验证码不存在
                break;
            case "-130":
                die_json("-230");//手机验证码已过期
                break;
            case "-140":
                die_json("-240");//使用次数超出规定范围
                break;
            case "-150":
                die_json("-250");//验证码不可用
                break;
            case "-1":
                die_json("-260");//设置验证码失败
                break;
            default:
                die_json("-270");//验证码已失效
        }
    }

    //螺丝帽
    public function luosimao_sms($phone = "18652216784", $phone_code = "1234")
    {
        $m_phone_code = new PhoneCode();
        return $m_phone_code->send_sms_luosimao($phone, $phone_code, $product = '');

    }

    //阿里大鱼验证码
    private function alidayu_sms($phone, $phone_code, $product = '')
    {
        $product = empty(C('SMS_TEMP_NAME')) ? $product : C('SMS_TEMP_NAME');
        $m_sms = new PhoneCode();
        return $m_sms->alidayu_reg_sms($phone, $phone_code, $product);
    }

    //鼎游企信通短信接口
    private function dingxintong_send($phone_num, $phone_code, $product = "")
    {
        $model = new PhoneCode();
        return $model->dingxintong_send($phone_num, $phone_code, $product);
    }

    //根据优先级自动获取手机验证码
    protected function get_phone_code_auto($phone, $rand_num)
    {
        $plat = 'alidayu';
        $r = $this->alidayu_sms($phone, $rand_num);
        //阿里大鱼不能用时,用备用接口
        if (empty($r["result"]['success'])) {
            $plat = 'luosimao';
            /**记录日志*/
            $log = new LogModel();
            $log_text = array(
                'user' => $phone,
                'type' => 'phone_code_error',
                'log' => json_encode($r)
            );
            $log->log_add($log_text);
            $r = $this->luosimao_sms($phone, $rand_num);
            //螺丝帽不能用 就用鼎信通短信
            if ($r['msg'] !== "ok") {
                $plat = 'dingxintong';
                $log_text = array(
                    'user' => $phone,
                    'type' => 'phone_code_error',
                    'log' => json_encode($r)
                );
                $log->log_add($log_text);
                //鼎信通接口
                $r = $this->dingxintong_send($phone, $rand_num);
            }
        }

        return array(
            "r" => $r,//接口返回
            "plat" => $plat
        );
    }


    //根据优先级发送获奖短信
    public function send_win_msg($phone,$nper_id,$token)
    {
        if($token !== C('TOKEN_ACCESS')){
            log_w("中奖短信验证不正确");
            return false;
        }
        $model = new PhoneCode();
        /**记录日志*/
        $log = new LogModel();

        $plat = 'alidayu';
        $r = $model->alidayu_win($phone);
        //阿里大鱼不能用时,用备用接口
        if (empty($r["result"]['success'])) {
            $plat = 'luosimao';
            $r = $model->luosimao_win($phone, $nper_id);
            //螺丝帽不能用 就用鼎信通短信
            if ($r['msg'] !== "ok") {
                $plat = 'dingxintong';
                $log_text = array(
                    'user' => $phone,
                    'type' => 'phone_code_error',
                    'log' => json_encode($r)
                );
                $log->log_add($log_text);
                //鼎信通接口
                $r = $model->dingxintong_win($phone, $nper_id);
                if (!empty($r['msg']) && $r['msg'] !== "ok") {
                    $log_text = array(
                        'user' => $phone,
                        'type' => 'phone_code_error',
                        'log' => json_encode($r)
                    );
                    $log->log_add($log_text);
                }
            }
        }

        return array(
            "r" => $r,//接口返回
            "plat" => $plat
        );
    }

    //获取手机归属地
    public function get_phone_location($phone)
    {
        if (empty($phone)) return false;
//        $m_com = new CommonModel();
        $url = C('TAOBAO_PHONE_API') . $phone;
        $s = curl_http($url);
        $s = gbk2utf8($s);
        preg_match_all("/(\\w+):'([^']+)/", $s, $m);
        $a = array_combine($m[1], $m[2]);
        return $a;
    }

    //获取ip归属地
    public function get_ip_location($ip = "")
    {
        if (empty($ip)) return false;
//        $m_com = new CommonModel();
        $url = C('TAOBAO_IP_API') . $ip;
        $s = curl_http($url);
        return json_decode($s, true);
    }

    //上传图片
    public function up_img()
    {

        $timestamp = microtime_float();
        $uid = get_user_id();
        $token = sign_by_key(array("uid" => $uid, "timestamp" => $timestamp));

        $this->assign('uid', $uid);
        $this->assign('timestamp', $timestamp);
        $this->assign('token', $token);
        return $this->fetch();
    }

    //生成二维码
    public function qr_code($text = '', $level = 0, $size = 3, $margin = 2)
    {
        C('default_return_type', 'null');
        import("app.lib.phpqrcode.phpqrcode", null, '.php');
        ob_clean();
        header('Content-Type:image/png');
        $text = I('get.text', '', 'trim,urldecode');
        \QRcode::png($text, false, $level, $size, $margin);
//        include ('phpqrcode.php');
//        $value = 'http://www.codesc.net';//二维码数据
//        $errorCorrectionLevel = 'L';//纠错级别：L、M、Q、H
//        $matrixPointSize = 10;//二维码点的大小：1到10
//        QRcode::png ( $value, 'ewm.png', $errorCorrectionLevel, $matrixPointSize, 2 );//不带Logo二维码的文件名
//        echo "二维码已生成" . "<br />";
//        $logo = 'http://o7djuqrn7.bkt.clouddn.com/logo1.png';//需要显示在二维码中的Logo图像
//        $QR='qrcode.png';
//        if($logo!== false){
//            $QR= imagecreatefromstring(file_get_contents($QR));
//            $logo= imagecreatefromstring(file_get_contents($logo));
//            $QR_width= imagesx($QR);
//            $QR_height= imagesy($QR);
//            $logo_width= imagesx($logo);
//            $logo_height= imagesy($logo);
//            $logo_qr_width= $QR_width / 5;
//            $scale= $logo_width / $logo_qr_width;
//            $logo_qr_height= $logo_height / $scale;
//            $from_width= ($QR_width - $logo_qr_width) / 2;
//            imagecopyresampled($QR,$logo, $from_width,$from_width, 0, 0,$logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
//        }
//        header("Content-Type:image/jpg");
//        imagepng($QR);
    }

    public function qr_base64($text = '', $level = 0, $size = 3, $margin = 2)
    {
        C('default_return_type', 'null');
        import("app.lib.phpqrcode.phpqrcode", null, '.php');
        ob_clean();
        header('Content-Type:image/png');
        $text = I('get.text', '', 'trim,base64_decode');
        \QRcode::png($text, false, $level, $size, $margin);
    }


}