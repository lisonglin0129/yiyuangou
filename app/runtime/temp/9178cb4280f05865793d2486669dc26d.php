<?php if (!defined('THINK_PATH')) exit(); /*a:8:{s:60:"D:\webroot\mengdie_yyg\app\www_tpl\default\goods\detail.html";i:1468303703;s:57:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/base.html";i:1470730361;s:63:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/common_css.html";i:1468303703;s:61:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/tool_bar.html";i:1470730313;s:59:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/header.html";i:1468303703;s:59:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/footer.html";i:1470726624;s:61:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/base_url.html";i:1468303703;s:62:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/common_js.html";i:1468303703;}*/ ?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title><?php echo $g_info['name']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo htmlspecialchars($site_config['website_desc']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($site_config['website_keyword']); ?>">
    <link rel="stylesheet" type="text/css" href="__static__/css/common.css" />
<link rel="stylesheet" type="text/css" href="__yyg__/css/common.css" />
<link rel="stylesheet" type="text/css" href="__plugin__/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="__plugin__/bigautocomplete/jquery.bigautocomplete.css" />

    
<link rel="stylesheet" type="text/css" href="__static__/css/detail.css" />
<style>
    .luck_num {
        color: #fff;
        background: #f36;
    }

    .list-join {
        text-align: left;
        color: #aaa;
        font-size: 17px;
    }
</style>

    <script type="text/javascript" src="__static__/js/jquery.min.js"></script>
</head>
<body>
<div class="g-header">
    <div class="m-toolbar" >
    <div class="g-wrap f-clear">
        <div class="m-toolbar-l">
            <span class="m-toolbar-welcome"><?php echo C('WEBSITE_WELCOME'); ?></span>
        </div>
        <ul class="m-toolbar-r">
            <li class="m-toolbar-login">
                <?php if(is_user_login()): ?>
                <div id="pro-view-6"><a class="m-toolbar-login-btn" href="<?php echo dwz_filter('ucenter/index'); ?>"><?php echo get_user_nick_name(); ?></a>
                    <a href="javascript:" class="login_out">退出登录</a></div>
                <?php else: ?>
                <div id=""><a class="m-toolbar-login-btn" href="<?php echo dwz_filter('user/login'); ?>">请登录</a>
                    <a href="<?php echo dwz_filter('user/reg'); ?>" >免费注册</a></div>
                <?php endif; ?>
            </li>
            <li class="m-toolbar-myDuobao">
                <a class="m-toolbar-myDuobao-btn" href="<?php echo dwz_filter('ucenter/index'); ?>">我的夺宝
                    <i class="ico ico-arrow-gray-s ico-arrow-gray-s-down"></i></a>
                <ul class="m-toolbar-myDuobao-menu">
                    <li><a href="<?php echo dwz_filter('ucenter/deposer'); ?>">夺宝记录</a></li>
                    <li class="m-toolbar-myDuobao-menu-win"><a href="<?php echo dwz_filter('ucenter/luck'); ?>">幸运记录</a></li>
                    <!--<li class="m-toolbar-myDuobao-menu-mall"><a href="<?php echo dwz_filter('ucenter/index'); ?>">购买记录</a></li>-->
                    <!--<li class="m-toolbar-myDuobao-menu-gems"><a href="#">我的宝石</a></li>-->
                    <li><a href="<?php echo dwz_filter('pay/recharge'); ?>">账户充值</a></li>
                    <?php if((empty($promote_spread) == false AND $promote_spread['status'] == 1) OR (empty($reg_spread) == false AND $reg_spread['status'] == 1)): ?>
                    <li><a href="<?php echo dwz_filter('ucenter/promote'); ?>">我的推广</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <!--<li class="m-toolbar-myBonus"><a href="#">我的红包</a><var>|</var></li>-->
            <li>
                <a href="<?php echo C('WEBSITE_SINA_WEIBO'); ?>" target="_blank">
                <img width="16" height="13" style="float:left;margin:8px 3px 0 0;" src="__static__/img/icon_weibo_s.png">官方微博</a>
                <var>|</var>
                <a href="<?php echo C('WEBSITE_QQ_GROUP'); ?>" target="_blank">官方交流群</a>
            </li>
            <!--<li><a href="http://1.xiangchang.com/groups.do">官方交流群</a></li>-->
        </ul>
    </div>
</div>
    <script>
    function no_img(img){
        img.attr('src',$("#no_img_url").val());
        img.onerror=null;
    }
</script>
<div class="m-header">
    <div class="g-wrap f-clear">
        <div class="m-header-logo">
            <h1><a class="m-header-logo-link" href="/" style="background-image: url('<?php echo C("WEBSITE_LOGO"); ?>')"><?php echo C('WEBSITE_NAME'); ?></a></h1>
            <div class="m-header-slogan">
                <a class="m-header-slogan-link" href="<?php echo C('ANDROID_DOWNLOAD_URL'); ?>" target="_blank" style="height: 90px;">
                    <img src="<?php echo C('WEBSITE_LOGO_SUB'); ?>" style="height: 90px;"></a>
            </div>
        </div>

        <div class="w-miniCart" id="cart_list_fade_in">
            <a class="w-miniCart-btn" href="javascript:void(0);" data-pro="btn"><i class="ico ico-miniCart"></i>清 单
                <b class="w-miniCart-count" >
                    <i class="ico ico-arrow-white-solid ico-arrow-white-solid-l"></i><p id="cart_num"><?php echo (isset($cart_num) && ($cart_num !== '')?$cart_num:'0'); ?></p></b>
            </a>
            <div class="w-layer w-miniCart-layer" style="display: none" id="cart_box_hidden">

                <div pro="title">
                    <p class="w-miniCart-layer-title" id="pro-view-28"><strong>最近加入的商品</strong></p>
                </div>

                <div id="cart_div_area">
                    <div class="w-miniCart-loading">
                        <b class="w-loading-ico"></b><span class="w-loading-txt">加载中……</span>
                    </div>
                   <!-- <ul pro="list" class="w-miniCart-list">
                        <li class="w-miniCart-item" id="pro-view-27">
                            <div class="w-miniCart-item-pic">
                                <img src="../../img/goods/b2.png" alt="佳能 EOS 5D Mark III 单反机身(无镜头)" width="74px" height="74px" />
                            </div>
                            <div class="w-miniCart-item-text">
                                <p><strong>佳能 EOS 5D Mark III 单反机身(无镜头)</strong></p>
                                <p><em>730<?php echo C('MONEY_NAME'); ?> &times; 1期</em><a class="w-miniCart-item-del" href="javascript:void(0);" pro="del">删除</a></p>
                            </div></li>
                    </ul>
                    <div pro="footer" class="w-miniCart-layer-footer" id="pro-view-29">
                        <p><strong>共有<b pro="count">1</b>件商品，金额总计：<em><span pro="amount">730</span><?php echo C('MONEY_NAME'); ?></em></strong></p>
                        <p><button pro="go" class="w-button w-button-main">查看清单并结算</button></p>
                    </div>-->
                </div>
            </div>
        </div>

        <div class="w-search"  id="pro-view-2" >
            <form action="<?php echo dwz_filter('lists/search'); ?>" method="get" id="searchForm" class="w-search-form f-clear" target="_blank">
                <div class="w-input" id="pro-view-3"><div class="autoComplete"><input id="search-condition" class="w-input-input" type="text" placeholder="请输入要搜索的商品" name="keyword"></div></div>
                <button type="submit" class="w-search-btn" ><i class="ico ico-search"></i></button>
            </form>
            <div class="w-search-recKeyWrap">
                <?php if(is_array($keywords)): $i = 0; $__LIST__ = $keywords;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$keyword): $mod = ($i % 2 );++$i;?>
                    <a class="w-search-recKey" href="javascript:void(0);"><?php echo (isset($keyword) && ($keyword !== '')?$keyword:''); ?></a>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </div>
