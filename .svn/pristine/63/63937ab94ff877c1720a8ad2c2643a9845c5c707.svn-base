<?php
namespace app\admin\controller;


use app\admin\model\WithdrawsCashModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;

Class Withdraw extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }


    //提现列表
    public function show_list()
    {
        $field = I('post.field');
        switch ($field) {
            case 1:
                $field = 'a.name';
                break;
            case 2:
                $field = 'a.bank_no';
                break;

        }
        //获取列表
        $condition_rule = array(
            array(
                'field' => $field,
                'value' => I('post.keywords',''),
                'operator' => 'like',   //关系符号
            ),
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $withdraw = new WithdrawsCashModel();
        $withdraw_list = $withdraw->get_withdraw_list($model);

        /*生成分页html*/
        $my_page = new Page($withdraw_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);


        $this->assign('withdraw_list', $withdraw_list['data']);
        return $this->fetch();

    }


    /**
     * 修改提现状态
     */
    public function update_withdraw_status()
    {
        $id = (int)I('post.id');
        $status = I('post.status');

        $withdraw = new WithdrawsCashModel();

        $res = $withdraw->update_withdraw_status($id, $status);


        if ($res['code'] == '1') {
            ok_return('修改成功');
        } else {
            wrong_return($res['msg']);
        }

    }


    /**
     * 批量审核提现
     */
    public function update_more_withdraw_status()
    {
        $id = I('post.id');
        $status = I('post.status');


        $withdraw = new WithdrawsCashModel();

        $res = $withdraw->update_more_withdraw_status($id, $status);


        if ($res) {
            ok_return('修改成功');
        } else {
            wrong_return('部分修改失败');
        }
    }


}