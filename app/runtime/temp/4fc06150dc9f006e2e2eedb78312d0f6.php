<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:62:"D:\webroot\mengdie_yyg\app\mobile\view\order\submit_order.html";i:1470794110;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
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
<script src="__MOBILE_JS__/js.js"></script>
<script>

    $(function(){

        //提交按钮按一次之后灰化不准再按
        $("form").submit(function(){
            $("#brt20").attr("disabled","disabled")
            $("#brt20").css("background-color","rgb(204,202,203)");
        });

        //格式


        $('#balance-button').click(function () {

            var pay_type = parseInt($("input[name='pay_type']").val());

            if(pay_type == 3) {

                var total_price = parseFloat($('#total-price').text());
                var use_balance = parseFloat($('#use-balance').text());


                if($("input[name='use_balance']").val() == '1') {
                    $("input[name='use_balance']").val('0');
                    $('#total-price').text((total_price+use_balance).toFixed(2));
                    $('.balance').hide();
                    //$(this).removeClass('w-checkbox');
                }else{
                    $("input[name='use_balance']").val('1');
                    $('#total-price').text((total_price-use_balance).toFixed(2));
                    $('.balance').show();
                    //$(this).addClass('w-checkbox');
                }

            }

        });

        $(".ddzf1-yue").click(function(){
            $(".txt-red1,.w-checkbox").toggle();
        });

        var time=new Date();
        time.setMinutes(30);
        time.setSeconds(0);
        var timeout;
        var timeshow=document.getElementById("timeshow");
        function countdown(){
            var hour=time.getHours();
            var min=time.getMinutes();
            var second=time.getSeconds();
            if(hour=="0"&&min=="0"&&second=="0"){
                layer.open({
                    content: "Time Out!",
                    time: 1 //1秒后自动关闭
                });
                clearInterval(timeout);
            }
            time.setSeconds(second-1);
            hour<10?hour="0"+hour:hour;
            min<10?min="0"+min:min;
            second<10?second="0"+second:second;
            timeshow.innerHTML=+min+"分"+second+"秒"+"完成支付";
        }
        timeout= setInterval(countdown,1000);


        //选取支付方式

        $("input[name='other_pay_type']").click(function(){
            if($(this).val() == 'weixin'){
                $('form').attr('action',"<?php echo U('Weixin/pay'); ?>");
            }else if($(this).val() == 'alipay'){
                $('form').attr('action',"<?php echo U('Alipay/alipay_api'); ?>");
            }else if($(this).val() == 'wx')
            {
            	 $('form').attr('action',"<?php echo U('Wxpay/pay'); ?>");
            }else{
                $('form').attr('action',"<?php echo U('Aipay/index'); ?>");
            }
        });

        var select = $("input[name='other_pay_type']").eq(0);
        select.attr('checked','checked');
        $('form').attr('action',select.attr('data-value'));







    })

</script>


</head>
<body>



<?php if(empty($order_info)): ?>
订单为空
<?php else: if($order_info['pay_type'] == 1): ?>

    <form action="<?php echo U('Order/personal_pay'); ?>" method="post" target="_self">

<?php else: ?>
    <form action="<?php echo U('Aipay/index'); ?>" method="post" target="_self">

<?php endif; ?>

    <input type="hidden" name="order_num" value="<?php echo $order_info['order_num']; ?>"/>

    <input type="hidden" name="pay_type" value="<?php echo $order_info['pay_type']; ?>"/>

    <?php if($order_info['pay_type'] == 3): ?>

        <input type="hidden" name="use_balance" value="1"/>

    <?php endif; ?>

    <div class="m-user" id="dvWrapper">
        <div class="m-simpleHeader" id="dvHeader">
            <a href="javascript:history.go(-1);" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico ico-back"></i></a>
            <h1>支付订单</h1>
        </div>

        <div class="ddzf1-top">
            <div class="header" id="idOrderHeader">
                <p>订单已经提交,请在</p>
                <div id="timeshow" style="margin:0 auto;"></div>

            </div>
            <div class="detail">
                商品合计：<em class="txt-red"><?php echo $order_info['total_price']; ?><span id="currency"><?php echo C('MONEY_NAME'); ?></span></em>
            </div>
        </div>


        <?php if($order_info['pay_type'] != 2): ?>

            <div class="w-checkBar w-bar w-checkBar-checked" id="pro-view-33">
                    <span id="pro-view-63" style="font-size: 10px;"><span>余额支付（余额：<?php echo $order_info['user_money']; echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?>）</span>

                        <?php if($order_info['pay_type'] == 3): ?>

                            <span class="w-bar-extText txt-red balance"><span id="use-balance"><?php echo $order_info['user_money']; ?></span><?php echo C('MONEY_NAME'); ?></span>

                        <?php endif; if($order_info['pay_type'] == 1): ?>

                            <span class="w-bar-extText txt-red balance"><span id="use-balance"><?php echo $order_info['total_price']; ?></span><?php echo C('MONEY_NAME'); ?></span>

                        <?php endif; ?>

                    </span>
                <div class="w-bar-ext">
                    <b data-pro="switcher" id="balance-button" class="w-checkbox"></b><input type="checkbox"  value="0">
                </div>


            </div>

        <?php endif; if($order_info['pay_type'] != 1): ?>
            <div class="w-checkBar w-bar w-checkBar-checked" id="pro-view-3">
                    <span id="pro-view-6">其他支付方式
                        <span class="w-bar-extText txt-red"><span id="total-price"><?php echo $order_info['remain_price']; ?></span><?php echo C('MONEY_NAME'); ?></span>
                    </span>
                <div class="w-bar-ext"><b data-pro="switcher" class=""></b>
                    <input type="checkbox"/>
                </div>
            </div>




            <div style="width: 90%;height: 10px;">
                <?php if($pay_type['ALI_PAY'] == 1): ?>
                <input type="radio" name="other_pay_type" data-value="<?php echo U('Alipay/alipay_api'); ?>" value="alipay"/> 支付宝
                <?php endif; if($pay_type['W_PAY'] == 1): ?>
              	  <input type="radio" name="other_pay_type" data-value="<?php echo U('Weixin/pay'); ?>" value="weixin"/>  微信支付
                <?php endif; if($pay_type['WX_THREE_PAY'] == 1): ?>
              	  <input type="radio" name="other_pay_type" data-value="<?php echo U('Wxpay/pay'); ?>" value="wx"/>  微信支付
                <?php endif; if($pay_type['AIBEI_PAY'] == 1): ?>
                  <input type="radio" name="other_pay_type" data-value="<?php echo U('Aipay/index'); ?>" checked="checked" value="aipay">爱贝支付
                <?php endif; ?>
            </div>
        <?php endif; ?>






        <div class="m-cashier-button ">
            <button  type="submit" id="brt20"> 确认支付</button>
        </div>

    </div>




</form>

<?php endif; ?>



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