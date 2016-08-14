<?php

namespace app\mobile\model;

use think\Model;
class ShowOrder extends Base {


    /**
     * 首页晒单列表
     * @return mixed
     */
    public function all_share_order($offset) {
        $share_list = $this->table('sp_show_order s')
            ->field('s.id as share_id,s.nper_id,s.username,u.nick_name,s.title,s.content,s.create_time,s.pic_list,s.uid')
            ->join('sp_users u on s.uid=u.id','left')
            ->limit($offset,10)
            ->order('s.create_time DESC')
            ->where('s.status = 1 AND s.complete = 1 AND s.create_time<'.time())
            ->select();


        if($share_list) {
            foreach($share_list as $key=>$value) {
                //格式化时间
                $share_list[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
                //取出第一张图片
                if($value['pic_list']) {
                    $img_arr = explode(',',$value['pic_list']);
                    $share_list[$key]['img_path'] = $this->get_one_image_src($img_arr[0]);
                }


            }
        }
        return $share_list;
    }

    /**
     * 该商品下的晒单分享
     * @param $goods_id
     * @return mixed
     */
    public function goods_share_order($goods_id) {
        $share_list = $this->table('sp_show_order s')
            ->field('s.id as share_id,s.nper_id,s.username,s.title,s.content,s.create_time,s.pic_list,u.nick_name,u.id user_id')
            ->join('sp_users u on u.id = s.uid','left')
            ->order('s.create_time DESC')
            ->where('s.status = 1 AND s.complete = 1 AND s.goods_id ='.$goods_id.' AND s.create_time <'.time())
            ->select();
        if($share_list) {
            foreach($share_list as $key=>$value) {
                //格式化时间
                $share_list[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
                //取出第一张图片
                if($value['pic_list']) {
                    $img_arr = explode(',',$value['pic_list']);
                    $share_list[$key]['img_path'] = $this->get_one_image_src($img_arr[0]);
                }
            }
        }
        return $share_list;
    }

    /**
     * 其他用户个人晒单
     * @return mixed
     */
    public function other_share_order($uid) {
        $share_list =
            $this->table('sp_show_order s')
                ->field('s.id as share_id,s.nper_id,s.username,s.title,s.content,s.create_time,s.pic_list,u.nick_name')
                ->join('sp_users u ON u.id=s.uid')
                ->order('create_time DESC')
                ->where('s.status = 1 AND s.complete = 1 && s.uid ='.$uid.' AND s.create_time <'.time())
                ->select();
        if($share_list) {
            foreach($share_list as $key=>$value) {
                //格式化时间
                $share_list[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
                //取出第一张图片
                if($value['pic_list']) {
                    $img_arr = explode(',',$value['pic_list']);
                    $share_list[$key]['img_path'] = $this->get_one_image_src($img_arr[0]);
                }


            }
        }
        return $share_list;
    }

    /**
     * 我的晒单详情
     * @return array|mixed
     */
    public function get_my_share_detail($share_id){
        $NperList = M('nper_list');
        $Goods=M('goods');

        $condition['s.status']=1;
        $condition['s.complete']=1;
        $condition['s.id']=$share_id;

        $share_detail=$this->alias('s')->join('nper_list n','s.nper_id=n.id')->join('goods g','s.goods_id=g.id')->join('users u ','u.id = s.uid')->field('u.nick_name,s.id as share_id,s.nper_id,s.username,s.uid,s.title,s.content,s.create_time,s.pic_list,g.name,n.participant_num,n.luck_time,n.open_time,n.luck_num')->where($condition)->find();

        $share_detail['luck_num'] = num_base_mask(intval($share_detail['luck_num']),1,0);
        if(!$share_detail) {
            return array();
        }

        $share_detail['create_time'] = date('Y-m-d H:i:s',$share_detail['create_time']);  //格式化时间
        $share_detail['open_time'] = date('Y-m-d H:i:s',$share_detail['open_time']);  //格式化时间
        $img_arr = explode(',',$share_detail['pic_list']);
        $img_list=array();
        foreach($img_arr as $value){
            $img_list[] = $this->get_one_image_src($value);//获取用户晒单的所有图片
        }
        $share_detail['imgList']=$img_list;

        return $share_detail;


    }


    /**
     * 个人中心晒单列表
     * @return mixed
     */
    public function my_share_list() {

        $Goods = M('goods');

        $user_info = session('user');
        $uid = $user_info['id'];

//        $uid = 3;
        $share_list = $this->field('id as share_id,nper_id,goods_id,uid,status,complete,username,title,content,create_time,pic_list')->order('create_time DESC')->where('uid ='.$uid)->select();


        if($share_list) {
            foreach($share_list as $key=>$value) {
                //格式化时间
                $share_list[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
                //取出第一张图片
                if($value['pic_list']) {
                    $img_arr = explode(',',$value['pic_list']);
                    $share_list[$key]['img_path'] = $this->get_one_image_src($img_arr[0]);
                }

                if($value['complete'] == 0) {
                    $goods_info = $Goods->field('name,index_img')->where(array('id' =>$value['goods_id']))->find();
                    $share_list[$key]['goods_img'] = $this->get_one_image_src($goods_info['index_img']);
                    $share_list[$key]['goods_name'] = $goods_info['name'];
                }

            }
        }


        return $share_list;

    }

    /**
     * 我要晒单下的商品信息
     * @param $share_id
     * @return mixed
     */
    public function submit_share_goods($share_id) {
        $Goods = M('goods');
        $Order_List = M('order_list');
        $NperList = M('nper_list');

        $share_goods = $this->field('id as share_id,luck_num,nper_id,goods_id,order_id')->where('complete = 0 AND id ='.$share_id)->find();
        $share_goods['goods_name'] = $Goods->field('name')->where('id ='.$share_goods['goods_id'])->find()['name'];
        $share_goods['join_num'] = $Order_List->field('num')->where('order_id ='.$share_goods['order_id'])->find()['num'];
        $open_time = $NperList->field('open_time')->where("id = '".$share_goods['nper_id']."'")->find()['open_time'];

        $share_goods['open_time'] = date('Y-m-d H:i:s',$open_time);// microtime_format($open_time,2);

        return $share_goods;

    }


    /**
     * 上传表单提交
     * @param $share_id
     * @param $title
     * @param $content
     * @param $pic_list
     * @return array
     */
    public function submit_share_form($share_id,$title,$content,$pic_list) {

        if(empty($share_id) || empty($title) || empty($content) || empty($pic_list)) {
            return array(
                'code' => '126',
                'status' => 'fail',
                'message' => '缺少参数'
            );
        }

        $show_order_data['title'] = $title;
        $show_order_data['content'] = $content;
        $show_order_data['pic_list'] = $pic_list;
        $show_order_data['create_time'] = time();
        $show_order_data['complete'] = 1;
        $show_order_data['status'] = 3;

        $res = $this->where('id ='.$share_id)->save($show_order_data);

        if($res) {
            return array(
                'code' => '200',
                'status' => 'success',
                'message' => '上传成功'
            );
        }else{
            return array(
                'code' => '129',
                'status' => 'fail',
                'message' => '上传失败'
            );
        }


    }


}