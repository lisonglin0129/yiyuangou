<?php
namespace app\admin\controller;

use app\admin\model\ConfModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;
use think\Exception;

Class Config extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->chk_login();
    }

    private function chk_login()
    {
        //CLI模式下绕过
        if(IS_CLI){
            return true;
        }
        $allow_list = [
            'admin/rt_presets/run_task_1s',
            'admin/rt_presets/run_task_1min',
            'admin/rt_regular/auto_exec',
        ];
        $path = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        if ( in_array($path,$allow_list) ) {
            return true;
        }
//        if(strtolower(MODULE_NAME) == 'admin' && strtolower(CONTROLLER_NAME) == 'rt_presets'
//            || strtolower(MODULE_NAME) == 'admin' && strtolower(CONTROLLER_NAME) =='rt_regular' )
//        {
//            if(strtolower(ACTION_NAME) == 'run_task_1s'||strtolower(ACTION_NAME) == 'run_task_1min' || 'auto_exec' == strtolower(ACTION_NAME))
//            //本地访问此方法不需要登录
//            //if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
//                return true;
//            //}
//        }
        $user_type=cookie('user_type');
        $this->assign('user_type',!empty($user_type)?cookie('user_type'):'');
        if (!is_user_login() || login_group() != '1') {
            if((!empty($user_type))&&($user_type==2)){
                $this->redirect(U('account/shop_login'));
                die();
            }
            $this->redirect(U('account/index'));
            die();
        }
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }


    //配置列表
    public function show_list()
    {
        $conf = new ConfModel();
        $conf_list = $conf->get_conf_list();
        $this->assign('category_arr', $conf_list['category_arr']);
        $this->assign('category_info', $conf_list['category_info']);
        return $this->fetch();
    }

    /**
     * 修改配置
     */
    public function update_conf()
    {
        $conf = M('conf', null);
        $id = I('post.id');
        $value = I('post.value');


        if (empty($id) || empty($value)) {
            wrong_return('无效参数');
        }

        $res = $conf->where('id = ' . $id)->setField('value', $value);

        if ($res == false) {
            wrong_return('修改失败');
        } else {
            ok_return('修改成功');
        }
    }


    //配置文件同步
    public function sync()
    {
        set_time_limit(1000);
        $data = curl_get("http://auth.mengdie.com/data/config.json");
        $arr = json_decode($data,true);

        //dump($arr);die();
        !is_array($arr) && wrong_return('读取配置文件失败');
        $m = M('conf', 'sp_');
        foreach ($arr as $k => $v) {
            //查询名称是否存在
            $info = $m->where(array('name' => strtoupper($v['name'])))->find();
            //如果存在,更新非关键信息
            $arr = array(
                "admin_tpl" => $v['admin_tpl'],
                "title" => $v['title'],
                "category" => $v['category'],
                "name" => $v['name'],
                "editable" => $v['delable'],
                "delable" => $v['delable'],
                "type" => $v['type'],
                "exec_data" => $v['exec_data'],
                "remark" => $v['remark'],
                "sort" => $v['sort'],
                "is_default" => $v['is_default'],
                "is_must" => $v['is_must']
            );
            //增加
            if (empty($info) && $v['is_default']="true") {
                echo "<span style='color:#f36'>增加[{$v['name']}]成功!</span>";
                $m->add($arr);
                echo $info['name'];
            } //更新
            {
                echo "<span style='color:#226b0a'>更新[{$info['id']}][{$v['name']}]成功!</span>";
                $m->where(array('name' => $v['name']))->save($arr);
            }

            echo "ok;<br/>\n";
            flush();
            ob_flush();
        }
        echo '执行完毕!请返回上一页面！';
//        $this->redirect(U('config/get_config_info'));die();
    }


    public function export(){
        $m = M('conf', 'sp_');
        $conf = $m->where('name != "DEMO" ')->field(array('value'),true)->select();
        empty($conf) && die('获取json失败');

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=conf.dat");
        die( trim(json_encode($conf), "\xEF\xBB\xBF"));


    }

    public function preview_img_by_id($id){
        $m_img = M('image_list','sp_');
        $img_addr = $m_img->where(['id'=>$id])->getField('img_path');
        if(empty($img_addr)){
            return '错误的图片信息';
        }else{
            $this->redirect($img_addr);
        }
    }

    public function get_config_info()
    {
        $conf = new ConfModel();
        $validate = $conf->get_config_validate();
        $ali_fish = $conf->get_config_fish();
        $pay_arr = $conf->get_config_pay();
        $conf_list = $conf->get_other_data();

        $this->assign('validate',$validate);
        $this->assign('ali_fish',$ali_fish);
        $this->assign('pay_arr',$pay_arr);
        $this->assign('category_arr', $conf_list['category_arr']);
        $this->assign('category_info', $conf_list['category_info']);

        return $this->fetch();

    }
    //检测用户是否填完所有必填项
    public function check_conf(){
        $conf = new ConfModel();
        $data = $conf->check_must_info();
        foreach($data as $k=>$v){
            if(empty($v['value'])){
                $conf->hide_third_switch();
                wrong_return("您还有信息没有填写完整,请继续填写！");
            }
        }
        //第三方登陆开关
        $conf->show_third_switch();
        //支付宝开关
        $ali_pay = $conf->check_ali_pay();
        foreach($ali_pay as $k=>$v){
            if(empty($v['value'])){
                $conf->hide_ali_pay();
            }
        }
        $conf->show_ali_pay();
        //爱呗开关
        $aibei_pay = $conf->check_aibei_pay();
        foreach($aibei_pay as $k=>$v){
            if(empty($v['value'])){
                $conf->hide_aibei_pay();
            }
        }
        $conf->show_aibei_pay();
        //微信开关
        $w_pay = $conf->check_w_pay();
        foreach($w_pay as $k=>$v){
            if(empty($v['value'])){
                $conf->hidew_pay();
            }
        }
        $conf->show_w_pay();

        $path = './data/key/conf.lock';
        try{
            unlink($path);
        }
        catch(Exception $e){
            ok_return("保存成功，正在为您跳转到首页");
        }
          ok_return("恭喜您配置完成！请继续使用本网张！");
    }

}