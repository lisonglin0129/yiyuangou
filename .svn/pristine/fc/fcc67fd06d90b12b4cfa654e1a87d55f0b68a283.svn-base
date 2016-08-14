<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/25
 * Time: 17:46
 */
namespace app\mobile\controller;
use app\core\model\AccountModel;
use app\core\model\UserModel;
use app\core\model\WithdrawsCashModel;

class Extract extends AccountBase{
    public function __construct(){
        parent::__construct();
    }
    public function index(){
        $a_model=new AccountModel();
        $account=$a_model->get_accounts();
        if(!$account){
            $this->redirect('account');
            exit;
        }
        $this->assign('accounts',$account);
        return $this->display();
    }
    //提现账户
    public function account(){
        return $this->display();
    }
    //添加账户页面
    public function add_account(){
       return $this->display();
    }
    // 账户保存
    public function account_sub(){
       $post=I('post.');
        $post['uid']=get_user_id();
        $a_model=new AccountModel();
        $rt = $a_model->account_add($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    public function go_extract(){
        $u_model=new UserModel();
        $a_model=new AccountModel();
        $w_model=new WithdrawsCashModel();
        $account=$a_model->get_accounts();
        $this->assign('v_money',$w_model->get_verified_money());
        $this->assign('cash',$u_model->get_leave_cash());
        $this->assign('accounts',$account);
      return $this->display();
    }
    //提现纪录
    public function extracts(){
        $w_model=new WithdrawsCashModel();
        $this->assign('list',$w_model->m_withdraw_list());
        $this->assign('pages',$w_model->get_withdraw_pages());
        return $this->display();
    }
    //jaxj获取提现记录
    public function ajax_extracts_list(){
        $w_model=new WithdrawsCashModel();
        $this->assign('list',$w_model->m_ajax_withdraw_list(I('post.offset'),I('post.start'),I('post.end')));
        return $this->fetch();
    }
    //提现申请
    public function extract_apply(){
        $data=I('post.');
        $w_model=new WithdrawsCashModel();
        $rt=$w_model->add_withdraws_cash($data);
        $rt && ok_return('申请成功!');
        wrong_return('申请失败');
    }
    //密码验证
    public function extract_check(){
        $u_model=new UserModel();
        $rt = $u_model->check_extract_user(I('post.'));
        $rt && ok_return('验证成功!');
        wrong_return('验证失败');
    }

}