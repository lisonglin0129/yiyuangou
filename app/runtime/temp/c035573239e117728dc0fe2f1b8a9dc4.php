<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\webroot\mengdie_yyg\app\www_tpl\default\ta\history_list.html";i:1468303703;}*/ ?>
<div class="listCont" style="display: block;">
    <div id="pro-view-16">
        <table class="w-table m-user-comm-table" pro="listHead">
            <thead>
            <tr>
                <th class="col-info">商品信息</th>
                <th class="col-period">期号</th>
                <th class="col-joinNum">参与人次</th>
                <th class="col-status">夺宝状态</th>
                <th class="col-opt">操作</th>
            </tr>
            </thead>
        </table>
        <div pro="duobaoList" class="duobaoList">
            <!--循环-->
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
                        <a class="w-button w-button-main" href="<?php echo dwz_filter('goods/jump_to_goods_buying',['gid'=>$vo['g_id']]); ?>" target="_blank"><span>参与最新</span></a>
                        <p><a href="javascript:void(0)" pro="viewCode" data-luckcode="" class="see_luck_num"  data-uid="<?php echo $user_info['id']; ?>" data-nper="<?php echo $vo['n_id']; ?>">查看夺宝号码</a></p>
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
                        <a class="w-button w-button-main" href="<?php echo dwz_filter('goods/jump_to_goods_buying',['gid'=>$vo['g_id']]); ?>" target="_blank"><span>参与最新</span></a>
                        <p><a href="javascript:void(0)" pro="viewCode" data-luckcode="" class="see_luck_num"  data-uid="<?php echo $user_info['id']; ?>" data-nper="<?php echo $vo['n_id']; ?>">查看夺宝号码</a></p>
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
                                <div class="name">获得者：<a href="<?php echo dwz_filter('ta/index',['uid'=>$vo['luck_uid']]); ?>" title="<?php echo $vo['nick_name']; ?>"><?php echo $vo['nick_name']; ?></a>（本期参与<strong class="txt-dark"><?php echo isset($num[$vo['n_id']]) ? $num[$vo['n_id']] : '暂无数据'; ?></strong>人次）</div>
                                <div class="code">幸运代码：<strong class="txt-impt"><?php echo num_base_mask($vo['luck_num'],1); ?></strong></div>
                                <div class="time_">揭晓时间：<?php echo date('Y-m-d H:i:s',$vo['open_time']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="col-period"><?php echo num_base_mask($vo['n_id']); ?></td>
                    <td class="col-joinNum"><?php echo $vo['num']; ?>人次</td>
                    <td class="col-status">
                        <span>已揭晓</span>
                    </td>
                    <td class="col-opt">
                        <a class="w-button w-button-main" href="<?php echo dwz_filter('goods/jump_to_goods_buying',['gid'=>$vo['g_id']]); ?>" target="_blank"><span>参与最新</span></a>
                        <p><a href="javascript:void(0)" pro="viewCode" data-luckcode="" class="see_luck_num" data-uid="<?php echo $user_info['id']; ?>" data-nper="<?php echo $vo['n_id']; ?>">查看夺宝号码</a></p>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php break; default: endswitch; endforeach; endif; else: echo "" ;endif; endif; ?>
        </div>
        <div class="pager">
            <div class="w-pager">
                <?php if($page_current > '1'): ?>
                <a data-url="<?php echo U('Ta/history_list'); ?>" class="w-button w-button-main w-button-aside" data-container="ajax_container" data-param='{"page":<?php echo $page_current-1; ?>,"uid":<?php echo $user_info['id']; ?>}' ajax_table="ajax_table"><span>上一页</span></a>
                <?php else: ?>
                <a class="w-button w-button-aside w-button-disabled"><span>上一页</span></a>
                <?php endif; $__FOR_START_11983__=1;$__FOR_END_11983__=$page_count+1;for($page=$__FOR_START_11983__;$page < $__FOR_END_11983__;$page+=1){ if(($page_current <= $page_count) AND ($page <= 5)): ?>
                    <a data-page="<?php echo $page; ?>" data-url="<?php echo U('Ta/history_list'); ?>" title="第<?php echo $page; ?>页" class="w-button <?php echo ($page_current==$page)?'w-button-main':'w-button-aside'; ?>" data-container="ajax_container" data-param='{"page":<?php echo $page; ?>,"uid":<?php echo $user_info['id']; ?>}' ajax_table="ajax_table"><?php echo $page; ?></a>
                    <?php endif; } if(($page_current < $page_count) AND ($page_current < 5)): ?>
                <a class="w-button w-button-aside" data-url="<?php echo U('Ta/history_list'); ?>" data-container="ajax_container" data-param='{"page":<?php echo $page_current+1; ?>,"uid":<?php echo $user_info['id']; ?>}' ajax_table="ajax_table"><span>下一页</span></a>
                <?php else: ?>
                <a class="w-button w-button-aside w-button-disabled"><span>下一页</span></a>
                <?php endif; ?>
            </div>
        </div>
        <div pro="limitTips" class="limitTips" style="display: block;">一年内共有<span class="txt-impt"><?php echo $count_all; ?></span>条夺宝记录，仅可查看其最近50条记录~</div>
    </div>
</div>