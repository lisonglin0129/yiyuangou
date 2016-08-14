<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/30
 * Time: 10:28
 */
namespace app\admin\model;
use think\Db;
use think\Exception;

class RewardModel{
    private $m;
    public function __construct(){
        $this->m=M('reward');
    }
    //获取注册推广列表
    public function get_reg_list($post){
        $r_model=M('reg_reward');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  r.*,u1.username as reg_username,u2.username as spread_username,u1.id as reg_uid,u2.id as spread_uid
        FROM  sp_reg_reward r
        LEFT JOIN sp_users u1 ON r.uid=u1.id
        LEFT JOIN sp_users u2 ON r.spread_uid = u2.id
        WHERE r.status <>'-1' " . $post->wheresql .
            " ORDER BY r.id desc " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $r_model->query($sql);
        $num = $r_model->query($sql_count);
        $rt["data"] = $info;
        $rt['reward']= $r_model->alias('r')->join('sp_users u2 on u2.id=r.spread_uid')->where('r.status <> -1 '.$post->wheresql)->sum('r.reward');
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //获取分销推广列表
    public function get_promote_list($post){

        $sql = "SELECT SQL_CALC_FOUND_ROWS  r.*,u1.username as level_username,u2.username as spread_username
        FROM  sp_reward r
        LEFT JOIN sp_users u1 ON r.uid=u1.id
        LEFT JOIN sp_users u2 ON r.spread_uid = u2.id
        WHERE r.status <>'-1' " . $post->wheresql .
            " ORDER BY r.id desc " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);
        $rt["data"] = $info;
        $rewards=$this->m->alias('r')->field('sum(r.total) as total,sum(r.reward) as reward,sum(r.reward_score) as reward_score')->join('sp_users u2 on u2.id=r.spread_uid')->where('r.status <> -1 '.$post->wheresql)->select();
        $rt['total']=$rewards[0]['total'];
        $rt['reward']=$rewards[0]['reward'];
        $rt['reward_score']=$rewards[0]['reward_score'];
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //推广记录状态更新
    public function update_status($ids,$status,$type=1){
        //1是推广2是注册
        if($type==2){
            $this->m=M('reg_reward');
        }
        $u_model=M('users');
        if($status==1){
            $rewards=$this->m->where(['id'=>['in',$ids]])->select();
            try{
                Db::startTrans();
                foreach($rewards as $v){
                    if($v['status']!=1){
                        $user = $u_model->where(['id'=>$v['spread_uid']])->find();
                        if( empty($user['cash']) ) {
                            M('users')->where(['id'=>$v['spread_uid']])->setField('cash',$v['reward']);
                        } else {
                            $u_model->where(['id'=>$v['spread_uid']])->setInc('cash',$v['reward']);
                        }
                        if($type==1){
                            if( empty($user['score']) ) {
                                M('users')->where(['id'=>$v['spread_uid']])->setField('score',$v['reward_score']);
                            } else {
                                $u_model->where(['id'=>$v['spread_uid']])->setInc('score',$v['reward_score']);
                            }
                        }
                    }
                }
                Db::commit();
            }catch (Exception $e){
                Db:rollback();
            }
        }
      return $this->m->where(['id'=>['in',$ids]])->save(['status'=>$status,'op_time'=>time()]);
    }
}