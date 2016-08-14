<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"D:\webroot\mengdie_yyg\app\admin\view\conf\show_list.html";i:1469522861;}*/ ?>
<?php 
$arr = array('test.xiangchang.com','1.xiangchang.com');
if(in_array($_SERVER['HTTP_HOST'],$arr))
{
die("演示站配置不允许修改");
}
 ?>
<style>
    .form-group {
        margin-bottom: 0;
    }

    .ask_btn {
        color: #128f76;
        cursor: pointer;
    }

    .ask_btn:hover {
        color: #19cfac;
    }
    .web_url{
        color: #f3ad36;
    }
</style>
<div class="tabbable">
    <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
        <?php if(is_array($category_info)): $k = 0; $__LIST__ = $category_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;if(($k == 1)): ?>
        <li class="active">
            <?php else: ?>
        <li>
            <?php endif; ?>
            <a data-toggle="tab" href="#home<?php echo $k; ?>"><?php echo $vo['category']; ?></a>
        </li>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
    <div class="tab-content">
        <?php if(is_array($category_arr)): $key1 = 0; $__LIST__ = $category_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value1): $mod = ($key1 % 2 );++$key1;if(($key1 == 1)): ?>
        <div id="home<?php echo $key1; ?>" class="tab-pane active">
            <?php else: ?>
            <div id="home<?php echo $key1; ?>" class="tab-pane">
                <?php endif; ?>
                <form class="form-horizontal" role="form" id="form_content<?php echo $key1; ?>">
                    <?php $pre_tmp = ''; if(is_array($value1)): $i = 0; $__LIST__ = $value1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <!--标记分割线-->
                    <?php if($vo['admin_tpl'] != $pre_tmp): ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right"> <span
                                style="color:#438eb9;font-size: 18px;"><?php echo $vo['admin_tpl']; ?></span>
                        </label>
                        <div class="col-sm-9">
                            <hr style="border: 1px solid rgb(231, 231, 231) ;position: relative;top:-5px;width: 600px;float: left">
                        </div>
                    </div>
                    <?php endif; $pre_tmp = $vo['admin_tpl']; ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right">

                            <?php echo (isset($vo['title']) && ($vo['title'] !== '')?$vo['title']:''); if(!empty(trim($vo["remark"]))): ?>
                            <i title="点击查看该配置帮助" class="fa fa-question-circle ask_btn
                               <?php if(preg_match('/^http.*/',$vo['remark'])): ?>
                                   web_url
                                   <?php else: ?>
                                content
                                <?php endif; ?>"
                            data-id="<?php echo (isset($vo['name']) && ($vo['name'] !== '')?$vo['name']:''); ?>"
                            ></i>
                            <?php endif; ?>
                            <input type="hidden" id="<?php echo (isset($vo['name']) && ($vo['name'] !== '')?$vo['name']:''); ?>" value="<?php echo (isset($vo['remark']) && ($vo['remark'] !== '')?$vo['remark']:''); ?>">
                            <?php if(!empty($vo["is_must"]) AND $vo["is_must"] == "true"): ?><i title='如果使用[<?php echo (isset($vo['admin_tpl']) && ($vo['admin_tpl'] !== '')?$vo['admin_tpl']:""); ?>]功能,此项为必填项' style="color: #f36;" class="fa fa-star-o"></i><?php endif; ?>
                        </label>
                        <div class="col-sm-9">
                            <?php switch($vo['type']): case "textarea": ?>
                            <textarea data-id="<?php echo $vo['id']; ?>" class="col-xs-10 col-sm-5 conf_text" name="" id="" cols="30"
                                      rows="3"><?php echo (isset($vo['value']) && ($vo['value'] !== '')?$vo['value']:''); ?></textarea>
                            <?php break; case "select": ?>
                            <select name="<?php echo $vo['name']; ?>" class="conf_text" data-id="<?php echo $vo['id']; ?>">
                                <?php $tmp = json_decode($vo['exec_data'],true); if(is_array($tmp)): foreach($tmp as $k=>$item): ?>
                              	  <option value="<?php echo $item; ?>" <?php if($vo['value'] == $item): ?>selected="selected" <?php endif; ?> ><?php echo $k; ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                            <?php break; case "radio": $tmp = json_decode($vo['exec_data'],true); if(is_array($tmp)): foreach($tmp as $k=>$item): ?>
                            <input data-id="<?php echo $vo['id']; ?>" class="conf_text" name="<?php echo $vo['name']; ?>"
                                   <?php if($vo['value'] == $item): ?>checked="checked" <?php endif; ?> type="radio"
                            value="<?php echo $item; ?>"><?php echo $k; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endforeach; endif; break; case "checkbox": break; case "img_id": ?>
                            <input type="text" data-id="<?php echo $vo['id']; ?>" placeholder="请选取图片"
                                   class="col-xs-10 col-sm-5 conf_text" value="<?php echo $vo['value']; ?>">
                            <a href="javascript:;" class="btn btn-xs btn-info conf_upload_btn" data-type="img_id"
                               title="上传">
                                <i class="icon-upload bigger-120"></i>上传图片
                            </a>
                            <a href="javascript:;" class="btn btn-xs btn-info conf_preview_id_btn" title="上传">
                                <i class="icon-picture bigger-120"></i>预览图片
                            </a>
                            <?php break; case "img_addr": ?>
                            <input type="text" data-id="<?php echo $vo['id']; ?>" placeholder="请选取图片"
                                   class="col-xs-10 col-sm-5 conf_text" value="<?php echo $vo['value']; ?>">
                            <a href="javascript:;" class="btn btn-xs btn-info conf_upload_btn" data-type="img_addr"
                               title="上传">
                                <i class="icon-upload bigger-120"></i>上传图片
                            </a>
                            <a href="javascript:;" class="btn btn-xs btn-info conf_preview_addr_btn" title="上传">
                                <i class="icon-picture bigger-120"></i>预览图片
                            </a>
                            <?php break; default: ?>
                            <input type="text" data-id="<?php echo $vo['id']; ?>" placeholder=""
                                   class="col-xs-10 col-sm-5 conf_text" value="<?php echo $vo['value']; ?>">
                            <?php endswitch; ?>
                        </div>
                    </div>
                    <div class="space-4"></div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </form>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>
<input type="hidden" id="update_conf" value="<?php echo U('conf/update_conf'); ?>"/>
<script type="text/javascript" src="__common__/admin/js/conf/show_list.js"></script>

<script>
    $(document).on("click", ".web_url", function () {
        var obj = $(this);
        var url = $("#" + obj.data("id")).val();
        //访问网址显示网址内容
        layer.open({
            type: 2,
            title: '配置指导',
            shadeClose: true,
            move:false,
            shade: 0.3,
            area: ['90%', '90%'],
            content: url

        });



    });
    $(document).on("click", ".content", function () {
        var obj = $(this);
        var content = $("#" + obj.data("id")).val();
        //自定页
        layer.alert(content,{"title":"配置指导",btn:false,move:false})

    });

</script>

