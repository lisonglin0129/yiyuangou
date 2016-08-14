<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:65:"D:\webroot\mengdie_yyg\app\mobile\view\users\personal_center.html";i:1468303710;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
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
    
<script src="__MOBILE_JS__/jquery.min.js"></script>
<link href="__MOBILE_CSS__/user.css" rel="stylesheet"/>
<script src="__MOBILE_JS__/js.js"></script>
<style type="text/css">

</style>

</head>
<body>


<div class="m-user mm mm1s" id="dvWrapper">
	<div class="m-simpleHeader" id="dvHeader">
		<a href="<?php echo U('Index/index'); ?>" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico ico-back"></i></a>
		<h1>个人中心</h1>
	</div>
	<div class="m-user-index">
		<div class="m-user-summary m-user-summary-simple is m-user-is">
			<div class="info">
				<div class="m-user-avatar">
					<img width="50" height="50"  src="<?php echo (isset($personal_center['user_face']) && ($personal_center['user_face'] !== '')?$personal_center['user_face']:''); ?>"/>
				</div>
				<div class="txt w-txts">
					<div class="name namess">
                        <?php echo $personal_center['nick_name']; ?>
					</div>

					<div class="id idd w-ids">
						<section class="w-spans">余额：<?php echo $personal_center['money']; echo C('MONEY_NAME'); ?></section><span class="cz czs"><a href="<?php echo U('Order/recharge'); ?>"  style="color: #D83652">充值</a></span>

						<!--<section class="w-spans">积分：20000000积分</section><span class="cz">转为香肠币</span>

						
						
						<span class="w-spans">积分：<?php echo $personal_center['money']; echo C('MONEY_NAME'); ?></span>-->
					</div>
				</div>
			</div>
			<a href="<?php echo U('Users/personal_data'); ?>"><span style="float:right;margin-right:15px;color:#fff;margin-top:40px;"><i class="ico ico-next"></i></span></a>
		</div>
	</div>
</div>
<!--<a href="wdhb.html">-->
<!--<div class="gr-1 w-nav-item">-->
	<!--<section>我的红包<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>-->
<!--</div>-->
<!--</a>-->
<a href="<?php echo U('Buy/person_indiana'); ?>">
<div class="gr-1 w-nav-item">
	<section>夺宝记录<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
</div>
</a>
<a href="<?php echo U('Buy/personal_win_record'); ?>">
<div class="gr-1 w-nav-item">
	<section>幸运记录<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
</div>
</a>
<!--<a href="xyd.html">-->
<!--<div class="gr-1 w-nav-item">-->
	<!--<section>心愿单<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>-->
<!--</div>-->
<!--</a>-->
<a href="<?php echo U('Buy/my_share_list'); ?>">
<div class="gr-1 w-nav-item">
	<section>我的晒单<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
</div>
</a>
<!--<a href="wdbs.html">-->
<!--<div class="gr-1 w-nav-item">-->
	<!--<section>我的宝石<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>-->
<!--</div>-->
<!--</a>-->
<a href="<?php echo U('Order/recharge_record'); ?>">
<div class="gr-1 w-nav-item">
	<section>充值记录<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
</div>
</a>
<a href="<?php echo U('Article/home_page'); ?>">
<div class="gr-1 gr-3 w-nav-item" style="border-bottom:none">
	<section>夺宝客服<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
</div>
</a>
<?php if((empty($register) == false AND $register['status'] == '1') OR (empty($level) == false AND $level['status'] == '1')): ?>
<a href="<?php echo U('Spread/index'); ?>">
    <div class="gr-1 gr-3 w-nav-item">
        <section>我的推广<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
    </div>
</a>
<?php endif; if(empty($register) == false AND $register['status'] == '1'): ?>
<!-- <a href="<?php echo U('Spread/index'); ?>">
    <div class="gr-1 gr-3 w-nav-item">
        <section>注册推广<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
    </div>
</a> -->
<?php endif; if((empty($register) == false AND $register['status'] == '1') OR (empty($level) == false AND $level['status'] == '1')): ?>
<!-- <a href="<?php echo U('extract/index'); ?>">
    <div class="gr-1 gr-3 w-nav-item">
        <section>我要提现<span style="float:right;margin-right:15px;color:#a9a9a9;"><i class="ico ico-next"></i></span></section>
    </div>
</a> -->
<?php endif; ?>
<a href="<?php echo U('Users/logout'); ?>">
<div class="gr-1 gr-2 w-nav-item">
	<section>退出登录</section>
</div>
</a>
<script>
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