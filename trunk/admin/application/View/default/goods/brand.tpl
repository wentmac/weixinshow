<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{BASE}js/tools.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">
<!--{if $action == 'add' }-->
    <h2>添加品牌</h2>
<form name="forms" id="forms" action="{PHP_SELF}?m=goods/brand.save" method="post"  onSubmit="return chkForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <td class="td_right_f00" width="200">品牌名称：</td>
        <td class="td_left" colspan="2"><input type="text" size="35" value="{$editinfo->brand_name}" id="brand_name" name="brand_name"></td>
      </tr>
      <tr>
        <td class="td_right" width="200">品牌网址：</td>
        <td class="td_left" colspan="2"><input type="text" size="35" value="{$editinfo->site_url}" id="site_url" name="site_url"></td>
      </tr>
      <tr>
        <td class="td_right" width="200">品牌描述：</td>
        <td class="td_left" colspan="2"><textarea cols="50" rows="10" name="brand_desc" id="brand_desc">{$editinfo->brand_desc}</textarea></td>
      </tr>
      <tr>
		  <td class="td_right" width="200">排序：</td>
		  <td class="td_left" colspan="2"><input type="text" size="35" value="{$editinfo->sort_order}" id="sort_order" name="sort_order"></td>
      </tr>

      <tr>
          <td class="td_right">是否显示：</td>
          <td class="td_left">{$is_delete_radio}</td>
      </tr>

      <tr>
          <td class="td_right">首页推荐：</td>
          <td class="td_left">
		  <select name="recommend_index" id="recommend_index">
		  {$recommend_index_option}
		  </select>
		  </td>
      </tr>
	  
      <tr>
          <td class="td_right">类目推荐：</td>
          <td class="td_left">
		  <select name="recommend_category" id="recommend_category">
		  {$recommend_category_option}
		  </select>
		  </td>
      </tr>
	  
      <tr>
          <td class="td_right">品牌所属分类：</td>
          <td class="td_left">
		  <select name="goods_cat_id" id="goods_cat_id">
			<option value=0>-无-</value>
		  {$goods_category_option}
		  </option>
		  </td>
      </tr>
	  
      <tr>
        <td class="td_right">品牌LOGO：</td>
        <td class="td_left">
        <span id="thumb_preview">{if $editinfo->photo_url != ''}<img src="{$editinfo->photo_url}" width="150" height="120" style="margin-bottom:6px">{/if}</span>
    <br>
    地址：<input size="80" value="{$editinfo->photo_url}" id="thumb" name="thumb"><br>
    上传：<input type="file" onchange="image_preview('thumb',this.value,1)" style="width: 400px;" id="thumb_upload" name="thumb_upload">
    &nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload','{PHP_SELF}?m=tool.uploadImageByAjax&filename=thumb_upload&action=brand','#thumb_loading', 'thumb', 'thumb_preview', 'brand_logo');" id="thumbupload" name="thumbupload">
    <img style="display:none;" src="{STATIC_URL}js/loading.gif" id="thumb_loading">
    <input type="hidden" name="brand_logo" id="brand_logo" value="{$editinfo->brand_logo}"/>
    </td>
      </tr>
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
          <input type="hidden" name="brand_id" value="{$editinfo->brand_id}" />
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
      </tr>

    </table>
</form>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ajaxfileupload.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ThumbAjaxFileUpload.js"></script>
<script language="javascript">
jq = jQuery.noConflict();
function chkForm()
{
	if($('brand_name').value == ""){
	   alert("请填写品牌名称！");
	   $('brand_name').focus();
	   return false;
   	}
}
</script>
<!--{/if}-->

<!--{if $action == 'index' }-->
<script language="javascript">
	function checkDelForm(){
		var check = GetCheckboxValue('id_a[]');
		var list_do = $('#do').value;

		if( list_do == '0' )
		{
			alert("好像您没有选择任何管理操作吧?:-(");
			document.getElementById('do').focus();
			return false;
		}
		if( check == '')
		{
			alert("好像您没有选择任何要操作的评论吧?:-(");
			return false;
		}
	}
</script>
<h2>品牌列表</h2>
<form method="GET" action="{PHP_SELF}" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>
	<td align="center">
    品牌名称 :
	 <input type="text" name="search_keyword" value="{$search_keyword}">　
	<input type="hidden" name="m" value="goods/brand"/>
	<input type="submit" name="search_btn" value="　搜索　">
	</td>
  </tr>
</table>
</form>

<form name="list_form" method="POST" action="{PHP_SELF}?m=goods/brand.operate&page={$pageCurrent}" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="2%">编号</th>
        <th width="25%">品牌名称</th>
        <th width="25%">品牌网站URL</th>
        <th width="13%">排序</th>
        <th width="10%">是否显示</th>
        <th width="13%">品牌所属分类</th>
        <th width="10%">管理</th>
      </tr>
	  <!--{if $ErrorMsg}-->
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center>$ErrorMsg</td>
	  </tr>
	  <!--{/if}-->
      <!--{loop $rs $k $v}-->
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">

      <td><input type="checkbox" value="{$v->brand_id}" name="id_a[]"></td>
      <td>$v->brand_id</td>
      <td>$v->brand_name</td>
      <td>$v->site_url</td>
      <td>$v->sort_order</td>      
      <td>$v->is_delete</td>
	  <td>$v->cat_name</td>
      <td><a href="{PHP_SELF}?m=goods/brand.add&bid={$v->brand_id}">修改</a> | <a href="{PHP_SELF}?m=goods/brand.operate&action=del&bid={$v->brand_id}" onclick="{if(confirm('删除将包括该信息，确定删除吗?')){return true;}return false;}">删除</a></td>
      </tr>
      <!--{/loop}-->
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td><select name="do" id='do'>$list_do_ary_option</select></td>
              <td class="td_left" colspan="7">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="确定" name="Submit">
      </td>
    </tr>
      </tbody></table>
      </form>
      {$page}

<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<!--{/if}-->
	</div>
</div>
</body>
</html>