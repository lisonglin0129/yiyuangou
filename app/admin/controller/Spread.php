<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/21
 * Time: 15:29
 */
namespace app\admin\controller;
use app\admin\controller\Common;
use app\admin\model\RewardModel;
use app\admin\model\SpreadModel;
use app\lib\Condition;
use app\lib\Page;

class Spread extends Common{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m=new SpreadModel();
    }
    //分销推广
    public function distribute(){
        $spread=$this->m->get_spread_by_type(2);
        $this->assign('spread',$spread);
       return $this->display();
    }
    //注册推广
    public function reg(){
        $spread=$this->m->get_spread_by_type(1);
        $this->assign('spread',$spread);
        return $this->display();
    }
    //注册推广更新
    public function reg_update(){
        $post=I('post.');
        $res = $this->m->reg_update($post);
        ($res!==false) && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //分销推广更新
    public function dis_update(){
        $post=I('post.');
        $res = $this->m->dis_update($post);
        ($res!==false) && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //注册推广列表
    public function reg_list(){
        return $this->display();
    }
    public function  reg_show_list(){
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'u2.id',
                'value' => I('post.keywords')
            ),
            array(
                'field'=>'r.status',
                'value'=>I('post.status')
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $r = new RewardModel();
        $reg_list = $r->get_reg_list($model);

        /*生成分页html*/
        $my_page = new Page($reg_list["count"], $this->page_num, $this->page, U('reg_show_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);
        $this->assign('reward',$reg_list['reward']);
        $this->assign('reg_list', $reg_list['data']);

        return $this->fetch();
    }
    //分销推广列表
    public function promote_list(){
        return $this->display();
    }
    public function promote_show_list(){
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'u2.username',
                'operator'=>'LIKE',
                'value' => I('post.keywords')
            ),
            array(
                'field'=>'r.status',
                'value'=>I('post.status')
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $r = new RewardModel();
        $promote_list = $r->get_promote_list($model);

        /*生成分页html*/
        $my_page = new Page($promote_list["count"], $this->page_num, $this->page, U('promote_show_list'), 5);
        $pages = $my_page->myde_write();

        $this->assign('pages', $pages);
        $this->assign('total',$promote_list['total']);
        $this->assign('reward',$promote_list['reward']);
        $this->assign('reward_score',$promote_list['reward_score']);
        $this->assign('promote_list', $promote_list['data']);

        return $this->fetch();
    }
    //推广记录状态更新
   public function update_promote_status(){
      $r_model=new RewardModel();
       $res = $r_model->update_status(trim(I('post.id'),','),I('post.status'));
       if($res){
           ok_return('操作成功');
       }else{
           wrong_return('操作失败');
       }
   }
    public function update_reg_status(){
        $r_model=new RewardModel();
        $res = $r_model->update_status(trim(I('post.id'),','),I('post.status'),2);
        if($res){
            ok_return('操作成功');
        }else{
            wrong_return('操作失败');
        }
    }
}