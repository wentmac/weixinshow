<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\agent_level.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\admin\application\View\default\agent_level.tpl', 1460715388)
;?>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>层次排位 - <?php echo $config['cfg_webname'];?></title>
  <link href="<?php echo STATIC_URL; echo APP_MOBILE_NAME; ?>/default/css/common/base.css" type="text/css" rel="stylesheet">
  <link href="<?php echo STATIC_URL; echo APP_MOBILE_NAME; ?>/default/css/order/kd.css" type="text/css" rel="stylesheet">
  
  <link rel="stylesheet" href="<?php echo STATIC_URL; echo APP_MOBILE_NAME; ?>/default/css/buyer/font-awesome.min.css" type="text/css" media="screen">
      <link rel="stylesheet" href="<?php echo STATIC_URL; echo APP_MOBILE_NAME; ?>/default/css/buyer/distribute-myaffiliate.css?time=1452498631" type="text/css">
<style>
body {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857143;
    color: #333;
    background-color: #fff;
    max-width: 1280px;
}
.text-center {
    text-align: center;
}

.affiliate-list .well {
    background: rgba(255, 255, 255, 0.8);
    -webkit-box-shadow: none;
    box-shadow: none;
    position: relative;
    z-index: 100;
    padding-top: 8px;
    padding-bottom: 8px;
    margin-bottom: 12px;
}
.well {
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
}
.agent_div ul{float: left; text-align: center; width: 100%;}
.agent_div ul li{float: left; text-align: center;}
.font_sz { font-size: 8px;}
.font_sz li{ width: 3.7%;}
.fensi {font-size: 14px;}
.fensi h3 {margin-bottom: 10px;}
.fensi li { border: 1px solid #f3f3f3; border-radius: 4px; padding: 4px; margin-bottom: 10px; line-height: 40px;}
.fensi li span,.fensi li em{ margin-left: 30px;}
.fensi li i{ font-style: normal; width: 10%; display: inline-block;}
.fensi li img { width: 40px; height: 40px;}
</style>

</head>

<body>
  <header id="common_hd" class="c_txt rel">      
      <a id="common_hd_logo" class="t_hide abs common_hd_logo">层次排位</a>
      <h1 class="hd_tle">层次排位</h1>
      
  </header>

  <section>
      <div class="container">


                  <div class="well text-center color-999 agent_div">
                      <ul> 
                        <?php if(is_array($bread_crumb_array)) foreach($bread_crumb_array AS $uid) { ?>
                          <li><a href="<?php echo PHP_SELF; ?>?m=member.level&uid=<?php echo $uid;?>"/><?php echo $bread_crumb_map[$uid]->nickname;?></a><span>&nbsp;>&nbsp;</<span></li>
                          <?php } ?>                          
                      </ul>
                  </div>  

        <div class="affiliate-list text-center">
          <div class="well user-well text-center">
            <img src="<?php echo $memberInfo->member_image_id;?>">
            <h4 class="color-333 text-center text-overflow"><?php echo $memberInfo->nickname;?></h4>          
          </div>
              <label class="affiliate-level">领航者</label>                
                  <div class="well text-center color-999 agent_div">
                      <ul>
                      <?php if(!empty($tree_array['1'])) { ?>
                      <?php if(is_array($tree_array['1'])) foreach($tree_array['1'] AS $vv) { ?>
                        <?php if(is_array($vv)) foreach($vv AS $value) { ?>
                          <?php if(empty($member_info_map[$value])) { ?>
                          <li style="width:33%">空</li>
                          <?php } else { ?>
                        <li style="width:33%"><a href="<?php echo PHP_SELF; ?>?m=member.level&uid=<?php echo $member_info_map[$value]->uid;?>"/><?php echo $member_info_map[$value]->nickname;?></a></li>
                          <?php } ?>
                        <?php } ?>                      
                      <?php } ?>        
                      <?php } ?>              
                    </ul>
                  </div>
                  <label class="affiliate-level">间接领航者</label>
                    <div class="well text-center color-999 agent_div">
                      <ul>
                      <?php if(!empty($tree_array['2'])) { ?>
                      <?php if(is_array($tree_array['2'])) foreach($tree_array['2'] AS $vv) { ?>
                        <?php if(is_array($vv)) foreach($vv AS $value) { ?>
                        <?php if(empty($value)) { ?>
                        <li style="width:11%" title="此排名位置空缺">空</li>
                        <?php } else { ?>
                        <li style="width:11%"><a href="<?php echo PHP_SELF; ?>?m=member.level&uid=<?php echo $member_info_map[$value]->uid;?>"/><?php echo $member_info_map[$value]->nickname;?></a></li>
                        <?php } ?>
                        <?php } ?>                      
                      <?php } ?>   
                      <?php } ?>
                      </ul>
                    </div>
                    <label class="affiliate-level">三级领航者</label>
                    <div class="well text-center color-999 agent_div font_sz">
                      <ul>
                      <?php if(!empty($tree_array['3'])) { ?>                      
                      <?php if(is_array($tree_array['3'])) foreach($tree_array['3'] AS $vv) { ?>
                        <?php if(is_array($vv)) foreach($vv AS $value) { ?>
                          <?php if(empty($value)) { ?>
                          <li title="此排名位置空缺">空</li>
                          <?php } else { ?>                        
                          <li title="<?php echo $member_info_map[$value]->nickname;?>"><a href="<?php echo PHP_SELF; ?>?m=member.level&uid=<?php echo $member_info_map[$value]->uid;?>"/><?php echo mb_substr(trim($member_info_map[$value]->nickname),0,3,'utf-8'); ?></a></li>
                          <?php } ?>
                        <?php } ?>                      
                      <?php } ?>   
                      <?php } ?>
                    </ul>
              </div>
              </div>
              <div class="fensi">
                <h3>我的粉丝</h3>
                <ul>
                <?php if(is_array($agent_array)) foreach($agent_array AS $value) { ?>
                  <li><i><?php echo $value->nickname;?></i><span><img src="<?php echo $value->member_image_id;?>"></span><em><?php echo $value->member_level;?></em><em><?php echo $value->reg_time;?></em></li>
                <?php } ?>                  
                </ul>
              </div>
              </div>

  </section>

  
</body>

</html>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.11.2/jquery-1.11.2.min.js" type="text/javascript"></script>    
<script type="text/javascript">
  var index_url = '<?php echo INDEX_URL; ?>';
  var mobile_url = '<?php echo MOBILE_URL; ?>';
  var static_url = '<?php echo STATIC_URL; ?>';
  var base_v = '<?php echo $BASE_V;?>';
  var php_self = '<?php echo PHP_SELF; ?>';
</script>