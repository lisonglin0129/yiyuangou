<?php
namespace app\core\model;

Class OrderModel
{
    private $m_order;
    private $m_luck_num;

    public function __construct()
    {
        $this->m_order = M('order_list', 'sp_');
        $this->m_luck_num = M('luck_num', 'sp_');
    }

    //获取当前用户当期购买列表
    public function get_now_user_order_by_nper_id($nper_id)
    {
        return $this->m_order->where(array('nper_id' => $nper_id, 'uid' => get_user_id()))->select();
    }

    //获取当前用户当期购买列表
    public function get_now_user_luck_num_by_nper_id($nper_id)
    {
        return $this->m_luck_num->where(array('nper_id' => $nper_id, 'uid' => get_user_id()))->select();
    }

    //获取 用户当期购买列表
    public function get_user_luck_num_by_nper_id($uid, $nper_id,$join_type='')
    {
        return $this->m_luck_num->where(array('nper_id' => $nper_id, 'uid' => $uid,'join_type'=>!empty($join_type)?$join_type:0))->select();
    }
    //获取用户购买 奇数 偶数
    public function get_zero_user_luck_num_by_nper_id($uid, $nper_id){
        return $this->m_luck_num->where(array('nper_id' => $nper_id, 'uid' => $uid))->select();
    }

    //获取用户夺宝记录统计
    public function get_payed_order_count_by_user($uid, $date = 0)
    {
        $where = ['o.uid' => $uid, 'o.bus_type' => 'buy','n.id'=>['exp','IS NOT NULL'],'n.status'=>['neq',-1],'p.pay_status'=>3];
        $result = $this->m_order
            ->table('sp_order_list o')
            ->field('o.nper_id,n.status')
            ->join('sp_nper_list n ON n.id = o.nper_id', 'LEFT')
            ->join('sp_order_list_parent p on p.id = o.pid','LEFT')
            ->where($where)
            ->group('o.nper_id,n.status')
            ->select();
        $result = $this->sum_num($result);
        return $result;
    }

    public function sum_num($result) {
        $arr = array();
        $arr['willReveal'] = 0;
        $arr['periodIng'] = 0;
        $arr['periodRevealed'] = 0;
        foreach ($result as $item) {
            switch ( $item['status'] ) {
                case 1:
                    $arr['periodIng']++;
                    break;
                case 2:
                    $arr['willReveal']++;
                    break;
                case 3:
                    $arr['periodRevealed']++;
                    break;
            }

        }
        $arr['all'] = $arr['willReveal'] + $arr['periodIng'] + $arr['periodRevealed'];
        return $arr;

    }
    //统计夺宝三状态数量
    public function get_nper_count_by_status($status){
        $where = ['o.uid' => get_user_id(), 'o.bus_type' => 'buy','n.id'=>['exp','IS NOT NULL'],'n.status'=>['neq',-1]];
        $where['n.status'] = 1;
        $where['p.pay_status']= 3;
        if ($status !== null) {
            $where['n.status'] = $status;
        }
        return $this->m_order
            ->table('sp_order_list o')
            ->join('sp_nper_list n ON n.id = o.nper_id', 'LEFT')
            ->join('sp_order_list_parent p on p.id = o.pid','LEFT')
            ->where($where)
            ->group('o.nper_id,o.join_type')
           ->count();
    }

    //获取用户夺宝记录
    public function get_payed_order_by_user($uid, $page = 1, $size = 20, $stat = null, $date = 0)
    {
        $where = ['o.uid' => $uid, 'o.bus_type' => 'buy','n.id'=>['exp','IS NOT NULL'],'n.status'=>['neq',-1]];
        $where['p.pay_status']= 3;
        if ($stat !== null) {
            $where['n.status'] = $stat;
        }
        if ($date == 1) {
            $where['o.create_time'];
        } elseif ($date == 2) {

        }
        $result = $this->m_order
            ->table('sp_order_list o')
            ->field('SQL_CALC_FOUND_ROWS
                g.id g_id,
                g.`name` g_name,
                gi.img_path g_image,
                n.id n_id,
                n.`status` n_status,
                n.sum_times n_sum,
                n.participant_num n_part,
                n.sum_times - n.participant_num n_remain,
                n.odd_times,
                n.even_times,
                n.odd_join_num,
                n.even_join_num,
                n.luck_num,
                n.luck_uid,
                n.luck_user,
                n.nper_type,
                n.deposer_type,
                u.nick_name,
                o.id o_id,
                o.join_type,
                n.open_time,
                w.luck_type,
                MAX(o.create_time) max_create_time,
                SUM(success_num) num,
                dt.name deposer_type_title'
            )
            ->join('sp_goods g ON g.id = o.goods_id', 'LEFT')
            ->join('sp_nper_list n ON n.id = o.nper_id', 'LEFT')
            ->join('sp_image_list gi ON gi.id = g.index_img', 'LEFT')
            ->join('sp_users u ON u.id = n.luck_uid', 'LEFT')
            ->join('sp_order_list_parent p on p.id = o.pid', 'LEFT')
            ->join('sp_win_record w on w.order_id=o.order_id', 'LEFT')
            ->join('sp_deposer_type dt on dt.id = n.deposer_type', 'LEFT')
            ->where($where)
            ->where('o.success_num > 0')
            ->page($page, $size)
            ->group('o.nper_id,o.join_type')
            ->order('max_create_time desc')
            ->select();

        if (!empty($result)) {
            $count = $this->m_order->query('select FOUND_ROWS() c');
            if ( $stat != 2 ) {
                $result = $this->format_data($result);
            } else {
                $result['data'] = $result;
                $result['num'] = array();
            }

            $result_count = count($result);
            return [$result['data'], $count[0]['c'],$result_count,$result['num']];
        } else {
            return [$result, 0,0,array()];
        }
    }

    /**
     * 数据处理
     * @param $data   处理的数据
     * @param string $status 期数状态别名
     * @param string $n_id 期数ID别名
     * @return mixed array
     *
     */
    public function format_data($data,$status='n_status',$n_id='n_id') {
        $arr = array();
        $nper_ids = array();
        foreach ( $data as  $key => $item) {
            if ( $item[$status] == 2 ) {
                unset($data[$key]);
                array_unshift($data,$item);
            } else if( $item[$status] == 3  ) {
                $nper_ids[]  = $item[$n_id];

            }

        }
        $res['num'] = $this->get_lucker_buy_num($nper_ids);
        $res['data'] = $data;
        return $res;
    }

    /**
     * 获得中奖人,期数和当期购买数量
     * @param $npers
     * @return array|mixed
     */
    private function get_lucker_buy_num($npers) {
        if( empty($npers) ) {
            return array();
        }

        $npers = implode(',',$npers);
//        $sql = "SELECT
//	        s1.nper_id nper_id,
//	        SUM(s1.success_num) num
//          FROM
//	        sp_order_list s1
//          WHERE
//	        s1.nper_id IN ({$npers})AND uid=any(
//            SELECT s2.uid FROM sp_order_list s2 WHERE s2.luck_status='true' AND s1.nper_id=s2.nper_id
//          ) GROUP BY s1.nper_id";

        $sql = " SELECT s1.nper_id nper_id,SUM(s1.success_num) num FROM sp_order_list s1 WHERE nper_id IN ({$npers})AND uid=any(SELECT s2.luck_uid FROM sp_nper_list s2 WHERE s1.nper_id=s2.id) GROUP BY s1.nper_id";
        $data = M()->query($sql);

        $data = $this->merge_array($data);

        return $data;

    }

    /**
     * 合并生成数组
     * @param $data
     * @return array
     */
    private function merge_array($data) {
        $arr = array();
        foreach ($data as $item) {
            $arr[$item['nper_id']] = $item['num'];
        }

        return $arr;


    }

    //根据期数获取夺宝参与记录列表
    public function get_payed_order_by_nper_id($post)
    {

        $sql = "SELECT SQL_CALC_FOUND_ROWS o.*,u.id uid,u.reg_ip,u.ip_area,u.phone,u.phone_area,i.img_path,i.img_path,u.nick_name uname
        FROM  sp_order_list o
        LEFT JOIN sp_users u ON o.uid=u.id
        LEFT JOIN sp_image_list i ON i.id = u.user_pic
        WHERE  o.status =1" . $post->wheresql .
            " ORDER BY o.id desc " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m_order->query($sql);
        $num = $this->m_order->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //0元购夺宝记录列表type=1奇数2偶数
    public function  get_zero_payed_order_by_nper_id($post,$type){
        $sql = "SELECT SQL_CALC_FOUND_ROWS o.*,u.id uid,u.reg_ip,u.ip_area,u.phone,u.phone_area,i.img_path,u.nick_name uname
        FROM  sp_order_list o
        LEFT JOIN sp_users u ON o.uid=u.id
        LEFT JOIN sp_image_list i ON i.id = u.user_pic
        WHERE  o.status =1 AND o.join_type={$type}" . $post->wheresql .
            " ORDER BY o.id desc " . $post->limitData;
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->m_order->query($sql);
        $num = $this->m_order->query($sql_count);
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //格式化购买号码
    public function format_code_list($data) {
        $list = explode(',',$data['code_list']);
        $list = array_filter($list);
        $arr = array();
        foreach ($list as $item) {
            $arr[] =  num_base_mask(intval($item),1,0);

        }
        $data['code_list'] = $arr;
        return $data;

    }

    //根据商品id获取晒单记录列表
    public function get_delivery_list_by_goods_id($post)
    {
        $m_show = M('show_order', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.*,u.user_pic,u.nick_name,i.img_path,i.img_path avatar
        FROM  sp_show_order s
        LEFT JOIN sp_users u ON u.id = s.uid
        LEFT JOIN sp_image_list i ON i.id = u.user_pic
        WHERE  s.status =1 AND s.create_time <".time() . $post->wheresql .
            " ORDER BY s.id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m_show->query($sql);
        $num = $m_show->query($sql_count);

//        $rt["data"] = $info;
//        $rt["count"] = $num[0]["num"];
        return [$info, $num[0]["num"]];

    }

    //首页晒单列表
    public function get_share_list($page = 1, $size = 20)
    {
        $m_show = M('show_order', 'sp_');
        $where = ['s.status' => 1, 's.complete' => 1,'s.create_time'=>array('LT',time())];
        $result = $m_show
            ->table('sp_show_order s')
            ->field('SQL_CALC_FOUND_ROWS s.*,u.user_pic,i.img_path,i.img_path avatar,g.name goods_name,ig.img_path goods_image,u.nick_name')
            ->join('sp_users u ON u.id = s.uid', 'LEFT')
            ->join('sp_image_list i ON i.id = u.user_pic',"LEFT")
            ->join('sp_goods g ON g.id = s.goods_id',"LEFT")
            ->join('sp_image_list ig ON ig.id = g.index_img',"LEFT")
            ->where($where)
            ->order('s.create_time desc')
            ->page($page, $size)
            ->select();
        foreach($result as $key=>$value){
            $img = explode(",",$value['pic_list']);
            $img_url = M("image_list")->field("img_path")->where("id = $img[0]")->find()['img_path'];
            $result[$key]['show_img']=$img_url;
        }
        $sql_count = "SELECT FOUND_ROWS() as num";
        $num = $m_show->query($sql_count);
        return [$result, $num[0]["num"]];
    }

    //获取往期开奖期数列表
    public function get_history_list($post)
    {
        $m_nper = M('nper_list', 'sp_');

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                n.id nper_id,n.sum_times ,n.status,n.luck_num,n.open_time,n.luck_time,n.open_time,n.remainder,n.trigger_time,n.pid,n.nper_type,o.pay_time,o.num buy_num,
                u.username,u.id uid,u.nick_name,u.ip_area,
                i.img_path
                FROM  sp_nper_list n
                LEFT JOIN sp_users u ON u.id = n.luck_uid
                LEFT JOIN sp_image_list i ON i.id =u.user_pic
                LEFT JOIN sp_order_list o ON o.order_id =n.luck_order_id
                WHERE ( n.status =3 OR n.status =2) " . $post->wheresql .
            "   ORDER BY n.status asc,n.id desc ,n.luck_time desc" . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m_nper->query($sql);

        $num = $m_nper->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //获取往期开奖期数列表
    public function get_history_list_p($gid, $page = 1, $size = 10)
    {
        $m_nper = M('nper_list', 'sp_');
        $result = $m_nper
            ->table('sp_nper_list n')
            ->field('SQL_CALC_FOUND_ROWS g.name goods_name,g.id goods_id,i2.img_path goods_image,n.id nper_id,g.sum_times,n.luck_uid,n.luck_user,n.luck_num,i.img_path avatar,n.open_time,n.status,o.pay_time luck_time,o.num luck_buy_num')
            ->join('sp_order_list o ON o.order_id = n.luck_order_id')
            ->join('sp_goods g ON g.id = n.pid','LEFT')
            ->join('sp_users u ON u.id = n.luck_uid','LEFT')
            ->join('sp_image_list i ON i.id = u.user_pic','LEFT')
            ->join('sp_image_list i2 ON i2.id = g.index_img','LEFT')
            ->where(['n.pid'=>$gid,'n.status'=>[2,3,'or']])
            ->page($page,$size)
            ->order('n.id desc')
            ->select();
        if ($result) {
            $count = $m_nper->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //最新/近揭晓
    public function get_opening_list($page = 1, $size = 20)
    {
        $m_nper = M('nper_list', 'sp_');
    //    $where = ['n.status' => [2, 3, 'or'],'g.status' =>];//,'n.open_time'=>['gt',time()]
	    $where = ' n.status = 2 OR n.status = 3 AND g.status > 0 ';
		
//        $where['n.mid']= __MID__;

        $result = $m_nper
            ->table('sp_nper_list n')
            ->field('SQL_CALC_FOUND_ROWS
                     n.id nper_id,n.luck_uid,n.luck_user,n.luck_num,n.open_time,n.status,n.nper_type,
                     g.name goods_name,g.id goods_id,i2.img_path goods_image,g.sum_times,n.luck_time,
                     i.img_path avatar,u.nick_name,d.code,g.status AS gstatus,
                     o.num luck_o_num,u.nick_name')
            ->join('sp_goods g ON g.id = n.pid', 'LEFT')
            ->join('sp_users u ON u.id = n.luck_uid', 'LEFT')
            ->join('sp_image_list i ON i.id = u.user_pic', 'LEFT')
            ->join('sp_image_list i2 ON i2.id = g.index_img', 'LEFT')
            ->join('sp_order_list o ON o.order_id = n.luck_order_id','LEFT')
            ->join('sp_deposer_type d ON d.id = g.deposer_type')
            ->where($where)
            ->page($page, $size)
            ->order('n.status asc,n.open_time desc')
            ->select();
        
        if ($result) {
            $count = $m_nper->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //首页一元传奇
    public function get_one_legendary($limit = 20)
    {
        $m_nper = M('win_record', 'sp_');
        $where  = ['w.order_num' => 1];
//        $where['n.mid']=__MID__;
        $result = $m_nper
            ->table('sp_win_record w')
            ->field('SQL_CALC_FOUND_ROWS
                    w.order_num num,
                    w.luck_user,
                    w.luck_uid,
                    w.luck_time,
                    n.id nper_id,
                    u.nick_name,
                    i.img_path avatar,
                    n.sum_times,
                    w.goods_id gid,
                    g.name goods_name')
            ->join('sp_nper_list n ON n.id = w.nper_id', 'LEFT')
            ->join('sp_goods g ON g.id = w.goods_id', 'LEFT')
            ->join('sp_users u ON u.id = w.luck_uid')
            ->join('sp_image_list i ON i.id = u.user_pic', 'LEFT')
            ->where($where)
            ->order('w.create_time desc')
            ->limit($limit)
            ->select();
        $count = $m_nper->query("SELECT FOUND_ROWS() as c");

        return [$result, $count[0]['c']];
    }

    //根据字段获取订单信息
    public function get_order_info_by_filed($filed, $value)
    {
        $m_order = M('order_list', 'sp_');
        return $m_order->where(array("status" => "1", $filed => $value))->find();
    }


    //获取当期参与人数列表
    public function get_now_list_by_nper_id($nper_id)
    {
        $m_order = M('order_list', 'sp_');
        $sql = 'SELECT DISTINCT uid FROM sp_order_list
WHERE pay_status = 3 AND nper_id = ' . $nper_id;
        return $m_order->query($sql);
    }

    //根据期数id和用户id获取幸运号码
    public function get_luck_num_list_by_nper_uid($nper_id, $uid)
    {
        $m_order = M('luck_num', 'sp_');
        return $m_order->where(array('uid' => $uid, 'nper_id' => $nper_id))->field(array('luck_num'))->select();
    }

    //根据订单 order_id获取父级订单详情
    public function get_p_order_info_by_filed($name, $value)
    {
        $m_order = M('order_list_parent', 'sp_');
        return $m_order->where(array($name => $value))->find();
    }
    //根据订单 order_id获取子级订单详情
    public function get_c_order_info_by_filed($name, $value)
    {
        $m_order = M('order_list', 'sp_');
        return $m_order->where(array($name => $value))->find();
    }

    //根据字段查询改变订单状态
    public function change_p_order_status_by_filed($name, $value, $save)
    {
        $m_order = M('order_list_parent', 'sp_');
        return $m_order->where(array($name => $value))->save($save);
    }

    //根据父级id获取子订单列表
    public function get_order_list_by_pid($pid)
    {
        $m_order = M("order_list", "sp_");
        return $m_order->where(array("pid" => $pid))->select();
    }

    //获取用户中奖记录
    public function get_lucky_history_by_uid($uid,$notify=null)
    {
        $where = ['w.luck_uid'=>$uid];
        if($notify!==null){
            $where['w.notice'] = $notify;
        }
        $m_nper = M('nper_list', 'sp_');
        $result = $m_nper
            ->table('sp_win_record w')
            ->field('SQL_CALC_FOUND_ROWS
                w.id w_id,
                i.img_path goods_image,
                g. NAME goods_name,
                g.id goods_id,
                n.id n_id,
                n.sum_times n_sum,
                n.luck_num,
                n.open_time,
                w.create_time,
                w.order_num o_num')
            ->join('sp_goods g ON g.id = w.goods_id','LEFT')
            ->join('sp_image_list i ON i.id = g.index_img','LEFT')
            ->join('sp_nper_list n ON n.id = w.nper_id','LEFT')
            ->group('n.id')
            ->where($where)
            ->select();
        if ($result) {
            $count = $this->m_order->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //获取用户中奖记录(用于显示duang提示)
    public function get_lucky_duang_by_uid($uid,$notify=null)
    {
        $where = ['w.luck_uid'=>$uid];
        $where['w.read_flag']=array('exp','IS NULL');
        if($notify!==null){
            $where['w.notice'] = $notify;
        }
        $m_nper = M('nper_list', 'sp_');
        $result = $m_nper
            ->table('sp_win_record w')
            ->field('SQL_CALC_FOUND_ROWS
                w.id w_id,
                i.img_path goods_image,
                g. NAME goods_name,
                g.id goods_id,
                g.reward_type,
                n.id n_id,
                n.sum_times n_sum,
                n.luck_num,
                n.open_time,
                w.create_time,
                w.order_num o_num')
            ->join('sp_goods g ON g.id = w.goods_id','LEFT')
            ->join('sp_image_list i ON i.id = g.index_img','LEFT')
            ->join('sp_nper_list n ON n.id = w.nper_id','LEFT')
            ->group('n.id')
            ->where($where)
            ->select();
        if ($result) {
            $count = $this->m_order->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //获取用户晒单记录
    public function get_share_list_by_uid($uid)
    {
        $where['s.uid'] = $uid ;
        $where['s.status'] = array('in',[1,'-3']);
        $where['s.complete'] = 1;
        $where['s.create_time'] =array('LT',time());
        $m_show = M(null, null);
        $result = $m_show
            ->table('sp_show_order s')
            ->field('SQL_CALC_FOUND_ROWS s.*,g.name goods_name,i.img_path goods_image,g.id goods_id')
            ->join('sp_goods g ON g.id = s.goods_id')
            ->join('sp_image_list i ON i.id = g.index_img')
            ->where($where)
            ->select();
        if ($result) {
            $count = $m_show->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //晒单详细信息
    public function get_share_info_by_id($id)
    {
        $m_show = M('show_order', 'sp_');
        $result = $m_show
            ->table('sp_show_order s')
            ->field('s.id,s.title,s.content,s.pic_list,s.create_time share_time,n.open_time,s.nper_id,s.luck_num,
            s.uid,s.username,iu.img_path avatar,u.nick_name,
            g.id goods_id,g.name goods_name,ig.img_path goods_image
            ,o.num order_num,n.luck_time
            ,n.sum_times')
            ->join('sp_goods g ON g.id = s.goods_id', 'LEFT')
            ->join('sp_luck_num l ON l.luck_num = s.luck_num', 'LEFT')
            ->join('sp_nper_list n ON n.id = s.nper_id', 'LEFT')
            ->join('sp_order_list o ON o.order_id = s.order_id','LEFT')
            ->join('sp_users u ON u.id = s.uid', 'LEFT')
            ->join('sp_image_list ig ON ig.id = g.index_img', 'LEFT')
            ->join('sp_image_list iu ON iu.id = u.user_pic', 'LEFT')
            ->where([
                's.status' => array('in',array('1','-3')),
                's.complete' => 1,
                's.id' => $id,
                's.create_time'=>array('LT',time())
            ])->find();
        return $result;
    }

    //根据
    public function get_luck_info_by_nper_id_num($nper_id, $remainder)
    {
        $m_luck = M("luck_num", "sp_");
        return $m_luck->where("nper_id = " . $nper_id . " AND code_list LIKE '%," . $remainder . ",%'")->find();
    }

    //根据期数获取获奖订单
    public function get_luck_info_by_nper_id($nper_id)
    {
        $m_luck = M("luck_num", "sp_");
        return $m_luck->where(array("status" => "true", "nper_id" => $nper_id))->find();
    }

    //检测订单号和用户匹配信息
    public function check_order($order,$uid){
        $geted_uid = M('order_list_parent')->field("uid")->where("order_id = ".$order)->find();
        if($geted_uid['uid']==$uid){
            return true;
        }else{
            return false;
        }
    }

    public function get_last_user_order_by_nper($nper_id){
        $m_order = M("order_list", "sp_");
         return $m_order->where(array('nper_id'=>$nper_id,'dealed'=>'true'))->order('pay_time desc')->find();
    }

    //消费返积分
    public function buy_return_score($order_id) {
        //读取消费返积分开关
        $swith = $this->get_conf('BUY_RETURN_SCORE_SWITH');
        //未开启则直接返回
        if ( $swith != 1 ) {
            return 0;
        }
        //获取用户信息
        $m_user = new UserModel();
        $info = $m_user->get_field_by_uid('score');
        $m_order = M('order_list_parent')->where(array('order_id'=> $order_id))->find();
        //获取订单总金额
        $price = isset($m_order['price'])?$m_order['price']:'';

        //读取订单信息失败
        empty($price) && $code = '-100';
        //获取用户信息失败
        is_null($info) && $code = '-101';
        //不是购买行为 code:-102
        $m_order['bus_type'] != 'buy' && $code = '-102';

        $score = !is_null($info['score'])?$info['score']:0;

        if( !empty($code) ) {
            return 0;
        }

        //获取兑换比例
        $expense = (int) $this->get_conf('BUY_RETURN_SCORE_MONEY');
        $c_money = (int) $this->get_conf('BUY_RETURN_SCORE_SCORE');
        $res_score = $price / $expense * $c_money;
        $expense_scpre = $res_score = intval($res_score);

        //计算返还后个人总积分
        $res_score = (int) $res_score + (int) $score;

        //返还积分入库
        $res = M('users')->where(array('id'=>get_user_id()))->save(array('score' =>$res_score));
        if ( $res === false ) {
            return 0;
        }

        return $expense_scpre;




    }

    private function get_conf($field) {
        return M('conf')->where(array('name'=>$field))->field('value')->find()['value'];

    }


    //根据订单号获取父订单信息
    public  function get_info_by_order_id($order_id,$field='*') {
        return M('order_list_parent')->field($field)->where(array('order_id',$order_id))->find();

    }
}