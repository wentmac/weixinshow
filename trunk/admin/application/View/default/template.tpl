<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>酒店分销联盟管理系统</title>
<script type="text/javascript" src="{STATIC_URL}js/tools.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="{$BASE_V}manage.js"></script>
<style>
.template ul{ float:left}
.template li{ text-align:left; line-height:25px}
.template .spanright{ float:left;width:auto}
.template .spanleft{ float:left; margin-right:10px; color:#F00}
</style>
</head>
<body>

<div style="z-index: 1; left: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display:none ;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<!--{if $action == 'index' }-->
<h2>当前模板</h2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
<tr>
        <td width="250" align="center"><img src="{$style_now[screenshot]}" id="screenshot"></td>
        <td valign="center" class="template">
        <ul>
          <li><span class="spanleft">模板名称:</span><strong><span class="span_right" id="style_name">$style_now[name]</span></strong></li>
          <li><span class="spanleft">版 本 号:</span><span class="span_right" id="style_version">$style_now[version]</span></li>
          <li><span class="spanleft">模板说明:</span><a target="_blank" id="style_uri" href="{$style_now[uri]}">$style_now[desc]</a></li>
          <li><span class="spanleft">模板作者:</span><a target="_blank" id="style_author_uri" href="{$style_now[author_uri]}">$style_now[author]</a></li>
          <li><span class="spanleft">模板目录:</span><span class="span_right" id="style_code">$style_now[code]</span></li>
        </ul>
        </td></tr>
      </tbody></table>
<h2>可用模板</h2>
<div style="margin-left:10px; float:left">
<!--{loop $info $k $v}-->
<div style="display:-moz-inline-stack;display:inline-block;vertical-align:top;zoom:1;*display:inline;">
    <table style="width: 220px;">
      <tbody><tr>
        <td><strong><a target="_blank" href="{$v[uri]}">$v[name]</a></strong></td>
      </tr>
      <tr>
        <td><img border="0" onclick="javascript:setupTemplate('{$v[code]}')" id="default" style="cursor:pointer; float:left; margin:0 2px;display:block;" src="{$v[screenshot]}" title="点击设置({$v[code]})为默认模板风格"></td>
      </tr>
      <tr>
        <td valign="top">$v[desc]</td>
      </tr>
    </tbody></table>
    </div>     
<!--{/loop}-->
</div>

<script language="javascript">
jq = jQuery.noConflict(); 
function setupTemplate(code){
	var r = confirm('您确定要启用选定的模板吗？');
	jq('#loading').attr('display','block');
	if(r==true)
	{
		jq.post("{PHP_SELF}?m=template.ajaxSaveDefaultTemplate", {template_dir:code},
			function(data){
				var obj=eval("("+data+")");//转换为json对象 			
				var error = obj.error;				
				if(error != null){
					alert(obj.error);
					return false;
				}
				var success = obj.success;				
				var screenshot = obj.style_now.screenshot;
				var code = obj.style_now.code;
				var name = obj.style_now.name;		
				var uri = obj.style_now.uri;
				var desc = obj.style_now.desc;
				var version = obj.style_now.version;
				var author = obj.style_now.author;
				var author_uri = obj.style_now.author_uri;
				alert(success);
				jq('#loading').attr('display','none');
				jq('#screenshot').attr('src',screenshot);
				jq('#style_name').html(name);
				jq('#style_version').html(version);	
				jq('#style_uri').attr('href',uri);			
				jq('#style_uri').html(desc);	
				jq('#style_author_uri').attr('href',author_uri);				
				jq('#style_author_uri').html(author);	
				jq('#style_code').html(code);

		});	
	}
	
}
</script>   
<!--{/if}-->


<!--{if $action == 'edit' }-->
<h2>模板源文件修改</h2>
<div style="margin-left:10px; float:left">
<!--{loop $info $k $v}-->
<div style="display:-moz-inline-stack;display:inline-block;vertical-align:top;zoom:1;*display:inline;">
    <table style="width: 220px;">
      <tbody><tr>
        <td><strong><a target="_blank" href="{$v[uri]}">$v[name]</a></strong></td>
      </tr>
      <tr>
        <td><a href="{PHP_SELF}?m=template.temlist&dir={$v[code]}" style="color:#09F"><img border="0" id="default" style="cursor:pointer; float:left; margin:0 2px;display:block;" src="{$v[screenshot]}" title="点击设置({$v[code]})为默认模板风格"></a></td>
      </tr>
      <tr>
        <td valign="top">$v[desc]</td>
      </tr>
      <tr>
        <td valign="top"><a href="{PHP_SELF}?m=template.temlist&dir={$v[code]}" style="color:#09F">模板文件修改</a> | <a href="{PHP_SELF}?m=template.stylelist&dir={$v[code]}" style="color:#09F">CSS样式文件修改</a></td>
      </tr>      
    </tbody></table>
    </div>     
<!--{/loop}-->
</div>
<!--{/if}-->

<!--{if $action == 'temlist' }-->
    <h2>{$dir}目录模板文件列表（{$relative_tmp_dir}）<a href="{PHP_SELF}?m=template.edit" style="color:#F00">返回模板列表</a></h2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th width="60%"></th>
        <th width="30%">最后修改时间</th>
        <th width="10%">管理</th>
      </tr>
	  <!--{if $ErrorMsg}-->
	  <tr>
	    <td height=23 colspan=3 class=forumRowHigh align=center>$ErrorMsg</td>
	  </tr>
	  <!--{/if}-->     
      $filelists 
	  <tr>
	    <td height=23 colspan=3 class=forumRowHigh align=center><a href="{PHP_SELF}?m=template.edit" style="color:#F00">返回模板列表</a></td>
	  </tr>
      </tbody></table>

<!--{/if}-->   

<!--{if $action == 'show' }-->
    <h2>{$dirname}目录模板文件列表  >> <a href="{PHP_SELF}?m=template.temlist&dir={$dirname}" style="color:#F00">返回模板列表</a></h2>
<form name="forms" id="forms" action="{PHP_SELF}?m=template.showsave" method="post"  onSubmit="return chkFormTemplate();">    
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <td width="100%" class="td_left" style="font-size:14px; line-height:30px; font-weight:bold; color:#0099CC; margin-left:-10px">编辑模板 - 模板文件名：{$dir} - 最后修改时间：{$edittime}</td>
      </tr>

	  <tr>
	    <td><textarea name="info" id="info" style="width:99%;height:450px" rows="24" cols="150">$template_file_info</textarea></td>
	  </tr>
      
      
      <tr>
        <td class="td_left">
          <input type="hidden" name="dir" value="$dir" />
          <input type="hidden" name="dirname" id="dirname" value="$dirname" />             
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="重置" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
      </tr>      
      </tbody></table>
</form>
<script language="javascript">
// JavaScript Document
function chkFormTemplate()
{
	if($('info').value == ""){
	   alert("对不起，模板的内容不能为空！");   
	   $('info').focus();
	   return(false);
   	}
}
</script>
<!--{/if}-->   

<!--{if $action == 'stylelist' }-->
    <h2>{$dir}目录样式文件列表（{$relative_tmp_dir}）<a href="{PHP_SELF}?m=template.edit" style="color:#F00">返回模板样式列表</a></h2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th width="60%"></th>
        <th width="30%">最后修改时间</th>
        <th width="10%">管理</th>
      </tr>
	  <!--{if $ErrorMsg}-->
	  <tr>
	    <td height=23 colspan=3 class=forumRowHigh align=center>$ErrorMsg</td>
	  </tr>
	  <!--{/if}-->     
      $filelists 
	  <tr>
	    <td height=23 colspan=3 class=forumRowHigh align=center><a href="{PHP_SELF}?m=template.edit" style="color:#F00">返回模板样式列表</a></td>
	  </tr>
      </tbody></table>

<!--{/if}-->   

<!--{if $action == 'showstyle' }-->
    <h2>{$dirname}目录新式文件列表  >> <a href="{PHP_SELF}?m=template.stylelist&dir={$dirname}" style="color:#F00">返回样式列表</a></h2>
<form name="forms" id="forms" action="{PHP_SELF}?m=template.showstylesave" method="post"  onSubmit="return chkFormTemplate();">    
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <td width="100%" class="td_left" style="font-size:14px; line-height:30px; font-weight:bold; color:#0099CC; margin-left:-10px">编辑样式 - 样式文件名：{$dir} - 最后修改时间：{$edittime}</td>
      </tr>

	  <tr>
	    <td><textarea name="info" id="info" style="width:99%;height:450px" rows="24" cols="150">$template_file_info</textarea></td>
	  </tr>
      
      
      <tr>
        <td class="td_left">
          <input type="hidden" name="dir" value="$dir" />
          <input type="hidden" name="dirname" id="dirname" value="$dirname" />             
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="重置" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
      </tr>      
      </tbody></table>
</form>
<script language="javascript">
// JavaScript Document
function chkFormTemplate()
{
	if($('info').value == ""){
	   alert("对不起，模板的内容不能为空！");   
	   $('info').focus();
	   return(false);
   	}
}
</script>
<!--{/if}-->   
	</div>
</div>
</body>
</html>