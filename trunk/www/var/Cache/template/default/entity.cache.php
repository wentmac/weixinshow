<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\entity.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\entity.tpl', 1456159578)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>user.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
    
<?php if($action == 'index' ) { ?>
<h2>数据表实体生成</h2>
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=entity.create" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
        <tr>
            <th width="10%">&nbsp;</th>
            <th align="left" width="45%">表名</th>
            <th align="left" width="45%">表描述</th>
        </tr>	  
      <?php if(is_array($tableArray)) foreach($tableArray AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v['Name'];?>" name="id_a[]"></td>            
      <td class="td_left" ><a href="<?php echo PHP_SELF; ?>?m=entity.table&t=<?php echo $v['Name'];?>&tn=<?php echo $v['Comment'];?>" target="_blank"><?php echo $v['Name'];?></a></td>       
      <td class="td_left" ><?php echo $v['Comment'];?></td>       
      </tr>
      <?php } ?>      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td class="td_left" colspan="8">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="生成实体类" name="Submit">
            <input type="hidden" value="del" name="action">
      </td></tr>
      </tbody></table>
      </form>
      
<?php } ?>
	</div>
</div>
</body>
</html>