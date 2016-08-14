<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:58:"D:\webroot\mengdie_yyg\app\mobile\view\cart\cart_list.html";i:1468303710;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
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


<style type="text/css">
    .menu1{
    height: 130px;
}
</style>

</head>
<body>



        <div class="m-user" id="dvWrapper">
            <div class="m-simpleHeader" id="dvHeader">
                <a href="javascript:history.go(-1);" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico ico-back"></i></a>
                <h1>清单</h1>
            </div>

        <?php if(empty($cart_list)): ?>

            <p style="width: 100%;text-align: center;margin-top: 50px;">购物车列表为空</p>

        <?php else: ?>


            <form action="<?php echo U('Order/confirm_order'); ?>" method="post">

                <?php if(is_array($cart_list)): $i = 0; $__LIST__ = $cart_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <div class="qd-1">
                        <?php if($vo['deposer_type'] == 2): ?>
                            <img class="ico ico-label ico-label-goods ico_ten" src="/static/img/icon_ten.png">
                        <?php endif; ?>
                        <div class="img1123"><img src="<?php echo $vo['img_path']; ?>" alt="" style="width: 100%;height: 100%;"/></div>
                        <div class="img1124">
                            <a href=""><?php if(!empty($vo['name']) && mb_strlen($vo['name']) > 18): echo mb_substr($vo['name'],0,18,'utf-8'); ?>...<?php else: echo (isset($vo['name']) && ($vo['name'] !== '')?$vo['name']:'--'); endif; ?></a></br>
                            总需 <?php echo $vo['sum_times']; ?> 人次，剩余 <span style="color:blue;"><?php echo $vo['remain_num']; ?></span>人次</br>
                            参与人次
                            <div class="w-number " id="pro-view-7">
                                <input name="" class="w-number-input cart-num-text" modify-cart-url = "<?php echo U('Cart/modify_cart_num'); ?>" nper-id = "<?php echo $vo['nper_id']; ?>" cart-id = "<?php echo $vo['cart_id']; ?>" remain-num = "<?php echo $vo['remain_num']; ?>"  min-times = "<?php echo $vo['min_times']; ?>" max-times = "<?php echo $vo['max_times']; ?>" unit-price = "<?php echo $vo['unit_price']; ?>" data-pro="input" value="<?php echo $vo['num']; ?>" pattern="[0-9]*"/>
                                <input type="hidden" name="cart_id[]" value="<?php echo $vo['cart_id']; ?>"/>
                                <em class="w-number-btn w-number-btn-plus add cart-add"   href="">+</em>
                                <a class="w-number-btn w-number-btn-minus jian1 cart-reduce"  href="">-</a>
                            </div>
                            <a href="javascript:void(0);" delete-cart-url = "<?php echo U('Cart/delete_cart'); ?>" class="del shanchu delete-cart"><i class="ico ico-del"></i></a>
                        </div>
                    </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>


                <div class="footer-0">共参与<span id="cart-num"><?php echo $cart_num; ?></span> 件商品，总计：
                    <em class="txt-red " id="total"><?php echo $cart_price; echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?></em>
                    <div data-pro="ext" class="m-simpleFooter-ext butt">
                        <button type="submit" class="w-button w-button-main" id="pro-view-2">提交</button>
                    </div>
                </div>
            </form>

        <?php endif; ?>

        </div>


