<?php

namespace app\mobile\model;


use think\Model;

class Goods extends Base
{

    private $exist_search_type = array(1 => 'hot', 2 => 'new', 3 => 'remain_num', 4 => 'total_asc', 5 => 'total_desc');

    /**
     * 首页轮播图片
     * @return array
     */
    public function home_carouse()
    {
        $HomeCarouse = M('home_carouse');
        $carouse_list = $HomeCarouse->alias('h')->field('h.image_id,h.content,h.type,h.flag,h.title,h.sort,i.img_path')->join('image_list i','i.id = h.image_id','LEFT')->where("flag = 1 AND platform = 2")->order("sort desc")->select();

        $website_url = __STATIC_PREFIX__;
        if ($carouse_list) {
            foreach ($carouse_list as $key => $value) {
                $carouse_list[$key]['src'] = $value['img_path'] ? $website_url.$value['img_path'] : $website_url.'/data/img/noPicture.jpg';
            }
        }
        return $carouse_list;

    }

    /**
     * 获取首页最新上架商品
     * @return array
     */
    public function get_index_new_goods()
    {
        $NperList = M('nper_list');
        $goods_info = $NperList->alias('n')
            ->join('goods g', 'n.pid = g.id')
            ->join('deposer_type d', 'n.deposer_type = d.id',"LEFT")
            ->join('image_list i','i.id = g.index_img',"LEFT")
            ->field('i.img_path,n.id as nper_id,n.init_times,n.unit_price,n.min_times,n.status,n.sum_times,n.deposer_type,n.participant_num,n.create_time as nper_create_time,g.id as good_id,g.name,g.index_img,g.buy_times,d.name as deposer_name,d.code')
            ->order('g.create_time DESC')
            ->where('n.status = 1 AND n.nper_type=1')
            ->limit(3)
            ->select();
        if ($goods_info) {
            $goods_info = $this->get_index_supplementary($goods_info);
        }
        return $goods_info;

    }

    /**
     * 获取首页今日热门商品
     * @return mixed
     */
    public function get_index_hot_goods()
    {
        $NperList = M('nper_list');
        $goods_info = $NperList->alias('n')
            ->join('goods g', 'n.pid = g.id')
            ->join('deposer_type d', 'n.deposer_type = d.id')
            ->join('image_list i ','i.id = g.index_img',"LEFT")
            ->field('g.is_zero,i.img_path,n.id as nper_id,n.init_times,n.unit_price,n.min_times,n.status,n.sum_times,n.deposer_type,n.participant_num,n.create_time as nper_create_time,g.id as good_id,g.name,g.index_img,g.buy_times,d.name as deposer_name,d.limit_num,d.code')
            ->order('g.buy_times DESC')
            ->where('n.status = 1')
            ->limit(16)
            ->select();

        if ($goods_info) {
            $goods_info = $this->get_index_supplementary($goods_info);
        }
        return $goods_info;
    }


    /**
     * 为getIndexNewGoods getIndexHotGoods 两个方法提供简化
     * @param $goods_info
     */
    private function get_index_supplementary($goods_info)
    {
        $website_url = __STATIC_PREFIX__;
        foreach ($goods_info as $key => $value) {

            $goods_info[$key]['image_src'] = $value['img_path'] ? $website_url.$value['img_path'] : $website_url.'/data/img/noPicture.jpg';
            $goods_info[$key]['progress'] = floor(((int)$value['participant_num']  / (int)$value['sum_times']) * 10000) / 100;
            $goods_info[$key]['remain_num'] = $value['sum_times'] - $value['participant_num'];


        }

        return $goods_info;

    }


    /**
     * 图文详情
     * @param $goods_id
     * @return mixed
     */
    public function graphic_details($goods_id)
    {

        return $this->field('detail')->where(array('id' => $goods_id))->find()['detail'];

    }

