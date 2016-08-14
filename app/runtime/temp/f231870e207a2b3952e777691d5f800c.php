<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:56:"D:\webroot\mengdie_yyg\app\www_tpl\default\pay\info.html";i:1468303703;}*/ ?>
<!--确认订单-->
<div class="m-duobao-order-list">
    <ul class="order-list">
        <li class="order-list-header f-clear">
            <div class="order-list-items-name items-goods-name">商品名称</div>
            <div class="order-list-items-name items-goods-period">商品期号</div>
            <div class="order-list-items-name items-goods-price">价值</div>
            <div class="order-list-items-name items-goods-buyunit">夺宝价</div>
            <div class="order-list-items-name items-goods-num">参与人次</div>
            <div class="order-list-items-name items-goods-regular">参与期数</div>
            <div class="order-list-items-name items-goods-total">小计</div>
        </li>
        <!--循环-->
        <?php if(!empty($list)): if(is_array($list)): foreach($list as $key=>$vo): ?>
        <li class="order-list-items f-clear">
            <div class="order-list-items-content items-goods-name">
                <p>
                    <a href="#l" target="_blank" title="<?php echo (isset($vo['g_name']) && ($vo['g_name'] !== '')?$vo['g_name']:'--'); ?>"><?php echo (isset($vo['g_name']) && ($vo['g_name'] !== '')?$vo['g_name']:'--'); ?></a>
                </p>
            </div>
            <div class="order-list-items-content items-goods-period f-items-center"><?php echo (num_base_mask($vo['nper_id']) !== ''?num_base_mask($vo['nper_id']):'--'); ?></div>
            <div class="order-list-items-content items-goods-price f-items-center"><?php echo number_format($vo['price'],'0'); echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?>
            </div>
            <div class="order-list-items-content items-goods-buyunit f-items-center">
                <?php echo number_format($vo['unit_price'],'0'); echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?>
            </div>
            <div class="order-list-items-content items-goods-num f-items-center"><?php echo (isset($vo['num']) && ($vo['num'] !== '')?$vo['num']:'0'); ?></div>
            <div class="order-list-items-content items-goods-regular f-items-center">1</div>
            <div class="order-list-items-content items-goods-total f-items-center">
                <span><?php echo number_format($vo['sum_price'],'0'); echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?></span>
            </div>
        </li>
        <?php endforeach; endif; else: ?>
        <div class="cart-empty-tips"><p>您的清单里还没有任何商品，<a href="/">马上去逛逛~</a></p></div>
        <?php endif; ?>
        <!--循环-->

        <li class="order-list-footer f-clear">
            <a href="javascript:" onclick="location.reload();">返回清单修改</a>
            <span class="order-total txt-gray">商品合计：<strong><?php echo number_format($sum_price,'0'); ?></strong>&nbsp;<?php echo C('MONEY_UNIT'); echo C('MONEY_NAME'); ?></span>
        </li>
    </ul>
    <div class="m-coupon-options f-clear" pro="orderfooter">
        <div class="m-order-footer-msg">
            
            <div class="footer-items pay-total">
                总需支付：
                <span class="footer-items-money"><strong>&yen;</strong><strong><?php echo number_format($sum_price,'2'); ?></strong></span>
            </div>
            <div class="m-order-operation f-clear" style="">
                <button class="w-button w-button-main w-button-xl go_to_pay" type="button"><span>去支付</span></button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="submit_ids" value="<?php echo (isset($ids) && ($ids !== '')?$ids:''); ?>">