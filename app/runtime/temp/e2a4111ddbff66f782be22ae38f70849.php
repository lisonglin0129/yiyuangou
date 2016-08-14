<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"D:\webroot\mengdie_yyg\app\www_tpl\default\pay\show_rand_goods.html";i:1468303703;}*/ ?>
<!--循环-->
<?php if(!empty($groom_list)): if(is_array($groom_list)): foreach($groom_list as $k=>$vo): if($k < 6): ?>
<li class="w-goodsList-item">
    <div class="w-goods w-goods-brief">
        <div class="w-goods-pic">
            <a href="<?php echo dwz_filter('goods/jump_to_goods_buying',array('gid'=>$vo['g_id'])); ?>" title="点击抢购" target="_blank">
                <img width="200" height="200"  src="__UPLOAD_DOMAIN__<?php echo (isset($vo['i_img_path']) && ($vo['i_img_path'] !== '')?$vo['i_img_path']:'__yyg__/images/empty_img.jpg'); ?>"
                     alt="<?php echo (isset($vo['g_name']) && ($vo['g_name'] !== '')?$vo['g_name']:'--'); ?>">
            </a>
        </div>
        <p class="w-goods-title f-txtabb">
            <a title="<?php echo (isset($vo['g_name']) && ($vo['g_name'] !== '')?$vo['g_name']:'--'); ?>" href="#" target="_blank"><?php echo (isset($vo['g_name']) && ($vo['g_name'] !== '')?$vo['g_name']:'--'); ?></a>
        </p>
        <p class="w-goods-price">剩余：
            <?php 
            $last_num_1 = (int)$vo['sum_times'] - (int)$vo['participant_num'];
            echo $last_num_1 < 0 ? 0 :$last_num_1;
             ?>
            人次</p>
    </div>
</li>
<?php endif; endforeach; endif; else: ?>
暂无推荐
<?php endif; ?>
