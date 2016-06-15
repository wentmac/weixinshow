<?php

/*
 * upfile表的上传公共API || 提供给快乐租所有业务中需要涉及到图片上传到
 * 
 * $Author: zhangwentao $
 * $Id: upfile.php 6 2014-09-20 15:13:57Z zhangwentao $
 */
/**
  $image_url = 'http://i1.hoopchina.com.cn/u/1211/20/661/16156661/9c2223b3.jpg';
  $imageMd5 = md5_file($image_url);
  $postField = array(
  'key' => md5('upfile_kuailezu_api_001@' . $imageMd5),
  'imageResource' => $image_url,
  'imageType' => 'room',
  'imageMd5' => $imageMd5,
  'size' => '600',
  'upType' => 1
  );

  $image_realpath = 'D:/111111111.jpg';
  $imageMd5 = md5_file($image_realpath);
  $postField = array(
  'key'           => md5('upfile_kuailezu_api_001@' . $imageMd5),
  'imageResource' => '@' . $image_realpath,
  'imageType'     => 'room',
  'imageMd5'     => $imageMd5,
  'size'          => '600'
  );
  $re = Functions::curl_post_contents(IMAGE_URL . 'upapi/upfile.php', $postField);
 * 
 */
require dirname( __file__ ) . '/common.inc.php';

/* 接收参数 */

$key = empty( $_POST[ 'key' ] ) ? '' : $_POST[ 'key' ];    //用户登录后的key
$imageType = empty( $_POST[ 'imageType' ] ) ? '' : $_POST[ 'imageType' ]; //图片类型 room|avatar|shops
$imageMd5 = empty( $_POST[ 'imageMd5' ] ) ? '' : $_POST[ 'imageMd5' ]; //图片md5值 a137b7ec1347bf5f25
$oldImageId = empty( $_POST[ 'oldImageId' ] ) ? '' : $_POST[ 'oldImageId' ]; //原图片路径｜如果不为空则在上传完后删除原图片
$size = empty( $_POST[ 'size' ] ) ? 'source' : $_POST[ 'size' ];          //尺寸
$upType = empty( $_POST[ 'upType' ] ) ? 0 : (int) $_POST[ 'upType' ];      //上传类型 0:upload file|1:抓url的图片

$suffix = '.jpg';
$max_file_size = 6; //图片大小6MB
$upload = 'imageResource'; //图片字段名

/* 验证参数是否合法 */
$error = '';
if ( $key !== md5( UPAPI_KEY . $imageMd5 ) ) {
    apiException( '认证失败' );
}

if ( !preg_match( "/(" . IMAGE_TYPE . ")$/i", $imageType ) ) {
    apiException( '图片保存类型格式不正确' );
}

if ( !preg_match( "/^([a-z0-9]{32})$/i", $imageMd5 ) ) {
    apiException( '图片名称格式不正确' );
}

if ( !preg_match( "/(source|\d+|\d+x\d+)$/i", $size ) ) {
    apiException( '图片尺寸格式不正确' );
}

$imageMd5 = substr( $imageMd5, 8, 16 );
$imageId = substr( $imageMd5, 0, 2 ) . '/' . substr( $imageMd5, 2, 2 ) . '/' . substr( $imageMd5, 4 ); //$imageId=a2/xs/sajiknilijklkjj
//以年月为名建立文件夹
$targetFile = CFG_ROOT . $imageType . DIRECTORY_SEPARATOR . $imageId . $suffix;
$cFolder = CheckFolder( dirname( $targetFile ) );
if ( !$cFolder ) {
    apiException( '创建路径失败' );
}

if ( $upType == 1 ) {//抓图片    
    $imgUrl = empty( $_POST[ '' . $upload . '' ] ) ? '' : $_POST[ '' . $upload . '' ];
    if ( empty( $imgUrl ) ) {
        apiException( '要抓取的图片URL不能为空' );
    }
    $imgString = curl_file_get_contents( $imgUrl );
    if ( $imgString ) {
        if ( file_put_contents( $targetFile, $imgString ) ) {
            $return = array(
                'imageUrl' => CFG_PICURL . $imageType . '_' . $size . '/' . $imageId . $suffix,
                'imageId' => $imageId,
            );
            apiReturn( $return );
        } else {
            apiException( '保存抓取的图片到服务器失败' );
        };
    } else {
        apiException( '抓取的图片URL失败' );
    }
} else {//标准表单式上传
    $upfile = @$_FILES[ '' . $upload . '' ];
    if ( ($max_file_size * 1024 * 1024) < $upfile[ 'size' ] ) {  //判断文件是否超过限制大小
        $error = "你上传的图片过大,本系统最大图片为{$max_file_size}MB!";
        apiException( $error );
    }
    if ( $upfile[ 'error' ] > 0 ) {
        switch ( $upfile[ 'error' ] )
        {
            case '1':
                apiException( '文件大小超过了php.ini定义的upload_max_filesize值' );
                break;
            case '2':
                apiException( '文件大小超过了HTML定义的MAX_FILE_SIZE值' );
                break;
            case '3':
                apiException( '文件上传不完全' );
                break;
            case '4':
                apiException( '无文件上传' );
                break;
            case '6':
                apiException( '缺少临时文件夹' );
                break;
            case '7':
                apiException( '写文件失败' );
                break;
            case '8':
                apiException( '上传被其它扩展中断' );
                break;
            case '999':
            default:
                apiException( '无有效错误代码' );
        }
    }

    if ( empty( $upfile[ 'tmp_name' ] ) || $upfile[ 'tmp_name' ] == 'none' ) {
        apiException( '无文件上传' );
    }

    //TODO 上传图片并返回图片url            
    if ( move_uploaded_file( $upfile[ 'tmp_name' ], $targetFile ) ) {
        $return = array(
            'imageUrl' => CFG_PICURL . $imageType . '_' . $size . '/' . $imageId . $suffix,
            'imageId' => $imageId,
        );
        //判断是否有删除原图
        if ( !empty( $oldImageId ) ) {
            $oldImageId = CFG_ROOT . $oldImageId . $suffix;
            if ( file_exists( $oldImageId ) ) {
                @unlink( $oldImageId );
            }
        }
        apiReturn( $return );
    } else {
        apiException( '上传图片' . $upfile[ 'name' ] . '失败' );
    }
}

/**
 * 出错抛出异常
 * @param type $message
 * @param type $status 
 */
function apiException( $message, $status = -1 )
{
    $return = array(
        'status' => $status,
        'success' => false,
        'message' => $message,
    );
    header( "Content-type: application/json; charset=utf-8" );
    echo json_encode( $return, JSON_UNESCAPED_UNICODE );
    exit();
}

/**
 * Api 返回值函数
 * @param type $data
 * @param type $debug
 * @param type $format 
 */
function apiReturn( $data = array() )
{
    $return = array(
        'status' => 0,
        'success' => true,
        'data' => $data
    );
    header( "Content-type: application/json; charset=utf-8" );
    echo json_encode( $return, JSON_UNESCAPED_UNICODE );
    exit();
}

//创建目录 递归创建多级目录
function CheckFolder( $filedir )
{
    if ( !file_exists( $filedir ) ) {
        return mkdir( $filedir, 0777, true );
    }
    return true;
}

/**
 * curl取文件
 * @param type $url
 * @param type $timeout
 * @return type 
 */
function curl_file_get_contents( $url, $timeout = 5 )
{
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    if ( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) )
        curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $r = curl_exec( $ch );
    curl_close( $ch );
    return $r;
}
