<?php
namespace app\mobile\controller;
use app\mobile\model\OrderList;
use app\mobile\model\ShowOrder;
use \think\Controller;
use app\core\model\GoodsModel as CoreGoodModel;

use \app\mobile\model\Goods as GoodsModel;
//商品类
class Goods extends Base
{


    /**
     * 图文详情
     * @return mixed
     */
    public function graphic_details()
    {
        $goods = new GoodsModel();

        //首页轮播图片
        $goods_id = (int)I('get.goods_id');

        if ($goods_id) {
            $details = $goods->graphic_details($goods_id);

            if ($details) {
                //移动端也要使用该页面
                if(I('get.origin')) {
                    $this->assign('origin','other');
                }else{
                    $this->assign('origin','');
                }
                $this->assign('details', $details);
                return $this->fetch();
            } else {
                $this->redirect('index/index');
            }
        } else {
            $this->redirect('index/index');
        }

    }

    /**
     * 往期揭晓
     */
    public function before_announce()
    {
        $goods = new GoodsModel();
        $goods_id = (int)I('get.goods_id');
        if ($goods_id) {
            $announce_info = $goods->before_announce($goods_id);
            $this->assign('goods_id',$goods_id);
            $this->assign('announce_info', $announce_info);
//            $this->assign('announce_done', $announce_info['announce_done']);
            return $this->fetch();
        } else {
            $this->redirect('index/index');
        }
    }
    //0元购详情
    public function zero_detail(){
        $goods = new GoodsModel();
        $nper_id = (int)I('get.nper_id');
        if ($nper_id) {
            $goods_detail = $goods->goods_detail($nper_id);
            //没有数据，返回首页
            if (!$goods_detail) {
                $this->redirect('index/index');
            }
            $this->assign('goods_detail', $goods_detail);

            //判断是否登录
            $is_login = session('user') ? 1 : 0;
            $this->assign('is_login', $is_login);
            return $this->fetch();
        } else {
            $this->redirect('index/index');
        }
    }

    /**
     * 商品详情
     * @return mixed
     */
    public function goods_detail()
    {
        $goods = new GoodsModel();
        $nper_id = (int)I('get.nper_id');
        if ($nper_id) {
            $goods_detail = $goods->goods_detail($nper_id);

            $new_id = $goods->get_gid_by_nper_id($nper_id);
            $this->assign('new_id',$new_id);
            //没有数据，返回首页
            if (!$goods_detail) {
                $this->redirect('index/index');
            }
            $this->assign('goods_detail', $goods_detail);

            //判断是否登录
            $is_login = session('user') ? 1 : 0;
            $this->assign('is_login', $is_login);
            return $this->fetch();
        } else {
            $this->redirect('index/index');
        }
    }

    /**
     * 商品下的晒单分享
     * @return mixed
     */
    public function goods_share_order()
    {
        $ShareOrder = new ShowOrder();
        $goods_id = (int)I('get.goods_id');
        if ($goods_id) {
            $share_list = $ShareOrder->goods_share_order($goods_id);
            $this->assign('share_list', $share_list);
            return $this->fetch();
        } else {
            $this->redirect('index/index');
        }
    }



    /**
     * 搜索
     * @return mixed
     */
    public function search()
    {   $goods=new GoodsModel();
        $result=$goods->get_hot_search();
        $this->assign('hot',$result);
        return $this->fetch();
    }


    /**
     * 搜索结果
     * @return mixed
     */
    public function search_result()
    {
        if (IS_POST) {//判断是否已有搜索内容，如果已经有内容跳转到搜索结果页面

            $goods = new GoodsModel();
            $search_result = $goods->get_search_goods($condition=I('post.search'));
            $total_result = count($search_result);

            $this->assign('search_result', $search_result);
            $this->assign('totalResult', $total_result);
            $this->assign('condition', $condition);

            return $this->fetch();
        }
        if(IS_GET){
            $goods = new GoodsModel();
            $condition = urldecode(urldecode(I('get.search')));
            $search_result = $goods->get_search_goods($condition);
            $total_result = count($search_result);

            $this->assign('search_result', $search_result);
            $this->assign('totalResult', $total_result);
            $this->assign('condition', $condition);

            return $this->fetch();

        }


    }

    /**
     * 计算详情
     * @return mixed
     */
    public function calculation_details() {

        $order_list = new OrderList();

        $nper_id = (int)I('get.nper_id');


        if($nper_id) {

            $calculation_details = $order_list->calculation_details($nper_id);


            if(empty($calculation_details)) {
                $this->redirect('index/index');
                return false;
            }

            //移动端也要使用该页面
            if(I('get.origin')) {
                $this->assign('origin','other');
            }else{
                $this->assign('origin','');
            }

            $calculation_details['nper_info']['luck_num']=num_base_mask(intval($calculation_details['nper_info']['luck_num']),1,0);
            //分别注入模板
            $this->assign('nper_info',$calculation_details['nper_info']);
            $this->assign('record',$calculation_details['record']);
            $this->assign('total_num',$calculation_details['total_num']);
            $this->assign('query_url',$calculation_details['query_url']);

            return $this->fetch();
        }else{
            $this->redirect('index/index');
        }


    }


