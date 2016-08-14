<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */
namespace app\lib\oAuth;
use app\core\model\CommonModel;
use app\lib\oAuth\URL;
use app\lib\oAuth\ErrorCase;
use think\Model;

class Recorder{

    private static $data;
    private $inc;
    private $error;

    public function __construct(){
        $this->error = new ErrorCase();
      //-------读取配置文件
        $c_model = new CommonModel();
        $inc['appid']=$c_model->get_conf('UNION_QQ_APPID');
        $inc['appkey']=$c_model->get_conf('UNION_QQ_APPKEY');
        $inc['callback']=U('mobile/login/callback',['type'=>'qq'],true,true);//'http://'.$_SERVER['HTTP_HOST'].'/index.php/mobile/Login/callback/type/qq';
        $inc['scope']='get_user_info';
        $inc['errorReport']='true';
        $inc['storageType']='file';

        $inc = json_decode(json_encode($inc));

        $this->inc=$inc;
        if(empty($this->inc)){
            $this->error->showError("20001");
        }

        if(empty($_SESSION['QC_userData'])){
            self::$data = array();
        }else{
            self::$data = $_SESSION['QC_userData'];

        }
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
        if(empty($this->inc->$name)){
            return null;
        }else{
            return $this->inc->$name;
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
    }
}
