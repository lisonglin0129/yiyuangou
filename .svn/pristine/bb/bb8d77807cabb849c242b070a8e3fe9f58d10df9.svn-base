<?php
namespace app\admin\controller;

use app\admin\model\ShowOrderModel;
use app\admin\model\WinRecordModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;

Class Win extends Common
{
    public function __construct()
    {
        //判断是否从消息进入晒单界面
        $is_notice =  I('get.notice');
        $order_id =  I('get.id');
        if (!is_null($is_notice)){
            $this->update_read_flag($order_id);
        }
        parent::__construct();

    }

    //首页
    public function index()
    {
        return $this->fetch();
    }


    //期数列表
    public function show_list()
    {
        $value = I('post.field');
        $value1 = I('post.field2');
        $value2 = I('post.field3');

        //获取列表
        $condition_rule = array(
            array(
                'field' => $value,
                'value' => I('post.keywords')
            ),
            array(
                'field' => 'u.type',
                'value' => $value1
            ),
            array(
                'field' => 'w.logistics_status',
                'value' => $value2
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $win_record = new WinRecordModel();
        $win_record_list = $win_record->get_win_record_list($model);


        /*生成分页html*/
        $my_page = new Page($win_record_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        //用户类型条件筛选项
        $this->assign('pages', $pages);
        $this->assign('win_record_list', $win_record_list['data']);
        return $this->fetch();
    }

    /**
     * 查看中奖纪录
     * @return mixed
     */
    public function get_info_by_id() {
        $id = (int)I('get.id');
        empty($id) && die('传真错误');

        $win_record = new WinRecordModel();
        $info = $win_record->get_info_by_id($id);
        $this->assign("info", $info);
        return $this->fetch('form');
    }


    //修改
    public function update()
    {
        $post = I("post.");
        extract($post);
        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        $win_record = new WinRecordModel();
        $rt = $win_record->update_win_record($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //晒单
    public function show_order(){
        $wid = I('get.id');
        $m_win = new WinRecordModel();
        $goods_info = $m_win->get_goods($wid);
        $this->assign('wid',$wid);
        $this->assign('goods_info',$goods_info);
        return $this->display();
    }

    public function update_read_flag($id)
    {
        //修改消息状态
        $m = new WinRecordModel();
        $m->update_read_flag($id);

    }

    //保存机器人晒单
    public function show_order_update(){
        $post = I("post.", []);
        extract($post);
        //图片
        strpos($pic_list,'null,') !== false && wrong_return("上传图片不能为空");
        $m=new WinRecordModel();
        $this->update_read_flag($wid);
        $win_record=$m->get_win_record(I('post.wid'));
        $post=array_merge($win_record,I('post.'));
        $post['pid']=3;
        if(!empty($post['auto_time'])){
            $time=explode('-',$post['auto_time']);
            $post['create_time'] = mktime(0,0,0,$time[1],$time[2],$time[0])+mt_rand(0,86400);
        }else{
            $post['create_time']=time()+mt_rand(0,86400);
        }
        $post['complete']=1;
        $s_m=new ShowOrderModel();
        $rt = $s_m->rt_update_win_record($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

}