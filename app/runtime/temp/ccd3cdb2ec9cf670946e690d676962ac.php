<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:64:"D:\webroot\mengdie_yyg\app\admin\view\zero\win_records_list.html";i:1468303702;}*/ ?>


<table id="sample-table-1" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th class="center">
            <label>
                <input type="checkbox" class="ace"/>
                <span class="lbl"></span>
            </label>
        </th>
        <th>期数</th>
        <th>商品名称</th>
        <th>订单号</th>
        <th>幸运用户</th>
        <th>抢宝类型</th>
        <th>中奖类型</th>
        <th>用户类型</th>
        <th>幸运时间</th>
        <th>商品状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!empty($vo["id"])): ?>
    <tr>
        <td class="center">

            <label>
                <input type="checkbox" class="ace"/>
                <span class="lbl"></span>
            </label>
        </td>
        <td><?php echo (isset($vo['nper_id']) && ($vo['nper_id'] !== '')?$vo['nper_id']:'--'); ?></td>
        <td><?php echo (isset($vo['goods_name']) && ($vo['goods_name'] !== '')?$vo['goods_name']:'--'); ?>(<?php echo (isset($vo['goods_id']) && ($vo['goods_id'] !== '')?$vo['goods_id']:'--'); ?>)</td>
        <td><?php echo (isset($vo['order_id']) && ($vo['order_id'] !== '')?$vo['order_id']:'--'); ?></td>
        <td><?php echo (isset($vo['nick_name']) && ($vo['nick_name'] !== '')?$vo['nick_name']:'--'); ?></td>
        <td>
            <?php switch($vo['qb_type']): case "1": ?>奇数<?php break; case "2": ?>偶数<?php break; default: ?><label style="color: red">--</label>
            <?php endswitch; ?>
        </td>
        <td>
            <?php switch($vo['luck_type']): case "1": ?>商品中奖<?php break; case "2": echo C('MONEY_NAME'); ?>中奖<?php break; default: ?><label style="color: red">--</label>
            <?php endswitch; ?>
        </td>
        <td>
            <?php switch($vo['type']): case "-1": ?>机器人<?php break; case "1": ?>普通用户<?php break; case "2": ?>商户<?php break; default: ?>--
            <?php endswitch; ?>
        </td>
        <td><?php echo (microtime_format($vo['luck_time'],'3','Y-m-d H:i:s') !== ''?microtime_format($vo['luck_time'],'3','Y-m-d H:i:s'):'--'); ?></td>
        <td>
            <?php switch($vo['logistics_status']): case "1": ?>用户确认收货地址<?php break; case "2": ?>商品派发中<?php break; case "3": ?>用户签收<?php break; case "4": ?>晒单完成<?php break; default: ?>
            用户获得商品
            <?php endswitch; ?>
        </td>
        <td>
            <a target="_blank" href="<?php echo U('win/get_info_by_id',array('id'=>$vo['id'],'form_zero'=>1)); ?>" class="btn btn-xs btn-info" title="编辑">
                <i class="icon-edit bigger-120"></i>
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