<script type="text/javascript">


    $(function() {

        //提交按钮按一次之后灰化不准再按
        $("form").submit(function(){
            $("#pro-view-2").attr("disabled","disabled");
            $("#pro-view-2").css("background-color","rgb(204,202,203)");
        });



        /**
         * 计算购物车总数量，总价钱
         */
        function total_price_num() {

            var cart_obj = $('.cart-num-text:visible');

            var total_num = cart_obj.size();

            if(total_num == 0){
                $('.footer-0').hide();
            }

            var total_price = 0;

            cart_obj.each(function(){
                var cart_num = parseInt($(this).val());

                var unit_price = parseInt($(this).attr('unit-price'));

                total_price +=  cart_num*unit_price ;

            });


            $('#cart-num').html(total_num);
            $('#total').html(total_price);

        }



        //购物车+(加)按钮点击
        $('.cart-add').click(function() {
            var text_obj = $(this).parent().find('.cart-num-text');
            var min_times = parseInt(text_obj.attr('min-times'));
            var max_times = parseInt(text_obj.attr('max-times'));
            var current_num = parseInt(text_obj.val());
            var remain_num = parseInt(text_obj.attr('remain-num'));
            var cart_id = parseInt(text_obj.attr('cart-id'));


            //当期数量大于页面剩余数量
            if(current_num >= remain_num || current_num >= max_times) {
                return false;
            }



            var after_num = current_num + min_times;
            $.ajax({
                url : text_obj.attr('modify-cart-url'),
                type : 'POST',
                data : {
                    cart_id : cart_id,
                    num : after_num
                },
                beforeSend : function () {

                },
                success : function (data, response, status) {

                    text_obj.val(after_num);
                    total_price_num();
                }
            });


        });

        //购物车-(减)按钮点击
        $('.cart-reduce').click(function(ev) {
            ev.preventDefault();

            var text_obj = $(this).parent().find('.cart-num-text');
            var min_times = parseInt(text_obj.attr('min-times'));
            var current_num = parseInt(text_obj.val());
            var remain_num = parseInt(text_obj.attr('remain-num'));
            var cart_id = parseInt(text_obj.attr('cart-id'));


            //当期数量小于最小数量
            if(current_num <= min_times) {
                return false;
            }

            var after_num = current_num - min_times;

            $.ajax({
                url : text_obj.attr('modify-cart-url'),
                type : 'POST',
                data : {
                    cart_id : cart_id,
                    num : after_num
                },
                beforeSend : function () {

                },
                success : function (data, response, status) {

                    text_obj.val(after_num);
                    total_price_num();
                }
            });

        });



        //输入框修改数量
        $('.cart-num-text').blur(function() {

            var min_times = parseInt($(this).attr('min-times'));
            var current_num = isNaN(parseInt($(this).val())) ? min_times :  parseInt($(this).val());
            var remain_num = parseInt($(this).attr('remain-num'));
            var cart_id = parseInt($(this).attr('cart-id'));
            var max_times = parseInt($(this).attr('max-times'));


            //当期数量大于页面剩余数量
            if(current_num > remain_num) {
                $(this).val(remain_num);
                current_num = remain_num;

            }

            //十元专区够买修改数量
            if (parseInt(current_num) == 0 || parseInt(current_num) % min_times != 0) {
                $(this).val((parseInt(current_num / min_times) + 1) * min_times)
                current_num = $(this).val();
            }

            //当期数量大于该商品最大购买数
            if(current_num > max_times) {
                $(this).val(max_times);
                current_num = max_times;
            }

            //当期数量小于最小数量
            if(current_num < min_times) {
                $(this).val(min_times);
                current_num = min_times;
            }



            var after_num = current_num;

            $.ajax({
                url : $(this).attr('modify-cart-url'),
                type : 'POST',
                data : {
                    cart_id : cart_id,
                    num : after_num
                },
                beforeSend : function () {

                },
                success : function (data, response, status) {

                    $(this).val(after_num);
                    total_price_num();
                }
            });

        });


        //删除购物车
        $('.delete-cart').click(function(ev) {
            var delete_obj = $(this);

            ev.preventDefault();

            var text_obj = $(this).parent().find('input');

            var cart_id = parseInt(text_obj.attr('cart-id'));

            $.ajax({
                url : $(this).attr('delete-cart-url'),
                type : 'POST',
                data : {
                    cart_id : cart_id
                },
                beforeSend : function () {

                },
                success : function (data, response, status) {
                    var response_data = $.parseJSON(data);
                    if(response_data.status == 'success'){
                        delete_obj.parent().parent().hide();
                    }
                    total_price_num();
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