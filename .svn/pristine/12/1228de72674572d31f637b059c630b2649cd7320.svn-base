<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/7/1
 * Time: 15:05
 */
namespace app\mobile\controller;
use app\mobile\model\CardCategory;

class Card extends AccountBase{
    public function __construct(){
        parent::__construct();

    }
    public function trans(){
        $this->assign('money_name',$this->get_conf('money_name'));
        $this->assign('win_record_id',I('get.id'));
        if(I('get.id')){
            $win=M('win_record')->field('notice,logistics_status')->where(['id'=>I('get.id')])->find();
            empty($win['notice'])&&M('win_record')->save(['id'=>I('get.id'),'notice'=>1]);
            empty($win['logistics_status'])&&M('win_record')->save(['id'=>I('get.id'),'logistics_status'=>1]);
        }
        return $this->display();
    }
    //充值香肠币
    public function trans_xc(){
        $c_model=new CardCategory();
        $this->assign('card',$c_model->get_card_by_win(I('get.id')));
        $this->assign('money_name',$this->get_conf('money_name'));
        $this->assign('money_unit',$this->get_conf('money_unit'));
        return $this->display();
    }
    //转换香肠比
    public function do_trans(){
        empty(I('post.win_id'))&&wrong_return('参数错误');
       // empty(I('post.money'))&&wrong_return('金额不能为空');
        //判断商品是否卡密
        $card=$this->get_reward_type(I('post.win_id'));
        if($card['reward_type']==1&&!empty($card['money'])){
            $data=[
                'uid'=>get_user_id(),
                'money'=>$card['money'],
                'time'=>time()
            ];
            $c_model=new CardCategory();
            $res=$c_model->trans_xc($data,I('post.win_id'));
            $res&&ok_return('转换成功');
            wrong_return('转换失败');
        }else{
            wrong_return('商品类型不对');
        }
    }
    //香肠币转换成功
    public function trans_success(){
        $this->assign('money_name',$this->get_conf('money_name'));
        return $this->display();
    }
    //充值话费
    public function charge(){
        $c_model=new CardCategory();
        $rt=$c_model->charge_records(I('get.id'));
        !$rt&&wrong_return('不能重复兑换或者联系管理员添加卡密');
        $this->assign('data',$rt['data']);
        $this->assign('title',$rt['title']);
        $this->assign('money',$rt['money']);
        return $this->display();
    }
    //中奖纪录详情
    public function record_detail(){
        $win_record=M('win_record')->field('id,create_time,logistics_status as prize_status,address_data,reward_type')->where(['id'=>I('get.win_record_id')])->find();
        if(!empty($win_record['address_data'])){
            $this->assign('data',json_decode($win_record['address_data'],true));
        }
        $this->assign('win_record',$win_record);
        return $this->display();
    }
    //获取兑换详情
    public function ajax_detail(){
        $win_id=I('post.win_id');
        empty($win_id)&&wrong_return('参数错误');
        $win_record=M('win_record')->field('address_data,reward_type')->where(['id'=>$win_id])->find();
        if($win_record['reward_type']==1){
            $category=M('card_category')->field('number,money')->where(['id'=>I('post.reward')])->find();
            $this->assign('money',intval($category['money']/$category['number']));
        }
        $this->assign('reward_type',$win_record['reward_type']);
        $this->assign('money_name',$this->get_conf('money_name'));
        $this->assign('list',json_decode($win_record['address_data'],true));
        return json_encode(
            [
                'code'=>1,
                'html'=>$this->fetch()
            ]
        );
    }
    public function get_conf($name){
        return M('conf')->where(['name'=>strtoupper($name)])->getField('value');
    }
    private function get_reward_type($win_id){
        if($win_id){
            return M('win_record')->alias('w')->field('c.money,g.reward_type')
                ->join('sp_goods g on g.id=w.goods_id')
                ->join('sp_card_category c on c.id=g.reward_data')
                ->where(['w.id'=>$win_id])
                ->find();
        }
    }

}