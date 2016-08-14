<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:58:"D:\webroot\mengdie_yyg\app\mobile\view\users\register.html";i:1468303710;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
注册界面
</title>
    <meta name="description" content="1元夺宝，就是指只需1元就有机会获得一件商品，是基于<?php echo C('WEBSITE_NAME'); ?>邮箱平台孵化的新项目，好玩有趣，不容错过。" />
    <meta name="keywords" content="1元,一元,1元夺宝,1元购,1元购物,1元云购,<?php echo C('WEBSITE_NAME'); ?>,一元购,一元购物,一元云购,夺宝奇兵" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width">
    <link href="__MOBILE_CSS__/common.css"  rel="stylesheet" />
    

<link rel="stylesheet" href="__MOBILE_CSS__/style1.css" media="screen" type="text/css" />
<link href="__MOBILE_CSS__/user.css"  rel="stylesheet" />
<script src="__MOBILE_JS__/jquery.min.js"></script>




</head>
<body>





<div class="m-user mm czz" id="dvWrapper" >
        <div class="m-simpleHeader" id="dvHeader" style="background:linear-gradient(to bottom,#fff,#f3eeee);">
             <a href="javascript:history.go(-1);" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico ico-back"></i></a>
             <a href="<?php echo U('Index/index'); ?>" data-pro="ok" class="m-simpleHeader-ok" id="aHome" style="color:#db3652;">取消</a>
          
            <h1>登录注册</h1>
        </div>
    <div class="clear"></div>
</div>
<form action="" method="">
<div class="hx-1"></div>

<div class="zc1">
	<div class="zc-2" >
		<div class="zc-left">手机号</div>
		<div class="zc-right"><input type="phone"  value="" class="zc-3 zc33 phone" placeholder="请输入手机号"></div>
	</div>
	
	<div class="zc-2">
		<div class="zc-left">验证码</div>
		<div class="zc-right"><input type="css/text" value="" class="zc-3 code" placeholder="请输入验证码"></div>
	</div>
		
	<div class="zc-2 zc-21">
		<button class="zc-left zc-left1 get-code">获取验证码</button>
	</div>
	
	<div class="zc-2">
		<div class="zc-left">密码</div>
		<div class="zc-right"><input type="password" value="" class="zc-3 password" placeholder="请输入密码"></div>
	</div>
	
	<div class="zc-2">
		<div class="zc-left">确认密码</div>
		<div class="zc-right"><input type="password" value="" class="zc-3 re-password" placeholder="请确认密码"></div>
	</div>
	

	<div class="zc-2">

		<div class="zc-right rrt2">
			<input type="checkbox" name="car" class="agree" />
			<a href="<?php echo U('user_agreement'); ?>" style="color:red;"><span style="text-decoration:underline">《我同意<?php echo C('WEBSITE_NAME'); ?>协议》</span></a>
		</div>
	</div>
	
	
	<div class="zc-2">

	
			<button type="button" class="zc-main register">立即注册</button>
		
	</div>

    <!--路径设置-->
    <input type="hidden" value="<?php echo U('OtherUsers/get_code'); ?>" id="get-code-url"/>
    <input type="hidden" value="<?php echo U('OtherUsers/register'); ?>" id="register-url"/>
    <input type="hidden" value="<?php echo U('Index/index'); ?>" id="index-url"/>
    <input type="hidden" value="<?php echo U('mobile/goods/goods_detail'); ?>" id="detail-url">
    <input type="hidden" value="<?php echo (isset($nper_id) && ($nper_id !== '')?$nper_id:''); ?>" id="nper_id">
    <input type="hidden" value="<?php echo (isset($spread_userid) && ($spread_userid !== '')?$spread_userid:''); ?>" id="spread_userid">
    <input type="hidden" value="<?php echo (isset($origin) && ($origin !== '')?$origin:''); ?>" id="origin">
