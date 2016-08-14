<?php

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login()
{
    $sign = session('sign');
    if (empty($sign)) {
        return 0;
    } else {
        $data = session('user');
        return $sign == data_auth_sign($data) ? session("user.id") : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str 要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ',')
{
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array $arr 要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ',')
{
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url)
{
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url()
{
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = array())
{
    \Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name)
{
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array())
{
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
    $addons = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons' => $addons,
        '_controller' => $controller,
        '_action' => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <service@xiangchang.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i:s')
{
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
        if ($info && isset($info[1])) {
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if ($info !== false && $info['nickname']) {
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}


/**
 * 获取顶级模型信息
 */
function get_top_model($model_id = null)
{
    $map = array('status' => 1, 'extend' => 0);
    if (!is_null($model_id)) {
        $map['id'] = array('neq', $model_id);
    }
    $model = M('Model')->where($map)->field(true)->select();
    foreach ($model as $value) {
        $list[$value['id']] = $value;
    }
    return $list;
}

/**
 * 获取文档模型信息
 * @param  integer $id 模型ID
 * @param  string $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null)
{
    static $list;

    /* 非法分类ID */
    if (!(is_numeric($id) || is_null($id))) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('DOCUMENT_MODEL_LIST');
    }

    /* 获取模型名称 */
    if (empty($list)) {
        $map = array('status' => 1, 'extend' => 1);
        $model = M('Model')->where($map)->field(true)->select();
        foreach ($model as $value) {
            $list[$value['id']] = $value;
        }
        S('DOCUMENT_MODEL_LIST', $list); //更新缓存
    }

    /* 根据条件返回数据 */
    if (is_null($id)) {
        return $list;
    } elseif (is_null($field)) {
        return $list[$id];
    } else {
        return $list[$id][$field];
    }
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data)
{
    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <service@xiangchang.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null)
{

    //参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id'] = $action_info['id'];
    $data['user_id'] = $user_id;
    $data['action_ip'] = ip2long(get_client_ip());
    $data['model'] = $model;
    $data['record_id'] = $record_id;
    $data['create_time'] = NOW_TIME;

    //解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user'] = $user_id;
            $log['record'] = $record_id;
            $log['model'] = $model;
            $log['time'] = NOW_TIME;
            $log['data'] = array('user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME);
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                } else {
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        //未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if (!empty($action_info['rule'])) {
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <service@xiangchang.com>
 */
function parse_action($action = null, $self)
{
    if (empty($action)) {
        return false;
    }

    //参数支持id或者name
    if (is_numeric($action)) {
        $map = array('id' => $action);
    } else {
        $map = array('name' => $action);
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();
    if (!$info || $info['status'] != 1) {
        return false;
    }

    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = $info['rule'];
    $rules = str_replace('{$self}', $self, $rules);
    $rules = explode(';', $rules);
    $return = array();
    foreach ($rules as $key => &$rule) {
        $rule = explode('|', $rule);
        foreach ($rule as $k => $fields) {
            $field = empty($fields) ? array() : explode(':', $fields);
            if (!empty($field)) {
                $return[$key][$field[0]] = $field[1];
            }
        }
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
            unset($return[$key]['cycle'], $return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <service@xiangchang.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null)
{
    if (!$rules || empty($action_id) || empty($user_id)) {
        return false;
    }

    $return = true;
    foreach ($rules as $rule) {

        //检查执行周期
        $map = array('action_id' => $action_id, 'user_id' => $user_id);
        $map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
        $exec_count = M('ActionLog')->where($map)->count();
        if ($exec_count > $rule['max']) {
            continue;
        }

        //执行数据库操作
        $Model = M(ucfirst($rule['table']));
        $field = $rule['field'];
        $res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));

        if (!$res) {
            $return = false;
        }
    }
    return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files)
{
    foreach ($files as $key => $value) {
        if (substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <service@xiangchang.com>
 */
function get_table_name($model_id = null)
{
    if (empty($model_id)) {
        return false;
    }
    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);
    if ($info['extend'] != 0) {
        $name = $Model->getFieldById($info['extend'], 'name') . '_';
    }
    $name .= $info['name'];
    return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id 属性ID
 * @param  string $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true, $fields = true)
{
    static $list;

    /* 非法ID */
    if (empty($model_id) || !is_numeric($model_id)) {
        return '';
    }

    /* 获取属性 */
    if (!isset($list[$model_id])) {
        $map = array('model_id' => $model_id);
        $extend = M('Model')->getFieldById($model_id, 'extend');

        if ($extend) {
            $map = array('model_id' => array("in", array($model_id, $extend)));
        }
        $info = M('Attribute')->where($map)->field($fields)->select();
        $list[$model_id] = $info;
    }

    $attr = array();
    if ($group) {
        foreach ($list[$model_id] as $value) {
            $attr[$value['id']] = $value;
        }
        $model = M("Model")->field("field_sort,attribute_list,attribute_alias")->find($model_id);
        $attribute = explode(",", $model['attribute_list']);
        if (empty($model['field_sort'])) { //未排序
            $group = array(1 => array_merge($attr));
        } else {
            $group = json_decode($model['field_sort'], true);

            $keys = array_keys($group);
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    if (!in_array($val['id'], $attribute)) {
                        unset($attr[$key]);
                    }
                }
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }
        if (!empty($model['attribute_alias'])) {
            $alias = preg_split('/[;\r\n]+/s', $model['attribute_alias']);
            $fields = array();
            foreach ($alias as &$value) {
                $val = explode(':', $value);
                $fields[$val[0]] = $val[1];
            }
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    if (!empty($fields[$val['name']])) {
                        $value[$key]['title'] = $fields[$val['name']];
                    }
                }
            }
        }
        $attr = $group;
    } else {
        foreach ($list[$model_id] as $value) {
            $attr[$value['name']] = $value;
        }
    }
    return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string $name 格式 [模块名]/接口名/方法名
 * @param  array|string $vars 参数
 */
function api($name, $vars = array())
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = $array ? array_pop($array) : 'Common';
    $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
    if (is_string($vars)) {
        parse_str($vars, $vars);
    }
    return call_user_func_array($callback, $vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <service@xiangchang.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null)
{
    if (empty($value) || empty($table)) {
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <service@xiangchang.com>
 */
function get_link($link_id = null, $field = 'url')
{
    $link = '';
    if (empty($link_id)) {
        return $link;
    }
    $link = M('Url')->getById($link_id);
    if (empty($field)) {
        return $link;
    } else {
        return $link[$field];
    }
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <service@xiangchang.com>
 */
function get_cover($cover_id, $field = null)
{
    if (empty($cover_id)) {
        return false;
    }
    $picture = M('Picture')->where(array('status' => 1))->getById($cover_id);
    if ($field == 'path') {
        if (!empty($picture['url'])) {
            $picture['path'] = $picture['url'];
        } else {
            $picture['path'] = __ROOT__ . $picture['path'];
        }
    }
    return empty($field) ? $picture : $picture[$field];
}


// 一维数组 xss过滤
function remove_arr_xss($data)
{
    foreach ($data as $k => $v) {
        $data[$k] = trim(remove_xss($v));
    }
    return $data;
}

function remove_xss($val)
{
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

function err_tip_arr($key, $msg)
{
    return array(
        "key" => $key,
        "msg" => $msg
    );
}

//获得指定文章分类的子分类组成的树形结构
function cateTree($pid = 0, $level = 0)
{
    $cate = M('cate');
    $array = array();
    $tmp = $cate->where("pid='%d'", $pid)->select();
    if (is_array($tmp)) {
        foreach ($tmp as $v) {
            $v['level'] = $level;
            $array[count($array)] = $v;
            $sub = cateTree($v['id'], $level + 1);
            if (is_array($sub)) $array = array_merge($array, $sub);
        }
    }
    return $array;
}

function md6($str)
{
    return md5(md5(md5(serialize(json_encode($str)))));
}

/**
 * 验证码检查，验证完后销毁验证码增加安全性 ,<br>返回true验证码正确，false验证码错误
 * @return boolean <br>true：验证码正确，false：验证码错误
 */
function sp_check_verify_code()
{
    if (!empty($_REQUEST['verify'])) {
        $verify = new \org\Verify();
        return $verify->check($_REQUEST['verify'], "");
    }
    return false;
}

function get_user_id()
{
    $user = session("user");
    return $user["id"];
}

function get_user_name()
{
    $user_login = session("user.username");
    return $user_login;
}

/*判断字符串是否为json格式
 *是json则返回true
*/
function isNotJson($str)
{
    return !is_null(json_decode($str));
}

/*校验字符串格式
 * $str:待校验字符串
 * $min:最小长度	$max:最大长度  为NULL则不限制
 * $preg_string:正则表达式
 * $isnull: 默认为false 不允许为空  true允许为空
 * 通过返回1
 * 不通过返回<1
 */

function preg_string($str, $min, $max, $preg_string, $isnull)
{
    $isnull = !$isnull ? false : true;
    // 允许为空且为空直接返回正确退出
    if ((strlen($str) < 1) && ($isnull === true)) return '1';

    if (empty($str) && $str != 0) return '-1';//校验字符串为空
    if (strlen($str) < (int)$min) {
        if ($min != NULL) return '-2';//最小长度不符合要求
    }
    if (strlen($str) > (int)$max) {
        if ($max != NULL) return '-3';//最大长度不符合要求
    }

    if (!preg_match_all($preg_string, $str, $matches)) return "-4";//格式不正确
    return '1';//通过
}

/*正则校验格式
* chkString:待测试字符
* $language:字符串格式 0英文(默认) 1中文
* $min $max 限制字符串的范围
* $preg_string 正则表达式
* $tipsName 提示名称
* $tipsFunc 正则匹配不通过返回提示信息
* 返回值,通过则返回值为空 不通过则返回错误信息
*/
function check_str($chkString, $language, $min, $max, $preg_string, $tipsName, $tipsFunc, $isnull)
{
    if ($language == 1) {
        $min1 = $min * 3;
        $max1 = $max * 3;
        $tips = '个汉字';
    } else {
        $min1 = $min;
        $max1 = $max;
        $tips = '个字符';
    }
    switch (preg_string($chkString, $min1, $max1, $preg_string, $isnull)) {
        case '1':
            return NULL;
        case '-1':
            $rtVal = $tipsName . '不能为空';
            break;
        case '-2':
            $rtVal = $tipsName . '最小长度应该大于' . $min . $tips;
            break;
        case '-3':
            $rtVal = $tipsName . '最大长度应该小于' . $max . $tips;
            break;
        case '-4':
            $rtVal = $tipsFunc;
            break;
        default :
            $rtVal = $tipsName . '返回值非法';
            break;
    }
    return $rtVal;
}

/*校验是否为base64格式，是返回true 不是返回false*/
function checkBase64($base64Str)
{
    if (base64_encode(base64_decode($base64Str)) == $base64Str) return true;
    return false;
}


function rex_rule($type)
{
    $rule = "/.*/";
    switch ($type) {
        case "cn_name"://中文用户名
            $rule = '/^[\x{4e00}-\x{9fa5}\w\d\_\ ]{2,20}$/u';
            break;
        case "en_name"://英文数字用户名
            $rule = "/^[a-zA-Z0-9]{6,20}$/";
            break;
        case "password"://密码
            $rule = "/^.{6,20}$/";
            break;
        case "email"://邮箱
            $rule = '/([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/';
            break;
        case "phone"://手机
            $rule = '/^[\d]{11}$/';
            break;
        case "money"://金额,保留2位小数
            $rule = '/^(([0-9]|([1-9][0-9]{0,9}))((\.[0-9]{1,2})?))$/';
            break;
        case "score"://积分
            $rule = '/[0-9]{1,10}/';
            break;
        case "qq"://qq
            $rule = '/[0-9]{4,13}/';
            break;
        case "url"://url
            $rule = '/^(https?|ftp|mms):\/\/?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\/?%#&=]*)?$/i';
            break;
        default:
            break;
    }
    return $rule;
}


function uuid()
{
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        mt_srand(( double )microtime() * 10000); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
        $charid = strtoupper(md5(uniqid(rand(), true))); //根据当前时间（微秒计）生成唯一id.
        $hyphen = chr(45); // "-"
        $uuid = '' . //chr(123)// "{"
            substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
        //.chr(125);// "}"
        return $uuid;
    }
}

/*
 * 生成随机数加强版
 * @param $k 生成随机数字的模式,n为数字,s为字符串,r为字符串或数字
 * @param $n 生成随机数字长度
 * @return 生成的随机数
 * */
function rand_str($k = 'n', $n = 6)
{
    $r = "";

    switch (strtolower($k)) {
        case "n":
            $pattern = '1234567890';
            $length = 9;
            break;
        case "s":
            $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
            $length = 52;
            break;

        case "r":
            $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
            $length = 62;
            break;
        default:
            $pattern = '1234567890';
            $length = 9;
            break;
    }

    for ($i = 0; $i < $n; $i++) {
        $r .= $pattern{mt_rand(0, $length)};    //生成php随机数
    }
    return $r;
}

function chk_id($id)
{
    if (empty($id)) return false;
    if (!is_numeric($id)) return false;
    return true;
}

function format_date($date)
{
    $time = strtotime($date);
    if (empty($time)) return date("Y-m-d");//时间格式错误
    return date("Y-m-d", $time);
}

function deldir($dir)
{
    //先删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }

    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

/*删除指定目录下的文件，不删除目录文件夹*/
function delFile($dirName)
{
    if (file_exists($dirName) && $handle = opendir($dirName)) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (file_exists($dirName . '/' . $item) && is_dir($dirName . '/' . $item)) {
                    delFile($dirName . '/' . $item);
                } else {
                    if (unlink($dirName . '/' . $item)) {
                        return true;
                    }
                }
            }
        }
        closedir($handle);
    }
}

//毫秒时间戳
function m_sec_timestamp()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

//生成全局唯一订单号
function create_order_num($i = '')
{
    return date("ymdhis") . rand(1000, 9999) . $i;
}


function rt_json($post, $type = null)
{

    if (is_array($post)) {
        if ($type) return json_encode($post, JSON_UNESCAPED_UNICODE);
        return json_encode($post);
    } else {
        if ($type) return json_encode(array("code" => $post), JSON_UNESCAPED_UNICODE);
        return json_encode(array("code" => $post));
    }
}


function die_json($post, $type = null)
{
    die(rt_json($post, $type));
}

/**
 * 汉字转拼音
 * @param string $str 待转换的字符串
 * @param string $charset 字符串编码
 * @param bool $ishead 是否只提取首字母
 * @return string 返回结果
 */
function get_pinyin($str, $charset = "utf-8", $ishead = 0)
{
    $restr = '';
    $str = trim($str);
    if ($charset == "utf-8") {
        $str = iconv("utf-8", "gb2312", $str);
    }
    $slen = strlen($str);
    $pinyins = array();
    if ($slen < 2) {
        return $str;
    }
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/Public/statics/pinyin.dat', 'r');

    while (!feof($fp)) {
        $line = trim(fgets($fp));
        $pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
    }
    fclose($fp);

    for ($i = 0; $i < $slen; $i++) {
        if (ord($str[$i]) > 0x80) {
            $c = $str[$i] . $str[$i + 1];
            $i++;
            if (isset($pinyins[$c])) {
                if ($ishead == 0) {
                    $restr .= $pinyins[$c];
                } else {
                    $restr .= $pinyins[$c][0];
                }
            } else {
                $restr .= "_";
            }
        } else if (preg_match("/[a-z0-9]/i", $str[$i])) {
            $restr .= $str[$i];
        } else {
            $restr .= "_";
        }
    }
    return $restr;
}

//字符串转数组去空
function str_to_arr($str, $split = ",")
{
    $arr = explode($split, $str);
    $arr = array_filter($arr);
    return $arr;
}

//字符串去空逗号分割
function str_implode($str, $split = ",")
{
    $arr = explode($split, $str);
    $arr = array_filter($arr);
    return implode(",", $arr);
}


//在时间基础上改变日期时间
function change_date_str($str, $date)
{
    return strtotime($str, strtotime($date));
}

//对象转数组
function obj_to_arr($obj)
{
    return json_decode(json_encode($obj), true);
}

//数组转get格式
function arr2get($para)
{
    $arg = "";
    while (list ($key, $val) = each($para)) {
        $arg .= $key . "=" . urlencode($val) . "&";
    }

    //去掉最后一个&字符
    $arg = substr($arg, 0, count($arg) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}

//强制浏览器不缓存
function clear_browesr_cache()
{
    header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");// 设置此页面的过期时间
    header("Last-Modified:" . gmdate(" D, d M Y H:i:s ") . "GMT");// 设置此页面的最后更新日期
    header("Cache-Control: no-cache, must-revalidate");// 告诉客户端浏览器不使用缓存
    header("Pragma: no-cache ");// 告诉客户端浏览器不使用缓存
}


//模拟get请求
function curl_get($url)
{
    //初始化
    $ch = curl_init();

    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    //执行并获取HTML文档内容
    $output = curl_exec($ch);

    //释放curl句柄
    curl_close($ch);

    //返回获得的数据
    return $output;
}

//模拟get和post
function curl_http($url = '', $post_data = false)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

//xml转数组
function xml_to_arr($xml)
{
    libxml_disable_entity_loader(true);
    return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
}

//xml转json
function xml_to_json($xml)
{
    return json_encode(xml_to_arr($xml));
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


function curl_https_get($url)
{
    $curl = curl_init();


    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
    $s = curl_exec($curl);
    dump($s);
    curl_close($curl);
    return $s;
}

//转换gbk gb2312为utf8
function gbk2utf8($str)
{
    $charset = mb_detect_encoding($str, array('UTF-8', 'GBK', 'GB2312'));
    $charset = strtolower($charset);
    if ('cp936' == $charset) {
        $charset = 'GBK';
    }
    if ("utf-8" != $charset) {
        $str = mb_convert_encoding($str, "UTF-8", "GBK");
    }
    return $str;
}

//用户是否登录
function is_user_login()
{
    $user_id = session("user.id");
    if ($user_id) return true;
    return false;
}

//获取用户登录名称
function login_username()
{
    if (is_user_login()) {
        $info = session("user");
        return $info['username'];
    } else return false;
}

function CheckPost($C_post)
{
    if(preg_match("/^\d{6}$/",$C_post))
        return true;
}


//检验身份证号
function checkIdCard($idcard){
    //身份证为非必填,不填写则不进行身份证验证
    if(empty($idcard)) {
        return true;
    }

    // 只能是18位
    if(strlen($idcard)!=18){
        return false;
    }

    // 取出本体码
    $idcard_base = substr($idcard, 0, 17);

    // 取出校验码
    $verify_code = substr($idcard, 17, 1);

    // 加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

    // 校验码对应值
    $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

    // 根据前17位计算校验码
    $total = 0;
    for($i=0; $i<17; $i++){
        $total += substr($idcard_base, $i, 1)*$factor[$i];
    }

    // 取模
    $mod = $total % 11;

    // 比较校验码
    if($verify_code == $verify_code_list[$mod]){
        return true;
    }else{
        return false;
    }

}

//检验手机号码

function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}
//获取用户登录id
function login_id()
{
    if (is_user_login()) {
        $info = session("user");
        return $info['id'];
    } else return false;
}


//获取用户登录id
function login_group()
{
    if (is_user_login()) {
        $info = session("user");
        if (!empty($info['user_group'])) {
            return (string)$info['user_group'];
        } else {
            return false;
        }
    } else return false;

}


//获取用户登录名称
function get_user_nick_name()
{
    if (is_user_login()) {
        $info = session("user");
        if(empty($info['nick_name']))return '匿名用户';
        return $info['nick_name'];
    } else return false;
}

/**
 * 获取当前时间戳，精确到毫秒,如果包含参数,格式为'2016-10-5 12:12:12.538'则转换为参数的时间戳
 * @param string $time 精确到毫秒的时间
 * @return int 返回值
 */
function microtime_float($time = null)
{
    if ($time) {
        $tmp = explode('.', $time);
        if (empty($tmp[1])) $m_time = strtotime($tmp[0]);
        else $m_time = strtotime($tmp[0]) . substr($tmp[1], 0, 3);
    } else {
        list($usec, $sec) = explode(" ", microtime());
        $time = ((float)$usec + (float)$sec) * 1000;
        $m_time = round($time, 0);
    }
    return $m_time;
}


/**
 * 格式化时间戳，精确到毫秒，x代表毫秒
 * @param string $time 毫秒级时间戳
 * @param int $type 1返回正常时间戳,2返回正常时间戳加毫秒,3返回正常时间戳加毫秒 加点再加毫秒
 * @param string $format 格式化的形式
 * @return string 返回值
 */

function microtime_format($time, $type = 1, $format = 'His')
{
    $length = strlen($time);
    if (!is_numeric($time) || $length < 13) return false;

    $sec = intval(substr($time, 0, $length - 3));
    $m_sec = substr($time, $length - 3, $length);

    switch ($type) {
        case "1":
            return date($format, $sec);
        case "2":
            return date($format, $sec) . $m_sec;
        default:
            return date($format, $sec) . '.' . $m_sec;
    }
}


//隐藏字符中间部分
function hidden_str_middel($str, $pre_num = 3, $last_num = 3)
{
    $len = strlen($str);
    $star_len = $len - $pre_num - $last_num;
    $star_len = $star_len < 0 ? 3 : $star_len;
    $star = '';
    for ($i = 0; $i < $star_len; $i++) {
        $star = $star . '*';
    }
    return substr($str, 0, $pre_num) . $star . substr($str, $len - $last_num, $last_num);
}


/**根据当前数量获取在数组中的坐标位置*/
function get_cid_by_num($num = null, $split_num = null)
{
    if (!isset($num) || !isset($split_num)) return false;
    $s1 = floor($num / $split_num);
    if ($s1 > 0) {
        $s2 = (int)$num % $split_num;
    } else {
        $s2 = (int)$num % $split_num;
    }

    return array($s1, $s2);
}


/**支付宝的函数START*/

/* *
 * 支付宝接口公用函数
 * 详细：该类是请求、通知返回两个文件所调用的公用函数核心处理文件
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * @return 拼接完成以后的字符串
 */
function createLinkstring($para)
{
    $arg = "";
    while (list ($key, $val) = each($para)) {
        $arg .= $key . "=" . $val . "&";
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, count($arg) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}


function arr_to_get($post)
{
    $str = '';
    foreach ($post as $k => $v) {
        $str = $str . '&' . $k . '=' . $v;
    }

    return trim($str, "&");
}

/**
 * 除去数组中的空值和签名参数
 * @param $para 签名参数组
 * @return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para)
{
    $para_filter = array();
    while (list ($key, $val) = each($para)) {
        if ($key == "sign" || $key == "sign_type" || $val == "") continue;
        else    $para_filter[$key] = $para[$key];
    }
    return $para_filter;
}

/**
 * 对数组排序
 * @param $para 排序前的数组
 * @return 排序后的数组
 */
function argSort($para)
{
    ksort($para);
    reset($para);
    return $para;
}

/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logResult($word = '')
{
    $fp = fopen("log.txt", "a");
    flock($fp, LOCK_EX);
    fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S", time()) . "\n" . $word . "\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * 远程获取数据，POST模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * @param $para 请求的数据
 * @param $input_charset 编码格式。默认值：空值
 * @return 远程输出的数据
 */
function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '')
{

    if (trim($input_charset) != '') {
        $url = $url . "_input_charset=" . $input_charset;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl, CURLOPT_POST, true); // post传输数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $para);// post传输数据
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}

/**
 * 远程获取数据，GET模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * @return 远程输出的数据
 */
function getHttpResponseGET($url, $cacert_url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}

/**
 * 实现多种字符编码方式
 * @param $input 需要编码的字符串
 * @param $_output_charset 输出的编码格式
 * @param $_input_charset 输入的编码格式
 * @return 编码后的字符串
 */
function charsetEncode($input, $_output_charset, $_input_charset)
{
    $output = "";
    if (!isset($_output_charset)) $_output_charset = $_input_charset;
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists("iconv")) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else die("sorry, you have no libs support for charset change.");
    return $output;
}

/**
 * 实现多种字符解码方式
 * @param $input 需要解码的字符串
 * @param $_output_charset 输出的解码格式
 * @param $_input_charset 输入的解码格式
 * @return 解码后的字符串
 */
function charsetDecode($input, $_input_charset, $_output_charset)
{
    $output = "";
    if (!isset($_input_charset)) $_input_charset = $_input_charset;
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists("iconv")) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else die("sorry, you have no libs support for charset changes.");
    return $output;
}


/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * @return 签名结果
 */
function md5Sign($prestr, $key)
{
    $prestr = $prestr . $key;
    return md5($prestr);
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * @return 签名结果
 */
function md5Verify($prestr, $sign, $key)
{
    $prestr = $prestr . $key;
    $mysgin = md5($prestr);

    if ($mysgin == $sign) {
        return true;
    } else {
        return false;
    }
}

function phpcharset($data, $to)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $data[$key] = phpcharset($val, $to);
        }
    } else {
        $encode_array = array('ASCII', 'UTF-8', 'GBK', 'GB2312', 'BIG5');
        $encoded = mb_detect_encoding($data, $encode_array);
        $to = strtoupper($to);
        if ($encoded != $to) {
            $data = mb_convert_encoding($data, $to, $encoded);
        }
    }
    return $data;
}

//返回签名
function create_token($data, $time)
{
    return md5(C("PAY_SUCCESS_TOKEN") . $data . $time);
}


//微信的东西提取

/**
 * 输出xml字符
 * @throws WxPayException
 **/
function ToXml($arr)
{
    if (!is_array($arr)
        || count($arr) <= 0
    ) {
        return false;
    }

    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
    }
    $xml .= "</xml>";
    return $xml;
}

/**
 * 生成微信签名
 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
 */
function MakeSign($arr)
{
    //签名步骤一：按字典序排序参数
    ksort($arr);
    $string = ToUrlParams($arr);
    //签名步骤二：在string后加入KEY
    require "./Application/Common/Lib/Weichat/WxPay.Config.php";
    $string = $string . "&key=" . \WxPayConfig::KEY;
    //签名步骤三：MD5加密
    $string = md5($string);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($string);
    return $result;
}

/**
 * 格式化参数格式化成url参数
 */
function ToUrlParams($arr)
{
    $buff = "";
    foreach ($arr as $k => $v) {
        if ($k != "sign" && $v != "" && !is_array($v)) {
            $buff .= $k . "=" . $v . "&";
        }
    }

    $buff = trim($buff, "&");
    return $buff;
}

/**支付宝的函数END*/

//die_json函数的进一步封装
function show_result($code = null, $msg = null, $flag = false)
{
    if ($flag) {
        die_json(array("code" => $code, 'msg' => $msg), 1);
    }
    return array("code" => $code, 'msg' => $msg);
}

//die_json函数的进一步封装
function die_result($code = null, $msg = null, $flag = null)
{
    if ($flag == 1) {
        return array("code" => $code, 'msg' => $msg);
    } else {
        die_json(array("code" => $code, 'msg' => $msg), 1);
    }

}

//输入日志信息
function log_w($txt = '', $keywords = '')
{
    if (!is_dir('./log')) {
        mkdir('./log');
    }
    $t = !empty($keywords) ? "关键字:" . $keywords . "\r\n" : '';
    file_put_contents("./log/log_" . date("Y-m-d") . ".txt",/* $split_start.*/
        "\r\n" . date("Y-m-d H:i:s") . ": \r\n" . $t . $txt . "\r\n" /*. $split_end*/, FILE_APPEND);
}

//判断是否为空排除0
function is_empty_but_zero($val)
{
    return ($val === 0) ? true : !empty($val);
}

//返回信息的封装
function rt($code = null, $val = null, $msg = null)
{
    if (!empty($msg)) {
        log_w($msg);
    }
    return array("code" => $code, "value" => $val);
}


/* *
 * 支付宝接口RSA函数
 * 详细：RSA签名、验签、解密
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */

/**
 * RSA签名
 * @param string $data 待签名数据
 * @param string $private_key_path 商户私钥文件路径
 * return string 签名结果
 */
function rsaSign($data, $private_key_path)
{
    $priKey = file_get_contents($private_key_path);
    $res = openssl_get_privatekey($priKey);
    openssl_sign($data, $sign, $res);
    openssl_free_key($res);
    //base64编码
    $sign = base64_encode($sign);
    return $sign;
}

/**
 * RSA验签
 * @param $data 待签名数据
 * @param $ali_public_key 支付宝的公钥文件路径
 * @param $sign 要校对的的签名结果
 * return 验证结果
 */
function rsaVerify($data, $ali_public_key_path, $sign)
{
//    $pubKey = file_get_contents($ali_public_key_path);
    $pubKey = $ali_public_key_path;

//    $pubKey = $ali_public_key;
    $res = openssl_get_publickey($pubKey);
    $result = (bool)openssl_verify($data, base64_decode($sign), $res);

    openssl_free_key($res);

    return $result;
}

/**
 * RSA解密
 * @param $content 需要解密的内容，密文
 * @param $private_key_path 商户私钥文件路径
 * return 解密后内容，明文
 */
function rsaDecrypt($content, $private_key_path)
{
    $priKey = file_get_contents($private_key_path);
    $res = openssl_get_privatekey($priKey);
    //用base64将内容还原成二进制
    $content = base64_decode($content);
    //把需要解密的内容，按128位拆开解密
    $result = '';
    for ($i = 0; $i < strlen($content) / 128; $i++) {
        $data = substr($content, $i * 128, 128);
        openssl_private_decrypt($data, $decrypt, $res);
        $result .= $decrypt;
    }
    openssl_free_key($res);
    return $result;
}


//返回格式化后的分页信息
function page_info_format($sum, $page_size, $current)
{
    return array(
        'total' => $sum,
        'page_count' => ($sum % $page_size) == 0 ? floor($sum / $page_size) : floor($sum / $page_size) + 1,
        'page_size' => $page_size,
        'page_current' => $current
    );
}


//$flag=true时允许发送,重试的时候防止多发
function send_sms_luosimao($phone, $phone_code, $luosimao_key, $plat_name = '香肠')
{

    $content = "验证码:" . $phone_code . ",关注微信公众号送体验金！【" . $plat_name . "】";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'api:key-' . $luosimao_key);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $phone, 'message' => $content));

    $res = curl_exec($ch);
    curl_close($ch);
    //file_put_contents("sms.txt", "luosimao send success ! " . $phone . " : " . $res, FILE_APPEND);
    $json = json_decode($res, true);
    $json['plat'] = 'luosimao';

    return $json;

}


/**
 * 淘宝大鱼发送短信
 * @param $mobile
 * @param $phone_code
 * @return mixed|SimpleXMLElement
 */
function TbSend($phone, $phone_code, $alidayu_key, $alidayu_secret)
{

    $product = '香肠';
    require __DIR__ . '/lib/tb_sdk/TopSDK.php';
    $c = new TopClient;
    $c->appkey = $alidayu_key;
    $c->secretKey = $alidayu_secret;
    $req = new app\lib\tb_sdk\top\request\AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName("注册验证");
    $param = '{"code":"' . $phone_code . '","product":"' . $product . '"}';
    $req->setSmsParam($param);
    $req->setRecNum($phone);
    $req->setSmsTemplateCode("SMS_6740183");
    $resp = $c->execute($req);
    return $resp;
}

/**
 * 百度发送短信
 * @param $mobile
 * @param $message
 * @return mixed
 */
function KaixintongSend($phone, $phone_code)
{
    $message = '您的验证码是 ' . $phone_code;
    $ch = curl_init();
    $url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile=' . $phone . '&content=' . $message . '【香肠】' . '&tag=2';
    $header = array(
        'apikey: 2384203ff39f6f1700185ba602ac0b43',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch, CURLOPT_URL, $url);
    $res = curl_exec($ch);

    return $res;
}

//通用返回值
function com_return($code = 300, $msg = 'error')
{
    die_json(array('code' => $code, 'msg' => $msg));
}

//错误返回值
function wrong_return($msg = 'error')
{
    die_json(array('code' => "-1", 'msg' => $msg), 1);
}

//正确返回值
function ok_return($msg = 'success')
{
    die_json(array('code' => "1", 'msg' => $msg));
}

//判断是否为外部地址
function is_web_url($url = '')
{
    if (preg_match('/^\d+\:\/\/.*$/', $url)) return true;
    return false;
}

//本站签名方法
function sign_by_key($arr = array('uid' => '', 'timestamp' => ''))
{
    $uid = empty($uid) ? 0 : $arr['uid'];
    $timestamp = empty($arr['timestamp']) ? microtime() : $arr['timestamp'];
    //$m = M('conf', 'sp_');
   // $r = $m->where(array('name' => 'TOKEN_ACCESS'))->field(array('value'))->find();
    $key = C('TOKEN_ACCESS');
    return md5($uid . $timestamp . $key);
}

//startWith
function startWith($haystack, $needle)
{

    return strpos($haystack, $needle) === 0;

}

//endWith
function endWith($haystack, $needle)
{

    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

//生成短网址
function dwz_filter($action, $param = [], $suffix = true, $domain = false)
{//,$suffix=''
    $action = strtolower($action);
    $dwz = '';
    try {
        switch ($action) {
            //跳转2
            case 'goods/jump_to_goods_buying':
                $dwz = "/detail/{$param['gid']}";
                unset($param['gid']);
                break;
            //商品详情
            case 'goods/detail':
                $dwz = "/detail/{$param['id']}";
                unset($param['id']);
                break;
            //帮助
            case 'help/read':
                if (key_exists('name', $param)) {
                    $dwz = "/help/{$param['name']}";
                    unset($param['name']);
                } elseif (key_exists('id', $param)) {
                    $dwz = "/help-{$param['id']}";
                    unset($param['id']);
                }
                break;
            //搜索
            case 'lists/search':
                if (key_exists('keyword', $param)) {
                    $dwz = "/search/{$param['keyword']}";
                } else {
                    $dwz = "/search/";
                }
                unset($param['keyword']);
                break;
            //分类页面
            case 'lists/index':
                $category = key_exists('category', $param) ? intval($param['category']) : 0;
                $sort = key_exists('sort', $param) ? intval($param['sort']) : 0;
                $page = key_exists('page', $param) ? intval($param['page']) : 1;

                $dwz = "/category/{$category}-{$sort}-{$page}";

                unset($param['category']);
                unset($param['sort']);
                unset($param['page']);
                break;
            //晒单分享
            case 'share/index':
                $dwz = "/share";
                break;
            //晒单详情
            case 'share/detail':
                $dwz = "/share/{$param['id']}";
                unset($param['id']);
                break;
            //ta的详情首页
            case 'ta/index':
                $dwz = "/ta/{$param['uid']}";
                unset($param['uid']);
                break;
            //ta的晒单
            case 'ta/share':
                $dwz = "/ta/share-{$param['uid']}";
                unset($param['uid']);
                break;
            //夺宝记录
            case 'ta/history':
                $dwz = "/history/{$param['uid']}";
                unset($param['uid']);
                break;
            //幸运记录
            case 'ta/luck':
                $dwz = "/luck/{$param['uid']}";
                unset($param['uid']);
                break;
            //十元专区
            case 'lists/ten':
                $dwz = "/ten";
                break;
            //最新揭晓
            case 'lists/results':
                $dwz = "/results";
                break;
            //登录
            case 'user/login':
                $dwz = '/login';
                break;
            //注册
            case 'user/reg':
                $dwz = '/reg';
                break;
            //找回密码
            case 'user/forgot':
                $dwz = '/forgot';
                break;
            //用户中心
            case 'ucenter/index':
                $dwz = '/ucenter';
                break;
            case 'ucenter/deposer':
                $dwz = '/deposer';
                break;
            case 'ucenter/luck':
                $dwz = '/luck';
                break;
            case 'ucenter/order':
                $dwz = '/order';
                break;
            case 'ucenter/person':
                $dwz = '/person';
                break;
            case 'ucenter/base':
                $dwz = '/base';
                break;
            case 'ucenter/addr':
                $dwz = '/addr';
                break;
            case 'ucenter/charge':
                $dwz = '/charge';
                break;
            //用户中心-充值
            case 'pay/recharge':
                $dwz = '/recharge';
                break;
            default:
                return U($action, $param);
        }
    } catch (Exception $ex) {
        //出错则返回默认链接
        return U($action, $param);
    }
    if (strlen($dwz) > 0) {
        //参数补充
        $query = http_build_query($param);
        $suffix = $suffix === true ? '.html' : $suffix;
        return $dwz . (strlen($query > 0) ? '?' . $query : '') . (endWith($dwz, '/') ? '' : $suffix);
    } else {
        return U($action, $param, $suffix, $domain);
    }
}

//生成短网址
function wap_dwz_filter($action, $param = [], $suffix = true, $domain = false)
{//,$suffix=''
    $action = strtolower($action);
    $dwz = '';
    try {
        switch ($action) {
            //首页
            case 'index/index':
                $dwz = "/mobile";
                break;
            //搜索
            case 'goods/search':
                $dwz = "/mobile/search";
                break;
            //搜索结果
            case 'goods/search_result':
                $dwz = "/mobile/search_result";
                break;
            //全部商品
            case 'index/all_goods':
                if(!empty($param['cate'])){
                    $dwz = "/mobile/all_goods/{$param['cate']}";
                }else{
                    $dwz = "/mobile/all_goods";
                }
                unset($param['cate']);
                break;
            //晒单
            case 'index/all_share_order':
                $dwz = "/mobile/all_share_order";
                break;
            //购物车
            case 'cart/cart_list':
                $dwz = "/mobile/car_list";
                break;
            //个人中心
            case 'users/personal_center':
                $dwz = "/mobile/personal_center";
                break;
            //首页轮播商品跳转
            case "goods/jump_to_goods_buying":
                $dwz = "/mobile/jump_to_goods_buying/{$param['gid']}";
                unset($param['gid']);
                break;
            //推广中心
            case 'spread/index':
                $dwz = "/mobile/Spread";
                break;
            //商品详情
            case 'goods/goods_detail':
                $dwz = "/mobile/detail/{$param['nper_id']}";
                unset($param['nper_id']);
                break;
            //0元商品详情
            case 'goods/zero_detail':
                $dwz = "/mobile/zero_detail/{$param['nper_id']}";
                unset($param['nper_id']);
                break;
            //什么是香肠一元购
            case 'article/help':
                $dwz = "/mobile/help";
                break;
            //他人用户中心
            case 'otherUsers/other_person_center':
                $dwz = "/mobile/other_person_center/{$param['uid']}";
                unset($param['uid']);
                break;
            //晒单详情
            case 'otherUsers/share_detail':
                $dwz = "/mobile/share_detail/{$param['share_id']}";
                unset($param['share_id']);
                break;
            //确认订单
            case 'order/confirm_order':
                $dwz = "/mobile/confirm_order";
                break;
            //充值页面
            case 'order/recharge':
                $dwz = "/mobile/charge";
                break;
            //个人资料
            case 'users/personal_data':
                $dwz = "/mobile/personal_data";
                break;
            //夺宝记录
            case 'buy/person_indiana':
                $dwz = "/mobile/person_indiana";
                break;
            //幸运记录
            case 'buy/personal_win_record':
                $dwz = "/mobile/personal_win_record";
                break;
            //我的晒单
            case 'buy/my_share_list':
                $dwz = "/mobile/my_share_list";
                break;
            //充值记录
            case 'order/recharge_record':
                $dwz = "/mobile/recharge_record";
                break;
            //夺宝客服
            case 'article/home_page':
                $dwz = "/mobile/service";
                break;
            //退出登录
            case 'users/logout':
                $dwz = "/mobile/logout";
                break;
            //支付宝充值
            case 'alipay/personal_charge':
                $dwz = "/mobile/ali_recharge";
                break;
            //微信充值
            case 'weixin/recharge':
                $dwz = "/mobile/wx_recharge";
                break;
            //爱贝充值
            case 'aipay/recharge':
                $dwz = "/mobile/ai_recharge";
                break;
            //修改上传头像
            case 'users/upload_portrait':
                $dwz = "/mobile/modify_head_image";
                break;
            //修改昵称
            case 'users/modify_nickname':
                $dwz = "/mobile/modify_nickname";
                break;
            //修改手机号
            case 'users/modify_phone':
                $dwz = "/mobile/modify_phone";
                break;
            //收货地址列表
            case 'users/address_list':
                $dwz = "/mobile/address_list";
                break;
            //收货地址详情
            case 'users/address_details':
                $dwz = "/mobile/address_details/{$param['address_id']}";
                break;
            //新增收货地址
            case 'users/add_address':
                $dwz = "/mobile/add_address";
                break;
            //修改收货地址
            case 'users/modify_address':
                $dwz = "mobile/modify_address";
                break;
            //查看中奖记录详细信息
            case 'buy/prize_info_confirm':
                $dwz = "/mobile/win_detail/{$param['win_record_id']}";
                break;
            //立即晒单
            case 'buy/submit_share_order':
                $dwz = "/mobile/submit_share_order/{$param['share_id']}";
                break;
            //客服中心
            case 'article/article':
                $dwz = "/mobile/article/{$param['id']}";
                break;
            //转为夺宝币
            case 'trans_xc':
                $dwz = "/mobile/trans_xc";
                break;
            //提现
            case 'extract/index':
                $dwz = "/mobile/extract";
                break;
            //查看二维码
            case 'me_codes':
                $dwz = "/mobile/me_codes";
                break;
            //推广明细
            case 'promote_detail':
                $dwz = "/mobile/promote_detail";
                break;
            //推广奖励模式
            case 'promote_reward':
                $dwz = "/mobile/promote_reward";
                break;
            //注册明细
            case 'register_detail':
                $dwz = "/mobile/register_detail";
                break;
            //注册奖励模式
            case 'register_reward':
                $dwz = "/mobile/register_reward";
                break;
            //添加银行卡账号
            case 'add_account':
                $dwz = "/mobile/add_account";
                break;
            //立即提现
            case 'go_extract':
                $dwz = "/mobile/go_extract";
                break;
            //提现记录
            case 'extracts':
                $dwz = "/mobile/extracts";
                break;
            //添加提现账号
            case 'account_sub':
                $dwz = "/mobile/account_sub";
                break;
            //余额转香肠币
            case 'spread/trans_2xc':
                $dwz = "/mobile/trans_2xc";
                break;
            //余额转夺宝币成功页面
            case 'trans_success':
                $dwz = "/mobile/trans_success";
                break;
            //分级别的分销列表页
            case 'promote_detail_list':
                $dwz = "/mobile/promote_detail_list/{$param['level']}";
                break;
            //第三方登录
            case 'login/login':
                $dwz = "/mobile/union_login/{$param['type']}";
                break;
            //注册
            case 'otherUsers/register':
                $dwz = "/mobile/register";
                break;
            //忘记密码
            case 'otherUsers/forget_password':
                $dwz = "/mobile/forget_password";
                break;
            default:
                return U($action, $param);
        }
    } catch (Exception $ex) {
        //出错则返回默认链接
        return U($action, $param);
    }
    if (strlen($dwz) > 0) {
        //参数补充
        $query = http_build_query($param);
        $suffix = $suffix === true ? '.html' : $suffix;
        return $dwz . (strlen($query > 0) ? '?' . $query : '') . (endWith($dwz, '/') ? '' : $suffix);
    } else {
        return U($action, $param, $suffix, $domain);
    }
}

//期号显示、幸运号码显示,@num:基础数字  @base_type:累加类型(0:期数  1:幸运数字)  @add:额外累加的指,默认0不填写
function num_base_mask($num = 0, $base_type = 0, $add = 0)
{
    $base = ($base_type === 0) ? 80000000 : 10000000;
    return intval(intval($num) + intval($base) + $add);
}


/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = 'cxphp')
{
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key] .= $value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function decode($string = '', $skey = 'cxphp')
{
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

function sp_testwrite($d)
{
    $tfile = "_test.txt";
    $fp = @fopen($d . "/" . $tfile, "w");
    if (!$fp) {
        return false;
    }
    fclose($fp);
    $rs = @unlink($d . "/" . $tfile);
    if ($rs) {
        return true;
    }
    return false;
}

function sp_dir_create($path, $mode = 0777)
{
    if (is_dir($path))
        return true;
    $ftp_enable = 0;
    $path = sp_dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir))
            continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}

