<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\webroot\mengdie_yyg\app\www_tpl\default\pay\recharge_do.html";i:1468303703;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>正在创建订单...</title>
</head>
<body>
<p>正在创建订单,请稍候...</p>
<form method="post" id="order_form" action="<?php echo $pay_url; ?>">
    <input type="hidden" name="order_id" value="<?php echo $order; ?>">
    <input type="hidden" name="timestamp" value="<?php echo $timestamp; ?>">
    <input type="hidden" name="sign" value="<?php echo $sign; ?>">
</form>
<script>
    document.getElementById("order_form").submit();
</script>
</body>
</html>