<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"D:\webroot\mengdie_yyg\app\admin\view\goods\show_list.html";i:1468303702;}*/ ?>
<table id="sample-table-1" class="table table-striped table-bordered table-hover table-bordered table-list">
    <thead>
    <tr>
        <th class="center">
            <label>
                <input type="checkbox" class="ace select-all"/>
                <span class="lbl"></span>
            </label>
        </th>
        <th>缩略图</th>
        <th>商品标题</th>
        <th>状态</th>
        <th>商品分类</th>
        <th>夺宝类型</th>
        <th>总价(元)</th>
        <th>总购买次数</th>
        <th>初始购买</th>
        <!--<th>最大购买</th>-->
        <th>每次购买</th>
        <th>更新时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(empty($list)): ?>
    <tr align="center"><td colspan="12">暂无数据</td></tr>
    <?php else: if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!empty($vo["id"])): ?>
    <tr>
        <td class="center">
            <label>
                <input type="checkbox" class="ace" value="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:0); ?>"/>
                <span class="lbl"></span>
            </label>
        </td>
        <td>
            <?php if(empty($vo['img_path'])): ?>
            <img style="width: 64px;height: 64px;" src="__common__/img/empty_img.jpg" alt="loading..">
            <?php else: ?>
            <a href="<?php echo (isset($vo['img_path']) && ($vo['img_path'] !== '')?$vo['img_path']:'__common__/img/empty_img.jpg'); ?>" data-lightbox="roadtrip">
                <img style="width: 64px;height: 64px;" src="<?php echo (isset($vo['img_path']) && ($vo['img_path'] !== '')?$vo['img_path']:'__common__/img/empty_img.jpg'); ?>">
            </a>
            <?php endif; ?>
        </td>
        <td>
            <a target="_blank" href="<?php echo $domain_base; echo U('yyg/goods/jump_to_goods_buying',array('gid'=>$vo['id'])); ?>"><?php echo (isset($vo['name']) && ($vo['name'] !== '')?$vo['name']:''); ?></a>
        </td>
        <td>
            <?php if(!empty($vo['status'])): switch($vo['status']): case "1": ?><span class="label label-success">正常</span><?php break; case "-2": ?><span class="label label-success">禁用</span><?php break; default: ?>--
            <?php endswitch; endif; ?>
        </td>
        <td><?php echo (isset($vo['category_name']) && ($vo['category_name'] !== '')?$vo['category_name']:'--'); ?></td>
        <td><?php echo (isset($vo['deposer_name']) && ($vo['deposer_name'] !== '')?$vo['deposer_name']:'--'); ?></td>
        <td><?php echo (isset($vo['price']) && ($vo['price'] !== '')?$vo['price']:'--'); ?></td>
        <td><?php echo (isset($vo['sum_times']) && ($vo['sum_times'] !== '')?$vo['sum_times']:'--'); ?></td>
        <td><?php echo (isset($vo['init_times']) && ($vo['init_times'] !== '')?$vo['init_times']:'--'); ?></td>
        <!--<td><?php echo (isset($vo['max_times']) && ($vo['max_times'] !== '')?$vo['max_times']:'&#45;&#45;'); ?></td>-->
        <td><?php echo (isset($vo['min_times']) && ($vo['min_times'] !== '')?$vo['min_times']:'--'); ?></td>
        <td>
            <?php if(!empty($vo['update_time'])): echo date('Y-m-d H:i:s',$vo['update_time']); else: ?>
            --
            <?php endif; ?>
        </td>
        <td>
            <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
                <a target="_blank" href="<?php echo U('exec',array('type'=>'see','id'=>$vo['id'])); ?>"
                   class="btn btn-xs btn-success" title="查看">
                    <i class="icon-eye-open bigger-120"></i>
                </a>
                <a target="_blank" href="<?php echo U('exec',array('id'=>$vo['id'],'type'=>'edit')); ?>" class="btn btn-xs btn-info"
                   title="编辑">
                    <i class="icon-edit bigger-120"></i>
                </a>
                <a href="javascript:"  data-id="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:''); ?>" class="btn btn-xs btn-danger  del_goods" title="删除">
                    <i class="icon-trash bigger-120"></i>
                </a>
                <a href="javascript:"  data-id="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:''); ?>" class="btn btn-xs btn-primary qr_code" title="二维码" url="http://<?php echo $_SERVER['HTTP_HOST']; echo U('mobile/goods/goods_detail',array('nper_id'=>$vo['last_nper_id'],'goods_id'=>$vo['id'])); ?>">
                    <i class="icon-eye-open bigger-120"></i>
                </a>
                <?php if(empty($user_type) == true): ?>
                <a href="javascript:" data-id="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:''); ?>" class="btn btn-xs btn-warning trigger_open" title="触发下一期开奖">
                    <i class="fa fa-stack-overflow bigger-120"></i>
                </a>
                <a href="javascript:" data-id="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:''); ?>" class="btn btn-xs  btn-danger lock_goods" title="下架该商品">
                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                </a>
                <?php endif; ?>
                <!--<a target="_blank" href="<?php echo U('exec',array('pid'=>$vo['id'],'type'=>'add')); ?>"-->
                   <!--data-id="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:''); ?>" class="btn btn-xs btn-warning" title="添加子级菜单">-->
                    <!--<i class="icon-external-link bigger-120"></i>-->
                <!--</a>-->
                <!--<a href="<?php echo U('set'); ?>" class="btn btn-xs btn-warning">-->
                <!--<i class="icon-flag bigger-120"></i>-->
                <!--</a>-->
            </div>
            <div class="visible-xs visible-sm hidden-md hidden-lg">
                <div class="inline position-relative">
                    <button class="btn btn-minier btn-primary dropdown-toggle"
                            data-toggle="dropdown">
                        <i class="icon-cog icon-only bigger-110"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">
                        <li>
                            <a href="javascript:" class="tooltip-info" data-rel="tooltip" title="View">
														<span class="blue">
															<i class="icon-zoom-in bigger-120"></i>
														</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:" class="tooltip-success" data-rel="tooltip" title="Edit">
														<span class="green">
															<i class="icon-edit bigger-120"></i>
														</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:" class="tooltip-error" data-rel="tooltip" title="Delete">
													    <span class="red">
													    	<i class="icon-trash bigger-120"></i>
													    </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
    </tr>
    <?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
    </tbody>
</table>

<div class="page">
    <?php echo (isset($pages) && ($pages !== '')?$pages:''); ?>
</div>