<?php
namespace app\core\model;

Class LogModel
{

    //增加通用日志
    public function log_add($post)
    {
        extract($post);
        $m_log =  M('log','');
        $data = array(
            'user'=>empty($user) ? "" : $user,
            'type'=>empty($type) ? "" : $type,
            'log'=>empty($log) ? "" : $log,
            'create_time'=>date('Y-m-d H:i:s'),
        );
        return $m_log->add($data);
    }

    public function record($pre_data,$after_data,$flag="",$status=1){
            $data = array(
                "uid" => $pre_data['id'],
                "username" => $pre_data['username'],
                "pre_money" => $pre_data['money'],
                "pre_score" => $pre_data['score'],
                "pre_cash" => $pre_data['cash'],

                "after_money" => $after_data['money'],
                "after_score" => $after_data['score'],
                "after_cash" => $after_data['cash'],

                "type" => $flag,
                "status" => $status,
                "create_time" => time()
            );

        M("charge","log_")->add($data);
    }



}