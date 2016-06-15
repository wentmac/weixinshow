<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\data_revert.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\data_revert.tpl', 1433408042)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>数据库维护--数据还原</title>
<style>
body{ font-size:12px}
</style>
<script language="javascript">
//获得选中文件的数据表
function getCheckboxItem(){
	 var myform = document.form1;
	 var allSel="";
	 if(myform.bakfile.value) return myform.bakfile.value;
	 for(i=0;i<myform.bakfile.length;i++)
	 {
		 if(myform.bakfile[i].checked){
			 if(allSel=="")
				 allSel=myform.bakfile[i].value;
			 else
				 allSel=allSel+","+myform.bakfile[i].value;
		 }
	 }
	 return allSel;	
}
//反选
function ReSel(){
	var myform = document.form1;
	for(i=0;i<myform.bakfile.length;i++){
		if(myform.bakfile[i].checked) myform.bakfile[i].checked = false;
		else myform.bakfile[i].checked = true;
	}
}
//全选
function SelAll(){
	var myform = document.form1;
	for(i=0;i<myform.bakfile.length;i++){
		myform.bakfile[i].checked = true;
	}
}
//取消
function NoneSel(){
	var myform = document.form1;
	for(i=0;i<myform.bakfile.length;i++){
		myform.bakfile[i].checked = false;
	}
}
//
function checkSubmit()
{
	var myform = document.form1;
	myform.bakfiles.value = getCheckboxItem();
	return true;
}

</script>
</head>