</div>
    <div class="m-nav" id="pro-view-1">
        <div class="g-wrap f-clear">
            <div class="m-catlog <?php echo strtolower(MODULE_NAME)=='yyg' && strtolower(CONTROLLER_NAME) == 'index' && strtolower(ACTION_NAME) == 'index'?' m-catlog-normal':' m-catlog-fold'; ?>">
                <div class="m-catlog-hd" style="padding-left:30px;cursor:pointer">
                    <h2>商品分类<i class="ico ico-arrow ico-arrow-white ico-arrow-white-down"></i></h2>
                </div>
                <div class="m-catlog-wrap"
                     style="<?php echo strtolower(MODULE_NAME)=='yyg' && strtolower(CONTROLLER_NAME) == 'index' && strtolower(ACTION_NAME) == 'index'?'':'height: 0; '; ?>">
                    <div class="m-catlog-bd">
                        <ul class="m-catlog-list">
                            <li><a class="catlog-0" href="<?php echo dwz_filter('lists/index',['category'=>0]); ?>">全部商品</a></li>
                            <!--  <?php if(is_array($category_list)): $i = 0; $__LIST__ = $category_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$each_category): $mod = ($i % 2 );++$i;?> -->
                            <li><a href="<?php echo dwz_filter('lists/index',['category'=>$each_category['id']]); ?>">
                                <?php if(empty(trim($each_category["icon"]))): ?>
                                <i class="fa <?php echo (isset($each_category['style']) && ($each_category['style'] !== '')?$each_category['style']:'fa-star-o'); ?>"></i>
                                <span>&nbsp;<?php echo $each_category['name']; ?></span>
                                <?php else: ?>
                                <img style="width: 16px;height: 16px;display: inline-block;" src="<?php echo (isset($each_category['icon']) && ($each_category['icon'] !== '')?$each_category['icon']:''); ?>" alt="">
                                <span style="position: absolute;margin-top: -2px;">&nbsp;<?php echo $each_category['name']; ?></span>
                                <?php endif; ?>

                                </a>
                            </li>
                          <!--   <?php endforeach; endif; else: echo "" ;endif; ?>  -->
                        </ul>
                    </div>
                    <div class="m-catlog-ft"></div>
                </div>
            </div>
            <div class="m-menu">
                <ul class="m-menu-list">
                    <li class="m-menu-list-item <?php echo strtolower(CONTROLLER_NAME) == 'index' && strtolower(ACTION_NAME) == 'index'?' selected':' '; ?>"
                        data-name="index">

                        <a class="m-menu-list-item-link" href="/">首页</a>

                    </li>
                    <li class="m-menu-list-item <?php echo strtolower(CONTROLLER_NAME) == 'lists' && strtolower(ACTION_NAME) == 'ten'?' selected':' '; ?>"
                        data-name="ten">
                        <var>|</var>
                        <a class="m-menu-list-item-link" href="<?php echo dwz_filter('lists/ten'); ?>">十元专区</a>

                    </li>
                    <li class="m-menu-list-item <?php echo strtolower(CONTROLLER_NAME) == 'lists' && strtolower(ACTION_NAME) == 'results'?' selected':' '; ?>"
                        data-name="results">
                        <var>|</var>
                        <a class="m-menu-list-item-link" href="<?php echo dwz_filter('lists/results'); ?>">最新揭晓</a>

                    </li>
                    <li class="m-menu-list-item <?php echo strtolower(CONTROLLER_NAME) == 'share' && strtolower(ACTION_NAME) == 'index'?' selected':' '; ?>"
                        data-name="share">
                        <var>|</var>
                        <a class="m-menu-list-item-link" href="<?php echo dwz_filter('Share/index'); ?>">晒单分享</a>

                    </li>
                    <!--<li class="m-menu-list-item dropdown" data-name="find">-->
                        <!--<var>|</var>-->
                        <!--<a class="m-menu-dropdown" href="javascript:void(0);">活动专区</a>-->

                    <!--</li>-->
                </ul>
            </div>
        </div>
    </div>
</div>
<input id="get_goods_url" type="hidden" value="<?php echo U('index/get_goods_key'); ?>">
<input id="flag_trigger" type="hidden" value="<?php echo U('core/common/flag_trigger'); ?>">


