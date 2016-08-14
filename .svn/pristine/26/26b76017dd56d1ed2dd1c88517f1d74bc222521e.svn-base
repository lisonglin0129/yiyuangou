<?php
namespace app\yyg\controller;
use app\core\controller\Common;
use app\core\model\ImageModel;
use app\core\model\OrderModel;
use app\core\model\UserModel;

class Ta extends Common
{
    private $user_info;

    public function __construct()
    {
        parent::__construct();

        $uid = I('param.uid',0,'intval');

        //自己信息回自己个人中心看去
        if(get_user_id()==$uid && $uid>0){
            $this->redirect('Ucenter/index');
        }

        //统一读取用户信息
        $m_user = new UserModel();
        $this->user_info=$m_user->get_user_simple_info_by_id($uid);
        if(!$this->user_info){
            $this->error('你所查看的用户失踪了');
            die();
        }

        $m_image = new ImageModel();
        $avatar = $m_image->get_img_map_by_ids($this->user_info['user_pic'],'id,img_path,thumb_path');
        if($avatar){
            $avatar = array_shift($avatar);
            $avatar = $avatar['img_path'];
            $this->user_info['avatar'] = $avatar;
        }
        $this->assign('user_info',$this->user_info);
    }

    /**
     * index
     * 用户信息首页/夺宝记录
     *
     *
     * @author(s)	xiaoyu <9#simple.moe>;
     * @since 		2016年4月19日 10:06:10
     */
    public function index(){
        return $this->history();
    }

    //夺宝记录
    public function history(){
        return $this->fetch('history');
    }
    //夺宝记录 ajax拉取
    public function history_list(){
        $page = I('post.page',1,'intval');
        if($page>=5){
            $page=5;
        }
        $size = 10;
        $m_order = new OrderModel();
        list($result,$count_all,$step,$num) = $m_order->get_payed_order_by_user($this->user_info['id'],$page,$size);
        foreach($result as &$each_row){
            $each_row['n_percent'] = $each_row['n_sum']>0?number_format($each_row['n_part'] * 100 / $each_row['n_sum'],2):'0.00';
        }
        $this->assign('order_list',$result);
        $this->assign('count_all',$count_all);
        $this->assign('page_count',floor($count_all/$size)+1);
        $this->assign('page_current',$page);
        $this->assign('num',$num);
        return json_encode(['code'=>1,'length'=>count($result),'count'=>$count_all,'html'=>$this->fetch()]);
    }

    //幸运记录
    public function luck(){
        $m_order = new OrderModel();
        list($luck_list,$count_all)= $m_order->get_lucky_history_by_uid($this->user_info['id']);
        $this->assign('luck_list',$luck_list);
        $this->assign('count_all',$count_all);
        return $this->fetch();
    }

    //晒单记录
    public function share(){
        $m_order = new OrderModel();
        list($show_list,$count_all) = $m_order->get_share_list_by_uid($this->user_info['id']);
        $this->assign('show_list',$show_list);
        $this->assign('show_count',$count_all);
        return $this->fetch();
    }
}