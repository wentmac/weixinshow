<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{$BASE}js/tools.js"></script>
<script type="text/javascript" src="{$BASE_V}update.js"></script>
<style>
.coolbg  {
    background: url("{$BASE_V}images/allbtbg2.gif") repeat scroll 0 0 #EFF7D0;
    border-color: -moz-use-text-color #ACACAC #ACACAC -moz-use-text-color;
    border-style: none solid solid none;
    border-width: medium 1px 1px medium;
    cursor: pointer;
    padding: 2px 5px;
}

.np {
    border: medium none;
}
.upinfotitle {
    border-bottom: 1px solid #CCCCCC;
    color: red;
    font-weight: bold;
    line-height: 26px;
}
.fup{ width:500px}
</style>
</head>
<body>

<div style="z-index: 1; text-align:center; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<!--{if $action == 'add' }-->
    <h2>修改广告</h2>
<form name="modform" id="forms" action="{PHP_SELF}?m=ad.save" method="post"  onSubmit="return chkForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
       <tbody><tr>
        <td width="100" class="td_right">位置：</td>
        <td class="td_left"><input type="text" size="40" value="$editinfo[ad_area]" id="area" name="area">
         </td>
      </tr>
      <tr>
        <td width="100" class="td_right">标题：</td>
        <td class="td_left"><input type="text" size="40" value="$editinfo[ad_title]" id="title" name="title">
         </td>
      </tr>
        <tr>
        <td width="100" class="td_right">广告大小：</td>
        <td class="td_left">宽：<input type="text" size="10" value="$editinfo[ad_width]" id="sizewidth" name="sizewidth">&nbsp;&nbsp;&nbsp;高：<input type="text" size="10" value="$editinfo[ad_height]" id="sizeheight" name="sizeheight">
         </td>
      </tr>
       <tr>
        <td width="100" class="td_right">链接：</td>
        <td class="td_left"><input type="text" size="40" value="$editinfo[ad_link]" id="link" name="link">
         </td>
      </tr>
      <tr>
        <td class="td_right">类型：</td>
        <td class="td_left">
        $ad_type_radio_option       
        <div style="display:{if $editinfo[ad_type_radio] == 2}block{else}none{/if}" id="changetype2">
            <span id="thumb_preview">{if $editinfo[ad_uploadfile] != ''}<img src="$editinfo[ad_uploadfile]" width="150" height="120" style="margin-bottom:6px">{/if}</span>
	<br>
	地址：<input size="50" value="$editinfo[ad_uploadfile]" id="thumb" name="thumb"><br>
	上传：<input type="file" onchange="image_preview('thumb',this.value,1)" style="width: 400px;" id="thumb_upload" name="thumb_upload">
	&nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload','/{PHP_SELF}?m=tool.upload&filename=thumb_upload&action=ad','#thumb_loading', 'thumb');" id="thumbupload" name="thumbupload">
    
		<img style="display:none;" src="{$BASE}js/loading.gif" id="thumb_loading">
        </div>        
        <div style="display:{if $editinfo[ad_type_radio] == 3}block{else}none{/if};" id="changetype3">广告代码：<textarea id="externallinks" rows="5" cols="60" name="externallinks">$editinfo[ad_externallinks]</textarea>&nbsp;&nbsp;当您的密码泄露时此处可能被挂马！</div>
        </td>
      </tr>
      <tr>
        <td class="td_right">排序：</td>
        <td class="td_left"><input type="text" id="order" name="order" value="$editinfo[ad_order]">
          </td>
      </tr>
      <tr>
        <td class="td_right">期限：</td>
        <td class="td_left">
        $ad_state_radio_option

        <div id="changestate" style="display:{if $editinfo[ad_state_radio] == 1}block{else}none{/if};">
        开始日期：<input type="text" id="start_date" name="ad_starttime" value="$editinfo[ad_starttime]" readonly/>
        &nbsp;&nbsp;&nbsp;结束日期：
        <input type="text" id="end_date" name="ad_endtime" value="$editinfo[ad_endtime]" readonly/>
        </div>
        </td>
      </tr>
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left"><input type="hidden" value="$editinfo[ad_id]" name="ad_id">
        <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
          <input type="reset" class="btn05" value="清除" name="reset_button" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'">
          <input type="button" value="返回" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'"></td>
      </tr>
    
    </tbody>
</table>    
</form>
<!--{/if}-->
    
<!--{if $action == 'index' }-->
<h2>联盟系统更新消息</h2>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
    <tr>
        <td colspan="2" id="updateinfos" style="text-align:center">
        <ul style="list-style-type:none">
        	<li><b>你系统版本最后更新时间为：2010-08-18</b>&nbsp;&nbsp;<a href='javascript:LoadUpdateInfos();' class='np coolbg'>进行在线更新</a>&nbsp;</li>			<li><iframe name='stafrm' src='{$upUrl}&uptime={$oktime}' frameborder='0' id='stafrm' width='50%' height='100'></iframe></li>
        </ul>    
        </td>
    </tr>
</table>
<!--{/if}-->  


<!--{if $action == 'getlist' }-->
<h2>联盟系统更新消息</h2>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
    <tr>
        <td colspan="2" style="text-align:left">
        $allFileList
        </td>
    </tr>
</table>
<!--{/if}-->     
	</div>
</div>
</body>
</html>