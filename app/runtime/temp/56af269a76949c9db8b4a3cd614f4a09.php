<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\webroot\mengdie_yyg\app\admin\view\order\show_pay_list.html";i:1468303702;}*/ ?>

<table id="sample-table-1" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th class="center">
            <label>
                <input type="checkbox" class="ace"/>
                <span class="lbl"></span>
            </label>
        </th>
        <th>用户ID</th>
        <th>用户昵称</th>
        <th>充值订单ID</th>
        <th>充值订单名称</th>
        <th>订单总金额</th>
        <th>订单状态</th>
        <th>支付状态</th>
        <th>支付平台</th>
        <th>支付时间</th>
        <th>订单未付款关闭时间</th>
        <th>来源</th>
        <th>创建时间</th>
        <th>操作</th>

    </tr>
    </thead>
    <tbody>
    <?php if(is_array($pay_list)): $i = 0; $__LIST__ = $pay_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!empty($vo["id"])): ?>
    <tr>
        <td class="center">

            <label>
                <input type="checkbox" class="ace"/>
                <span class="lbl"></span>
            </label>
        </td>
        <td><?php echo (isset($vo['uid']) && ($vo['uid'] !== '')?$vo['uid']:'--'); ?></td>
        <td><?php echo (isset($vo['nick_name']) && ($vo['nick_name'] !== '')?$vo['nick_name']:'--'); ?></td>
        <td><?php echo (isset($vo['order_id']) && ($vo['order_id'] !== '')?$vo['order_id']:'--'); ?></td>
        <td><?php echo (isset($vo['name']) && ($vo['name'] !== '')?$vo['name']:'--'); ?></td>
        <td><?php echo (isset($vo['price']) && ($vo['price'] !== '')?$vo['price']:'--'); ?></td>
        <td>
            <?php if(!empty($vo['status'])): switch($vo['status']): case "1": ?>正常<?php break; case "-2": ?>关闭<?php break; default: ?>
            --
            <?php endswitch; endif; ?>
        </td>
        <td>
            <?php if(!empty($vo['pay_status'])): switch($vo['pay_status']): case "1": ?>未付款<?php break; case "2": ?>已付款<?php break; case "3": ?>已分配<?php break; default: ?>
            --
            <?php endswitch; endif; ?>
        </td>
        <td>
            <?php switch($vo['plat_form']): case "aipay": ?>爱贝支付<?php break; case "alipay": ?>支付宝<?php break; case "wxpay": ?>微信支付<?php break; default: ?>
            其他
            <?php endswitch; ?>
        </td>

        <td><?php echo microtime_format($vo['pay_time'],'3','Y-m-d H:i:s'); ?></td>
        <td><?php echo date("Y-m-d H:i:s",$vo['close_time']); ?></td>
        <td><?php echo (isset($vo['origin']) && ($vo['origin'] !== '')?$vo['origin']:'--'); ?></td>
        <td><?php echo date("Y-m-d H:i:s",$vo['create_time']); ?></td>

        <td>
            <a href="javascript:" data-id="<?php echo (isset($vo['order_id']) && ($vo['order_id'] !== '')?$vo['order_id']:''); ?>" data-plat="<?php echo (isset($vo['plat_form']) && ($vo['plat_form'] !== '')?$vo['plat_form']:''); ?>" class="btn btn-xs btn-warning search_miss_document" title="查询掉单状态">
                <i class="fa fa-stack-overflow bigger-120"></i>
            </a>


        </td>


    </tr>

    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
</table>
<input type="hidden" id="submit" value="<?php echo U('Order/update_order_status'); ?>"/>
<div class="page">
    <?php echo (isset($pages) && ($pages !== '')?$pages:''); ?>
</div>


