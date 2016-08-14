<?php
namespace app\admin\model;

Class RtModel extends BaseModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('users',null);
    }

    //获取RT列表
    public function get_users_list($post)
    {
        $m = M('users', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_users
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        foreach($info as $key=>$value) {
            $info[$key]['user_face'] = $this->get_one_image_src($value['user_pic']);
        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }
    //获取RT用户
    public function rand_users($num){
        $m_user = M('users');
        $result = $m_user->field('id')->where(['type'=>-1])->order('rand()')->limit($num)->select();
        return $result;
    }

    //获取RT详情
    public function get_info_by_id($id)
    {
        $user_info = $this->m->where(array("id" => $id))->find();
        $user_info['user_face'] = $this->get_one_image_src($user_info['user_pic']);

        return $user_info;
    }

    //添加RT
    public function update_users($post)
    {
        $status = $type = $username = $phone = $nick_name = $user_pic = $origin = $age = $sex = $money = $score = $email = $real_name = $qq = $ip_area = $reg_ip = $zip_code = $update_time = $create_time= null;
        extract($post);
        //如果全部正确,执行写入数据
        $data = array(
            "id" => !empty($id) ? $id : "",
            "status" => $status,
            "type" => $type,
            "username" => $username,
            "phone" => $phone,
            "nick_name" => $nick_name,
            "origin" => $origin,
            "age" => $age,
            "sex" => $sex,
            "money" => $money,
            "score" => $score,
            "email" => $email,
            'user_pic' => $user_pic,
            "qq" => $qq,
            'zip_code' => $zip_code,
            "real_name" => $real_name,
            "ip_area" => $ip_area,
            "reg_ip" => $reg_ip,
            "update_time" => $update_time,
            "create_time" => $create_time,

        );
        if (!empty($id) && is_numeric($id)) {
            $r = $this->m->where(array("id" => $id))->save($data);
            return $r !== false;
        } else {
            return $this->m->add($data);
        }
    }

    //删除RT
    public function del_users($id)
    {
        return $this->m->where(array("id" => $id))->save(array("status" => "-1"));
    }

    public function check_exist($username,$phone){
        $sql = 'SELECT * FROM sp_users WHERE username ="'.$username.'" OR phone ="'.$phone.'"';
        $info = $this->m->query($sql);
        return ;
    }

    //获取日志列表
    public function get_log_list($post)
    {
        $m = M('rt_task', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  t.*,g.name goods_name,u.username,u.nick_name
        FROM  sp_rt_task t
        LEFT JOIN sp_goods g ON g.id =t.gid
        LEFT JOIN sp_users u ON u.id =t.uid
        WHERE  t.id>0 " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }
    //获取预设列表
    public function presets_list($post){

        $m = M();
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_rt_presets
        WHERE  1=1 " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    
    public function add_task($data,$multi=true){
        $m_task = M('task','sp_rt_');
        if($multi){
            return $m_task->addAll($data);
        }else{
            return $m_task->data($data)->add();
        }
    }
    //添加图片
    public function add_pic($data){
        $m =M('image_list','sp_');
        return $m->add($data);
    }
    /**
     * 拉取任务
     */
    public function query_task($interval,$time=null,$stat=0){
        $m = M('rt_task', 'sp_');
        if($time==null)$time = time();
        $where = ['stat'=>$stat,'time'=>[['egt',$time],['lt',$time+$interval]]];
        $result = $m->where($where)->order('time asc')->select();
        //var_dump($this->m_model->getLastSql());
        return [$result,$m->getLastSql()];
    }
    /*
     * 清理任务
     */
    public function clear_task($gid=null,$time=null,$stat=null){
        $m = M('rt_task', 'sp_');
        $where=[];
        if($time===false){
        }elseif($time===null){
            $time = time();
            $where['time']=['elt',$time];
        }else{
            $where['time']=['elt',$time];
        }
        if($gid!==null)$where['gid']=$gid;
        if($stat!==null)$where['stat']=$stat;
        if(empty($where)){
            $where='1';
        }
        $result = $m->where($where)->delete();
        return $result;
    }
    //添加用户
    public function add_user($post){
        $username=$password=$nick_name=$user_pic=$email=$qq=$age=$sex=$origin=$reg_ip=$ip_area=$zip_code=$user_group=$receive_sms=$birthday=$type=null;
        extract($post);
        $data = array(
            'username'=>$username,
            'password'=>$password,
            'nick_name'=>$nick_name,
            'user_pic'=>$user_pic,
            'email'=>$email,
            'qq'=>$qq,
            'age'=>$age,
            'sex'=>$sex,
            'origin'=>$origin,
            'reg_ip'=>$reg_ip,
            'ip_area'=>$ip_area,
            'zip_code'=>$zip_code,
            'user_group'=>$user_group,
            'receive_sms'=>$receive_sms,
            'birthday'=>$birthday,
            'type'=>$type,
        );
        $m =M('users','sp_');
        return $m->add($data);
    }
    public function update_task($id,$stat=0,$msg=null,$nper_id=null){
        $m = M('rt_task', 'sp_');
        $result = $m->where(['id'=>$id])->data(['stat'=>$stat,'msg'=>$msg,'nid'=>$nper_id])->save();
        return $result;
    }

    //获取所给商品ID下正在开奖的期数
    public function get_nper($id,$nper_type) {
        $m_nper = M('nper_list');
        $where = array(
            'pid' => $id,
            'status' =>1,
            'nper_type' => $nper_type
        );
        return $m_nper->where($where)->find();
    }

    /**
     * 验证用户名输入是否正确
     * @param $id
     * @return mixed
     */
    public function verify_username($id) {
        $m_user = M('users');
        return $m_user->where(array('username'=>$id))->find();


    }

    /**
     * 验证期数ID是否输入正确
     * @param $id
     * @return mixed
     */
    public function verify_nper_id($id,$type) {
        $m_nper = M('nper_list');
        $nper = $m_nper->where(array('id'=>$id))->find();
        if ( $nper ) {
            if ($nper['status'] != 1) {
                return -1;
            } else if (!empty($nper['rt_point_uid']) && $type == 'add') {
                return -2;
            } else {
                return true;
            }

        } else {
            return -3;
        }
        return $nper;

    }

    /**
     * 指定中奖用户入库
     * @param $id
     * @param $username
     * @return bool
     * @throws \think\Exception
     */
    public function savedata($id,$post) {
        $m_nper = M('nper_list');
        $m_user = M('users');
        $username = $post['username'];
        $user = $m_user->where(array('username'=>$username))->find();
        if ( !$user ) {
            return false;
        }
        $data = array(
            'category' => $post['category'],
            'rt_point_uid' => $user['id'],
            'rt_point_type' => $post['rt_point_type']
        );
        return $m_nper->where(array('id'=>$id))->save($data);



    }

    public  function get_set_user_info($id){
        $m_nper = M('nper_list');
        $nper = $m_nper->where(array('id'=>$id))->find();
        $m_user = M('users');
        $user = $m_user->where(array('id' => $nper['rt_point_uid']))->find();
        $nper['rt_point_uid'] = $user['username'];
        return $nper;
    }

    public function del_select($ids) {
        return $this->m->where(array('id'=>['in',$ids]))->save(array('status'=>-1));

    }

    //获取购买期数的用户列表
    public function get_users_list_by_nper($nper_id){
        $m_nper = M('luck_num','sp_');
        $query = "SELECT
l.id,
sum(l.num) num,
l.join_type,
	u.id uid,
	u.username,
	u.nick_name,
	u.type,
GROUP_CONCAT(l.code_list) code
FROM
	sp_luck_num l
LEFT JOIN sp_users u ON  l.uid = u.id
WHERE
 l.nper_id = {$nper_id}
GROUP BY
 l.uid,l.join_type
ORDER BY
	l.id DESC";
        return $m_nper->query($query);
    }

}