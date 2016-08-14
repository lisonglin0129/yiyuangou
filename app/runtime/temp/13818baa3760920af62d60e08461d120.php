<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:55:"D:\webroot\mengdie_yyg\app\mobile\view\index\index.html";i:1468303711;s:55:"D:\webroot\mengdie_yyg\app\mobile\view\base/common.html";i:1468303711;s:57:"D:\webroot\mengdie_yyg\app\mobile\view\public/header.html";i:1468303711;}*/ ?>
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
    <script src="http://apps.bdimg.com/libs/layer/2.1/layer.js"></script>
    <script>
        var img_path = "__MOBILE_IMG__";
    </script>
    <script type="text/javascript" src="__MOBILE_JS__/js.js"></script>
<!--    <script src="__MOBILE_JS__/jquery.event.drag-1.5.min.js"></script>-->
    <script src="__MOBILE_JS__/jquery.touchSlider.js"></script>
    <link href="__MOBILE_CSS__/index.css" rel="stylesheet" />
    <link href="__MOBILE_CSS__/jiaodian.css" type="text/css" rel="stylesheet"/>


    <script>
        $(document).ready(function () {

            $(".main_image").touchSlider({
                flexible : true,
                mouseTouch: true,
                container: this,
                speed : 200,
                counter : function (e) {
                    $(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
                }
            });
            var timer=setInterval(function(){
                $(".main_image")[0].animate(-1, true);
            },5000);

            //加入购物车
            $('.w-button-addToCart').click(function() {

                //判断期数与个数是否正确
                var nper_id = $(this).attr('nper-id');



                //Ajax提交
                $.ajax({
                    url : $('#cart-url').val(),
                    type : 'POST',
                    data : {
                        nper_id : nper_id
                    },
                    beforeSend : function () {

                    },
                    success : function (data, response, status) {
                        var response_data = $.parseJSON(data);

                        if (response_data.exist_flag == false) {
                            $('#count').text(parseInt($('#count').text())+1);
                            $('#count').show();
                        }
//                        if(response_data.status == 'success') {
//                            alert('登陆成功');
//                            window.location.href = $('#personal-url').val()
//                        }
                    }
                });



            });


            //判断是否显示购物车数量
            if($('#count').text() == '0') {
                $('#count').hide();
            }


            //点击关闭中奖提示按钮
            $('.zjts6').click(function(){

                $.ajax({
                    url : "<?php echo U('Goods/confirm_win_notice'); ?>",
                    type : 'POST',
                    data : {
                        win_record_id : $('#win_record_id').val()
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
                            $('.body123').hide();
                            $('.zjts').hide();
                        }
                    }
                });

            });

            $(".footer_dl").click(function(){
                var ua = navigator.userAgent.toLowerCase();
                if(ua.match(/MicroMessenger/i)=="micromessenger") {
                    layer.open({
                        content: '微信浏览器暂不支持下载，请用其他浏览器下载',
                        time: 1 //1秒后自动关闭
                    });
                } else {
                    window.location.href="http://7xtewd.com2.z0.glb.clouddn.com/xiangchang.apk";
                }
            });
            //点击确认收货地址
            $('#confirm_address').click(function(ev){
                ev.preventDefault();
                $('.zjts6').trigger('click');
                window.location.href = $(this).attr('href');
            });



        });
    </script>

</head>
<body>






<div class="g-header">
    <div class="m-header">
        <div class="g-wrap">
            <h1 class="m-header-logo">
                <a class="m-header-logo-link m-header-logo-links" href="<?php echo U('Index/index'); ?>">
                     <img class="ico-logo" src="<?php echo (isset($wap_config_info['logo']) && ($wap_config_info['logo'] !== '')?$wap_config_info['logo']:''); ?>"/>                    
                </a>
            </h1>
            <div class="m-header-toolbar">
                <a class="m-header-toolbar-btn searchBtn" href="<?php echo U('Goods/search'); ?>"  target="_self" title="搜索"><i class="ico ico-search"></i></a>
               <!--  <a class="m-header-toolbar-btn userpageBtn" href="" title="我的夺宝"><i class="ico ico-userpage"></i></a> -->
            </div>
        </div>
    </div>
    <!-- 导航栏 -->
    <div class="m-nav">
        <div class="g-wrap">
            <ul class="m-nav-list">

                <li <?php if(!empty($index)){echo 'class="selected"';} ?>><a href=""><span>首页</span></a></li>
                <li <?php if(!empty($all_goods_cate)){echo 'class="selected"';} ?>><a href=""><span>全部商品</span></a></li>
                <li <?php if(!empty($shareOrder)){echo 'class="selected"';} ?>><a href="" ><span>晒单</span></a></li>
            </ul>
        </div>
    </div>
