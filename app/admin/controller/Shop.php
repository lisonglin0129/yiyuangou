<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/18
 * Time: 14:05
 */
namespace app\admin\controller;
use app\admin\model\GoodsModel;
use app\admin\model\OrderListModel;
use app\admin\model\SystemModel;
use app\admin\model\UsersModel;
use app\admin\controller\Common;
use app\admin\model\WinRecordModel;
use app\lib\Condition;
use app\lib\Page;
use think\Controller;

class Shop extends Common{
    public function __construct()
    {
        parent::__construct();
    }
    //商家首页
    public function index(){
        $system = new SystemModel();
        //管理员信息
        $admin_info = $system->get_admin_info();
        //数据统计
        $shop_id=I('get.shop_id')?I('get.shop_id'):get_user_id();
        $data_statistics = $system->get_shop_data_statistics($shop_id);
     //   dump($data_statistics);
        //最新订单
       // $new_order = $system->get_new_order();
        $this->assign('admin_info',$admin_info);
        $this->assign('data_statistics',$data_statistics);
        return $this->fetch();
    }
    //商家信息
    public function info(){
        /**
         * lc
         */
        $id = I('get.id')?I('get.id'):session('uid');
//        $id = is_null($id)?'':$id;
//        if ( !is_null($id) ) {
//            $root_url = U('admin/users/shop_list');
//        } else {
//            $root_url = U('info');
//
//        }
        $this->assign('id',$id);
//        $this->assign('root_url',$root_url);
        return $this->fetch();
    }
    public function shop_info(){
        $m=new UsersModel();
        $id = I('get.id')?I('get.id'):session('uid');
        if ( is_null($id) )
            $id=get_user_id();


        if(empty($id)){
            $this->redirect(U('account/shop_login'));
            die();
        }
        $user_type=$m->get_user_type_by_id($id);
        if(is_null($id) && $user_type!=2){
            die("该用户不是商家");
        }
        $info=$m->get_info_by_id($id);
        $this->assign('type','edit');
        $this->assign('info',$info);
        return $this->fetch();
    }
    //查看用户
    public function users(){
       return $this->display();
    }
    //获取用户列表
    public function show_list(){
        $m=new UsersModel();
        $keywords=I('post.keywords');
        if($shop_id=get_user_id()){
            $user_type=$m->get_user_type_by_id($shop_id);
            if($user_type==2){
                $m_goods=new GoodsModel();
                if(!empty($keywords)){
                    $u_ids=$m_goods->get_uids_by_gname_shop_id($keywords,$shop_id);
                }else{
                    $u_ids=$m_goods->get_uids_by_shop_id($shop_id);
                }
                $condition_rule = array(
                    array(
                        'field'=>'id',
                        'operator' => 'IN',   //关系符号
                        'value'=>!empty($u_ids)?$u_ids:'0'
                    )
                );
                //获取列表
                $model = new Condition($condition_rule, $this->page, $this->page_num);
                $model->init();
                $shop_users_list = $m->get_shop_users_list($model);
                /*生成分页html*/
                $my_page = new Page($shop_users_list["count"], $this->page_num, $this->page, U('show_list'), 5);
                $pages = $my_page->myde_write();
                $this->assign('pages', $pages);
                $this->assign('users_list', $shop_users_list['data']);
                return $this->fetch();
            }else{
                die("该用户不是商家");
            }
        }

    }
    //查看中奖用户
    public function win_users(){
       return $this->display();
    }
    //获取中奖用户列表
    public function win_users_list(){
        $keywords=I('post.keywords');
        $time=I('post.time');
        if($shop_id=get_user_id()){
            $m=new UsersModel();
            $user_type=$m->get_user_type_by_id($shop_id);
            if($user_type==2){
                $m_goods=new GoodsModel();
                $goods_ids=$m_goods->get_gids_by_shop_id($shop_id)?$m_goods->get_gids_by_shop_id($shop_id):0;
                $condition_rule = array(
                    array(
                        'field'=>'g.name',
                        'operator' => 'LIKE',   //关系符号
                        'value'=>$keywords
                    ),
                    array(
                        'field'=>'w.goods_id',
                        'operator' => 'IN',   //关系符号
                        'value'=>$goods_ids
                    )
                );
                    if(!empty($time)){
                        $condition_rule[] =  array(
                            'field'=>'w.create_time',
                            'operator'=>'>=',
                            'value'=>strtotime(date('Ymd'))
                        );
                        $condition_rule[] =  array(
                            'field'=>'w.create_time',
                            'operator'=>'<=',
                            'value'=>strtotime(date('Ymd'))+86400
                        );
                    }
            }else{
                die("该用户不是商家");
            }
        }
        //获取列表

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $w_model = new WinRecordModel();
        $shop_win_users_list = $w_model->get_shop_win_record_list($model);
        /*生成分页html*/
        $my_page = new Page($shop_win_users_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('users_list', $shop_win_users_list['data']);
        return $this->fetch();
    }

    //删除中奖纪录
    public function win_del(){
        $id = I("post.id");
        (empty($id)) && wrong_return('参数异常,删除失败');
        $m = new WinRecordModel();
        $m->win_del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }

    //中奖纪录详情
    public function win(){
        $m=new WinRecordModel();
        $info=$m->get_win_user_info(I('get.id'));
        $this->assign('info',$info);
        return $this->display();
    }

    public function update(){
        $username=$phone=null;
        $post = I("post.");
        extract($post);
        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        $m = new UsersModel();

        //检测用户是否已存在
        if(empty($id)) {
            $r = $m->check_exist($username, $phone);
            if($r)wrong_return('用户已存在');
        }

        $rt = $m->update_users($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

}