    /**
     * 最新揭晓
     * @return mixed
     */
    public function latest_announcement()
    {
        $website_url = __STATIC_PREFIX__;

        $NperList = M('nper_list');
        $goods_info = $NperList->alias('n')
            ->join('goods g', 'n.pid = g.id')
            ->join('deposer_type d', 'd.id=g.deposer_type')
            ->join('image_list i','i.id = g.index_img',"LEFT")
            ->field('n.id as nper_id,n.open_time,g.index_img,d.code,i.img_path,n.nper_type')
            ->order('n.open_time DESC')
            ->where('n.status = 2  AND n.open_time > ' . NOW_TIME)
            ->limit(3)
            ->select();
        if ($goods_info) {
            foreach ($goods_info as $key => $value) {
                $goods_info[$key]['image_src'] = $value['img_path'] ? $website_url.$value['img_path'] : $website_url.'/data/img/noPicture.jpg';
                //倒计时差
                $goods_info[$key]['count_down'] = 1000 * ($value['open_time'] - time());

            }
        }
        return $goods_info;
    }

    /**
     * 全部商品信息
     * @param $cate
     * @param $type
     * @return array|bool|mixed
     */
    public function all_goods($cate, $type, $offset,$mid="")
    {
        //判断所传种类是否数据库种类范围之内，否则默认为0（所有）
        $CategoryList = M('category_list');
        $map['status'] = 1;
        if(!empty($mid)){
            $map['mid'] = $mid;
        }
        $cate_list = $CategoryList->field('id')->where($map)->order('orders DESC')->select();

        $cate_exist = array();
        foreach ($cate_list as $key => $value) {
            $cate_exist[] = $value['id'];
        }

        if (!in_array($cate, $cate_exist)) {
            $cate = 0;
        }

        //判断所传类型是否在允许范围之内,否则默认为1

        if (empty($this->exist_search_type[$type])) {
            $type = 1;
        }

        $search_type = $this->exist_search_type[$type];
        switch ($search_type) {
            case 'hot':
                $order = 'buy_times';
                break;
            case 'new':
                $order = 'nper_create_time';
                break;
            case 'remain_num':
                $order = 'remain_num';
                break;
            case 'total_desc' :
                $order = 'sum_times desc';
                break;
            case 'total_asc' :
                $order = 'sum_times asc';
                break;
            default:
                return false;
        }

        $NperList = M('nper_list');

        //根据$cate是否为0进行查询
        $goods_info = array();
        if ($cate == 0) {
            $goods_info = $NperList->alias('n')
                ->join('goods g', 'n.pid=g.id')
                ->join('deposer_type d', 'd.id = g.deposer_type')
                ->field('n.id as nper_id,n.init_times,n.unit_price,n.min_times,n.status,n.sum_times,n.deposer_type,n.participant_num,n.create_time as nper_create_time,g.id as good_id,g.name,g.index_img,g.buy_times,d.code,n.sum_times-n.participant_num as remain_num')
                ->where('n.status = 1 AND g.status=1 AND n.nper_type=1')
                ->order($order)
                ->limit($offset, 10)
                ->select();
        } else {
            $goods_info = $NperList->alias('n')
                ->join('goods g', 'n.pid=g.id')
                ->join('deposer_type d', 'd.id = g.deposer_type')
                ->field('n.id as nper_id,n.init_times,n.unit_price,n.min_times,n.status,n.sum_times,n.deposer_type,n.participant_num,n.create_time as nper_create_time,g.id as good_id,g.name,g.index_img,g.buy_times,d.code,n.sum_times-n.participant_num as remain_num')
                ->where('n.status = 1 AND n.nper_type=1 AND g.status=1 AND g.category =' . $cate)
                ->order($order)
                ->limit($offset, 10)
                ->select();
        }

        $search_type = $this->exist_search_type[$type];
        if ($goods_info) {
            foreach ($goods_info as $key => $value) {

                $goods_info[$key]['image_src'] = $this->get_one_image_src($value['index_img']);
                $goods_info[$key]['progress'] = floor(((int)$value['participant_num']  / (int)$value['sum_times']) * 10000) / 100;
            }
        }

        //传回search_type用来前端显示字体红色
        return array(
            'search_type' => $type,
            'goods_info' => $goods_info
        );
    }


