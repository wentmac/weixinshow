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
    <h2>添加/修改分类{if $action == 'index'}｜<a class="cursor" onclick="open_cat()">全部展开</a>｜<a class="cursor" onclick="close_cat()">全部收缩</a>｜<a href="{PHP_SELF}?m=goods/category.update_goods_count">更新分类商品量</a>{/if}</h2>    
<!--{if $action == 'add' }-->    
    <form method="post" action="{PHP_SELF}?m=goods/category.save" onSubmit="return checkForm(this);">
    <input type="hidden" value="{$editinfo->goods_cat_id}" name="goods_cat_id" id="goods_cat_id">
    <table width="100%" cellspacing="1" cellpadding="3" border="0" align="center" class="t_list"> 
    <tbody>
		<tr>
        	<td class="td_right_f00" width="15%" height="30">分类名称</td>
			<td class="td_left" width="25%"> <input type="text" size="35" name="cat_name" id="cat_name" value="{$editinfo->cat_name}"></td>
            <td class="td_left hui" width="60%">这将是它在站点上显示的名字。</td>
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
			<td class="td_left"> <input type="text" size="35" name="cat_keywords" id="cat_keywords" value="{$editinfo->cat_keywords}"></td>
            <td class="td_left hui">keywords</td>
        </tr>

		<tr>
        	<td class="td_right">描述</td>
			<td class="td_left" colspan="2"><textarea id="cat_description" rows="4" style="height:50px" cols="70" name="cat_description">{$editinfo->cat_description}</textarea> description</td>
        </tr>  
        
		<tr>
        	<td class="td_right">分类排序</td>
			<td class="td_left"><input type="text" size="5" name="cat_sort" id="cat_sort" value="{$editinfo->cat_sort}"></td>
            <td class="td_left hui">数字越大的排第一</td>
        </tr>
		
		<tr>
        	<td class="td_right">是否云端商品库分类</td>
			<td class="td_left" colspan="2">
			<select name="is_cloud_product">
			{$is_cloud_product_option}
			</select>
			</td>            
        </tr>
                                                     
    <tr>
        <td class="td_left" colspan="3"> <input type="submit" value="$button" class="btn02" name="Submit"></td>
    </tr>
    </tbody>
    </table>
    </form>
<!--{/if}-->
    
<!--{if $action == 'index' }-->
    <table width="100%" cellspacing="1" cellpadding="2" align="center" class="t_list">
        <tbody>
            <tr>
                <th width="10%"><strong>名称</strong></th>
                <th width="20%"><strong>是否上架云端商品库分类</strong></th>                
                <th width="30%"><strong>别名</strong></th>                
                <th width="40%"><strong>操作</strong></th>
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
/* 如果需要默认全部闭合时*/
jq(function(){
   jq('.category_list li ul').each(function(){
	  jq(this).css({display:'none'});
   });
});

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