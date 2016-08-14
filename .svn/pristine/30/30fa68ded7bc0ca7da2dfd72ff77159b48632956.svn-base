<?php
namespace app\admin\controller;


use app\admin\model\CategoryModel;
use app\admin\model\CommonModel;
use app\admin\model\NperListModel;
use app\admin\model\PromotModel;
use app\admin\model\RtModel;
use app\admin\model\UserModel;
use app\lib\Condition;
use app\lib\iHttp;
use app\lib\Tree;
use app\yyg\controller\User;
use think\Controller;
use app\lib\Page;

Class Rt extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->del_model = new RtModel();
    }

    //首页
    public function user()
    {
        return $this->fetch();
    }


    //用户列表
    public function show_list()
    {
        $post = I('post.');
        $field = $post['field'];
        switch ($field) {
            case 1:
                $field = 'id';
                $operator = '=';
                break;
            case 2:
                $field = 'username';
                $operator = 'LIKE';
                break;
            case 3:
                $field = 'nick_name';
                $operator = 'LIKE';
                break;
        }
        //获取列表
        $condition_rule = array(
            array(
                'field' => $field,
                'operator' => $operator,
                'value' => I('post.keywords')
            ),
            array(
                'field' => 'type',
                'value' => -1
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m = new RtModel();
        $users_list = $m->get_users_list($model);
        /*生成分页html*/
        $my_page = new Page($users_list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('users_list', $users_list['data']);
        return $this->fetch();
    }

    //查看/编辑/新增
    public function exec()
    {
        $m = new RtModel();
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
        return $this->fetch('form');
    }

    //删除用户
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new RtModel();
        $m->del_users($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }

    //执行添加用户
    public function update()
    {
        $username = $phone = null;
        $post = I("post.");
        extract($post);
        !empty($id) && !is_numeric($id) && wrong_return('id格式不正确');
        $m = new RtModel();


        //检测用户是否已存在
        $r = $m->check_exist($username, $phone);
        if ($r) wrong_return('用户已存在');

        $rt = $m->update_users($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //预设
    public function presets()
    {
        return $this->fetch();
    }


    //机器人购买日志
    public function rt_log()
    {
        return $this->fetch();
    }

    //机器人购买日志
    public function rt_log_list()
    {
        //获取列表
        $condition_rule = array();

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m = new RtModel();
        $list = $m->get_log_list($model);
        /*生成分页html*/
        $my_page = new Page($list["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('list', $list['data']);
        return $this->fetch();
    }


    //删除之前的全部日志
    public function del_pre_log()
    {

    }

    //预设列表
    public function presets_list()
    {
        //获取列表
        $condition_rule = array();
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $users = new RtModel();
        $presets_list = $users->presets_list($model);
        /*生成分页html*/
        $my_page = new Page($presets_list["count"], $this->page_num, $this->page, U('presets_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('presets_list', $presets_list['data']);
        return $this->fetch();
    }


    //通过接口获取机器人信息写入数据库,下载图片
    public function api_user_url()
    {
        $num = I('request.num', 1);
        $m = new CommonModel();
        $m_rt =new RtModel();
        //接口地址
        $url = $m->get_conf('RT_GET_USER_API');
        //最后一个用户的id
        $last_id = $m->get_conf('RT_ID_LAST_SITE');
        //最后一个用户的id
        $rt_img_root= $m->get_conf('RT_HEAD_SAVE_ROOT');
        $rt_img_root = empty($rt_img_root) ? 'data':$rt_img_root;
        //获取返回值列表
        $url = $url . '?num=' . $num . '&start=' . $last_id;
        $r = curl_get($url);
        $list = json_decode($r, true);

        $max_id = 0;

        $i = 0;
        foreach ($list as $k => $v) {
            $i++;
            $url = $v['img_path'];
            $r = file_get_contents($url);
            $path = './'.$rt_img_root.'/img/rt/'.date('ymdhi').'/';
            if(!is_dir($path)){
                mkdir($path,777,true);
                chmod($path,0777);
            }
            //图片本地地址
            $name = microtime_float();
            $img_path = $path.$name.'.png';
            file_put_contents($img_path,$r);
            $img_path= trim($img_path,'.');
            $data = array(
                'name'=>$name,
                'uid'=>0,
                'status'=>'1',
                'origin'=>'head',
                'img_path'=>$img_path,
                'create_time'=>NOW_TIME
            );
            $id=$m_rt->add_pic($data);
            $max_id = $id;
            if($id){
                $mail_arr =array(
                    'souhu.com',
                    '163.com',
                    '126.com',
                    'year.net',
                    'aliyun.com',
                    'foxmail.com',
                    'sina.com',
                    'gmail.com',
                    'hotmail.com',
                    'ask.com',
                    'msn.com',
                    'tom.com',
                    'vip.qq.com',
                );
                $mail = array_rand($mail_arr,1);
                $mail = $mail_arr[$mail];
                $data = array(
                    'username'=>rand_str('r',10).rand(1,9999),
                    'password'=>md6($v['username'].$v['ip']),
                    'nick_name'=>$v['username'],
                    'user_pic'=>$id,
                    'email'=>rand(100086,999999999).'@'.$mail,
                    'qq'=>rand(100086,999999999),
                    'age'=>rand(18,50),
                    'sex'=>($i%2==1)?'man':'woman',
                    'origin'=>'rt',
                    'reg_ip'=>$v['ip'],
                    'ip_area'=>$v['area'],
                    'zip_code'=>rand(100001,888888),
                    'user_group'=>'0',
                    'receive_sms'=>'false',
                    'birthday'=>date('Y-m-d',rand(473356800,1451577600)),
                    'type'=>'-1',
                );
                $m_rt->add_user($data);
            }
        }
        //保存最后的id到配置表
        $m->set_conf('RT_ID_LAST_SITE',$max_id);

        //完成
        ok_return('设置成功!');
    }

    /**
     * 指定用户中奖
     */
    public function set_win_user() {
        return $this->fetch();
    }

    public function set_user_list() {
        $keywords = null;
        $post = remove_arr_xss(I("post.", []));
        extract($post);
        $type ='';
        switch ($post['field'])
        {
            case 1:
                $field = 'n.id';
                break;
            case 2:
                $field = 'u.username';
                $type = 'LIKE';
                break;
        }
        //获取列表
        $condition_rule = array(
            array(
                'field' => $field,
                'operator' => $type,   //关系符号
                'value' => $keywords
            ),
        );

        $nper = new NperListModel();

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        $info = $nper->get_set_user_nper($model);
        $list = $info['list'];
        $this->assign('list',$list);

        /*生成分页html*/
        $my_page = new Page($info["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        return $this->fetch();
    }

    /**
     * 指定用户中奖的表单页面
     * @return mixed
     */
    public function set_user_exec() {
        $m = new RtModel();
        $p = new PromotModel();
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);
        //获取商品分类
        $m_cat = new CategoryModel();
        $cat_list = $m_cat->get_list_by_code_mid('xiangchang');
        $tree = new Tree();
        $cat_list = $tree->toFormatTree($cat_list, 'name');
        $this->assign('cat_list', $cat_list);
        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $m->get_set_user_info($id);
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
        return $this->fetch('set_user_form');

    }

    /**
     * 获得所选商品未开奖期数ID
     */
    public function get_nper() {
        $id = I('post.id',null);
        $nper_type = I('post.nper_type',1,'trim,intval');
        is_null($id) && wrong_return('商品不能为空');
        $m = new RtModel();
        $nper = $m->get_nper($id,$nper_type);
        !$nper && wrong_return('该商品中,没有未开奖的'.($nper_type==2?'零元抢宝':'一元夺宝').'期数');
        $users = $m->get_users_list_by_nper($nper['id']);
        if($nper_type==2){
            foreach($users as &$user){
                $code = $user['code'];
                $code = trim($code,',');
                $code = str_replace(',,,',',',$code);
                $code_list = explode(',',$code);
                $user['has_odd'] = !empty(array_filter($code_list,'is_odd'));
                $user['has_even'] = !empty(array_filter($code_list,'is_even'));
                unset($user['code']);
            }
        }
        $data = array(
            'code' => 1,
            'id' => $nper['id'],
            'users' => $users
        );
        die_json($data);

    }

    public function set_user_update() {
        $post = I("post.", []);
        extract($post);
        $m = new RtModel();
        $nper_id = $post['nper_id'];
        $username = $post['username'];
        //验证用户名期数是否为空
        empty($nper_id) && wrong_return('期数不能为空');
        empty($username) && wrong_return('用户名不能为空');
        //验证期数id是否正确
        $nper_res = $m->verify_nper_id($nper_id,$post['type']);
        if($nper_res === -1) {
            wrong_return('该期已开奖,无法添加');
        }
        if ($nper_res === -2) {
            wrong_return('该期已有指定中奖用户,无法添加');
        }
        if ( $nper_res === -3 ) {
            wrong_return('期数不正确,无法添加');
        }
        //验证用户输入是否正确
        !$m->verify_username($username) && wrong_return('用户名输入错误');
        $m->savedata($nper_id,$post) !== false && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    /**
     * 获取指定中奖用户
     */
    public function get_user() {
        //获取指定中奖用户
        $username = $_POST['query'];
        if ( empty($username) ) {
            die_json(array());
        }
        $m_user = new UserModel();
        $users = $m_user->get_set_user($username);
        die_json($users);
    }

    /**
     * 删除指定中奖的用户
     */
    public function set_user_del() {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new NperListModel();
        $m->del_set_user($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');

    }

}