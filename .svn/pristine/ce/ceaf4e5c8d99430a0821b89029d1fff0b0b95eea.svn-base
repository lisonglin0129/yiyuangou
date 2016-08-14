<?php
namespace app\core\model;
use app\admin\model\PromotModel;

/**商品model*/
Class GoodsModel extends CommonModel
{
    private $m_goods;
    private $m_nper;

    public function __construct()
    {
        parent::__construct();
        $this->m_goods = M('goods', 'sp_');
        $this->m_nper = M('nper_list', 'sp_');
    }

    //根据id获取商品详情
    public function get_goods_info_by_id($id)
    {
        $good = $this->m_goods
            ->table('sp_goods g')
            ->field('g.*,d.code')
            ->join('sp_deposer_type d ON d.id=g.deposer_type')
            ->where(array('g.id' => $id))->find();
        return $good;
    }

    //根据id获取商品期数详情
    public function get_nper_info_by_id($id)
    {
        $sql = 'SELECT n.*,de.name de_name,de.code FROM sp_nper_list n
                LEFT JOIN sp_deposer_type de ON n.deposer_type = de.id
                WHERE n.id=' . $id;
        $info = $this->m_nper->query($sql);
        return empty($info[0]) ? false : $info[0];
    }

    //根据期号id获取商品期数详情(包括获奖用户信息/头像)
    //用于刷新最新揭晓信息
    public function get_nper_detail_by_id($id)
    {
        $where = ['n.id'=>$id];//,'n.open_time'=>['gt',time()]
        $result = $this->m_nper
            ->table('sp_nper_list n')
            ->field('SQL_CALC_FOUND_ROWS
                     n.id nper_id,n.luck_uid,n.luck_user,n.luck_num,n.open_time,n.status,
                     g.name goods_name,g.id goods_id,i2.img_path goods_image,g.sum_times,
                     i.img_path avatar,u.nick_name,
                     o.num luck_o_num ')
            ->join('sp_goods g ON g.id = n.pid', 'LEFT')
            ->join('sp_users u ON u.id = n.luck_uid', 'LEFT')
            ->join('sp_image_list i ON i.id = u.user_pic', 'LEFT')
            ->join('sp_image_list i2 ON i2.id = g.index_img', 'LEFT')
            ->join('sp_order_list o ON o.order_id = n.luck_order_id','LEFT')
            ->where($where)
            ->find();
        return $result;
    }

    //获取当前商品最新一期的信息
    public function get_last_nper_info_by_gid($gid)
    {
        $result = $this->m_goods
            ->table('sp_goods g')
            ->field('SQL_CALC_FOUND_ROWS n.id,n.pid,g.name,g.id gid,n.sum_times,n.participant_num,(n.sum_times-n.participant_num) remain,i.img_path,g.sub_title,n.init_times,n.unit_price,n.min_times')
            ->join('sp_nper_list n ON g.id = n.pid', 'LEFT')
            ->join('sp_image_list i ON i.id = g.index_img', 'LEFT')
            ->where(['g.id' => $gid])
            ->order('n.id desc')
            ->find();
        return $result;
    }

    //获取简单的商品信息
    public function get_simple_goods_by_gid($gid)
    {
        if (empty($gid)) return false;
        if (is_array($gid)) {
            $where = ['g.id' => ['in', $gid]];
        } else {
            $where = ['g.id' => $gid];
        }
        $result = $this->m_goods
            ->table('sp_goods g')
            ->field('SQL_CALC_FOUND_ROWS g.name,g.id,i.img_path,g.sub_title')
            ->join('sp_image_list i ON i.id = g.index_img', 'LEFT')
            ->where($where)
            ->select();
        return $result;
    }

    //获取当前商品id未开奖状态期数的详情
    public function get_no_lottory_nper_info_by_pid($pid)
    {
        return $this->m_nper->where(array('status' => '1', 'pid' => $pid,'nper_type'=>1))->find();
    }
    //获取0元购商品id未开奖期数详情
    public function get_zero_no_lottory_nper_id($pid)
    {
        return $this->m_nper->where(array('status' => '1', 'pid' => $pid,'nper_type'=>2))->getField('id');
    }
    public function get_zero_no_lottory_info($pid){
        return $this->m_nper->where(array('status' => '1', 'pid' => $pid,'nper_type'=>2))->find();
    }

    //获取商品（夺宝）列表
    public function get_list_by_category($cate_id = 0, $size = 36, $page = 1, $order = 0, $deposer_type = false)
    {
        $where['g.status']=1;
        $where['n.status']=1;
        $where['n.nper_type']=['neq',2];
//        $where['g.mid'] = __MID__;//MID-----
        if (intval($cate_id) !== 0) {
            $where['g.category'] = $cate_id;
        }
        if ($deposer_type !== false) {
            $where['g.deposer_type'] = $deposer_type;
        }
        $order_types = [
            0 => 'g.buy_times desc',    //人气商品
            1 => 'remain asc',      //剩余人次
            2 => 'g.create_time desc',    //最新商品
            3 => 'g.sum_times asc',    //总需人次asc
            4 => 'g.sum_times desc',    //总需人次desc
        ];
        //ALIAS:
        //n:    nper_list
        //g:    goods
        //i:    image_list
        if ($size > 0) {
            $result = $this->m_goods
                ->table('sp_goods g')
                ->field(
                    'SQL_CALC_FOUND_ROWS n.id,n.pid,g.name,g.id gid,g.is_zero,n.sum_times,n.participant_num,
                    (n.sum_times-n.participant_num) remain,i.img_path,g.sub_title,n.init_times,n.unit_price,n.min_times,d.code'
                )
                ->join('sp_nper_list n ON g.id = n.pid', 'LEFT')
                ->join('sp_image_list i ON i.id = g.index_img', 'LEFT')
                ->join('sp_deposer_type d ON d.id=g.deposer_type')
                ->where($where)
                ->order($order_types[$order])
                ->page($page, $size)
                ->select();
        } else {
            $result = $this->m_goods
                ->table('sp_goods g')
                ->field(
                    'SQL_CALC_FOUND_ROWS n.id,
                    n.pid,g.name,g.id gid,n.sum_times,n.participant_num,(n.sum_times-n.participant_num) remain,i.img_path,g.sub_title,n.init_times,n.unit_price,n.min_times,code'
                )
                ->join('sp_nper_list n ON g.id = n.pid', 'LEFT')
                ->join('sp_deposer_type d ON d.id=g.deposer_type')
                ->join('sp_image_list i ON i.id = g.index_img', 'LEFT')
                ->where($where)
                ->order($order_types[$order])
                ->select();
        }
        if ($result) {
            $count = $this->m_goods->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //搜索商品
    public function search_goods_by_name($keyword)
    {
        //通配符过滤
        $keyword = '%' . str_replace(['%', '_'], ['\%', '\_'], $keyword) . '%';
        $where = ['n.status' => 1, 'g.name' => ['like', $keyword],'g.status'=>1,'n.nper_type'=>1];
//        $where['g.mid'] = __MID__;//MID分类 的东西-----
        $result = $this->m_goods
            ->table('sp_goods g')
            ->field('SQL_CALC_FOUND_ROWS n.id,n.pid,g.name,g.id gid,n.sum_times,n.participant_num,i.img_path,g.sub_title,g.unit_price,g.min_times,g.max_times,g.init_times')
            ->join('sp_nper_list n ON g.id = n.pid', 'LEFT')
            ->join('sp_image_list i ON i.id = g.index_img', 'LEFT')
            ->where($where)->select();
        if ($result) {
            $count = $this->m_goods->query('select FOUND_ROWS() c');
            return [$result, $count[0]['c']];
        } else {
            return [$result, 0];
        }
    }

    //获取推广的商品列表
    public function get_promo_goods($type, $mid = null)
    {
        $m_promo = M('promo', 'sp_');
        $where = $mid == null ? ['c.code' => $type] : ['c.type' => $type, 'p.mid' => $mid];
        //$where = $category==null?" AND p.type={$type}":" AND p.type={$type} AND p.category={$category}";
        $result = $m_promo->table('sp_promo p')
            ->field('p.gid gid,p.url,p.category,il.img_path')
            ->join('sp_image_list il ON il.id = p.img', 'LEFT')
            ->join('sp_category_list c','c.id=p.type','LEFT')
            ->where($where)
            ->select();

        return $result;
    }

    //获取购物车商品数量
    public function get_cart_num()
    {
        $m_cart = M('shop_cart', 'sp_');
        $sql = 'SELECT COUNT(id) num FROM sp_shop_cart WHERE uid = ' . get_user_id();
        $r = $m_cart->query($sql);
        return $r[0]['num'];
    }

    //获取当前用户期号为nper_id的购物车信息
    public function get_user_cart_info_by_nper_id($nper_id,$join_type='')
    {
        $m_cart = M('shop_cart', 'sp_');
        return $m_cart->where(array('uid' => get_user_id(), 'nper_id' => $nper_id,'join_type'=>!empty($join_type)?$join_type:0))->find();
    }

    //更新购物车的商品信息
    public function update_cart_info($info)
    {
        $m_cart = M('shop_cart', 'sp_');
        extract($info);
        if (empty($nper_id) || empty($num)) return false;

        $data = array(
            "num" => $num,
        );
        $cart_info = $m_cart->where(array('uid' => get_user_id(), 'nper_id' => $nper_id,'join_type'=>!empty($join_type)?$join_type:0))->find();

        if ($cart_info) {
            $r = $m_cart->where(array('uid' => get_user_id(), "nper_id" => $nper_id,'join_type'=>!empty($join_type)?$join_type:0))->save($data);

            return $r !== false;
        } else {
            $data['uid'] = get_user_id();
            $data['nper_id'] = $nper_id;
            $data['create_time'] = NOW_TIME;
            $data['join_type']=!empty($join_type)?$join_type:0;
            return $m_cart->add($data);
        }

    }

    //根据ids获取用户购物车中的列表信息
    public function get_cart_list_by_ids($ids)
    {
        $m_cart = M('shop_cart', 'sp_');
        return $m_cart->where("uid=" . get_user_id() . " AND id IN (" . $ids . ")")->select();
    }

    /**
     * @param $id 期数ID
     * @param $uid 用户ID
     * @param $oid 订单ID
     * @param int $join_type 1为1元购,0为0元购
     * @return array
     */
    public function get_code_list($id,$uid,$oid,$join_type=1) {
        $where = [
            'l.nper_id'   => $id,
            'l.join_type' => 0,
            'l.uid'       => $uid,
            'o.id'        => $oid
        ];
        if ( $join_type === 0 ) {
            $where['join_type'] = ['neq'=>0];

        }
        $m = M('luck_num');
        $list = $m->field('code_list')->alias('l')->join('order_list o','o.order_id=l.order_id')->where($where)->find();
        $list['code_list'] = array_filter(explode(',',$list['code_list']));
        $list = $this->format_code_list($list);
        return $list['code_list'];

    }

    public function get_recomment_indiana() {
        $m_cate = new CategoryModel();
        $info = $m_cate->get_category_list_info_by_code('goods_indiana');
        if(!empty($info)){
            $m = M();
            $sql = 'select * from sp_promo WHERE type='.$info['id'].'  order by rand() limit 1';
            $goods = $m->query($sql);
            if(!empty($goods[0])){
                return $goods[0];
            }
            return false;
        }
    }

    public function format_code_list($list) {
        $arr = array();
        foreach ( $list['code_list'] as $key => $code ) {
            $arr[] = num_base_mask(intval($code),1,0);
        }

        $list['code_list'] = $arr;
        return $list;

    }

}