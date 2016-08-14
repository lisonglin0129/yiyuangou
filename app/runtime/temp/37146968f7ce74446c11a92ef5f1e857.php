<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:54:"D:\webroot\mengdie_yyg\app\admin\view\order\index.html";i:1468303702;s:52:"D:\webroot\mengdie_yyg\app\admin\view\base/base.html";i:1468303702;s:58:"D:\webroot\mengdie_yyg\app\admin\view\base/common_css.html";i:1468303702;s:54:"D:\webroot\mengdie_yyg\app\admin\view\base/navbar.html";i:1468303702;s:57:"D:\webroot\mengdie_yyg\app\admin\view\base/common_js.html";i:1468303702;s:56:"D:\webroot\mengdie_yyg\app\admin\view\base/base_url.html";i:1468303702;}*/ ?>
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
            
<div class="breadcrumbs" id="breadcrumbs">
    <script type="text/javascript">
        try {
            ace.settings.check('breadcrumbs', 'fixed')
        } catch (e) {
        }
    </script>

    <ul class="breadcrumb">
        <li>
            <i class="icon-home home-icon"></i>
            <a href="javascript:">后台管理</a>
        </li>

        <li class="active">订单列表</li>
    </ul><!-- .breadcrumb -->


</div>

<div class="page-content">
    <div class="page-header">
        <form id="form_condition" class="form-search form-inline">
            <div class="row">
                <div class="col-xs-10">
                    <div class="form-group" style="float: left;" >
                        <label class="sr-only" ></label>
                        <select class="form-control fc-select" id="form-field-select-1" name="field" style="min-width: 120px">
                            <option value="1">订单号</option>
                            <option value="2">用户id</option>
                            <option value="3">来源</option>
                        </select>
                        </div>
                    <div class="col-xs-2">
                        <select class="form-control" id="form-field-select-2" name="field2">
                            <option value="">全部用户</option>
                            <option value="1">普通用户</option>
                            <option value="-1">机器人</option>
                            <option value="2">商户</option>
                        </select>
                    </div>
                        <div class="form-group" style="float: left;width:50%;">
                            <label class="sr-only" ></label>
                            <input class="form-control search-query " style="min-width: 200px;float: left;box-sizing: border-box;" placeholder="请输入" type="text" name="keywords">
                                <label class="sr-only" ></label>
                            <span class="input-group-btn">
							<button type="button" class="btn btn-purple btn-sm search_btn">
                                搜索
                                <i class="icon-search icon-on-right bigger-110"></i>
                            </button>
							<!--<a href="<?php echo U('exec',array('type'=>'add')); ?>" target="_blank" class="btn btn-success btn-sm">-->
                                <!--<i class="icon-plus icon-on-right bigger-110"></i>-->
                                <!--新增-->
                            <!--</a>-->
						    </span>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-responsive">
                    <div id="form_content" data-url="<?php echo U('show_list'); ?>"></div>
                    </div><!-- /.table-responsive -->
                </div><!-- /span -->
            </div><!-- /row -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.page-content -->

<input type="hidden" id="del_url" value="<?php echo U('del'); ?>">



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

<script type="text/javascript" src="__common__/admin/js/order/index.js"></script>


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