    /**
     * 上传晒单
     */
    public function upload_image() {

        if(empty($_FILES)) {
            return false;
        }

        $user_info = session('user');
        $uid = $user_info['id'];


        $ext = substr($_FILES['file']['type'],6);

        //创建文件夹
        $new_file_dir = './data/img/'.date("ymd");

        if(!is_dir($new_file_dir)) {
            mkdir($new_file_dir, 0777);
        }


        $filename = md5(time().mt_rand(10,99)).'.'.$ext;
        $new_file_path = $new_file_dir.'/'.$filename;



        $local_save_res = file_put_contents($new_file_path,file_get_contents($_FILES['file']['tmp_name']));


        //保存到七牛
        $width = 0;
        $height = 0;
        if($local_save_res) {
            list($width, $height) = getimagesize($new_file_path);
            //$qiniu_save_res = upload_to_qiniu($new_file_path);
        }

        $local_save_path = $local_save_res ? substr($new_file_path,1) :'';

        //$qiniu_save_path = isset($qiniu_save_res['error']) && $qiniu_save_res['error'] == '-1' ? '' : C('QINIU')['DOMAIN'].'/'.$qiniu_save_res['key'];


        //判断是否有用户头像

        $ImageList = M('image_list');
        $save_data = array();
        $save_data['name'] = $filename;
        $save_data['uid'] = $uid;
        $save_data['type'] = 1;
        $save_data['status'] = 1;
        $save_data['img_path'] = $local_save_path;
        $save_data['width'] = $width;
        $save_data['height'] = $height;
        $save_data['ext'] = $ext;
        $save_data['qiniu_path'] = '';
        $save_data['update_time'] = time();
        $save_data['create_time'] = time();
        $insert_id = $ImageList->add($save_data);

        if($insert_id) {
            echo json_encode(array(
                'code' => '200',
                'status' => 'success',
                'insert_id' => $insert_id
            ));
        }else{
            echo json_encode(array(
               'code' => '199',
                'status' => 'fail'
            ));
        }

    }


    /**
     * 商品详情下方的所有参与纪录总数
     */
    public function all_join_count() {
        $this->ajax_request();
        $order_list = M('order_list');
        $nper_id = I('post.nper_id');
        $join_num = $order_list->field('id')->where("dealed = 'true' AND status = 1 AND nper_id = ".$nper_id)->count();
        echo ceil($join_num / 10);
    }

    /**
     * 商品详情下方的所有参与纪录总数
     */
    public function all_ever_count() {
        $this->ajax_request();
        $nper_list = M('nper_list');
        $goods_id = I('post.goods_id');
        $join_num = $nper_list->where(array('pid'=>$goods_id,'status'=>'3'))->count();
        echo ceil($join_num / 10);
    }



    /**
     * Ajax请求商品详情下方的参与记录
     * @return mixed
     */
    public function ajax_goods_join() {
        $this->ajax_request();
        $order_list = new OrderList();
        $nper_id = I('post.nper_id');
        $offset = I('post.offset');
        $join_type=I('post.join_type')?I('post.join_type'):0;
        if(empty($join_type)){
            $participant_record = $order_list->goods_details_order($nper_id,$offset);
        }else{
            $participant_record = $order_list->zero_goods_detail_order($nper_id,$offset,$join_type);

        }
        $this->assign('participant_record',$participant_record);
        return $this->fetch();

    }

    /**
     * Ajax请求往期揭晓数据
     */
    public function ajax_ever_announce() {
        $goods = new GoodsModel();
        $this->ajax_request();
        $goods_id = I('post.goods_id');
        $offset = I('post.offset');
        $ever_data = $goods->before_announce($goods_id,$offset);
        $this->assign('announce_info',$ever_data);
        return $this->fetch();


    }

    /**
     * 点击中奖弹出框，代表用户已经知道自己中奖
     * @return array
     */
    public function confirm_win_notice(){


        $this->ajax_is_login();

        $this->ajax_request();

        $win_record_id = I('post.win_record_id');

        if(empty($win_record_id)) {
            return array(
                'code' => '126',
                'status' => 'fail',
                'message' => '缺少参数'
            );
        }

        //修改已经提示
        $WinRecord = M('win_record');

        $res = $WinRecord->where(array('id' =>$win_record_id))->setField('notice',1);

        if($res) {
            die_json(array(
                'code' => '200',
                'status' => 'success',
                'message' => '成功'
            ));
        }else{
            die_json(
                array(
                    'code' => '126',
                    'status' => 'fail',
                    'message' => '确认失败'
                )
            );
        }



    }

    //根据商品id跳转到当前商品正在购买的期数
    public function jump_to_goods_buying()
    {
        $gid = I("get.gid");
        empty($gid) && $this->redirect('/');//id错误跳转到首页
        //获取当前正在购买的商品的期数信息
        $m_goods = new CoreGoodModel();
        $info = $m_goods->get_no_lottory_nper_info_by_pid($gid);
        if($info==NULL){
            $this->redirect('Index/index');
            die();
        }
       $url = U('goods/goods_detail',array('nper_id'=>$info['id']));
        $this->redirect($url);
    }




}