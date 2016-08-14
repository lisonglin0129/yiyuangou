<?php
namespace app\admin\controller;


use app\admin\model\RtModel;
use app\admin\model\RtPresetsModel;
use app\lib\Condition;
use think\Controller;
use app\lib\Page;
use think\Exception;

Class RtPresets extends Common
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

    //查看/编辑/新增
    public function exec()
    {
        $m = new RtPresetsModel();
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);

        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $m->get_info_by_id($id);
            empty($info) && die('获取信息失败');
            if(empty($info['num_max'])){
                $info['num_max']=$info['sum_times']>=100?100:$info['sum_times'];
            }
            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
//            } else if($type=="gen_task") {
//                $this->gen($info);
            }else{
                    $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }
        return $this->fetch('form');
    }

    public function presets_del()
    {
        $gid = I("post.id");
        (empty($gid) || !is_numeric($gid)) && wrong_return('参数异常,删除失败');
        $m_presets = new RtPresetsModel();
        $m_rt = new RtModel();
        if($m_presets->del($gid)){
            $m_rt->clear_task($gid,false);
            ok_return('删除成功');
        }
        wrong_return('删除操作失败');
    }

    //执行添加用户
    public function update()
    {
        $post = I("post.");
        $this->save_test($post);
        extract($post);

        $m = new RtPresetsModel();

        $rt = $m->update($post);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //预设
    public function presets()
    {
        return $this->fetch();
    }

    //预设列表
    public function presets_list(){
        //获取列表
        $condition_rule = array(
        );
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $users = new RtPresetsModel();
        $presets_list = $users->presets_list($model);
        /*生成分页html*/
        $my_page = new Page($presets_list["count"], $this->page_num, $this->page, U('presets_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('presets_list', $presets_list['data']);
        return $this->fetch();
    }
    //生成任务
    private function gen_task($info,$time=null){
        set_time_limit(0);
        ini_set('max_execution_time','0');

        $time = $time==null?time():$time;
        $num = $info['num_count'];
        $interval = $info['interval'];
        $gid = $info['gid'];
        $min = $info['num_min'];
        $max = $info['num_max'];

        if($time==null){
            $time = time();
        }
        //参与的用户数
        $user = 200;
        $mod = 5;

        //购买次数的列表
        $list = [];
        //购买的总数
        $count = 0;

        while($count<$num){
            $r = mt_rand(0,9999);
            if($r<3000){
//购买最低
                $n=$min;
                array_push($list,$n);
                $count+=$n;
            }elseif($r<6000){
//最低的倍数
                $r = mt_rand(0,9999);
                if($r<3000){
                    $n=$min*2;
                    array_push($list,$n);
                    $count+=$n;
                }elseif($r<6000){
                    $n=$min*3;
                    array_push($list,$n);
                    $count+=$n;
                }elseif($r<9000){
                    $n=$min*5;
                    array_push($list,$n);
                    $count+=$n;
                }else{
                    $n=$min*10;
                    array_push($list,$n);
                    $count+=$n;
                }
            }elseif($r<9990){
                $r = mt_rand(1,100);
                $n=intval(pow(10,log10($max)*($r/100)));
                $n=intval($n-$n%$mod + $mod);
                array_push($list,$n);
                $count+=$n;
            }else{
//最大值
                $n=$max;
                array_push($list,$n);
                $count+=$n;
            }
        }
        $c = $count-$num;
        //最后修正
        foreach($list as &$l){
            if($l > $c){
                $l = $l - $c;
                break;
            }
        }
        $list=array_filter($list);

        //计算时间
        $t_list =[];
        for($i=count($list)-1;$i>=0;$i--){
            $t_list[$i]=$time+mt_rand(0,$interval);
        }
        sort($t_list);
        $m_rt = new RtModel();
        $users = $m_rt->rand_users(count($list));
        $results = [];

        $c = count($list);
        for($i=0;$i<$c;$i+=100){
            $frag100 = [];
            for($j=$i;$j<$i+100&&$j<$c;$j++){
                array_push($frag100,[
                    'gid'=>$gid,
                    'time'=>$t_list[$j],
                    'num'=>$list[$j],
                    'uid'=>$users[rand(0,count($users)-1)]['id'],
                    'stat'=>0,
                    'type'=>'buy'
                ]);
            }
            $m_rt->add_task($frag100);
        }
        return true;
    }

    //保存设置并生成任务
    public function save_and_start(){
        $post = I("post.");
        $this->save_test($post);
        $post['enable']='true';
        extract($post);

        $m = new RtPresetsModel();

        $rt = $m->update($post);
        //&& $this->gen($post)
        if($rt ){
            $m_rt = new RtModel();
            $clear_result = $m_rt->clear_task($post['gid'],false);
            $gen_result = $this->gen_task($post);
            if($post['loop']=='true'){
                $loop_result = $m_rt->add_task([
                    'gid'=>$post['gid'],
                    'time'=>time()+$post['interval'],
                    'type'=>'gen',
                    'stat'=>0,
                ],false);
            }
            ok_return('恭喜!操作成功!');
        }
        wrong_return('数据写入失败');
    }

    //保存设置并清理任务
    public function save_and_stop(){
        $gid = I("post.gid",0,'intval');
        //$this->save_test($post);
        //$post['enable']='false';
        //extract($post);

        $m = new RtPresetsModel();

        $rt = $m->update_by_gid($gid,['enable'=>'false']);

        //&& $this->gen($post);
        if($rt ){
            $m_rt = new RtModel();
            $clear_result = $m_rt->clear_task($gid,false);

            ok_return('恭喜!操作成功!');
        }
        wrong_return('数据写入失败');
    }

    /*
    //按请求的秒数执行任务
    public function run_task_1s($t=null){
        //try{
            $interval = 1;
            header('Content-Type:text/html;charset=utf8');
            set_time_limit(30);
            ini_set('max_execution_time',30);
            $start_time = $t;
            $m_rt = new RtModel();
            $task_list = $m_rt->query_task($interval,$t);
            if(empty($task_list) || !is_array($task_list))die();
            foreach($task_list as &$each_task){
                if($each_task['type']=='buy'){
                    list($success,$result)= $this->rt_buy_goods($each_task['gid'],$each_task['uid'],$each_task['num']);
                    if($success){
                        $update_result = $m_rt->update_task(
                            $each_task['id'],
                            key_exists('code',$result)?intval($result['code']):null,
                            key_exists('msg',$result)?$result['msg']:null,
                            key_exists('nper_id',$result)?intval($result['nper_id']):null
                        );
                    }

                }else if($each_task['type']=='gen'){
                    $m_preset = new RtPresetsModel();
                    $preset_info = $m_preset->get_info_by_id($each_task['gid']);
                    if($preset_info['loop']=='true'){
                        $gen_result = $this->gen_task($preset_info,$t);
                        $loop_result = $m_rt->add_task([
                            'gid'=>$preset_info['gid'],
                            'time'=>$t+$preset_info['interval'],
                            'type'=>'gen'
                        ],false);
                        if($gen_result){
                            $update_result = $m_rt->update_task($each_task['id'],1,'生成完成');
                        }
                    }
                }else{

                }
            }
//        }catch(Exception $ex){
//            var_dump($ex);
//        }
    }
    */
    //拉取1分钟的任务
    public function run_task_1min($interval=60){
        //$interval = 60;
        session_start();
        $interval = intval($interval);
        set_time_limit($interval+60);
        ini_set('max_execution_time',$interval+60);
        $time = time();
        $start_time = $time;
        $end_time = $time+$interval;
        $m_rt = new RtModel();
        $this->cli_log(date('Y-m-d H:i:s')."--".date('Y-m-d H:i:s',$end_time));
        //拉取任务
        list($task_list,$debug_sql) = $m_rt->query_task($interval);
        $task_count = count($task_list);
        $this->cli_log($task_count.'task(s)');
        //$this->cli_log($debug_sql);
        $time_keys = array_column($task_list,'time');

        while(count($time_keys)>0){
            $next_t = array_shift($time_keys);
            $this->cli_log("sleep_until:".date('Y-m-d H:i:s',$next_t));
            try {
                time_sleep_until($next_t);
            }catch(Exception $ex){
                //等待0.1秒
                usleep(100000);
                $next_t=time();
            }
            //for($i=0;$i<count($task_list);$i++){
            foreach($task_list as &$each_task){
                if($each_task['time']<=$next_t && !key_exists('flag',$each_task)){
                    //购买任务
                    $this->cli_log(count($task_list)."/{$task_count}:{$each_task['id']}");
                    if($each_task['type']=='buy') {
                        $this->cli_log("buy:{$each_task['id']}-{$each_task['gid']}-{$each_task['uid']}-{$each_task['num']}");
                        list($success,$result)= $this->rt_buy_goods($each_task['gid'],$each_task['uid'],$each_task['num']);
                        $this->cli_log("ok");
                        $this->cli_log("update");
                        if($success){
                            list($_code,$_msg)=$result;
                            $_nper = key_exists('nper_id',$result)?$result['nper_id']:null;
                            $update_result = $m_rt->update_task(
                                $each_task['id'],$_code,$_msg,$_nper
                            );
                        }else{
                            $update_result = $m_rt->update_task(
                                $each_task['id'],-555,json_encode($result)
                            );
                        }
                        $this->cli_log("ok");
                    //生成任务
                    }else if($each_task['type']=='gen') {
                        $this->cli_log("gen");
                        $m_preset = new RtPresetsModel();
                        $preset_info = $m_preset->get_info_by_id($each_task['gid']);
                        if($preset_info['loop']=='true'){
                            $gen_result = $this->gen_task($preset_info,$next_t);
                            $loop_result = $m_rt->add_task([
                                'gid'=>$preset_info['gid'],
                                'time'=>$next_t+$preset_info['interval'],
                                'type'=>'gen'
                            ],false);
                            if($gen_result){
                                $update_result = $m_rt->update_task($each_task['id'],1,'生成完成');
                            }
                        }
                    }else{

                    }
                    unset($success,$_nper,$_code,$_msg);
                    $this->cli_log("fin~".$each_task['id']);
                    $each_task['flag']=1;
                }
            }
            $time_keys = array_diff($time_keys,[$next_t]);
        }

//            if(array_search($t,$time_keys)){
//                foreach($task_list as &$each_task){
//                    if($each_task['time']==$t){
//                        echo(ch_time($t).'<br/>');
//                        $result = $this->rt_buy_goods($each_task['gid'],$each_task['uid'],$each_task['num']);
//                        if($result){
//                            $result = $m_rt->update_task($each_task['id'],$result['code'],$result['msg']);
//                        }else{
//                            $result = $m_rt->update_task($each_task['id'],'-1','msg');
//                        }
//                        var_dump($result);
//                    }
//                }
//            }
    }
    private function cli_log($msg){
        if(IS_CLI){
            echo date('Y-m-d H:i:s').":\t{$msg}\n";
        }else{
            echo date('Y-m-d H:i:s').":<br/>{$msg}<hr/>";
            flush();
            ob_flush();
        }
    }
    //保存前有效性测试
    private function save_test($post){
        $interval = $num_max = $num_min = $min_times = $num_count = $max_times = null;
        extract($post);

        !empty($gid) && !is_numeric($gid) && wrong_return('id格式不正确');
        if($interval<300){
            wrong_return('[作用时间]最低为300秒；（5分钟）');
        }
        if($num_max>$max_times || $num_max>1000 || $num_max <$num_min*10){
            wrong_return('[最大购买次数]至少为[最小购买次数的十倍],且不得大于商品单价或大于1000');
        }
        if($min_times>$num_count ){
            wrong_return('[参与人次]不得小于[最小购买次数]');
        }
    }

    //模拟购买商品
    public function rt_buy_goods($goods_id,$uid,$num){
        //$m_conf = M('conf');
        //$api = $m_conf->where(['name'=>'RT_BUY_API'])->getField('value');
        //$api = 'http://www.yiyuangou.local/core/rt/rt_buy?';
        $param = "goods_id={$goods_id}&uid={$uid}&num={$num}";

        $r=R('core/rt/rt_buy_p',$param);
        return [true,$r];
        //var_dump($r);
        //die();

        $ch = curl_init($api.$param);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $ch_result = curl_exec($ch);
        $curl_error = curl_error($ch);
        if($curl_error==''){
            $json_result = json_decode($ch_result,true);
            if(json_last_error()==JSON_ERROR_NONE){
                return [true,$json_result];
            }else{
                return [false,['code'=>-1,'msg'=>$ch_result]];
            }
        }else{
            return [false,['code'=>-2,'msg'=>$curl_error]];
        }
    }


}