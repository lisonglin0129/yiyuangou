<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:56:"D:\webroot\mengdie_yyg\app\admin\view\account\index.html";i:1468303701;s:57:"D:\webroot\mengdie_yyg\app\admin\view\base/common_js.html";i:1468303702;}*/ ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="renderer" content="webkit">
    <link href="__common__/admin//css/account/common.css" rel="stylesheet">
    <link href="__common__/admin/css/account/login_u.css" rel="stylesheet">
    <title>首页</title>
</head>
<body>
<div class="header">
    <div class="nav">
        <div class="head-cap">
            <a class="logo" href="<?php echo U('/'); ?>"  style="background-image:url(<?php echo $logo; ?>);background-repeat:no-repeat; ">
            </a>
            <span>
                <b>互联网创新模式与技术服务解决方案提供商</b><br>
                TCECHNICAL SERVICE SOLUTIONS PROVIDER
            </span>
        </div>
    </div>
</div>
<div class="body">
    <div class="login-bgcon">
        <div class="bgcon"><span class="login-bot"></span></div>
        <div class="center-cell">

            <div class="login-con">
                <div class="lgn-link">
                </div>
                <form id="form_content" autocomplete="off">
                    <div class="logo-form">
                        <h2>后台登录</h2>
                        <div class="lgn-f-l">
                            <span class="icon username"></span>
                            <input placeholder="请输入用户名" name="username" value="">
                        </div>
                        <div class="lgn-f-l">
                            <span class="icon password"></span>
                            <input placeholder="请输入密码" type="password" name="password">
                        </div>
                        <div class="lgn-f-l v-code">
                            <input class="v-code" placeholder="验证码" name="verify">
                            <img class="val-img verify_img" src="<?php echo U('account/verify'); ?>" onclick="this.src='<?php echo U('account/verify'); ?>?&amp;time='+Math.random();" style="cursor: pointer;" title="点击获取">
                        </div>
                        <input class="lgn-submit submit_btn" type="button" value="登录">
                    </div>
                </form>
            </div>
        </div></div>

</div>
<div class="foot">
    <div class="ft-con">
        <p>
            企业QQ：<?php echo (isset($qq) && ($qq !== '')?$qq:''); ?>&nbsp;&nbsp;&nbsp;&nbsp;企业邮箱：<?php echo (isset($mail) && ($mail !== '')?$mail:'--'); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo (isset($beian) && ($beian !== '')?$beian:''); ?><br>
            400电话：<?php echo (isset($phone) && ($phone !== '')?$phone:'--'); ?>&nbsp;&nbsp;&nbsp;&nbsp;（售前咨询：8:00～24:00）
        </p>
    </div>
</div>
<script type="text/javascript" src="__common__/js/jquery.min.js"></script>
<script type="text/javascript" src="__plugin__/layer/layer.js"></script>
<script type="text/javascript" src="__plugin__/laydate/laydate.js"></script>
<script type="text/javascript" src="__common__/js/common.js"></script>
<script type="text/javascript" src="__common__/js/table_ajax_load.js"></script>
<script type="text/javascript" src="__common__/admin/js/public.js"></script>




<script type="text/javascript" src="__common__/admin/js/account/index.js"></script>
<script>
    var wh = $(window).height();
    if (wh >= 500) {
        $(".body").height(wh - $(".header").height() - $(".foot").height());
    }

</script>
<input type="hidden" id="root_url" value="<?php echo U('index/index'); ?>">
<input type="hidden" id="submit_url" value="<?php echo U('login_do'); ?>">
</body>
</html>