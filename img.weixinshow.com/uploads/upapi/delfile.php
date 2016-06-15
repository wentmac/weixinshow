<?php

/*
 * delfile 删除缩略图的 API || 提供给快乐租所有业务中需要涉及到要删除的缩略图
 * 删除房屋图片物理文件
 * 不删除原图，只删除缩略图
 * $Author: zhangwentao $
 * $Id: delfile.php 6 2014-09-20 15:13:57Z zhangwentao $
 */
/**
  $image_realpath = 'D:/111111111.jpg';
  $imageId = '01/37/b7ec1347bf5f';
  $postField = array(
  'key'           => md5('upfile_kuailezu_api_001@' . $imageId),
  'imageType'     => 'room',
  'imageId'     => $imageId,
  'deleteSource'     => false,
  );
  $re = Functions::curl_post_contents(IMAGE_URL . 'upapi/delfile.php', $postField);
 * 
 */
require dirname(__file__) . '/common.inc.php';

/* 接收参数 */

$key = empty($_POST['key']) ? '' : $_POST['key'];    //用户登录后的key
$imageType = empty($_POST['imageType']) ? '' : $_POST['imageType']; //图片类型 room|avatar|shops
$imageId = empty($_POST['imageId']) ? '' : $_POST['imageId']; //图片md5值 a137b7ec1347bf5f25
$deleteSource = empty($_POST['deleteSource']) ? false : $_POST['deleteSource']; //是否删除原图

/* 验证参数是否合法 */
$error = '';
if ($key !== md5(UPAPI_KEY . $imageId)) {
    apiException('认证失败');
}
if (!preg_match("/(" . IMAGE_TYPE . ")$/i", $imageType)) {
    apiException('图片保存类型格式不正确');
}

if (!preg_match("/^[a-z0-9]{2}\/[a-z0-9]{2}\/[a-z0-9]{12}?$/i", $imageId)) {
    apiException('图片名称格式不正确');
}

if ($deleteSource) {
    $imageSource = CFG_ROOT . $imageType . DIRECTORY_SEPARATOR . $imageId . '.jpg';    
    @unlink($imageSource);
}

//图片所有类型的所有尺寸
$imagesSizeConfigArray = $config['images']['size'];
if (!isset($imagesSizeConfigArray[$imageType])) {
    apiException('图片类型不存在');
}
$return = array();
foreach ($imagesSizeConfigArray[$imageType] AS $k => $v) {
    $photo_thumb_url = STATIC_ROOT . 'thumb/' . $imageType . '_' . $v . '/' . $imageId . '.jpg';
    $return[] = $v;
    @unlink($photo_thumb_url);
}
apiReturn($return);

/**
 * 出错抛出异常
 * @param type $message
 * @param type $status 
 */
function apiException($message, $status=-1)
{
    $return = array(
        'status' => $status,
        'success' => false,
        'message' => $message,
    );
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($return, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Api 返回值函数
 * @param type $data
 * @param type $debug
 * @param type $format 
 */
function apiReturn($data = array())
{
    $return = array(
        'status' => 0,
        'success' => true,
        'data' => $data
    );
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($return, JSON_UNESCAPED_UNICODE);
    exit();
}