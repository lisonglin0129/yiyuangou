<?php
/**
 * Created by PhpStorm.
 * User: bb3201
 * Date: 2016/6/4
 * Time: 13:56
 */

namespace app\admin\controller;


use app\admin\model\GoodsModel;
use app\admin\model\RtPresetsModel;
use app\admin\model\RtRegularModel;
use app\lib\Condition;
use app\lib\Page;

class RtRegular extends  Common {
    private $_time_range = array();

    public function index() {
        return $this->fetch();
    }

    //任务列表
    public function show_list()
    {
        $keywords=null;
        extract(I('post.'));

        $keywords = remove_xss($keywords);
        //获取列表
        $condition_rule = array(
            [
                'field'    => 'g.name',
                'operator' => 'LIKE',
                'value'    => $keywords
            ]

        );
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m_rt = new RtRegularModel();
        $presets_list = $m_rt->regular_list($model);
        /*生成分页html*/
        $my_page = new Page($presets_list["count"], $this->page_num, $this->page, U('presets_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('list', $presets_list['data']);
        return $this->fetch();
    }

    public function exec() {
        $m = new RtRegularModel();
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);
        $res = $m->is_add($id);

        if ( $res ) {

            $info = $m->get_info_by_id($id,'edit');

        } else {
            $info = $m->get_info_by_id($id,'add');
        }

        $m_goods = new GoodsModel();
        empty($info) && die('获取信息失败');
        $is_cheat = $m_goods->get_field_by_gid($id,'is_cheat');

        isset($is_cheat['is_cheat']) && $this->assign('is_cheat',$is_cheat['is_cheat']);
        $this->assign("info", $info);



        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
//            } else if($type=="gen_task") {
//                $this->gen($info);
            }else{
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }
        return $this->fetch('form');

    }

    //ajax请求增加参数设置
    public function ajax_set_condition() {
        $info['num'] = (int) I('post.length');
        $info['num_max'] = (int) I('post.num_max');
        $info['num_min'] = (int) I('post.num_min');
        $info['num'] === 0 && wrong_return('参数错误');
        $this->assign(array('info'=>$info));
        return $this->fetch();


    }

    public function update(){
        $max_buy_times=$min_time=$max_time=$data=$exec_times=$multiples=null;
        $post = I("post.", []);
        extract(I('post.'));
        $m = new RtRegularModel();
        !is_numeric($exec_times) && wrong_return('执行次数必须为数字');
        !$this->range_verify($data) && wrong_return('时间范围选择错误');
        $res = $this->verify_data($data);
        !is_bool($res) && wrong_return($res);
        //生成任务
        $res = $m->create_tasks($post);
        $res == true && ok_return('添加成功');
        wrong_return('添加失败');

    }

    //验证输入
    public function verify_data($data) {
        foreach ( $data as $item ) {
            if ( empty($item['min_time']) && $item['min_time'] != 0 ){
                return '最小购买时间不能为空';

            }if ( $item['min_time'] < 10 ){
                return '最小购买时间不能低于10秒';

            } else if ( empty($item['max_time']) && $item['max_time'] != 0 ) {
                return '最大购买时间不能为空';

            } else if ( empty($item['min_buy_times']) && $item['min_buy_times'] != 0 ) {
                return '最小购买次数不能为空';

            } else if ( is_numeric($item['min_buy_times']) === false  ) {
                return '最小购买次数只能为数字';

            } else if ( $item['min_buy_times'] < 1 ) {
                return '最小购买次数不能小于1';
            }
            else if ($item['min_time'] > $item['max_time']) {
                //最小时间不能大于最大时间
                return '最小时间不能大于最大时间';
            } else if ($item['max_buy_times'] < $item['min_buy_times']) {
                //最大购买次数输入错误
                return '最大购买次数输入错误';
            }
        }

        return true;
    }

    //时间选择范围验证
    public function range_verify($data) {
        $a = array();
        foreach ( $data as $item ) {
            $a[] = explode(',',$item['time_range']);
        }

        $r = array_unique(call_user_func_array('array_merge', $a));
        sort($r);

        foreach($a as $v) {
            if(array_search($v[0], $r) != array_search($v[1], $r) - 1) return false;
        }
        return true;

    }


    //删除事件

    public function del($id) {
        $m = new RtRegularModel();
        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除失败');

    }



    //日志首页
    public function regular_log() {
        return $this->fetch();
    }

    //获取日志列表
    public function show_log_list() {

        //获取列表
        $condition_rule = array(

        );
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m_rt = new RtRegularModel();
        $presets_list = $m_rt->get_log($model);

        /*生成分页html*/
        $my_page = new Page($presets_list["count"], $this->page_num, $this->page, U('presets_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('list', $presets_list['data']);
        return $this->fetch();

    }

    //删除参数配置
    public function del_conf() {
        $id =  I('post.id');
        $m = new RtRegularModel();
        $m->remove_conf($id) !== false && ok_return('删除成功');
        wrong_return('删除失败');


    }

}