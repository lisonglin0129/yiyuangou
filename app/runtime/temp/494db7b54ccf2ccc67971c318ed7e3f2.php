<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:51:"D:\webroot\mengdie_yyg\app\pay\view\wxpay\WxSm.html";i:1470823669;}*/ ?>
<html>
	<head>
		<title>微信支付</title>
		<style>
			 body {margin:0 auto;padding:0;font-family:"Microsoft YaHei";}
			.back-img {position:absolute; z-index;-1; left:0; top:0; width:100%; height:100%;}
			.box {width:100%; height:100%; z-index;1; position:absoulte; width:100%; height:100%;}
			.box-center {position:relative; margin:auto; width:50%; top:20%;  padding:20 10;}
			.center-wag {text-align:center;}
			.wx ,.bt {text-align:center; width:100%;}
			.wx-img {width:30%; align:center;}
			.bt a:link{ text-decoration:none;}
			.bt {padding-top:15px;}
			.bt a { color:#FFF; text-align:center; z-index:999; position:relative; display:block; margin:0 auto; width:280px; height:50px;  line-height:50px;  background:#00b8f3;}
			.wx-wg {width:100%; padding-top:10%; margin:0 auto; height:30px; position:relative; z-index;4;}
			.wx-wg  p{text-align:center;}
		</style>
	</head>
	<body>
		<img src='/common/img/paybg.png' class='back-img'/>
		<div class = 'box'>
			<div class = 'box-center'>
				<p class='center-wag'>本次订单号为：<?php echo $wx_order_id; ?></p>
				<p class='center-wag'>价格:<?php echo $price; ?>元</p>
				<div class = 'wx'>
					<img src='<?php echo $Wx->code_img_url; ?>' class='wx-img' />
				</div>
				<div id ='bt' class = 'bt'>
					<a  href = '/'>返回首页</a>
				</div>
			</div>
			<div class='wx-wg'>
				<p id = 'info-help'>
					
				</p>
			</div>
		</div>
	</body>
	<script>
	  
		 var dat  = new Object();
		 var url = '/';
		 var s = 3600;
		 var timeAtion = setInterval(function(){
			 //--- 如果到了时间自动跳转
			if(0 >= s) {
				alert(url);
				clearInterval(timeAtion);
				window.location.href = url;
				return;
			}
			document.getElementById('info-help').innerHTML =  s;
			s = s - 1;
		 },1000);
		 document.getElementById('Go').onclick = function() 
		 {
			window.location.href = url;
		 }
	</script>
</html>