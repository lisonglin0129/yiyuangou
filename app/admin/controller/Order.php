<?php
namespace app\admin\controller;

use app\admin\model\OrderListModel;
use app\admin\model\OrderListParentModel;
use app\admin\model\UsersModel;
use app\lib\Condition;
use app\pay\controller\Aipay;
use think\Controller;
use app\lib\Page;

Class Order extends Common
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


    //订单列表
    public function show_list()
    {
        $field = I('post.field');
        $value1 = I('post.field2');
        switch ($field)
        {
            case 1:
                $field = 'order_id';
                break;
            case 2:
                $field = 'u.id';
                break;
            case 3:
                $field = "u.origin";
                break;
            default :
                $field = 'order_id';
        }
        //获取列表
        $condition_rule = array(
            array(
                'field' => $field,
                'value' => I('post.keywords')
            ),
            array(
                'field' => 'u.type',
                'value' => $value1
            ),

        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $order = new OrderListModel();
        $order_list = $order->get_order_list($model);

        /*生成分页html*/
        $my_page = new Page($order_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);


        $this->assign('order_list', $order_list['data']);

        return $this->fetch();


    }

    //订单列表
    public function son_show_list()
    {
        //获取列表
        $condition_rule = array(
            array(
                'field' => I('post.field'),
                'value' => I('post.keywords')
            ),
            array(
                'field' => 'pid',
                'value' => I('get.id')
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $order = new OrderListModel();
        $order_list = $order->get_son_order_list($model);

        /*生成分页html*/
        $my_page = new Page($order_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);


        $this->assign('order_list', $order_list['data']);

        return $this->fetch();


    }

    /**
     * 修改订单状态
     */
    public function update_order_status()
    {
        $id = I('post.id');
        $status = I('post.status');
        $table = I('post.table');

        $m = M($table, 'sp_');

        $res = $m->where(array("id"=>$id))->setField('status', $status);
        if ($res) {
            ok_return('修改成功');
        } else {
            wrong_return('修改失败');
        }

    }

    /**
     * 查看幸运数字
     */
    public function see_luck_num()
    {
        $order_id = I('post.order_id');
        if (empty($order_id)) {
            wrong_return('传值错误');
        }
        $m = M('luck_num', null);
        $code_list = $m->field('code_list')->where(array('order_id' => (string)$order_id))->find()['code_list'];

        if (empty($code_list)) {
            wrong_return('没有分配幸运数字哦');
        }

        $code_list = substr($code_list, 1, -1);
        $code_list = str_to_arr($code_list);
        foreach ($code_list as $k => $v) {
            $code_list[$k] = num_base_mask(intval($code_list[$k]),1,0).',&nbsp;';
        }
        $code_list = implode($code_list);
        $arr = array(
            "code" => "1",
            "msg" => "success",
            "list" => $code_list
        );
        die_json($arr);


    }

    //支付订单列表index
    public function pay()
    {
        return $this->fetch();
    }

    //支付订单列表页
    public function show_pay_list()
    {
        $keywords = null;
        $field = I('post.field');
        $value1 = I('post.field2');
        switch ($field)
        {
            case 1:
                $field = 'order_id';
                break;
            case 2:
                $field = 'u.id';
                break;
            case 3:
                $field = "u.origin";
                break;
            default :
                $field = 'order_id';
        }
        //获取列表
        $condition_rule = array(
            array(
                'field' => $field,
                'value' => I('post.keywords')
            ),
            array(
                'field' => 'u.type',
                'value' => $value1
            ),
        );
        $pay_list = new OrderListParentModel();
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $res = $pay_list->get_pay_list($model);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('show_pay_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('pay_list', $res['data']);
        return $this->fetch();
    }

    //掉单查询
    public function search_miss_document($order_id,$plat='all') {
        $m_order_parent = new OrderListParentModel();
        $data = $m_order_parent->get_miss_document_detail($order_id,$plat);
        die_json($data);



    }

   //返还用户支付的钱进入余额
    public function back_money() {
        $order_id = I('post.order_id');
        $pay_plat = I('post.pay_plat');
        //返还用户余额
        $m_order_parent = new OrderListParentModel();
        list($code,$status) = $m_order_parent->give_back_money($order_id,$pay_plat);
        $data['code'] = $code;
        $data['status'] = $status;

        die_json($data);


    }


}