    /**
     * ajax请求商品数量
     * @param $cate
     * @param $type
     * @return array|bool|mixed
     */
    public function all_goods_count($cate, $type)
    {
        //判断所传种类是否数据库种类范围之内，否则默认为0（所有）
        $CategoryList = M('category_list');
        $cate_list = $CategoryList->field('id')->where('pid =1 AND status = 1')->order('orders DESC')->select();
        $cate_exist = array();
        foreach ($cate_list as $key => $value) {
            $cate_exist[] = $value['id'];
        }

        if (!in_array($cate, $cate_exist)) {
            $cate = 0;
        }


        //判断所传类型是否在允许范围之内,否则默认为1

        if (empty($this->exist_search_type[$type])) {
            $type = 1;
        }

        $NperList = M('nper_list');


        //根据$cate是否为0进行查询
        $goods_num = 0;
        if ($cate == 0) {
            $goods_num = $NperList->field('id')->where('status = 1 AND nper_type=1')->count();
        } else {
            $goods_num = $NperList->alias('n')->join('goods g', 'n.pid=g.id')->field('n.id')->where('n.status = 1 AND n.nper_type=1 AND g.category =' . $cate)->count();
        }

        return $goods_num;


    }


    /**
     * 往期揭晓
     * @param $goods_id
     * @return mixed
     */
    public function before_announce($goods_id, $page = 0)
    {
        $NperList = M('nper_list');
        $announce_info = $NperList
            ->alias('n')
            ->join('users u', 'n.luck_uid = u.id')
            ->join('order_list o', 'n.luck_order_id = o.order_id')
            ->join('image_list i', 'i.id=u.user_pic',"LEFT ")
            ->field('n.luck_time,n.status n_status,i.img_path user_face,n.id as nper_id,n.luck_user,n.status,n.luck_uid,n.luck_num,n.open_time,o.num,u.user_pic,u.reg_ip,u.nick_name')
            ->where('(n.status = 2 OR n.status = 3) AND n.nper_type=1 AND n.pid = ' . $goods_id)
            ->order('n.open_time','desc')
            ->limit($page, 10)
            ->select();
        $announce_info = $this->format_luck_num($announce_info);
        return $announce_info;


    }

    private function format_luck_num($announce_info) {
        foreach ( $announce_info as $key => $info ) {
            $announce_info[$key]['luck_num'] =num_base_mask(intval($info['luck_num']),1,0);
        }
        return $announce_info;

    }

    //根据期数获取商品ID
    public function get_gid_by_nper_id($nper_id) {
        $nper = M('nper_list')->where(array('id'=>$nper_id))->find();
        if( is_null($nper) )
            return false;
        if ( $nper['status'] == 1 )
            return false;
        $nper = M('nper_list')->field('id')->where(array('pid'=>$nper['pid'],'status'=>1))->find();
        return isset($nper['id'])?$nper['id']:false;

    }


