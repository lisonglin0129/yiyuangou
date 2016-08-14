<?php
namespace app\core\controller;

use app\core\model\AuthModel;
use app\core\model\UploadModel;
use org\Image;
use org\image\driver\Gd;

/**
 * Created by PhpStorm.
 * User: phil
 * Date: 2016/4/13
 * Time: 16:15
 */
Class Upload
{
    public function __construct()
    {
        $m_auth = new AuthModel();
        $m_auth->init();
    }

    public function upload_img()
    {
        // 指定允许其他域名访问
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        if (1)//($_FILES && (md5($_POST['uid'] . $_POST['timestamp']) == $_POST['token']))
        {
            //裁剪信息
            $width = I("post.width", null);//裁剪长
            $height = I("post.height", null);//裁剪宽
            $x = I("post.x", null);//x坐标
            $y = I("post.y", null);//y坐标
            $jcrop_flag = false;

            if (isset($width) && isset($height) && isset($x) && isset($y)) {
                $jcrop_flag = true;
            }

            $m_up = new UploadModel();
            $config = array(
                // 允许上传的文件MiMe类型
                'mimes' => [],
                // 上传的文件大小限制 (0-不做限制)
                'maxSize' => 2014000,
                // 允许上传的文件后缀
                'exts' => ['jpg', 'gif', 'png', 'jpeg', 'bmp'],
                // 自动子目录保存文件
                'autoSub' => true,
                // 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'subName' => ['date', 'ymd'],
                //保存根路径
                'rootPath' => './data/img/',
                // 保存路径
                'savePath' => '',
                // 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName' => ['uniqid', ''],
                // 文件保存后缀，空则使用原后缀
                'saveExt' => '',
                // 存在同名是否覆盖
                'replace' => true,
                // 是否生成hash编码
                'hash' => true,
                // 检测文件是否存在回调，如果存在返回文件信息数组
                'callback' => false,
                // 文件上传驱动e,
                'driver' => '',
                // 上传驱动配置
                'driverConfig' => ["Local"],
            );

            $upload = new \org\Upload($config, 'LOCAL');// 实例化上传类
            $post = $upload->upload();
            $post = $post['file'];

            $post['img_path'] = $post['savepath'] . $post['savename'];
            $img_path = $config['rootPath'] . $post['img_path'];

            /*保存后如果有裁剪标记则进行裁剪*/
            if ($jcrop_flag) {
                $img = new Gd();
                $img->open($img_path);
                $img->crop($width, $height, $x, $y)->save($img_path);
            }


            /**校验图片是否已经上传,如果已经上传则直接从数据看里拷贝配置创建一条 S*/
//            $chk_hash = $post["md5"];
//
//            $chk_img_info = $m_up->get_img_info_by_hash($chk_hash);
//
//            if (!empty($chk_img_info)) {
//                $chk_img_info["name"] = $post['name'];
//                $save = $m_up->save_img_info($chk_img_info);
//                if ($save !== false) {
//                    $rt = array(
//                        "id" => $save,
//                        "img_path" => $chk_img_info["img_path"],
//                        "width" => $chk_img_info["width"],
//                        "height" => $chk_img_info["height"],
//                    );
//                    die_json($rt);
//                }
//                die("-1");
//            }
            /**校验图片是否已经上传,如果已经上传则直接从数据看里拷贝配置创建一条 E*/


            if (empty($post['img_path'])) {
                die("-1");
            }

            $filePath = $config['rootPath'] . trim($post['img_path'], "/");
            $image_size = getimagesize($filePath);

            //保存到数据库
            $arr = array(
                "name" => $post['name'],
                "img_path" =>  trim($img_path,"."),
                "width" => $image_size[0],
                "height" => $image_size[1],
                "ext" => $post['ext'],
                "hash" => $post['md5'],
                "sha1" => $post['sha1'],
            );

            $save = $m_up->save_img_info($arr);

            if ($save) {
                $rt = array(
                    "id" => $save,
                    "img_path" => trim($img_path,"."),
                    "width" => $arr["width"],
                    "height" => $arr["height"],
                );
                die(json_encode($rt));
            }
            die("-1");
        }
        $data['code'] = "-1";
        $data['message'] = "上传失败";
        die(json_encode($data));
    }


    public function upload_share_img(){

        header('Content-Type:application/json;charset=utf-8;');
        $config =[
            'maxSize' => 5 * 1024 * 1024,
            'rootPath' => './',
            'savePath' => '/public/images/',
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'gif', 'png', 'jpeg', 'bmp'),
            'autoSub' => true,
            'subName' => array('date', 'Y/m/d/H'),
        ];
        $upload = new \org\Upload($config,'LOCAL');// 实例化上传类
        $result = array();
        if(!$upload_result = $upload->upload()){
            $result['error']= $upload->getError();
            return json_encode($result);
            die();
        }
        $img_path = $upload_result['file']['savepath'].$upload_result['file']['savename'];

        //$qiniu_info = $this->upload_to_qiniu(realpath('.'.$img_path));
        //$qiniu_upload = new \org\Upload($config,'Qiniu',C('TP_QINIU'));// 实例化上传类

        //$qiniu_info = $qiniu_upload->upload($_FILES);
        //var_dump($qiniu_info);
        //$this->thumbnail('.'.$img_path,'width',200,$upload_result['file']['ext']);
        //$thumb_path = $img_path.'_width_200.'.$upload_result['file']['ext'];
        //TODO 上传失败
        $result['error'] = 0;
        //入库
        $data = array(
            'name'=>$upload_result['file']['name'],
            'uid'=>get_user_id(),
            'type'=>1,
            'status'=>1,
            'img_path'=>$img_path,
            'thumb_path'=>'',//$thumb_path,
            'width'=>I('post.width',0,'intval'),
            'height'=>I('post.height',0,'intval'),
            'ext'=>$upload_result['file']['ext'],
            //'hash'=>$qiniu_info['hash'],
            'sha1'=>$upload_result['file']['sha1'],
            //'qiniu_path'=>C('QINIU.DOMAIN').'/'.$qiniu_info['key'],
            'update_time'=>I('post.lastModifiedDate',0,'strtotime'),
            'create_time'=>time(),
        );
        $m_up = new UploadModel();
        $img_id = $m_up->save_img_info($data);
        if($img_id>0){
            $result['id'] = $img_id;
            //$result['qiniu_path']=C('QINIU.DOMAIN').'/'.$qiniu_info['key'];
            $result['img_path']=$img_path;
            return json_encode($result);
        }else{

        }

    }


