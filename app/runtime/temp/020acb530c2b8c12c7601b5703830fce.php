<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:54:"D:\webroot\mengdie_yyg\app\admin\view\index\index.html";i:1468303702;s:52:"D:\webroot\mengdie_yyg\app\admin\view\base/base.html";i:1468303702;s:58:"D:\webroot\mengdie_yyg\app\admin\view\base/common_css.html";i:1468303702;s:54:"D:\webroot\mengdie_yyg\app\admin\view\base/navbar.html";i:1468303702;s:57:"D:\webroot\mengdie_yyg\app\admin\view\base/common_js.html";i:1468303702;s:56:"D:\webroot\mengdie_yyg\app\admin\view\base/base_url.html";i:1468303702;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title></title>
    <meta name="keywords" content="<?php echo (isset($keywords) && ($keywords !== '')?$keywords:''); ?>" />
    <meta name="description" content="<?php echo (isset($description) && ($description !== '')?$description:''); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/font-awesome/css/font-awesome.min.css" />
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/font-awesome-ie7.min.css" />
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/font-awesome/css/font-awesome-ie7.min.css" />
<![endif]-->
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/google_css.css" />
<!-- ace styles -->
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/ace.min.css" />
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/ace-rtl.min.css" />
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/ace-skins.min.css" />
<!--[if lte IE 8]>
<link rel="stylesheet" type="text/css" href="__ADMIN_ASSETS__/css/ace-ie.min.css" />
<![endif]-->

<link rel="stylesheet" type="text/css" href="__common__/admin/css/main.css" />
<link rel="stylesheet" type="text/css" href="__plugin__/jrange/jquery.range.css" />
    
    <style>
        .nav.nav-list .fa{
            font-size: 14px;
            margin-left: 8px;
            margin-top: 6px;
        }
    </style>
