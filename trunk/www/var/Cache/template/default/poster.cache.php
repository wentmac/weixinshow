<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\poster.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\poster.tpl', 1453645924)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>ad.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<?php if($action == 'add' ) { ?>    
<script src="<?php echo STATIC_URL; ?>js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/ajaxfileupload.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/ThumbAjaxFileUpload.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo STATIC_URL; ?>js/ui.datepicker.css"/>
<script src="<?php echo STATIC_URL; ?>js/jq.date.js" type="text/javascript"></script>
<script language="javascript">
jq = jQuery.noConflict(); 
//以后jquery中的都用jq代替即可。 
jq(document).ready(function() {
	jq('#forms input#start_date').datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '<?php echo STATIC_URL; ?>js/calendar.gif', buttonImageOnly: true });
});

jq(document).ready(function() {
	jq('#forms input#end_date').datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '<?php echo STATIC_URL; ?>js/calendar.gif', buttonImageOnly: true });
});

    function add_seriesInit(){	
	//获取了页面所有的file
	 var file = jq(':file').get();                        
	 var fileNum = file.length; 	
	 var id = fileNum+1;
	 var fileName = 'thumb[]'; 

     var thumbStr = '<div id="upload_div_'+id+'"><span id="thumb_preview'+id+'"></span><br>链接：<input type="text" size="96" value="" name="thumburl[]"><br>描述：<input type="text" size="96" value="" name="thumbdes[]"><br>地址：<input type="text" size="96" value="" id="thumb'+id+'" readonly="readonly"><br>自定义：<input type="text" size="96" value="" name="self_field[]"><br><input type="hidden" value=""  name="'+fileName+'" id="imgid_'+id+'">上传：<input type="file" onchange="image_preview(\'thumb'+id+'\',this.value,1)" style="width: 400px;" id="thumb_upload'+id+'" name="thumb_upload'+id+'">&nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload(\'thumb_upload'+id+'\',\'/<?php echo PHP_SELF; ?>?m=tool.uploadImageByAjax&filename=thumb_upload'+id+'&action=poster&size=600x200\',\'#thumb_loading'+id+'\', \'thumb'+id+'\', \'thumb_preview'+id+'\', \'imgid_'+id+'\');" id="thumbupload'+id+'" name="thumbupload'+id+'">    <input type="button" value="删除" onclick="delimg(\'upload_div_'+id+'\')" /><br>排序：<input type="text" size="10" value="" name="sort[]" id="sort'+id+'"> 越大越靠前<br><img style="display:none;" src="<?php echo STATIC_URL; ?>js/loading.gif" id="thumb_loading'+id+'"></div>';
      jq("#add_seriesArea").before(thumbStr);                       
    };
	
	function delimg(id){
		jq("#"+id+"").remove();
	}
</script>
    <h2>新增/修改广告</h2>
