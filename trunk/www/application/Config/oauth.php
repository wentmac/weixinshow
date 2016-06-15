<?php

/**
  const APPID = 'wx7bf2888c2d9d1446';
  const MCHID = '1242720702';
  const KEY = 'd2bfs5s0f3sccb65ce7750cab463931e';
  const APPSECRET = 'd2bf6590f3ccb65ce77250c85463931e';
 */
//mp.weixin.qq.com admin@090.cn
$config[ 'oauth' ][ 'wechat' ] = array(
    'appid' => 'wx6********0698',
    'mchid' => '12******01',
    'key' => '0d884dc*****************0d',//非唯 
    'appsecret' => 'fc5*****************f68e1'
);
//open.weixin.qq.com  18610247767@qq.com 移动应用
$config[ 'oauth' ][ 'wechat_open_app' ] = array(
    'appid' => 'wx90*******72d',
    'mchid' => '12******01',
    'key' => '0d8*********************eb0d',
    'appsecret' => '5536******************c806'
);
//open.weixin.qq.com  18610247767@qq.com web应用
$config[ 'oauth' ][ 'wechat_open_web' ] = array(
    'appid' => 'wx27d***********31a',    
    'appsecret' => '63ce******************00d75'
);
$config[ 'oauth' ][ 'weibo' ] = array(
    'appkey' => '10*****34',    
    'appsecret' => 'dee******************72b'
);
$config[ 'oauth' ][ 'qq' ] = array(
    'appid' => '10*****2',
    'appkey' => '0048******************41d03'    
);
$config[ 'oauth' ][ 'qq_app' ] = array(
    'appid' => '11******09',
    'appkey' => 'af***********uw'    
);
//mp.weixin.qq.com admin@090.cn 企业给用户付款专用
$config[ 'oauth' ][ 'weixin_transfers' ] = array(
    'appid' => 'wx**************51',
    'mchid' => '1********1',
    'key' => '427******************fe',
    'appsecret' => '468******************84'
);