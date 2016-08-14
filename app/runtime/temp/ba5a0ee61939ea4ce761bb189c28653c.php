<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"D:\webroot\mengdie_yyg\app\admin\view\category\son_show_list.html";i:1468303702;}*/ ?>
<table id="sample-table-1" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th class="center">
            <label>
                <input type="checkbox" class="ace"/>
                <span class="lbl"></span>
            </label>
        </th>
        <th style="text-align:left">显示名称</th>
        <!--<th>父级分类</th>-->
        <th>排序号</th>
        <th>所属分类</th>
        <!--<th>图标ID</th>-->
        <th>小图标url</th>
        <th>小图标样式扩展</th>
        <th>识别码</th>
        <!--<th>状态</th>-->
        <th>URL</th>
        <th>描述</th>
        <!--<th>总点击次数</th>-->
        <th>图片地址</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($son_cate_list)): $i = 0; $__LIST__ = $son_cate_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!empty($vo["id"])): ?>
    <tr>
        <td class="center">

            <label>
                <input type="checkbox" class="ace"/>
                <span class="lbl"></span>
            </label>
        </td>
        <td class="hidden-480;"  style="text-align:left"><?php echo (isset($vo['title_show']) && ($vo['title_show'] !== '')?$vo['title_show']:''); ?></td>
        <!--<td><?php echo (isset($vo['pid']) && ($vo['pid'] !== '')?$vo['pid']:''); ?></td>-->
        <td><?php echo (isset($vo['orders']) && ($vo['orders'] !== '')?$vo['orders']:''); ?></td>
        <td><?php echo (isset($vo['mid']) && ($vo['mid'] !== '')?$vo['mid']:''); ?></td>
        <td><?php echo (isset($vo['imgid']) && ($vo['imgid'] !== '')?$vo['imgid']:''); ?></td>
        <!--<td><?php echo (isset($vo['icon']) && ($vo['icon'] !== '')?$vo['icon']:''); ?></td>-->
        <td><?php echo (isset($vo['style']) && ($vo['style'] !== '')?$vo['style']:''); ?></td>
        <td><?php echo (isset($vo['code']) && ($vo['code'] !== '')?$vo['code']:''); ?></td>
        <!--<td><?php echo (isset($vo['status']) && ($vo['status'] !== '')?$vo['status']:''); ?></td>-->
        <td><?php echo (isset($vo['url']) && ($vo['url'] !== '')?$vo['url']:''); ?></td>
        <td><?php echo (isset($vo['desc']) && ($vo['desc'] !== '')?$vo['desc']:''); ?></td>
        <!--<td><?php echo (isset($vo['sumclick']) && ($vo['sumclick'] !== '')?$vo['sumclick']:''); ?></td>-->
        <td><?php echo (isset($vo['img_url']) && ($vo['img_url'] !== '')?$vo['img_url']:''); ?></td>
        <td>
            <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
                <a target="_blank" href="<?php echo U('son_exec',array('type'=>'see','id'=>$vo['id'],'mid'=>$mid)); ?>"
                   class="btn btn-xs btn-success" title="查看">
                    <i class="icon-eye-open bigger-120"></i>
                </a>

                <?php if($vo["edit_status"] == 1): ?>
                <a target="_self" href="<?php echo U('son_exec',array('id'=>$vo['id'],'type'=>'edit','mid'=>$mid)); ?>" class="btn btn-xs btn-info"
                   title="编辑">
                    <i class="icon-edit bigger-120"></i>
                </a>
                <?php endif; if($vo["del_status"] == 1): ?>
                <a href="javascript:" data-id="<?php echo (isset($vo['id']) && ($vo['id'] !== '')?$vo['id']:''); ?>" class="btn btn-xs btn-danger del_btn" title="删除">
                    <i class="icon-trash bigger-120"></i>
                </a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
</table>

<div class="page">
    <?php echo (isset($pages) && ($pages !== '')?$pages:''); ?>
</div>