<div class="g-body">
    <div class="m-detail m-detail-willReveal">
        <div class="g-wrap g-body-hd f-clear">
            <div class="g-main">
                <div class="w-dir">
                    <a href="/">首页</a> &gt;<?php if(!empty($cat_name)): ?> <a href="<?php echo dwz_filter('lists/index',['category'=>$g_info['category']]); ?>"><?php echo $cat_name; ?></a> &gt; <?php endif; ?><span
                        class="txt-gray">第<?php echo (num_base_mask($now_issue) !== ''?num_base_mask($now_issue):''); ?>期 <?php echo $g_info['name']; ?></span>
                </div>
                <div class="g-main-l m-detail-show pic_start">
                    <div class="w-gallery">
                        <div class="w-gallery-fullsize">
                            <?php if($g_info['code'] == 'shiyuanduobao'): ?>
                                <img class="ico ico-label ico-label-goods" src="__static__/img/icon_ten.png">
                            <?php endif; ?>
                            <div class="w-gallery-picture">
                                <?php if(!empty($pic_index)): ?>
                                <img src="__UPLOAD_DOMAIN__<?php echo $pic_index; ?>">
                                <?php else: ?>
                                <img src="__yyg__/images/empty_img.jpg">
                                <?php endif; ?>
                            </div>
                        </div>
                        <i class="ico ico-arrow ico-arrow-red ico-arrow-red-up" style="left: 31px;"></i>
                        <div class="w-gallery-thumbnail">
                            <ul class="w-gallery-thumbnail-list">
                                <!--循环-->
                                <?php if(!empty($pic_list)): if(is_array($pic_list)): foreach($pic_list as $k=>$vo): if($k < 4): ?>
                                <li class="w-gallery-thumbnail-item ">
                                    <img src="__UPLOAD_DOMAIN__<?php echo (isset($vo['img_path']) && ($vo['img_path'] !== '')?$vo['img_path']:'__yyg__/images/empty_img.jpg'); ?>">
                                </li>
                                <?php endif; endforeach; endif; endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="m-detail-promise f-clear">
                        <span class="m-detail-promise-item"><i
                                class="ico ico-detail-promise ico-detail-promise-open"></i><span
                                class="m-detail-promise-item-txt">公正公开</span></span>
                        <span class="m-detail-promise-item"><i
                                class="ico ico-detail-promise ico-detail-promise-real"></i><span
                                class="m-detail-promise-item-txt">正品保证</span></span>
                        <span class="m-detail-promise-item"><i
                                class="ico ico-detail-promise ico-detail-promise-right"></i><span
                                class="m-detail-promise-item-txt">权益保障</span></span>
                        <span class="m-detail-promise-item m-detail-promise-item-last"><i
                                class="ico ico-detail-promise ico-detail-promise-ship"></i>
                            <span class="m-detail-promise-item-txt">免费配送</span></span>
                    </div>
                    <div class="w-shareTo" style="height: 20px; padding-top: 12px;">

                        <div id="ckepop">
                            <a href="http://www.jiathis.com/share/" class="jiathis jiathis_txt jtico jtico_jiathis" target="_blank">分享到：</a>
                            <a class="jiathis_button_tools_1"></a>
                            <a class="jiathis_button_tools_2"></a>
                            <a class="jiathis_button_tools_3"></a>
                            <a class="jiathis_button_tools_4"></a>
                        </div>
                    </div>
                </div>
                <?php if($n_info['status'] == '1'): ?>
                <!--未开奖S-->
                <div class="g-main-m m-detail-main" id="no_lottery">
                    <div class="m-detail-main-intro">
                        <div class="m-detail-main-title">
                            <h1 title="<?php echo $g_info['name']; ?>"><?php echo $g_info['name']; ?></h1>
                        </div>
                        <p class="m-detail-main-desc" title="<?php echo $g_info['sub_title']; ?>"><?php echo $g_info['sub_title']; ?></p>
                    </div>
                    <div class="m-detail-main-ing m-detail-main-onlyOne">
                        <?php if($g_info["status"] == 1 AND $g_info["num"] > 0): ?>
                        <!--正常-->
                        <div class="m-detail-main-onlyOne-content" id="typeOne">
                            <div class="m-detail-main-one-header f-clear">
                                <h2 class="m-detail-main-type-title"><?php echo $n_info['de_name']; ?></h2>
                                <h3 class="m-detail-main-type-subtitle"><span class="period">期号：<?php echo (num_base_mask($now_issue) !== ''?num_base_mask($now_issue):''); ?> </span>每满总需<?php echo $n_info['sum_times']; ?>人次，即抽取1人获得该商品
                                </h3>
                                <div class="m-detail-main-one-explanation">?
                                    <div class="more-box">
                                        <i class="ico ico-detail-main-left-anchor"></i>
                                        <div class="content">

                                            <p>
                                                “一元夺宝”指的是一件商品被平分成若干等份，用户可以使用<?php echo C('MONEY_NAME'); ?>购买其中一份或多份（1<?php echo C('MONEY_NAME'); ?>可购买一份，每份对应一个夺宝码）。
                                                </p>
                                            <p>当所有等份全部售完后，一元夺宝系统会根据平台规则计算出一个“幸运夺宝码”。<strong>持有“幸运夺宝码”的用户获得该商品，其它用户轮空。</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-detail-main-one-progress">
                                <div class="w-progressBar f-clear" title="40%">
                                    <div class="w-progressBar-wrap">
                                        <span class="w-progressBar-bar" style="width:<?php echo $precent; ?>%;"></span>
                                    </div>
                                    <div class="w-progressBar-txt">已完成<?php echo $precent; ?>%</div>
                                </div>
                            </div>
                            <div class="m-detail-main-one-intro f-clear">
                                <div class="m-detail-main-one-price">总需人次<?php echo $n_info['sum_times']; ?></div>
                                <div class="m-detail-main-one-remain">剩余人次<?php echo $last_times; ?></div>
                            </div>
                            <div class="m-detail-main-one-count f-clear">
                                <span>参与</span>
                                <div id="buyNumbers"
                                     class="m-detail-main-count-number m-detail-main-count-buyTimes f-clear">
                                    <div class="w-number">
                                        <a class="w-number-btn w-number-btn-minus reduce_num change_num_btn"
                                           href="javascript:void(0);">－</a>
                                        <input data-last="<?php echo (isset($last_times) && ($last_times !== '')?$last_times:1); ?>"
                                               data-unit="<?php echo (isset($n_info['unit_price']) && ($n_info['unit_price'] !== '')?$n_info['unit_price']:1); ?>"
                                               data-min="<?php echo (isset($n_info['min_times']) && ($n_info['min_times'] !== '')?$n_info['min_times']:1); ?>"
                                               data-max="<?php echo (isset($n_info['max_times']) && ($n_info['max_times'] !== '')?$n_info['max_times']:1000); ?>"
                                               class="w-number-input participation_num" type="text"
                                               value="<?php echo (isset($n_info['init_times']) && ($n_info['init_times'] !== '')?$n_info['init_times']:1); ?>">
                                        <a class="w-number-btn w-number-btn-plus add_num change_num_btn"
                                           href="javascript:void(0);">＋</a>
                                    </div>
                                </div>
                                <?php if((int)$last_times > 10): ?>
                                    <a data-num="10" class="m-detail-bn-sel sel_num_input">10</a>
                                <?php endif; if((int)$last_times > 100): ?>
                                    <a data-num="100" class="m-detail-bn-sel sel_num_input">100</a>
                                <?php endif; if((int)$last_times > 1000): ?>
                                    <a data-num="1000" class="m-detail-bn-sel sel_num_input">1000</a>
                                <?php endif; ?>
                                <a data-num="<?php echo $last_times; ?>" class="m-detail-bn-sel sel_num_input">包尾</a>
                                <!--<span style="padding-left: 0;">人次</span>-->

                                <span class="m-detail-main-buyHint"><span class="ico-arror"></span>
                                    <b>加大参与人次，夺宝在望！</b></span>
                                <span class="m-detail-main-buyUnitHint">每次参与<?php echo ceil($n_info['unit_price']); ?>元,参与次数需是<?php echo (isset($n_info['min_times']) && ($n_info['min_times'] !== '')?$n_info['min_times']:'1'); ?>的倍数</span>
                            </div>
                            <div class="m-detail-main-one-operation f-clear">
                                <a data-type="quick_buy" data-nper="<?php echo $n_info['id']; ?>" data-id="<?php echo $n_info['pid']; ?>"
                                   id="quick_buy" class="m-detail-main-type-btn m-detail-main-one-buy buy_now_btn"
                                   href="javascript:void(0)"><?php echo C('ONE_AWARD_BTN_KEYWORDS'); ?></a>
                                <a data-type="add_to_cart" data-nper="<?php echo $n_info['id']; ?>" data-id="<?php echo $n_info['pid']; ?>"
                                   id="add_to_cart"
                                   class="m-detail-main-type-btn m-detail-main-one-cart buy_now_btn"
                                   href="javascript:void(0)">
                                    <i class="ico ico-miniCart"></i>
                                    <span class="btn-txt">加入清单</span>
                                </a>
                            </div>
                            <!--已参与-->
                            <div class="m-detail-main-one-codes">
                                <?php if(!empty($luck_list)): ?>
                                <div class="m-detail-codes">
                                    <h4><label>您已参与:</label><span><?php echo (isset($now_user_num) && ($now_user_num !== '')?$now_user_num:0); ?> 人次</span></h4>
                                    <div>
                                        <label>夺宝号码:</label>
                                        <ul class="m-detail-codes-list">
                                                <?php $i=0; if(is_array($luck_list)): foreach($luck_list as $k=>$vo): $i++; ?>
                                                <li  class="single ">
                                                    <?php echo (num_base_mask($vo,"1") !== ''?num_base_mask($vo,"1"):''); ?>
                                                </li>
                                            <?php endforeach; endif; ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php else: ?>
                                <span>你还没参与本期商品哦~</span>
                                <?php endif; ?>
                            </div>
                            <!--未参与-->
                            <!--<div class="m-detail-main-one-codes">
                                你还没参与本期商品哦~
                            </div>-->
                        </div>
                        <?php elseif($g_info["status"] == 1 AND $g_info["num"] < 1): ?>
                        <!--缺货-->
                        <div id="wrapOutOfStock" class="m-detail-main-outOfStock f-clear">
                            <i class="ico m-detail-main-outOfStock-ico"></i>
                            <div class="m-detail-main-outOfStock-content">
                                <p><span>此商品暂时缺货。我们会尽快重新上架，</span><span>敬请期待！</span></p>
                                <a href="/">去逛逛其它商品</a>
                            </div>
                        </div>
                        <?php else: ?>
                        <!--下架-->
                        <div id="wrapExpired" class="m-detail-main-soldOut">商品已下架</div>
                        <?php endif; ?>
                    </div>
                </div>
                <!--未开奖E--->
                <?php endif; if($n_info['status'] == '2'): ?>
                <!--开奖中S-->
                <div class="g-main-m m-detail-main">
                    <div class="m-detail-main-intro">
                        <div class="m-detail-main-title">
                            <h1 title="<?php echo $g_info['name']; ?>）"><?php echo $g_info['name']; ?></h1>
                        </div>
                        <p class="m-detail-main-desc" style="height: 36px;line-height: 26px;" title="<?php echo $g_info['sub_title']; ?>"><?php echo $g_info['sub_title']; ?></p>
                    </div>
                    <div class="m-detail-main-countdown f-clear">
                        <i class="ico ico-detail-main-hourglass"></i>
                        <div class="m-detail-main-countdown-content">
                            <div class="m-detail-main-countdown-hd">
                                <span class="period">期号：<?php echo (num_base_mask($now_issue) !== ''?num_base_mask($now_issue):''); ?></span>
                                <span class="split">|</span>
                                <span class="title">揭晓倒计时</span>
                            </div>
                            <?php if(empty($sec)): ?>
                            <div class="m-detail-main-countdown-num count_down" data-sec="0" style="font-size: 16px;">
                                等待最新一期彩票开奖中,即将开奖,不要走开...
                            </div>
                            <?php else: ?>
                            <div class="m-detail-main-countdown-num count_down" data-sec="<?php echo $sec; ?>">
                                <span class="cd_min">--</span>:
                                <span class="cd_sec">--</span>:
                                <span class="cd_ms">--</span>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="m-detail-main-calculation f-clear">
                        <div class="m-detail-main-calculation-formula m-detail-main-calculation-main f-clear">
                            <div class="m-detail-main-calculation-title">如何计算？</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-luckyCode">
                                <span class="num">?</span>
                                <span class="tip">本期幸运号码</span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-equal">=</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-constant">
                                <span class="num"><?php echo num_base_mask(0,1,1); ?></span>
                                <span class="tip">固定数值</span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-add">+</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-variable">
                                <span class="num">?</span>
                                <span class="tip">变化数值</span>
                            </div>
                        </div>
                        <div class="m-detail-main-calculation-formula m-detail-main-calculation-secondary f-clear">
                            <div class="m-detail-main-calculation-title"><strong>变化数值</strong>是取下面公式的余数</div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-leftBracket">(
                            </div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-sum"
                            >
                                <span class="num"><?php echo (isset($n_info['sum_timestamp']) && ($n_info['sum_timestamp'] !== '')?$n_info['sum_timestamp']:'0'); ?></span>
                                <span class="tip" style="font-size: 12px;">最后50个订单时间和</span>
            <span class="more">
                <i class="ico ico-detail-main-calculation-tipBox"></i>
                <span class="more-content">商品的最后一个号码分配完毕，公示该分配时间点前本站全部商品的<strong>全部参与时间</strong>，并求和。</span>
            </span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-add">+</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-lottery"
                            >
                                <span class="num">?</span>
                                <span class="tip" style="font-size: 12px;">“老时时彩”开奖号</span>
            <span class="more">
                <i class="ico ico-detail-main-calculation-tipBox"></i>
                <span class="more-content">取最近一期“老时时彩” (第<?php echo (isset($n_info['lottory_issue']) && ($n_info['lottory_issue'] !== '')?$n_info['lottory_issue']:'--'); ?>期) 揭晓结果。</span>
            </span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-rightBracket">)
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-divide">求余</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-price">
                                <span class="num"><?php echo $n_info['sum_times']; ?></span>
                                <span class="tip" style="font-size: 12px;">总需人次</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-detail-main-one-codes hasopen" style="">
                        <?php if(!empty($luck_list)): ?>
                        <div class="m-detail-codes">
                            <h4><label>您已参与:</label><span><?php echo (isset($now_user_num) && ($now_user_num !== '')?$now_user_num:0); ?> 人次</span></h4>
                            <div>
                                <label>夺宝号码:</label>
                                <ul class="m-detail-codes-list">
                                    <?php $i=0; if(is_array($luck_list)): foreach($luck_list as $k=>$vo): $i++; ?>
                                    <li  class="single ">
                                        <?php echo (num_base_mask($vo,"1") !== ''?num_base_mask($vo,"1"):''); ?>
                                    </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </div>
                        </div>
                        <?php else: ?>
                        <span>你还没参与本期商品哦~</span>
                        <?php endif; ?>
                    </div>
                    <div class="m-detail-main-newest f-clear">
                        <div class="m-detail-main-newest-title">
                            <strong>【最新一期】</strong>正在火热进行中…
                        </div>
                        <div class="m-detail-main-newest-progress">
                            <div class="w-progressBar f-clear">
                                <div class="w-progressBar-wrap"><span class="w-progressBar-bar"
                                                                      style="width: <?php echo (isset($new_precent) && ($new_precent !== '')?$new_precent:'0'); ?>%;"></span>
                                </div>
                                <div class="w-progressBar-txt w-progressBar-empty">
                                    已完成<?php echo (isset($new_precent) && ($new_precent !== '')?$new_precent:'0'); ?>%，剩余<?php echo (isset($new_last_times) && ($new_last_times !== '')?$new_last_times:'0'); ?>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($new_nper_info)): ?>
                        <a class="m-detail-main-newest-go"
                           href="<?php echo dwz_filter('goods/detail',array('id'=>$g_info['id'].'-'.$new_nper_info['id'])); ?>"
                           target="_blank">立即前往</a>
                        <?php else: ?>
                        <a class="m-detail-main-newest-go" href="javascript:" style="background-color: #aaa;">暂无最新一期</a>
                        <?php endif; ?>
                    </div>
                </div>
                <!--开奖中E--->
                <?php endif; if($n_info['status'] == '3'): ?>
                <!--已开奖S-->
                <div class="g-main-m m-detail-main">
                    <!--中奖状态-->
                    <div class="m-detail-main-intro">
                        <div class="m-detail-main-title">
                            <h1 title="<?php echo $g_info['name']; ?>"><?php echo $g_info['name']; ?></h1>
                        </div>
                        <p class="m-detail-main-desc" title="<?php echo $g_info['sub_title']; ?>"><?php echo $g_info['sub_title']; ?></p>
                    </div>
                    <div class="m-detail-main-winner">
                        <div class="m-detail-main-winner-luckyCode f-clear">
                            <div class="hd">
                                <span class="period">期号<span class="period-num"><?php echo (num_base_mask($now_issue) !== ''?num_base_mask($now_issue):''); ?></span></span>
                                <span class="title">幸运号码</span>
                            </div>
                            <div class="code">
                                <?php if(empty($remainder)): echo num_base_mask(0,1,1); else: echo (num_base_mask($remainder,"1","1") !== ''?num_base_mask($remainder,"1","1"):'&#63;'); endif; ?>
                            </div>
                        </div>
                        <!--中奖用户信息-->
                        <div class="m-detail-main-winner-detail f-clear">
                            <i class="ico ico-detail-main-winner"></i>


                            <img width="90" height="90" src='<?php if(empty($u_info['img_path'])): ?>__UPLOAD_DOMAIN____yyg__/images/empty_img.jpg<?php else: ?>__UPLOAD_DOMAIN__<?php echo $u_info['img_path']; endif; ?>' class="user-avatar">
                            <div class="user-info">
                                <div class="info-item user-nickname">
                                    <span class="hd">用户昵称</span>：
                                    <span class="bd">
                                        <a href="#" target="_blank" title="<?php echo (isset($u_info['username']) && ($u_info['username'] !== '')?$u_info['username']:'--'); ?>"><?php echo (isset($u_info['nick_name']) && ($u_info['nick_name'] !== '')?$u_info['nick_name']:'--'); ?></a><?php if(!empty($u_info['ip_area'])): ?>（<?php echo (isset($u_info['ip_area']) && ($u_info['ip_area'] !== '')?$u_info['ip_area']:''); ?>）<?php endif; ?></span>
                                </div>
                                <div class="info-item user-id"><span class="hd">用户UID</span>：
                                    <span class="bd">
                                    <?php echo (isset($u_info['id']) && ($u_info['id'] !== '')?$u_info['id']:'--'); ?>
                                    <span style="font-size: 12px;color:#aaa;">（ID为用户唯一不变标识）</span>
                                </span>
                                </div>
                                <div class="info-item user-buyTimes"><span class="hd">本期参与</span>：<span
                                        class="bd"><?php echo (isset($luck_num_count) && ($luck_num_count !== '')?$luck_num_count:'0'); ?>人次</span></div>
                            </div>
                            <div class="record-info">
                                <div class="info-item published-time"><span class="hd">揭晓时间</span>：<span class="bd">
                                    <?php echo microtime_format($n_info['luck_time'],3,'Y-m-d H:i:s'); ?>
                                </span>
                                </div>
                                <div class="info-item buy-time">
                                    <span class="hd">夺宝时间</span>：
                                    <span class="bd">
                                        <?php echo microtime_format($luck_time,3,'Y-m-d H:i:s'); ?>
                                    </span>
                                </div>
                                <div class="info-item codes">
                                    <a id="btnWinnerCodes" class="see_luck_num" href="javascript:void(0)">
                                        查看TA的号码&gt;&gt;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-detail-main-calculation f-clear">
                        <div class="m-detail-main-calculation-formula m-detail-main-calculation-main f-clear">
                            <div class="m-detail-main-calculation-title">如何计算？</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-luckyCode">
                                <span class="num">
                                     <?php if(empty($remainder)): echo num_base_mask(0,1,1); else: echo (num_base_mask($remainder,"1","1") !== ''?num_base_mask($remainder,"1","1"):'&#63;'); endif; ?>
                                </span>
                                <span class="tip">本期幸运号码</span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-equal">=</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-constant">
                                <span class="num"><?php echo num_base_mask(0,1,1); ?></span>
                                <span class="tip">固定数值</span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-add">+</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-variable">
                                <span class="num"><?php echo (isset($remainder) && ($remainder !== '')?$remainder:'&#63;'); ?></span>
                                <span class="tip">变化数值</span>
                            </div>
                        </div>
                        <div class="m-detail-main-calculation-formula m-detail-main-calculation-secondary f-clear">
                            <div class="m-detail-main-calculation-title"><strong>变化数值</strong>是取下面公式的余数</div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-leftBracket">(
                            </div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-sum"
                            >
                                <span class="num"><?php echo (isset($n_info['sum_timestamp']) && ($n_info['sum_timestamp'] !== '')?$n_info['sum_timestamp']:'--'); ?></span>
                                <span class="tip" style="font-size: 12px;">最后50个订单时间和</span>
                                    <span class="more">
                                        <i class="ico ico-detail-main-calculation-tipBox"></i>
                                        <span class="more-content">商品的最后一个号码分配完毕，公示该分配时间点前本站全部商品的<strong>全部参与时间</strong>，并求和。</span>
                                    </span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-add">+</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-lottery"
                            >
                                <span class="num"><?php echo (isset($n_info['open_num']) && ($n_info['open_num'] !== '')?$n_info['open_num']:'--'); ?></span>
                                <span class="tip" style="font-size: 12px;">“老时时彩”开奖号</span>
                                    <span class="more">
                                        <i class="ico ico-detail-main-calculation-tipBox"></i>
                                        <span class="more-content">取最近一期“老时时彩” (第<?php echo (isset($n_info['lottery_issue']) && ($n_info['lottery_issue'] !== '')?$n_info['lottery_issue']:'--'); ?>期) 揭晓结果。</span>
                                    </span>
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-rightBracket">)
                            </div>
                            <div class="m-detail-main-calculation-operation m-detail-main-calculation-divide">求余</div>
                            <div class="m-detail-main-calculation-parameter m-detail-main-calculation-price">
                                <span class="num"><?php echo $n_info['sum_times']; ?></span>
                                <span class="tip" style="font-size: 12px;">总需人次</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-detail-main-one-codes hasopen">
                        <?php if(!empty($luck_list)): ?>
                        <div class="m-detail-codes">
                            <h4><label>您已参与:</label><span><?php echo (isset($now_user_num) && ($now_user_num !== '')?$now_user_num:0); ?> 人次</span></h4>
                            <div>
                                <label>夺宝号码:</label>
                                <ul class="m-detail-codes-list">
                                    <?php $i=0; if(is_array($luck_list)): foreach($luck_list as $k=>$vo): $i++; ?>
                                    <li  class="single ">
                                        <?php echo (num_base_mask($vo,"1") !== ''?num_base_mask($vo,"1"):''); ?>
                                    </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </div>
                        </div>
                        <?php else: ?>
                        <span>你还没参与本期商品哦~</span>
                        <?php endif; ?>
                    </div>
                    <div class="m-detail-main-newest f-clear m-detail-main-newest1s">
                        <div class="m-detail-main-newest-title"><strong>【最新一期】</strong>正在火热进行中…</div>
                        <div class="m-detail-main-newest-progress">
                            <div class="w-progressBar f-clear">
                                <div class="w-progressBar-wrap">
                                    <span class="w-progressBar-bar" style="width: <?php echo (isset($new_precent) && ($new_precent !== '')?$new_precent:'0'); ?>%;"></span>
                                </div>
                                <div class="w-progressBar-txt w-progressBar-empty">
                                    已完成<?php echo (isset($new_precent) && ($new_precent !== '')?$new_precent:'0'); ?>%，剩余<?php echo (isset($new_last_times) && ($new_last_times !== '')?$new_last_times:'0'); ?>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($new_nper_info)): ?>
                        <a class="m-detail-main-newest-go"
                           href="<?php echo dwz_filter('goods/detail',array('id'=>$g_info['id'].'-'.$new_nper_info['id'])); ?>"
                           target="_blank">立即前往</a>
                        <?php else: ?>
                        <a class="m-detail-main-newest-go" href="javascript:" style="background-color: #aaa;">暂无最新一期</a>
                        <?php endif; ?>
                    </div>
                    <!--中奖状态-->
                </div>
                <!--已开奖E--->
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="w-tabs w-tabs-main m-detail-mainTab">
        <div class="g-wrap g-body-bd f-clear">
            <div class="w-tabs-tab">
                <?php if($n_info['status'] == '1'): ?>
                <div id="intro_tab" class="w-tabs-tab-item w-tabs-tab-item-selected">商品详情</div>
                <?php else: ?>
                <div id="result_tab" class="w-tabs-tab-item w-tabs-tab-item-selected get_calc_result_list"
                     data-id="<?php echo $n_info['id']; ?>">计算结果
                </div>
                <?php endif; ?>
                <div id="record_tab" class="w-tabs-tab-item get_deposer_list" data-id="<?php echo $n_info['id']; ?>">夺宝参与记录</div>
                <div id="share_tab" class="w-tabs-tab-item get_delivery_list" data-id="<?php echo $g_info['id']; ?>">晒单</div>
                <div id="history_tab" class="w-tabs-tab-item get_history_list" data-id="<?php echo $g_info['id']; ?>">往期夺宝
                </div>
            </div>
            <div class="w-tabs-panel">
                <?php if($n_info['status'] == '1'): ?>
                <!--商品详情-->
                <div id="intro_panel" class="w-tabs-panel-item intro_tab" style="">
                    <!--循环-->
                    <?php echo $g_info['detail']; ?>
                    <!--<dl class="special">-->
                        <!--<dt>重要说明：</dt>-->
                        <!--<dd>1、本商品由第三方提供；<br>2、以上详情页面展示信息仅供参考，商品以实物为准；<br>3、如快递无法配送至获得者提供的送货地址，将默认配送到距离最近的送货地，由获得者本人自提。-->
                        <!--</dd>-->
                    <!--</dl>-->
                </div>
                <?php else: ?>
                <!--计算结果-->
                <div id="result_panel" class="w-tabs-panel-item result_tab">
                    <div class="m-detail-mainTab-calcRule">
                        <h4>
                        <span class="wrap">
                                <i class="ico ico-text"></i><span class="txt">幸运号码计算规则</span>
                        </span>
                        </h4>
                        <div class="ruleWrap">
                            <ol class="ruleList">
                                <li><span class="index">1</span>商品的最后一个号码分配完毕后，将公示该商品最后50条参与时间（如不足50条时则取该商品全部参与时间）；</li>
                                <li><span class="index">2</span>系统将这50个时间的数值相加，得出数值A，时间显示顺序依次为：分、秒、毫秒，例如23:12:25.342，其数值为1225342；
                                </li>
                                <li><span class="index">3</span>本着公平公开的原则，系统还会等待一小段时间，取离当前时间点最近的下一期中国福利彩票“老时时彩”的揭晓结果（5位数），作为数值B；
                                </li>
                                <li><span class="index">4</span>将数值A和数值B相加求和，再除以当期商品的总需人次取得余数<i style="margin-top:-3px;"
                                                                                          data-func="remainder"
                                                                                          class="ico ico-questionMark"></i>（例如：19÷3，商为6，余数为1）
                                    ，用余数+原始数 10000001得出的号码即为“幸运夺宝码”，持有幸运夺宝码的用户获得该商品；
                                </li>
                                <li class="txt-red">
                                    注：如遇福彩中心通讯故障，无法正常获取与当期商品揭晓时间点对应的中国福利彩票“老时时彩”揭晓结果，且24小时内仍无法获取，则默认“老时时彩”揭晓结果为00000。
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="calc_result_list_content">
                        <!--计算结果-->
                        <div class="w-loading">
                            <b class="w-loading-ico"></b><span class="w-loading-txt">正在努力加载……</span>
                        </div>
                    </div>

                </div>
                <?php endif; ?>
                <!--参与纪录-->
                <div id="record_panel" class="w-tabs-panel-item record_tab m-detail-mainTab-record"
                     style="display: none;">
                    <!--夺宝参与记录-->
                    <div class="content deposer_list_content">
                        <div class="w-loading">
                            <b class="w-loading-ico"></b><span class="w-loading-txt">正在努力加载……</span>
                        </div>
                    </div>
                </div>
                <!--晒单-->
                <div id="share_panel" class="w-tabs-panel-item share_tab m-detail-mainTab-share"
                     style="display: none;">
                    <!--晒单列表-->
                    <div class="delivery_list_content" id="pro-view-80">
                        <div class="w-loading">
                            <b class="w-loading-ico"></b><span class="w-loading-txt">正在努力加载……</span>
                        </div>
                    </div>
                </div>
                <!--历史-->
                <div class="w-tabs-panel-item history_tab m-detail-mainTab-history"
                     style="display: none;">
                    <div class="content history_content">
                        <div class="w-loading" id="pro-view-16">
                            <b class="w-loading-ico"></b><span class="w-loading-txt">正在努力加载……</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="get_calc_result_list" value="<?php echo U('get_calc_result_list'); ?>"><!--夺宝参与记录-->
