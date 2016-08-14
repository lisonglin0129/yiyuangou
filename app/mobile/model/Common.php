<?php

namespace app\mobile\model;
use think\Model;
class Common extends Model {

    //获取配置信息
    public function get_conf($name) {
        $m_conf =  M('conf','sp_');
        $info = $m_conf->where(array('name' => $name))->find();
        return $info["value"];
    }



}