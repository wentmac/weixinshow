<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{$BASE}js/tools.js"></script>
<script type="text/javascript" src="{$BASE_V}article.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<!--{if $action == 'add' }-->    
    <h2>添加文章</h2>
<form name="forms" id="forms" action="{PHP_SELF}?m=article.save" method="post"  onSubmit="return chkForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <td class="td_right_f00" width="150">标题：</td>
        <td class="td_left" colspan="2"><input type="text" size="100" value="{$editinfo->title}" id="title" name="title">
	  </td>
      </tr>
       <tr>
        <td class="td_right">选择分类：</td>
        <td class="td_left">
            <select name="cat_id" id="cat_id">
    			<option value="0">|-根分类</option>
				{$treers}
		    </select>
        </td>
      </tr>

      <tr>
          <td class="td_right">关键字</td>
          <td class="td_left"> <input type="text" size="35" name="keywords" id="keywords" value="{$editinfo->keywords}"> keywords</td>
      </tr>

      <tr>
          <td class="td_right">描述</td>
          <td class="td_left" colspan="2"><textarea id="description" rows="4" style="height:50px" cols="70" name="description">$editinfo->description</textarea> description</td>
      </tr>        
            
      <tr>
        <td class="td_right">内容：</td>
        <td class="td_left">
		<textarea name="content" id="content" rows="60" cols="100" class="editor">$editinfo->content</textarea>        
		</td>
      </tr>     
      
       <tr>
        <td class="td_right">作者：</td>
        <td class="td_left">
        <select name="uid" id="uid" onchange="changeAuthor(this.value)">
        $user_array_option
        </select>
		</td>
      </tr>
      
       <tr>
        <td class="td_right">状态：</td>
        <td class="td_left">
        <select name="status" id="status">
		$status_array_option
        </select>
		</td>
      </tr>      
      
       <tr>
        <td class="td_right">评论：</td>
        <td class="td_left">
        <select name="comment_status" id="comment_status">
        $comment_status_array_option
        </select>
		</td>
      </tr>                 
            
      <tr>
        <td class="td_right">排序：</td>
        <td class="td_left"><input type="text" value="{$editinfo->article_order}" id="article_order" name="article_order"></td>
      </tr>
      
      <tr>
        <td class="td_right">别名：</td>
        <td class="td_left"><input type="text" value="{$editinfo->name}" id="name" name="name">
         "别名"是对于 URL 友好的一个别称。它通常为小写并且只能包含字母，数字和连字符（-）。
        </td>
      </tr>  
      
      <tr>
        <td class="td_right">文章Tag：</td>
        <td class="td_left"><input type="text" value="添加新Tag" id="new_tag" name="new_tag" style="color:#999" size="30"> <input type="button" id="tag_add_button" value="添加" onclick=""/>
         多个标签请用英文逗号（,）分开。
		<div class="tagchecklist" id="tagchecklist">
        {loop $tag_info_array $k $v}
        <span><a class="ntdelbutton" id="post_tag-check-num-{$v->tag_id}">X</a><p>{$v->tag_name}</p></span>
        {/loop}
        </div>
		<p class="hide-if-no-js"><a id="link-post_tag" class="tagcloud-link" href="#titlediv">从常用标签中选择</a></p>         
		<p class="the-tagcloud" id="tagcloud-post_tag"></p>    
        <p id="tag_array" style="display:none">
        {loop $tag_info_array $k $v}
		<input id="tag_id_{$v->tag_id}" value="{$v->tag_id}" name="tag_id[]"><input id="tag_name_{$v->tag_name}" value="{$v->tag_id}" name="tag_name[]">        
        {/loop}
        </p>     
        </td>
      </tr>            
      
      <tr>
        <td class="td_right">缩略图片：</td>
        <td class="td_left">
        <span id="thumb_preview">{if $editinfo->photo_url != ''}<img src="{$editinfo->photo_url}" width="150" height="120" style="margin-bottom:6px">{/if}</span>
		<br>
		地址：<input size="96" value="{$editinfo->photo_url}" id="thumb" name="thumb"><br>
		上传：<input type="file" onchange="image_preview('thumb',this.value,1)" style="width: 400px;" id="thumb_upload" name="thumb_upload">
		&nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload','{PHP_SELF}?m=tool.uploadImageByAjax&filename=thumb_upload&action=article','#thumb_loading', 'thumb', 'thumb_preview', 'thumb_image_id');" id="thumbupload" name="thumbupload">    
		<img style="display:none;" src="{$BASE}js/loading.gif" id="thumb_loading">
		<input type="hidden" name="thumb_image_id" id="thumb_image_id" value="{$editinfo->thumb}"/>		
		</td>
      </tr>   
      
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
          <input type="hidden" name="aid" value="{$editinfo->article_id}" />
          <input type="hidden" name="author" id="author" value="{$editinfo->author}" />             
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
      </tr>
    
    </table>
