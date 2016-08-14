<?php
namespace app\core\model;

use think\model\Adv;
use app\core\model\LogModel as core_log;

Class UserModel extends Adv
{
    private $core_log;
    private $m_user;

    public function __construct()
    {
        parent::__construct();
        $this->core_log = new core_log();
        $m_auth = new AuthModel();
        $m_auth->init();
        $this->m_user = M('users', 'sp_');
    }

    //根据字段获取用户信息
    public function get_user_info_by_filed($filed, $value)
    {
        $sql = 'SELECT u.*,i.img_path,i.thumb_path,i.name image_name FROM sp_users u
                LEFT JOIN sp_image_list i ON u.user_pic = i.id
                WHERE u.' . $filed . ' = "' . $value . '"';
        $info = $this->m_user->query($sql);
        if (!empty($info[0]))
            return $info[0];
        return false;
    }


    //获取用户非关键信息
    public function get_user_simple_info_by_id($id)
    {
        return $this->m_user->where(array('id' => $id))->field(array("password", "create_time", "update_time", "is_test", "is_pay", "last_login_ip", "promoter", "origin", "type"), true)->find();
    }

    //新建用户
    public function add_user($post)
    {
        $username = $nick_name = $password = $origin = $phone = $promoter = $user_pic =$unionid= null;
        extract($post);
        $data = array(
            "username" => $username,
            "nick_name" => $nick_name,
            "password" => $password,
            "origin" => $origin,
            "status" => 1,
            "money" => 0,
            "score" => 0,
            "phone" => $phone,
            "reg_ip" => get_client_ip(),
            "promoter" => $promoter,
            "create_time" => NOW_TIME,
            "user_pic" => empty($user_pic)?intval(C('WEBSITE_AVATAR_DEF')):intval($user_pic),
            "unionid" => $unionid
        );
        return $this->m_user->add($data);
    }

    //根据字段值保存用户内容
    public function save_user_info_by_filed_value($filed, $value, $data)
    {
        return $this->m_user->where(array($filed => $value))->save($data);
    }

    //根据字段值保存当前登录用户内容
    public function save_login_user_info_by_filed_value($data)
    {
        return $this->m_user->where(array('id' => get_user_id()))->save($data);
    }

    //根据条件查询用户信息
    public function get_user_info_by_condition($conditions)
    {
        return $this->m_user->where($conditions)->find();
    }

    //获取登录用户信息
    public function get_login_user_info()
    {
        return $this->m_user->where(array("id" => get_user_id()))->find();
    }

    //获取登录用户详细信息
    public function get_login_user_info_detail()
    {
        $sql = 'SELECT u.*,i.img_path,i.thumb_path,i.name image_name FROM sp_users u
                LEFT JOIN sp_image_list i ON u.user_pic = i.id
                WHERE u.id=' . get_user_id();
        $info = $this->m_user->query($sql);
        if (!empty($info[0]))
            return $info[0];
        return false;
    }

    //记录最后用户的登录ip
    public function record_last_ip(){
        if(get_user_id()){
            return $this->m_user->where(array("id"=>get_user_id()))->save(array("last_login_ip"=>get_client_ip()));
        }
        return false;
    }
    //记录最后用户的登录时间
    public function record_last_time(){
        if(get_user_id()){
            return $this->m_user->where(array("id"=>get_user_id()))->save(array("last_login_time"=>time()));
        }
        return false;
    }
    //更新用户信息
    public function update_info_by_id($id,$data){
        return $this->m_user->where(['id'=>$id])->save($data);
    }
    //
    public function check_extract_user($data){
        if(empty($data['uid'])){
            $data['uid']=get_user_id();
        }
        $where=[
            'id'=>$data['uid'],
            'password'=>md6($data['password'])
        ];
        return $this->m_user->where($where)->find();
    }

    //验证用户名密码是否正确
    public function check_password($uid,$pass){
        $password = $this->m_user->where(array("id = $uid"))->find();
        if($password!==md6($pass)||$password!==md5($pass)){
            return false;
        }else{
            return true;
        }
    }

    //获取当前用户字段信息
    public function get_field_by_uid($field='*') {
        return $this->m_user->where(array('id'=>get_user_id()))->field($field)->find();

    }
    //获取余额
    public function get_leave_cash(){
        return $this->m_user->where(['id'=>get_user_id()])->getField('cash');
    }
    //api获取用户余额
    public function api_leave_cash($uid){
        return $this->m_user->where(['id'=>$uid])->getField('cash');
    }
    //现金转香肠
    public function get_money_by_cash($money){
        $pre_data = M("users")->where(array("id"=>get_user_id()))->find();
        M('log','')->add(['user'=>get_user_id(),'log'=>'现金转香肠币转换金额'.floatval($money),'create_time'=>time()]);
        $this->startTrans();
        $r1 = $this->m_user->where(['id'=>get_user_id()])->where('cash >='.floatval($money))->setInc('money',floatval($money));
        $r2 = $this->m_user->where(['id'=>get_user_id()])->where('cash >='.floatval($money))->setDec('cash',floatval($money));
        if($r1&&$r2){
            $this->commit();
            $after_data = M("users")->where(array("id"=>get_user_id()))->find();
            $this->core_log->record($pre_data,$after_data,6,1);
            return true;
        }
        else{
            $this->rollback();
            $after_data = M("users")->where(array("id"=>get_user_id()))->find();
            $this->core_log->record($pre_data,$after_data,6,0);
            return false;
        }

    }
    //API现金转香肠
    public function api_money_by_cash($money,$uid){
        $pre_data = M("users")->where(array("id"=>$uid))->find();
        M('log','')->add(['user'=>$uid,'log'=>'api现金转香肠币：'.floatval($money),'create_time'=>time()]);
        $this->startTrans();
        $r1 = $this->m_user->where(['id'=>$uid])->where('cash >='.floatval($money))->setInc('money',floatval($money));
        $r2 = $this->m_user->where(['id'=>$uid])->where('cash >='.floatval($money))->setDec('cash',floatval($money));
        if($r1&&$r2){
            $this->commit();
            $after_data = M("users")->where(array("id"=>$uid))->find();
            $this->core_log->record($pre_data,$after_data,6,1);

            return true;
        }
        else{
            $this->rollback();
            $after_data = M("users")->where(array("id"=>$uid))->find();
            $this->core_log->record($pre_data,$after_data,6,0);
            return false;
        }
    }
    //获取API用户信息
    public function get_user_info($uid){
        $sql = 'SELECT u.nick_name,u.cash,u.id,i.img_path,i.thumb_path,i.name image_name,phone FROM sp_users u
                LEFT JOIN sp_image_list i ON u.user_pic = i.id
                WHERE u.id=' . $uid;
        $info = $this->m_user->query($sql);
        if (!empty($info[0]))
            return $info[0];
        return false;
    }

    //获得用户头像
    public function get_headshot() {
        return $this->m_user->alias('u')
            ->field('i.img_path')
            ->join('image_list i','i.id=u.user_pic')
            ->where(array("u.id" => get_user_id()))->find();

    }

    //积分转虚拟币
    public function transensure($score,$password,$front_res_money) {

        $fields = $this->get_field_by_uid('score,password,money,cash,id,username');
        $u_score = $fields['score'];
        $u_password = $fields['password'];
        $code = '';
        //code -103 积分输入错误
        $score > $u_score &&  $code=-103;
        //code -104 密码输入错误
        $password != $u_password && $code = -104;

        //获得系统配置的积分和虚拟币
        $c_score = (int) $this->get_conf('MARKETING_SCORE');
        $c_money = (int) $this->get_conf('MARKETING_MONEY');

        $res_money = $score / $c_score * $c_money;
        //计算兑换结果
        $res_money = round($res_money,2);

        //code -105 校验前后台计算结果是否一致
        ($res_money != $front_res_money) && $code = -105;
        if ( !empty($code) )  {
            return $code;
        }

        $res_money += + (float) $fields['money'];

        $res_score = (int) $u_score - (int) $score;
        M('log','')->add(['user'=>get_user_id(),'log'=>'积分转虚拟币之前用户积分：'.$u_score.'用户余额：'.$fields['money'].'之后用户积分：'.$res_score.'用户余额：'.$res_money,'create_time'=>time()]);

        $res = $this->m_user->where(array('id'=>get_user_id()))->save(array('score' =>$res_score,'money'=>$res_money));
        $after_data = $this->get_field_by_uid('score,money,cash');
        $this->core_log->record($fields,$after_data,3,1);
        return $res;



    }

    private function get_conf($field) {
        return M('conf')->where(array('name'=>$field))->field('value')->find()['value'];

    }
}