</head>
<body>
<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try {
            ace.settings.check('navbar', 'fixed')
        } catch (e) {
        }
    </script>

    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="/" class="navbar-brand">
                <small>
                    <img src="<?php echo C('WEBSITE_LOGO'); ?>" style="height: 25px"
                         alt="">
                    <span style="color:#fff;font-size: 15px;">一元购管理后台</span>
                </small>
            </a><!-- /.brand -->
        </div><!-- /.navbar-header -->

        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav" style="height: auto;">
                <?php if($user_type != '2'): ?>
                <li class="green">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon-envelope icon-animated-vertical"></i>
                        <span class="badge badge-success"><?php echo (isset($data['num']) && ($data['num'] !== '')?$data['num']:''); ?></span>
                    </a>

                    <ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                        <li class="dropdown-header">
                            <i class="icon-envelope-alt"></i>
                            当前未晒单<?php echo (isset($data['num']) && ($data['num'] !== '')?$data['num']:''); ?>条
                        </li>
                        <?php if(is_array($data['info'])): $i = 0; $__LIST__ = $data['info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?>
                            <li>
                                <a href="<?php echo U('/admin/win/show_order',array('id'=>$vo1['id'],'notice'=>1)); ?>">
                                <span class="msg-body">
                                    <span class="msg-titl  e">
                                        机器人<?php echo $vo1['luck_user']; ?>未晒单，请晒单！
                                    </span>
                                </span>
                                </a>
                            </li>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        <li>
                            <a href="<?php echo U('/admin/win/index'); ?>">
                                查看更多
                                <i class="icon-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                 <?php endif; ?>
                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <!--<img class="nav-user-photo" src="__ADMIN_ASSETS__/avatars/user.jpg" alt="" />-->
								<span class="user-info">
									<small>欢迎您,</small>
									<?php echo get_user_name();; ?>
								</span>

                        <i class="icon-caret-down"></i>
                    </a>

                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <!--<li>-->
                        <!--<a href="#">-->
                        <!--<i class="icon-cog"></i>-->
                        <!--Settings-->
                        <!--</a>-->
                        <!--</li>-->

                        <!--<li>-->
                        <!--<a href="#">-->
                        <!--<i class="icon-user"></i>-->
                        <!--Profile-->
                        <!--</a>-->
                        <!--</li>-->

                        <!--<li class="divider"></li>-->

                        <li>
                            <a href="javascript:" class="quit_btn">
                                <i class="icon-off"></i>
                                退出
                            </a>
                        </li>
                    </ul>
                </li>
            </ul><!-- /.ace-nav -->
        </div><!-- /.navbar-header -->
    </div><!-- /.container -->
</div>
<div class="main-container" id="main-container">


    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>


        <div class="sidebar" id="sidebar">


            <div class="sidebar-shortcuts" id="sidebar-shortcuts" style="display:none;">
                <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                    <button class="btn btn-success">
                        <i class="icon-signal"></i>
                    </button>

                    <button class="btn btn-info">
                        <i class="icon-pencil"></i>
                    </button>

                    <button class="btn btn-warning">
                        <i class="icon-group"></i>
                    </button>

                    <button class="btn btn-danger">
                        <i class="icon-cogs"></i>
                    </button>
                </div>

                <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                    <span class="btn btn-success"></span>

                    <span class="btn btn-info"></span>

                    <span class="btn btn-warning"></span>

                    <span class="btn btn-danger"></span>
                </div>
            </div><!-- #sidebar-shortcuts -->
            <?php echo W('Menu/show_menu'); ?><!--菜单-->

            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="icon-double-angle-left" data-icon1="icon-double-angle-left"
                   data-icon2="icon-double-angle-right"></i>
            </div>


        </div>

        <div class="main-content">
            
<div class="page-content admin_index">
    <div class="row">
        <div class="col-xs-12 ad-index">
            <h4>
                <i class="ace-icon fa fa-bell"></i>
                欢迎使用
                <strong class="green">
                    一元购后台管理系统
                    <small>(<?php echo __version__; ?>)</small>
                </strong>
            </h4>

            <!-- PAGE CONTENT BEGINS -->
<!--            <div class="alert alert-block alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="icon-remove"></i>
                </button>

                <i class="icon-ok green"></i>

            </div>-->
            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
    <div class="row ad-qbox">
        <div class="col-sm-4">
            <div class="widget-box index-wb-fl">
                <div class="widget-header widget-header-flat widget-header-small">
                    <h5 class="widget-title">
                        用户信息
                    </h5>
                </div>
                <div class="widget-body ad-line1">
                    <div class="widget-main">
                        <ul class="ad-u-info">
                            <li><label>管理账户：</label><span><?php echo (isset($admin_info['username']) && ($admin_info['username'] !== '')?$admin_info['username']:'--'); ?></span></li>
                            <li><label>管理员角色：</label><span><?php echo (isset($admin_info['role_name']) && ($admin_info['role_name'] !== '')?$admin_info['role_name']:'--'); ?></span><li>
                            <li><label>上次登录时间：</label><span><?php echo (isset($admin_info['last_login_time']) && ($admin_info['last_login_time'] !== '')?$admin_info['last_login_time']:'--'); ?></span><li>
                            <li><label>上次登录IP：</label><span><?php echo (isset($admin_info['last_login_ip']) && ($admin_info['last_login_ip'] !== '')?$admin_info['last_login_ip']:'--'); ?><br></span><li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="widget-box ">
                <div class="widget-header widget-header-flat widget-header-small">
                    <h5 class="widget-title">
                        网站信息
                    </h5>
                </div>
                <div class="widget-body ad-line2">
                    <div class="widget-main ad-site-info">
                        <ul>
                            <li>网站名称：<?php echo C('WEBSITE_NAME'); ?></li>
                            <li>网站关键字：<?php echo C('WEBSITE_KEYWORD'); ?></li>
                            <li>网站域名：<?php echo C('WEBSITE_URL'); ?></li>
                            <li>微信端域名：<?php echo C('WAP_WEBSITE_URL'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
    
        </div>
        <div class="col-sm-4">
            <!--<div class="widget-box index-wb-fl">-->
                <!--<div class="widget-header widget-header-flat widget-header-small">-->
                    <!--<h5 class="widget-title">-->
                        <!--搜索来源-->
                    <!--</h5>-->
                <!--</div>-->
                <!--<div class="widget-body ad-line1">-->
                    <!--<div class="widget-main">-->

                    <!--</div>-->
                <!--</div>-->
            <!--</div>-->
            <div class="widget-box index-wb-fl sys-info">
                <div class="widget-header widget-header-flat widget-header-small">
                    <h5 class="widget-title">
                        系统信息
                    </h5>
                </div>
                <div class="widget-body ad-line2">
                    <div class="widget-main ">

                        <ul>
                            <li>操作系统 :  <?php echo $system_info['os']; ?></li>
                            <li>服务器版本 :  <?php echo $system_info['php_uname']; ?></li>
                            <li>PHP版本 :  <?php echo $system_info['php_version']; ?></li>
                            <li>MYSQL版本 :  <?php echo $system_info['mysql_version']; ?></li>
                            <li>上传限制 :  <?php echo $system_info['upload_limit']; ?></li>
                            <li>GD库 :  <?php if(empty($system_info['support_gd'])): ?>
                                不支持
                                <?php else: ?>
                                支持
                                <?php endif; ?>
                            </li>
                            <li>POST限制 : <?php echo $system_info['post_limit']; ?></li>
                            <li>脚本超出时间 :  <?php echo $system_info['script_out_time']; ?>秒</li>

                            <li>fsockopen :  
                                <?php if(empty($system_info['support_fsockopen'])): ?>
                                不支持
                                <?php else: ?>
                                支持
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="widget-box index-wb-fl">
                <div class="widget-header widget-header-flat widget-header-small">
                    <h5 class="widget-title">
                        快捷菜单
                    </h5>
                </div>
                <div class="widget-body ad-line1">
                    <div class="widget-main">
                        <div class="ad-qumenu">
                            <a class="aqbtn-btn1" href="<?php echo U('goods/exec',array('type'=>'add')); ?>"><span>添加商品</span></a>
                            <a class="aqbtn-btn2" href="<?php echo U('conf/index'); ?>"><span>系统设置</span></a>
                            <a class="aqbtn-btn3" href="<?php echo U('order/index'); ?>"><span>订单列表</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="widget-box index-wb-fl sys-info">
                <div class="widget-header widget-header-flat widget-header-small">
                    <h5 class="widget-title">
                        数据统计
                    </h5>
                </div>
                <div class="widget-body ad-line2">
                    <div class="widget-main">
                        <ul>
                            <li>今日新增用户订单数 :  <?php echo $data_statistics['today_true_order_count']; ?></li>
                            <li>今日新增机器人订单数 :  <?php echo $data_statistics['rt_order_add_count']; ?></li>
                            <li>今日新增用户数量 :  <?php echo $data_statistics['today_user_add_count']; ?></li>
                            <li>今日新增商品数量 :  <?php echo $data_statistics['today_goods_add_count']; ?></li>
                            <li>今日开奖数量 :  <?php echo $data_statistics['today_nper_open_count']; ?></li>
                            <li>商品总数量 :  <?php echo $data_statistics['goods_count']; ?></li>
                            <li>会员人数 :  <?php echo $data_statistics['users_count']; ?></li>
                            <li>今日账户收入 :  <?php echo $data_statistics['today_order_money']; ?>元</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--
    <div class="row ad-qbox">
        <div class="col-sm-4">

            </div>
        <div class="col-sm-4 ">

        </div>
        <div class="col-sm-4">

        </div>
    </div>
-->

    <!-- 以上 -->

    <div style="display:none;" class="row">
        <div class="col-xs-12">
            <div class="space-6"></div>
            <div class="col-sm-7 infobox-container">
                <div class="infobox infobox-green  ">
                    <div class="infobox-icon">
                        <i class="icon-comments"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">32</span>
                        <div class="infobox-content">2个评论</div>
                    </div>
                    <div class="stat stat-success">8%</div>
                </div>

                <div class="infobox infobox-blue  ">
                    <div class="infobox-icon">
                        <i class="icon-twitter"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">11</span>
                        <div class="infobox-content">新粉丝</div>
                    </div>

                    <div class="badge badge-success">
                        +32%
                        <i class="icon-arrow-up"></i>
                    </div>
                </div>

                <div class="infobox infobox-pink  ">
                    <div class="infobox-icon">
                        <i class="icon-shopping-cart"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">188</span>
                        <div class="infobox-content">新订单</div>
                    </div>
                    <div class="stat stat-important">4%</div>
                </div>

                <div class="infobox infobox-red  ">
                    <div class="infobox-icon">
                        <i class="icon-beaker"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">25期</span>
                        <div class="infobox-content">今日开奖</div>
                    </div>
                </div>

                <div class="infobox infobox-orange2  ">
                    <div class="infobox-chart">
                        <span class="sparkline" data-values="196,128,202,177,154,94,100,170,224"></span>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">6,251</span>
                        <div class="infobox-content">页面查看次数</div>
                    </div>

                    <div class="badge badge-success">
                        7.2%
                        <i class="icon-arrow-up"></i>
                    </div>
                </div>

                <div class="infobox infobox-blue2  ">
                    <div class="infobox-progress">
                        <div class="easy-pie-chart percentage" data-percent="42" data-size="46">
                            <span class="percent">42</span>%
                        </div>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-text">交易盈利</span>

                        <div class="infobox-content">
                            18000

                        </div>
                    </div>
                </div>

                <div class="space-6"></div>

                <div class="infobox infobox-green infobox-small infobox-dark">
                    <div class="infobox-progress">
                        <div class="easy-pie-chart percentage" data-percent="61" data-size="39">
                            <span class="percent">61</span>%
                        </div>
                    </div>

                    <div class="infobox-data">
                        <div class="infobox-content">任务</div>
                        <div class="infobox-content">完成</div>
                    </div>
                </div>

                <div class="infobox infobox-blue infobox-small infobox-dark">
                    <div class="infobox-chart">
                        <span class="sparkline" data-values="3,4,2,3,4,4,2,2"></span>
                    </div>

                    <div class="infobox-data">
                        <div class="infobox-content">今日流水</div>
                        <div class="infobox-content">58000</div>
                    </div>
                </div>

                <div class="infobox infobox-grey infobox-small infobox-dark">
                    <div class="infobox-icon">
                        <i class="icon-download-alt"></i>
                    </div>

                    <div class="infobox-data">
                        <div class="infobox-content">APP下载</div>
                        <div class="infobox-content">1,205</div>
                    </div>
                </div>
            </div>

            <div class="vspace-sm"></div>
            <div class="col-sm-5">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat widget-header-small">
                        <h5>
                            <i class="icon-signal"></i>
                            访问来源
                        </h5>

                        <div class="widget-toolbar no-border">
                            <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown">
                                本周
                                <i class="icon-angle-down icon-on-right bigger-110"></i>
                            </button>

                            <ul class="dropdown-menu pull-right dropdown-125 dropdown-lighter dropdown-caret">
                                <li class="active">
                                    <a href="#" class="blue">
                                        <i class="icon-caret-right bigger-110">&nbsp;</i>
                                        本周
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="icon-caret-right bigger-110 invisible">&nbsp;</i>
                                        上周
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="icon-caret-right bigger-110 invisible">&nbsp;</i>
                                        本月
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="icon-caret-right bigger-110 invisible">&nbsp;</i>
                                        上月
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="widget-body">
                        <div class="widget-main">
                            <div id="piechart-placeholder"></div>

                            <div class="hr hr8 hr-double"></div>

                            <div class="clearfix">
                                <div class="grid3">
															<span class="grey">
																<i style="font-size: 30px;" class="fa fa-paw icon-2x blue"></i>
																&nbsp; 百度搜索
															</span>
                                    <h4 class="bigger pull-right">1,255</h4>
                                </div>

                                <div class="grid3">
															<span class="grey">
																<i style="font-size: 30px;" class="fa fa-google-plus-square icon-2x purple"></i>
																&nbsp; 谷歌搜索
															</span>
                                    <h4 class="bigger pull-right">941</h4>
                                </div>

                                <div class="grid3">
															<span class="grey">
																<i style="font-size: 30px;" class="fa fa-search-plus icon-2x red"></i>
																&nbsp; 360搜索
															</span>
                                    <h4 class="bigger pull-right">1,050</h4>
                                </div>
                            </div>
                        </div><!-- /widget-main -->
                    </div><!-- /widget-body -->
                </div><!-- /widget-box -->
            </div><!-- /span -->
        </div>
    </div><!-- /row -->


    <div style="display:none;" class="row">
        <div class="col-sm-12">
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="lighter">
                        <i class="icon-star orange"></i>
                        最新订单
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <table class="table table-bordered table-striped">
                            <thead class="thin-border-bottom">
                            <tr>
                                <th>
                                    <i class="icon-caret-right blue"></i>
                                    名称
                                </th>

                                <th>
                                    <i class="icon-caret-right blue"></i>
                                    付款金额
                                </th>

                                <th class="hidden-480">
                                    <i class="icon-caret-right blue"></i>
                                    状态
                                </th>
                            </tr>
                            </thead>

                            <tbody>

                            <?php if(is_array($new_order)): $i = 0; $__LIST__ = $new_order;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>

                            <tr>
                                <td> <?php echo $vo['name']; ?></td>

                                <td>
                                    <b class="green"><?php echo $vo['price']; ?></b>
                                </td>

                                <td class="hidden-480">

                                    <?php switch($vo['pay_status']): case "1": ?>
                                        <span class="label label-info arrowed-right arrowed-in">已创建</span>
                                    <?php break; case "2": ?>
                                        <span class="label label-info arrowed-right arrowed-in">已付款</span>
                                    <?php break; case "3": ?>
                                        <span class="label label-info arrowed-right arrowed-in">已付款</span>
                                    <?php break; default: ?>
                                        <span class="label label-info arrowed-right arrowed-in">已付款</span>
                                    <?php endswitch; ?>



                                </td>
                            </tr>

                            <?php endforeach; endif; else: echo "" ;endif; ?>

                            </tbody>
                        </table>
                    </div><!-- /widget-main -->
                </div><!-- /widget-body -->
            </div><!-- /widget-box -->
        </div>

    </div>
</div><!-- /.page-content -->


        </div><!-- /.main-content -->
        <div class="ace-settings-container" id="ace-settings-container" style="display:none;">
            <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                <i class="icon-cog bigger-150"></i>
            </div>

            <div class="ace-settings-box" id="ace-settings-box">
                <div>
                    <div class="pull-left">
                        <select id="skin-colorpicker" class="hide">
                            <option data-skin="default" value="#438EB9">#438EB9</option>
                            <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                            <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                            <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                        </select>
                    </div>
                    <span>&nbsp; 选择皮肤</span>
                </div>

                <div>
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar"/>
                    <label class="lbl" for="ace-settings-navbar"> 固定标题</label>
                </div>

                <div>
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar"/>
                    <label class="lbl" for="ace-settings-sidebar">固定侧边</label>
                </div>

                <div>
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs"/>
                    <label class="lbl" for="ace-settings-breadcrumbs"> 固定面包屑</label>
                </div>

                <div>
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl"/>
                    <label class="lbl" for="ace-settings-rtl">菜单转移</label>
                </div>

                <div>
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container"/>
                    <label class="lbl" for="ace-settings-add-container">
                        缩小
                        <b>.放大</b>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="icon-double-angle-up icon-only bigger-110"></i>
    </a>

</div>
<div class="m-footer">
    <div class="m-copyright">
        <div class="g-wrap">
            <div class="m-copyright-logo">

            </div>

            <div class="m-copyright-txt">
                <p> <?php echo C('WEB_INC_INFO'); ?> | <?php echo C('WEBSITE_BEIAN'); ?></p>
            </div>
        </div>
    </div>
</div>
<!--[if lt IE 9]>
<script type="text/javascript" src="__ADMIN_ASSETS__/js/html5shiv.js"></script>
<script type="text/javascript" src="__ADMIN_ASSETS__/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="__common__/js/jquery.min.js"></script>
<script type="text/javascript" src="__plugin__/layer/layer.js"></script>
<script type="text/javascript" src="__plugin__/laydate/laydate.js"></script>
<script type="text/javascript" src="__common__/js/common.js"></script>
<script type="text/javascript" src="__common__/js/table_ajax_load.js"></script>
<script type="text/javascript" src="__common__/admin/js/public.js"></script>



<script type="text/javascript" src="__ADMIN_ASSETS__/js/bootstrap.min.js"></script>
<script type="text/javascript" src="__ADMIN_ASSETS__/js/typeahead-bs2.min.js"></script>
<script type="text/javascript" src="__ADMIN_ASSETS__/js/ace-elements.min.js"></script>
<script type="text/javascript" src="__ADMIN_ASSETS__/js/ace.min.js"></script>
<script type="text/javascript" src="__ADMIN_ASSETS__/js/ace-extra.min.js"></script>
<script type="text/javascript" src="__ADMIN_ASSETS__/js/jquery.mobile.custom.min.js"></script>


<script type="text/javascript">
    try {
        ace.settings.check('sidebar', 'collapsed')

        ace.settings.check('main-container', 'fixed')

        ace.settings.check('sidebar', 'fixed')
    } catch (e) {
    }
</script>
<div style="display:none">
    <!--全局url-->
<input type="hidden" id="user_quit" value="<?php echo U('Account/quit'); ?>">
<input type="hidden" id="login_url" value="<?php echo U('Account/index'); ?>">
<input type="hidden" id="change_pass_url" value="<?php echo U('User/change_pass_do'); ?>">
<input type="hidden" id="upload_img_url_system" value='<?php echo U("core/api/up_img"); ?>'/><!--上传图片调用-->
<input type="hidden" id="pre_page_url" name="<?php if((!empty($_SERVER['HTTP_REFERER']))): echo $_SERVER['HTTP_REFERER']; endif; ?>">
</div>
</body>
</html>
