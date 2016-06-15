<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>{$notice_info->notice_title}</title>
		<link href="{STATIC_URL}common/assets/css/amazeui.css" rel="stylesheet" type="text/css">
		<style type="text-css">
			.gray{color: #a1a1a1;}
		</style>
	</head>

<body>
  <div class="am-container am-padding-vertical-sm" style="background: #fff;">
    <div class="am-text-center am-text-danger am-text-lg am-padding-top"><strong>{$notice_info->notice_title}</strong></div>
    <div class="am-text-center am-padding-vertical-sm am-text-sm gray">{$notice_info->notice_time}</div>
    <article class="note_body">
      {$notice_info->notice_content}
    </article>
  </div>

</body>

</html>

<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/amazeui.min.js"></script>

<script type="text/javascript">
	var index_url = '{INDEX_URL}';
	var mobile_url = '{MOBILE_URL}';
	var static_url = '{STATIC_URL}';
	var base_v = '{$BASE_V}';	
</script>