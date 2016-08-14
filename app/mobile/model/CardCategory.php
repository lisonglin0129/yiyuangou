<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/7/1
 * Time: 17:19
 */
namespace app\mobile\model;
use think\model\Adv;

class CardCategory extends Adv{
   public function __construct(){
       parent::__construct();
   }
    //根据记录获取卡密
    public function get_card_by_win($win_id){
        return M('win_record')->alias('w')
            ->field('c.*,w.id win_id')
            ->join('sp_goods g on w.goods_id=g.id')
            ->join('sp_card_category c on g.reward_data=c.id')
            ->where(['w.id'=>$win_id])->find();
    }
    //香肠币转换
    public function trans_xc($data,$win_id){
        $win_record=M('win_record')->field('address_data,logistics_status')->where(['id'=>$win_id])->find();
        if(!empty($win_record['address_data'])){
            return false;exit();
        }
        if($win_record['logistics_status']==1){
            $this->startTrans();
            !empty($win_id)&&$rt=M('win_record')->save(['id'=>$win_id,'address_data'=>json_encode($data),'reward_type'=>'-1','logistics_status'=>5]);
            !empty($data['uid'])&&$res=M('users')->where(['id'=>$data['uid']])->setInc('money',$data['money']);
            if($res&&$rt){
                $this->commit();
                return true;
            }else{
                $this->rollback();
                return false;
            }
        }else{
            return false;
        }

    }
    //充值话费 返回数据
    public function charge_records($win_id){
        $win_record=M('win_record')->where(['id'=>$win_id])->getField('address_data');
        if(!empty($win_record)){
            return false;exit();
        }
        $info=M('win_record')->alias('w')
            ->field('w.nper_id,w.luck_uid,g.reward_data')
            ->join('sp_goods g on g.id=w.goods_id')
            ->where(['w.id'=>$win_id])->find();
        $card_category=$this->where(['id'=>$info['reward_data']])->find();
        //获取卡密列表
        $card_list=M('card_list')->where(['category'=>$info['reward_data'],'used'=>0])->limit($card_category['number'])->select();
        if(count($card_list)<$card_category['number']){
            return false;exit();
        }
        $json=[];
        $ids=[];
        foreach($card_list as $k=>$v){
            $json[]=[
                'id'=>$v['id'],
                'num'=>$v['num'],
                'sec'=>$v['sec']
            ];
            $ids[]=$v['id'];
        }
        $this->startTrans();
        !empty($ids)&&$r1=M('card_list')->where(['id'=>['in',$ids]])->save(['uid'=>get_user_id(),'nper'=>$info['nper_id'],'used'=>1]);
        !empty($win_id)&&$r2=M('win_record')->where(['id'=>$win_id])->save(['address_data'=>json_encode($json),'logistics_status'=>5,'reward_type'=>1]);
        if($r1&&$r2){
            $this->commit();
            return ['data'=>$card_list,'title'=>$card_category['title'],'money'=>intval($card_category['money']/$card_category['number'])];
        }else{
            $this->rollback();
            return false;
        }

    }
}