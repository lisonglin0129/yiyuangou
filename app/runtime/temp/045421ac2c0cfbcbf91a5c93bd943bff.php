<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"D:\webroot\mengdie_yyg\app\admin\view\order\show_list.html";i:1468303702;}*/ ?>


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
        <th>订单号</th>
        <th>用户昵称</th>
        <th>子订单数量</th>
        <th>订单金额</th>
        <th>业务类型</th>
        <th>支付状态</th>
        <th>支付时间</th>
        <th>来源</th>
        <th>订单状态修改</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($order_list)): $i = 0; $__LIST__ = $order_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!empty($vo["id"])): ?>
    <tr>
        <td class="center">

            <label>
                <input type="checkbox" class="ace"/>
                <span class="lbl"></span>
            </label>
        </td>
        <td><?php echo (isset($vo['uid']) && ($vo['uid'] !== '')?$vo['uid']:'--'); ?></td>
        <td><?php echo (isset($vo['order_id']) && ($vo['order_id'] !== '')?$vo['order_id']:'--'); ?></td>
        <td><?php echo (isset($vo['nick_name']) && ($vo['nick_name'] !== '')?$vo['nick_name']:'--'); ?></td>
        <td><?php echo (isset($vo['num']) && ($vo['num'] !== '')?$vo['num']:'--'); ?></td>
        <td><?php echo (isset($vo['price']) && ($vo['price'] !== '')?$vo['price']:'--'); ?></td>
        <td>
            <?php if(!empty($vo['bus_type'])): switch($vo['bus_type']): case "recharge": ?>充值<?php break; case "buy": ?>购买商品<?php break; default: ?>
            --
            <?php endswitch; endif; ?>
        </td>
        <td>
            <?php if(!empty($vo['pay_status'])): switch($vo['pay_status']): case "1": ?>未付款<?php break; case "2": ?>已付款<?php break; case "3": ?>已分配<?php break; default: ?>
            --
            <?php endswitch; endif; ?>
        </td>
        <td><?php echo (isset($vo['pay_time']) && ($vo['pay_time'] !== '')?$vo['pay_time']:'--'); ?></td>
        <td><?php echo (isset($vo['pay_origin']) && ($vo['pay_origin'] !== '')?$vo['pay_origin']:'--'); ?></td>


        <td>
                <select class="col-xs-12 col-sm-5 select_status" data-placeholder="请选择" name="status" style="width: 100%;" id="<?php echo $vo['id']; ?>">
                    <option value="1" <?php if(!empty($vo['status']) AND $vo['status'] == '1'): ?>selected="selected"<?php endif; ?> >正常</option>
                    <option value="-1" <?php if(!empty($vo['status']) AND $vo['status'] == '-1'): ?>selected="selected"<?php endif; ?> >删除</option>
                    <option value="-2" <?php if(!empty($vo['status']) AND $vo['status'] == '-2'): ?>selected="selected"<?php endif; ?> >关闭</option>
                </select>
        </td>

        <td>
            <a href="<?php echo U('Order/son_show_list',array('id'=>$vo['o_id'])); ?>" target="_blank">
                <button type="button" class="btn btn-primary">查看子订单</button>
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


