<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\webroot\mengdie_yyg\app\mobile\view\alipay\form_submit.html";i:1468303710;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>正在为您跳转到支付宝..</title>
</head>
<body>
<form action="<?php echo $url; ?>" target="_self" id="submit_form">
    <?php if(is_array($list)): foreach($list as $k=>$vo): ?>
        <input type="hidden" name="<?php echo $k; ?>" value="<?php echo $vo; ?>">
    <?php endforeach; endif; ?>
    <h3 class="">正在提交数据… 如果没有进入支付页面，请点<input type="submit" value="提交">继续提交</h3>
</form>
<script>
    document.getElementById("submit_form").submit();
</script>
</body>
</html>