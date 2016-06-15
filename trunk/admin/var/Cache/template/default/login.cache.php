<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\login.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\admin\application\View\default\login.tpl', 1464846067)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台登录</title>
<script src="<?php echo BASE; ?>js/tools.js" type="text/javascript"></script>
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<style>
body { background:url(<?php echo $BASE_V;?>images/login_bg.gif) 0 0 repeat-x;}
</style>
<script language="javascript">
function CheckPost(){
  if(document.myform.username.value==""){
  alert("请输入用户名");
  document.myform.username.style.color="#FF0000"
  document.myform.username.focus();
  return false;
 }
 if(document.myform.password.value==""){
  alert("请输入密码");
  document.myform.password.style.color="#FF0000"
  document.myform.password.focus();
  return false;
 }
 
if (!trim($('yzm').value) || $('yzm').value.length != 4 || isNaN($('yzm').value)) {
    alert('验证码必须为4位数字!');
    $('yzm').focus();
    return false
}
 
 
}

function reloadcode()
{
	$('img11').setAttribute('src','/<?php echo PHP_SELF; ?>?m=tool.Captcha&'+Math.random())
}
</script>
</head>

<body onLoad="document.myform.username.select();">

<div id="login">

  <form id="myform" name="myform" method="post" action="<?php echo PHP_SELF; ?>?m=login.check" onSubmit="return CheckPost();">
    用户名
    <input name="username" type="text" class="login_input" id="username" />
    　密码
    <input name="password" type="password" class="login_input" id="password" />
    　验证码
    <input name="yzm" type="text" style="width:50px" id="yzm" /><img src="<?php echo PHP_SELF; ?>?m=tool.Captcha" id="img11" class="yzm" onclick="reloadcode();">
    <input name="submit" type="submit" class="btn_login" id="submit" value=" " />
  </form>
</div>

</body>
</html>