<input type="hidden" id="get_deposer_list" value="<?php echo U('get_deposer_list'); ?>"><!--夺宝参与记录-->
<input type="hidden" id="get_delivery_list" value="<?php echo U('get_delivery_list'); ?>"><!--晒单-->
<input type="hidden" id="get_history_list" value="<?php echo U('get_history_list'); ?>"><!--往期夺宝-->
<input type="hidden" id="see_luck_num" value="<?php echo U('see_luck_num'); ?>"><!--获取用户购买幸运数字-->
<input type="hidden" id="get_code_list" value="<?php echo U('get_code_list'); ?>"> <!--获取参与用户购买的号码-->

<input type="hidden" id="nper_id" value="<?php echo (isset($n_info['id']) && ($n_info['id'] !== '')?$n_info['id']:'0'); ?>"><!--期数id-->
<input type="hidden" id="goods_id" value="<?php echo (isset($g_info['id']) && ($g_info['id'] !== '')?$g_info['id']:'0'); ?>"><!--商品id-->
<input type="hidden" id="luck_uid" value="<?php echo (isset($n_info['luck_uid']) && ($n_info['luck_uid'] !== '')?$n_info['luck_uid']:'0'); ?>"><!--幸运用户id-->

<input type="hidden" id="add_to_cart_url" value="<?php echo U('core/pay/add_to_cart'); ?>"><!--添加到购物车-->
<input type="hidden" id="quick_buy_url" value="<?php echo U('Pay/quick_buy'); ?>"><!--幸运用户id-->
<input type="hidden" id="nper_open_api" value="<?php echo U('core/Gdfc/open_by_nper'); ?>"><!--开奖api-->
<input type="hidden" id="nper_status" value="<?php echo (isset($n_info['status']) && ($n_info['status'] !== '')?$n_info['status']:1); ?>"><!--开奖状态-->

