<?php
namespace app\core\controller;
use app\lib\iHttp;
use app\core\model\CollectModel;


class Collect extends Common
{
    public function __construct()
    {
        parent::__construct();
        set_time_limit(5);
        ini_set('date.timezone', 'Asia/Taipei');
    }

    public function index(){
        $flag = session("collect_flag");
        if($flag=='baidu'){
            $this->kaicaiwang();
        }
        else{
            $this->baidu();
        }
    }

    //开彩网采集
    private function kaicaiwang()
    {
        session("collect_flag","kaicaiwang");
        $url = sprintf('http://f.apiplus.cn/cqssc.json');
        $data = file_get_contents($url);
        !$data && die_json(array('code' => '1', 'msg' => '接口拉取获取失败'));
        $arr = json_decode($data, true);
        $arr = $arr['data'];
        !is_array($arr) && die_json(array('code' => '2', 'msg' => '数组格式转换错误'));

        $m_collect = new CollectModel();

        foreach (array_reverse($arr) as $v) {
            $data = array(
                "issue" => $v['expect'],
                "num" => str_replace(',', '', $v['opencode']),
                "origin" => 'kaicaiwang',
                "open_time" => date("Y-m-d H:i:s", $v['opentimestamp'])
            );
            $m_collect->update_lottery($data);
        }
        die_json(array("code"=>'1',"plat"=>"kaicaiwang"));
    }

    //百度彩票采集
    private function baidu()
    {
        session("collect_flag","baidu");
        $url = 'http://baidu.lecai.com/lottery/ajax_latestdrawn.php?lottery_type=200';
        $http = new iHttp();
        $data = $http->get($url);
        !$data && die_json(array('code' => '-110', 'msg' => '接口拉取获取失败'));

        $arr = json_decode($data, true);
        !is_array($arr) && die_json(array('code' => '-120', 'msg' => '数组格式转换错误'));
        $arr['code'] != '0' && die_json('-130');
        $arr = $arr['data'];
        $m_collect = new CollectModel();

        foreach ($arr as $v) {
            if (!empty($v['result'])) {
                $num = implode($v['result']['result'][0]['data']);
                (!is_numeric($num) || strlen($num) != 5) && die_json(array('code' => '-140', 'msg' => '数据格式不正确'));
                $issue = $v['phase'];
                $open_time = date("Y-m-d H:i:s", strtotime($v['time_endsale']));
                empty($open_time) && die_json(array('code' => '-120', 'msg' => '期数错误'));

                $data = array(
                    "issue" => $issue,
                    "num" => $num,
                    "origin" => 'baidu',
                    "open_time" => $open_time
                );

                $m_collect->update_lottery($data);
            }
        }
        die_json(array("code"=>'1',"plat"=>"baidu"));
    }
}
