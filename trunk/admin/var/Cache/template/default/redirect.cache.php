<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\redirect.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\admin\application\View\default\redirect.tpl', 1464846064)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>系统消息</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css">
body{
    font-size: 12px;
    background-color: #EAF8F9;
}
.alert {
	color: #FF9900;
	font-size: 14px;
	margin-bottom: 25px;
}
.box {
	border: #FF9900 1px solid;
	width: 460px;
	margin: 100px auto;
	background-color: #FEFBE2;
	text-align: center;
	padding: 30px;
}
.alertmsg {
	margin-top: 25px;
}
a{ text-decoration:none}
</style>

</head>
<body style="text-align:center; height:100%; width:98%;">
<div class="box">
  <h2 class="alert"><?php echo $msg;?></h2>
  <div class="alertmsg"><a href="<?php echo $url;?>">将在<span id='backL'></span>秒以后将返回   如果你不想等待或浏览器没有自动跳转请点击这里跳转</a>
  </div>
</div>
<script language="JavaScript">
var url = "<?php echo $url;?>";
var t=<?php echo $time;?>;
function later_back(){
	document.getElementById('backL').innerHTML=t;
	t--; 
	if (t==0) {
		location.href= url ; 
	} else {
		setTimeout("later_back();",1000); 
	}
} 
later_back(); 
</script>
</body>
</html>

