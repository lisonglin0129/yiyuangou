<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/6/2
 * Time: 11:38
 */
namespace app\admin\controller;


use app\admin\model\GoodsModel;
use app\admin\model\WinRecordModel;
use app\core\lib\Condition;
use app\core\lib\Page;
use app\core\model\CommonModel;

class Zero extends Common{
    public function __construct(){
        parent::__construct();
    }
    //商品管理
    public function goods_list(){
      return $this->display();
    }
    public function goods_show_list(){
        $keywords = null;
        $post = remove_arr_xss(I("post.", []));
        extract($post);
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'g.name',
                'operator' => 'LIKE',   //关系符号
                'value' => $keywords
            ),
        );
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        //获取订单列表
        $m = new GoodsModel();
        $rt = $m->get_zero_goods_list($model);

        /*生成分页html*/
        $my_page = new Page($rt["count"], $this->page_num, $this->page, U('goods_show_list'), 5);
        $pages = $my_page->myde_write();
        $m_com = new CommonModel();
        $domain_base = $m_com->get_conf('WEBSITE_URL');
        $this->assign('domain_base', $domain_base);
        $this->assign('pages', $pages);
        $this->assign('list', $rt['data']);
        return $this->fetch();
    }
    //中奖记录管理
    public function win_records(){
       return $this->display();
    }
    public function win_records_list(){
        $search_type = $keywords = $luck_type = null;
        $post = remove_arr_xss(I("post.", []));
        extract($post);

        switch ($search_type) {
            case 1:
                $field = 'w.nper_id';
                $operator = '=';
                break;
            case 2:
                $field = 'u.nick_name';
                $operator = 'LIKE';
                break;
            case 3:
                $field = 'u.phone';
                $operator = '=';
                break;

        }
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'w.luck_type',
                'value' => $luck_type
            ),
            array(
                'field' => $field,
                'operator' => $operator,   //关系符号
                'value' => $keywords
            ),
        );
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        //获取订单列表
        $m = new WinRecordModel();
        $rt = $m->get_zero_win_record_list($model);

        /*生成分页html*/
        $my_page = new Page($rt["count"], $this->page_num, $this->page, U('win_records_list'), 5);
        $pages = $my_page->myde_write();
        $m_com = new CommonModel();
        $domain_base = $m_com->get_conf('WEBSITE_URL');
        $this->assign('domain_base', $domain_base);
        $this->assign('pages', $pages);
        $this->assign('list', $rt['data']);
        return $this->fetch();
    }
}