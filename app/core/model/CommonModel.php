<?php
namespace app\core\model;

Class CommonModel
{
    public $m_model;

    public function __construct()
    {
//        $m_auth = new AuthModel();
//        $m_auth->init();
    }

    public function get_all_conf(){
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->field(array("name","value"))->select();
        return $info;
    }
    //获取配置信息
    public function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => strtoupper($name)))->find();
        return $info["value"];
    }

    //设置配置信息
    public function set_conf($name, $value)
    {
        $m_conf = M('conf', 'sp_');
        return $m_conf->where(array('name' => strtoupper($name)))->save(array('value' => $value));
    }

    //批量获取配置信息 主键统一小写
    public function get_conf_by_keys($keys)
    {
        if(is_array($keys)){
            $where = ['name' => ['in', $keys]];
        }else if(is_string($keys)){
            $where = ['name' => ['like',$keys]];
        }else{
            return false;
        }
        $m_conf = M(null, null);
        $result = $m_conf->table('sp_conf')->where($where)->select();
        $map = [];
        foreach ($result as $each_result) {
            $map[strtolower($each_result['name'])] = $each_result['value'];
        }
        return $map;
    }

    //批量获取配置信息 主键统一小写
    public function get_conf_website()
    {
        $m_conf = M(null, null);
        $result = $m_conf->table('sp_conf')->where(['name' => ['like', 'WEBSITE\_%']])->select();
        $map = [];
        foreach ($result as $each_result) {
            $map[strtolower($each_result['name'])] = $each_result['value'];
        }
        return $map;
    }

    //标记用户中奖提示信息
    public function flag_trigger($id){
        $m = M('win_record','sp_');
        return $m->where(array('id'=>$id))->save(array('read_flag'=>1)) !== false;
    }
}