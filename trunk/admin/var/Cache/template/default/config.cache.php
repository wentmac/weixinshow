<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\config.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\admin\application\View\default\config.tpl', 1457362951)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>config.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<?php if($action == 'add' ) { ?>
<h2>添加系统配置参数新变量</h2>
<form name="modform" id="forms" action="<?php echo PHP_SELF; ?>?m=config.save" method="post"  onSubmit="return chkForm();">    
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
        <td width="100" class="td_right">变量名称：</td>
        <td class="td_left"><input type="text" size="40" value="<?php echo $editinfo->varname;?>" id="nvarname" name="nvarname">
         </td>
      </tr>
       <tr>
        <td width="100" class="td_right">变量值：</td>
        <td class="td_left"><textarea name="nvarvalue" id="nvarvalue" rows="6" cols="62"><?php echo $editinfo->value;?></textarea>
         </td>
      </tr>
      <tr>
        <td class="td_right">变量类型：</td>
        <td class="td_left">
        <?php echo $vartype_ary_radio;?>
        <div style="display:<?php if($editinfo->type == 'select' || $editinfo->type == 'radio') { ?>block<?php } else { ?>none<?php } ?>;" id="changetype">nameacton：<input type="text" size="60" value="<?php echo $editinfo->nameaction;?>" id="nameaction" name="nameaction">&nbsp;acton事件 选填<br />
        数据值：<textarea name="item" id="item" cols="52" rows="5" style="margin-top:10px"><?php echo $editinfo->item;?></textarea>&nbsp;两数据之间用{|}来分隔
        </div>
        </td>
      </tr>
      <tr>
        <td class="td_right">参数说明：</td>
        <td class="td_left"><input type="text" value="<?php echo $editinfo->info;?>" id="varmsg" name="varmsg" size="80">
          </td>
      </tr>
      
      <tr>
        <td class="td_right">参数说明帮助：</td>
        <td class="td_left"><input type="text" value="<?php echo $editinfo->help;?>" id="help" name="help" size="80">
          </td>
      </tr>      
      
      <tr>
        <td class="td_right">排序：</td>
        <td class="td_left"><input type="text" value="<?php echo $editinfo->order;?>" id="order" name="order" size="10">
          </td>
      </tr>            
     
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
	      <input type="hidden" value="<?php echo $editinfo->sys_id;?>" name="sysid">
          <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
          <input type="reset" class="btn05"  onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" value="清除" name="reset_button">
          <input type="button" value="返回"  onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton"></td>
      </tr>
    
    </tbody></table>
</form>    
<?php } ?>
    
<?php if($action == 'index' ) { ?>

<h2>修改网站设置</h2>
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=config.savelist" onSubmit="return fillcitylist();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
          <tr>
            <td width="20%">参数说明</td>
            <td width='60%'>参数值</td>
            <td width="20%">变量名</td>                
          </tr>  
      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
          <tr>
            <td class="td_right"><?php echo $v->info;?>：</td>
            <td class="td_left">
            <?php if($v->type == 'string') { ?><input type="text" size="40" value="<?php echo $v->value;?>" id="value" name="<?php echo $v->varname;?>">
            <?php } elseif($v->type == 'bstring') { ?><textarea name="<?php echo $v->varname;?>" id="nvarvalue" rows="4" cols="62"><?php echo $v->value;?></textarea>
            <?php } elseif($v->type == 'select') { echo $v->select;?>
            <?php } elseif($v->type == 'radio') { echo $v->radio;?>
            <?php } ?>
    		<?php echo $v->help;?>
            </td>
            <td><?php echo $v->varname;?></td>                
          </tr>  
      <?php } ?>      
          <tr>
              <td class="td_right">&nbsp;</td>
              <td class="td_left" colspan="2">
              <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
              <input type="button" value="返回" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'"></td>
          </tr>          
      </tbody>
	</table>
      </form>

      
<?php } ?>
	</div>
</div>
</body>
</html>