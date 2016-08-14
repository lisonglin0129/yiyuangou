<?php
namespace app\core\model;
/**
 * Created by PhpStorm.
 * User: phil
 * Date: 2016/4/13
 * Time: 22:05
 */
Class UcenterModel
{
    //获奖商品状态
    private $prize_status = array('get_goods','confirm_address','send_goods','confirm_receipt','already_sign');
    private $prize_status_info = array('获得商品','确认收货地址','商品派发','确认收货','已签收');

    public function __construct()
    {
    }

    //获取当前登录用户收货地址
    public function get_login_user_addr()
    {
        $m_user = M("user_addr", "sp_");
        return $m_user->where(array("uid" => get_user_id()))->order("type desc")->select();
    }

    public function update_new_addr($post)
    {
        $m_user = M("user_addr", "sp_");
        extract($post);
        $data = array(
            "uid" => get_user_id(),
            "sn_id" => $sn_id,
            "name" => $name,
            "code" => $code,
            "address" => $address,
            "tel" => $tel,
            "phone" => $phone,
            "provice" => $provice,
            "city" => $city,
            "area" => $area,
            "type" => $type,
            "create_time" => NOW_TIME,
        );
        if (!empty($id)) {
            $r = $m_user->where(array("id" => $id))->save($data);
            return $r !== false;
        } else {
            return $m_user->add($data);
        }

    }

    //设置登录用户的字段
    public function set_login_addr_by_feild($post)
    {
        $m_user = M("user_addr", "sp_");
        return $m_user->where(array("uid" => get_user_id()))->save($post);
    }

    //删除地址byid
    public function del_addr_by_id($id)
    {
        $m_user = M("user_addr", "sp_");
        return $m_user->where(array("id" => $id))->delete();
    }

    //根据id返回地址信息
    public function get_addr_info_by_id($id,$field="*")
    {
        $m_addr = M("user_addr", "sp_");
        return $m_addr->field($field)->where(array("id" => $id))->find();
    }

    //获取登录用户的充值列表
    public function get_login_user_charge_list($post)
    {
        $m_order_p = M("order_list_parent", "sp_");

        $sql = "SELECT SQL_CALC_FOUND_ROWS *
        FROM  sp_order_list_parent
        WHERE  bus_type ='recharge' AND pay_status != '1' AND status ='1' AND uid=".get_user_id() . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m_order_p->query($sql);
        $num = $m_order_p->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }

    public function get_total_charge() {
        $data = M('order_list_parent')->field('SUM(price) price')->where(array('bus_type'=>'recharge','pay_status'=>3,'status'=>1,'uid'=>get_user_id()))->select();
        return isset($data[0])?$data[0]['price']:0.00;
    }

    //根据id获取id
    public function get_pic_info_by_id($id)
    {
        $m_order_p = M("image_list", "sp_");
        return $m_order_p->where(array("id" => $id))->find();
    }

    //获取自己中奖记录
    public function get_my_lucky_by_uid($uid,$page=1,$size=10){
        $m_luck = M('win_record', 'sp_');
        $result = $m_luck
            ->table('sp_win_record w')
            ->field('SQL_CALC_FOUND_ROWS
    w.id,
	w.goods_id,
	g.`name` goods_name,
	n.nper_type,
	n.deposer_type,
	w.nper_id n_id,
	i.img_path goods_image,
	w.luck_num,
	w.order_num o_num,
	n.sum_times n_sum,
	w.create_time,
	w.luck_time open_time,
	w.luck_uid uid,
	w.luck_type,
	w.logistics_status,
w.notice')
            ->join('sp_goods g ON g.id = w.goods_id','LEFT')
            ->join('sp_image_list i ON i.id = g.index_img','LEFT')
            ->join('sp_nper_list n ON n.id = w.nper_id','LEFT')
            ->where(['w.luck_uid'=>$uid])
            ->order('w.id desc')
            ->page($page,$size)
            ->select();
//        if(APP_DEBUG) echo '<pre>'.$m_luck->getLastSql().'</pre>';
        if ($result) {
            $count = $m_luck->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //奖品物流详情
    public function get_luck_detail_by_luckid_and_uid($id,$uid){
        $m_luck = M('win_record', 'sp_');
        $result = $m_luck
            ->table('sp_win_record w')
            ->field('w.*, g.`name` goods_name,ig.img_path goods_image,s.id show_order,g.price g_price,c.name logistics_company ')
            ->join('sp_goods g ON g.id = w.goods_id','LEFT')
            ->join('sp_image_list ig ON ig.id = g.index_img','LEFT')
            ->join('sp_show_order s ON s.nper_id = w.nper_id','LEFT')
            ->join('category_list c','c.code=w.logistics_company',"left")
            ->where(['w.id'=>$id,'w.luck_uid' => $uid])
            ->find();
//        if(APP_DEBUG) echo '<pre>'.$m_luck->getLastSql().'</pre>';
        return $result;
    }

    //更新中奖通知状态
    public function set_win_record_notify($id){
        $m_luck = M('win_record', 'sp_');
        $where = ['id'=>$id];
        $result = $m_luck
            ->data(['notice'=>1,'logistics_status'=>1])
            ->where($where)
            ->save();
//        if(APP_DEBUG) echo '<pre>'.$m_luck->getLastSql().'</pre>';
        return $result;
    }

    //奖品状态变更
    public function set_luck_status_by_id($id,$stat){
        $m_luck = M('win_record', 'sp_' );
        $where = ['id'=>$id];
        $result = $m_luck
            ->data(['logistics_status'=>$stat])
            ->where($where)
            ->save();
        return $result;
    }

    //奖品填充收货地址
    public function set_win_record_addr($id,$addr_data){
        $m_luck = M('win_record', 'sp_');
        $where = ['id'=>$id];
        $result = $m_luck
            ->data(['address_data'=>$addr_data,'logistics_status'=>2])
            ->where($where)
            ->save();
//        if(APP_DEBUG)echo('<pre>'.$m_luck->getLastSql().'</pre>');
        return $result;
    }

    //晒单初始化
    public function show_order_init($data)
    {
        $m_show = M('show_order', 'sp_');
        $result = $m_show
            ->data($data)
            ->add();
        return $result;
    }
    //晒单保存
    public function show_order_update($id, $uid, $title, $content, $pic_list)
    {
        $m_show = M('show_order', 'sp_');
        $result = $m_show
            ->data([
                'title' => $title,
                'content' => $content,
                'pic_list' => $pic_list,
                'create_time' => time(),
                'complete' => 1,
            ])
            ->where(['id' => $id, 'uid' => $uid])
            ->save();

        return $result;
    }
    //获取充值卡分类信息
    public function get_card_category_by_id($id){
        $m_card = M('card_category','sp_');
        return $m_card->where(['id'=>$id])->find();
    }
    //获取充值卡信息
    public function get_card_detail($category,$num=1){
        $m_card = M('card_list','sp_');
        $result = $m_card
            ->field('SQL_CALC_FOUND_ROWS id,num,sec')
            ->where([
                'category'=>$category,
                'used'=>0
            ])->limit($num)
            ->select();
        if ($result) {
            $count = $m_card->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }
    //标记充值卡使用信息
    public function sign_card_detail($id,$uid,$nper){
        if(is_int($id)){
            $where=['id'=>$id];
        }else if(is_array($id)){
            $where=['id'=>['in',$id]];
        }

        $m_card = M('card_list','sp_');
        return $m_card->where($where)->data(['uid'=>$uid,'nper'=>$nper,'used'=>1])->save();
    }
    //写入用户获奖（物流/卡密）信息
    public function set_reward_info($id,$reward_type,$addr){
        $m_luck = M('win_record', 'sp_');
        $where = ['id'=>$id];
        $result = $m_luck
            ->data(['reward_type'=>$reward_type,'address_data'=>$addr])
            ->where($where)
            ->save();
        return $result;
    }
    public function add_money($uid,$money){
        $m_user = M('users','sp_');
        return $m_user->where(['id'=>$uid])->setInc('money',$money);
    }

    public function prize_status_add($record_id,$state){
        $m_prize = M('prize_status','sp_');
        return $m_prize->add([
            'win_record_id'=>$record_id,
            'status'=>$this->prize_status[$state],
            'status_info'=>$this->prize_status_info[$state],
            'create_time'=>time()
        ]);
    }
}