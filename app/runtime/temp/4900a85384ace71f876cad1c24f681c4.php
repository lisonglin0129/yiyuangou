<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:56:"D:\webroot\mengdie_yyg\app\admin\view\win\show_list.html";i:1468303701;}*/ ?>


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
        <th>彩票开奖号码</th>
        <th>幸运数字</th>
        <th>幸运用户名</th>
        <th>幸运用户昵称</th>
        <th>手机号</th>
        <th>用户类型</th>
        <th>幸运时间</th>
        <th>购买数量</th>
        <th>是否通知中奖</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($win_record_list)): $i = 0; $__LIST__ = $win_record_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!empty($vo["id"])): ?>
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
        <td><?php echo (isset($vo['open_num']) && ($vo['open_num'] !== '')?$vo['open_num']:'--'); ?></td>
        <td><?php echo (isset($vo['luck_num']) && ($vo['luck_num'] !== '')?$vo['luck_num']:'--'); ?></td>
        <td><?php echo (isset($vo['username']) && ($vo['username'] !== '')?$vo['username']:'--'); ?></td>
        <td>
            <?php switch($vo['type']): case "-1": echo (isset($vo['nick_name']) && ($vo['nick_name'] !== '')?$vo['nick_name']:'--'); break; default: ?><label style="color: red"><?php echo (isset($vo['luck_user']) && ($vo['luck_user'] !== '')?$vo['luck_user']:'--'); ?></label>
            <?php endswitch; ?>
        </td>
        <td><?php echo $vo['phone']; ?></td>
        <td>
            <?php switch($vo['type']): case "-1": ?>机器人<?php break; case "1": ?>普通用户<?php break; case "2": ?>商户<?php break; default: ?>--
            <?php endswitch; ?>
        </td>
        <td><?php echo (microtime_format($vo['luck_time'],'3','Y-m-d H:i:s') !== ''?microtime_format($vo['luck_time'],'3','Y-m-d H:i:s'):'--'); ?></td>
        <td><?php echo (isset($vo['order_num']) && ($vo['order_num'] !== '')?$vo['order_num']:'--'); ?></td>
        <td>
            <?php switch($vo['notice']): case "1": ?>已通知<?php break; case "0": ?>未通知<?php break; default: ?>
            --
            <?php endswitch; ?>
        </td>
        <td>
            <?php switch($vo['logistics_status']): case "1": ?>用户确认收货地址<?php break; case "2": ?>商品等待派发<?php break; case "3": ?>商品已派发<?php break; case "4": ?>商品已经签收<?php break; case "5": ?>晒单完成<?php break; default: ?>
            用户中奖
            <?php endswitch; ?>
        </td>
        <td>
            <a target="_blank" href="<?php echo U('win/get_info_by_id',array('id'=>$vo['id'])); ?>" class="btn btn-xs btn-info" title="编辑">
                <i class="icon-edit bigger-120"></i>
            </a>
            <?php if($vo['logistics_status'] >=3): ?>
                <a target="_blank" href="http://www.kuaidi100.com/chaxun?com=<?php echo $vo['logistics_company']; ?>&nu=<?php echo $vo['logistics_number']; ?>" data-id="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:''); ?>" class="btn btn-xs btn-warning ems" title="查看快递">
                    <i class="fa fa-twitter bigger-120"></i>
                </a>
            <?php endif; if($vo['type'] == '-1' AND empty($vo['sid']) OR (!empty($vo['sid']) AND $vo['status'] == '-1')): ?>
            <a target="_blank" href="<?php echo U('win/show_order',array('id'=>$vo['id'])); ?>" class="btn btn-xs btn-success" title="晒单">
                <i class="icon-eye-open bigger-120"></i>
            </a>
            <?php endif; ?>

        </td>
    </tr>
    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
</table>
<input type="hidden" id="submit" value="<?php echo U('Order/update_order_status'); ?>"/>
<div class="page">
    <?php echo (isset($pages) && ($pages !== '')?$pages:''); ?>
</div>
<script type="text/javascript" src="__plugin__/layer/extend/layer.ext.js"></script>