</form>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/xheditor/xheditor-1.1.14-zh-cn.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ajaxfileupload.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ThumbAjaxFileUpload.js"></script>
<script language="JavaScript" src="{STATIC_URL}js/jq.date.js"></script>
<script language="javascript">
jq = jQuery.noConflict(); 
//以后jquery中的都用jq代替即可。
var editor;
jq(pageInit);
function pageInit()
{
	var allPlugin={
		moreTag:{c:'xheditor_moretag',t:'文章中插入更多标签',s:'ctrl+8',e:function(){
			var _this=this;			
			this.pasteHTML('&lt;--more--&gt;');
		}},
		Code:{c:'btnCode',t:'插入代码',h:1,e:function(){
			var _this=this;
			var htmlCode='<div><select id="xheCodeType"><option value="html">HTML/XML</option><option value="js">Javascript</option><option value="css">CSS</option><option value="php">PHP</option><option value="java">Java</option><option value="py">Python</option><option value="pl">Perl</option><option value="rb">Ruby</option><option value="cs">C#</option><option value="c">C++/C</option><option value="vb">VB/ASP</option><option value="">其它</option></select></div><div><textarea id="xheCodeValue" wrap="soft" spellcheck="false" style="width:300px;height:100px;" /></div><div style="text-align:right;"><input type="button" id="xheSave" value="确定" /></div>';			var jCode=jq(htmlCode),jType=jq('#xheCodeType',jCode),jValue=jq('#xheCodeValue',jCode),jSave=jq('#xheSave',jCode);
			jSave.click(function(){
				_this.loadBookmark();
				_this.pasteHTML('<pre class="prettyprint lang-'+jType.val()+' linenums">'+_this.domEncode(jValue.val())+'</pre>');
				_this.hidePanel();
				return false;	
			});
			_this.saveBookmark();
			_this.showDialog(jCode);
		}}		
	}
	editor=jq('#content').xheditor({plugins:allPlugin,tools:'full',upLinkUrl:"{PHP_SELF}?m=tool.uploadImg",upLinkExt:"zip,rar,txt",upImgUrl:"{PHP_SELF}?m=tool.uploadImg",upImgExt:"jpg,jpeg,gif,png",upFlashUrl:"{PHP_SELF}?m=tool.uploadImg",upFlashExt:"swf",upMediaUrl:"{PHP_SELF}?m=tool.uploadImg",upMediaExt:"avi",loadCSS:'<style>pre{margin-left:2em;border-left:3px solid #CCC;padding:0 1em;}</style>',}); 
}
//jq('#content').xheditor({upLinkUrl:"Public/uploadfiles/upload.php",upLinkExt:"zip,rar,txt",upImgUrl:"Public/uploadfiles/upload.php",upImgExt:"jpg,jpeg,gif,png",upFlashUrl:"Public/uploadfiles/upload.php",upFlashExt:"swf",upMediaUrl:"Public/uploadfiles/upload.php",upMediaExt:"avi"});
function chkForm()
{
	if($('title').value == ""){
	   alert("请填写标题！");   
	   $('title').focus();
	   return(false);
   	}

	if(jq('#content').val() == ""){
	   alert("请填写内容！");   
	   $('content').focus();	   
	   return(false);
   	}	
		
}

function changeAuthor(uid)
{
	var user_array=new Array();
	{loop $user_array $k $v}
	user_array[$v->uid] = '{$v->nicename}';{/loop}
	var author = user_array[uid];
	jq('#author').val(author);
}

jq(document).ready(function(){
  	jq("#new_tag").focus(function(){
		if(jq('#new_tag').val() == '添加新Tag'){
			jq('#new_tag').val('');
		}
  	});
  	jq("#new_tag").blur(function(){
		if(jq('#new_tag').val() == ''){
			jq('#new_tag').val('添加新Tag');
		}
  	});	
	
	get_tag_ajax = 0;
	//点击后 从常用标签中调Tag
	jq("#link-post_tag").click(function(){
		if(get_tag_ajax == 0){
			jq.post("{PHP_SELF}?m=tag.ajaxGetTag",
				function(data){	
					var obj=eval("("+data+")");//转换为json对象 			
					var string = '';
					jq.each(obj,function(j, n){
						var tag_id = n.tag_id;
						var tag_name = n.tag_name;
						var tag_usernum = n.tag_usernum;
						string += '<a style="font-size: 8pt;" title="'+tag_usernum+' 个话题" class="tag-link-'+tag_id+'">'+tag_name+'</a>    ';
					});
					get_tag_ajax = 1;
					jq('#tagcloud-post_tag').html(string);
					
					//点击tag时增加事件
					jq("#tagcloud-post_tag a").click(function(){
						var classname = jq(this).attr('class');
						var tag_id = classname.replace("tag-link-","");
						var tag_name = jq(this).html();
						//增加Tag的隐藏表单和显示界面
						addTagInfo(tag_id, tag_name);
						
						//显示界面 带关闭的Tag				
						//删除Tag
						delTagInfo();					
					});	
					
																				
				});
		}
		jq('#tagcloud-post_tag').toggle();		
  	});
		
	//点击Tag添加按钮
	jq('#tag_add_button').click(function(){
		var new_tag = jq('#new_tag').val();
		if(new_tag == '添加新Tag'){
			jq('#new_tag').focus();
		} else {
			jq.post("{PHP_SELF}?m=tag.ajaxSaveTag", {tag_name:new_tag},
				function(data){
					jq('#new_tag').val('');
					var obj=eval("("+data+")");//转换为json对象 			
					jq.each(obj,function(j, n){
						var tag_id = j;
						var tag_name = n;
						//增加Tag的隐藏表单和显示界面
						addTagInfo(tag_id, tag_name);	
						//删除Tag						
						delTagInfo();					
					});
			});		
		}
	});	
	
	//删除Tag						
	delTagInfo();			
});

