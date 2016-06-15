<?php

/**
 * 后台 清理硬盘缓存 模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Clearcache.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Clearcache_admin extends Model
{

    private $cache_array;
    private $app_array;

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        //连接数据库
//        $this->connect();
        $this->app_array = array(
            'admin' => '网站后台',
            'www' => '网站前台',
        );
        $this->cache_array = array(
            '数据库缓存文件路径' => DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR . 'data',
            '模板缓存文件路径' => DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . $GLOBALS['TmacConfig']['Template']['cache_dir'] . DIRECTORY_SEPARATOR . $GLOBALS['TmacConfig']['Template']['template_dir'],
        );
    }

    public function getCacheList()
    {
        $filesize_count = 0;
        $filesize_size = 0;
        $app_array = $this->app_array;
        $cache_array = $this->cache_array;
        foreach ($app_array AS $k => $v) {
            $dir = VAR_ROOT . str_replace('/var/', '', $v);
            $dir = VAR_ROOT . str_replace(DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR, '', $v);
            foreach ($cache_array AS $kk => $vv) {
                $dir = TMAC_BASE_PATH . $k . $vv;
                $dir_array = $this->getDirSize($dir);
                $rs[$k][$kk]['dir_size'] = round($dir_array[0] / 1024 / 1024, 3);
                $rs[$k][$kk]['dir_count'] = $dir_array[1];
                $rs[$k][$kk]['dir_name'] = $k;
                $rs[$k][$kk]['dir_dir'] = $vv;
                $rs[$k][$kk]['dir_allname'] = $v;
                $filesize_count += $dir_array[1];
                $filesize_size += $rs[$k][$kk]['dir_size'];
            }
        }

        $result = array('filesize_count' => $filesize_count, 'filesize_size' => $filesize_size, 'cache_array' => $rs);
        return $result;
    }

    public function delCacheList($dir)
    {
        $del = $this->deldir($dir);
        if ($del) {
            Functions::CreateFolder($dir);
            header("location:" . PHP_SELF . "?m=cache");
        } else {
            $this->redirect('好像未删除干净,请重试!');
            exit;
        }
    }

    /**
     * 取文件夹内文件大小
     * @param <type> $dir
     * @return <type>
     */
    public function getDirSize($dir)
    {
        $size = 0;
        $count = 0;
        $ardir = @scandir($dir);
        if (is_array($ardir)) {
            foreach ($ardir as $i) {
                if (is_file($dir . "/" . $i)) {
                    $size+=filesize($dir . "/" . $i);
                    $count += 1;
                }

                if ($i != "." && $i != ".." && is_dir($dir . "/" . $i)) {
                    $dir_info_array = $this->getDirSize($dir . "/" . $i);
                    $size+=$dir_info_array[0];
                    $count+=$dir_info_array[1];
                }
            }
        }
        return array($size, $count);
    }

    /**
     * 删除非空文件夹
     * @param $dir;
     * return
     */
    public static function deldir($dir)
    {
        $dh = opendir($dir);
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..' && $file != '.svn') {
                is_dir($dir . DIRECTORY_SEPARATOR . $file) ?
                                self::delDir($dir . DIRECTORY_SEPARATOR . $file) :
                                unlink($dir . DIRECTORY_SEPARATOR . $file);
            }
        }
        if (readdir($dh) == false) {
            closedir($dh);
            if (rmdir($dir)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}