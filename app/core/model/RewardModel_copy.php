<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/23
 * Time: 10:57
 */
namespace app\core\model;
class RewardModel {
    private $m,$start,$end;
    public function __construct(){
        $this->m=M('reward','sp_');
    }
    //获取最新推广记录5条
    public function get_last_rewards(){
        return $this->m->alias('r')->field('r.*,u.username')->join('sp_users u ON r.uid=u.id')->where(['spread_uid'=>get_user_id()])->order('r.create_time desc')->limit(6)->select();
    }
    //获取下级消费总金额
    public function get_total($post){
        return $this->m->where('spread_uid='.get_user_id().$post->wheresql)->sum('total');
    }
    //获取推广收益
    public function get_total_reward($post){
        return $this->m->where('spread_uid='.get_user_id().$post->wheresql)->sum('reward');
    }
    //按照用户级别
    public function get_rewards_group_by_level($post){

        $sql = "SELECT SQL_CALC_FOUND_ROWS count( DISTINCT uid) as level_count,sum(reward) as reward,level
        FROM  sp_reward
        WHERE  spread_uid=".get_user_id() . $post->wheresql .
            " GROUP BY level" . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    public function get_rewards_by_level($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS uid,sum(reward) as reward,level,sum(total) as total
        FROM  sp_reward
        WHERE  spread_uid=".get_user_id() . $post->wheresql .
            " GROUP BY uid " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);
        $u_model=M('users','sp_');
         foreach($info as $k=>$v){
             $info[$k]['username']=$u_model->where(['id'=>$v['uid']])->getField('username');
         }
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    public function get_reward_info($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
        FROM  sp_reward
        WHERE  spread_uid=".get_user_id() . $post->wheresql .
            " ORDER BY create_time DESC " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m->query($sql);
        $num = $this->m->query($sql_count);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //获取注册明细列表
    public function get_register_rewards_by_id($post){
        $r_model=M('reg_reward','sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.*,u.username
        FROM  sp_reg_reward r
        LEFT JOIN sp_users u ON u.id=r.uid
        WHERE  r.spread_uid=".get_user_id() . $post->wheresql .
            " ORDER BY create_time DESC " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $r_model->query($sql);
        $num = $r_model->query($sql_count);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //注册推广收益添加
    public function add_reg_reward($data){
        $r_model=M('reg_reward','sp_');
        return $r_model->add($data);
    }
    //手机端获取注册推广列表
    public function m_register_rewards($uid){
        $r_model=M('reg_reward','sp_');
        return $r_model->alias('r')->field('r.*,u.username')->join('sp_users u ON u.id=r.uid','left')->where(['r.spread_uid'=>$uid])->order('r.create_time DESC')->limit(0,10)->select();
    }
    //获取总页码
    public function m_register_pages($uid){
        $r_model=M('reg_reward','sp_');
        $total=$r_model->field('id')->where(['spread_uid'=>$uid])->count();
        return ceil($total/10);
    }
    //ajax获取注册列表
    public function m_ajax_register_detail($uid,$offset=0,$year='',$month=''){
        $this->time_init($year,$month);
        $r_model=M('reg_reward','sp_');
        $where=[];
        $where['r.spread_uid']=$uid;
        if($this->start&&$this->end){
            $where['r.create_time']=['between',[$this->start,$this->end]];
        }
        return $r_model->alias('r')->field('r.*,u.username')->join('sp_users u ON u.id=r.uid','left')->where($where)->order('r.create_time DESC')->limit($offset,10)->select();
    }

    public function m_promote_pages($level,$uid){
        $total=count($this->m->where(['spread_uid'=>$uid,'level'=>$level])->group('uid,month_time')->select());
        return ceil($total/10);
    }
    //
    public function m_ajax_promote_detail_list($uid,$offset=0,$year='',$month='',$level){
        $where=[];
        $where['spread_uid']=$uid;
        $where['level']=$level;
        if($year&&$month){
            $where['month_time']=strtotime($year.'-'.$month);
        }elseif($year&&!$month){
            $first=$year.'-01-01';
            $where['month_time']=['between',[strtotime($first),strtotime("$first +1 year -1 day")]];
        }
        $data['list']= $this->m->alias('r')->field('r.uid,sum(r.reward) reward,r.month_time,sum(r.total) as total,r.level,u.username')
            ->join('sp_users u on u.id=r.uid')->where($where)
            ->group('r.month_time,r.uid')->order('r.month_time desc')->limit($offset,10)->select();
        $total=$this->m->field('sum(total) as total,sum(reward) as reward')->where($where)->select();
        $data['total_fee']=$total[0]['total'];$data['total_reward']=$total[0]['reward'];
        return $data;

    }
    //获取分销推广详情
    public function m_promote_detail($level,$uid){
        $data['list']= $this->m->alias('r')
            ->field('r.uid,sum(r.reward) reward,r.month_time,sum(r.total) as total,r.level,u.username')
            ->join('sp_users u on u.id=r.uid')->where(['r.spread_uid'=>$uid,'r.level'=>$level])
            ->group('r.month_time,r.uid')->order('r.month_time desc')->limit(10)->select();
        $total=$this->m->field('sum(total) as total,sum(reward) as reward')->where(['spread_uid'=>$uid,'level'=>$level])->select();
        $data['total_fee']=$total[0]['total'];$data['total_reward']=$total[0]['reward'];
        return $data;
    }
    //获取分销推广汇总
    public function m_ajax_promote_detail($uid,$year='',$month=''){
        $where=[];
        $data=[];
        $where['spread_uid']=$uid;
        if($year&&$month){
            $where['month_time']=strtotime($year.'-'.$month);
        }elseif($year&&!$month){
            $first=$year.'-01-01';
            $where['month_time']=['between',[strtotime($first),strtotime("$first +1 year -1 day")]];
        }
        $data['list']=$this->m->field('count(DISTINCT uid) as level_count,level,sum(reward) as reward')->where($where)->group('level')->select();
        $data['total_users']=count($this->m->distinct(true)->field('uid')->where($where)->group('uid')->select());
        $data['total_reward']=$this->m->where($where)->sum('reward');
        return $data;

    }
    //时间初始化
    private function time_init($year,$month){
        if($year&&$month){
            $first=$year.'-'.$month.'-01';
            $this->start=strtotime($first);
            $this->end=strtotime("$first +1 month -1 day");
        }elseif($year&&!$month){
            $first=$year.'-01-01';
            $this->start=strtotime($first);
            $this->end=strtotime("$first +1 year -1 day");
        }
    }
}