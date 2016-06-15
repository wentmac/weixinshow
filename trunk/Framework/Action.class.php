<?php

/**
 * Action控制器基类
 * ====================================================================
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Action.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class Action extends Base
{

    /**
     * 构造函数 初始化
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取脚本执行时间
     *
     * @return float
     * @access public
     */
    public final function getActionTime()
    {
        global $TMAC_START_TIME;
        return microtime(true) - $TMAC_START_TIME;
    }

    /**
     * 析构函数
     *
     * @global array $TmacConfig
     */
    public final function __destruct()
    {
        global $TmacConfig;
        //打印debug信息
        if ($TmacConfig['Common']['debug']) {
            $debug = Debug::getInstance();
            //打印页面Trace信息：加载文件
            $debug->setIncludeFile();
            echo $debug->getDebug();
            
        }
    }

}