<!-- 分享代码JiaThis Button BEGIN -->
<script type="text/javascript" src="http://v2.jiathis.com/code/jia.js" charset="utf-8"></script>
<!-- JiaThis Button END -->


<div class="g-footer">
    <div class="m-instruction">
        <div class="g-wrap f-clear">
            <div class="g-main">
                <ul class="m-instruction-list">
                    <li class="m-instruction-list-item">
                        <h5><i class="ico ico-instruction ico-instruction-1"></i>新手指南
                        </h5>
                        <ul class="list">
                            <?php if(is_array($novice_directory)): $i = 0; $__LIST__ = $novice_directory;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$each_help_article): $mod = ($i % 2 );++$i;if($each_help_article["is_hide"] != 1): if(empty($each_help_article['outlink'])): if(empty($each_help_article['name'])): ?>
                            <li><a href="<?php echo dwz_filter('help/read',['id'=>$each_help_article['id']]); ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
                            </li>
                            <?php else: ?>
                            <li><a href="<?php echo dwz_filter('help/read',['name'=>$each_help_article['name']]); ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
                            </li>
                            <?php endif; else: ?>
                            <li><a href="<?php echo $each_help_article['outlink']; ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
                            </li>
                            <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li>
                    <li class="m-instruction-list-item">
                        <h5><i class="ico ico-instruction ico-instruction-2"></i>夺宝保障
                        </h5>
                        <ul class="list">
                            <?php if(is_array($indiana_security)): $i = 0; $__LIST__ = $indiana_security;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$each_help_article): $mod = ($i % 2 );++$i;if($each_help_article["is_hide"] != 1): if(empty($each_help_article['outlink'])): if(empty($each_help_article['name'])): ?>
                            <li><a href="<?php echo dwz_filter('help/read',['id'=>$each_help_article['id']]); ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
                            </li>
                            <?php else: ?>
                            <li><a href="<?php echo dwz_filter('help/read',['name'=>$each_help_article['name']]); ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
                            </li>
                            <?php endif; else: ?>
                            <li><a href="<?php echo $each_help_article['outlink']; ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
                            </li>
                            <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li>
                    <li class="m-instruction-list-item">
                        <h5><i class="ico ico-instruction ico-instruction-3"></i>商品配送
                        </h5>
                        <ul class="list">
                            <?php if(is_array($goods_send)): $i = 0; $__LIST__ = $goods_send;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$each_help_article): $mod = ($i % 2 );++$i;if($each_help_article["is_hide"] != 1): if(empty($each_help_article['outlink'])): if(empty($each_help_article['name'])): ?>
	                            <li><a href="<?php echo dwz_filter('help/read',['id'=>$each_help_article['id']]); ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
	                            </li>
	                            <?php else: ?>
	                            <li><a href="<?php echo dwz_filter('help/read',['name'=>$each_help_article['name']]); ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
	                            </li>
	                            <?php endif; else: ?>
                            <li><a href="<?php echo $each_help_article['outlink']; ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['title']); ?></a>
                            </li>
                            <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li>
                    <li class="m-instruction-list-item">
                        <h5><i class="ico ico-instruction ico-instruction-5"></i>友情链接
                        </h5>
                        <ul class="list">
                            <?php if(is_array($youqinglianjie)): $i = 0; $__LIST__ = $youqinglianjie;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$each_help_article): $mod = ($i % 2 );++$i;?>
                            <li>
                                <a href="<?php echo (isset($each_help_article['url']) && ($each_help_article['url'] !== '')?$each_help_article['url']:'--'); ?>" target="_blank"><?php echo htmlspecialchars($each_help_article['name']); ?></a>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="g-side">
                <div class="g-side-l">
                    <ul class="m-instruction-state f-clear">
                        <li><i class="ico ico-state-l ico-state-l-1"></i>100%公平公正公开</li>
                        <li><i class="ico ico-state-l ico-state-l-2"></i>100%正品保证</li>
                        <li><i class="ico ico-state-l ico-state-l-3"></i>100%权益保障</li>
                    </ul>
                </div>
                <div class="g-side-r">
                    <div class="m-instruction-yxCode">
                        <a href="<?php echo $down_url; ?>" ><img width="100%" src="<?php echo C('WEBSITE_QRCODE'); ?>"></a>
                        <!--<p >下载客户端</p>-->
                    </div>
                    <div class="m-instruction-service">
                        <p><?php echo C('WEB_SERVICE_TIME'); ?></p>
                        <p><?php echo C('WEB_SERVICE_TEL'); ?></p>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-copyright">
        <div class="g-wrap">
            <div class="m-copyright-logo">

            </div>
            <div class="m-copyright-txt">
                <p>&nbsp;</p>
                <p> <?php echo C('WEB_INC_INFO'); ?> | <?php echo C('WEBSITE_BEIAN'); ?></p>
            </div>
        </div>
    </div>
