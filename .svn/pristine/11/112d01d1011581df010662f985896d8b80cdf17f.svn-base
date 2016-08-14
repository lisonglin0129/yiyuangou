<?php
/**
 * Created by PhpStorm.
 * User: liuchao
 * Date: 16/7/9
 * Time: 上午11:17
 */
namespace app\core\model;
class LogBehaviorModel{
    private $m;
    public function __construct(){
        $this->m=M('behavior','log_');
    }
    //添加行为日志
    public function log_behavior_add(){
        $data = array(
            "uid" => login_id(),
            "use_login" => login_username(),
            "modules" => MODULE_NAME,
            "controllers" => CONTROLLER_NAME,
            "action" => ACTION_NAME,
            "action_cn_name" => null,
            "request_type" => IS_AJAX ? "ajax" : "normal",
            "method_type" => IS_POST ? "post" : "get",
            "request_data" => json_encode(I("request.", ''), JSON_UNESCAPED_UNICODE),
            "ip" => get_client_ip(),
            "create_time" => time()
        );
        $this->m->add($data);
    }
}