<?php
namespace app\admin\controller;

use app\admin\model\CardModel;
use app\admin\model\GoodsModel;
use app\admin\model\CategoryModel;
use app\admin\model\CommonModel;
use app\admin\model\HomeCarouseModel;
use app\admin\model\NperListModel;
use app\admin\model\PromotModel;
use app\admin\model\UsersModel;
use app\lib\Condition;
use app\lib\Page;
use app\lib\phpqrcode;
use app\lib\Tree;

/**
 * Created by PhpStorm.
 * User: phil
 * Date: 2016/5/7
 * Time: 15:55
 */
Class Goods extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->del_model = new GoodsModel();
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }
    //商品推荐
    public function recommend_add(){

            $m_order = new GoodsModel();
            $rt = $m_order->get_all_goods();
           // $info=['type'=>'','content'=>''];
            if(I('get.id')){
                $h=new HomeCarouseModel();
                $info=$h->get_recommend(I('get.id'));
                $this->assign('info',$info);
            }
            $this->assign('pros',$rt);
            $this->assign('type',I('get.type')?I('get.type'):'add');
            return $this->display();

    }
    //保存商品推荐
    public function recommend_update(){
        $m=new HomeCarouseModel();
        $post = I("post.");
        $post['mid']=11;
       if($post['content2']){
           $post['content']=$post['content2'];
       }elseif($post['content3']){
           $post['content']=$post['content3'];
       }else{
           $post['content']=$post['content1'];
       }
        $rt = $m->update_recommend($post);
        $rt !== false && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //删除商品推荐
    public function recommend_del(){
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new HomeCarouseModel();
        $m->del_rec($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }
    public function recommend_list(){
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'title',
                'value' => I('post.keywords')
            )
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $m = new HomeCarouseModel();
        $rec_list = $m->get_recommend_list($model);

        /*生成分页html*/
        $my_page = new Page($rec_list["count"], $this->page_num, $this->page, U('recommend_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('recommend_list', $rec_list['data']);
        return $this->fetch();
    }

    //列表
    public function show_list()
    {
        $keywords = null;
        $post = remove_arr_xss(I("post.", []));
        extract($post);
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'g.name',
                'operator' => 'LIKE',   //关系符号
                'value' => $keywords
            ),
            array(
                'field' => 'g.status',
                'operator' => '<>',   //关系符号
                'value' =>  -1
            )
        );
        $m_user=new UsersModel();
        if($shop_id=get_user_id()){
            $user_type=$m_user->get_user_type_by_id($shop_id);
            if($user_type==2){
                $condition_rule=array(
                    array(
                        'field' => 'g.name',
                        'operator' => 'LIKE',   //关系符号
                        'value' => $keywords
                    ),
                    array(
                        'field'=>'g.shop_id',
                        'value'=>$shop_id
                    ),
                    array(
                        'field' => 'g.status',
                        'operator' => '<>',   //关系符号
                        'value' => -1
                    )
                );
                $this->assign('user_type',$user_type);
            }
        }
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        //获取商品列表
        $m_order = new GoodsModel();
        $rt = $m_order->get_list($model);

        /*生成分页html*/
        $my_page = new Page($rt["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $m_com = new CommonModel();
        $domain_base = $m_com->get_conf('WEBSITE_URL');
        $this->assign('domain_base', $domain_base);
        $this->assign('pages', $pages);
        $this->assign('list', $rt['data']);
        return $this->fetch();
    }
////二维码
//    public function goods_qr(){
//        return $this->fetch();
//    }

    //查看/编辑/新增
    public function exec()
    {
        $m = new GoodsModel();
        $c_model=new \app\core\model\CommonModel();
        $zero_start=$c_model->get_conf('zero_start');
        $this->assign('zero_start',!empty($zero_start)?$zero_start:'');
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);
        $from_zero=I('get.from_zero',null);
        $this->assign('from_zero',$from_zero);

        //获取商品分类
        $m_cat = new CategoryModel();
        $cat_list = $m_cat->get_list_by_code_mid('xiangchang');
        $tree = new Tree();
        $cat_list = $tree->toFormatTree($cat_list, 'name');
        $this->assign('cat_list', $cat_list);

        //获取夺宝类型分类
        $deposer_list = $m->get_deposer_type_list();
        $this->assign('deposer_list', $deposer_list);

//        //获取平台类型分类
//        $category_list = $m->get_plat_list();
//        $this->assign('category_list', $category_list);

        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $m = new GoodsModel();
            $info = $m->get_info_by_id($id);
            empty($info) && die('获取信息失败');

            //获取图片地址列表
            $img_list = $m->get_img_list($info['pic_list']);
            $this->assign('img_list', $img_list);

            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
            } else {
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }

        $card_model=new CardModel();
        if($cats=$card_model->category_list_simple()){
            $this->assign('reward_data',$cats);
        }
        return $this->fetch('form');
    }

    //执行添加菜单
    public function update()
    {
		//ini_set("display_errors", "On");
		//error_reporting(E_ALL);
        $post = I("post.", []);
        $sec_open=null;
        extract($post);
		
        //标题
        !empty($id) && !is_numeric($id) && wrong_return('参数不正确(ID)');
        empty($name) && wrong_return('标题不能为空');
        empty($sub_title) && wrong_return('副标题不能为空');
        empty($category) && wrong_return('分类不能为空');

        $m_goods = new GoodsModel();
        empty($deposer_type) && wrong_return('夺宝类型不能为空');
        $deposer_info = $m_goods->get_deposer_info_by_id($deposer_type);
        empty($deposer_info) && wrong_return('夺宝类型获取失败');
        (empty($deposer_info['unit_price']) || !is_numeric($deposer_info['unit_price'])) && wrong_return('夺宝单价获取失败');
        (empty($deposer_info['limit_num']) || !is_numeric($deposer_info['limit_num'])) && wrong_return('每次最少购买次数获取失败');
        $unit_price = $deposer_info['unit_price'];
        $min_times = $deposer_info['limit_num'];
		

        //商品类型
        $m_cat = new CategoryModel();
        $cat_info = $m_cat->get_list_info_by_id($category);
        empty($cat_info["mid"]) && wrong_return("分类MID获取失败");
        $mid = $cat_info["mid"];

        //图片
        empty($pic_list) && wrong_return("上传图片不能为空");
        $pic_list = str_implode($pic_list, ',');
        empty($pic_list) && wrong_return("上传图片不能为空");
        empty($index_img) && wrong_return("您还没有设置主图");

        empty($price) && wrong_return('商品总价不能为空');
        //配置红包人数／金额验证
        if(!empty($is_packet)){
            empty($packet_num)&&wrong_return('红包领取人数不能空');
            !is_numeric($packet_num)&&wrong_return('参数不对');
            empty($packet_money)&&wrong_return('红包金额不能空');
        }
        !is_numeric($price) && wrong_return('价格必须为数字');
        ((float)$price <= $unit_price || $price >= 10000000) && wrong_return('商品价格范围应该在' . $unit_price . '~9999999元之间');

        !in_array($status, [1, -2]) && wrong_return('状态不正确');

        //每次限制购买次数
        if (empty($init_times) || ((int)$init_times <= 0)) {
            $init_times = $min_times;
        } else {
            //初始化购买次数大于总价 等于总价
            $init_times = intval($init_times) * $min_times;
            if ((int)$init_times > $price) {
                $init_times = $price;
            }
        }
		

        //最少购买次数
        if (empty($min_times) || ((int)$min_times <= 0)) {
            $min_times = 1;
        } else {
            $min_times = intval($min_times);
        }

        //计算总购买次数:商品总价/单价,进位取整
        $sum_times = ceil((float)$price / (float)$unit_price);
        //判断用户类型 商家 插入商家id
        $m_user=new UsersModel();
        if($shop_id=get_user_id()){
            $user_type=$m_user->get_user_type_by_id($shop_id);
        }
        //如果全部正确,执行写入数据
        $data = array(
            "id" => !empty($id) ? $id : "",
            "status" => $status,
            "name" => $name,
            "sub_title" => $sub_title,
            "category" => $category,
            "mid" => $mid,//商品类别的MID
            "deposer_type" => $deposer_type,
            "init_times" => $init_times,
            "unit_price" => $unit_price,
            "min_times" => $min_times,
            "sum_times" => $sum_times,
            "max_times" => empty($max_times) ? 99999999 : intval($max_times),
            "num" => '99999',
            "index_img" => $index_img,
            "pic_list" => $pic_list,
            "price" => $price,
            "buy_price" => $price,
            "detail" => empty($detail) ? "" : $detail,
            "remarks" => empty($remarks) ? "" : $remarks,
            "shop_id"=>($user_type==2)?$shop_id:0,
            "is_zero"=>empty($is_zero)?0:1,
            "reward_type"=>empty($reward_type)?0:$reward_type,
            "reward_data"=>empty($reward_data)?0:$reward_data,
            "sec_open"=>$sec_open,
            "is_packet"=>!empty($is_packet)?$is_packet:'',
            "packet_num"=>!empty($packet_num)?$packet_num:'',
            "packet_money"=>!empty($packet_money)?$packet_money:'',
        );
        $m = new GoodsModel();
		
        $rt = $m->update($data);
		
        $rt !== false && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }


    public function sel_plat()
    {
        $id = I("post.id", '');
        empty($id) && wrong_return("参数不正确");
        //获取该mid下的全部菜单
        $m_cat = new CategoryModel();
        $list = $m_cat->get_list_by_mid($id);
        $this->assign("list", $list);
        $data = array(
            "code" => "1",
            "html" => $this->fetch()
        );
        die_json($data);
    }
    //下载二维码图片
    public function down_pic(){
        ob_clean();
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=qrcode_goods.jpg");
        readfile(I('get.url'));
    }

    //删除菜单
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new GoodsModel();

        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }
    public function sel_goods()
    {
        $id = I("post.id", '');
        empty($id) && wrong_return("参数不正确");
        //获取该mid下的全部菜单
        $m_cat = new GoodsModel();
        $list = $m_cat->get_goods_list($id);
        $this->assign("list", $list);
        $data = array(
            "code" => "1",
            "html" => $this->fetch()
        );
        die_json($data);
    }


    public function recommend()
    {
        //获取夺宝分类
        $m_cat = new CategoryModel();
        $goods_recommend = $m_cat->get_list_by_code_mid('goods_recommend');

        $this->assign('goods_recommend',$goods_recommend);

        return $this->fetch();
    }

    public function show_promo_list()
    {
        $type = null;
        $post = remove_arr_xss(I("post.", []));
        extract($post);
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'p.type',
                'operator' => 'LIKE',   //关系符号
                'value' => $type
            ),
        );

        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        //获取订单列表
        $m_order = new PromotModel()    ;
        $rt = $m_order->get_list($model);

        //获取夺宝分类
        $m_cat = new CategoryModel();
        $goods_recommend = $m_cat->get_list_by_code_mid('goods_recommend');
        $goods_recommend =  $this->format_recommend_data($goods_recommend);

        /*生成分页html*/
        $my_page = new Page($rt["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $m_com = new CommonModel();
        $domain_base = $m_com->get_conf('WEBSITE_URL');
        $this->assign('goods_recommend',$goods_recommend);
        $this->assign('domain_base', $domain_base);
        $this->assign('pages', $pages);
        $this->assign('list', $rt['data']);
        return $this->fetch();
    }

    private function format_recommend_data($data) {
        $arr = array();
        foreach ( $data as $item ) {
            $arr[$item['id']] = $item['name'];

        }
        return $arr;

    }

    public function promot_exec()
    {
        $m = new PromotModel();
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);
        //获取商品分类
        $m_cat = new CategoryModel();
        $cat_list = $m_cat->get_list_by_code_mid('xiangchang');
        $tree = new Tree();
        $cat_list = $tree->toFormatTree($cat_list, 'name');
        $this->assign('cat_list', $cat_list);

        //获取夺宝分类
        $goods_recommend = $m_cat->get_list_by_code_mid('goods_recommend');

        $goods = array();
        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $m = new PromotModel();
            $info = $m->get_info_by_id($id);
            $m_goods = new GoodsModel();
            $goods = $m_goods->get_in_cate_goods($info['category'],'id,name');

            empty($info) && die('获取信息失败');

            //获取图片地址列表
            $img = $m->get_img_list($info['img']);
            $this->assign('img', $img);
            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
            } else {
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }

        $this->assign('goods',$goods);
        $this->assign('goods_recommend',$goods_recommend);

        return $this->fetch('promot_form');

    }


    public function promot_update()
    {
        //获取表单信息
        $post = I("post.", []);
        extract($post);

        empty($gid) && wrong_return("商品不能为空");

        //商品类型
        $m_cat = new CategoryModel();
        $cat_info = $m_cat->get_list_info_by_id($post['category']);
        empty($cat_info["mid"]) && wrong_return("分类MID获取失败");
        $mid = $cat_info["mid"];
        $post['mid'] = $mid;

        //添加进库
        $m=new PromotModel();
        $res = $m->update($post);
        $res !== false && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    public function promot_del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new PromotModel();

        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');

    }

    //删除商品下未开奖的期数
    public function del_nper()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new NperListModel();
        $m->del_form_goods($id) !== false && ok_return('删除成功');
    }

    public function map_pic()
    {
        return $this->fetch('recommend_index');
    }

    //已经下架的商品
    public function trash(){
        return $this->fetch();
    }

    //已经下架的商品列表
    public function show_trash_list(){
        $keywords = null;
        $post = remove_arr_xss(I("post.", []));
        extract($post);
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'g.name',
                'operator' => 'LIKE',   //关系符号
                'value' => $keywords
            ),
            array(
                'field' => 'g.status',
                'operator' => '=',   //关系符号
                'value' => -2
            )
        );
        $m_user=new UsersModel();
        if($shop_id=get_user_id()){
            $user_type=$m_user->get_user_type_by_id($shop_id);
            if($user_type==2){
                $condition_rule=array(
                    array(
                        'field' => 'g.name',
                        'operator' => 'LIKE',   //关系符号
                        'value' => $keywords
                    ),
                    array(
                        'field'=>'g.shop_id',
                        'value'=>$shop_id
                    ),
                    array(
                        'field' => 'g.status',
                        'operator' => '=',   //关系符号
                        'value' => -2
                    )
                );
                $this->assign('user_type',$user_type);
            }
        }
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();

        //获取商品列表
        $m_order = new GoodsModel();
        $rt = $m_order->get_list($model);

        /*生成分页html*/
        $my_page = new Page($rt["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();

        $m_com = new CommonModel();
        $domain_base = $m_com->get_conf('WEBSITE_URL');
        $this->assign('domain_base', $domain_base);
        $this->assign('pages', $pages);
        $this->assign('list', $rt['data']);
        return $this->fetch();
    }

    //删除
}