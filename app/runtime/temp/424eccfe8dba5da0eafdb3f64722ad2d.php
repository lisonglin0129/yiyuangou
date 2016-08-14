<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"D:\webroot\mengdie_yyg\app\www_tpl\default\goods\get_calc_result_list.html";i:1468303703;}*/ ?>
<style>
    table th, table td {
        text-align: center;
    }
</style>
<table class="m-detail-mainTab-resultList" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th class="time" colspan="2">夺宝时间</th>
        <th>会员帐号</th>
        <th>用户IP</th>
        <th>用户地区</th>
        <th>参与人次</th>
    </tr>
    </thead>
    <tbody>
    <tr class="startRow">
        <td colspan="6">
            截至本商品最后一名用户购买时间【<?php echo (microtime_format($last_pay_time,'3','Y-m-d H:i:s') !== ''?microtime_format($last_pay_time,'3','Y-m-d H:i:s'):''); ?>】购买记录
        </td>
    </tr>
    <!--循环-->
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
    <tr class="calcRow">
        <td class="day"><?php echo microtime_format($vo['pay_time'],1,'Y-m-d'); ?></td>
        <td class="time"><?php echo microtime_format($vo['pay_time'],3,'H:i:s'); ?>
            <i class="ico ico-arrow-transfer"></i>
            <b class="txt-red"><?php echo $vo['pay_time_format']; ?></b>
        </td>
        <td class="user">
            <div class="f-txtabb"><a title="<?php echo (isset($vo['username']) && ($vo['username'] !== '')?$vo['username']:'--'); ?>(ID:<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:'--'); ?>)" href="<?php echo dwz_filter('ta/index',['uid'=>$vo['uid']]); ?>" target="_blank"><?php echo $vo['uname']; ?></a>
            </div>
        </td>
        <td><?php echo (isset($vo['reg_ip']) && ($vo['reg_ip'] !== '')?$vo['reg_ip']:'--'); ?></td>
        <td><?php echo (isset($vo['ip_area']) && ($vo['ip_area'] !== '')?$vo['ip_area']:'--'); ?></td>
        <td><?php echo $vo['num']; ?>次</td>
    </tr>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    <!--结果-->
    <tr class="resultRow">
        <td colspan="6" style="text-align: left">
            <h4>计算结果<a name="calcResult"></a></h4>
            <ol>
                <li><span class="index">1、</span>求和：<?php echo (isset($n_info['sum_timestamp']) && ($n_info['sum_timestamp'] !== '')?$n_info['sum_timestamp']:'0'); ?>
                    (上面50条参与记录的时间取值相加)
                </li>
                <li><span class="index">2、</span>
                    最近下一期 (<?php echo (isset($n_info['lottery_issue']) && ($n_info['lottery_issue'] !== '')?$n_info['lottery_issue']:'--'); ?>期)“老时时彩”幸运号码：
                    <?php if(empty($open_num)): ?>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <?php else: if(is_array($open_num)): $i = 0; $__LIST__ = $open_num;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <b class="ball"><?php echo $vo; ?></b>
                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </li>
                <li><span class="index">3、</span>
                    求余：(<?php echo (isset($n_info['sum_timestamp']) && ($n_info['sum_timestamp'] !== '')?$n_info['sum_timestamp']:'0'); ?> +
                    <?php if(empty($open_num)): ?>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <b class="ball">?</b>
                    <?php else: if(is_array($open_num)): $i = 0; $__LIST__ = $open_num;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <b class="ball"><?php echo $vo; ?></b>
                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    ) % <?php echo (isset($n_info['sum_times']) && ($n_info['sum_times'] !== '')?$n_info['sum_times']:'0'); ?> (商品所需人次) =
                    <!--余数-->
                    <?php if(empty($remainder)): ?>
                    <b class="square">?</b>
                    <b class="square">?</b>
                    <b class="square">?</b>
                    <?php else: if(is_array($remainder)): $i = 0; $__LIST__ = $remainder;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <b class="square"><?php echo $vo; ?></b>
                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    (余数) <i data-func="remainder" class="ico ico-questionMark"></i>
                </li>
                <li><span class="index">4、</span>
                    <!--余数-->
                    <?php if(empty($remainder)): ?>
                    <b class="square">?</b>
                    <b class="square">?</b>
                    <b class="square">?</b>
                    <?php else: if(is_array($remainder)): $i = 0; $__LIST__ = $remainder;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <b class="square"><?php echo $vo; ?></b>
                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    (余数) + <?php echo $lottory_base; ?> =

                    <!--开奖号码-->
                    <?php if(empty($luck_num)): ?>
                    <b class="square">?</b>
                    <b class="square">?</b>
                    <b class="square">?</b>
                    <?php else: if(is_array($luck_num)): $i = 0; $__LIST__ = $luck_num;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <b class="square"><?php echo $vo; ?></b>
                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </li>
            </ol>
            <span class="resultCode">幸运号码：
                <span style="margin-left:10px;color:#bbb">

                    <!--开奖号码-->
                    <?php if(empty($luck_num)): ?>
                    等待揭晓...
                    <?php else: if(is_array($luck_num)): $i = 0; $__LIST__ = $luck_num;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <b class="ball"><?php echo $vo; ?></b>
                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </span>
            </span>
        </td>
    </tr>
    </tbody>
</table>
<div class="xc_pages" data-flag="calc_result">
    <?php echo $pages; ?>
</div>