<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:62:"D:\webroot\mengdie_yyg\app\mobile\view\goods\goods_detail.html";i:1468303711;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;}*/ ?>
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
    
<link href="__MOBILE_CSS__/detail.css"  rel="stylesheet" />
<script src="__MOBILE_JS__/jquery.min.js"></script>
<script src="__MOBILE_JS__/js.js"></script>
<link href="__MOBILE_CSS__/jiaodian.css"  rel="stylesheet"/>
<script src="__MOBILE_JS__/jquery.event.drag-1.5.min.js"></script>
<script src="__MOBILE_JS__/jquery.touchSlider.js"></script>
<link href="__MOBILE_CSS__/user.css" rel="stylesheet"/>
<script src="__MOBILE_JS__/index.js"></script>


<style>
    b{
        color: #ffffff;
    }
</style>








<script>
    $(function () {
        $(".main_visual").hover(function(){
            $("#btn_prev,#btn_next").fadeIn()
        },function(){
            $("#btn_prev,#btn_next").fadeOut()
        })
        $dragBln = false;
        $(".main_image").touchSlider({
            flexible : true,
            speed : 200,
            btn_prev : $("#btn_prev"),
            btn_next : $("#btn_next"),
            paging : $(".flicking_con a"),
            counter : function (e) {
                $(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
            }
        });
        $(".main_image").bind("mousedown", function() {
            $dragBln = false;
        })
        $(".main_image").bind("dragstart", function() {
            $dragBln = true;
        })
        $(".main_image a").click(function() {
            if($dragBln) {
                return false;
            }
        })
        timer = setInterval(function() { $("#btn_next").click();}, 5000);
        $(".main_visual").hover(function() {
            clearInterval(timer);
        }, function() {
            timer = setInterval(function() { $("#btn_next").click();}, 5000);
        })
        $(".main_image").bind("touchstart", function() {
            clearInterval(timer);
        }).bind("touchend", function() {
            timer = setInterval(function() { $("#btn_next").click();}, 5000);
        })



        //加入清单点击
        $('#add-to-cart').click(function(ev) {

            ev.preventDefault();

            //判断期数与个数是否正确
            var nper_id = $(this).attr('nper-id');
            var car_list = $("#cart-list").val();


            //Ajax提交
            $.ajax({
                url : $('#add-to-cart').attr('add-cart-url'),
                type : 'POST',
                data : {
                    nper_id : nper_id
                },
                beforeSend : function () {

                },
                success : function (data, response, status) {
                    var response_data = $.parseJSON(data);

                        if (response_data.status == 'fail') {
                            layer.open({
                                content: response_data.message,
                                time: 1 //1秒后自动关闭
                            });
                            return;
                        }
                        layer.open({
                            content: '添加成功',
                            time: 1
                        });
//                        if(response_data.status == 'success') {
//                            alert('登陆成功');
//                            window.location.href = $('#personal-url').val()
//                        }
                }
            });

        });


        //立即参与点击
        $('#immediate-participate').click(function(ev) {

            ev.preventDefault();

            //判断是否登录，没有登录跳转到登录页面，否则添加购物车并跳转到购物车列表页面

            var  is_login = $('#is-login').val();
            var nper_id = $(this).attr('nper-id');


            if(is_login == 1) {

                //Ajax提交
                $.ajax({
                    url : "<?php echo U('Cart/ajax_add_cart'); ?>",
                    type : 'POST',
                    data : {
                        nper_id : nper_id
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
                            return;
                        }



                        //成功,跳转到购物车列表页面
                        window.location.href = $('#cart-list').val()

                    }
                });

            }else{
                window.location.href = $('#is-login').attr('login-url');
            }




        });


        //查看我的购买号码
        $('#my-code').click(function(){
            $('.my-code').show();
        });


        $('.my-code-confirm').click(function(){
            $('.my-code').hide();
        });



        //查看获奖者的号码
        $('#win-code').click(function(ev){
            ev.preventDefault();
            $('.win-code').show();
        });

        $('.win-code-confirm').click(function(){
            $('.win-code').hide();
        });


        //得到总页码
        $.ajax({
            url : "<?php echo U('Goods/all_join_count'); ?>",
            type : 'POST',
            data : {
                nper_id : $('#nper-id').val()
            },
            success: function(data, response, status){
                window.count = parseInt(data);
            }
        });


        //滚动条拖动
        window.scrollFlag = true;
        window.first = 10;
        window.page = 1;
        $(window).scroll(function () {
            if (window.page < window.count) {
                if (window.scrollFlag) {
                    if ($(document).scrollTop() >= ($('#load-more').offset().top + $('#load-more').outerHeight() - $(window).height() - 20)) {
                        setTimeout(function(){
                            $.ajax({
                                url: "<?php echo U('Goods/ajax_goods_join'); ?>",
                                type: 'POST',
                                data: {
                                    offset: window.first,
                                    nper_id : $('#nper-id').val()
                                },
                                success: function(data, response, status){
                                    $('#load-more').before(data);
                                }
                            });
                            window.scrollFlag = true;
                            window.first += 10;
                            window.page += 1;
                        }, 10);
                        window.scrollFlag = false;
                    }
                }
            } else {
                $('#load-more').html('<section style="width:100%;height30px;text-align:center;margin-top:10px;">没有更多数据</section>');
            }
        });

    });
