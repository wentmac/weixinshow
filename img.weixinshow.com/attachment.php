<?php

set_time_limit(0);
require_once("images.config.php");
header("Content-Type:text/html;charset:utf-8");

$type = $_GET['type'];
$id = $_GET['id'];
$name = $_GET['name'];
if (!preg_match("/^([a-z]+)$/", $type)) {
    die('图片保存类型格式不正确');
}

if (!preg_match("/^([a-z0-9.\/]{21,24})$/", $id)) {
    die('图片名称格式不正确');
}
downFile($type, $id, $name);

//$file_name：传入下载文件名
//$file_name_id:传入下载文件存储的子路径
function downFile($file_type, $file_name_id, $file_name)
{
    $file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploadfiles' . DIRECTORY_SEPARATOR . 'attachment' . DIRECTORY_SEPARATOR . $file_type . DIRECTORY_SEPARATOR . $file_name_id;
    if (!file_exists($file_path)) {
        echo '文件不存在';
        return false;
    } else {
        $fp = fopen($file_path, 'r');
        $file_size = filesize($file_path);
        $file_suffix = pathinfo($file_path, PATHINFO_EXTENSION);
        header("Content-type:application/octet-stream"); // 返回的文件类型是流
        header("Accept-Ranges:bytes"); //按照字节大小返回
        header("Accept-Length:$file_size"); //返回文件大小
        header("Content-Disposition:attachment;filename=" . $file_name . '.' . $file_suffix); //这里是客户端的弹出对话框。对应的文件名
        $buffer = 1024;
        while (!feof($fp)) {
            $file_data = fread($fp, $buffer);
            echo $file_data;
        }
        fclose($fp);
    }
}

?>