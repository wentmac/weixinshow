<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{STATIC_URL}js/tools.js"></script>
<script type="text/javascript" src="{$BASE_V}config.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<!--{if $action == 'add' }-->
<h2>添加系统配置参数新变量</h2>
<form name="modform" id="forms" action="{PHP_SELF}?m=config.save" method="post"  onSubmit="return chkForm();">    
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
        <td width="100" class="td_right">变量名称：</td>
        <td class="td_left"><input type="text" size="40" value="{$editinfo->varname}" id="nvarname" name="nvarname">
         </td>
      </tr>
       <tr>
        <td width="100" class="td_right">变量值：</td>
        <td class="td_left"><textarea name="nvarvalue" id="nvarvalue" rows="6" cols="62">$editinfo->value</textarea>
         </td>
      </tr>
      <tr>
        <td class="td_right">变量类型：</td>
        <td class="td_left">
        $vartype_ary_radio
        <div style="display:{if $editinfo->type == 'select' || $editinfo->type == 'radio'}block{else}none{/if};" id="changetype">nameacton：<input type="text" size="60" value="{$editinfo->nameaction}" id="nameaction" name="nameaction">&nbsp;acton事件 选填<br />
        数据值：<textarea name="item" id="item" cols="52" rows="5" style="margin-top:10px">$editinfo->item</textarea>&nbsp;两数据之间用{|}来分隔
        </div>
        </td>
      </tr>
      <tr>
        <td class="td_right">参数说明：</td>
        <td class="td_left"><input type="text" value="{$editinfo->info}" id="varmsg" name="varmsg" size="80">
          </td>
      </tr>
      
      <tr>
        <td class="td_right">参数说明帮助：</td>
        <td class="td_left"><input type="text" value="{$editinfo->help}" id="help" name="help" size="80">
          </td>
      </tr>      
      
      <tr>
        <td class="td_right">排序：</td>
        <td class="td_left"><input type="text" value="{$editinfo->order}" id="order" name="order" size="10">
          </td>
      </tr>            
     
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
	      <input type="hidden" value="{$editinfo->sys_id}" name="sysid">
          <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
          <input type="reset" class="btn05"  onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" value="清除" name="reset_button">
          <input type="button" value="返回"  onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton"></td>
      </tr>
    
    </tbody></table>
</form>    
<!--{/if}-->
    
<!--{if $action == 'index' }-->

<h2>修改网站设置</h2>
<form name="list_form" method="POST" action="{PHP_SELF}?m=config.savelist" onSubmit="return fillcitylist();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
          <tr>
            <td width="20%">参数说明</td>
            <td width='60%'>参数值</td>
            <td width="20%">变量名</td>                
          </tr>  
      
      {loop $rs $k $v}
          <tr>
            <td class="td_right">{$v->info}：</td>
            <td class="td_left">
            {if $v->type == 'string'}<input type="text" size="40" value="{$v->value}" id="value" name="$v->varname">
            {elseif $v->type == 'bstring'}<textarea name="{$v->varname}" id="nvarvalue" rows="4" cols="62">$v->value</textarea>
            {elseif $v->type == 'select'}$v->select
            {elseif $v->type == 'radio'}$v->radio
            {/if}
    		$v->help
            </td>
            <td>$v->varname</td>                
          </tr>  
      {/loop}      
          <tr>
              <td class="td_right">&nbsp;</td>
              <td class="td_left" colspan="2">
              <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
              <input type="button" value="返回" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'"></td>
          </tr>          
      </tbody>
	</table>
      </form>

      
<!--{/if}-->
	</div>
</div>
</body>
</html>