</div>


<?php if(!empty($win_notice)): ?>
<!--遮罩弹出层-->
<div class="body123"></div>
<div class="zjts">
    <div class="zjts2">
        <div class="zjts6"></div>
        <div class="zjts5">
            <img src="<?php echo $win_notice['img_path']; ?>" class="zjts5-1"/>
            <section style="color:#555;">
                <?php echo mb_substr($win_notice['name'],0,20); ?>
            </section>
            <section>期号：<?php echo num_base_mask($win_notice['nper_id'],0); ?></section>
            <section>中奖号码：<?php echo $win_notice['luck_num']; ?></section>
        </div>
        <a id="confirm_address" href="<?php echo U('Buy/prize_info_confirm',array('win_record_id'=>$win_notice['win_record_id'])); ?>"><div class="zjts3">确认收货地址</div></a>
        <input type="hidden" id="win_record_id" value="<?php echo $win_notice['win_record_id']; ?>"/>
    </div>

</div>

<!--点击显示弹出层--->
<?php endif; ?>






<div class="g-body">
			<div class="m-index" style="padding-top:0px;">
				<!--焦点图-->

				<div class="kePublic">
					<!--效果html开始-->
					<div class="main_visual" style="height:49vw;">
						<div class="flicking_con">

						</div>
						<div class="main_image">
							<ul>
                                <?php if(is_array($home_promo_list)): $i = 0; $__LIST__ = $home_promo_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$each_home_promo): $mod = ($i % 2 );++$i;?>
                                <li class="w-slide-wrap-list-item">
                                    <?php switch($each_home_promo['type']): case "1": ?>
                                    <a href="<?php echo U('Goods/jump_to_goods_buying',array('gid'=>$each_home_promo['content'])); ?>" title="<?php echo htmlspecialchars($each_home_promo['title']); ?>" target="_blank"><img src="__UPLOAD_DOMAIN__<?php echo $each_home_promo['img_path']; ?>" onerror="no_img($(this));" ></a>
                                    <?php break; case "2": ?>
                                    <a href="<?php echo $each_home_promo['content']; ?>" title="<?php echo htmlspecialchars($each_home_promo['title']); ?>" target="_blank">
                                        <img src="__UPLOAD_DOMAIN__<?php echo $each_home_promo['img_path']; ?>"></a>
                                    <?php break; case "3": ?>
                                    <a href="<?php echo dwz_filter('lists/search',['keyword'=>$each_home_promo['content']]); ?>" title="<?php echo htmlspecialchars($each_home_promo['title']); ?>" target="_blank"><img src="__UPLOAD_DOMAIN__<?php echo $each_home_promo['img_path']; ?>"></a>
                                    <?php break; endswitch; ?>
                                </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>

							</ul>
						</div>
					</div>
					<!--效果html结束-->
					<div class="clear"></div>
				</div>

                <div class="w-menus">
                    <a href="<?php echo U('mobile/index/all_goods'); ?>" class="w-menus-htm"><section class="n-menus-sect"></section>商品分类</a>
                    <a href="<?php echo U('mobile/index/all_goods',array('cate'=>'0-2')); ?>" class="w-menus-htm"><section class="n-menus-sect n-menus-sect2"></section>最新商品</a>
                   


                     
                    
                    <a href="<?php echo U('mobile/index/all_share_order'); ?>" class="w-menus-htm"><section class="n-menus-sect n-menus-sect3"></section>晒单分享</a>
                    <a href="<?php echo U('mobile/spread/index'); ?>" class="w-menus-htm"><section class="n-menus-sect n-menus-sect4"></section>邀请好友</a>
                </div>

                <?php if(!empty($latest_announcement)): ?>

                    <!--最新揭晓-->

                    <div class="g-wrap g-body-bd">

                        <div class="m-index-mod m-index-newArrivals">
                            <div class="m-index-mod-hd">
                                <h3>最新揭晓</h3>

                            </div>
                            <div class="m-index-mod-bd">
                                <ul class="w-goodsList w-goodsList-brief m-index-newArrivals-list">

                                    <?php if(is_array($latest_announcement)): $i = 0; $__LIST__ = $latest_announcement;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <li class="w-goodsList-item">
                                        <?php if($vo['code'] == 'shiyuanduobao'): ?>
                                            <img class="ico ico-label ico-label-goods" src="__MOBILE_STATIC__/img/icon_ten.png">
                                        <?php endif; ?>
                                        <div class="w-goods w-goods-brief">
                                            <div class="w-goods-pic w-new-pic">
                                                <a href="<?php if($vo['nper_type'] == 1): echo U('Goods/goods_detail',array('nper_id'=>$vo['nper_id'])); else: echo U('Goods/zero_detail',array('nper_id'=>$vo['nper_id'])); endif; ?>"  >
                                                    <img src="<?php echo $vo['image_src']; ?>"/>
                                                </a>
                                            </div>
                                            <p class="w-goods-title f-txtabb"><div class="w-countdown" time="<?php echo $vo['count_down']; ?>"></div></p>
                                        </div>
                                    </li>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>

                                </ul>
                            </div>
                        </div>


                    <?php endif; ?>
				



				 <!------新品上架------>  
					<div class="m-index-mod m-index-newArrivals">
						<div class="m-index-mod-hd">
							<h3>上架新品</h3>
							<a class="m-index-mod-more" href="<?php echo U('index/all_goods',array('cate'=>'0-1')); ?>">更多</a>
						</div>						
						<div class="m-index-mod-bd">
							<ul class="w-goodsList w-goodsList-brief m-index-newArrivals-list">

                                <?php if(is_array($new_goods)): $i = 0; $__LIST__ = $new_goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <li class="w-goodsList-item">
                                        <?php if($vo['code'] == 'shiyuanduobao'): ?>
                                            <img class="ico ico-label ico-label-goods" src="__MOBILE_STATIC__/img/icon_ten.png">
                                        <?php endif; ?>
                                        <div class="w-goods w-goods-brief">
                                            <div class="w-goods-pic w-new-pic">
                                                <a href="<?php echo U('Goods/goods_detail',array('nper_id'=>$vo['nper_id'])); ?>"  title="<?php echo $vo['name']; ?>">
                                                    <img src="<?php echo $vo['image_src']; ?>"/>
                                                </a>
                                            </div>
                                            <p class="w-goods-title f-txtabb"><a title="<?php echo $vo['name']; ?>" href="<?php echo U('index/all_goods',array('cate'=>'0-1')); ?>" ><?php echo $vo['name']; ?></a></p>
                                        </div>
                                    </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>

						    </ul>
						</div>
					</div>

                    
					<div class="m-index-mod m-index-popular">
						<div class="m-index-mod-hd">
							<h3>今日热门商品</h3>
								<a class="m-index-mod-more" href="<?php echo U('index/all_goods',array('cate'=>'0-1')); ?>" >更多</a>
						</div>
						<div class="m-index-mod-bd">
							<ul class="w-goodsList w-goodsList-s m-index-popular-list">
                                <?php if(is_array($hot_goods)): $i = 0; $__LIST__ = $hot_goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <li class="w-goodsList-item">

                                        <div class="w-goods w-goods-ing" data-gid="1093" data-period="303175306" data-price="8090" data-buyUnit="10">
                                            <?php if($vo['code'] == 'shiyuanduobao'): ?>
                                                 <img class="ico ico-label ico-label-goods" src="__MOBILE_STATIC__/img/icon_ten.png">
                                            <?php endif; ?>
                                            <div class="w-goods-pic w-hot-pic">
                                                <a href="<?php echo U('Goods/goods_detail',array('nper_id'=>$vo['nper_id'])); ?>">
                                                    <img src="<?php echo $vo['image_src']; ?>" />
                                                </a>
                                            </div>

                                            <div class="w-goods-info">
                                                <p class="w-goods-title f-txtabb"><a href="<?php echo U('Goods/goods_detail',array('nper_id'=>$vo['nper_id'])); ?>" ><?php echo $vo['name']; ?></a></p>
                                                <div class="w-progressBar">
                                                    <p class="txt">揭晓进度<strong><?php echo $vo['progress']; ?>%</strong></p>
                                                    <p class="wrap">
                                                        <span class="bar" style="width:<?php echo $vo['progress']; ?>%;"><i class="color"></i></span>
                                                    </p>
                                                </div>
                                            </div>


                                            <div class="w-goods-shortFunc  m-tip">
                                                <button class="w-button w-button-round w-button-addToCart w-bt1s" nper-id="<?php echo $vo['nper_id']; ?>"></button>
                                            </div>

                                           <div class="w-index">
                                           <!--1元夺宝-->


                                             <!--1元夺宝 AND 0元夺宝  -->
                                               <?php if($zero_start == 1 AND $vo['is_zero'] == 1): ?>
                                             <a href="<?php echo U('Goods/goods_detail',array('nper_id'=>$vo['nper_id'])); ?>" class="w-index-a w-index-z"><?php echo C('ONE_AWARD_BTN_KEYWORDS'); ?></a>
                                              <a href="<?php echo U('Goods/zero_detail',array('nper_id'=>$vo['nper_id'])); ?>" class="w-index-a w-index-z">0元夺宝</a>
                                               <?php else: ?>
                                               <a href="<?php echo U('Goods/goods_detail',array('nper_id'=>$vo['nper_id'])); ?>" class="w-index-a"><?php echo C('ONE_AWARD_BTN_KEYWORDS'); ?></a>
                                               <?php endif; ?>
                                           </div>
                                        </div>
                                    </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                            <div class="w-more">
                                <a href="<?php echo U('index/all_goods',array('cate'=>'0-1')); ?>" >点击查看更多商品</a>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
