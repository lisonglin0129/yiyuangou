<?php
namespace app\admin\model;

Class WinRecordModel extends BaseModel
{
    public $win_record_model;

    public function __construct()
    {
        $this->win_record_model = M('win_record',null);
    }
    //获取单个中奖纪录
    public function get_win_record($id){
        if(empty($id)){
            die('出错');
        }
        return $this->win_record_model->alias('w')->field('w.order_id,w.goods_id,w.nper_id,u.id uid,u.username,w.luck_num')
            ->join('sp_users u ON u.id=w.luck_uid')->where(['w.id'=>intval($id)])->find();
    }
    //获取商家中奖用户列表
    public function get_shop_win_record_list($post){
        $m = M('win_record', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  w.*,u.id uid,u.username,u.nick_name,u.money,u.phone,g.name
        FROM  sp_win_record w
        LEFT JOIN sp_users u ON u.id=w.luck_uid
        LEFT JOIN sp_goods g ON g.id=w.goods_id
        WHERE  1=1 " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);
        foreach($info as $key=>$value) {
            $info[$key]['luck_time'] = microtime_format($value['luck_time'],1,'Y-m-d H:i:s');
        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }
    //获取0元购中奖列表
    public function get_zero_win_record_list($post){
        $sql = "SELECT SQL_CALC_FOUND_ROWS  w.*,g.name goods_name,u.type,u.nick_name
        FROM  sp_win_record w
        LEFT JOIN sp_users u ON u.id=w.luck_uid
        LEFT JOIN sp_goods g ON g.id=w.goods_id
        WHERE  w.qb_type>0 " . $post->wheresql .
            " ORDER BY w.create_time DESC " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $this->win_record_model->query($sql);
        $num = $this->win_record_model->query($sql_count);
        foreach ( $info as $index => $item) {
            $info[$index]['luck_num'] = num_base_mask(intval($item['luck_num']),1,0);
        }
        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //获取中奖列表
    public function get_win_record_list($post)
    {
        $m = M('win_record', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  w.*,g.name goods_name,u.type,s.id sid,s.status,u.nick_name,u.username,u.phone
        FROM  sp_win_record w
        LEFT JOIN sp_users u ON u.id=w.luck_uid
        LEFT JOIN sp_goods g ON g.id=w.goods_id
        LEFT JOIN sp_show_order s ON s.nper_id=w.nper_id
        WHERE  1 " . $post->wheresql .
            "ORDER BY w.luck_time desc,w.id desc,u.type,s.status asc,luck_time desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);
        foreach ( $info as $index => $item) {
            $info[$index]['luck_num'] = num_base_mask(intval($item['luck_num']),1,0);
        }

//        $goods = M('goods',null);

//        foreach($info as $key=>$value) {
//            $info[$key]['goods_name'] = $goods->field('name')->where('id ='.$value['goods_id'])->find()['name'];
//            $info[$key]['luck_time'] = microtime_format($value['luck_time'],3,'Y-m-d H:i:s');
//        }

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }
    //获取中奖单个用户详情
    public function get_win_user_info($id){
        $info=$this->win_record_model->alias('w')->field('w.*,u.*')->join('sp_users u ON u.id=w.luck_uid')->where(['w.id'=>$id])->find();
        $info['user_face']=$this->get_one_image_src($info['user_pic']);
        $addr=json_decode($info['address_data'],true);
        $info['address_data']=$addr['provice'].$addr['city'].$addr['area'].$addr['address'];
        return $info;
    }

    //获取中奖详情
    public function get_info_by_id($id)
    {
        $win_record_info = $this->win_record_model
            ->field('w.*,u.nick_name')
            ->table('sp_win_record w')
            ->join('sp_users u ON u.id = w.luck_uid')
            ->where(array("w.id" => $id))->find();
        if(!empty($win_record_info['address_data'])) {
            $win_record_info['address_data'] = json_decode($win_record_info['address_data'],true);
        }

        $goods = M('goods',null);
        $category = M('category',null);
        $category_list = M('category_list',null);

        $win_record_info['luck_num'] = num_base_mask(intval($win_record_info['luck_num']),1,0);
        $win_record_info['goods_name'] = $goods->field('name')->where(array('id' =>$win_record_info['goods_id']))->find()['name'];
        $win_record_info['luck_time'] = microtime_format($win_record_info['luck_time'],3,'Y-m-d H:i:s');

        //查询物流公司信息
        $pid = $category->field('id')->where("code ='logistics'")->find()['id'];
        if(is_null($pid)) {
            $win_record_info['logistics_list'] = '';
        } else {
            $win_record_info['logistics_list'] = $category_list->field('code,name')->where(array('mid' =>$pid))->select();
        }

        return $win_record_info;
    }


    //修改
    public function update_win_record($post)
    {
        $id = $name = $phone = $code = $provice = $city = $area = $address = $logistics_company = $logistics_number = null;

        extract($post);


        //判断地址是否填写完整，要么填写完整，要么不填
        $address_part = true;
        $address_full = false;
        $address_null = false;
        $logistics_effect = false;


        if(empty($name) && empty($phone) && empty($code) && empty($provice) && empty($city) && empty($area) && empty($address)) {
            $address_null = true;
        }

        if(!empty($name) && !empty($phone) && !empty($code) && !empty($provice) && !empty($city) && !empty($area) && !empty($address)) {
            $address_full = true;
        }

        if($address_full || $address_null) {
            $address_part = false;
        }

        $address_part && wrong_return('地址请填写完整');

        //检测物流
        if(!empty($logistics_company) && !empty($logistics_number)) {
            $logistics_effect = true;
        }elseif(empty($logistics_number) && !empty($logistics_company)) {
            wrong_return('请填写物流单号');
        }elseif(!empty($logistics_number) && empty($logistics_company)) {
            wrong_return('请填写物流公司');
        }

        //如果物流信息存在而收货地址不存在，不对
        if($logistics_effect && $address_null) {
            wrong_return('请填写收货地址');
        }

        //查询该中奖纪录状态
        $win_status = $this->win_record_model->field('logistics_status')->where(array('id' =>$id))->find()['logistics_status'];

        if(empty($win_status) || $win_status == 0) {
            $win_status = 1;
        }


        //组合地址信息
        $address_data = array(
            'name' => $name,
            'code' => $code,
            'address' => $address,
            'phone' => $phone,
            'provice' => $provice,
            'city' => $city,
            'area' => $area
        );

        $address_data = json_encode($address_data);

        $PrizeStatus = M('prize_status');

        $save_data = array();

        //根据不同状态进行不同操作
        switch ($win_status) {
            case 1:
                $save_res = false;
                //收货地址,物流信息都存在,则直接存入到数据库，更新状态到3
                if($logistics_effect && $address_full) {
                    $save_data['address_data'] = $address_data;
                    $save_data['logistics_company'] = $logistics_company;
                    $save_data['logistics_number'] = $logistics_number;
                    $save_data['logistics_status'] = 3;
                    $save_res = $this->win_record_model->where(array('id' =>$id))->save($save_data);

                    //插入到sp_prize_status表中,更新2次状态
                    $status_data = array();
                    $status_data['win_record_id'] = $id;
                    $status_data['status'] = 'confirm_address';
                    $status_data['status_info'] = '确认收货地址';
                    $status_data['create_time'] = time();

                    $PrizeStatus->add($status_data);

                    $status_data = array();
                    $status_data['win_record_id'] = $id;
                    $status_data['status'] = 'send_goods';
                    $status_data['status_info'] = '商品派发';
                    $status_data['create_time'] = time();

                    $PrizeStatus->add($status_data);

                    return $save_res !== false;

                }
                //只存在收货地址，更新状态到2
                if($address_full) {
                    $save_data['address_data'] = $address_data;
                    $save_data['logistics_status'] = 2;
                    $save_res = $this->win_record_model->where(array('id' =>$id))->save($save_data);

                    //插入到sp_prize_status表中,更新2次状态
                    $status_data = array();
                    $status_data['win_record_id'] = $id;
                    $status_data['status'] = 'confirm_address';
                    $status_data['status_info'] = '确认收货地址';
                    $status_data['create_time'] = time();

                    $PrizeStatus->add($status_data);

                    return $save_res !== false;
                }

                //都没填，返回true
                return true;

                break;
            case 2:
                //状态为2，即已经有收货地址，所以传过来的收货地址不得为空
                if($address_null) {
                    wrong_return('请填写收货地址');
                }

                //如果地址与物流信息都传过来，更新状态到3
                if($address_full && $logistics_effect) {
                    $save_data['address_data'] = $address_data;
                    $save_data['logistics_company'] = $logistics_company;
                    $save_data['logistics_number'] = $logistics_number;
                    $save_data['logistics_status'] = 3;
                    $save_res = $this->win_record_model->where(array('id' =>$id))->save($save_data);

                    $status_data = array();
                    $status_data['win_record_id'] = $id;
                    $status_data['status'] = 'send_goods';
                    $status_data['status_info'] = '商品派发';
                    $status_data['create_time'] = time();

                    $PrizeStatus->add($status_data);

                    return $save_res !== false;
                }

                //如果只传过来地址信息,则直接保存，不须变更状态
                if($address_full && !$logistics_effect) {
                    $save_data['address_data'] = $address_data;
                    $save_res = $this->win_record_model->where(array('id' =>$id))->save($save_data);
                    return $save_res !== false;
                }

                return true;

                break;
            case 3:

                //状态为3，说明地址信息，物流信息都有,
                if($address_null) {
                    wrong_return('请填写地址信息');
                }
                if(!$logistics_effect) {
                    wrong_return('请填写物流信息');
                }

                //直接保存即可
                $save_data['address_data'] = $address_data;
                $save_data['logistics_company'] = $logistics_company;
                $save_data['logistics_number'] = $logistics_number;
                $save_res = $this->win_record_model->where(array('id' =>$id))->save($save_data);

                return $save_res !== false;


                break;
            default:
                return true;
        }


    }
    public function win_del($id){
        return $this->win_record_model->where(['id'=>['IN',$id]])->delete();
    }
    public  function update_read_flag($id)
    {
        $m = M('win_record', 'sp_');
        $m->where(array('id'=>$id))->save(array('read_flag'=>1));

    }

    public function get_goods($id) {
        return $this->win_record_model
            ->alias('w')
            ->field('w.nper_id,g.name,i.img_path')
            ->where(array('w.id'=>$id))
            ->join('goods g','g.id =w.goods_id ')
            ->join('image_list i','i.id=g.pic_list')
            ->select()[0];

    }



}