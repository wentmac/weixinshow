<?php

/**
 * WEB后台 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Controller.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
abstract class service_Controller_crontab extends service_Controller_base
{
        

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
        // $this->memberInfo = new stdClass();
        // $this->memberInfo->uid = 1;
    }

}