</div>
<?php if(isset($notify_list)): if(is_array($notify_list)): $i = 0; $__LIST__ = $notify_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
    <div style="display: none;" class="luck">
        <div class="luck-bg"></div>
        <div class="luck-main active">
            <button class="luck-close" data-id="<?php echo (isset($vo['w_id']) && ($vo['w_id'] !== '')?$vo['w_id']:''); ?>" title="关闭"><i class="iconfont"></i></button>
            <img src="__UPLOAD_DOMAIN__<?php echo $vo['goods_image']; ?>">
            <h6><?php echo $vo['goods_name']; ?></h6>
            <p>期号：<span><?php echo num_base_mask($vo['n_id']); ?></span><br>
                中奖号码：<span><?php echo num_base_mask($vo['luck_num'],1,0); ?></span></p>
            <?php if($vo['reward_type'] == 0): ?>
              <a href="<?php echo U('ucenter/luck_detail',['id'=>$vo['w_id']]); ?>" target="_blank" class="verify_btn">确认收货方式</a>
            <?php else: ?>
            <a href="<?php echo U('ucenter/luck_detail',['id'=>$vo['w_id']]); ?>" target="_blank" class="verify_btn">立即兑换</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    <script>
        // DUANG恭喜你中奖了
        $(".luck").show().find(".luck-main").addClass("active");
        $(document).on("click",".luck-close,.verify_btn",function(){
            var obj  =$(this);
            var url = $('#flag_trigger').val();
            common.ajax_post(url,{'id':obj.data('id')},true,function(rt){});
            $(this).parents(".luck").hide();
        });
    </script>
