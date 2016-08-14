<?php
/**
 * Created by PhpStorm.
 * User: bb3201
 * Date: 2016/6/4
 * Time: 14:15
 */

namespace app\admin\model;

class RtRegularModel extends BaseModel
{

    public $m;
    private $_maxtime = 1000;//物品购买上限(0为无上限)

    public function __construct()
    {
        $this->m = M('rt_regular', null);
    }

    //获取预设列表
    public function regular_list($post)
    {

        $m = M();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
        p.id,g.id goods_id,g.`name`,SUM(p.exec_record_times) as exec_record_times ,p.exec_times,p.enable  FROM sp_rt_regular p RIGHT JOIN sp_goods g ON p.gid = g.id
        WHERE  g.status <> -1 " . $post->wheresql .
            " GROUP BY goods_id ORDER BY id desc,goods_id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }


    //根据ID获取数据
    public function get_info_by_id($id, $type = 'add')
    {
        if ($type == 'add') {
            $info = $this->m->alias('p')
                ->field('p.*,g.id goods_id,g.name goods_name,g.min_times goods_min_times,g.sum_times,g.max_times goods_max_times,g.unit_price,n.min_times multiples,
                n.max_times limit_time,n.sum_times,p.join_type')
                ->join('sp_goods g', 'p.gid = g.id', 'RIGHT')
                ->join('sp_nper_list n', 'n.pid=g.id', 'left')
                ->where(array('g.status' => 1, 'g.id' => $id, 'n.status' => 1))
                ->find();
        } else {
            $info = $this->m->alias('p')
                ->field('p.*,g.id goods_id,g.name goods_name,n.min_times goods_min_times,g.sum_times,g.max_times goods_max_times,g.unit_price,n.min_times multiples,n.max_times limit_time,n.sum_times,p.join_type')
                ->join('sp_goods g', 'p.gid = g.id', 'RIGHT')
                ->join('sp_nper_list n', 'n.pid=g.id', 'left')
                ->where(array('g.status' => 1, 'g.id' => $id, 'n.status' => 1))
                ->select();

        }

        if ($type != 'add') {
            $info = $this->format_data($info);
        }


        return $info;

    }


    public function format_data($data)
    {
        $head = array();
        $body = array();
        foreach ($data as $key => $item) {
            if ($key === 0) {
                $head['enable'] = $item['enable'];
                $head['goods_name'] = $item['goods_name'];
                $head['goods_id'] = $item['goods_id'];
                $head['exec_times'] = $item['exec_times'];
                $head['cogged'] = $item['cogged'];

            }
            if (!is_array($item))
                continue;
            $body[$key]['min_time'] = $item['min_time'];
            $body[$key]['max_time'] = $item['max_time'];
            $body[$key]['id'] = $item['id'];
            $body[$key]['min_buy_times'] = $item['min_buy_times'];
            $body[$key]['max_buy_times'] = $item['max_buy_times'];
            $body[$key]['default_max_times'] = 1;
            $body[$key]['unit_price'] = (int)$item['unit_price'];
            $body[$key]['multiples'] = (int)$item['multiples'];
            $body[$key]['time_range'] = $item['min_hour'] . ',' . $item['max_hour'];
            if (is_null($item['min_hour']) && is_null($item['max_hour'])) {
                $body[$key]['time_range'] = '0,1';
            }

        }
        $head['data'] = $body;
        return $head;

    }

    //创建任务处理
    public function create_tasks($post)
    {
        $gid = $post['gid'];
        $is_cheat = $post['is_cheat'];
        if (isset($post['id'])) {
            unset($post['id']);
            unset($post['is_cheat']);
        }
        //写入作弊数据到GOODS表
        $res = $this->save_is_cheat($gid,$is_cheat);
        if ( $res === false )
            return $res;
        $res = $this->add_create($post);

        return $res;

    }

    //写入作弊数据到GOODS表
    private function save_is_cheat($id,$is_cheat){
        return M('goods')->where(array('id'=>$id))->save(array('is_cheat' => $is_cheat));
    }

    //添加任务
    public function add_create($post)
    {
        $data = $post['data'];
        unset($post['data']);
        foreach ($data as $key => $item) {
            $hour_range = explode(',', $item['time_range']);
            unset($item['time_range']);
            $data[$key] = array_merge($item, $post);
            $data[$key]['min_hour'] = $hour_range[0];
            $data[$key]['max_hour'] = $hour_range[1];
            if (isset($item['id'])) {
                $enable = $post['enable'];
                if ($enable == '-1') {
                    $data[$key]['exec_record_times'] = 0;


                }
                $this->m->where(array('id' => $item['id']))->save($data[$key]);
            } else {
                $this->m->data($data[$key])->add();
            }
        }

        return true;


    }

    //判断有无任务
    public function is_add($id)
    {
        return $this->m->where(array('gid' => $id))->find();
    }

    public function del($id)
    {
        return $this->m->where(array('gid' => $id))->delete();
    }

    //获取需要执行的任务列表
    public function get_exec_data($hour)
    {
        $hour = (int)$hour;
        $data = $this->m->where("min_hour <= {$hour} AND {$hour} < max_hour AND enable=1")->order('update_time', 'ASC')->select();
        $data = $this->deals_data($data);
        return $data;
    }

    //处理任务数据
    public function deals_data($data)
    {
        foreach ($data as $key => $item) {
            //获取执行时间
            $exec_time = $item['exec_time'];
            if ($exec_time == 0) {
                //初始化执行时间
                $data[$key]['exec_time'] = $this->init_exec_time($item);

            }

            $data[$key]['buy_times'] = $this->get_buy_times($item);

        }
        return $data;
    }

    //初始化任务执行时间
    private function init_exec_time($data)
    {
        $time = time();
        $time = (int)rand($data['min_time'], $data['max_time']) + (int)$time;
        $data['exec_time'] = $time;
        unset($data['buy_times']);
        $this->m->where(array('id' => $data['id']))->data($data)->save();
        return $time;


    }

    //获取购买次数
    private function get_buy_times($data)
    {
        $time = (int)ceil(rand($data['min_buy_times'], $data['max_buy_times']));
        //若设置购买上限则最大购买次数为购买上限设置的数值
        if ($this->_maxtime !== 0 && $time > $this->_maxtime) {
            $time = $this->_maxtime;
        }

        return $time;


    }


    //随机获取机器人
    public function get_rand_rt()
    {
        $m = M();
        $sql = 'select id,nick_name from sp_users WHERE type=-1 AND status=1 order by rand() limit 1';
        $rt = $m->query($sql);
        return $rt;

    }


    //初始化数据
    public function init_data($data)
    {
        //初始化执行时间
        $data['exec_time'] = $this->init_exec_time($data);
        $data['exec_record_times'] = (int)$data['exec_record_times'] + 1;
        unset($data['buy_times']);
        $data['update_time'] = time();
        return $this->m->where(array('id' => $data['id']))->data($data)->save();


    }

    //写入购买日志
    public function buy_log($rt, $message, $num)
    {
        $m = M('log', '');
        if (!isset($message[1]['nper_id'])) {
            return;
        }
        $nper_id = $message[1]['nper_id'];
        $m_nper = M('nper_list');
        $goods_name = $m_nper->alias('n')->join('sp_goods g', 'n.pid=g.id', 'left')->field('name')->where(array('n.id' => $nper_id))->find()['name'];
        $data['nper_id'] = $message[1]['nper_id'];
        $data['user'] = $rt[0]['id'];
        $data['type'] = 'RtRegular';
        $data['log'] = '机器人(' . $rt[0]['nick_name'] . ')于' . date('Y-m-d H:i:s') . ' 购买了(' . $goods_name . " ) ";
        $data['create_time'] = time();
        $m->data($data)->add();

    }

    //获取日志
    public function get_log($post)
    {
        $m = M();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
        * FROM log
        WHERE  type='RtRegular' " . $post->wheresql . 'ORDER BY id desc' . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }

    //删除任务
    public function remove_conf($id)
    {
        return $this->m->where(array('id' => $id))->delete();
    }

    //开启机器人作弊
    public function open_cogged($gid)
    {
        $m = M('nper_list');
        $data = $m->where(array('pid' => $gid, 'status' => 1))->find();
        if (empty($data) || !is_null($data['rt_point_uid']))
            return;


        $user = $this->get_rand_rt_win($data['id']);

        !empty($user[0]['user']) && ($data['rt_point_uid'] = $user[0]['user']);
        $m->where(array('id' => $data['id']))->data($data)->save();


    }

    //随机获取机器人
    public function get_rand_rt_win($id)
    {
        $m = M();
        $sql = "select user from log WHERE type=-'RtRegular' AND nper_id={$id} order by rand() limit 1";
        $rt = $m->query($sql);
        return $rt;

    }


}