<?php

/**
 * 后台 工具 模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Tool.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Tool_admin extends Model
{

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        //连接数据库
        //$this->db = $this->connect();
    }

    public function jsonString($str)
    {
        return preg_replace("/([\\\\\/'])/", '\\\$1', $str);
    }

    public function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
        } elseif ($bytes >= 1048576) {
            $bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
        } elseif ($bytes >= 1024) {
            $bytes = round($bytes / 1024 * 100) / 100 . 'KB';
        } else {
            $bytes = $bytes . 'Bytes';
        }
        return $bytes;
    }

    /**
     * Api 返回值函数
     * @param type $data
     * @param type $debug
     * @param type $format 
     */
    public function apiReturn($data = array(), $debug = 0, $format = 'json')
    {
        $return = array(
            'status' => 0,
            'success' => true,
            'data' => $data
        );
        if ($debug == 1) {
            header("Content-type: text/html; charset=utf-8");
            echo '<pre>';
            print_r($return);
            echo '</pre>';
        } else {
            if ($format == 'json') {
                header("Content-type: application/json; charset=utf-8");
                echo json_encode($return);
                exit();
            }
        }
    }

    /**
     * 通过图片md5_file来返回图片哈唏ID
     * @param type $md5File 
     * @return $imageId
     */
    public function getImageId($md5File)
    {
        $imageMd5 = substr($md5File, 8, 16);
        $imageId = substr($imageMd5, 0, 2) . '/' . substr($imageMd5, 2, 2) . '/' . substr($imageMd5, 4); //$imageId=a2/xs/sajiknilijklkjj
        return $imageId;
    }

}