<?php

/** Power By Tmac PHP MVC framework
 *  $Author: zhangwentao $
 *  $Id: DatabaseDriver.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */
class DatabaseDriver
{

    /**
     * 数据库实例
     *
     * @var object
     * @static
     */
    protected static $instance = array();

    /**
     * 取得缓存实例
     *
     * @return object
     * @access public
     * @static
     */
    public static function getInstance($name)
    {
        $class = 'Db' . $GLOBALS['db'][$name]['dbdriver'];
        if (!isset(self::$instance[$name])) {
            if (class_exists($class, false) === false) {
                $filename = TMAC_PATH . 'Database' . DIRECTORY_SEPARATOR . $class . '.class.php';
                if (is_file($filename)) {
                    require $filename;
                } else {
                    throw new TmacException("没有找到{$class}数据库驱动!");
                }
            }
            $config = $GLOBALS['db'][$name];
            self::$instance[$name] = new $class($config);
        }
        return self::$instance[$name];
    }

}