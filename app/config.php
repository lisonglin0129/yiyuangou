<?php

return [
    'category_goods' => 'xiangchang',//默认的商品
    'url_route_on' => true,
    'log' => [
//        'type' => 'trace', // 支持 socket trace file
    ],
    // 默认模块名
    'default_module' => __default_module__,
    // 默认控制器名
    'default_controller' => 'Index',
    // 默认操作名
    'default_action' => 'index',
    'parse_str' => [
        '__UPLOAD_DOMAIN__' => __STATIC_PREFIX__,
        '__static__' => __STATIC_PREFIX__ . '/theme/' . __demo_static__,
        '__common__' => __STATIC_PREFIX__ . '/common',
        '__yyg__' => __STATIC_PREFIX__ . '/common/yyg',
        '__upload__' => __STATIC_PREFIX__ . '',
        '__plugin__' => __STATIC_PREFIX__ . '/common/plugin',
        '__ROOT__' => __STATIC_PREFIX__ . '/',
        '__AVATAR__DEFAULT__' => __STATIC_PREFIX__ . '/common/img/avatar.jpg',
        '__ADMIN_ASSETS__' => __STATIC_PREFIX__ . '/common/admin/assets',
        '__MOBILE_IMG__' => __STATIC_PREFIX__ . '/common/mobile/img',
        '__MOBILE_CSS__' => __STATIC_PREFIX__ . '/common/mobile/css',
        '__MOBILE_JS__' => __STATIC_PREFIX__ . '/common/mobile/js',
        '__MOBILE_FONTS__' => __STATIC_PREFIX__ . '/common/mobile/fonts',
        '__MOBILE_STATIC__' => __STATIC_PREFIX__ . '/static',
        '__noimg__' => __STATIC_PREFIX__ . '/common/yyg/images/empty_img.jpg',
    ],
//    'default_return_type'=>'json'
    'extra_config_list' => ['database', 'fang_zhu_ru'],  // 扩展配置文件
    //SESSION配置
    'session' => [
        'id' => '',
        'var_session_id' => '',  // session_ID的提交变量,解决flash上传跨域
        'prefix' => 'xiangchang518',   // session 前缀
        'type' => '',  // 驱动方式 支持redis memcache memcached
        'auto_start' => true,  // 是否自动开启 session
    ],

    /**网站相关配置 START*/
    //网站版本号
    'WEB_VERSION_NUM' => 'V2.0',
    //订单关闭时间
    'ORDER_CLOSE_TIME' => 1800,
    //该配置只能在安装时修改一次,运行时不允许再次修改
    'TOKEN_ACCESS' => 'UW4RMqDCFjyVk3tQCCbM',
    //关闭网站 为字符串的true
    'WEB_SITE_CLOSE' => 'false',
    //是否开启调试如果为 'true'  全局付款订单的价格为0.01
    'OPEN_TEST_ENV' => 'true',
    //获取机器人的接口
    'RT_GET_USER_API' => 'http://auth.mengdie.com/index.php/core/rtuser/get_user',
    //是否开启0元购,1:开启 -1:关闭
    'ZERO_START' => '-1',
    //是否开启SQL注入拦截,1:开启 -1:关闭
    'OPEN_SQL_INJECT' => '1',
    /**网站相关配置 END*/


    /**微信相关配置 START*/
    //微信红包有效期
    'PACKET_LIFE_TIME' => '5',//微信红包有效期时间(天)
    /**微信相关配置 END*/

    /**
     * 开启行为日志 1开启－1开启
     */
    //后台
    'ADMIN_LOG_BEHAVIOR'=>'1',
    //手机端
    'MOBILE_LOG_BEHAVIOR'=>'1',
    //app
    'APP_LOG_BEHAVIOR'=>'1',
    //pc
    'PC_LOG_BEHAVIOR'=>'1',
    //无需纪录的日志行为
    'NO_LOG_URL'=>[
        'core/gdfc/trigger_open','core/collect/index','core/rt/auto_exec','core/gdfc/scan_timeout_nper','admin/rt_presets/run_task_1min'
    ]


];
