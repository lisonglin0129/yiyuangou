<?php
/**
 * 防止mysql注入.
 * User: phil
 * Date: 2016/6/7
 * Time: 13:14
 */


function inject_check($str) {//自动过滤Sql的注入语句。
    $check=preg_match('/ +select| +insert| +update| +delete| +\'| +\\*| +\*| +\.\.\/|\.\/| +union| +into| +load_file| +outfile/i',$str);
    if ($check) {
       return true;//检测到非法字符
    }else{
        return false;
    }
}

$req = $_REQUEST;
if(is_array($req)){
    foreach($req as $k=>$v){
        if(inject_check(json_encode($v))){
            die('param sql hack abandon[select|insert|update|delete|union|into|load_file|outfile]:'.$v);
        }
    }
}