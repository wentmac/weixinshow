<?php

define( 'CFG_INDEX_URL', 'http://dev.img.yph.weixinshow.com/' ); //静态文件URL
define( 'IMAGE_TYPE', 'avatar|article|shop|idcard|logo|goods|refund|settle|notice|poster|brand' );     //允许上传新建的图片目录
define( 'UPAPI_KEY', 'upfile_@—dsio' ); //图片上传接收的私钥
$config[ 'images' ][ 'size' ] = array(
    'article' => array(
        '120x90',
        '200x150',
        '209x97',
        '220x135',
        '220x188',
        '164x111',
        '487x293',
        '221x132',
        '350x195',
        '745x293'
    ),
    'idcard' => array(
        '120x90',
        '200x150',
    ),
    'avatar' => array(
        '64',
        '110'
    ),
    'shop' => array(
        '50',
        '110',
        '120x90',
        '640x330',
        '200x150'
    ),
    'logo' => array(
        '120x90',
        '200x150',
        '224x38',
    ),
    'goods' => array(
        '50',
        '80',
        '110',
        '300',
        '640',
        '120x90',
        '200x150',
        '224x38',
        '640x0'
    ),
    'refund' => array(
        '50',
        '80',
        '98x55',
        '360x0',
        '800x0',
        '110'
    ),
    'settle' => array(
        '800x0'
    ),
    'notice' => array(
        '550x260'
    ),
    'poster' => array(
        '600x0'
    ),
    'brand' => array(
        '200x150'
    )
);
?>