<?php endif; ?>
<!--全局url-->
<input type="hidden" id="login_out" value="<?php echo U('User/login_out'); ?>"><!--退出登录-->
<input type="hidden" id="get_user_login_info" value="<?php echo U('User/get_user_login_info'); ?>"><!--用户登录状态-->
<input type="hidden" id="show_login" value="<?php echo U('User/show_login'); ?>"><!--显示登录界面-->
<input type="hidden" id="cart_page_url" value="<?php echo U('Pay/cart'); ?>"><!--购物车页面-->
<input type="hidden" id="cart_list_div_url" value="<?php echo U('core/pay/cart_list_div'); ?>"><!--购物车ajax载入时候的页面-->
<input type="hidden" id="del_cart_by_nper_id_url" value="<?php echo U('core/pay/del_cart_by_nper_id'); ?>"><!--购物车ajax载入时候的页面-->
<input type="hidden" id="del_cart_by_ids_url" value="<?php echo U('core/pay/del_cart_by_ids'); ?>"><!--根据id删除购物车内容-->
<input type="hidden" id="update_cart_num_url" value="<?php echo U('core/pay/update_cart_num'); ?>"><!--更新购物车数量-->
<input type="hidden" id="create_order_url" value="<?php echo U('core/pay/create_order'); ?>"><!--生成订单-->