//    private function upload_to_qiniu($filePath = null)
//    {
//        if (!file_exists($filePath)) return false;
//        $qiniu_config = C('QINIU');
//        $bucket = $qiniu_config['BUCKET'];
//        $accessKey = $qiniu_config['ACCESS_KEY'];
//        $secretKey = $qiniu_config['SECRET_KEY'];
//        //鉴权
//        $auth = new app\lib\Qiniu\src\Auth($accessKey, $secretKey);
//        //生成上传token
//        $token = $auth->uploadToken($bucket);
//        //获取扩展名用于重命名拼接
//        $path_info = pathinfo($filePath);
//        $key = md5(uuid()) . "." . $path_info["extension"];
//
//        $uploadMgr = new \Qiniu\Storage\UploadManager();
//        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//
//        if ($err !== null) {
//            return array("error" => "-1", "msg" => $err);
//        } else {
//            return $ret;
//        }
//    }



    //上传图片
    public function img()
    {
        // 指定允许其他域名访问
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $uid=$timestamp=$token=null;
        //file_put_contents('./aaa.txt',var_export(I('post.'),true));
        $post = I('post.',array());
        extract($post);

        $sign = sign_by_key(array('uid'=>$uid,'timestamp'=>$timestamp));

        if (($_FILES && ($sign===$token)))
        {
            //裁剪信息
            $width = I("post.width", null);//裁剪长
            $height = I("post.height", null);//裁剪宽
            $x = I("post.x", null);//x坐标
            $y = I("post.y", null);//y坐标
            $jcrop_flag = false;

            if (isset($width) && isset($height) && isset($x) && isset($y)) {
                $jcrop_flag = true;
            }

            $m_up = new UploadModel();
            $config = array(
                // 允许上传的文件MiMe类型
                'mimes' => [],
                // 上传的文件大小限制 (0-不做限制)
                'maxSize' => 2014000,
                // 允许上传的文件后缀
                'exts' => ['jpg', 'gif', 'png', 'jpeg', 'bmp'],
                // 自动子目录保存文件
                'autoSub' => true,
                // 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'subName' => ['date', 'ymd'],
                //保存根路径
                'rootPath' => './data/img/',
                // 保存路径
                'savePath' => '',
                // 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName' => ['uniqid', ''],
                // 文件保存后缀，空则使用原后缀
                'saveExt' => '',
                // 存在同名是否覆盖
                'replace' => true,
                // 是否生成hash编码
                'hash' => true,
                // 检测文件是否存在回调，如果存在返回文件信息数组
                'callback' => false,
                // 文件上传驱动e,
                'driver' => '',
                // 上传驱动配置
                'driverConfig' => ["Local"],
            );
            if(!is_dir($config['rootPath'])){
                mkdir($config['rootPath'],777,true);
            }
            $upload = new \org\Upload($config, 'LOCAL');// 实例化上传类
            $post = $upload->upload();
            $post = $post['file'];

            $post['img_path'] = $post['savepath'] . $post['savename'];
            $img_path = $config['rootPath'] . $post['img_path'];

            /*保存后如果有裁剪标记则进行裁剪*/
            if ($jcrop_flag) {
                $img = new Gd();
                $img->open($img_path);
                $img->crop($width, $height, $x, $y)->save($img_path);
            }


            /**校验图片是否已经上传,如果已经上传则直接从数据看里拷贝配置创建一条 S*/
//            $chk_hash = $post["md5"];
//
//            $chk_img_info = $m_up->get_img_info_by_hash($chk_hash);
//
//            if (!empty($chk_img_info)) {
//                $chk_img_info["name"] = $post['name'];
//                $save = $m_up->save_img_info($chk_img_info);
//                if ($save !== false) {
//                    $rt = array(
//                        "id" => $save,
//                        "img_path" => $chk_img_info["img_path"],
//                        "width" => $chk_img_info["width"],
//                        "height" => $chk_img_info["height"],
//                    );
//                    die_json($rt);
//                }
//                die("-1");
//            }
            /**校验图片是否已经上传,如果已经上传则直接从数据看里拷贝配置创建一条 E*/


            if (empty($post['img_path'])) {
                die("-1");
            }

            $filePath = $config['rootPath'] . trim($post['img_path'], "/");
            $image_size = getimagesize($filePath);

            //保存到数据库
            $arr = array(
                "name" => $post['name'],
                "img_path" =>  trim($img_path,"."),
                "origin" => empty(I('request.q',null))?null:I('request.q',null),
                "width" => $image_size[0],
                "height" => $image_size[1],
                "ext" => $post['ext'],
                "hash" => $post['md5'],
                "sha1" => $post['sha1'],
            );

            $save = $m_up->save_img_info($arr);

            if ($save) {
                $rt = array(
                    "code" => '1',
                    "id" => $save,
                    "img_path" => trim($img_path,"."),
                    "width" => $arr["width"],
                    "height" => $arr["height"],
                );
                die_json($rt);
            }
            die_json(array("code"=>"-1","msg"=>"save error"));
        }
        $data['code'] = "-1";
        $data['message'] = "up error";
        die_json($data);
    }

    //上传image图片
    public function um_img()
    {
//        // 指定允许其他域名访问
//        header('Access-Control-Allow-Origin:*');
//        header('Access-Control-Allow-Methods:POST');
//        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $uid=$timestamp=$token=null;
        $post = I('post.',array());
        extract($post);

        $sign = sign_by_key(array('uid'=>$uid,'timestamp'=>$timestamp));

        if (1)//(($_FILES && ($sign===$token)))
        {
            //裁剪信息
            $width = I("post.width", null);//裁剪长
            $height = I("post.height", null);//裁剪宽
            $x = I("post.x", null);//x坐标
            $y = I("post.y", null);//y坐标
            $jcrop_flag = false;

            if (isset($width) && isset($height) && isset($x) && isset($y)) {
                $jcrop_flag = true;
            }

            $m_up = new UploadModel();
            $config = array(
                // 允许上传的文件MiMe类型
                'mimes' => [],
                // 上传的文件大小限制 (0-不做限制)
                'maxSize' => 2014000,
                // 允许上传的文件后缀
                'exts' => ['jpg', 'gif', 'png', 'jpeg', 'bmp'],
                // 自动子目录保存文件
                'autoSub' => true,
                // 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'subName' => ['date', 'ymd'],
                //保存根路径
                'rootPath' => './data/img/',
                // 保存路径
                'savePath' => '',
                // 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName' => ['uniqid', ''],
                // 文件保存后缀，空则使用原后缀
                'saveExt' => '',
                // 存在同名是否覆盖
                'replace' => true,
                // 是否生成hash编码
                'hash' => true,
                // 检测文件是否存在回调，如果存在返回文件信息数组
                'callback' => false,
                // 文件上传驱动e,
                'driver' => '',
                // 上传驱动配置
                'driverConfig' => ["Local"],
            );

            $upload = new \org\Upload($config, 'LOCAL');// 实例化上传类
            $post = $upload->upload();
            if(!is_dir($config['rootPath'])){
                mkdir($config['rootPath'],777,true);
            }
            $post = $post['upfile'];

            $post['img_path'] = $post['savepath'] . $post['savename'];
            $img_path = $config['rootPath'] . $post['img_path'];

            /*保存后如果有裁剪标记则进行裁剪*/
            if ($jcrop_flag) {
                $img = new Gd();
                $img->open($img_path);
                $img->crop($width, $height, $x, $y)->save($img_path);
            }


            /**校验图片是否已经上传,如果已经上传则直接从数据看里拷贝配置创建一条 S*/
//            $chk_hash = $post["md5"];
//
//            $chk_img_info = $m_up->get_img_info_by_hash($chk_hash);
//
//            if (!empty($chk_img_info)) {
//                $chk_img_info["name"] = $post['name'];
//                $save = $m_up->save_img_info($chk_img_info);
//                if ($save !== false) {
//                    $rt = array(
//                        "id" => $save,
//                        "img_path" => $chk_img_info["img_path"],
//                        "width" => $chk_img_info["width"],
//                        "height" => $chk_img_info["height"],
//                    );
//                    die_json($rt);
//                }
//                die("-1");
//            }
            /**校验图片是否已经上传,如果已经上传则直接从数据看里拷贝配置创建一条 E*/


            if (empty($post['img_path'])) {
                die("-1");
            }

            $filePath = $config['rootPath'] . trim($post['img_path'], "/");
            $image_size = getimagesize($filePath);

            //保存到数据库
            $arr = array(
                "name" => $post['name'],
                "img_path" =>  trim($img_path,"."),
                "origin" => empty(I('request.q',null))?null:I('request.q',null),
                "width" => $image_size[0],
                "height" => $image_size[1],
                "ext" => $post['ext'],
                "hash" => $post['md5'],
                "sha1" => $post['sha1'],
            );

            $save = $m_up->save_img_info($arr);

            if ($save) {

                $rt = array(
                    "originalName" => $arr['name'],
                    "name" => $arr['name'],
                    "url" => trim($img_path,"."),
                    "size" => $post["size"],
                    "type" =>$post['ext'],
                    "state" => 'SUCCESS',
                );
                die_json($rt);
            }
            die_json(array("code"=>"-1","msg"=>"save error"));
        }
        $data['code'] = "-1";
        $data['message'] = "up error";
        die_json($data);
    }
}