<body>
<?php if($action == 'do') { ?>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#D1DDAA">
  <tr>
    <td height="19" colspan="6" background="img/tbg.gif" bgcolor="#E7E7E7">
    	<table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr>
          <td width="24%"><strong>数据库管理</strong></td>
          <td width="76%" align="right">
          	<b><a href="<?php echo $php_self;?>?m=data.back"><u>数据备份</u></a></b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <form name="form1" onSubmit="checkSubmit()" action="<?php echo $php_self;?>?m=data.dorevert" method="post" target="stafrm">
    <input type='hidden' name='path' value='<?php echo $path;?>' />
    <input type='hidden' name='bakfiles' value='' />
    <tr bgcolor="#F7F8ED"> 
      <td height="24" colspan="4" valign="top">
      	<strong>发现<?php echo $path;?>的备份文件：</strong>
        <?php if(count($filelists)==0 ) { ?> 没找到任何备份文件... <?php } ?>
      </td>
    </tr>
    <?php for($i=0;$i<count($filelists);$i++){  ?>    <?php echo "<tr  bgcolor='#FFFFFF' align='center' height='24'>\r\n";
      $mtd = "<td width='10%'>
             <input name='bakfile' id='bakfile' type='checkbox' class='np' value='".$filelists[$i]."' checked='1' /> 
             </td>
             <td width='40%'>$filelists[$i]</td>\r\n";
      echo $mtd;; ?>      
      <?php if(isset($filelists[$i+1]) ) { ?>
      <?php $i++;
      	$mtd = "<td width='10%'>
              <input name='bakfile' id='bakfile' type='checkbox' class='np' value='".$filelists[$i]."' checked='1' /> 
              </td>
              <td width='40%'>$filelists[$i]</td>\r\n";
        echo $mtd;
       } else { ?>
      <?php echo "<td></td><td></td>\r\n"; ?>      <?php } ?>
      
      <?php echo "</tr>\r\n";; ?>    <?php } ?>    <tr align="center" bgcolor="#FDFDEA"> 
      <td height="24" colspan="4">
      	&nbsp; 
        <input name="b1" type="button" id="b1" onClick="SelAll()" value="全选" /> 
        &nbsp;
        <input name="b2" type="button" id="b2" onClick="ReSel()" value="反选" /> 
        &nbsp;
        <input name="b3" type="button" id="b3" onClick="NoneSel()" value="取消" />
     </td>
    </tr>
	  <tr bgcolor="#F7F8ED"> 
      <td height="24" colspan="4" valign="top">
      	<strong>附加参数：</strong>
      </td>
    </tr>
    <tr  bgcolor="#FFFFFF"> 
      <td height="24" colspan="4"> 
        <input name="structfile" type="checkbox" class="np" id="structfile" value="<?php echo $structfile;?>" checked='1' />
        还原表结构信息(<?php echo $structfile;?>) 
        <input name="delfile" type="checkbox" class="np" id="delfile" value="1" />
        还原后删除备份文件 (谨慎操作)</td>
    </tr>
    <tr bgcolor="#E3F4BB"> 
      <td height="33" colspan="4">
      	 &nbsp; 
      	 <input type="submit" name="Submit" value="开始还原数据" class="coolbg np" />
      </td>
    </tr>
  </form>
  <tr bgcolor="#F7F8ED">
    <td height="24" colspan="6"><strong>进行状态：</strong></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="180" colspan="6">
	<iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
	</td>
  </tr>
</table>
<?php } else { ?>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#D1DDAA">
  <tr>
    <td height="19" colspan="6" background="img/tbg.gif" bgcolor="#E7E7E7">
    	<table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr>
          <td width="24%"><strong>数据库管理</strong></td>
          <td width="76%" align="right">
          	<b><a href="<?php echo $php_self;?>?m=data.back"><u>数据备份</u></a></b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <form name="form1" onSubmit="checkSubmit()" action="<?php echo $php_self;?>?m=data.delete" method="post">
    <input type='hidden' name='bakfiles' value='' />  
    <tr bgcolor="#F7F8ED"> 
      <td height="24" colspan="4" valign="top">
      	<strong>发现的备份日期：		请选择一个要还原的日期</strong>
        <?php if(count($filelists)==0 ) { ?> 没找到任何备份文件... <?php } ?>
      </td>
    </tr>
    <?php for($i=0;$i<count($filelists);$i++){  ?>    <?php echo "<tr  bgcolor='#FFFFFF' align='center' height='24'>\r\n";
      $mtd = "<td width='10%'>
             <input name='bakfile' id='bakfile' type='checkbox' class='np' value='".$filelists[$i]."'/> 
             </td>
             <td width='40%'><a href='$php_self?m=data.revertlist&path=$filelists[$i]'>$filelists[$i]</a></td>\r\n";
      echo $mtd;
      ; ?>      
      <?php if(isset($filelists[$i+1]) ) { ?>
      <?php $i++;
      $mtd = "<td width='10%'>
             <input name='bakfile' id='bakfile' type='checkbox' class='np' value='".$filelists[$i]."' /> 
             </td>
              <td width='40%'><a href='$php_self?m=data.revertlist&path=$filelists[$i]'>$filelists[$i]</a></td>\r\n";
        echo $mtd;
       } else { ?>
      <?php echo "<td></td><td></td>\r\n"; ?>      <?php } ?>
      
      <?php echo "</tr>\r\n";; ?>    <?php } ?>    <tr align="center" bgcolor="#FDFDEA"> 
      <td height="24" colspan="4">
      	&nbsp; 
        <input name="b1" type="button" id="b1" onClick="SelAll()" value="全选" /> 
        &nbsp;
        <input name="b2" type="button" id="b2" onClick="ReSel()" value="反选" /> 
        &nbsp;
        <input name="b3" type="button" id="b3" onClick="NoneSel()" value="取消" />
     </td>
    </tr>


    <tr bgcolor="#E3F4BB"> 
      <td height="33" colspan="4">
      	 &nbsp; 
      	 <input type="submit" name="Submit" value="删除" class="coolbg np" onclick="javascript:if (confirm('您确定要删除吗？注意：此操作不可恢复，请谨慎操作！')){return true;} return false;"/>
      </td>
    </tr>
</form>

</table>
<?php } ?>
</body>
</html>