function sp_dir_path($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}

/**
 * 终端操作,进行一次输出
 */
function echo_flush()
{
    flush();
    ob_flush();
}

/*
* $string： 明文 或 密文
* $operation：true表示解密,其它表示加密
* $key： 密匙
* $expiry：密文有效期
* */
function authcode($string, $operation = true, $key = '', $expiry = 0)
{

    if ($operation == true) {
        $string = str_replace('[a]', '+', $string);
        $string = str_replace('[b]', '&', $string);
        $string = str_replace('[c]', '/', $string);
    }
    $ckey_length = 4;
    $key = md5($key ? $key : 'livcmsencryption ');
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == true ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == true ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == true) {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {

            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        $ustr = $keyc . str_replace('=', '', base64_encode($result));
        $ustr = str_replace('+', '[a]', $ustr);
        $ustr = str_replace('&', '[b]', $ustr);
        $ustr = str_replace('/', '[c]', $ustr);
        return $ustr;
    }
}

//简易判断奇偶函数 用于filter
function is_odd($num)
{
    if (empty($num)) return false;
    return intval($num) % 2 == 1;
}

function is_even($num)
{
    if (empty($num)) return false;
    return intval($num) % 2 == 0;
}

function get_total_join() {
    //读取缓存中的配置如果不存在则获取并缓存3600秒
    if (empty($site_config = S('get_total_join'))) {
        $m_order = new \app\admin\model\OrderList();
        $num =  $m_order->get_total_join();
        $num = $num[0]['num'];
        S('get_total_join',$num, ['expire' => 3600]);
    }
    return S('get_total_join');
}

