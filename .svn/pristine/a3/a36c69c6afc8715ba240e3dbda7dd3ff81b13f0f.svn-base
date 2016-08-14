<?php
namespace app\yyg\controller;


use app\admin\model\SpreadModel;
use app\core\controller\Common;
use app\core\model\AccountModel;
use app\core\model\ArticleModel;
use app\core\model\CommonModel;
use app\core\model\GoodsModel;
use app\core\model\OrderModel;
use app\core\lib\Condition;
use app\core\lib\Page;
use app\core\model\ImageModel;
use app\core\model\PacketModel;
use app\core\model\RewardModel;
use app\core\model\UcenterModel;
use app\core\model\UserModel;
use app\core\model\WithdrawsCashModel;
use app\pay\model\ExecModel;
use think\Controller;
use think\Db;
use think\Exception;
use Think\Model;

/**
 * 用户中心
 * User: phil
 * Date: 2016/4/11
 * Time: 21:51
 */
Class Ucenter extends Common
{
    private $user_info,$start,$end,$time;

    public function __construct()
    {
        parent::__construct();
        $this->user_info = session("user");
        //TODO:跳转登录
        if (empty($this->user_info)) {
            if (IS_AJAX) {
                return json_encode(['code' => -1, 'msg' => '请先登录']);
            } else {
                $this->redirect(U("user/login"));
                die();
            }
        }
    }

    //检查是否登录,没有登录则跳转
    private function chk_login()
    {
        if (!is_user_login()) {
            $this->redirect(U("user/login"));
        }
    }

    //裁切
    public function cut()
    {
        return $this->fetch();
    }

    //我的夺宝
    public function index()
    {
        $m_com = new CommonModel();
        $c_score = $m_com->get_conf('MARKETING_SCORE');
        $c_money = $m_com->get_conf('MARKETING_MONEY');
        $m_user = new UserModel();
        $info = $m_user->get_login_user_info_detail();
        $this->assign("info", $info);
        $m_order = new OrderModel();
        list($result, $count_all,$step,$num) = $m_order->get_payed_order_by_user($this->user_info['id'], 1, 20, null);
        foreach ($result as &$each_row) {
            if($each_row['join_type']==1){
                $each_row['n_percent'] = $each_row['odd_times'] > 0 ? number_format($each_row['odd_join_num'] * 100 / $each_row['odd_times'], 2) : '0.00';
                $each_row['n_remain']=$each_row['odd_times']-$each_row['odd_join_num'];
            }elseif($each_row['join_type']==2){
                $each_row['n_percent'] = $each_row['even_times'] > 0 ? number_format($each_row['even_join_num'] * 100 / $each_row['even_times'], 2) : '0.00';
                $each_row['n_remain']=$each_row['even_times']-$each_row['even_join_num'];
            }else{
                $each_row['n_percent'] = $each_row['n_sum'] > 0 ? number_format($each_row['n_part'] * 100 / $each_row['n_sum'], 2) : '0.00';
                $each_row['n_remain']=$each_row['n_sum']-$each_row['n_part'];
            }
        }
        foreach ($result as &$each_row) {
            $each_row['n_percent'] = $each_row['n_sum'] > 0 ? number_format($each_row['n_part'] * 100 / $each_row['n_sum'], 2) : '0.00';
        }

        $this->assign('c_score',$c_score);
        $this->assign('c_money',$c_money);
        $this->assign('num',$num);
        $this->assign('order_list',$result);
        return $this->fetch();
    }

    //获取问候
    private function get_hello() {
        $h=date("H");
        if($h<11) $hello =  "早上好";
        else if($h<13) $hello = "中午好";
        else if($h<17) $hello = "下午好";
        else $hello = "晚上好";
        return $hello;
    }

    //夺宝记录
    public function deposer()
    {
        return $this->fetch();
    }

    //ajax拉取 夺宝记录-参与记录
    public function deposer_list()
    {
        $action = I('post.action', '', 'trim');
        $page = I('post.page', 1, 'intval');
        $size = I('post.size', 20, 'intval');
        $date = I('post.date', 0, 'intval');
        $stat = null;
        switch ($action) {
//    夺宝记录-多期参与
            case 'all':
                break;
//    夺宝记录-即将揭晓
            case 'willReveal':
                $stat = 2;
                break;
//    夺宝记录-正在进行
            case 'periodIng':
                $stat = 1;
                break;
//    宝记录-已揭晓
            case 'periodRevealed':
                $stat = 3;
                break;
            default:
                return json_encode(['code' => -2, 'msg' => '参数错误']);
        }
        $m_order = new OrderModel();
        list($result, $count_all,$result_count,$num) = $m_order->get_payed_order_by_user($this->user_info['id'], $page, $size, $stat, $date);
        $count_stat = $m_order->get_payed_order_count_by_user($this->user_info['id']);
        $count_stat['result_count'] = $result_count;
        foreach ($result as &$each_row) {
            if($each_row['join_type']==1){
                $each_row['n_percent'] = $each_row['odd_times'] > 0 ? number_format($each_row['odd_join_num'] * 100 / $each_row['odd_times'], 2) : '0.00';
                $each_row['n_remain']=$each_row['odd_times']-$each_row['odd_join_num'];
            }elseif($each_row['join_type']==2){
                $each_row['n_percent'] = $each_row['even_times'] > 0 ? number_format($each_row['even_join_num'] * 100 / $each_row['even_times'], 2) : '0.00';
                $each_row['n_remain']=$each_row['even_times']-$each_row['even_join_num'];
            }else{
                $each_row['n_percent'] = $each_row['n_sum'] > 0 ? number_format($each_row['n_part'] * 100 / $each_row['n_sum'], 2) : '0.00';
                $each_row['n_remain']=$each_row['n_sum']-$each_row['n_part'];
            }
        }
        $this->assign('order_list',$result);
        $this->assign('count_all',$count_all);
        $this->assign('num',$num);
        $this->assign('page_count',floor($count_all/$size)+1);
        $this->assign('page_current',$page);

        return json_encode(['code' => 1, 'length' => count($result), 'count' => $count_all, 'html' => $this->fetch('deposer_ajax_table'),'count_stat'=>$count_stat]);
    }

    //幸运记录
    public function luck()
    {
        $size = I('get.size',20,'intval');
        $page = I('get.page',1,'intval');
        $m_uc = new UcenterModel();
        list($luck_list, $count_all) = $m_uc->get_my_lucky_by_uid($this->user_info['id'],$page,$size);
        $this->assign('luck_list', $luck_list);
        $this->assign('count_all',$count_all);

        $page = page_info_format($count_all,$size,$page);

        $this->assign('page_count',$page['page_count']);
        $this->assign('page_current',$page['page_current']);
        return $this->fetch();
    }
    //虚拟商品
    private function luck_detail_card($luck_info,$goods_info){

        $m_uc = new UcenterModel();
        if($luck_info['luck_type']==1) {
            //更新中奖通知状态
            if ($luck_info['notice'] == 0) {
                $update_result = $m_uc->set_win_record_notify($luck_info['id']);
                $luck_info['logistics_status'] = 1;
            }
        }

        $card_info = $m_uc->get_card_category_by_id($goods_info['reward_data']);
        $this->assign('card_info',$card_info);
        $this->assign('card_value',number_format(floatval($card_info['money'])/intval($card_info['number']),2));
        if(IS_POST){
            $action = I('post.action');
            switch($action){
                case 'choose_reward':
                    if($luck_info['logistics_status'] == 1) {
                        $reward_type = I('post.reward_type', null, 'trim');
                        if ($reward_type == 'recharge') {
                            $recharge_result = $m_uc->add_money($this->user_info['id'],$card_info['money']);
                            if($recharge_result){
                                $time = time();
                                $m_uc->set_reward_info($luck_info['id'], -1, json_encode(['uid'=>$this->user_info['id'], 'money' => $card_info['money'],'time'=>$time]));
                                $m_uc->set_luck_status_by_id($luck_info['id'], 5);
                                $luck_info['address_data'] = json_encode(['uid'=>$this->user_info['id'], 'money' => $card_info['money'],'time'=>$time]);
                                $luck_info['reward_type'] = -1;
                                $luck_info['logistics_status'] = 5;
                            }else{

                            }
                        } elseif ($reward_type == 'card') {
                            //list($card_list,$card_count) = $m_uc->get_card_detail($goods_info['reward_data'], $this->user_info['id'], $luck_info['nper_id'],$card_info['number']);
                            list($card_list,$card_count) = $m_uc->get_card_detail($goods_info['reward_data'],$card_info['number']);
                            if ($card_count>=$card_info['number'] && count($card_list)>=$card_info['number']) {
                                $cards_id = array_column($card_list,'id');
                                $sign_result = $m_uc->sign_card_detail($cards_id, $this->user_info['id'], $luck_info['nper_id']);
                                $m_uc->set_reward_info($luck_info['id'], 1, json_encode($card_list));
                                $m_uc->set_luck_status_by_id($luck_info['id'], 5);
                                $luck_info['address_data'] = json_encode($card_list);
                                $luck_info['reward_type'] = 1;
                                $luck_info['logistics_status'] = 5;
                            } else {
                                return $this->error('获取卡密失败,请联系客服',$_SERVER['HTTP_REFERER']);
                            }
                        }
                    }
                    break;
                default :
                    break;
            }
        }
        if($luck_info['logistics_status']==5){
            if($luck_info['reward_type']==1){
                $card_list=json_decode($luck_info['address_data'],true);
                $this->assign('multi_card',!empty(array_column($card_list,'id')));
                $this->assign('card',$card_list);
            }elseif($luck_info['reward_type']==-1){
                $reward_detail=json_decode($luck_info['address_data'],true);
                $this->assign('reward',$reward_detail);
            }
        }
        $this->assign('luck_info',$luck_info);
        return $this->fetch('luck_detail_v');
    }
    //实物奖详情
    private function luck_detail_r($luck_info){
        $m_uc = new UcenterModel();
        if($luck_info['luck_type']==1) {
            //更新中奖通知状态
            if ($luck_info['notice'] == 0) {
                $update_result = $m_uc->set_win_record_notify($luck_info['id']);
                $luck_info['logistics_status'] = 1;
            }
            //提交
            if (IS_POST) {
                $step = I('post.step', 0, 'intval');
                switch ($step) {
                    case 1:
                        //确认地址
                        if ($luck_info['logistics_status'] == 1) {
                            $addr_id = I('post.addr_id', 0, 'intval');
                            $addr_data = $m_uc->get_addr_info_by_id($addr_id, 'name,code,address,phone,provice,city,area');
                            if ($addr_data) {
                                $result = $m_uc->set_win_record_addr($luck_info['id'], json_encode($addr_data));
                                $luck_info['address_data'] = json_encode($addr_data);
                                $luck_info['logistics_status'] = 2;
                                $m_uc->prize_status_add($luck_info['id'],1);
                            }else{
                                return $this->error('无效的收货地址');
                            }
                        };
                        break;
                    case 3:
                        //确认收货
                        if ($luck_info['logistics_status'] == 3) {
                            $result = $m_uc->set_luck_status_by_id($luck_info['id'], 4);
                            $luck_info['logistics_status'] = 4;
                            $m_uc->prize_status_add($luck_info['id'],3);
                            $m_uc->prize_status_add($luck_info['id'],4);
                            $show_order = $m_uc->show_order_init([
                                'order_id' => $luck_info['order_id'],
                                'goods_id' => $luck_info['goods_id'],
                                'nper_id' => $luck_info['nper_id'],
                                'status' => -3,
                                'uid' => $luck_info['luck_uid'],
                                'username' => $luck_info['luck_user'],
                                'luck_num' => $luck_info['luck_num'],
                                'complete' => 0
                            ]);
                            $luck_info['show_order'] = $show_order;
                        }
                        break;
                }
            }

            switch ($luck_info['logistics_status']) {
                case 0:
                    $luck_info['logistics_status'] = 1;
                case 1:
                    //获取当前用户收货地址列表,以默认排序
                    $addr_list = $m_uc->get_login_user_addr();
                    $this->assign("addr_list", $addr_list);
                    break;
            }
            $this->assign('address_data', json_decode($luck_info['address_data'], true));
        }
        $this->assign('luck_info',$luck_info);

        return $this->fetch('luck_detail');
    }
    //中奖详情
    public function luck_detail(){
        $id = $_GET['id'];
        $m_uc = new UcenterModel();
        $luck_info = $m_uc->get_luck_detail_by_luckid_and_uid($id,$this->user_info['id']);
        if(empty($luck_info)){
            return $this->error('因为不是您的中奖信息,所以无法查看',U('ucenter/luck'));
        }
        $m_goods = new GoodsModel();
        $goods_info = $m_goods->get_goods_info_by_id($luck_info['goods_id']);
        if($goods_info['reward_type']==1 && $luck_info['luck_type']==1){
            return $this->luck_detail_card($luck_info,$goods_info);
        }else{
            return $this->luck_detail_r($luck_info);
        }
    }

    //购买记录
    public function buy()
    {
        return $this->fetch();
    }

    //我的晒单
    public function order()
    {
        $m_order = new OrderModel();
        list($show_list, $count_all) = $m_order->get_share_list_by_uid($this->user_info['id']);
        $this->assign('show_list', $show_list);
        $this->assign('show_count', $count_all);
        return $this->fetch();
    }

    //个人资料
    public function person()
    {

        $m_user = new UserModel();
        $info = $m_user->get_login_user_info_detail();
        $this->assign("info", $info);

        return $this->fetch();
    }

    //基本设置
    public function base()
    {
        $m_user = new UserModel();
        $info = $m_user->get_login_user_info();
        $this->assign("info", $info);
        return $this->fetch();
    }

    //收货地址
    public function addr()
    {
        //获取当前用户收货地址列表,以默认排序
        return $this->fetch();
    }

    //收货地址列表
    public function addr_list()
    {
        //获取当前用户收货地址列表,以默认排序
        $m_uc = new UcenterModel();
        $addr_list = $m_uc->get_login_user_addr();
        $this->assign("addr_list", $addr_list);
        //用户当前地址数量
        $num = count($addr_list);
        $last_num = 5 - $num;
        $data = array(
            "num" => empty($num) ? 0 : $num,
            "last_num" => empty($last_num) ? 0 : $last_num,
            "code" => "1",
            "html" => $this->fetch(),

        );
        die_json($data);
    }

    public function get_addr_info_by_id()
    {
        $id = I("post.id", "");
        !is_numeric($id) && die_json("-100");//id不能为空
        $m_uc = new UcenterModel();
        $info = $m_uc->get_addr_info_by_id($id);
        if ($info) {
            $data = array(
                "code" => "1",
                "data" => $info
            );
            die_json($data);
        }
        die_json("-1");
    }

    //新增一条地址
    public function add_new_addr()
    {
        $m_uc = new UcenterModel();
        //获取用户地址总数
        $addr_list = $m_uc->get_login_user_addr();
        count($addr_list) >= 5 && die_json("-100");//数量超多不能保存
        $post = I("post.");
        !is_array($post) && die_json("-110");//参数错误
        extract($post);
        !CheckPost($code) && die_json("-70");
        !checkIdCard($sn_id)&& die_json("-90");
        !isMobile($phone)&&die_json("-80");//手机号不对
        empty($name) && die_json("-120");//姓名不能为空
        empty($code) && die_json("-130");//邮编不能为空
        empty($address) && die_json("-140");//地址不能为空
        empty($phone) && die_json("-150");//手机不能为空
        empty($province) && die_json("-160");//省份不能为空
        empty($city) && die_json("-170");//城市不能为空


        if (!empty($default_addr)) {
            //设置该用户全部地址为不默认
            $m_uc->set_login_addr_by_feild(array("type" => 0));
        }
        $data = array(
            "sn_id" => empty($sn_id) ? "" : $sn_id,
            "name" => empty($name) ? "" : $name,
            "code" => empty($code) ? "" : $code,
            "address" => empty($address) ? "" : $address,
            "tel" => empty($tel) ? "" : $tel,
            "phone" => empty($phone) ? "" : $phone,
            "provice" => empty($province) ? "" : $province,
            "city" => empty($city) ? "" : $city,
            "area" => empty($area) ? "" : $area,
            "type" => !empty($default_addr) ? "1" : 0,
        );
        if (!empty($id)) $data["id"] = $id;
        $m_uc->update_new_addr($data) && die_json("1");
        die_json("-1");
    }



    //删除一条地址
    public function del_addr_by_id()
    {
        $id = I("post.id", "");
        !is_numeric($id) && die_json("-100");
        //删除
        $m_uc = new UcenterModel();
        $m_uc->del_addr_by_id($id) && die_json("1");
        die_json("-1");
    }

    //充值记录
    public function charge()
    {
        //获取用户信息

        $m_user = new UserModel();
        $info = $m_user->get_login_user_info();
        $this->assign("info", $info);
        return $this->fetch();
    }

    //充值记录列表
    public function charge_list()
    {
        //获取当前用户充值记录列表
        $m_uc = new UcenterModel();

        //第几页
        $page = (int)I('post.page', '1');
        $month_fitter = (int)I('post.month_fitter', '3');
        $page_num = 10;//每页显示条数

        $condition_rule = array(
            array(
            'relation' => 'AND', //关系
            'operator' => '>',   //关系符号
            'field' => 'create_time',   //sql语句where里的字段
            'value' => strtotime("-".$month_fitter." month", NOW_TIME),  //参数值
            )
        );

        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();

        $rt = $m_uc->get_login_user_charge_list($model);

        /*生成分页html*/
        $mpage = new Page($rt["count"], $page_num, $page, U('charge_list'), 5);
        $pages = $mpage->myde_write();

        $this->assign('pages', $pages);

        $list = $rt["data"];
        if(!empty($list)){
            $timestamp=NOW_TIME;
            $m_com = new CommonModel();
            $key = $m_com->get_conf("TOKEN_ACCESS");
            foreach ($list as $k=>$v) {
                $list[$k]["url"]=U('pay/alipay/index').'?order_id='.$v["order_id"].'&timestamp='.$timestamp.'&sign='.md5($v["order_id"].$timestamp.$key);
            }
        }
        $this->assign("list", $list);

        $data = array(
            "code" => "1",
            "html" => $this->fetch(),
        );
        die_json($data);
    }

    //TA的夺宝记录
    public function view_deposer()
    {
        return $this->fetch('view_deposer');
    }

    //TA的幸运记录
    public function view_luck()
    {
        return $this->fetch('view_luck');
    }
    //注册明细
    public function register_detail(){
        return $this->display();
    }
    //注册明细列表
    public function register_detail_list(){
        $page = (int)I('post.page', '1');
        $year_fitter=(int)I('post.year_fitter');
        $month_fitter = (int)I('post.month_fitter');
        $page_num = 10;//每页显示条数
        $this->time_init($year_fitter,$month_fitter);
        $condition_rule=array(
            array(
                'field' => 'r.create_time',
                'operator' => '>=',   //关系符号
                'value' => $this->start
            ),
            array(
                'field' => 'r.create_time',
                'operator' => '<=',   //关系符号
                'value' => $this->end
            )
        );
        $r_model=new RewardModel();
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();
        $rt = $r_model->get_register_rewards_by_id($model);
        /*生成分页html*/
        $mpage = new Page($rt["count"], $page_num, $page, U('register_detail_list'), 5);
        $pages = $mpage->myde_write();
        $this->assign('pages', $pages);
        $this->assign("list", $rt);

        $data = array(
            "code" => "1",
            "html" => $this->fetch(),
        );
        die_json($data);
    }
    //注册奖励
    public function register_reward(){
        $m_article = new ArticleModel();
        $register = $m_article->get_article_detail_by_name('REGISTER','title,content');

        $this->assign('register',$register);
        return $this->display();
    }
    //分销奖励
    public function distribution(){
        $m_article = new ArticleModel();
        $article = $m_article->get_article_detail_by_name('LEVEL','title,content');

        $this->assign('article',$article);
        return $this->display();
    }
    //推广主页
    public function promote()
    {
        $user_model=new UserModel();
        $r_model=new RewardModel();
        $w_model=new WithdrawsCashModel();
        //获取问候
        $hello = $this->get_hello();
        $this->assign('hello',$hello);
        $this->assign('v_money',$w_model->get_verified_money());
        $this->assign('rewards',$r_model->get_last_rewards());
        $this->assign('info',$user_model->get_login_user_info_detail());
        return $this->fetch('promote');
    }
    //推广明细
    public function promote_detail()
    {
        return $this->fetch('promote_detail');
    }
    public function promote_detail_list(){
        //第几页
        $page = (int)I('post.page', '1');
        $year_fitter=(int)I('post.year_fitter');
        $month_fitter = (int)I('post.month_fitter');
        $page_num = 10;//每页显示条数
        $this->time_init($year_fitter,$month_fitter);
        $condition_rule=array(
            array(
                'field' => 'create_time',
                'operator' => '>=',   //关系符号
                'value' => $this->start
            ),
            array(
                'field' => 'create_time',
                'operator' => '<=',   //关系符号
                'value' => $this->end
            )
        );
        $r_model=new RewardModel();
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();
        $rt = $r_model->get_rewards_group_by_level($model);
        /*生成分页html*/
        $mpage = new Page($rt["count"], $page_num, $page, U('promote_detail_list'), 5);
        $pages = $mpage->myde_write();
        $this->assign('pages', $pages);
        $this->assign("list", $rt);

        $data = array(
            "code" => "1",
            'total'=>$r_model->get_total($model),
            'total_reward'=>$r_model->get_total_reward($model),
            "html" => $this->fetch(),
        );
        die_json($data);
    }
    //提现中心
    public function extract()
    {
        $a_model=new AccountModel();
        $accounts=$a_model->get_accounts();
        $this->assign('accounts',$accounts);
        return $this->fetch('extract');
    }
    //添加银行卡
    public function add_extract_account()
    {
        return $this->fetch('add_extract_acount');
    }
    //保存银行卡
    public function account_save(){
        $post=I('post.');
        $a_model=new AccountModel();
        $rt = $a_model->account_add($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //删除银行卡
    public function del_account(){
        $a_model=new AccountModel();
        $rt = $a_model->account_del(I('get.id'));
        $rt && ok_return('删除成功!');
        wrong_return('删除失败');
    }
    //提现
    public function extract_submit()
    {
        $a_model=new AccountModel();
        //$w_model=new WithdrawsCashModel();
        $accounts=$a_model->get_accounts();
        $this->assign('accounts',$accounts);
       // $this->assign('v_money',$w_model->get_verified_money());
        $this->assign('cash',M('users')->where(['id'=>get_user_id()])->getField('cash'));
        return $this->fetch('extract_submit');
    }
    //提现申请
    public function extract_apply(){
        $data=I('post.');
        $w_model=new WithdrawsCashModel();
        $rt=$w_model->add_withdraws_cash($data);
        $rt && ok_return('申请成功!');
        wrong_return('申请失败');
    }
    //提现记录删除
    public function extract_del(){
        $w_model=new WithdrawsCashModel();
        $rt = $w_model->withdraws_del(I('get.id'));
        $rt && ok_return('删除成功!');
        wrong_return('删除失败');
    }
    //提现记录
    public function extract_list(){
        return $this->display();
    }
    public function extract_show_list(){
        $page = (int)I('post.page', '1');
        $this->start=strtotime(I('post.start'));
        $this->end = strtotime(I('post.end'));
        $page_num = 10;//每页显示条数
        $condition_rule=array(
            array(
                'field' => 'w.create_time',
                'operator' => '>=',   //关系符号
                'value' => $this->start
            ),
            array(
                'field' => 'w.create_time',
                'operator' => '<=',   //关系符号
                'value' => $this->end
            )
        );
       // file_put_contents('./menu.txt',var_export($condition_rule,true));
        $r_model=new WithdrawsCashModel();
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();
        $rt = $r_model->get_withdraws_cash_list($model);
        /*生成分页html*/
        $mpage = new Page($rt["count"], $page_num, $page, U('extract_show_list'), 5);
        $pages = $mpage->myde_write();
        $this->assign('pages', $pages);
        $this->assign("list", $rt);

        $data = array(
            "code" => "1",
            "html" => $this->fetch(),
        );
        die_json($data);
    }

    //修改密码
    public function change_password()
    {
        $uid = get_user_id();
        $old_pass = I("post.old");
        $new_pass1 = I("post.new1");
        $new_pass2 = I("post.new2");
        if (!$uid || empty($old_pass) || empty($new_pass1)) {
            die_json("缺少参数");
        }

        if($new_pass1!==$new_pass2){
            die_json("新密码不一致");
        }

        if ($old_pass == $new_pass1) {
            die_json("新密码与原密码重复");
        }

        //$check_password = md6($old_pass);
        $res = M("users")->where(["id" => $uid, "password" => [
            ['eq', md6($old_pass)],
            ['eq', md5($old_pass)],
            'or'
        ]])->find();
        if (!$res) {
            die_json("密码错误");
        } else {
            $data['password'] = md6($new_pass1);
            $update_res = M("users")->where(array("id" => $uid))->save($data);
            if ($update_res) {
                die_json("修改成功");
            } else {
                die_json("系统繁忙,请稍后再试");
            }
        }


    }
    //按照用户等级查看记录
    public function rewards_level(){
        //第几页
        $page = (int)I('post.page', '1');
        $year_fitter=(int)I('post.year_fitter');
        $month_fitter = (int)I('post.month_fitter');
        $page_num = 10;//每页显示条数
        $this->time_init($year_fitter,$month_fitter,1);
        $condition_rule=array(
            array(
                'field' => 'create_time',
                'operator' => '>=',   //关系符号
                'value' => $this->start
            ),
            array(
                'field' => 'create_time',
                'operator' => '<=',   //关系符号
                'value' => $this->end
            ),
            array(
                'field' => 'level',
                'value' => I('post.level')
            )
        );
        $r_model=new RewardModel();
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();
        $rt = $r_model->get_rewards_by_level($model);
        foreach($rt['data'] as $k=>$v){
            $rt['data'][$k]['time']=$this->time?$this->time:'--';
        }
        /*生成分页html*/
        $mpage = new Page($rt["count"], $page_num, $page, U('rewards_level'), 5);
        $pages = $mpage->myde_write();
        $this->assign('pages', $pages);
        $this->assign("list", $rt);

        $data = array(
            "code" => "1",
            'total'=>$r_model->get_total($model),
            'total_reward'=>$r_model->get_total_reward($model),
            "html" => $this->fetch(),
        );
        die_json($data);
    }
    //推广详情
    public function reward_info(){
        $page = (int)I('post.page', '1');
        $year_fitter=(int)I('post.year_fitter');
        $month_fitter = (int)I('post.month_fitter');
        $page_num = 10;//每页显示条数
       $this->time_init($year_fitter,$month_fitter);
        $condition_rule=array(
            array(
                'field' => 'create_time',
                'operator' => '>=',   //关系符号
                'value' => $this->start
            ),
            array(
                'field' => 'create_time',
                'operator' => '<=',   //关系符号
                'value' => $this->end
            ),
            array(
                'field' => 'level',
                'value' => intval(I('level'))
            ),
            array(
                'field'=>'uid',
                'value'=>intval(I('uid'))
            )
        );
        $r_model=new RewardModel();
        $model = new Condition($condition_rule, $page, $page_num);
        $model->init();
        $rt = $r_model->get_reward_info($model);
        $mpage = new Page($rt["count"], $page_num, $page, U('reward_info'), 5);
        $pages = $mpage->myde_write();
        $this->assign('pages', $pages);
        $this->assign("list", $rt);
        $data = array(
            "code" => "1",
            "html" => $this->fetch(),
        );
        die_json($data);
    }
  //提现检验
    public function extract_check(){
        $u_model=new UserModel();
        $rt = $u_model->check_extract_user(I('get.'));
        $rt && ok_return('验证成功!');
        wrong_return('验证失败');
    }
    //将账户余额转换为香肠
    public function get_money_by_cash(){
       $u_model=new UserModel();
        $rt = $u_model->get_money_by_cash(I('get.money'));
        $rt && ok_return('转换成功!');
        wrong_return('转换失败');
    }
    //TA的晒单
    public function view_order()
    {
        return $this->fetch('view_order');
    }

    /*方法*/
    public function cut_box()
    {
        $data = array(
            "code" => "1",
            "html" => $this->fetch()
        );
        die_json($data);
    }

    //获取图片的详情
    public function get_login_user_img()
    {
        $m_user = new UserModel();
        $u_info = $m_user->get_login_user_info_detail("id", get_user_id());

        $img_url = $u_info['img_path'];
        $id = $u_info['user_pic'];

        empty($img_url) && die_json("-1");
        die_json(array("code" => "1", "img_path" => $img_url, "img_id" => $id));
    }


    //进行晒单的表单
    public function share($id){
        $this->assign('show_order_id',$id);
        return $this->fetch();
    }

    //晒单处理
    public function share_do(){
        $id = I('post.id',0,'intval');
        $title = I('post.title',0,'trim,htmlspecialchars');
        $content = I('post.content',0,'trim,htmlspecialchars');
        $share_img = I('post.share_img/a');
        $pic_list = implode(',',$share_img);
        $m_order = new UcenterModel();
        $m_tmp = M();
        $win_info = $m_tmp->table('sp_win_record r')
            ->field('r.id,r.logistics_status,s.complete,s.uid')
            ->join('sp_show_order s ON r.nper_id = s.nper_id','LEFT')
            ->where(['s.id'=>$id])
            ->find();
        if(empty($win_info))
            return json_encode(['code' => -100,'msg'=>'']);
        if($win_info['logistics_status']!='4' || $win_info['complete']!='0' || $win_info['uid']!=$this->user_info['id'])
            return json_encode(['code' => -110,'msg'=>'当前奖品无法晒单','debug'=>$m_tmp->getLastSql()]);
        if ($result = $m_order->show_order_update($id, $this->user_info['id'], $title, $content, $pic_list)) {
            $m_order->set_luck_status_by_id($win_info['id'],5);
            return json_encode(['code' => 1]);
        } else {
            return json_encode(['code' => -1]);
        }

    }

    //保存用户头像图片id
    public function save_login_user_img()
    {
        $id = I("post.id", "");
        (empty($id) || !is_numeric($id)) && die_json("-100");//id不能为空
        //校验图片是否存在
        $m_img = new ImageModel();
        !$m_img->get_img_list_by_id($id) && die_json("-110");//图片不存在
        //保存图片
        $m_user = new UserModel();
        $save = $m_user->save_login_user_info_by_filed_value(array("user_pic" => $id));
        ($save !== false) && die_json("1");
        die_json("-1");
    }
    //下载二维码图片
    public function down_pic(){
        ob_clean();
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=qrcode_spread.jpg");
        readfile(I('get.url'));
    }
    //获取时间
    private function time_init($year_fitter='',$month_fitter='',$type=''){
        if($year_fitter&&$month_fitter){
            $firstday=$year_fitter.'-'.$month_fitter.'-01';
            $this->start=strtotime($firstday);
            $this->end=strtotime("$firstday +1 month -1 day");
            if(!empty($type)){
                $this->time=$year_fitter.'年'.$month_fitter.'月';
            }
        }elseif($year_fitter&&!$month_fitter){
            $firstday=$year_fitter.'-01-01';
            $this->start=strtotime($firstday);
            $this->end=strtotime("$firstday +1 year -1 day");
            if(!empty($type)){
                $this->time=$year_fitter.'年';
            }
        }
    }

    public function transensure() {
        $m_user  = new UserModel();
        $score = I('post.score');
        $front_res_money =(float) I('post.font_res_money');
        $password = md6(I('post.password'));
        return $m_user->transensure($score,$password,$front_res_money);
    }



    //新增红包页面
    public function red_packet(){
        //获取用户手机
        $this->assign('phone',M('users')->where(['id'=>get_user_id()])->getField('phone'));
        $this->assign('copy_right',$this->get_conf('WEB_INC_INFO'));

        return $this->display();
    }
    public function unused_packet(){
        $page = (int)I('post.page', '1');
        $page_num = 10;//每页显示条数
        $r_model=new PacketModel();
        $model = new Condition([], $page, $page_num);
        $model->init();
        $rt = $r_model->get_no_send_packets($model);
        /*生成分页html*/
        $mpage = new Page($rt["count"], $page_num, $page, U('unused_packet'), 5);
        $pages = $mpage->myde_write();
        $this->assign('pages', $pages);
        $this->assign("list", $rt['data']);

        $data = array(
            "code" => "1",
            "html" => $this->fetch(),
            "count"=>$rt['count']
        );
        die_json($data);
    }
    public function used_packet(){
        $page = (int)I('post.page', '1');
        $page_num = 10;//每页显示条数
        $r_model=new PacketModel();
        $model = new Condition([], $page, $page_num);
        $model->init();
        $rt = $r_model->get_used_packets($model);
        /*生成分页html*/
        $mpage = new Page($rt["count"], $page_num, $page, U('used_packet'), 5);
        $pages = $mpage->myde_write();
        $this->assign('pages', $pages);
        $this->assign("list", $rt['data']);

        $data = array(
            "code" => "1",
            "html" => $this->fetch(),
            "count"=>$rt['count']
        );
        die_json($data);    }

    public function vouchers(){
        return $this->fetch();
    }

    public function settlement(){
        return $this->fetch();
    }


}