    /**
     * 商品详情
     * @param $nper_id
     * @return array|mixed
     */
    public function goods_detail($nper_id)
    {
        $NperList = M('nper_list');

        $User = M('users');
        $OrderList = D('order_list');
        $Luck_Num = M('luck_num');


        $status = $NperList->field('status')->where(array("id" =>  $nper_id))->find()['status'];

        $goods_info = array();
        switch ($status) {
            case '1' :
                $goods_info = $NperList->alias('n')
                    ->join('goods g', 'n.pid = g.id','LEFT')
                    ->join('deposer_type d', 'n.deposer_type = d.id','LEFT')
                    ->join("users u","u.id = luck_uid",'LEFT')
                    ->field('u.nick_name,n.nper_type,n.id as nper_id,n.status,n.sum_times,n.deposer_type,n.init_times,n.unit_price,n.min_times,n.participant_num,n.create_time as nper_create_time,g.id as goods_id,g.name,g.pic_list,d.name as deposer_name,d.code,n.odd_join_num,n.even_join_num')
                    ->where(array('n.id' => $nper_id))
                    ->find();

                if (!empty($goods_info['pic_list'])) {
                    $goods_info['img_path'] = $this->get_more_image_src($goods_info['pic_list']);
                }

                $goods_info['progress'] = floor(((int)$goods_info['participant_num']  / (int)$goods_info['sum_times']) * 10000) / 100;
                $goods_info['odd_process']=round((float)$goods_info['odd_join_num']/(float)$goods_info['sum_times'], 2) * 100;
                $goods_info['even_process']=round((float)$goods_info['even_join_num']/(float)$goods_info['sum_times'], 2) * 100;
                break;
            case '2' :
                $goods_info = $NperList->alias('n')
                    ->join('goods g', 'n.pid = g.id','LEFT')
                    ->join('deposer_type d', 'n.deposer_type = d.id','LEFT')
                    ->join("users u","u.id = luck_uid",'LEFT')
                    ->field('u.nick_name,n.nper_type,n.id as nper_id,n.init_times,n.unit_price,n.min_times,n.status,n.deposer_type,n.create_time as nper_create_time,n.open_time,g.id as goods_id,g.name,g.pic_list,d.name as deposer_name,d.code')
                    ->where(array('n.id' => $nper_id))
                    ->find();


                $goods_info['img_path'] = $this->get_more_image_src($goods_info['pic_list']);

                $countdown = 1000 * ($goods_info['open_time'] - time());
                $goods_info['countdown'] = $countdown > 0 ? $countdown : 0;

                break;
            case '3' :
                $goods_info = $NperList->alias('n')
                    ->join('goods g', 'n.pid = g.id','LEFT')
                    ->join('deposer_type d', 'n.deposer_type = d.id','LEFT')
                    ->join('order_list o', 'o.order_id = n.luck_order_id','LEFT')
                    ->join("users u","u.id = luck_uid",'LEFT')
                    ->field('u.nick_name,n.nper_type,n.id as nper_id,n.init_times,n.unit_price,n.min_times,n.status,n.deposer_type,n.sum_times,n.create_time as nper_create_time,n.open_time as nper_open_time,n.luck_user,n.luck_order_id,n.luck_num,luck_uid,g.id as goods_id,g.name,g.pic_list,d.name as deposer_name,o.num as luck_join_num,d.code')
                    ->where(array('n.id' => $nper_id))
                    ->find();

                //测试阶段，数据不完善，导致取出来的数据可能为空
                if (!$goods_info) {
                    return array();
                }

                $user_face_id = $goods_info['luck_uid'] ? $User->field('user_pic')->where(array("id" =>  $goods_info['luck_uid']))->find()['user_pic'] : '';
                if ($user_face_id) {
                    $goods_info['user_face'] = $this->get_one_image_src($user_face_id);
                } else {
                    $goods_info['user_face'] = $_SERVER['SERVER_NAME'] . '/uploads/user_face.jpg';
                }


                $goods_info['img_path'] = $goods_info['pic_list'] ? $this->get_more_image_src($goods_info['pic_list']) : '';


                $goods_info['announce_time'] = date('Y-m-d H:i:s', $goods_info['nper_open_time']);

                //查询出得奖者的号码
                if($goods_info['nper_type']==1) {
                    $win_code_list = $Luck_Num->field('code_list')->where('nper_id =' . $nper_id . ' AND uid =' . $goods_info['luck_uid'] . ' AND join_type=0')->select();
                    $win_code_arr = array();
                    if ($win_code_list) {
                        foreach ($win_code_list as $value) {
                            $win_code_arr = array_merge_recursive($win_code_arr, explode(',', substr($value['code_list'], 1, -1)));
                        }
                    }
                    //转化号码格式
                    foreach ($win_code_arr as $key => $value) {
                        $win_code_arr[$key] = num_base_mask(intval($value),1,0);
                    }
                    $goods_info['win_code_list'] = $win_code_arr;
                    $goods_info['win_code_list_num'] = count($win_code_arr);
                }
                //0元购
                if($goods_info['nper_type']==2){
                    $zero_win_code_arr=[];
                    $zero_win_code_list=$Luck_Num->field('code_list')->where('nper_id =' . $nper_id . ' AND uid =' . $goods_info['luck_uid'] . ' AND join_type>0')->select();
                    if($zero_win_code_list){
                        foreach ($zero_win_code_list as $value) {
                            $zero_win_code_arr = array_merge_recursive($zero_win_code_arr, explode(',', substr($value['code_list'], 1, -1)));
                        }
                    }
                    foreach ($zero_win_code_arr as $key => $value) {
                        $zero_win_code_arr[$key] = num_base_mask(intval($value),1,0);
                    }
                    $goods_info['win_code_list']=$zero_win_code_arr;
                    $goods_info['win_code_list_num']=count($zero_win_code_arr);
                }

                break;


        }

        //日期格式转化
        $goods_info['nper_create_time'] = date('Y-m-d H:i:s', $goods_info['nper_create_time']);

        if($goods_info['nper_type']==1){
            $goods_info['participant_record'] = $OrderList->goods_details_order($goods_info['nper_id'], 0);
        }
        if($goods_info['nper_type']==2){
            $goods_info['odd_record'] = $OrderList->zero_goods_detail_order($goods_info['nper_id'], 0,1);
            $goods_info['even_record'] = $OrderList->zero_goods_detail_order($goods_info['nper_id'], 0,2);
        }



        //判断登录用户是否购买该期商品
        if (session('user')) {
            $uid = session('user')['id'];
            //一元夺宝
            if($goods_info['nper_type']==1) {
                $code_list = $Luck_Num->field('code_list')->where('nper_id =' . $nper_id . ' AND uid =' . $uid.' AND join_type=0')->select();
                $code_list_arr = array();
                if ($code_list) {
                    foreach ($code_list as $value) {
                        $code_list_arr = array_merge_recursive($code_list_arr, explode(',', substr($value['code_list'], 1, -1)));
                    }
                }
                //转化号码格式
                foreach ($code_list_arr as $key => $value) {

                    $code_list_arr[$key] = num_base_mask(intval($value),1,0);
                }
                $goods_info['code_list_num'] = count($code_list_arr);

                if ($goods_info['code_list_num'] >= 6) {
                    $goods_info['code_list_less'] = array_slice($code_list_arr, 0, 6);
                }
                $goods_info['code_list'] = $code_list_arr;
            }
            if($goods_info['nper_type']==2){
                $odd_code_list = $Luck_Num->field('code_list')->where('nper_id =' . $nper_id . ' AND uid =' . $uid.' AND join_type=1')->select();
                $even_code_list = $Luck_Num->field('code_list')->where('nper_id =' . $nper_id . ' AND uid =' . $uid.' AND join_type=2')->select();
                $odd_code_list_arr = array();
                $even_code_list_arr=[];
                if ($odd_code_list) {
                    foreach ($odd_code_list as $value) {
                        $odd_code_list_arr = array_merge_recursive($odd_code_list_arr, explode(',', substr($value['code_list'], 1, -1)));
                    }
                }
                if ($even_code_list) {
                    foreach ($even_code_list as $value) {
                        $even_code_list_arr = array_merge_recursive($even_code_list_arr, explode(',', substr($value['code_list'], 1, -1)));
                    }
                }
                //转化号码格式
                foreach ($odd_code_list_arr as $key => $value) {

                    $odd_code_list_arr[$key] = num_base_mask(intval($value),1,0);
                }
                foreach ($even_code_list_arr as $key => $value) {

                    $even_code_list_arr[$key] = num_base_mask(intval($value),1,0);
                }
                $goods_info['odd_code_list_num'] = count($odd_code_list_arr);
                $goods_info['even_code_list_num'] = count($even_code_list_arr);


                if ($goods_info['odd_code_list_num'] >= 6) {
                    $goods_info['odd_code_list_less'] = array_slice($odd_code_list_arr, 0, 6);
                }
                if ($goods_info['even_code_list_num'] >= 6) {
                    $goods_info['even_code_list_less'] = array_slice($even_code_list_arr, 0, 6);
                }
                $goods_info['odd_code_list'] = $odd_code_list_arr;
                $goods_info['even_code_list'] = $even_code_list_arr;
            }
        } else {
            if($goods_info['nper_type']==1){
                $goods_info['code_list'] = array();
            }
            if($goods_info['nper_type']==2){
                $goods_info['odd_code_list']=[];
                $goods_info['even_code_list']=[];
            }

        }
        if (isset($goods_info['luck_num'])) {
            $goods_info['luck_num'] =num_base_mask(intval($goods_info['luck_num']),1,0);
        }

        return $goods_info;


    }


