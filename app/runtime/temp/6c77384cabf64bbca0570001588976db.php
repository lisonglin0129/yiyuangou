<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:58:"D:\webroot\mengdie_yyg\app\mobile\view\order\recharge.html";i:1470794988;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
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
    
<link href="__MOBILE_CSS__/user.css" rel="stylesheet"/>
<script src="__MOBILE_JS__/jquery.min.js"></script>
<script src="http://apps.bdimg.com/libs/layer/2.1/layer.js"></script>


<script>
    $(function(){


        //源自文件cz.html
        $(".cz2-1").click(function() {
            var index = $(this).index()-1;
            $(".cz2-1").eq(index).css("border", "2px solid red").siblings(".cz2-1").css("border", "2px solid #F7F7F7");

            //赋值金额

            $('#price').val($(this).find('button').text());
        });

        $('.price-text').blur(function () {
            var price = parseFloat($(this).val()).toFixed(2);
            if(isNaN(price)) {
                $('#price').val('');
            }else{
                $(this).val(price);
                $('#price').val(price);
            };
        });

		$('#mainform').attr('action',"<?php echo U('Aipay/recharge'); ?>");
		$('.pay_type').change(function(){
			if($(this).val()=='wxpay'){
				$('#mainform').attr('action',"<?php echo U('weixin/recharge'); ?>");
			}else if($(this).val()=='alipay'){
				$('#mainform').attr('action',"<?php echo U('Alipay/personal_charge'); ?>");
			}else if($(this).val()=='wx'){
				$('#mainform').attr('action',"<?php echo U('Wxpay/rechargePay'); ?>");
			}else{
				$('#mainform').attr('action',"<?php echo U('Aipay/recharge'); ?>");
			}
		});

		var select = $("input[name='pay_type']").eq(0);
		select.attr('checked','checked');
		$('#mainform').attr('action',select.attr('data-value'));

		$("form").submit(function(){
			if($('.price-text').val()<0.00){
				alert("充值金额错误");return false;
			}
		});


		//layer.alert('1元=1<?php echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?>，可用来参与夺宝，充值金额无法退回。');

    })
</script>




</head>
<body>





<div class="m-user mm czz" id="dvWrapper">
	<div class="m-simpleHeader" id="dvHeader">
		<a href="<?php echo U('Users/personal_center'); ?>" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico ico-back"></i></a>
		<h1>充值</h1>
	</div>
</div>
<div class="cz-1">
	<span class="sp9">选择充值金额（元）</span>
</div>
<div class="cz-2">
	<form action="<?php echo U('Alipay/personal_charge'); ?>" method="post" target="_self" id="mainform">
        <input type="hidden" value="20" name="money" id="price"/>
		<div class="cz2-1 fix_price" style="border:2px solid red">
			<button type="button" class="cz2-11 att2">20</button>
		</div>
		<div class="cz2-1 fix_price">
			<button type="button" class="cz2-11">50</button>
		</div>
		<div class="cz2-1 fix_price">
			<button type="button" class="cz2-11">100</button>
		</div>
		<div class="cz2-1 fix_price">
			<button type="button" class="cz2-11">200</button>
		</div>
		<div class="cz2-1 ">
			<input type="number" class="cz2-12 price-text" value="" placeholder="其他金额"/>
		</div>
	<div class="aa">
		<div class="clr"></div>
		<div class="zffs1 zffs1s" style="border-top: none;">
			<span class="sp9">请选择支付方式</span>
		</div>
		<!--  -->
		<?php if($pay_type['ALI_PAY'] == 1): ?>
        	<label class="rad adda ">
        	<input type="radio" name="pay_type" data-value="<?php echo U('Alipay/personal_charge'); ?>" class="rad1 pay_type" value="alipay"/>支付宝</label>
		<?php endif; if($pay_type['WX_THREE_PAY'] == '1'): ?>
       	    <label class="rad adda ">
       	    <input type="radio" name="pay_type" data-value="<?php echo U('Wxpay/rechargePay'); ?>" class="rad1 pay_type" value="wx"/>微信</label>
        <?php endif; if($pay_type['W_PAY'] == 1): ?>
       		<label class="rad adda ">
      		<input type="radio" name="pay_type" data-value="<?php echo U('weixin/recharge'); ?>" class="rad1 pay_type" value="wxpay"/>微信</label>
		<?php endif; if($pay_type['AIBEI_PAY'] == 1): ?>
		<label class="rad adda "><input type="radio" name="pay_type"  data-value="<?php echo U('Aipay/recharge'); ?>" class="rad1 pay_type"  value="aipay"/>爱贝支付</label>
		<?php endif; ?>
		<div class="clear"></div>
		
			
		
	</div>
	<section style="font-size: 8px;margin: 0 auto;color: red;text-align: center;margin-top: 30px;">	
		温馨提示：充值可获得<?php echo C('MONEY_NAME'); ?>（1元=1<?php echo C('MONEY_NAME'); ?>），可用于夺宝，已充值的款项无法退回。
	</section>
	<div class="ah">
	</div>
	<div class="aff affss">
		<input type="submit" value="提交" class="sub1"/>
	</div>
    </form>

</div>


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