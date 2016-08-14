<?php
/**
 * Created by PhpStorm.
 * User: bb3201
 * Date: 2016/5/25
 * Time: 9:59
 */
namespace app\admin\controller;
use think\Controller;

class ClearCache extends Common {
    public function index() {
        return $this->fetch();
    }

    public function clear() {
        $cache = I("post.cache");
        is_null($cache) && wrong_return('请确定清除缓存!');
        $path = APP_PATH.'runtime';
        if (is_dir($path)) {
            deldir($path);
            ok_return('缓存清空操作成功');
        }
        wrong_return('无缓存文件,初始化失败');

    }
}