<form name="modform" id="forms" action="<?php echo PHP_SELF; ?>?m=poster.save" method="post"  onSubmit="return chkForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
       <tbody><tr>
        <td width="100" class="td_right">标题：</td>
        <td class="td_left"><input type="text" size="40" value="<?php echo $editinfo->poster_title;?>" id="poster_title" name="poster_title">
         </td>
      </tr>
	<tr>
        <td width="100" class="td_right">位置代号：</td>
        <td class="td_left"><input type="text" size="20" value="<?php echo $editinfo->poster_name;?>" id="poster_name" name="poster_name"> 请用英文,调用的时候用得着（如首页上部第一个的广告位可以这样写index_top_1)
         </td>
      </tr>      
      
        <tr>
        <td width="100" class="td_right">广告大小：</td>
        <td class="td_left">宽：<input type="text" size="10" value="<?php echo $editinfo->poster_width;?>" id="poster_width" name="poster_width">&nbsp;&nbsp;&nbsp;高：<input type="text" size="10" value="<?php echo $editinfo->poster_height;?>" id="poster_height" name="poster_height">
         </td>
      </tr>
       <tr>
        <td width="100" class="td_right">链接：</td>
        <td class="td_left"><input type="text" size="40" value="<?php echo $editinfo->poster_link;?>" id="poster_link" name="poster_link">
         </td>
      </tr>
	  
      <tr>
        <td class="td_right">类型：</td>
        <td class="td_left">
        <?php echo $poster_type_radio_option;?>       
        <div style="display:<?php if($editinfo->poster_type_radio == 2) { ?>block<?php } else { ?>none<?php } ?>" id="changetype2">
		<?php if($editinfo->poster_id>0) { ?>
        <?php if(is_array($imgurl_array)) foreach($imgurl_array AS $k => $v) { ?>
        <div id="upload_div_<?php echo $k;?>">
        <span id="thumb_preview<?php echo $k;?>"><img src="<?php echo $v;?>" width="600" height="120" style="margin-bottom:6px"></span><br>
        链接：<input type="text" size="96" value="<?php echo $thumburl_array[$k];?>" name="thumburl[]"><br>
        描述：<input type="text" size="96" value="<?php echo $thumbtitle_array[$k];?>" name="thumbdes[]"><br>        
		地址：<input type="text" size="96" value="<?php echo $imgid_array[$k];?>" id="thumb<?php echo $k;?>" readonly="readonly"><br>		
		自定义：<input type="text" size="96" value="<?php echo $self_field_array[$k];?>" name="self_field[]"><br>
		<input type="hidden" value="<?php echo $imgid_array[$k];?>" name="thumb[]" id="imgid_<?php echo $k;?>"> 
		上传：<input type="file" onchange="image_preview('thumb<?php echo $k;?>',this.value,1)" style="width: 400px;" id="thumb_upload<?php echo $k;?>" name="thumb_upload<?php echo $k;?>">
		&nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload<?php echo $k;?>','/<?php echo PHP_SELF; ?>?m=tool.uploadImageByAjax&filename=thumb_upload<?php echo $k;?>&action=poster&size=600x200','#thumb_loading<?php echo $k;?>', 'thumb<?php echo $k;?>', 'thumb_preview<?php echo $k;?>','imgid_<?php echo $k;?>');" id="thumbupload<?php echo $k;?>" name="thumbupload<?php echo $k;?>">    <input type="button" value="删除" onclick="delimg('upload_div_<?php echo $k;?>')" /><br>
		排序：<input type="text" size="10" value="<?php echo $sort_array[$k];?>" name="sort[]" id="sort<?php echo $k;?>">越大越靠前<br>		
		<img style="display:none;" src="<?php echo STATIC_URL; ?>js/loading.gif" id="thumb_loading<?php echo $k;?>">
        </div>
        <?php } ?>
        <?php } else { ?>
        <div id="upload_div">
        <span id="thumb_preview"></span><br>
		链接：<input type="text" size="96" value="" name="thumburl[]"><br>
		描述：<input type="text" size="96" value="" name="thumbdes[]"><br>        
		地址：<input type="text" size="96" value="" id="thumb" readonly="readonly"><br>        		
		自定义：<input type="text" size="96" value="" name="self_field[]"><br>
		<input type="hidden" value="" name="thumb[]" id="imgid_1"> 
		上传：<input type="file" onchange="image_preview('thumb',this.value,1)" style="width: 400px;" id="thumb_upload" name="thumb_upload">
		&nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload','/<?php echo PHP_SELF; ?>?m=tool.uploadImageByAjax&filename=thumb_upload&action=poster&size=600x200','#thumb_loading', 'thumb', 'thumb_preview','imgid_1');" id="thumbupload" name="thumbupload">    <input type="button" value="删除" onclick="delimg('upload_div')" /><br>
		排序：<input type="text" size="10" value="" name="sort[]">越大越靠前<br>
		<img style="display:none;" src="<?php echo STATIC_URL; ?>js/loading.gif" id="thumb_loading">
        </div>        
		<?php } ?>
        <div id="add_seriesArea"><input class="button" type="button" value="添加" onclick="add_seriesInit()"/></div>	
        </div>        
        </td>
      </tr>

      
      <tr>
        <td class="td_right">期限：</td>
        <td class="td_left">
        <?php echo $poster_state_radio_option;?>

        <div id="changestate" style="display:<?php if($editinfo->poster_state_radio == 1) { ?>block<?php } else { ?>none<?php } ?>;">
        开始日期：<input type="text" id="start_date" name="poster_starttime" value="<?php echo $editinfo->poster_starttime;?>" readonly/>
        &nbsp;&nbsp;&nbsp;结束日期：
        <input type="text" id="end_date" name="poster_endtime" value="<?php echo $editinfo->poster_endtime;?>" readonly/>
        </div>
        </td>
      </tr>
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left"><input type="hidden" value="<?php echo $editinfo->poster_id;?>" name="poster_id">
        <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
          <input type="reset" class="btn05" value="清除" name="reset_button" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'">
          <input type="button" value="返回" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'"></td>
      </tr>
    
    </tbody>
</table>    
</form>
<?php } ?>
    
<?php if($action == 'index' ) { ?>
    <h2>广告管理</h2>
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=poster.action_do&action=del&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <th width="2%"></th>
        <th width="5%">编号</th>
        <th width="18%">标题</th>
        <th width="10%">位置代号</th>        
        <th width="8%">状态</th>
        <th width="10%">类型</th>
        <th width="12%">添加日期</th>
        <th width="12%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=9 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      <td><input type="checkbox" value="<?php echo $v->poster_id;?>" name="id_a[]"></td>
      <td><?php echo $v->poster_id;?></td>
      <td class="td_left"><?php echo $v->poster_title;?></td>
      <td class="td_left"><?php echo $v->poster_name;?></td>      
      <td><span style="color: rgb(255, 102, 0);"><?php echo $v->poster_type_radio_text;?></span></td> 
      <td><span style="color: rgb(255, 102, 0);"><?php echo $v->poster_state_radio_text;?></span></td>
      <td><?php echo $v->poster_time;?></td>      
      <td><a href="<?php echo PHP_SELF; ?>?m=poster.add&poster_id=<?php echo $v->poster_id;?>">修改</a> ｜ <a href="<?php echo PHP_SELF; ?>?m=poster.action_do&action=del&id=<?php echo $v->poster_id;?>" onclick="{if(confirm('删除将包括该信息，确定删除吗?')){return true;}return false;}">删除</a></td>
      </tr>
      <?php } ?>      
	  <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td class="td_left" colspan="8">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="删除所选" name="Submit">
            <input type="hidden" value="del" name="action">
      </td></tr>	      
      </tbody></table>
      </form>
      <?php echo $page;?>
<?php } ?>     
	</div>
</div>
</body>
</html>