    /**
     * 获取搜索内容
     * @param $search
     * @return array
     */

    public function get_search_goods($search)
    {
        $search = str_replace('+',' ',$search);
        if (empty($search)) {
            return array();
        }
        $NperList = M('nper_list');
        $condition['g.name'] = array('like', "%$search%");
        $condition['n.status'] = 1;
        $condition['n.nper_type']=1;
        $condition['g.status'] = 1;
        $goods_info = $NperList->alias('n')->join('goods g', 'n.pid=g.id')->field('n.id as nper_id,n.sum_times,n.participant_num,g.index_img,g.name,g.search_times,g.id')->where($condition)->select();

        foreach ($goods_info as $key => $value) {
            $goods_info[$key]['image_src'] = $this->get_one_image_src($value['index_img']);
            $goods_info[$key]['remain_num'] = $value['sum_times'] - $value['participant_num'];
            $goods_info[$key]['percent_bar'] = ($value['participant_num'] / $value['sum_times']) * 100;
            for ($i = 0; $i <= 2; $i++) {
                $data['id'] = $goods_info[$key]['id'];
                $data['search_times'] = $goods_info[$key]['search_times'] + 1;
                $this->save($data);
            }
        };


        return $goods_info;
    }


    public function get_hot_search()
    {
        $result = $this->field('name')->where(array('status'=>1))->order('search_times desc')->limit(4)->select();
        foreach ($result as $index => $item) {
            $result[$index]['url_name'] = urlencode($item['name']);
            
        }
        return $result;
    }


