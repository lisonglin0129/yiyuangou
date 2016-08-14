<?php
/**
 * Created by PhpStorm.
 * User: liuchao
 * Date: 16/6/25
 * Time: 下午7:48
 */
namespace app\mobile\controller;

use app\core\model\CommonModel;
use app\core\model\PacketModel;
use app\core\model\PacketRecordModel;
use app\lib\Wechat;
use app\mobile\model\Users;
use think\Controller;

class Packet extends Controller
{
    private $wechat;
    private $c_model;

    public function __construct()
    {
        parent::__construct();
        $this->c_model=new CommonModel();
        $this->wechat=new Wechat(['appid'=>$this->c_model->get_conf('UNION_WECHAT_MP_APPID'),'appsecret'=>$this->c_model->get_conf('UNION_WECHAT_MP_APPSEC')]);
        $this->assign('wap_title',$this->c_model->get_conf('WAP_WEB_TITLE'));
    }

    //拆红包记录信息
    public function info()
    {

        $p_model = new PacketModel();
        $p_r_model=new PacketRecordModel();

        //微信sdk微信授权
        if(!session('openid')){
            //当前访问链接
            $cur_url="http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            session('openid',$this->wechat->getOauthAccessToken()['openid']) ;
            $code_url = $this->wechat->getOauthRedirect($cur_url);
            if (empty($_GET['code']) && empty($_GET['state'])) {
                header("Location: $code_url");
                exit;
            }
        }
        $u_model=new Users();
        $this->assign('packet', $p_model->get_packet(I('get.packet_id')));
        $this->assign('user',$u_model->other_user_info(I('get.uid')));
        $this->assign('list',$p_r_model->get_records(I('get.packet_id')));
        $this->assign('num',$p_r_model->get_num(I('get.packet_id')));
        $this->assign('total',$p_r_model->get_total(I('get.packet_id')));
        $this->assign('packet_id',I('get.packet_id'));
        $this->assign('uid',I('get.uid'));
        return $this->display();
    }
    public function ajax_records(){
        $p_r_model=new PacketRecordModel();
        $this->assign('list',$p_r_model->ajax_records(I('post.packet_id'),I('post.offset')));
        return $this->fetch();
    }
    //注册
    public function reg(){
        $this->assign('spread_userid',I('get.uid'));
        return $this->display();
    }
    //登录
    public function login(){
        return $this->display();
    }
    public function qr_code(){
        empty($qr=$this->c_model->get_conf('wechat_qrcode'))&&wrong_return('没有配置公众号二维码');
        $this->assign('qr_code',$this->c_model->get_conf('website_url').$qr);
        return $this->display();
    }
    //拆取红包
    public function knock_packet(){
      empty(session('openid'))&&wrong_return('获取用户openid失败');
        empty(I('post.packet_id'))&&wrong_return('获取红包参数失败');
        //验证是否拆取过红包
        $p_r_model=new PacketRecordModel();
        $record=$p_r_model->check_record(I('post.packet_id'),session('openid'));
        !empty($record)&&wrong_return('每个用户只能领取一次红包');
        //获取红包信息
        $packet=M('packet')->field('money,num,uid')->where(['id'=>I('post.packet_id')])->find();
        //判断红包是否被领取完
        $p_num=$p_r_model->get_num(I('post.packet_id'));
        intval($p_num)>=intval($packet['num'])&&wrong_return('红包已被领取完');
        $money=round($packet['money']/$packet['num'],2);
        //微信用户信息
        $user_info=$this->wechat->getUserInfo(session('openid'));
        $data=[
            'wx_name'=>$user_info['nickname'],
            'wx_head'=>$user_info['headimgurl'],
            'packet_id'=>I('post.packet_id'),
            'openid'=>session('openid'),
            'money'=>!empty($money)?$money:'',
            'create_time'=>time(),
        ];
        $res=$p_r_model->add_record($data);
        session('money',$money);
        //分享者的信息
        $share_info=M('users')->field('nick_name,user_pic')->where(['id'=>$packet['uid']])->find();

        if($user_face=M('image_list')->where(['id'=>intval($share_info['user_pic'])])->getField('img_path')){
            if(strpos($user_face,'http')===false){
                $user_face=$this->c_model->get_conf('WEBSITE_URL').$user_face;
            }
        }else{
            $user_face=$this->c_model->get_conf('WEBSITE_URL').'/data/img/noPicture.jpg';
        }

        session('share_nick_name',$share_info['nick_name']);
        session('share_head',$user_face);
        $res&&ok_return('拆取成功');
        wrong_return('拆取失败');
    }
    //公众号发送红包
    public function send_packet($openid='okEllw7PsIQQCH9YWTVvkD-XVQrs'){
       $money=0.01;
        $sender='test';
        $data=[
            'wxappid'=>$this->c_model->get_conf('UNION_WECHAT_MP_APPID'),
            'mch_id'=>$this->c_model->get_conf('WXPAY_MP_MCHID'),
            'mch_billno'=>$this->c_model->get_conf('WXPAY_MP_MCHID').date('YmdHis').rand(1000,9999),
            'client_ip'=>$_SERVER['REMOTE_ADDR'],
            're_openid'=>$openid,
            'total_amount'=>$money,
            'min_value'=>$money,
            'max_value'=>$money,
            'total_num'=>1,
            'nick_name'=>$sender,
            'send_name'=>$sender,
            'wishing'=>'恭喜发财',
            'act_name'=>$sender.'红包',
            'remark'=>$sender.'红包',
            'nonce_str'=>$this->wechat->generateNonceStr(32),
        ];
        $data['sign']=$this->wechat->get_packet_sign($data,false,$this->c_model->get_conf('WXPAY_MP_KEY'));
        dump($data);
        $res=$this->wechat->curl_post_ssl($this->arrayToXml($data),120,[],$this->c_model->get_conf('WXPAY_MP_SSLCERT_PATH'),$this->c_model->get_conf('WXPAY_MP_SSLKEY_PATH'),$this->c_model->get_conf('WXPAY_MP_SSLCA_PATH'));
        dump($res);

    }
    private function arrayToXml($arr){
        $xml="<xml>";
        foreach($arr as $key=>$val){
            if(is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]</".$key.".";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}