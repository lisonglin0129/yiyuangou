<?php
namespace app\core\lib;
/**
 * 正则校验类
 * User: phil
 * Date: 2016/3/26
 * Time: 11:56
 */
Class RegExp
{
    public function exec($post)
    {
        extract($post);
        if (empty($type) || empty($val)) return false;
        switch ($type) {
            //用户名正则
            case "username":
                $preg = '/(^[^\d_][0-9a-zA-Z\x{4e00}-\x{9fa5}_]{4,20}$)|(^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$)|(^1\\d{10}$)/u';//非数字下划线开头的,4-20位中英文数字下划线
                break;
            //密码正则
            case "password":
                $preg = '/^.{6,20}$/';//6-20位任意字符串
                break;
            //手机号正则
            case "phone":

                $preg = '/^[1][3-9][0-9]{9}$/';//11位数字
                break;
            //手机号验证码
            case "phone_code":
                $preg = '/^[0-9]{4,6}$/';//4-6位数字
                break;
            //汉字中文数字下划线
            case "ch_en_num":
                $preg = '/(^[0-9a-zA-Z\x{4e00}-\x{9fa5}_]{1,100}$)/u';//4-6位数字
                break;
            default:
                $preg = '/.*/';
        }
        return preg_match($preg, $val);
    }
}