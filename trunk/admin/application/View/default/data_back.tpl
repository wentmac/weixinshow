<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>数据库维护-数据备份</title>
<style>
body{ font-size:12px}
</style>
<script language="javascript">
//获得选中文件的数据表

function getCheckboxItem(){
	 var myform = document.form1;
	 var allSel="";
	 if(myform.tables.value) return myform.tables.value;
	 for(i=0;i<myform.tables.length;i++)
	 {
		 if(myform.tables[i].checked){
			 if(allSel=="")
				 allSel=myform.tables[i].value;
			 else
				 allSel=allSel+","+myform.tables[i].value;
		 }
	 }
	 return allSel;
}

//反选
function ReSel(){
	var myform = document.form1;
	for(i=0;i<myform.tables.length;i++){
		if(myform.tables[i].checked) myform.tables[i].checked = false;
		else myform.tables[i].checked = true;
	}
}

//全选
function SelAll(){
	var myform = document.form1;
	for(i=0;i<myform.tables.length;i++){
		myform.tables[i].checked = true;
	}
}

//取消
function NoneSel(){
	var myform = document.form1;
	for(i=0;i<myform.tables.length;i++){
		myform.tables[i].checked = false;
	}
}

function checkSubmit()
{
	var myform = document.form1;
	myform.tablearr.value = getCheckboxItem();
	return true;
}

</script>
</head>

<body>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#D1DDAA">
  <tr>
    <td height="19" colspan="6" background="img/tbg.gif" bgcolor="#E7E7E7">
    	<table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr>
          <td width="24%"><strong>数据库管理</strong></td>
          <td width="76%" align="right">
          	<b><a href="{PHP_SELF}?m=data.revert"><u>数据还原</u></a></b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <form name="form1" onSubmit="checkSubmit()" action="{PHP_SELF}?m=data.dobak" method="post" target="stafrm">
  <input type='hidden' name='tablearr' value='' />
  <tr bgcolor="#F7F8ED">
    <td height="24" colspan="6"><strong>{$dname}数据库 默认数据表：</strong></td>
  </tr>
  <tr bgcolor="#F2FFB5" align="center">
    <td height="24" width="5%">选择</td>
    <td width="20%">表名</td>
    <td width="8%">记录数</td>
    <td width="5%">选择</td>
    <td width="20%">表名</td>
    <td width="8%">记录数</td>
  </tr>
  <!--{eval 
  for($i=0; isset($tableAry[$i]); $i++)
  {
    $t = $tableAry[$i]['tname'];
    echo "<tr align='center'  bgcolor='#FFFFFF' height='24'>\r\n";
  }-->
    <td>
    	<input type="checkbox" name="tables" value="$t" class="np" checked />
    </td>
    <td>
      $t
    </td>
    <td>
    {echo $tableAry[$i]['tcount'];}
    </td>
  <!--{eval
   $i++;
   if(isset($tableAry[$i]['tname'])) {
    $t = $tableAry[$i]['tname'];
  }-->
    <td>
    	<input type="checkbox" name="tables" value="{echo $t}" class="np" checked />
    </td>
    <td>
      {echo $t}
    </td>
    <td>
    {echo $tableAry[$i]['tcount'];}
    </td>

  <!--{eval
   }
   else
   {
   	  echo "<td></td><td></td><td></td>\r\n</tr>\r\n";
  }-->
  
  <!--{eval  }
  }-->
    <!--{eval  }
  }-->
  
    <tr bgcolor="#FDFDEA">
      <td height="24" colspan="6">
      	&nbsp;
        <input name="b1" type="button" id="b1" class="coolbg np" onClick="SelAll()" value="全选" />
        &nbsp;
        <input name="b2" type="button" id="b2" class="coolbg np" onClick="ReSel()" value="反选" />
        &nbsp;
        <input name="b3" type="button" id="b3" class="coolbg np" onClick="NoneSel()" value="取消" />
      </td>
  </tr>
  <tr bgcolor="#F7F8ED">
    <td height="24" colspan="6"><strong>数据备份选项：</strong></td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF">
    <td height="50" colspan="6">
    	  <table width="90%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="30">当前数据库版本： {$mysql_version}</td>
          </tr>
          <tr>
            <td height="30">
            	指定备份数据格式：
              <input name="datatype" type="radio" class="np" value="4.0" />
              MySQL3.x/4.0.x 版本
              <input type="radio" name="datatype" value="4.1" class="np" checked='1' />
              MySQL4.1.x/5.x 版本
              </td>
          </tr>
          
          <tr>
            <td height="30">
            	分卷大小：
              <input name="fsize" type="text" id="fsize" value="1024" size="6" />
              K&nbsp;，
              <input name="isstruct" type="checkbox" class="np" id="isstruct" value="1" checked='1' />
               备份表结构信息
              <input type="submit" name="Submit" value="提交" class="coolbg np" />
             </td>
          </tr>
        </table>
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
</body>
</html>