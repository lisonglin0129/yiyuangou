<?php
namespace app\admin\controller;


use app\admin\model\AdminRoleModel;
use app\admin\model\UsersModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;
use think\Exception;

Class Users extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->del_model = new UsersModel();
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }


    //用户列表
    public function show_list()
    {
        //获取列表
        $field = I('post.field');
        $operator = '=';
        switch ($field) {
            case 1 :
                $field='id';
                break;
            case 2 :
                $field='nick_name';
                $operator = 'like';
                break;
            case 3 :
                $field='phone';
                break;
            case 4 :
                $field='username';
                $operator = 'like';
                break;

        }
        $condition_rule = array(
            array(
                'field' => $field,
                'operator' => $operator,
                'value' => I('post.keywords')
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m = new UsersModel();
        $users_list = $m->get_users_list($model);
        /*生成分页html*/
        $my_page = new Page($users_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('users_list', $users_list['data']);
        return $this->fetch();
    }
    //商家列表
    public function shop_list(){
        //获取列表
        return $this->display();
    }
    public function shop_users_list(){
        $time=I('post.time');
        $condition_rule=[];
        if(!empty($time)){
            $condition_rule = array(
                array(
                    'field' => 'create_time',
                    'operator'=>'>=',
                    'value' => strtotime(date('Ymd'))
                ),array(
                    'field' => 'create_time',
                    'operator'=>'<=',
                    'value' => strtotime(date('Ymd')) +86400
                )
            );
        }
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m = new UsersModel();
        $users_list = $m->get_shop_list($model);
        /*生成分页html*/
        $my_page = new Page($users_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('users_list', $users_list['data']);
        $this->assign('total',$users_list['total']);
        $this->assign('charge',$users_list['charge']);
        $this->assign('sale',$users_list['sale']);
        return $this->fetch();
    }

    //查看/编辑/新增
    public function exec()
    {

        $m = new UsersModel();
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);

        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $m->get_info_by_id($id);
            empty($info) && die('获取信息失败');
            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
            } else {
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }

        $this->assign('user_type',cookie('user_type'));
        return $this->fetch('form');
    }

    //删除用户
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new UsersModel();
        $m->del_users($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }

    //执行添加用户
    public function update()
    {
        $username=$phone=null;
        $post = I("post.");
        $user_type=cookie('user_type');
        if($user_type==2){
          $post['shop_id']=get_user_id();
        }
        extract($post);
        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        $m = new UsersModel();

        //检测用户是否已存在
        if(empty($id)) {
            $r = $m->check_exist($username, $phone);
            if($r)wrong_return('用户已存在');
        }


        $rt = $m->update_users($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //修改密码
    public function modify_password() {
        $id = I('post.id');
        $password = I('post.password');
        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        empty($password) && wrong_return('密码不可为空');

        $users = M('users');
        $password = md6($password);
        $res = $users->where(array("id"=>$id))->setField('password',$password);
        if($res === false) {
            wrong_return('修改失败');
        }else{
            ok_return('修改成功');
        }

    }

    public function admin_list() {
        return $this->fetch();
    }

    public function admin_show_list() {
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'username',
                'value' => I('post.keywords')
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m = new UsersModel();
        $users_list = $m->get_admin_users_list($model);
        /*生成分页html*/
        $my_page = new Page($users_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('users_list', $users_list['data']);
        return $this->fetch();

    }


    //查看/编辑/新增
    public function admin_exec()
    {
        $m = new UsersModel();
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);

        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $m->get_info_by_id($id);
            empty($info) && die('获取信息失败');
            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
            } else {
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }

        //角色列表
        $admin_role = new AdminRoleModel();
        //所有角色列表
        $all_role_list = $admin_role->get_admin_role_list();


        $role_list_arr = array();
        if(isset($info) && !empty($info)) {
            $role_list_arr = explode(',',$info['role_list']);
        }
        if(!empty($all_role_list)) {
            foreach($all_role_list as $key=>$value) {
                if(in_array($value['id'],$role_list_arr)) {
                    $all_role_list[$key]['checked'] = true;
                }

                $all_role_list[$key]['open'] = true;
                $all_role_list[$key]['pId'] = $value['pid'];
            }
        }

        $this->assign('all_role_list',json_encode($all_role_list));

        return $this->fetch('admin_form');
    }


    //执行添加管理员用户
    public function admin_update()
    {
        $username=$phone=null;
        $post = I("post.");
        extract($post);
        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        $m = new UsersModel();

        //检测用户是否已存在
        if(empty($id)) {
            $r = $m->check_exist($username, $phone);
            if(!empty($r))wrong_return('用户已存在');
        }

        $rt = $m->update_users($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //导入界面
    public function csv_import(){
        return $this->fetch();
    }

    public function csv_import_do(){
        $col_username = I('post.col_username',0,'trim,intval');
        $col_passwd = I('post.col_passwd',0,'trim,intval');
        $col_nick = I('post.col_nick',0,'trim,intval');
        $col_phone = I('post.col_phone',0,'trim,intval');
        $charset = I('post.charset',0,'trim,intval');
        if($col_username<=0 || $col_passwd <=0){
            return $this->error('用户名和密码的所在列数为必填');
        }

        $filename = $_FILES['file']['tmp_name'];
        if (empty ($filename)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }
        $handle = fopen($filename, 'r');
        try{
            $data = $this->input_csv($handle);
        }catch(Exception $ex){
            echo '解析CSV失败，请检查格式；<hr/>'.$ex->getMessage();
            exit;
        }
        $c = count($data);
        if($c==0){
            echo '没有任何数据';
            exit;
        }

        try{
            $m = new UsersModel();
            for($i=0;$i<$c;$i+=100){
                $frag100 = [];
                for($j=$i;$j<$i+100&&$j<$c;$j++){
                    $new_user= [
                        'username'=> $data[$j][$col_username-1],
                        'password'=> md6($data[$j][$col_passwd-1]),
                        'create_time'=>time(),
                        'origin'=>'import'
                        ];
                    if($col_nick>0){
                        $nick_name = $data[$j][$col_nick-1];
                        $new_user['nick_name'] = $charset==1?iconv('gb2312','utf-8',$nick_name):$nick_name;
                    }
                    if($col_phone>0)$new_user['phone'] = $data[$j][$col_phone-1];
                    array_push($frag100,$new_user);
                }
                $m->import_users($frag100);
            }
        }catch(Exception $ex){
            echo '导入失败；<hr/>'.$ex->getMessage();
            exit;
        }
        echo '导入完成.';
    }
    private function input_csv($handle) {
        $out = array ();
        $n = 0;
        while ($data = fgetcsv($handle)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
    }
}