<input type="hidden" value="<?php echo U('Cart/ajax_add_cart'); ?>" id="cart-url"/>
<div class="g-footer">
    <div class="g-wrap">
        <p style="margin-bottom: 7px;text-align: left;border-bottom: #DCDCDC 1px dotted;padding: 2px 6px;">特别说明：苹果公司不是<?php echo C('WEBSITE_NAME'); ?>赞助商，并且苹果公司也不会以任何形式参与其中！</p>
            <ul class="m-state f-clear">
                <li><i class="ico ico-state ico-state-1"></i>公平公正公开</li>
                <li><i class="ico ico-state ico-state-2"></i>正品保证</li>
                <li class="last"><i class="ico ico-state ico-state-3"></i>权益保障</li>
            </ul>
        <p class="m-link">
            <a href="<?php echo U('Article/help'); ?>" >什么是<?php echo C('WEBSITE_NAME'); ?>？</a><var>|</var>
            <a href="<?php echo $down; ?>"  target="_blank" style="color:#0079fe" class="footer_dl">下载APP</a>
        </p>
        <p class="m-copyright"><?php echo (isset($wap_config_info['record_num']) && ($wap_config_info['record_num'] !== '')?$wap_config_info['record_num']:''); ?>  &nbsp;<span><?php echo (isset($wap_config_info['copy_right']) && ($wap_config_info['copy_right'] !== '')?$wap_config_info['copy_right']:''); ?></span></p>
    </div>
</div>
<!--底部导航栏--->

<a href="<?php echo U('Cart/cart_list'); ?>"><div class="indw end"><span id="count" style="position:absolute;width:20px;height:20px;margin-left:30px;top:-5px;font-size:13px;background-color:#000;border-radius:12px;color:#fff;line-height:20px;text-align:center"><?php echo $cart_num; ?></span></div></a>
<div class="fhdb"></div>
<script src="__MOBILE_JS__/fly.js"></script>
<script src="__MOBILE_JS__/requestAnimationFrame.js"></script>


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