<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/25
 * Time: 9:47
 */
namespace app\mobile\controller;

use app\admin\model\SpreadModel;
use app\core\model\ArticleModel;
use app\mobile\model\Common;
use app\core\model\RewardModel;
use app\core\model\CommonModel;
use app\core\model\UserModel;
use app\core\model\WithdrawsCashModel;

class Spread extends AccountBase
{
    private $m;

    public function __construct()
    {
        parent::__construct();
        $this->m = new RewardModel();
    }

    //推广首页
    public function index()
    {
        //获取用户信息
        $s_modle = new SpreadModel();
        $u_model = new UserModel();
        $w_model = new WithdrawsCashModel();
        $notice = M('conf')->field('value')->order('id asc')->where("name ='PROMOTE_NOTICE'")->select();
        $this->assign('notice', $notice);

        $c_model = new Common();
        $this->assign('m_name', $c_model->get_conf('MONEY_UNIT'));
        $this->assign('v_money', $w_model->get_verified_money());
        $this->assign('level', $s_modle->get_spread_by_type(2));
        $this->assign('register', $s_modle->get_spread_by_type(1));
        $this->assign('info', $u_model->get_login_user_info_detail());
        return $this->display();
    }

    //转香肠币
    public function trans_xc()
    {

        $cash = M('users')->where(['id' => get_user_id()])->getField('cash');
        $this->assign('cash', $cash);
        return $this->display();
    }

    //转换成功页面
    public function trans_success()
    {
        return $this->display();
    }

    //将账户余额转换为香肠
    public function get_money_by_cash()
    {
        $money = floatval(I('post.money', 0));
        ($money <= 0) && wrong_return('转换失败');

        //获取用户余额
        $u_model = new UserModel();
        $user_info = $u_model->get_login_user_info();
        $last_money = empty($user_info['cash']) ? 0 : floatval($user_info['cash']);
        if ($last_money < $money) wrong_return('余额不足');
        $rt = $u_model->get_money_by_cash($money);
        $rt && ok_return('转换成功!');
        wrong_return('转换失败');
    }

    //分销推广奖励
    public function promote_reward()
    {
        $m_article = new ArticleModel();
        $article = $m_article->get_article_detail_by_name('LEVEL', 'title,content_mob');

        $this->assign('article', $article);
        return $this->display();
    }

    //注册推广奖励
    public function register_reward()
    {
        $m_article = new ArticleModel();
        $register = $m_article->get_article_detail_by_name('REGISTER', 'title,content_mob');

        $this->assign('register', $register);
        return $this->display();
    }

    //注册明细
    public function register_detail()
    {
        $list = $this->m->m_register_rewards(get_user_id());
        $this->assign('list', $list);
        $this->assign('pages', $this->m->m_register_pages(get_user_id()));
        return $this->display();
    }

    //ajax获取注册列表
    public function ajax_register_detail()
    {
        $list = $this->m->m_ajax_register_detail(get_user_id(), I('post.offset'), I('post.year'), I('post.month'));
        $this->assign('list', $list);
        return $this->fetch();
    }

    //
    public function ajax_promote_detail_list()
    {
        $list = $this->m->m_ajax_promote_detail_list(get_user_id(), I('post.offset'), I('post.year'), I('post.month'), I('post.level'));
        $this->assign('data', $list);
        return $this->fetch();
    }

    //分销推广明细
    public function promote_detail()
    {
        $this->assign('last', $this->m->get_last_rewards());
        return $this->display();
    }

    public function ajax_promote_detail()
    {
        $this->assign('data', $this->m->m_ajax_promote_detail(get_user_id(), I('post.year'), I('post.month')));
        return $this->fetch();
    }

    public function promote_detail_list()
    {
        $list = $this->m->m_promote_detail(I('get.level'), get_user_id());
        $this->assign('pages', $this->m->m_promote_pages(I('get.level'), get_user_id()));
        $this->assign('level', I('get.level'));
        $this->assign('data', $list);
        return $this->display();
    }