</script>

</head>
<body>


<div class="g-body">
    <div class="m-detail">
	<div class="m-simpleHeader" id="dvHeader">
	    <a href="javascript:history.go(-1);" data-pro="back" data-back="true" class="m-simpleHeader-back"><i class="ico-back"></i></a>
	    <h1>商品详情</h1>
	</div>
    <div class="m-detail-menu">

    </div>
    <div class="g-wrap">
            <div class="g-wrap-hd">



                <div class="w-slide m-detail-show">


                    <div class="w-slide-wrap" style="width:80vw;height:80vw;margin-bottom: 5px;margin-top: 18px;">
                        <ul class="w-slide-wrap-list" data-pro="list" style="width:100%;">

                            <div class="kePublic" >
                                <!--效果html开始-->
                                <div class="main_visual" style="width:100%;" >
                                    <div class="shiyuan-1"></div>
                                        <div class="flicking_con" >
                                            <a href="#">1</a>
                                            <a href="#">2</a>
                                            <a href="#">3</a>
                                            <a href="#">4</a>
                                            <a href="#">5</a>
                                        </div>
                                        <?php if($goods_detail['code'] == 'shiyuanduobao'): ?>
                                            <i class="ico ico-label ico-label-ten ico100 ico101"></i>
                                         <?php endif; ?>
                                        <div class="main_image main_image1s" style="width:100%;">
                                            <ul>

                                     <?php if(is_array($goods_detail['img_path'])): $i = 0; $__LIST__ = $goods_detail['img_path'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?>
                                                    <li><span class="img_3" style='height:80vw;width:80vw;background: url("<?php echo $data; ?>");
                                                    background-size: 100% 100%;
                                                    ;'></span></li>
                                     <?php endforeach; endif; else: echo "" ;endif; ?>

                                            </ul>
                                            <a href="javascript:;" id="btn_prev"></a>
                                            <a href="javascript:;" id="btn_next"></a>
                                        </div>
                                    </div>
                                    <!--效果html结束-->
                                    <div class="clear"></div>
                                </div>

                            </div>

                        </ul>
                    </div>
                </div>
                <?php switch($goods_detail['status']): case "1": ?>

                        <!--status = 1-->
                        <div class="w-goods w-goods-xxl m-detail-goods">
                            <div class="w-goods-info">
                                <p class="w-goods-title"><?php echo $goods_detail['name']; ?></p>
                                <p class="w-goods-period" style="margin-left: 15px;">期号：<?php echo num_base_mask($goods_detail['nper_id'],0); ?></p>
                                <div class="w-progressBar">
                                    <p class="wrap">
                                        <span class="bar" style="width:<?php echo $goods_detail['progress']; ?>%;"><i class="color"></i></span>
                                    </p>
                                    <ul class="txt good_text1s">
                                        <li class="txt-l" style="margin-left: 15px;margin-top: -3px;height: 23px;"><p>总需<?php echo $goods_detail['sum_times']; ?>人次</p></li>
                                        <li class="txt-r" style="margin-right: 15px;margin-top: -3px;"><p>剩余<b class="txt-blue"><?php echo $goods_detail['sum_times'] - $goods_detail['participant_num']; ?></b></p></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php break; case "2": ?>
                        <!--status = 2-->

                        <div class="w-goods w-goods-xxl m-detail-goods">
                            <div class="w-goods-info">
                                <p class="w-goods-title"><?php echo $goods_detail['name']; ?></p>
                                 <p class="w-goods-period"style="margin-left: 15px;">期号：<?php echo num_base_mask($goods_detail['nper_id'],0); ?></p>
                                <div class="w-progressBar list-sq1 list-sq11">
                                    <!--响应式样式在common.css文件中-->
                                    <!-- <p class="wrap">
                                        <span class="bar" style="width:50.6%;"><i class="color"></i></span>
                                    </p> -->
                                    <!-- <ul class="txt">
                                        <li class="txt-l"><p>总需85 人次</p></li>
                                        <li class="txt-r"><p>剩余<b class="txt-blue">42</b></p></li>
                                    </ul> -->

                                    <div class="list-sq1-left"><span style="float: left;">揭晓倒计时</span><span class="w-countdown" time="<?php echo $goods_detail['countdown']; ?>" style="font-size:16px;"></span></div>

                                    <a href="<?php echo U('Goods/calculation_Details',array('nper_id'=>$goods_detail['nper_id'])); ?>"><div class="list-sq1-right">查看计算详情</div></a>
                                </div>
                            </div>
                        </div>

                    <?php break; case "3": ?>


                        <!--中奖者购买号码   弹出框+遮罩层开始-->
                        <div class="win-code mm" style="display:none">
                            <div class="dbjl-trop01"></div><!--遮罩层-->
                            <div class="dbjl-trop02">
                                <div class="dbjl-trop02-1">
                                    <div class="dbjl-trop02-left"></div><!--点击消失--><div class="clr"></div>
                                    <div class="dbjl-trop02-01">
                                        <section>夺宝号码</section>
                                        <section>参与<span style="color:red;"><?php echo $goods_detail['win_code_list_num']; ?></span>人次，夺宝号码：</section>
                                        <section class="seed">
                                            <?php if(is_array($goods_detail['win_code_list'])): $i = 0; $__LIST__ = $goods_detail['win_code_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?>
                                            <span style="color:#666"><?php echo $data; ?></span>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>

                                        </section>
                                    </div>
                                    <section class="dbjl-trop02-02 win-code-confirm">确定</section>
                                </div>
                            </div>
                        </div>
                        <!--弹出框+遮罩层结束-->

                        <!--status = 3-->
                        <div class="w-goods w-goods-xxl m-detail-goods">
                            <div class="w-goods-info">
                                <p class="w-goods-title"><?php echo $goods_detail['name']; ?></p>
                                <p class="w-goods-period">期号：<?php echo num_base_mask($goods_detail['nper_id'],0); ?></p>
                                <div class="w-progressBar list-sq1">
                                    <!--响应式样式在common.css文件中-->
                                    <!-- <p class="wrap">
                                        <span class="bar" style="width:50.6%;"><i class="color"></i></span>
                                    </p> -->
                                    <!-- <ul class="txt">
                                        <li class="txt-l"><p>总需85 人次</p></li>
                                        <li class="txt-r"><p>剩余<b class="txt-blue">42</b></p></li>
                                    </ul> -->

                                    <!-- 	<div class="list-sq1-left"><span>揭晓倒计时</span><span>00:00:00</span></div> -->

                                    <!-- <div class="list-sq1-right">查看计算详情</div> -->
                                    <div class="list-sq2">
                                        <div class="list-sq2-1"></div>
                                        <div class="list-sq2-2"><img src="<?php echo $goods_detail['user_face']; ?>"/></div>
                                        <div class="list-sq2-right">
                                            <section><span>获奖者：<?php echo $goods_detail['nick_name']; ?></span>&nbsp;<section>
                                                <section><span>用户ID：<?php echo $goods_detail['luck_uid']; ?>(唯一不变的标识)</span><section>
                                                    <section><span>本期参与人数：<?php echo $goods_detail['luck_join_num']; ?>人次</span>&nbsp;&nbsp;&nbsp;&nbsp;<a id="win-code" href="" style="color: blue">查看Ta的号码</a></section>
                                                    <section><span>揭晓时间：<?php echo $goods_detail['announce_time']; ?></span></section>

                                        </div>
                                        <div class="list-sq2-3">
                                            <span style="margin-left:18px;display:block;float:left;">幸运号码</span>
                                            <span style="font-size:20px;margin-left:10px;display:block;float:left;"><?php echo (isset($goods_detail['luck_num']) && ($goods_detail['luck_num'] !== '')?$goods_detail['luck_num']:''); ?></span>
                                            <a href="<?php echo U('Goods/calculation_details',array('nper_id'=>$goods_detail['nper_id'])); ?>"><div class="list-xxhm">查看计算详情</div></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php break; endswitch; ?>
                <div class="m-detail-userCodes m-detail-userCodes1" style="padding: 0px;">

                    <?php if(($is_login == 1)): if(empty($goods_detail['code_list'])): else: ?>



                            您参与了 <?php echo $goods_detail['code_list_num']; ?> 人次

                                <?php if($goods_detail['code_list_num'] <= 6): if(is_array($goods_detail['code_list'])): $i = 0; $__LIST__ = $goods_detail['code_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;echo $data; ?> &nbsp;
                                    <?php endforeach; endif; else: echo "" ;endif; else: if(is_array($goods_detail['code_list_less'])): $i = 0; $__LIST__ = $goods_detail['code_list_less'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;echo $data; ?> &nbsp;
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                    <button id="my-code">点击查看全部号码</button>

                                    <!--我的购买号码  弹出框+遮罩层开始-->
                                    <div class="my-code mm" style="display:none">
                                        <div class="dbjl-trop01"></div><!--遮罩层-->
                                        <div class="dbjl-trop02">
                                            <div class="dbjl-trop02-1">
                                                <div class="dbjl-trop02-left"></div><!--点击消失--><div class="clr"></div>
                                                <div class="dbjl-trop02-01">
                                                    <section>夺宝号码</section>
                                                    <section>参与<span style="color:red;"><?php echo $goods_detail['code_list_num']; ?></span>人次，夺宝号码：</section>
                                                    <section class="seed">
                                                        <?php if(is_array($goods_detail['code_list'])): $i = 0; $__LIST__ = $goods_detail['code_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?>
                                                        <span style="color:#666"><?php echo $data; ?></span>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>

                                                    </section>
                                                </div>
                                                <section class="dbjl-trop02-02 my-code-confirm">确定</section>
                                            </div>
                                        </div>
                                    </div>
                                    <!--弹出框+遮罩层结束-->

                                <?php endif; endif; ?>

                        <div class="inventorys">
                            <?php if($new_id !== false): ?>
                            <a href="<?php echo U('mobile/goods/goods_detail',array('nper_id'=>$new_id)); ?>" class="invent_a">前往下一期</a>
                            <?php else: ?>
                            <a href="" id="immediate-participate"  nper-id="<?php echo $goods_detail['nper_id']; ?>" class="inventorys-l invenas">1元夺宝</a>
                            <a id="add-to-cart"  class="inventorys-l inventorys-r" add-cart-url = "<?php echo U('Cart/ajax_add_cart'); ?>" nper-id = "<?php echo $goods_detail['nper_id']; ?>" href="">加入清单</a>
                            <?php endif; ?>
                        </div>

                    <?php else: ?>
                    <!--<div class="inventorys">-->
                        <!--<a href="" id="immediate-participate"  nper-id="<?php echo $goods_detail['nper_id']; ?>" class="inventorys-l invenas">1元夺宝</a>-->
                        <!--<a id="add-to-cart"  class="inventorys-l inventorys-r" add-cart-url = "<?php echo U('Cart/ajax_add_cart'); ?>" nper-id = "<?php echo $goods_detail['nper_id']; ?>" href="">加入清单</a>-->
                    <!--</div>-->
                        <p class="m-detail-userCodes-blank"><a href="<?php echo U('OtherUsers/login'); ?>"><b style="color:#db3652;">请登录</b></a>，查看你的夺宝号码！</p>
                    <?php endif; ?>
                </div>
                
            </div>

            <div class="g-wrap-bd">
                <div class="m-detail-more">
                    <a href="<?php echo U('Goods/graphic_details',array('goods_id'=>$goods_detail['goods_id'])); ?>"  class="w-bar">图文详情 <span class="w-bar-hint">( 建议在wifi下查看 )</span><span class="w-bar-ext"><b class="ico-next"></b></span></a>
                    <?php if($goods_detail['status'] != '1'): ?>
                      <!-- <div class="inventorys">
                            <div  class="inventorys-l">新的一期正在火热进行 </div>
                            <a id="add-to-cart"  class="inventorys-l inventorys-r" href="">立即前往</a>
                        </div> -->
                    <?php endif; ?>
                    <a href="<?php echo U('Goods/before_announce',array('goods_id'=>$goods_detail['goods_id'])); ?>"  class="w-bar">往期揭晓<span class="w-bar-ext"><b class="ico-next"></b></span></a>
                    <a href="<?php echo U('Goods/goods_share_order',array('goods_id'=>$goods_detail['goods_id'])); ?>" class="w-bar">晒单分享<span class="w-bar-ext"><b class="ico-next"></b></span></a>
                </div>

               


                <div class="m-detail-record">
                    <div class="w-bar">所有参与记录 <span class="w-bar-hint">( 自<?php echo $goods_detail['nper_create_time']; ?>开始 )</span></div>

                        <?php if(empty($goods_detail['participant_record'])): ?>
                            <p style="font-size: 13px;color:#ccc;width:100%;text-align:center;margin: 10px 0px 10px 0px;">暂时没有参与记录</p>
                        <?php else: if(is_array($goods_detail['participant_record'])): $i = 0; $__LIST__ = $goods_detail['participant_record'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?>
                            <div class="m-detail-record-wrap">
                                <div class="tq">
                                    <div class="tq-1"><img src="<?php echo $data['user_face']; ?>"/></div>
                                    <div class="add"><a href="<?php echo U('OtherUsers/other_person_center',array('uid'=>$data['uid'])); ?>"><span style="color:#0079FE;"><?php echo $data['nick_name']; ?></span></a><span>(<?php echo $data['ip_area']; ?>IP：<?php echo $data['reg_ip']; ?>)</span>
                                        <section>参与了<span  style="color:red;"><?php echo $data['count']; ?></span>人次<span> <?php echo $data['pay_time']; ?></span></section>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <p id="load-more"></p>
                        <?php endif; ?>
                </div>
            </div>
    </div>


    <?php if($goods_detail['status'] == '1'): ?>

		<div class="m-simpleFooter m-detail-buy">
			<div data-pro="text" class="m-simpleFooter-text" style="text-align:center">
				<a id="immediate-participate" class="w-button w-button-main" nper-id = "<?php echo $goods_detail['nper_id']; ?>" href="">立即参与</a>
				<!--<a id="add-to-cart" class="w-button" add-cart-url = "<?php echo U('Cart/ajaxAddCart'); ?>" nper-id = "<?php echo $goods_detail['nper_id']; ?>" href="">加入清单</a>-->
			</div>
			<div data-pro="ext" class="m-simpleFooter-ext"></div>
        </div>
    <?php endif; ?>


    </div>
</div>
<input type="hidden" id="is-login" value="<?php echo $is_login; ?>" login-url = "<?php echo U('OtherUsers/login'); ?>"/>

<input type="hidden" id="cart-list" value="<?php echo U('Cart/cart_list'); ?>"/>
<input type="hidden" id="nper-id" value="<?php echo $goods_detail['nper_id']; ?>"/>





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