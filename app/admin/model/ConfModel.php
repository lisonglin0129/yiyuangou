<?php
namespace app\admin\model;

Class ConfModel extends BaseModel
{
    public $conf_model;

    public function __construct()
    {
        $this->conf_model = M('conf',null);
    }

    //获取配置列表
    public function get_conf_list()
    {

        $category_info = $this->conf_model->field('category')->order('is_must,sort,category desc')->where(array('editable'=>'true'))->group('category')->select();


        $category_arr = array();

        foreach($category_info as $key=>$value) {
            $category_arr[] = $this->conf_model->where(array("editable"=> 'true', "category" => $value['category']))->order('is_must,admin_tpl,sort desc')->select();
        }

        return array(
            'category_arr' => $category_arr,
            'category_info' => $category_info
        );


    }
    //获取单个配置信息
    public function get_conf_by_id($id){
        return $this->conf_model->where(['id'=>$id])->find();   //ceshiss
    }

    //获取极验证信息
    public function get_config_validate(){
        return $this->conf_model->where(array("admin_tpl ='极验证' AND editable = 'true'"))->order('admin_tpl,sort,is_must desc')->select();
    }
    //获取阿里大鱼信息
    public function get_config_fish(){
        return $this->conf_model->where(array("admin_tpl ='阿里大鱼' AND editable = 'true'"))->order('admin_tpl,sort,is_must desc')->select();
    }
    //获取支付信息
    public function get_config_pay(){
        return  $this->conf_model->where(array("category ='支付配置' AND editable = 'true'"))->order('admin_tpl,sort,is_must desc')->select();
    }
    //获取其他配置信息
    public function get_other_data(){
        $category_info = $this->conf_model->field('category')->order('sort,category,is_must  desc')->where(array('editable'=>'true'))->group('category')->select();


        $category_arr = array();

        foreach($category_info as $key=>$value) {
            $category_arr[] = $this->conf_model->where(array("editable" => 'true', "is_must" => 'false', "category" => $value['category']))->order('admin_tpl,sort,is_must desc')->select();
        }

        return array(
            'category_arr' => $category_arr,
            'category_info' => $category_info
        );
    }

    //获取配置项中必填项信息
    public function check_must_info(){
        return $this->conf_model->where("editable = 'true' AND is_must = 'true'")->select();
    }
    //隐藏第三方开关
    public function hide_third_switch(){
        return $this->conf_model->where("admin_tpl = '第三方登录开关'")->setField('editable','false');
    }
    //打开第三方开关
    public function show_third_switch(){
        return $this->conf_model->where("admin_tpl = '第三方登录开关'")->setField('editable','true');
    }
    //检测是否要初始化配置项
    public function check_remark_conf(){
        return $this->conf_model->field('remark')->where("name = 'RUN_CONFIG_SETUP'")->find();
    }
    //检测支付宝支付信息
    public function check_ali_pay(){
        return $this->conf_model->where("admin_tpl = '支付宝配置'")->select();
    }
    //隐藏支付宝选项
    public function hide_ali_pay(){
        return $this->conf_model->where("admin_tpl = '支付宝配置'")->setField('editable','false');
    }
    //打开支付宝选项
    public function show_ali_pay(){
        return $this->conf_model->where("admin_tpl = '支付宝配置'")->setField('editable','true');
    }
    //检测爱呗支付信息
    public function check_aibei_pay(){
        return $this->conf_model->where("admin_tpl = '爱呗支付'")->select();
    }
    //隐藏爱呗选项
    public function hide_aibei_pay(){
        return $this->conf_model->where("admin_tpl = '爱呗支付'")->setField('editable','false');
    }
    //打开爱呗选项
    public function show_aibei_pay(){
        return $this->conf_model->where("admin_tpl = '爱呗支付'")->setField('editable','true');
    }
    //检测微信支付信息
    public function check_w_pay(){
        return $this->conf_model->where("admin_tpl = '微信支付配置'")->select();
    }
    //隐藏微信选项
    public function hide_w_pay(){
        return $this->conf_model->where("admin_tpl = '微信支付配置'")->setField('editable','false');
    }
    //打开微信选项
    public function show_w_pay(){
        return $this->conf_model->where("admin_tpl = '微信支付配置'")->setField('editable','true');
    }

}