<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:55:"D:\webroot\mengdie_yyg\app\mobile\view\users\login.html";i:1468303710;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo (isset($wap_config_info['title']) && ($wap_config_info['title'] !== '')?$wap_config_info['title']:'--'); ?></title>
    <meta name="description" content="1元夺宝，就是指只需1元就有机会获得一件商品，是基于<?php echo C('WEBSITE_NAME'); ?>邮箱平台孵化的新项目，好玩有趣，不容错过。" />
    <meta name="keywords" content="1元,一元,1元夺宝,1元购,1元购物,1元云购,<?php echo C('WEBSITE_NAME'); ?>,一元购,一元购物,一元云购,夺宝奇兵" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width">
    <link href="__MOBILE_CSS__/common.css"  rel="stylesheet" />
    
<script type="text/javascript" src="__MOBILE_JS__/jquery.min.js"></script>
<script type="text/javascript" src="__MOBILE_JS__/common.js"></script>
<link href="__MOBILE_CSS__/login.css" rel="stylesheet" />


</head>
<body>



<div class="m-simpleHeader" id="dvHeader">
		<a href="javascript:history.go(-1)" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico ico-back"></i></a>
		<h1>用户登录</h1>
	</div>
	<div class="m-login">
		<div class="m-login-tips" id="tips"></div>
		<div class="m-login-form w-form">
			<div class="w-form-item m-login-form-account w-inputBar w-bar" id="dvAccount"><div class="w-bar-label">帐号：</div><a data-pro="clear" href="javascript:void(0);" class="w-bar-input-clear">×</a><div class="w-bar-control"><input placeholder="请输入帐号" autocapitalize="off" data-pro="input" class="w-bar-input" type="text" name="" value=""></div></div>
			<div class="w-form-item m-login-form-password w-inputBar w-bar" id="dvPassword"><div class="w-bar-label">密码：</div><a data-pro="clear" href="javascript:void(0);" class="w-bar-input-clear">×</a><div class="w-bar-control"><input placeholder="请输入密码" autocapitalize="off" data-pro="input" class="w-bar-input" type="password" name="" value=""></div></div>
			<div class="m-login-menu" id="autoCmpl" style="display:none;"></div>
		</div>
		<div class="m-login-tips-bottom" id="tipsBottom"></div>
		<div class="m-login-submit"><button class="w-button w-button-main" data-pro="submit" id="btnLogin">登 录</button></div>
		<div class="m-login-link">
			<a href="<?php echo U('OtherUsers/register'); ?>">进行注册</a>
			<a href="<?php echo U('OtherUsers/forget_password'); ?>" id="aForget" class="aside" target="_blank">忘记密码？</a>
		</div>
		<?php if(C('UNION_QQ_SWITH') == 1 || C('UNION_SINA_WEIBO_SWITH') == 1 || (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')!==false && C('UNION_WEICHAT_SWITH') == 1)): ?>
		<div class="m-login-extLogin">
			<div class="hd"><span>第三方登录</span></div>
			<div class="bd">
                <?php 
                if(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')!==false && C('UNION_WEICHAT_SWITH') == 1){
                 ?>
					<div class="m-login-extLogin-item">
						<a class="ico ico-thirdLogin ico-thirdLogin-wechat" href="<?php echo U('Login/login',array('type'=>'wechat')); ?>"></a>
						<p>微信</p>
					</div>
                <?php 
                }
                 if(C('UNION_QQ_SWITH') == 1): ?>
					<div class="m-login-extLogin-item">
						<a class="ico ico-thirdLogin ico-thirdLogin-qq" href="<?php echo U('Login/login',array('type'=>'qq')); ?>"></a>
						<p>QQ</p>
					</div>
				<?php endif; if(C('UNION_SINA_WEIBO_SWITH') == 1): ?>
					<div class="m-login-extLogin-item">
						<a class="ico ico-thirdLogin ico-thirdLogin-weibo" href="<?php echo U('Login/login',array('type'=>'weibo')); ?>"></a>
						<p>新浪微博</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<form action="" method="post" id="loginForm" style="display:none;">
		<input id="loginForm_username" type="hidden" name="username" value="">
		<input id="loginForm_password" type="hidden" name="password" value="">
		<input id="loginForm_product" type="hidden" name="product" value="mail163">
		<input id="loginForm_url" type="hidden" name="<?php echo U(); ?>" value="">
		<input id="loginForm_url2" type="hidden" name="url2" value="">
		<input id="loginForm_savelogin" type="hidden" name="savelogin" value="">
	</form>
<input type="hidden" value="<?php echo U('OtherUsers/login'); ?>" id="login-url"/>
<input type="hidden" value="<?php if(!isset($url) && empty($url)): echo U('Users/personal_center'); else: echo (isset($url) && ($url !== '')?$url:''); endif; ?>" id="personal-url"/>

<script>

    /**
     * TODO 弹出框美化
     * 点击登录按钮
     */
    $('#btnLogin').click(function () {
        if($('#dvAccount input').val() == '') {
			layer.open({
				content: '账号不得为空',
				time: 1 //1秒后自动关闭
			});
            return;
        }
        if($('#dvPassword input').val() == '') {
			layer.open({
				content: '密码不得为空',
				time: 1 //1秒后自动关闭
			});
            return;
        }

        $.ajax({
            url : $('#login-url').val(),
            type : 'POST',
            data : {
                phone : $('#dvAccount input').val(),
                password : $('#dvPassword input').val()
            },
            beforeSend : function () {

            },
            success : function (data, response, status) {


                var response_data = $.parseJSON(data);



                if (response_data.status == 'fail') {
					layer.open({
						content: response_data.message,
						time: 1
					});
                    return;
                }
                if(response_data.status == 'success') {
                    layer.open({
                        content: '登录成功',
                        time: 1
                    });
                    window.location.href = $('#personal-url').val()
                }
            }
        });


    });







</script>




<div class="menu1"></div>
<?php if(!isset($_GET['a'])): ?>
<div class="menu2">
    <ul>
        <a href="<?php echo U('Index/index'); ?>" class="img166 img171 <?php if(!empty($index)): ?>act<?php endif; ?>">夺宝</a>
        <a href="<?php echo U('Index/all_goods', array('cate'=> '0-1')); ?>" class="img166 img172 <?php if(!empty($all_goods_cate)): ?>act<?php endif; ?>">全部商品</a>
        <a href="<?php echo U('Index/all_share_order'); ?>" class="img166 img173 <?php if(!empty($shareOrder)): ?>act<?php endif; ?>">晒单</a>
        <a href="<?php echo U('Cart/cart_list'); ?>" class="img166 img171 img174 <?php if(!empty($cart_select)): ?>act<?php endif; ?>">购物车</a>
        <a href="<?php echo U('Users/personal_center'); ?>" class="img166 img175 <?php if(!empty($personal_center)): ?>act<?php endif; ?>">我的</a>
    </ul>
</div>
<?php endif; ?>

<script type="text/javascript" src="__common__/plugin/layer_mobile/layer.js"></script>

<!--<script type="text/javascript" src="__common__/plugin/layer/layer.js"></script>-->

<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1259010002'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1259010002%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
</body>
</html>