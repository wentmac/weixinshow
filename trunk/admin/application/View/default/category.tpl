<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{STATIC_URL}js/tools.js"></script>
<script type="text/javascript" src="{$BASE_V}category.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<style>
.fenlei{text-align:left; text-indent:20px}
</style>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">
    <h2>添加/修改分类{if $action == 'index'}｜<a class="cursor" onclick="open_cat()">全部展开</a>｜<a class="cursor" onclick="close_cat()">全部收缩</a>{/if}</h2>    
<!--{if $action == 'add' }-->    
    <form method="post" action="{PHP_SELF}?m=category.save" onSubmit="return checkForm(this);">
    <input type="hidden" value="{$editinfo->cat_id}" name="cat_id" id="cat_id">
    <table width="100%" cellspacing="1" cellpadding="3" border="0" align="center" class="t_list"> 
    <tbody>
		<tr>
        	<td class="td_right_f00" width="15%" height="30">分类名称</td>
			<td class="td_left" width="25%"> <input type="text" size="35" name="cat_name" id="cat_name" value="{$editinfo->cat_name}"></td>
            <td class="td_left hui" width="60%">这将是它在站点上显示的名字。</td>
        </tr>
           
		<tr>
        	<td class="td_right">内容模型</td>
			<td class="td_left">
            <select name="channeltype" id="channeltype">
            $channeltype_option
            </select>
            <td class="td_left hui">栏目模型分类(article,image)</td>
        </tr>   
                   
		<tr>
        	<td class="td_right">别名</td>
			<td class="td_left"> <input type="text" size="35" name="category_nicename" id="category_nicename" value="{$editinfo->category_nicename}"></td>
            <td class="td_left hui">"别名"是对于 URL 友好的一个别称。它通常为小写并且只能包含字母，数字和连字符（-）。</td>
        </tr>  
        
		<tr>
        	<td class="td_right">父级</td>
			<td class="td_left">
            <select name="cat_pid" id="cat_pid">
    			<option value="0">|-根分类</option>
				{$category_tree_list}
		    </select>
            </td>
            <td class="td_left hui">分类目录，和标签不同，它可以有层级关系。</td>
        </tr>   
        
		<tr>
        	<td class="td_right">关键字</td>
			<td class="td_left"> <input type="text" size="35" name="category_keywords" id="category_keywords" value="{$editinfo->category_keywords}"></td>
            <td class="td_left hui">keywords</td>
        </tr>

		<tr>
        	<td class="td_right">描述</td>
			<td class="td_left" colspan="2"><textarea id="category_description" rows="4" style="height:50px" cols="70" name="category_description">{$editinfo->category_description}</textarea> description</td>
        </tr>  
        
		<tr>
        	<td class="td_right">内容</td>
			<td class="td_left" colspan="2"><textarea name="category_content" id="category_content" rows="60" cols="100" class="xheditor editor" style="height:270px">$editinfo->category_content</textarea><br />&nbsp;&nbsp;通常用于企业简介之类的单页面用途</td>			
        </tr>          
        
        
		<tr>
        	<td class="td_right">分类排序</td>
			<td class="td_left"><input type="text" size="5" name="cat_order" id="cat_order" value="{$editinfo->cat_order}"></td>
            <td class="td_left hui">数字越大的排第一</td>
        </tr>
        
		<tr>
        	<td class="td_right">前台链接文件</td>
			<td class="td_left"><input type="text" size="10" name="urlfile" id="urlfile" value="{$editinfo->urlfile}"></td>
            <td class="td_left hui">没有特别需求,请不用填写这里</td>
        </tr>
		
		<tr>
        	<td class="td_right">前台导航条显示</td>
			<td class="td_left">
            <select name="nav_show" id="nav_show">
            $nav_show_array_option
            </select>
            <td class="td_left hui">是否在前台导航条中显示</td>
        </tr>  		
                                                     
    <tr>
        <td class="td_left" colspan="3"> <input type="submit" value="$button" class="btn02" name="Submit"></td>
    </tr>
    </tbody>
    </table>
    </form>
<script type="text/javascript" src="{STATIC_URL}js/xheditor/xheditor-1.1.14-zh-cn.min.js"></script>
<script language="javascript">
jq = jQuery.noConflict(); 
//以后jquery中的都用jq代替即可。 
jq('#category_content').xheditor({upImgUrl:"{PHP_SELF}?m=tool.uploadImg",upImgExt:"jpg,jpeg,gif,png"});
</script>      
<!--{/if}-->
    
<!--{if $action == 'index' }-->
    <table width="100%" cellspacing="1" cellpadding="2" align="center" class="t_list">
        <tbody>
            <tr>
                <th width="20%"><strong>名称</strong></th>
                <th width="10%"><strong>别名</strong></th>
                <th width="45%"><strong>描述</strong></th>
                <th width="35%"><strong>操作</strong></th>
            </tr>
            <tr>            
                <td colspan="4" style="border-bottom:0px">
                {$category_list}
                </td>
			</tr>
        </tbody>
    </table>
<script language="javascript">
jq = jQuery.noConflict(); 
jq('.cate img').click(function(){
	var cate_ul = jq(this).parent().parent().parent().next("ul");	
	if(cate_ul.css('display')=='block') {
		cate_ul.hide('normal');		
		jq(this).parent().children("img").attr("src","{$BASE_V}images/add.gif");
	} else {
		cate_ul.show('normal');		
		jq(this).parent().children("img").attr("src","{$BASE_V}images/desc.gif");					
	}
});

jq(function(){
	jq(".category_list dl").hover(
	   function(){
		   jq(this).addClass("category_list_bg");
	   }, 
	   function (){
		   jq(this).removeClass("category_list_bg");
	   }
	);
})
</script>
<!--{/if}-->
	</div>
</div>   

</body>
</html>
<script language="javascript">
/* 如果需要默认全部闭合时
jq(function(){
   jq('.category_list li ul').each(function(){
	  jq(this).css({display:'none'});
   });
});
*/
function close_cat()
{
   jq('.category_list li').each(function(){	  
  	  jq(this).find('ul').hide('normal');	  
  	  jq(this).find('.cate img').attr("src","{$BASE_V}images/add.gif");
   });
}

function open_cat()
{
   jq('.category_list li').each(function(){
		jq(this).find('ul').show('normal');	  
  	  jq(this).find('.cate img').attr("src","{$BASE_V}images/desc.gif");
   });
}
</script>