//使用配置的域名(可能是网址)获取域名 用于U函数的 (缓存)
function get_conf_domain($conf='WEBSITE_URL'/*WAP_WEBSITE_URL*/){
    $domain = S('conf_website_domain_'.strtolower($conf));
    if(empty($domain)){
        $domain = strtolower(trim(C($conf)));
        //是否为空
        if(empty($domain)){
            $domain=true;
        }else{
            $domain_subs = parse_url($domain);
            //是否为网址
            if(!empty($domain_subs) && key_exists('host',$domain_subs) && !empty($domain_subs['host'])){
                $domain = $domain_subs['host'];
            }else{
                //简易纯域名验证
                if(preg_match('/^[\w\-\.]{0,}$/',$domain)===false){
                    $domain=true;
                }
            }
        }
        S('conf_website_domain',$domain,3600);
    }
    return $domain;
}



//判断是否为手机浏览器
function mobile_device_detect($iphone=true,$android=true,$opera=true,$mobileredirect=false,$desktopredirect=false){

    $mobile_browser   = false; // set mobile browser as false till we can prove otherwise
    $user_agent       = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed
    $accept           = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed

    switch(true){ // using a switch against the following statements which could return true is more efficient than the previous method of using if statements

        case (preg_match('/.*ipod.*/',$user_agent)||preg_match('/.*iphone.*/',$user_agent)); // we find the words iphone or ipod in the user agent
            $mobile_browser = $iphone; // mobile browser is either true or false depending on the setting of iphone when calling the function
            if(substr($iphone,0,4)=='http'){ // does the value of iphone resemble a url
                $mobileredirect = $iphone; // set the mobile redirect url to the url value stored in the iphone value
            } // ends the if for iphone being a url
            break; // break out and skip the rest if we've had a match on the iphone or ipod

        case (preg_match('/.*android.*/',$user_agent));  // we find android in the user agent
            $mobile_browser = $android; // mobile browser is either true or false depending on the setting of android when calling the function
            if(substr($android,0,4)=='http'){ // does the value of android resemble a url
                $mobileredirect = $android; // set the mobile redirect url to the url value stored in the android value
            } // ends the if for android being a url
            break; // break out and skip the rest if we've had a match on android

        case (preg_match('/.*opera mini.*/',$user_agent)); // we find opera mini in the user agent
            $mobile_browser = $opera; // mobile browser is either true or false depending on the setting of opera when calling the function
            if(substr($opera,0,4)=='http'){ // does the value of opera resemble a rul
                $mobileredirect = $opera; // set the mobile redirect url to the url value stored in the opera value
            } // ends the if for opera being a url
            break; // break out and skip the rest if we've had a match on opera

        case (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|mobile|pda|psp|treo)/i',$user_agent)); // check if any of the values listed create a match on the user agent - these are some of the most common terms used in agents to identify them as being mobile devices - the i at the end makes it case insensitive
            $mobile_browser = true; // set mobile browser to true
            break; // break out and skip the rest if we've preg_match on the user agent returned true

        case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); // is the device showing signs of support for text/vnd.wap.wml or application/vnd.wap.xhtml+xml
            $mobile_browser = true; // set mobile browser to true
            break; // break out and skip the rest if we've had a match on the content accept headers

        case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); // is the device giving us a HTTP_X_WAP_PROFILE or HTTP_PROFILE header - only mobile devices would do this
            $mobile_browser = true; // set mobile browser to true
            break; // break out and skip the final step if we've had a return true on the mobile specfic headers

        case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','comp'=>'comp','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','ppc;'=>'ppc;','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','tosh'=>'tosh','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',))); // check against a list of trimmed user agents to see if we find a match
            $mobile_browser = true; // set mobile browser to true
            break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it

    } // ends the switch

    if($mobile_browser==true){
        return true;
    }
    else{
        return false;
    }
} // ends function mobile_device_detect
