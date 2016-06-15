<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>$admin_title</title>
</head>
<body>
<div class="main_box">
    <h2 onClick="return openitem('fuwuqi',this)" style="cursor:pointer;">系统信息&nbsp;&nbsp;&nbsp;&nbsp;<h><font id="fuwuqi_h">＋</font></h></h2>

    <table width="100%"  border="0" align="center" cellpadding="3" cellspacing="1" class="t_list" id="fuwuqi">
      <tr>
        <td width="19%" class="td_left">PHP版本：</td>
        <td width="28%" class="td_left">${echo phpversion();}</td>
        <td width="20%" class="td_left">GD版本：</td>
        <td width="33%" class="td_left">$gdversion</td>
      </tr>

      <tr>
        <td class="td_left">Register_Globals：</td>
        <td class="td_left">{eval echo ini_get("register_globals") ? 'On' : 'Off'}</td>
        <td class="td_left">Magic_Quotes_Gpc：</td>
        <td class="td_left">${echo ini_get("magic_quotes_gpc") ? 'On' : 'Off'}</td>
      </tr>
      <tr>

        <td class="td_left">支持上传的最大文件：</td>
        <td class="td_left">${echo ini_get("post_max_size")}</td>
        <td class="td_left">是否允许打开远程连接：</td>
        <td class="td_left">${echo ini_get("allow_url_fopen") ? '支持' : '不支持';}</td>
      </tr>

      <tr>
        <td class="td_left">服务器操作系统：</td>
        <td class="td_left">${echo PHP_OS; echo '('.$_SERVER['SERVER_ADDR'].')';}</td>
        <td class="td_left">Web 服务器：</td>
        <td class="td_left">${echo $_SERVER['SERVER_SOFTWARE']}</td>
      </tr>
      
      <tr>
        <td class="td_left">MySQL 版本：</td>
        <td class="td_left">{$mysql_version}</td>
        <td class="td_left">安全模式：</td>
        <td class="td_left">${$safe_mode = (boolean) ini_get('safe_mode') ? 'yes' : 'no'; echo $safe_mode;}</td>
      </tr>
      
      <tr>
        <td class="td_left">版本名称：</td>
        <td class="td_left">${echo $GLOBALS['TmacConfig']['config']['softname'];}[${echo $GLOBALS['TmacConfig']['config']['soft_enname'];}]</td>
        <td class="td_left">版本号：</td>
        <td class="td_left">${echo $GLOBALS['TmacConfig']['config']['version'];} Release {$upTime}</td>
      </tr> 
      
      <tr>
        <td class="td_left">开发团队：</td>
        <td class="td_left">${echo $GLOBALS['TmacConfig']['config']['soft_devteam'];}</td>
        <td class="td_left">程序编码：</td>
        <td class="td_left">${echo $GLOBALS['TmacConfig']['config']['soft_lang'];}</td>
      </tr>                  
      </table>
   
    
  




    
  </div>
<script>
function openitem(val,thisval)
{
	hid = val+'_h';	
	if(document.getElementById(val).style.display=="none"){		
		document.getElementById(val).style.display="";		
		document.getElementById(hid).innerHTML="—";
	}
	else{
		document.getElementById(hid).innerHTML="＋";
		document.getElementById(val).style.display="none";
	}
}
</script>
</body>
</html>