<!--登录相关-->
<input type="hidden" id="gee_test_url" value="<?php echo U('core/api/gee_test'); ?>"><!--极验证-->
<input type="hidden" id="user_login_do_url" value="<?php echo U('User/user_login_do'); ?>"><!--ajax登录-->
<input type="hidden" id="check_need_verify_url" value="<?php echo U('User/check_need_verify'); ?>"><!--需要验证码检测-->
<!--开奖相关-->
<input type="hidden" id="trigger_open_url" value="<?php echo U('core/Gdfc/trigger_open'); ?>"><!--全局开奖定时触发任务-->
<input type="hidden" id="collect_url" value="<?php echo U('core/Collect/index'); ?>"><!--彩票开奖定时触发任务-->
<input type="hidden" id="no_img_url" value="__yyg__/images/empty_img.jpg"><!--图片不存在时候显示的图片-->

<!--获取总参与人数-->
<input id="total_join_num" type="hidden" value="<?php  echo get_total_join() ?>">



<script type="text/javascript" src="__plugin__/layer/layer.js"></script>
<script type="text/javascript" src="__common__/js/common.js"></script>
<script type="text/javascript" src="__yyg__/js/public.js"></script>
<script type="text/javascript" src="__common__/js/geetest.js"></script>
<script type="text/javascript" src="__plugin__/bigautocomplete/jquery.bigautocomplete.js"></script>

<script type="text/javascript" src="__static__/js/goods/detail.js"></script>
<script type="text/javascript" src="__static__/js/img_modal.js"></script>
<script type="text/javascript" src="__yyg__/js/goods/detail.js"></script>
<script type="text/javascript" src="__static__/js/count_down.js"></script>

</body>

<div style="display: none;">

<!--WEBSITE_TONGJI_BD-->

<?php echo $site_config['website_tongji_bd']; ?>
<!--WEBSITE_TONGJI_CNZZ-->
<?php echo $site_config['website_tongji_cnzz']; ?>
</div>
</html>