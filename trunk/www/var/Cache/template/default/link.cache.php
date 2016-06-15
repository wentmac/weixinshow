<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\link.tpl', 'D:\Web\Site\tblog\trunk\admin\application\View\default\link.tpl', 1404245533)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>link.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<?php if($action == 'add' ) { ?>    
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/ajaxfileupload.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/ThumbAjaxFileUpload.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo STATIC_URL; ?>js/ui.datepicker.css"/>
<script src="<?php echo STATIC_URL; ?>js/jq.date.js" type="text/javascript"></script>
<script language="javascript">
jq = jQuery.noConflict(); 
//以后jquery中的都用jq代替即可。 
jq(document).ready(function() {
	jq('#forms input#start_date').datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '<?php echo $BASE;?>js/calendar.gif', buttonImageOnly: true });
});

jq(document).ready(function() {
	jq('#forms input#end_date').datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '<?php echo $BASE;?>js/calendar.gif', buttonImageOnly: true });
});
</script>
<h2>添加友情链接</h2>
<form name="modform" id="forms" action="<?php echo PHP_SELF; ?>?m=link.save" method="post"  onSubmit="return chkForm();">    
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
        <td width="100" class="td_right">标题：</td>
        <td class="td_left"><input type="text" size="40" value="<?php echo $editinfo->link_name;?>" id="title" name="title">
         </td>
      </tr>
       <tr>
        <td width="100" class="td_right">链接：</td>
        <td class="td_left"><input type="text" size="40" value="<?php echo $editinfo->link_url;?>" id="link" name="link">
         </td>
      </tr>
      <tr>
        <td class="td_right">类型：</td>
        <td class="td_left">
        <?php echo $link_type_radio_option;?>   (图片规格：88*31)
        <div style="display:<?php if($editinfo->link_type_radio == 2) { ?>block<?php } else { ?>none<?php } ?>" id="changetype2">
		<span id="thumb_preview"><?php if($editinfo->photo_url != '') { ?><img src="<?php echo $editinfo->photo_url;?>" width="200" height="150" style="margin-bottom:6px"><?php } ?></span>
	<br>
	地址：<input size="96" value="<?php echo $editinfo->photo_url;?>" id="thumb" name="thumb"><br>
	上传：<input type="file" onchange="image_preview('thumb',this.value,1)" style="width: 400px;" id="thumb_upload" name="thumb_upload">
		&nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload','<?php echo PHP_SELF; ?>?m=tool.uploadImageByAjax&filename=thumb_upload&action=link','#thumb_loading', 'thumb', 'thumb_preview', 'link_image_id');" id="thumbupload" name="thumbupload">    
    
		<img style="display:none;" src="<?php echo STATIC_URL; ?>js/loading.gif" id="thumb_loading">
        </div>        
        <div style="display:<?php if($editinfo->link_type_radio == 3) { ?>block<?php } else { ?>none<?php } ?>;" id="changetype3">链接地址：<input type="text" size="60" value="<?php echo $editinfo->externallinks;?>" id="externallinks" name="externallinks"></div>
        </td>
      </tr>

      <tr>
        <td class="td_right">期限：</td>
        <td class="td_left">
        <?php echo $link_state_radio_option;?>

        <div id="changestate" style="display:<?php if($editinfo->link_state_radio == 1) { ?>block<?php } else { ?>none<?php } ?>;">
        开始日期：<input type="text" id="start_date" name="starttime" value="<?php echo $editinfo->link_starttime;?>" readonly/>
        &nbsp;&nbsp;&nbsp;结束日期：
        <input type="text" id="end_date" name="endtime" value="<?php echo $editinfo->link_endtime;?>" readonly/>
        </div>
        </td>
      </tr>
        
      <tr>
        <td class="td_right">链接打开方式：</td>
        <td class="td_left"><?php echo $link_target_radio_option;?></td>
      </tr>
      
      <tr>
        <td class="td_right">链接描述：</td>
        <td class="td_left"><input type="text" id="link_description" name="link_description" value="<?php echo $editinfo->link_description;?>" size="80"/><br />&nbsp;&nbsp;通常，当访客将鼠标光标悬停在链接表链接的上方时，它会显示出来。根据主题的不同，也可能显示在链接下方。</td>
      </tr>      
      
      <tr>
        <td class="td_right">排序：</td>
        <td class="td_left"><input type="text" value="<?php echo $editinfo->link_order;?>" id="order" name="order"></td>
      </tr>
              
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
	      <input type="hidden" value="<?php echo $editinfo->link_id;?>" name="link_id">
		  <input type="hidden" name="link_image_id" id="link_image_id" value="<?php echo $editinfo->link_image_id;?>" />          
          <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
          <input type="reset" class="btn05"  onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" value="清除" name="reset_button">
          <input type="button" value="返回"  onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton"></td>
      </tr>
    
    </tbody></table>
</form>    
<?php } ?>
    
<?php if($action == 'index' ) { ?>
<h2>友情链接管理</h2>
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=link.link_do&action=del&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="10%">编号</th>
        <th width="16%">链接名称</th>
        <th width="30%">链接地址</th>
        <th width="10%">状态</th>
        <th width="10%">类型</th>        
        <th width="16%">添加日期</th>        
        <th width="13%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=7 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->link_id;?>" name="id_a[]"></td>
      <td><?php echo $v->link_id;?></td>
      <td class="td_left"><a href="<?php echo PHP_SELF; ?>?m=link.add&link_id=<?php echo $v->link_id;?>"><?php echo $v->link_name;?></a></td>
      <td class="td_left"><?php echo $v->link_url;?></td> 
      <td><span style="color: rgb(255, 102, 0);"><?php echo $v->state_name;?></span></td> 
      <td><span style="color: rgb(255, 102, 0);"><?php echo $v->type_name;?></span></td>     
      <td><?php echo $v->time;?></td> 
      <td><a href="<?php echo PHP_SELF; ?>?m=link.add&link_id=<?php echo $v->link_id;?>">修改</a>| <a href="<?php echo PHP_SELF; ?>?m=link.link_do&action=del&id=<?php echo $v->link_id;?>">删除</a></td>
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