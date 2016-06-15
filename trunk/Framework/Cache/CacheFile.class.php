<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: CacheFile.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class CacheFile extends Cache
{

    /**
     * 缓存目录
     *
     * @var string
     * @access private
     */
    private $dir;
    private $md5KeyStatus = true;
    private $time;

    /**
     * 构造器
     *
     * @access public
     */
    public function __construct()
    {
        $this->dir = VAR_ROOT . 'Cache' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
        @chmod($this->dir, 0777);
        if (!is_writable($this->dir)) {
            throw new TmacException('缓存文件夹' . $this->dir . '不可写！');
        }
        $this->time = time();
    }

    /**
     * 设置是否用把缓存的key值名称md5，false的节省系统开销
     * @param type $md5KeyStatus
     * @return CacheFile 
     */
    public function setMd5Key($md5KeyStatus)
    {
        $this->md5KeyStatus = $md5KeyStatus;
        return $this;
    }

    public function filename($name)
    {
        if ($this->md5KeyStatus)
            $name = md5($name);
        if ($GLOBALS['TmacConfig']['Cache']['File']['DATA_CACHE_SUBDIR']) {
            // 使用子目录
            $dir = '';
            for ($i = 0; $i < $GLOBALS['TmacConfig']['Cache']['File']['DATA_PATH_LEVEL']; $i++) {
                $dir .= $name{$i} . '/';
            }
            if (!is_dir($this->dir . $dir)) {
                Functions::CreateFolder($this->dir . $dir);
            }
            $filename = $dir . $name . '.cache';
        } else {
            $filename = $name . '.cache';
        }
        return $this->dir . $filename;
    }

    /**
     * 设置一个缓存变量
     *
     * @param String $key    缓存Key
     * @param mixed $value   缓存内容
     * @param int $expire    缓存时间(秒)
     * @return boolean       是否缓存成功
     * @access public
     * @abstract
     */
    public function set($key, $value, $expire = 60)
    {
        $file = $this->filename($key);
        if (file_put_contents($file, serialize($value), LOCK_EX)) {            
            touch($file, $this->time + $expire);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取一个已经缓存的变量
     *
     * @param String $key  缓存Key
     * @return mixed       缓存内容
     * @access public
     */
    public function get($key)
    {
        $file = $this->filename($key);
        if (is_file($file)) {
            if ($this->time <= filemtime($file)) {
                return unserialize(file_get_contents($file));
            } else {
                @unlink($file);
                //删除缓存
                return false;
            }
        } else {
            //没有找到缓存
            return false;
        }
    }

    /**
     * 删除一个已经缓存的变量
     *
     * @param  $key
     * @return boolean       是否删除成功
     * @access public
     */
    public function del($key)
    {
        $file = $this->filename($key);
        return @unlink($file);
    }

    /**
     * 删除全部缓存变量
     *
     * @return boolean       是否删除成功
     * @access public
     */
    public function delAll()
    {
        $files = scandir($this->dir);
        $files = array_diff($files, array('.', '..'));
        foreach ($files as $file) {
            @unlink($file);
        }
        return true;
    }

    /**
     * 检测是否存在对应的缓存
     *
     * @param string $key   缓存Key
     * @return boolean      是否存在key
     * @access public
     */
    public function has($key)
    {
        $file = $this->filename($key);
        return (is_file($file) === NULL ? false : true);
    }

}