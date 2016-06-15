<!DOCTYPE html>
<html>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="UTF-8" />
	<title>{$config[cfg_nameinfo]} - {$config[cfg_webname]}</title>
	<link rel="stylesheet" href="{$BASE_V}style.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="{STATIC_URL}js/prettify/sons-of-obsidian.css" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="Prower&#039;s Blog RSS Feed" href="http://www.prower.cn/feed" />
	
<link rel='index' title='{$config[cfg_webname]}' href='$config[cfg_indexurl]' />

<!-- All in One SEO Pack 1.6.13.3 by Michael Torbert of Semper Fi Web Design[94,119] -->
<meta name="description" content="$config[cfg_description]" />
<meta name="keywords" content="$config[cfg_keywords]" />
<link rel="canonical" href="$config[cfg_indexurl]" />
<!-- /all in one seo pack -->
</head>

<body>
<div id="wrap">
	<!--{template inc/header}-->
	<div id="content">	
	<div id="main">
		<ul id="post_list">
		<!--{loop $result[rs] $k $v}-->
			<li class="post-2131 post type-post status-publish format-standard hentry category-life tag-38835 tag-38834">
			<h2><a href="{$config[cfg_indexurl]}{$v[category_nicename]}/{$v[article_id]}">$v[title]</a></h2>
				<div class="meta">$v[time]</div>					
				<div class="excerpt">
				<p>$v[content_descrption]</p>				
				<div class="meta">分类目录：<a href="{$config[cfg_indexurl]}{$v[category_nicename]}/" title="查看 $v[cat_name] 中的全部文章" rel="category tag">$v[cat_name]</a> 
				<!--{if count($v[tag_info])>0}-->
				| 标签： ${$tk=1;}
				<!--{loop $v[tag_info] $kk $vv}-->{if $tk>1}、{/if}<a href="{$config[cfg_indexurl]}tag/${echo urlencode($vv)}" rel="tag">$vv</a>${$tk++;}			
				<!--{/loop}-->
				<!--{/if}-->
				| 查看：$v[click_count]</div>
				<div class="comments_num"><a href="{$config[cfg_indexurl]}{$v[category_nicename]}/{$v[article_id]}#comments" title="《$v[title]》上的评论">$v[comment_count] 条评论</a></div>
				</div>
			</li>
		<!--{/loop}-->
					</ul>
		<div class="navigation">
		$result[page]
			<span class="alignright"></span>
			<span class="alignleft"></span>
		</div>

	</div>
	<!--{template inc/sidebar}-->
</div>
	<!--{template inc/footer}-->
</div>
</body>
</html>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{STATIC_URL}js/prettify/prettify.js"></script>
<script type="text/javascript">prettyPrint();</script>