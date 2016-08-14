<?php
namespace app\admin\controller;

use app\admin\model\ConfModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;
use think\Exception;

Class Conf extends Common
{
    public function __construct()
    {
        parent::__construct();
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

        $value = trim($value);

        $res = $conf->where(array("id"=>$id))->setField('value', $value);

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
        //读取官网配置JSON内容
        $conf = './data/db/config.json';

        try {
            $json = file_get_contents($conf);
        } catch (Exception $e) {
            die('读取json数据错误');
        }

        $arr = json_decode($json,true);

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
            );

            //增加
            if (empty($info)) {
                echo "<span style='color:#f36'>增加[{$v['name']}]成功!</span>";

                $m->add($arr);
                echo $info['name'];
            } //更新
            else {
                echo "<span style='color:#226b0a'>更新[{$info['id']}][{$v['name']}]成功!</span>";
                $m->where(array('name' => $v['name']))->save($arr);
            }

            echo "ok;<br/>\n";
            flush();
            ob_flush();
        }
        echo '执行完毕!';
    }


    public function export(){
        $m = M('conf', 'sp_');
        $conf = $m->where('name != "DEMO" ')->select();
        empty($conf) && die('获取json失败');
        foreach($conf as $k=>$v){
            if(empty($v['is_default']) || $v['is_default'] != "true"){
                unset($v["value"]);
                $conf[$k] = $v;
            }
        }

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=config.json");
        die(trim(json_encode($conf), "\xEF\xBB\xBF"));
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
}