//增加tag_id 和 tag_name的显示
function addTagInfo(tag_id, tag_name){
	var tag_id_id = 'tag_id_'+tag_id;
	var tag_name_id = 'tag_name_'+tag_name;	
	
	if (jq("#"+tag_id_id+"").length == 0) { 
		//插入input hidden tag_id值					
		jq("#tag_array").append('<input name="tag_id[]" value="'+tag_id+'" id="tag_id_'+tag_id+'" />');
	}
	if (jq("#"+tag_name_id+"").length == 0) { 
		//插入input hidden tag_name值					
		jq("#tag_array").append('<input name="tag_name[]" value="'+tag_name+'" id="tag_name_'+tag_name+'" />');
		jq(".tagchecklist").append('<span><a class="ntdelbutton" id="post_tag-check-num-'+tag_id+'">X</a><p>'+tag_name+'</p></span>');						
		jq('#new_tag').focus();
	}	
}

//删除Tag
function delTagInfo(){
	jq("#tagchecklist span a").click(function(){
		var classname = jq(this).attr('id');						
		var tag_id = classname.replace("post_tag-check-num-","");
		var tag_name = jq(this).parent().children('p').html();
		
		var tag_id_id = 'tag_id_'+tag_id;
		var tag_name_id = 'tag_name_'+tag_name;							
	
		jq("#"+tag_id_id).remove();
		jq("#"+tag_name_id).remove();
		jq(this).parent().remove();
	});	
}
</script>
<!--{/if}-->
    
<!--{if $action == 'index' }-->
    <h2>内容列表</h2>
<form method="GET" action="{PHP_SELF}" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>
	<td align="center">
    显示 : 
    <select name="cat_id" id="cat_id">
	<option value="0">|-全部</option>
     $treers
    </select>
         	　
	关键词：<input type="text" name="search_keyword" value="{$search_keyword}">　
	<input type="hidden" name="m" value="article"/>
	<input type="submit" name="search_btn" value="　搜索　">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="{PHP_SELF}?m=article.article_do&page={$pageCurrent}" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="5%">编号</th>
        <th width="10%">类别</th>
        <th width="13%">模型</th>
        <th width="35%">标题</th>
        <th width="10%">状态</th>        
        <th width="17%">时间</th>        
        <th width="10%">管理</th>
      </tr>
	  <!--{if $ErrorMsg}-->
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center>$ErrorMsg</td>
	  </tr>
	  <!--{/if}-->      
      <!--{loop $rs $k $v}-->
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="{$v->article_id}" name="id_a[]"></td>
      <td>$v->article_id</td>
      <td><a href="{PHP_SELF}?m=archives.arclist&channelid={$v->channel}&cat_id={$v->cat_id}">$v->cat_name</a></td>
      <td><a href="{PHP_SELF}?m=archives.arclist&channelid={$v->channel}">$v->channeltype</a></td>      
      <td class="td_left">$v->title</td> 
      <td>$v->status</td> 
      <td>$v->time</td> 
      <td><a href="{PHP_SELF}?m=archives.edit&channelid={$v->channel}&aid={$v->article_id}">修改</a> | <a href="{PHP_SELF}?m=article.article_do&action=del&aid={$v->article_id}" onclick="{if(confirm('删除将包括该信息，确定删除吗?')){return true;}return false;}">删除</a></td>
      </tr>
      <!--{/loop}-->      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td><select name="do" id='do'>$article_do_ary_option</select></td>              
              <td class="td_left" colspan="7">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="确定" name="Submit">
      </td></tr>
      </tbody></table>
      </form>
      {$page}
<!--{/if}-->     


	</div>
</div>
</body>
</html>