    /**
     * 中奖提示
     * @return array|mixed
     */
    public function win_notice()
    {

        if (!session('user')) {
            return array();
        }

        $uid = session('user')['id'];
        $WinRecord = M('win_record');
        $OrderList = M('order_list');
        $win_record = $WinRecord->field('id as win_record_id,nper_id,goods_id,luck_num')->where('notice = 0 AND luck_uid =' . $uid)->order('id desc')->find();

        if ($win_record && !empty($win_record['nper_id'])) {
            //将该中奖的商品信息放入到结果数组里面
            $goods_info = $OrderList
                ->alias('o')
                ->join('nper_list n', 'o.nper_id = n.id')
                ->join('goods g', 'n.pid = g.id')
                ->field('n.status,n.participant_num,n.sum_times,n.open_time,n.luck_uid,n.luck_user,g.index_img,g.id as good_id,g.name,o.num as join_num')
                ->where("o.uid=" . $uid . " AND n.id = " . $win_record['nper_id'])->find();

            if (empty($goods_info)) {
                return array();
            }

            if ($goods_info['index_img']) {
                $goods_info['img_path'] = $this->get_one_image_src($goods_info['index_img']);
            }
            $win_record = array_merge($win_record, $goods_info);
            return $win_record;

        } else {
            return array();
        }

    }


}


