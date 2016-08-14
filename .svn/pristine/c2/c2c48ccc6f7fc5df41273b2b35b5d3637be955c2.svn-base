<?php
namespace app\core\model;

use think\Exception;

Class AuthModel
{
    //初始化
    public function init()
    {
        $this->check_domain();
        $this->set_conf();
        if (C('WEB_SITE_CLOSE') == 'true') {
            die('站点已经关闭，请稍后访问~');
        }
    }

    //设置配置
    private function set_conf()
    {
        //配置不存在
        if (empty(S('config_cache'))) {
            /* 读取站点配置 */
            $m = M('conf', 'sp_');
            $r = $m->select();
            foreach ($r as $k => $v) {
                $r[$k]['name'] = strtoupper($r[$k]['name']);
            }
            $r = array_column($r, 'value', 'name');


            S('config_cache', $r);
        }

        //取配置,赋值
        C(S('config_cache')); //添加配置
    }


    public function check_domain()
    {
        $key_path = './data/key/domain.dat';
        try {
            $val = file_get_contents($key_path);
        } catch (Exception $e) {
            die('授权文件读取失败');
        }
        $val = $this->decode($val);
        extract($val);
        $expire = empty($expire_time) ? '0' : strtotime($expire_time);
        //选择模块
        $model_arr = empty($model_arr) ? [] : $model_arr;
        //根域名
        $root_domain = empty($root_domain) ? '' : strtolower($root_domain);
        //其他域名
        $other_domain = empty($other_domain) ? [] : $other_domain;

        //到期时间
        ($expire < NOW_TIME) && die('软件使用时间已过期:' . $expire_time);
        //判断是否允许模块
        $model_name = strtolower(MODULE_NAME);
        if (in_array($model_name, ["yyg", "mobile", "api"])) {
            !in_array($model_name, $model_arr) && die('该模块没有授权访问,请联系梦蝶软件(http://mengdie.com)');
        }

        //当前域名:
        $domain = $_SERVER['HTTP_HOST'];
        //域名授权标记
        $flag = false;
        if (!empty($root_domain)) {
            if (preg_match('/(^[a-zA-Z0-9_-]+\.' . $root_domain . '$)|(^' . $root_domain . '$)/', $domain)) {
                $flag = true;
            }
        }
        if (!empty($other_domain)) {
            if (in_array($domain, $other_domain)) {
                $flag = true;
            }
        }

        if (!$flag) {
            echo '域名没授权哦!已授权域名如下列表:<br>';
            if (!empty($root_domain)) echo '<span style="color: #f36;font-weight: bold">' . $root_domain . '的根域名以及全部二级域名可用</span><br><br>';
            if (!empty($other_domain)) {
                if (is_array($other_domain)) {
                    foreach ($other_domain as $k => $v) {
                        echo '<span style="color: #5397ff;">域名***(' . $v . ')***已授权</span><br>';
                    }
                } else {
                    echo $other_domain;
                }
            }
            die('<br>一经发现非法传播本系统或者尝试破解本系统,本公司将停止对该用户的一切更新维护,所有损失由购买者负责,特此声明');
        }
    }

    private function decode($val)
    {
        $val = base64_decode($val);
        empty($val) && die('密钥错误[code:0x01-->base_64 error]');
        $val = explode('|', $val);
        count($val) < 3 && die('密钥错误[code:0x02-->explode error]');
        $str = $val[0] . '|' . $val[1] . 'http://www.mengdie.com';
        (md6($str) !== $val[2]) &&
        die('密钥错误[code:0x04-->sign error]');

        $val = authcode($val[0], true, $val[1]);
        empty($val) && die('密钥错误[code:0x03-->auth error]');

        $arr = json_decode($val, true);
        empty($arr) && die('密钥错误[code:0x05-->json error]');
        return $arr;
    }

}