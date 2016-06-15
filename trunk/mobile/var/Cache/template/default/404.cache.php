<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\404.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\404.tpl', 1465815661)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>您访问的页面没有找到...</title>
		<style>
			body {
				font-size: 16px;
				background: #eee;
			}
			pre {
				text-align: center;
			}
		</style>
		<link href="<?php echo STATIC_URL; ?>common/assets/css/amazeui.css" rel="stylesheet" type="text/css">
	</head>

	<body>
		<div class="admin-content">
			<div class="am-g">
				<div class="am-u-sm-12">
					<h2 class="am-text-center am-text-xxxl am-margin-top-lg"><?php echo $title;?></h2>
					<h2 class="am-text-center am-text-xxxl am-margin-top-lg">404. Not Found</h2>
					<p class="am-text-center">没有找到你要的页面</p>
					<pre class="page-404">          .----.
       _.'__    `.
   .--($)($$)---/#\
 .' @          /###\
 :         ,   #####
  `-..__.-' _.-\###/
        `;_:    `"'
      .'"""""`.
     /,  ya ,\\
    //  404!  \\
    `-._______.-'
    ___`. | .'___
   (______|______)
   <div ><a href="<?php echo STATIC_URL; ?>">返回首页</a> <a onclick="history.back();" >返回上一页</a> </div>
        </pre>
			
				<h1 class="am-text-center am-text-lg">Copyright 银品惠</h1>
			
				</div>
	
			</div>
		
		</div>

	</body>

</html>