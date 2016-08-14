<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:63:"D:\webroot\mengdie_yyg\app\mobile\view\order\confirm_order.html";i:1468303711;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
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
    
<link href="__MOBILE_CSS__/user.css"  rel="stylesheet" />
<script src="__MOBILE_JS__/jquery.min.js"></script>
<script src="__MOBILE_JS__/index.js"></script>
<script src="http://apps.bdimg.com/libs/layer/2.1/layer.js"></script>


</head>
<body>



<!---html--->

<?php if(empty($cart_info)): ?>

    购物车均已过期，请重新购买

<?php else: ?>

    <body class="isss">
    <div class="m-user" id="dvWrapper">
        <div class="m-simpleHeader" id="dvHeader">
            <a href="javascript:history.go(-1);" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico ico-back"></i></a>
            <h1>支付订单</h1>
        </div>
        <form action="<?php echo U('Order/submit_order'); ?>" method="post" onsubmit="return checksubmit()" name="submit_order">

            <?php if(is_array($cart_info)): $i = 0; $__LIST__ = $cart_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?>
            <div class="zf-1">
                <span class="buy-p"><?php echo $data['name']; ?></span>
                <section style="float:right"><span style="color:red;" class="buy_num"><?php echo $data['num']; ?>人次</section>
                <input type="hidden" name="cart_id[]" value="<?php echo $data['cart_id']; ?>"/>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>

            <div class="zf-1">
                <section style="float:right">商品合计<span style="color:red;"><?php echo $total_price; echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?></span></section>
            </div>
            <button type="submit" id="brt20">提交</button>
        </form>
    </div>



</body>

<?php endif; ?>
<script language="javascript">
    //--->
    function checksubmit()
    {
        $(".buy_num").each(function(){
            if($(this).text()>500){
                layer.open({
                    content: "购买数量不能大于500",
                    time: 1 //1秒后自动关闭
                });
                return false;
            }
        });
    }

    //提交按钮按一次之后灰化不准再按
    $("form").submit(function(){
        $("#brt20").attr("disabled","disabled")
        $("#brt20").css("background-color","rgb(204,202,203)");
    });
    //--->
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