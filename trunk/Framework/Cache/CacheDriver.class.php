<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: CacheDriver.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class CacheDriver
{

    /**
     * 缓存实例
     *
     * @var object
     */
    static protected $instance = array();

    /**
     * 取得缓存实例
     *
     * @return objeact
     * @access public
     * @static
     */
    public static function getInstance($name=null)
    {
        if (empty($name))
            $name = $GLOBALS['TmacConfig']['Cache']['class'];
        $class = 'Cache' . $name;
        if (!isset(self::$instance[$name])) {
            $filename = TMAC_PATH . 'Cache' . DIRECTORY_SEPARATOR . $class . '.class.php';
            if (is_file($filename)) {
                require $filename;
            } else {
                throw new TmacException("没有找到{$class}缓存驱动!");
            }
            self::$instance[$name] = new $class();
        }
        return self::$instance[$name];
    }

}