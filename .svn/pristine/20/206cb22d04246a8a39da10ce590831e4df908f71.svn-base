<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/6/13
 * Time: 15:42
 */
namespace app\admin\controller;

use app\admin\model\ConfModel;
use app\admin\model\WechatMessageModel;
use app\core\lib\Condition;
use app\lib\Page;
use app\lib\Wechat;

class Message extends Common
{
    private $m;

    public function __construct()
    {
        parent::__construct();
        $this->m = new WechatMessageModel();
    }

    public function index()
    {
        return $this->display();
    }

    public function show_list()
    {

        $model = new Condition([], $this->page, $this->page_num);
        $model->init();
        $res = $this->m->show_list($model);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('list', $res['data']);
        return $this->fetch();
    }

    public function imgs_message_list()
    {
        $index_mid = I('post.id');
        $mid = I('post.mid');
        $model = new Condition([], $this->page, $this->page_num);
        $model->init();
        $res = $this->m->get_excepted_img_messages($model, $index_mid);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('imgs_message_list'), 5);
        $pages = $my_page->myde_write();
        if (!empty($mid)) {
            $this->assign('mids', explode(',', $this->m->get_content($mid)));
        }
        $this->assign('pages', $pages);
        $this->assign('list', $res['data']);
        return $this->fetch();
    }

    //添加页面
    public function exec()
    {
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);
        //消息类型
        $m_type = I('get.m_type', null);
        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $this->m->get_m_info_by_id($id);
            empty($info) && die('获取信息失败');
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
        if ($m_type == 3) {
            $this->assign('imgs', $this->m->get_img_messages());
        }
        $tpl = $m_type == 1 ? 'text_form' : ($m_type == 2 ? 'img_form' : 'imgs_form');
        return $this->fetch($tpl);
    }

    //文本消息数据保存
    public function text_update()
    {
        $rt = $this->m->update_text(I('post.'));
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //图文消息数据保存
    public function img_update()
    {
        $rt = $this->m->update_img(I('post.'));
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //多图文消息数据保存
    public function imgs_update()
    {
        $rt = $this->m->update_imgs(I('post.'));
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //消息删除
    public function del()
    {
        $id = I('post.id');
        empty($id) && die('id不能空');
        $rt = $this->m->update_status($id);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }

    //验证关键字
    public function valid()
    {
        $key = I('post.key');
        $rt = $this->m->key_valid($key);
        $rt && wrong_return('关键字已经存在');
        ok_return('关键字不存在');
    }

    //群发消息
    public function send_msg()
    {
        $c_model = new ConfModel();
        $wechat = new Wechat(array('appid' => $c_model->get_conf('UNION_WECHAT_MP_APPID'),
            'appsecret' => $c_model->get_conf('UNION_WECHAT_MP_APPSEC')));
        $info = $this->upload_img($wechat, I('post.id'));
       $rt= $wechat->sendGroupMassMessage([
            "filter" => ['is_to_all' => True],
            "msgtype" => "mpnews",
            "mpnews" => ['media_id'=>$wechat->uploadForeverArticles(['articles'=>$info])['media_id']]
        ]);
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('操作失败');
    }

    //上传缩略图
    private function upload_img($obj, $mid)
    {
        $m_model = new WechatMessageModel();
        $msg = $m_model->get_m_info_by_id($mid);
        $info = [];
        if ($msg['type'] == 2) {
            if (class_exists('\CURLFile')) {
                $field = array('media' => new \CURLFile(realpath(ltrim($msg['img_face'],'/'))));
            } else {
                $field = array('media' => '@' . $msg['img_face']);
            }
            $info[] = [
                "title" => $msg['title'],
                "thumb_media_id" => $obj->uploadForeverMedia($field,'image')['media_id'],
                "author" => 'admin',
                "digest" => $msg['title'],
                "show_cover_pic" => 1,
                "content" => $msg['title'],
                "content_source_url" => !empty($msg['link']) ? $msg['link'] : 'http://' . $_SERVER['HTTP_HOST'] . U('mobile/article/msg_info', ['id' => $msg['id']])
            ];
        } else {
            $mids = $msg['index_mid'] . ',' . $msg['content'];
            $mids = explode(',', $mids);
            foreach ($mids as $v) {
                $msg=$m_model->get_m_info_by_id($v);
                if (class_exists('\CURLFile')) {
                    $field = array('media' => new \CURLFile(realpath(ltrim($msg['img_face'],'/'))));
                } else {
                    $field = array('media' => '@' . $msg['img_face']);
                }
                $info[] = [
                    "title" => $msg['title'],
                    "thumb_media_id" => $obj->uploadForeverMedia($field, 'image')['media_id'],
                    "author" => 'admin',
                    "digest" => '',
                    "show_cover_pic" => 1,
                    "content" => $msg['title'],
                    "content_source_url" => !empty($msg['link']) ? $msg['link'] : 'http://' . $_SERVER['HTTP_HOST'] . U('mobile/article/msg_info', ['id' => $msg['id']])
                ];
            }
        }
        return $info;
    }
}