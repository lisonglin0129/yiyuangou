<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/6/12
 * Time: 13:47
 */
namespace app\mobile\controller;

use app\mobile\model\Common;
use think\Controller;

class Wechat extends Controller
{
    private $wechat_obj;
    private $rev;
    private $msg_obj;
    private $welcom;

    public function __construct()
    {
        parent::__construct();
        $c_model = new Common();
        $this->wechat_obj = new \app\lib\Wechat([
            'token' => $c_model->get_conf('WECHAT_TOKEN'),
            'encodingaeskey' => $c_model->get_conf('WECHAT_ENCODING_KEY'),
            'appid' => $c_model->get_conf('UNION_WECHAT_MP_APPID'),
            'appsecret' => $c_model->get_conf('UNION_WECHAT_MP_APPSEC')]);
        $this->msg_obj = M('wechat_message');
        $this->welcom = '欢迎关注';
    }

    public function index()
    {
        //基础验证
        $this->wechat_obj->valid();
        //事件处理
        $this->rev = $this->wechat_obj->getRev();
        $type = $this->rev->getRevType();
        switch ($type) {
            case 'text':
                $this->send_msg($this->rev->getRevContent());
                exit;
                break;
            case 'event':
                $event = $this->rev->getRevEvent();
                if (!empty($event)) {
                    if ($event['event'] == 'subscribe' && !empty($event['key']) && (strpos($event['key'], 'qrscene_') !== false)) {

                        M('scene')->add(['scene' => explode('_', $event['key'])[1], 'openid' => $this->rev->getRevFrom(), 'create_time' => time()]);
                    }
                    if ($event['event'] == 'CLICK' && !empty($event['key'])) {
                       $this->send_msg($event['key']);
                    }
                }
                break;
            case 'image':

                break;
            default:
                $this->wechat_obj->text("help info")->reply();
        }
    }
    //发送文本消息
    private function send_msg($key)
    {
        $m = $this->get_m_by_key($key);
        empty($m) && $this->wechat_obj->text($this->welcom)->reply() && exit;
        if ($m['type'] == 1) {
            //文本消息
            $c = !empty($m['content']) ? $m['content'] : $this->welcom;
            $this->wechat_obj->text($c)->reply();
            exit;
        } elseif ($m['type'] == 2) {
            //图文消息
            $this->wechat_obj->news($this->generate_msgs([0=>$m]))->reply();
            exit;
        } else {
            //大图消息
            $ids=$m['index_mid'].','.$m['content'];
            $this->wechat_obj->news($this->generate_msgs($this->get_messages_by_id($ids)))->reply();
            exit;
        }
    }
    //组装多图文
    private function generate_msgs($msgs){
        foreach($msgs as $m){
            !empty($m['img_url']) && $picurl = $this->get_picurl_by_id($m['img_url']);
            $url=!empty($m['link'])?$m['link']:'http://' . $_SERVER['HTTP_HOST'] .U('mobile/article/msg_info',['id'=>$m['id']]);
            $data[] = [
                'Title' => !empty($m['title']) ? $m['title'] : '',
                'Description' => !empty($m['title']) ? $m['title'] : '',
                'PicUrl' => 'http://' . $_SERVER['HTTP_HOST'] . $picurl,
                'Url' =>$url
            ];
        }
        return $data;
    }
    //ids获取图文
    private function get_messages_by_id($ids){
        return $this->msg_obj->where(['id'=>['in',$ids]])->select();
    }
    //关键字获去消息
    private function get_m_by_key($key)
    {
        return $this->msg_obj->where(['keyword' => $key])->find();
    }

    private function get_picurl_by_id($id)
    {
        return M('image_list')->where(['id' => $id])->getField('img_path');
    }
}