    public function down_pic()
    {
        ob_clean();
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=qrcode_spread.jpg");
        readfile(I('get.url'));
    }

    public function me_codes()
    {
        $u_model = new UserModel();
        $this->assign('info', $u_model->get_login_user_info_detail());
        return $this->display();
    }

    public function wechat_spread()
    {
        // $qr_url = U('other_users/wechat_spread',['uid'=>session('user.id')],true,true);
        $data = $this->get_ticket($this->get_access_token(), session('user.id'));
        $qr_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $data['ticket'];
        $this->assign('spread_url', $qr_url);
        return $this->fetch();


    }

    //生成微信二维码
    public function get_access_token()
    {
        if (!S('access_token')) {
            $m = new \app\mobile\model\Common();
            $appid = $m->get_conf('UNION_WECHAT_MP_APPID');
            $appsec = $m->get_conf('UNION_WECHAT_MP_APPSEC');
            $access_toekn_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsec;
            $data = json_decode(curl_http($access_toekn_url), true);
            S('access_token', $data['access_token'], 3600);
        }
        return S('access_token');
    }

    public function get_ticket($access_token, $uid)
    {
        $ticket_url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
        return json_decode(curl_http($ticket_url, '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": ' . $uid . '}}}'), true);
    }

    public function sweep_the_yard()
    {
        $data = $this->get_ticket($this->get_access_token(), session('user.id'));
        $qr_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $data['ticket'];
        $this->assign('spread_url', $qr_url);
        return $this->fetch();
    }

    public function wechat_money()
    {
        return $this->display();
    }

    public function red_login()
    {

        return $this->display();
    }

    public function red_register()
    {
        return $this->display();
    }

    public function pattern()
    {
        // $qr_url = U('other_users/wechat_spread',['uid'=>session('user.id')],true,true);
        $data = $this->get_ticket($this->get_access_token(), session('user.id'));
        $qr_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $data['ticket'];
        $this->assign('spread_url', $qr_url);
        return $this->fetch();
    }

    public function telephone_charge()
    {
        // $qr_url = U('other_users/wechat_spread',['uid'=>session('user.id')],true,true);
        $data = $this->get_ticket($this->get_access_token(), session('user.id'));
        $qr_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $data['ticket'];
        $this->assign('spread_url', $qr_url);
        return $this->fetch();
    }

    public function sausage()
    {
        // $qr_url = U('other_users/wechat_spread',['uid'=>session('user.id')],true,true);
        $data = $this->get_ticket($this->get_access_token(), session('user.id'));
        $qr_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $data['ticket'];
        $this->assign('spread_url', $qr_url);
        return $this->fetch();
    }

    public function sausage_win()
    {
        // $qr_url = U('other_users/wechat_spread',['uid'=>session('user.id')],true,true);
        $data = $this->get_ticket($this->get_access_token(), session('user.id'));
        $qr_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $data['ticket'];
        $this->assign('spread_url', $qr_url);
        return $this->fetch();
    }

    //积分转虚拟币
    public function trans_2xc()
    {
        $m_com = new CommonModel();
        $c_score = $m_com->get_conf('MARKETING_SCORE');
        $c_money = $m_com->get_conf('MARKETING_MONEY');
        $m_user = new UserModel();
        $info = $m_user->get_login_user_info_detail();


        $this->assign('info', $info);
        $this->assign('c_score', $c_score);
        $this->assign('c_money', $c_money);
        return $this->fetch();
    }

    //积分转虚拟币处理
    public function transensure()
    {
        $m_user = new UserModel();
        $score = I('post.score');
        $front_res_money = (float)I('post.font_res_money');
        $password = md6(I('post.password'));
        return $m_user->transensure($score, $password, $front_res_money);
    }

    //积分转虚拟币成功跳转
    public function score_trans_success()
    {
        return $this->fetch();

    }
}