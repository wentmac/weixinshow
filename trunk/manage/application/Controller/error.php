<?php

/**
 * 前台 404 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: error.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class errorAction extends Action
{

    private $tmp_model;
    private $hotel_left_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('liansuocity');
        $this->hotel_left_model = Tmac::model('hotelLeft');

        //footer里的相关设置
        $this->hotel_left_model->footer();
    }

    /**
     * 城市地标 首页
     */
    public function index()
    {               
        $this->V('404');
    }

}