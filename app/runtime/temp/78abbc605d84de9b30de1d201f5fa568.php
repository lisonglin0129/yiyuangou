<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"D:\webroot\mengdie_yyg\app\admin\view\menu\show_menu.html";i:1468303701;}*/ ?>

<ul class="nav nav-list">
    <?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu_1): $mod = ($i % 2 );++$i;?>



    <li <?php if( $menu_1['id'] == $select_path) echo  'class="active"' ;if( in_array($menu_1['id'],$open_path)) echo  'class="open"' ; ?>>


        <a target="_<?php echo (isset($menu1['target']) && ($menu1['target'] !== '')?$menu1['target']:'self'); ?>" href="<?php if(empty($menu_1['_child'])): echo U($menu_1['url']); if(!empty($menu_1['param'])): ?>?<?php echo $menu_1['param']; endif; else: ?>javascript:<?php endif; ?>" class="dropdown-toggle">
            <i class="fa <?php echo (isset($menu_1['ico']) && ($menu_1['ico'] !== '')?$menu_1['ico']:'fa-cogs'); ?>"></i>
            <span class="menu-text"><?php echo (isset($menu_1['title']) && ($menu_1['title'] !== '')?$menu_1['title']:'暂无标题'); ?></span>
            <?php if(!empty($menu_1['_child'])): ?>
            <b class="arrow icon-angle-down"></b>
            <?php endif; ?>
        </a>
        <?php if(!empty($menu_1['_child'])): ?>
        <ul class="submenu" <?php if( in_array($menu_1['id'],$open_path)) echo  'style="display:block;"' ; ?>>
            <?php if(is_array($menu_1['_child'])): $i = 0; $__LIST__ = $menu_1['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu_2): $mod = ($i % 2 );++$i;?>
            <li <?php if( $menu_2['id'] == $select_path) echo  'class="active"' ; if( in_array($menu_2['id'],$open_path)) echo  'class="open"' ; ?>>
                <?php if( is_null($menu_2['hide']) || $menu_2['hide'] == 'false'  ) {  ?>
                <a target="_<?php echo (isset($menu2['target']) && ($menu2['target'] !== '')?$menu2['target']:'self'); ?>" href="<?php if(empty($menu_2['_child'])): echo U($menu_2['url']); if(!empty($menu_2['param'])): ?>?<?php echo $menu_2['param']; endif; else: ?>javascript:<?php endif; ?>" class="dropdown-toggle">
                    <i class="fa <?php echo (isset($menu_2['ico']) && ($menu_2['ico'] !== '')?$menu_2['ico']:'fa-cogs'); ?>"></i>
                    <?php echo (isset($menu_2['title']) && ($menu_2['title'] !== '')?$menu_2['title']:'暂无标题'); if(!empty($menu_2['_child'])): ?>
                    <b class="arrow icon-angle-down"></b>
                    <?php endif; ?>
                </a>
                <?php } if(!empty($menu_2['_child'])): ?>
                <ul class="submenu" <?php if( in_array($menu_2['id'],$open_path)) echo  'style="display:block;"' ; ?>>
                    <?php if(is_array($menu_2['_child'])): $i = 0; $__LIST__ = $menu_2['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu_3): $mod = ($i % 2 );++$i;?>
                    <li <?php if( $menu_3['id'] == $select_path) echo  'class="active"' ; if( in_array($menu_3['id'],$open_path)) echo  'class="open"' ; ?>>
                    <?php if( is_null($menu_2['hide']) || $menu_2['hide'] == 'false'  ) {  ?>
                        <a target="_<?php echo (isset($menu3['target']) && ($menu3['target'] !== '')?$menu3['target']:'self'); ?>" href="<?php if(empty($menu_3['_child'])): echo U($menu_3['url']); if(!empty($menu_3['param'])): ?>?<?php echo $menu_3['param']; endif; else: ?>javascript:<?php endif; ?>" class="dropdown-toggle">
                            <i class="fa <?php echo (isset($menu_3['ico']) && ($menu_3['ico'] !== '')?$menu_3['ico']:'fa-cogs'); ?>"></i>
                            <?php echo (isset($menu_3['title']) && ($menu_3['title'] !== '')?$menu_3['title']:'暂无标题'); if(!empty($menu_3['_child'])): ?>
                            <b class="arrow icon-angle-down"></b>
                            <?php endif; ?>
                        </a>
                        <?php } if(!empty($menu_3['_child'])): ?>
                        <ul class="submenu" <?php if( in_array($menu_3['id'],$open_path)) echo  'style="display:block;"' ; ?>>
                            <?php if(is_array($menu_3['_child'])): foreach($menu_3['_child'] as $k=>$menu_4): ?>
                            <li <?php if( $menu_4['id'] == $select_path) echo  'class="active"' ; ?>>
                            <?php if( is_null($menu_2['hide']) || $menu_2['hide'] == 'false'  ) {  ?>
                                <a target="_<?php echo (isset($menu4['target']) && ($menu4['target'] !== '')?$menu4['target']:'self'); ?>" href="<?php if(empty($menu_4['_child'])): echo U($menu_4['url']); if(!empty($menu_4['param'])): ?>?<?php echo $menu_4['param']; endif; else: ?>javascript:<?php endif; ?>">
                                    <i class="fa <?php echo (isset($menu_4['ico']) && ($menu_4['ico'] !== '')?$menu_4['ico']:'fa-cogs'); ?>"></i>
                                    <?php echo (isset($menu_4['title']) && ($menu_4['title'] !== '')?$menu_4['title']:'暂无标题'); ?>
                                </a>
                            <?php } ?>
                            </li>
                            <?php endforeach; endif; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <?php endif; ?>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <?php endif; ?>
    </li>
    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
</ul><!-- /.nav-list -->