</div>
</form>
<script>
	$(function(){

        //检验手机号码是否为空
        function checkPhoneNotEmpty() {
            return $('.phone').val() == '' ? false : true;
        }

        //检验手机号码是否符合规范
        function checkPhone() {
            return $(".phone").val().match(/^((1)\d{10})$/) ? true : false;

        }

        //检验验证码是否为空
        function checkCodeNotEmpty() {
            return $('.code').val() == '' ? false : true;
        }

        //检测密码是否为空
        function checkPassNotEmpty() {
            return $('.password').val() == '' ? false : true;
        }

        //检测密码与确认密码是否相等
        function checkRepasswordEqual() {
            return $('.password').val() == $('.re-password').val() ? true : false;
        }

        var timer = 60;

        //点击获取验证码之后，倒计时
        function countDown() {
            var code_button = $('.get-code');
            code_button.attr("disabled", true);

            var time = setInterval(function(){
                if(timer == 0) {
                    clearInterval(time);
                    code_button.removeAttr("disabled");
                    code_button.html('获取验证码');
                    return false;
                }
                timer -= 1;
                code_button.html(timer+' 秒');
            },1000);

        }

        //发送验证码按钮变亮或者变暗
		$(".zc33").focus(function(){
			$(".zc-left1").addClass("focus");
		}).blur(function(){
			$(".zc-left1").removeClass("focus");
		});

        //点击获取验证码
        $(".get-code").click(function(ev){
            ev.preventDefault();
            if(!checkPhoneNotEmpty()) {
                layer.open({
                    content: '手机号码不得为空',
                    time: 1
                });
                return false;
            }

            if(!checkPhone()) {
                layer.open({
                    content: '手机号码不符合规范，请重新填写',
                    time: 1
                });
                return false;
            }

            //请求发送验证码
            $.ajax({
                url : $('#get-code-url').val(),
                type : 'POST',
                data : {
                    phone : $('.phone').val(),
                    type : 'reg'
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
                    }else{
                        layer.open({
                            content: '发送成功',
                            time: 1
                        });
                        countDown();
                    }


                }
            });


        });




            //点击注册按钮
        $('.register').click(function() {


            if(!checkPhoneNotEmpty()) {
                layer.open({
                    content: '手机号码不得为空',
                    time: 1
                });
                return false;
            }

            if(!checkPhone()) {
                layer.open({
                    content: '手机号码不符合规范，请重新填写',
                    time: 1
                });
                return false;
            }

            if(!checkCodeNotEmpty()) {
                layer.open({
                    content: '验证码不得为空',
                    time: 1
                });
                return false;
            }

            if(!checkPassNotEmpty()) {
                layer.open({
                    content: '密码不得为空',
                    time: 1
                });
                return false;
            }



            if(!checkRepasswordEqual()) {
                layer.open({
                    content: '确认密码与密码不相等',
                    time: 1
                });
                return false;
            }
            //检查是否同意协议
            if(!$(".agree").is(':checked')) {
                layer.open({
                    content: '请先阅读协议并同意',
                    time: 1
                });
                return false;
            }






            //请求注册
            $.ajax({
                url : $('#register-url').val(),
                type : 'POST',
                data : {
                    phone : $('.phone').val(),
                    password : $('.password').val(),
                    rePassword : $('.re-password').val(),
                    code : $('.code').val(),
                    agree : $('.agree').val(),
                    nper_id:$('#nper_id').val(),
                    spread_userid:$('#spread_userid').val(),
                    origin:$("#origin").val()
                },
                beforeSend : function () {

                },
                success : function (data, response, status) {
                    var response_data = $.parseJSON(data);
                    console.log(response_data);
                    if (response_data.status == 'fail') {
                        layer.open({
                            content: response_data.message,
                            time: 1
                        });
                    }
                    if(response_data.status == 'success') {
                        layer.open({
                            content: '注册成功',
                            time: 1
                        });
                        if($('#nper_id').val()!=''){
                            window.location.href = $('#detail-url').val()+'?nper_id='+$('#nper_id').val();
                        }else{
                            window.location.href = $('#index-url').val()
                        }

                    }
                }
            });

        })




	})
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