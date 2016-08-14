<?php if (!defined('THINK_PATH')) exit(); /*a:9:{s:61:"D:\webroot\mengdie_yyg\app\www_tpl\default\ucenter\index.html";i:1468303703;s:57:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/base.html";i:1470730361;s:63:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/common_css.html";i:1468303703;s:61:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/tool_bar.html";i:1470730313;s:59:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/header.html";i:1468303703;s:60:"D:\webroot\mengdie_yyg\app\www_tpl\default\ucenter/_nav.html";i:1468303703;s:59:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/footer.html";i:1470726624;s:61:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/base_url.html";i:1468303703;s:62:"D:\webroot\mengdie_yyg\app\www_tpl\default\base/common_js.html";i:1468303703;}*/ ?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>我的首页</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo htmlspecialchars($site_config['website_desc']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($site_config['website_keyword']); ?>">
    <link rel="stylesheet" type="text/css" href="__static__/css/common.css" />
<link rel="stylesheet" type="text/css" href="__yyg__/css/common.css" />
<link rel="stylesheet" type="text/css" href="__plugin__/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="__plugin__/bigautocomplete/jquery.bigautocomplete.css" />

    
<link rel="stylesheet" type="text/css" href="__static__/css/usercenter.css" />
<link rel="stylesheet" type="text/css" href="__plugin__/webuploader/webuploader.css" />

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


<form id="form_content" autocomplete="off">
    <div class="g-body">
        <div class="m-user">
            <div class="g-wrap">
                <div class="m-user-frame-wraper">
                    <!--用户中心  菜单-->
                    
                <div class="m-user-frame-colNav">

                    <h3><a href="<?php echo dwz_filter('ucenter/index'); ?>">我的夺宝</a></h3>
                    <hr>
                    <ul pro="userFrameNav">
                        <li  <?php if(strtolower(ACTION_NAME) == 'deposer'): ?>class="active"<?php endif; ?>><a href="<?php echo dwz_filter('ucenter/deposer'); ?>" >夺宝记录 <strong pro="userDuobao_num" data-pos="userNav" class="txt-impt"></strong></a> </li>
                        <li <?php if(strtolower(ACTION_NAME) == 'luck'): ?>class="active"<?php endif; ?>><a href="<?php echo dwz_filter('ucenter/luck'); ?>" >幸运记录 <strong pro="userWin_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                        <!--<li><hr></li>-->
                        <!--<li <?php if(strtolower(ACTION_NAME) == 'buy'): ?>class="active"<?php endif; ?>>-->
                            <!--<a href="<?php echo U('ucenter/buy'); ?>" pro="userMall">购买记录 <strong pro="userMall_num" data-pos="userNav" class="txt-impt"></strong></a>-->
                        <!--</li>-->
                        <!--<li >-->
                            <!--<hr>-->
                        <!--</li>-->
                        <!--<li <?php if(strtolower(ACTION_NAME) == 'deposer'): ?>class="active"<?php endif; ?>>-->
                            <!--<a href="#" pro="userBonus">我的红包 <strong pro="userBonus_num" data-pos="userNav" class="txt-impt"></strong></a>-->
                        <!--</li>-->
                        <!--<li <?php if(strtolower(ACTION_NAME) == 'deposer'): ?>class="active"<?php endif; ?>>-->
                            <!--<a href="#" pro="userGems">我的宝石 <strong pro="userGems_num" data-pos="userNav" class="txt-impt"></strong></a>-->
                        <!--</li>-->
                        <!--<li <?php if(strtolower(ACTION_NAME) == 'deposer'): ?>class="active"<?php endif; ?>>-->
                            <!--<a href="#" pro="userWish">我的心愿单 <strong pro="userWish_num" data-pos="userNav" class="txt-impt"></strong></a>-->
                        <!--</li>-->
                        <li <?php if(strtolower(ACTION_NAME) == 'order'): ?>class="active"<?php endif; ?>>
                            <a href="<?php echo dwz_filter('ucenter/order'); ?>" pro="userShare">我的晒单 <strong pro="userShare_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                        <li >
                            <hr>
                        </li>
                        <li <?php if(strtolower(ACTION_NAME) == 'person'): ?>class="active"<?php endif; ?>>
                            <a href="<?php echo dwz_filter('ucenter/person'); ?>" pro="userProfile">个人资料 <strong pro="userProfile_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                        <li <?php if(strtolower(ACTION_NAME) == 'base'): ?>class="active"<?php endif; ?>>
                            <a href="<?php echo dwz_filter('ucenter/base'); ?>" pro="userSettings">基本设置 <strong pro="userSettings_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                        <li <?php if(strtolower(ACTION_NAME) == 'addr'): ?>class="active"<?php endif; ?>>
                            <a href="<?php echo dwz_filter('ucenter/addr'); ?>" pro="userAddress">收货地址 <strong pro="userAddress_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                        <li <?php if(strtolower(ACTION_NAME) == 'charge'): ?>class="active"<?php endif; ?>>
                            <a href="<?php echo dwz_filter('ucenter/charge'); ?>" pro="userChargeRecord">充值记录 <strong pro="userChargeRecord_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                        <?php if((empty($promote_spread) == false AND $promote_spread['status'] == 1) OR (empty($reg_spread) == false AND $reg_spread['status'] == 1)): ?>
                        <li <?php if(strtolower(ACTION_NAME) == 'promote'): ?>class="active"<?php endif; ?>>
                        <a href="<?php echo dwz_filter('ucenter/promote'); ?>" pro="userPromote">我的推广<strong pro="userPromote_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                        <?php endif; ?>
                        <li <?php if(strtolower(ACTION_NAME) == 'red_packet'): ?>class="active"<?php endif; ?>>
                        <a href="<?php echo dwz_filter('ucenter/red_packet'); ?>" pro="userChargeRecord">我的红包 <strong pro="userChargeRecord_num" data-pos="userNav" class="txt-impt"></strong></a>
                        </li>
                    </ul>
                </div>
                    <!--用户中心 main-->
                    <div class="m-user-frame-colMain ">
                        <div class="m-user-frame-content" pro="userFrameWraper">

                            <div module-launched="true" module-id="module-3" id="pro-view-2" module="user/index/Index">
                                <div class="m-user-frame-content-bd">
                                    <div class="m-user-frame-content-m">
                                        <div module-launched="true" module-id="module-8" id="pro-view-9" class="m-user-comm-infoBox f-clear" module="user/common/infoBox/InfoBox">
                                            <a pro="UC_avatarEdit" class="w-user-avatarEdit w-user-avatarEdit-160 m-user-comm-infoBox-face" href="javascript:void(0);">
                                                <img pro="avatar" src="__UPLOAD_DOMAIN__<?php echo (isset($info['img_path']) && ($info['img_path'] !== '')?$info['img_path']:'__AVATAR_DEFAULT__'); ?>" width="160" height="160">
                                            </a>
                                            <div class="m-user-comm-infoBox-cont">
                                                <ul>
                                                    <li class="item nickname">

                                                        <div pro="nameShow">
                                                            <span class="txt" pro="nickname"><?php echo (isset($info['nick_name']) && ($info['nick_name'] !== '')?$info['nick_name']:''); ?></span>
                                                            <a pro="UC_nicknameEdit" href="javascript:void(0)" class="optLink" style="display:none;">编辑</a>
                                                        </div>
                                                        <div class="nameEdit" pro="nameEdit" style="display:none"></div>

                                                    </li>
                                                    <li class="item"><span class="txt">ID：<strong><?php echo (isset($info['id']) && ($info['id'] !== '')?$info['id']:''); ?></strong></span></li>
                                                    <li class="item">
                                                        <span class="txt">账户余额：<strong class="txt-impt"><?php echo (isset($info['money']) && ($info['money'] !== '')?$info['money']:0); echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?></strong></span>
                                                        <a pro="UC_goRecharge" href="<?php echo dwz_filter('pay/recharge'); ?>" class="w-button w-button-s"><span>充值</span></a>
                                                    </li>
                                                    <!-- <li class="item">
                                                        <span class="txt">积分：<strong class="txt-impt"><?php echo (isset($info['score']) && ($info['score'] !== '')?$info['score']:0); ?>分</strong></span>
                                                        <a id="trans_to_btn"  class="w-button w-button-s"><span>转为<?php echo C('MONEY_NAME'); ?></span></a>
                                                        <span class="notice_1s">(<?php echo (isset($c_score) && ($c_score !== '')?$c_score:''); ?>积分=<?php echo (isset($c_money) && ($c_money !== '')?$c_money:''); echo C('MONEY_NAME'); ?>)</span>
                                                    </li> -->
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="m-user-index-duobaoRecord">
                                            <div class="m-user-comm-title m-user-comm-titleHasBdr">
                                                <a class="ext" href="<?php echo dwz_filter('deposer'); ?>">查看更多记录</a>
                                                <h3 class="title">我最近的夺宝</h3>
                                            </div>

                                            <div module-launched="true" module-id="module-11" tag="moduleRecord" module="duobaoRecord/DuobaoRecord" cid="78203681" status="9" region="4" full-list="false" pagesize="6" class="m-user-duobao">

                                                <div id="pro-view-34">
                                                    <table class="w-table m-user-comm-table" pro="listHead">
                                                        <thead
                                                        <tr>
                                                            <th class="col-info">商品信息</th>
                                                            <th class="col-period">期号</th>
                                                            <th class="col-opt">操作</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                    <div pro="duobaoList" class="duobaoList duobaoList-simple">
                                                        <?php if(empty($order_list)): ?>
                                                        <div id="pro-view-0">
                                                            <div pro="duobaoList" class="duobaoList m-user-duobao-multi">
                                                                <div id="pro-view-49" class="m-user-comm-empty"><b class="ico ico-face-sad"></b>
                                                                    <div class="i-desc">您一年内没有多期夺宝记录哦~</div>
                                                                    <div class="i-opt"><a href="/" class="w-button w-button-main w-button-xl">马上去逛逛</a></div>
                                                                </div>
                                                            </div>
                                                            <div pro="pager" class="pager"></div>
                                                        </div>
                                                        <?php else: if(is_array($order_list)): $i = 0; $__LIST__ = $order_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;switch($vo['n_status']): case "1": ?>
                                                        <table id="pro-view-<?php echo $vo['o_id']; ?>" class="m-user-comm-table">
                                                            <tbody>
                                                            <tr>
                                                                <td class="col-info">
                                                                    <div class="w-goods w-goods-l w-goods-hasLeftPic">
                                                                        <div class="w-goods-pic">
                                                                            <a target="_blank" href="<?php echo dwz_filter('goods/detail',['id'=>$vo['g_id'].'-'.$vo['n_id']]); ?>">
                                                                                <img src="__UPLOAD_DOMAIN__<?php echo $vo['g_image']; ?>" alt="<?php echo htmlspecialchars($vo['g_name']); ?>" width="74" height="74">
                                                                            </a>

                                                                        </div>
                                                                        <p class="w-goods-title f-txtabb">
                                                                            <a title="<?php echo htmlspecialchars($vo['g_name']); ?>" target="_blank" href="<?php echo dwz_filter('goods/detail',['id'=>$vo['g_id'].'-'.$vo['n_id']]); ?>"><?php echo htmlspecialchars($vo['g_name']); ?></a>
                                                                        </p>
                                                                        <p class="w-goods-price">总需：<?php echo $vo['n_sum']; ?>人次</p>
                                                                        <div class="w-progressBar">
                                                                            <p class="w-progressBar-wrap">
                                                                                <span class="w-progressBar-bar" style="width:<?php echo $vo['n_percent']; ?>%"></span>
                                                                            </p>
                                                                            <ul class="w-progressBar-txt f-clear">
                                                                                <li class="w-progressBar-txt-l">已完成<?php echo $vo['n_percent']; ?>%，剩余<span class="txt-residue"><?php echo $vo['n_remain']; ?></span></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="col-period"><?php echo num_base_mask($vo['n_id']); ?></td>
                                                                <td class="col-joinNum"><?php echo $vo['num']; ?>人次</td>
                                                                <td class="col-status">

                                                                    <span class="txt-suc">正在进行</span>


                                                                </td>
                                                                <td class="col-opt">
                                                                    <a class="w-button w-button-main" href="<?php echo dwz_filter('goods/jump_to_goods_buying',['gid'=>$vo['g_id']]); ?>" target="_blank"><span>追加人次</span></a>
                                                                    <p><a href="javascript:void(0)" pro="viewCode" data-luckcode="" class="see_luck_num" data-uid="<?php echo login_id(); ?>" data-nper="<?php echo $vo['n_id']; ?>">查看夺宝号码</a></p>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <?php break; case "2": ?>
                                                        <table id="pro-view-36" class="m-user-comm-table">
                                                            <tbody>
                                                            <tr>
                                                                <td class="col-info">
                                                                    <div class="w-goods w-goods-l w-goods-hasLeftPic">
                                                                        <div class="w-goods-pic">
                                                                            <a target="_blank" href="<?php echo dwz_filter('goods/detail',['id'=>$vo['g_id'].'-'.$vo['n_id']]); ?>">
                                                                                <img src="__UPLOAD_DOMAIN__<?php echo $vo['g_image']; ?>" alt="<?php echo htmlspecialchars($vo['g_name']); ?>" width="74" height="74">
                                                                            </a>
                                                                        </div>
                                                                        <p class="w-goods-title f-txtabb">
                                                                            <a title="<?php echo htmlspecialchars($vo['g_name']); ?>" target="_blank" href="<?php echo dwz_filter('goods/detail',['id'=>$vo['g_id'].'-'.$vo['n_id']]); ?>"><?php echo htmlspecialchars($vo['g_name']); ?></a>
                                                                        </p>
                                                                        <p class="w-goods-price">总需：<?php echo $vo['n_sum']; ?>人次</p>
                                                                        <div class="w-progressBar">
                                                                            <p class="w-progressBar-wrap">
                                                                                <span class="w-progressBar-bar" style="width:100%"></span>
                                                                            </p>
                                                                            <ul class="w-progressBar-txt f-clear">
                                                                                <li class="w-progressBar-txt-l">已完成100%，剩余<span class="txt-residue">0</span></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="col-period"><?php echo num_base_mask($vo['n_id']); ?></td>
                                                                <td class="col-joinNum"><?php echo $vo['num']; ?>人次</td>
                                                                <td class="col-status">
                                                                    <span class="txt-wait">等待揭晓</span>
                                                                </td>
                                                                <td class="col-opt">
                                                                    <a class="w-button w-button-main" href="<?php echo dwz_filter('goods/jump_to_goods_buying',['gid'=>$vo['g_id']]); ?>" target="_blank"><span>追加人次</span></a>
                                                                    <p><a href="javascript:void(0)" pro="viewCode" data-luckcode="" class="see_luck_num" data-uid="<?php echo login_id(); ?>" data-nper="<?php echo $vo['n_id']; ?>">查看夺宝号码</a></p>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <?php break; case "3": ?>
                                                        <table id="pro-view-21" class="m-user-comm-table">
                                                            <tbody>
                                                            <tr>
                                                                <td class="col-info">
                                                                    <div class="w-goods w-goods-l w-goods-hasLeftPic">
                                                                        <div class="w-goods-pic">
                                                                            <a target="_blank" href="<?php echo dwz_filter('goods/detail',['id'=>$vo['g_id'].'-'.$vo['n_id']]); ?>">
                                                                                <img src="__UPLOAD_DOMAIN__<?php echo $vo['g_image']; ?>" alt="<?php echo htmlspecialchars($vo['g_name']); ?>" width="74" height="74">
                                                                            </a>
                                                                        </div>
                                                                        <p class="w-goods-title f-txtabb">
                                                                            <a title="<?php echo htmlspecialchars($vo['g_name']); ?>" target="_blank" href="<?php echo dwz_filter('goods/detail',['id'=>$vo['g_id'].'-'.$vo['n_id']]); ?>"><?php echo htmlspecialchars($vo['g_name']); ?></a>
                                                                        </p>
                                                                        <p class="w-goods-price">总需：<?php echo $vo['n_sum']; ?>人次</p>
                                                                        <div class="winner">
                                                                            <div class="name">获得者：<a href="<?php echo dwz_filter('ta/index',['uid'=>$vo['luck_uid']]); ?>" title="<?php echo $vo['nick_name']; ?>"><?php echo $vo['nick_name']; ?></a>
                                                                                （本期参与<strong class="txt-dark"><?php echo isset($num[$vo['n_id']]) ? $num[$vo['n_id']] : '暂无数据'; ?></strong>人次）</div>
                                                                            <div class="code">幸运代码：<strong class="txt-impt"><?php echo num_base_mask($vo['luck_num'],1); ?></strong></div>
                                                                            <div class="time_">揭晓时间：<?php echo microtime_format($vo['open_time'],3,'Y-m-d H:i:s'); ?></div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="col-period"><?php echo num_base_mask($vo['n_id']); ?></td>
                                                                <td class="col-joinNum"><?php echo $vo['num']; ?>人次</td>
                                                                <td class="col-status">
                                                                    <span>已揭晓</span>
                                                                </td>
                                                                <td class="col-opt">
                                                                    <a class="w-button w-button-main" href="<?php echo dwz_filter('goods/jump_to_goods_buying',['gid'=>$vo['g_id']]); ?>" target="_blank"><span>追加人次</span></a>
                                                                    <p><a href="javascript:void(0)" pro="viewCode" data-luckcode="" class="see_luck_num" data-uid="<?php echo login_id(); ?>" data-nper="<?php echo $vo['n_id']; ?>">查看夺宝号码</a></p>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <?php break; endswitch; endforeach; endif; else: echo "" ;endif; endif; ?>
                                                    </div>
                                                    <div pro="pager" class="pager"></div>
                                                    <div pro="limitTips" class="limitTips" style="display:none"></div>
                                                </div>
                                            </div>
                                            <div class="lineWhite"></div>
                                        </div>
                                    </div>
                                    <div class="m-user-frame-content-r">
                                        <div class="m-user-frame-extension">
                                            <a href="/" target="_blank"><img src="http://res.126.net/p/dbqb/resupload/onlinepath/2016/1/11/6/eb6117c05b36eb22f6195a9065161486.jpg" alt="在这里，惊喜只卖1元"></a>
                                        </div>
                                        <div module-launched="true" module-id="module-10" tag="moduleHistory" module="user/viewhistory/ViewHistory" class="m-user-frame-history">

                                            <!--<div id="pro-view-22"><h3 class="mTitle">我最近浏览的商品</h3>-->
                                                <!--<div class="mCont">-->
                                                    <!--<ul class="f-clear" pro="entry">-->
                                                        <!--<li id="pro-view-23" class="item"><a href="/detail/117.html" target="_blank" title="Apple Mac Pro ME253CH/A"><img src="http://onegoods.nosdn.127.net/goods/117/8595f30e9a1e58f63a9b386bbca2ee46.png"-->
                                                                                                                                                                          <!--alt="Apple Mac Pro ME253CH/A"></a></li>-->
                                                        <!--<li id="pro-view-24" class="item"><a href="/detail/1899.html" target="_blank" title="绝对 Absolute 伏特加（原味） 700ml/瓶"><img-->
                                                                <!--src="http://onegoods.nosdn.127.net/goods/1899/3b2718a127804e1b7d99a41295ada2fc.jpg" alt="绝对 Absolute 伏特加（原味） 700ml/瓶"></a></li>-->
                                                        <!--<li id="pro-view-25" class="item"><a href="/detail/1859.html" target="_blank" title="气味图书馆 花觉香水礼盒 女士淡香水小样"><img src="http://onegoods.nosdn.127.net/goods/1859/ada9d4b556a934c8fc414a3613078bed.jpg"-->
                                                                                                                                                                        <!--alt="气味图书馆 花觉香水礼盒 女士淡香水小样"></a></li>-->
                                                        <!--<li id="pro-view-26" class="item"><a href="/detail/552.html" target="_blank" title="顶诺家庭牛排套餐10片装"><img src="http://onegoods.nosdn.127.net/goods/552/068b10992f05fcbf19c42d1bda3e3e6e.png"-->
                                                                                                                                                               <!--alt="顶诺家庭牛排套餐10片装"></a></li>-->
                                                        <!--<li id="pro-view-27" class="item"><a href="/detail/1880.html" target="_blank" title="气味图书馆 车载固体香水 自然系列 香薰罐头礼盒"><img src="http://onegoods.nosdn.127.net/goods/1880/8bffbd9d3882a81b552b9595da7f83b0.jpg"-->
                                                                                                                                                                            <!--alt="气味图书馆 车载固体香水 自然系列 香薰罐头礼盒"></a></li>-->
                                                        <!--<li id="pro-view-28" class="item"><a href="/detail/216.html" target="_blank" title="飞利浦（Philips）充电式声波震动牙刷"><img src="http://onegoods.nosdn.127.net/goods/216/9e6134565e00edf067bd12002713e7d2.png"-->
                                                                                                                                                                        <!--alt="飞利浦（Philips）充电式声波震动牙刷"></a></li>-->
                                                        <!--<li id="pro-view-29" class="item"><a href="/detail/1413.html" target="_blank" title="三星 SAMSUNG 曲面电视 UA65JU6800JXXZ 65英寸 超高清4K"><img-->
                                                                <!--src="http://onegoods.nosdn.127.net/goods/1413/ffbefceee1814adfbd45f636b5c7a74c.png" alt="三星 SAMSUNG 曲面电视 UA65JU6800JXXZ 65英寸 超高清4K"></a></li>-->
                                                        <!--<li id="pro-view-30" class="item"><a href="/detail/1093.html" target="_blank" title="Apple iPhone6s Plus 128G 颜色随机"><img-->
                                                                <!--src="http://onegoods.nosdn.127.net/goods/1093/1613ccae60869faab080f8503580cb9e.png" alt="Apple iPhone6s Plus 128G 颜色随机"></a></li>-->
                                                        <!--<li id="pro-view-31" class="item"><a href="/detail/348.html" target="_blank" title="Apple Watch Sport 38毫米 铝金属表壳 运动表带"><img-->
                                                                <!--src="http://onegoods.nosdn.127.net/goods/348/edbde701b205bef7a89a92cce674e797.png" alt="Apple Watch Sport 38毫米 铝金属表壳 运动表带"></a></li>-->
                                                    <!--</ul>-->
                                                <!--</div>-->
                                            <!--</div>-->
                                        </div>

                                    </div>
                                    <div class="f-clear"></div>

                                    <div module-launched="true" module-id="module-7" tag="moduleRecommend" module="goodsRecommend/GoodsRecommend">
                                        <div id="pro-view-127" class="w-goodsRecommend">
                                            <!--<div class="w-hd"><h3 class="w-hd-title">推荐夺宝</h3><span>根据你的浏览记录推荐的商品</span><a pro="pswitch" class="w-hd-refresh" href="javascript:void(0);"><i class="ico ico-refresh"></i>换一批</a></div>-->
                                            <!--<div class="w-recommend-bd">-->
                                                <!--<ul class="w-goodsList f-clear" pro="goodsList">-->
                                                    <!--<li id="pro-view-128" class="w-goodsList-item">-->
                                                        <!--<div class="w-goods w-goods-brief">-->
                                                            <!--<div class="w-goods-pic"><a href="/detail/1801.html" title="1箱8袋 | 百草味 在一起礼盒 坚果干果零食大礼包" target="_blank"><img alt="1箱8袋 | 百草味 在一起礼盒 坚果干果零食大礼包"-->
                                                                                                                                                                         <!--src="http://onegoods.nosdn.127.net/goods/1801/aa87cbcde445de0f850e549543f30ec7.jpg"-->
                                                                                                                                                                         <!--width="200" height="200"></a></div>-->
                                                            <!--<p class="w-goods-title f-txtabb"><a title="1箱8袋 | 百草味 在一起礼盒 坚果干果零食大礼包" href="/detail/1801.html" target="_blank">1箱8袋 | 百草味 在一起礼盒 坚果干果零食大礼包</a></p>-->
                                                            <!--<p class="w-goods-price">总需：159人次</p></div>-->
                                                    <!--</li>-->
                                                    <!--<li id="pro-view-129" class="w-goodsList-item">-->
                                                        <!--<div class="w-goods w-goods-brief">-->
                                                            <!--<div class="w-goods-pic"><a href="/detail/1723.html" title="巧克力与爱牌 Chocolate and Love 黑巧克力精选礼盒 400g" target="_blank"><img alt="巧克力与爱牌 Chocolate and Love 黑巧克力精选礼盒 400g"-->
                                                                                                                                                                                      <!--src="http://onegoods.nosdn.127.net/goods/1723/1bad8f83a381d45c4ac350e8cbd89bf4.jpg"-->
                                                                                                                                                                                      <!--width="200" height="200"></a></div>-->
                                                            <!--<p class="w-goods-title f-txtabb"><a title="巧克力与爱牌 Chocolate and Love 黑巧克力精选礼盒 400g" href="/detail/1723.html" target="_blank">巧克力与爱牌 Chocolate and Love 黑巧克力精选礼盒 400g</a></p>-->
                                                            <!--<p class="w-goods-price">总需：288人次</p></div>-->
                                                    <!--</li>-->
                                                    <!--<li id="pro-view-130" class="w-goodsList-item">-->
                                                        <!--<div class="w-goods w-goods-brief">-->
                                                            <!--<div class="w-goods-pic"><a href="/detail/1889.html" title="海购商品 MCM 男士短款钱包 棕色" target="_blank"><img alt="海购商品 MCM 男士短款钱包 棕色"-->
                                                                                                                                                                 <!--src="http://onegoods.nosdn.127.net/goods/k/37569/be3078e9e51542757f93e4ebbcdc3b00.jpg"-->
                                                                                                                                                                 <!--width="200" height="200"></a></div>-->
                                                            <!--<p class="w-goods-title f-txtabb"><a title="海购商品 MCM 男士短款钱包 棕色" href="/detail/1889.html" target="_blank">海购商品 MCM 男士短款钱包 棕色</a></p>-->
                                                            <!--<p class="w-goods-price">总需：1650人次</p></div>-->
                                                    <!--</li>-->
                                                    <!--<li id="pro-view-131" class="w-goodsList-item">-->
                                                        <!--<div class="w-goods w-goods-brief">-->
                                                            <!--<div class="w-goods-pic"><a href="/detail/1705.html" title="1箱6盒装 | 肯迪雅 candia 全脂纯牛奶 1L/盒" target="_blank"><img alt="1箱6盒装 | 肯迪雅 candia 全脂纯牛奶 1L/盒"-->
                                                                                                                                                                            <!--src="http://onegoods.nosdn.127.net/goods/1705/f4430988da5110893ddf89e5bce76997.jpg"-->
                                                                                                                                                                            <!--width="200" height="200"></a></div>-->
                                                            <!--<p class="w-goods-title f-txtabb"><a title="1箱6盒装 | 肯迪雅 candia 全脂纯牛奶 1L/盒" href="/detail/1705.html" target="_blank">1箱6盒装 | 肯迪雅 candia 全脂纯牛奶 1L/盒</a></p>-->
                                                            <!--<p class="w-goods-price">总需：150人次</p></div>-->
                                                    <!--</li>-->
                                                    <!--<li id="pro-view-132" class="w-goodsList-item">-->
                                                        <!--<div class="w-goods w-goods-brief">-->
                                                            <!--<div class="w-goods-pic"><a href="/detail/1862.html" title="Calvin Klein 卡文克莱 女士Ladies系列 K2G23620 女士石英表" target="_blank"><img alt="Calvin Klein 卡文克莱 女士Ladies系列 K2G23620 女士石英表"-->
                                                                                                                                                                                          <!--src="http://onegoods.nosdn.127.net/goods/1862/132eedf5ce5d235c4f2ed29f7e61ba92.jpg"-->
                                                                                                                                                                                          <!--width="200" height="200"></a></div>-->
                                                            <!--<p class="w-goods-title f-txtabb"><a title="Calvin Klein 卡文克莱 女士Ladies系列 K2G23620 女士石英表" href="/detail/1862.html" target="_blank">Calvin Klein 卡文克莱 女士Ladies系列 K2G23620 女士石英表</a></p>-->
                                                            <!--<p class="w-goods-price">总需：1899人次</p></div>-->
                                                    <!--</li>-->
                                                <!--</ul>-->
                                            <!--</div>-->
                                        </div>
                                    </div>


                                </div>

                            </div>


                            <script type="text/template" id="tplItemDuobaoNormal">
                                <table class="m-user-comm-table">
                                    <tbody>
                                    <tr
                                    {{#isMyWin}} class="getWin"{{/isMyWin}}>
                                    <td class="col-info">
                                        <div class="w-goods w-goods-l w-goods-hasLeftPic">
                                            <div class="w-goods-pic">
                                                <a target="_blank" href="{{goodsUrl}}">
                                                    <img src="{{goods.smallPic}}" alt="{{goods.gname}}" width="74" height="74">
                                                </a>
                                                {{#isMyWin}}<b class="ico ico-winner"></b>{{/isMyWin}}
                                            </div>
                                            <p class="w-goods-title f-txtabb">
                                                {{#isTen}}<span class="type type-ten">十元专区</span>{{/isTen}}
                                                {{#isMulti}}<span class="type type-hd">多期参与</span>{{/isMulti}}
                                                {{#isFree}}<span class="type type-free">赠币专区</span>{{/isFree}}
                                                {{#isLimit}}<span class="type type-limit">限时夺宝</span>{{/isLimit}}
                                                <a title="{{goods.gname}}" target="_blank" href="{{goodsUrl}}">{{goods.gname}}</a>
                                            </p>
                                            <p class="w-goods-price">总需：{{goods.totalDesc}}</p>

                                            {{#isShowPreprogress}}
                                            <div class="w-progressBar">
                                                <p class="w-progressBar-wrap">
                                                    <span class="w-progressBar-bar" style="width:{{percent}}"></span>
                                                </p>
                                                <ul class="w-progressBar-txt f-clear">
                                                    <li class="w-progressBar-txt-l">已完成{{percent}}，剩余<span class="txt-residue">{{remain}}</span></li>
                                                </ul>
                                            </div>
                                            {{/isShowPreprogress}}


                                            {{^isShowPreprogress}}
                                            <div class="winner">
                                                <div class="name">获得者：<a href="/user/index.do?cid={{ownerId}}" title="{{ownerName}}">{{ownerName}}</a>（本期参与<strong class="txt-dark">{{ownerTotal}}</strong>人次）</div>
                                                <div class="code">幸运代码：<strong class="txt-impt">{{luckyCode}}</strong></div>
                                                <div class="time">揭晓时间：{{calcTime}}</div>
                                            </div>
                                            {{/isShowPreprogress}}

                                        </div>

                                    </td>
                                    <td class="col-period">{{period}}</td>
                                    <td class="col-joinNum">{{num}}人次</td>
                                    <td class="col-status">
                                        {{#isExpire}}<span>已结束</span>{{/isExpire}}
                                        {{#isPerioding}}<span class="txt-suc">正在进行</span>{{/isPerioding}}
                                        {{#iswillReveal}}<span class="txt-wait">等待揭晓</span>{{/iswillReveal}}
                                        {{#isRevealed}}<span>已揭晓</span>{{/isRevealed}}
                                    </td>
                                    <td class="col-opt">
                                        <p>我参与<span class="txt-impt">{{num}}</span>人次 <a href="javascript:void(0)" pro="viewCode" data-gid="{{goods.gid}}" data-period="{{period}}" data-cid="78203681" data-luckCode="{{luckyCode}}">查看</a></p>

                                        {{#saleOff}}<a class="w-button w-button-disabled w-button-main" href="javascript:void(0)"><span>暂无最新</span></a>{{/saleOff}}
                                        {{^saleOff}}
                                        {{#isPerioding}}
                                        <a class="w-button w-button-main" href="{{goods.url}}" target="_blank"><span>追加人次</span></a>
                                        {{/isPerioding}}
                                        {{^isPerioding}}
                                        <a class="w-button w-button-main" href="{{goods.url}}" target="_blank"><span>参与最新</span></a>
                                        {{/isPerioding}}
                                        {{/saleOff}}
                                    </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </script>


                            <script type="text/template" id="tplItemDuobaoOrder">

                                <div class="m-user-duobao-order-title">
                                    <div class="orderNo">订单号：{{code}} &nbsp;&nbsp;&nbsp;&nbsp; 夺宝时间：{{time}}</div>
                                    <div class="opt">
                                        {{#isPayAble}}
                                        <a class="w-button w-button-main  w-button-s" href="{{payUrl}}"><span>支付订单</span></a>
                                        {{/isPayAble}}&nbsp;
                                    </div>
                                    <div class="price">总价：<span class="txt-impt">{{price}}<?php echo C('MONEY_NAME'); ?></span></div>
                                </div>


                                {{#multi}}
                                <table class="m-user-comm-table multi">
                                    <tbody>
                                    <tr>
                                        <td class="col-info">
                                            <div class="w-goods w-goods-l w-goods-hasLeftPic">
                                                <div class="w-goods-pic">
                                                    <a target="_blank" href="{{goods.url}}">
                                                        <img src="{{goods.smallPic}}" alt="{{goods.gname}}" width="74" height="74">
                                                    </a>
                                                </div>
                                                <p class="w-goods-title f-txtabb">
                                                    {{#isTen}}<span class="type type-ten">十元专区</span>{{/isTen}}
                                                    <span class="type type-hd">多期参与</span>
                                                    <a title="{{goods.gname}}" target="_blank" href="{{goods.url}}">{{goods.gname}}</a>
                                                </p>
                                                <p class="w-goods-price">总需：{{goods.totalDesc}}</p>
                                            </div>
                                        </td>

                                        <td class="col-periodNum">
                                            {{periodNum}}期
                                            {{#isPerioding}}<p>（剩余{{remainNum}}期）</p>{{/isPerioding}}
                                        </td>
                                        <td class="col-joinNum">
                                            {{duobaoNum}}人次
                                        </td>
                                        <td class="col-status">
                                            {{#isExpire}}<span>已结束</span>{{/isExpire}}
                                            {{#isPerioding}}<span class="txt-suc">正在进行</span>{{/isPerioding}}
                                            {{#isCancel}}<span>已取消</span>{{/isCancel}}
                                        </td>
                                        <td class="col-opt">&nbsp;</td>
                                    </tr>
                                    </tbody>
                                </table>
                                {{/multi}}

                                {{#record}}
                                <table class="m-user-comm-table normal">
                                    <tbody>
                                    <tr
                                    {{#isRevealed}} class="isRevealed"{{/isRevealed}}>
                                    <td class="col-info">
                                        <div class="w-goods w-goods-l w-goods-hasLeftPic">
                                            <div class="w-goods-pic">
                                                <a target="_blank" href="{{goods.url}}">
                                                    <img src="{{goods.smallPic}}" alt="{{goods.gname}}" width="74" height="74">
                                                </a>
                                                {{#isMyWin}}<b class="ico ico-winner"></b>{{/isMyWin}}
                                            </div>
                                            <p class="w-goods-title f-txtabb">
                                                {{#isTen}}<span class="type type-ten">十元专区</span>{{/isTen}}
                                                {{#isMulti}}<span class="type type-hd">多期参与</span>{{/isMulti}}
                                                {{#isFree}}<span class="type type-free">赠币专区</span>{{/isFree}}
                                                {{#isLimit}}<span class="type type-limit">限时夺宝</span>{{/isLimit}}
                                                <a title="{{goods.gname}}" target="_blank" href="{{goods.url}}">{{goods.gname}}</a>
                                            </p>
                                            <p class="w-goods-price">总需：{{goods.totalDesc}}</p>

                                            {{#isShowPreprogress}}
                                            <div class="w-progressBar">
                                                <p class="w-progressBar-wrap">
                                                    <span class="w-progressBar-bar" style="width:{{percent}}"></span>
                                                </p>
                                                <ul class="w-progressBar-txt f-clear">
                                                    <li class="w-progressBar-txt-l">已完成{{percent}}，剩余<span class="txt-residue">{{remain}}</span></li>
                                                </ul>
                                            </div>
                                            {{/isShowPreprogress}}


                                            {{^isShowPreprogress}}
                                            <div class="winner">
                                                <div class="name">获得者：<a href="/user/index.do?cid={{ownerId}}" title="{{ownerName}}">{{ownerName}}</a>（本期参与<strong class="txt-dark">{{ownerTotal}}</strong>人次）</div>
                                                <div class="code">幸运代码：<strong class="txt-impt">{{luckyCode}}</strong></div>
                                                <div class="time">揭晓时间：{{calcTime}}</div>
                                            </div>
                                            {{/isShowPreprogress}}

                                        </div>

                                    </td>
                                    <td class="col-period">{{period}}</td>
                                    <td class="col-joinNum">{{num}}人次</td>
                                    <td class="col-status">
                                        {{#isExpire}}<span>已结束</span>{{/isExpire}}
                                        {{#isPerioding}}<span class="txt-suc">正在进行</span>{{/isPerioding}}
                                        {{#iswillReveal}}<span class="txt-wait">等待揭晓</span>{{/iswillReveal}}
                                        {{#isRevealed}}<span>已揭晓</span>{{/isRevealed}}
                                    </td>
                                    <td class="col-opt">&nbsp;</td>
                                    </tr>
                                    </tbody>
                                </table>
                                {{/record}}
                                <div style="margin-bottom:20px;"></div>
                            </script>


                            <script type="text/template" id="tplItemDuobaoMulti">
                                <table class="m-user-comm-table">
                                    <tbody>
                                    <tr>
                                        <td class="col-info">
                                            <div class="w-goods w-goods-l w-goods-hasLeftPic">
                                                <div class="w-goods-pic">
                                                    <a target="_blank" href="{{goods.url}}">
                                                        <img src="{{goods.smallPic}}" alt="{{goods.gname}}" width="74" height="74">
                                                    </a>
                                                </div>
                                                <p class="w-goods-title f-txtabb">
                                                    {{#isTen}}<span class="type type-ten">十元专区</span>{{/isTen}}
                                                    <a title="{{goods.gname}}" target="_blank" href="{{goods.url}}">{{goods.gname}}</a>
                                                </p>
                                                <p class="w-goods-price">总需：{{goods.totalDesc}}</p>
                                            </div>
                                        </td>
                                        <td class="col-time">{{time}}</td>
                                        <td class="col-joinNum">{{duobaoNum}}人次</td>
                                        <td class="col-periodNum">
                                            {{periodNum}}期
                                            {{#isPerioding}}<p>（剩余{{remainNum}}期）</p>{{/isPerioding}}
                                        </td>
                                        <td class="col-status">
                                            {{#isExpire}}<span>已结束</span>{{/isExpire}}
                                            {{#isPerioding}}<span class="txt-suc">正在进行</span>{{/isPerioding}}
                                            {{#isCancel}}<span>已取消</span>{{/isCancel}}
                                        </td>
                                        <td class="col-opt"><a target="_blank" href="/user/multiDetail.do?gid={{goods.gid}}&mid={{id}}&cid=78203681">查看详情</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </script>


                        </div>
                    </div>
                    <div class="m-user-frame-clear"></div>
                </div>
            </div>
        </div>
    </div>
    <input name="geetest_challenge" type="hidden">
    <input name="geetest_validate" type="hidden">
    <input name="geetest_seccode" type="hidden">
</form>

<div class="m-trans-hd">
    <div class="m-user-comm-alert_z1">
        <h6>转为<?php echo C('MONEY_NAME'); ?></h6>
        <div class="alert_z1_content">
            <div>
                <span>我的积分</span>
                <p><span><?php echo (isset($info['score']) && ($info['score'] !== '')?$info['score']:0); ?></span> 分</p>
                <a href="javascript:;" class="all_buy">全部转出</a>
            </div>
            <div>
                <span>兑换积分</span>
                <input type="text" class="money" value="">
            </div>
            <div>
                <span><?php echo C('MONEY_NAME'); ?></span>
                <span class="sort_money">0</span>
            </div>
            <div>
                <span>账户密码</span>
                <input type="password" class="password" value="">
            </div>
        </div>
        <div class="alert_z1_buttons">
            <a href="javascript:;" class="alert_z1_buttons_n1 layui-layer-close1">取 消</a>
            <a href="javascript:;" class="alert_z1_buttons_n2 transensure">确认转出</a>
        </div>
        <!--<a href="javascript:;" class="alert_z1_buttons_cancle">×</a>-->
    </div>
</div>
<div class="m-trans-hd">
    <div class="m-user-comm-alert_z1 m-user-comm-alert_z2">
        <div class="comm-alert_qb"></div>
        <p>您的余额已成功转为<?php echo C('MONEY_NAME'); ?>!</p>
        <a href="/index.php/yyg/ucenter/promote.html" class="alert_z1_buttons_n3">查看余额</a>
        <!--<a href="javascript:;" class="alert_z1_buttons_cancle">×</a>-->
    </div>
</div>

<input type="hidden" id="open_cut_box" value="<?php echo U('cut_box'); ?>">
<input type="hidden" id="swf_path" value="__plugin__/webuploader/uploader.swf"><!--swf-->
<input type="hidden" id="server_path" value="<?php echo U('core/upload/upload_img'); ?>"><!--上传-->

<input type="hidden" id="save_nick_name" value="<?php echo U('user/save_nick_name'); ?>"><!--保存用户昵称-->

<input type="hidden" id="get_phone_code" value="<?php echo U('core/api/get_phone_code'); ?>"><!--获取验证码-->
<input type="hidden" id="gee_test" value="<?php echo U('core/api/gee_test'); ?>"><!--极验证-->
<input type="hidden" id="chk_countdown" value="<?php echo U('User/chk_countdown'); ?>"><!--检查倒计时-->
<input type="hidden" id="write_countdown" value="<?php echo U('User/write_countdown'); ?>"><!--写倒计时-->
<input type="hidden" id="save_phone" value="<?php echo U('User/save_phone'); ?>"><!--写倒计时-->

<input type="hidden" id="get_login_user_img" value="<?php echo U('ucenter/get_login_user_img'); ?>"><!--获取图片信息-->
<input type="hidden" id="save_login_user_img" value="<?php echo U('ucenter/save_login_user_img'); ?>"><!--保存用户头像信息-->
<input type="hidden" id="see_luck_num" value="<?php echo U('goods/see_luck_num'); ?>"><!--获取用户购买幸运数字-->
<input type="hidden" id="money_name" value="<?php echo C('MONEY_NAME'); ?>">
<input type="hidden" id="scorce" value="<?php echo (isset($info['score']) && ($info['score'] !== '')?$info['score']:0); ?>">
<input type="hidden" id="transensure_url" value="<?php echo U('transensure'); ?>">

<input type="hidden" id="c_score" value="<?php echo (isset($c_score) && ($c_score !== '')?$c_score:''); ?>">
<input type="hidden" id="c_money" value="<?php echo (isset($c_money) && ($c_money !== '')?$c_money:''); ?>">
<input type="hidden" id="my_score" value="<?php echo (isset($info['score']) && ($info['score'] !== '')?$info['score']:0); ?>">



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

<script type="text/javascript" src="__plugin__/webuploader/webuploader.min.js"></script>
<script type="text/javascript" src="__yyg__/js/ucenter/person.js"></script>
<script type="text/javascript" src="__static__/js/ucenter/view_num.js"></script>

</body>

<div style="display: none;">

<!--WEBSITE_TONGJI_BD-->

<?php echo $site_config['website_tongji_bd']; ?>
<!--WEBSITE_TONGJI_CNZZ-->
<?php echo $site_config['website_tongji_cnzz']; ?>
</div>
</html>