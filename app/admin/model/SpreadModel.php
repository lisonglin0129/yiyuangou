<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/21
 * Time: 16:07
 */
namespace app\admin\model;
class SpreadModel extends BaseModel{
    private $m;
    public function __construct(){
        $this->m=M('spread','sp_');
    }
    //注册推广更新
    public function reg_update($post){
        $id=$money=$status=null;
        extract($post);
        $data=[
            'status'=>$status,
            'money'=>empty($money)?0:$money,
            'type'=>1
        ];
        if(empty($id)){
            return $this->m->add($data);
        }else{
            return $this->m->where(['id'=>$id])->save($data);
        }
    }
    //分销推广更新
    public function dis_update($post){
        $id=$status=$level=$reward_per=$score_reward=null;
        extract($post);
        $data=[
            'status'=>$status,
            'level'=>$level,
            'reward_per'=>$reward_per,
            'score_reward'=>$score_reward,
            'type'=>2
        ];
        if(empty($id)){
            return $this->m->add($data);
        }else{
            return $this->m->where(['id'=>$id])->save($data);
        }
    }
    //获取推广信息
    public function  get_spread_by_type($type){
        return $this->m->where(['type'=>$type])->find();
    }
}
