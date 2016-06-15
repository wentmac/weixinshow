<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: da.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class daAction extends service_Controller_mobile
{

    public function index()
    {
        $union = '';
        if ( isset( $_GET[ 'bd' ] ) ) {
            $union = 'bd';
        }

        $array[ 'union' ] = $union;
        $this->assign( $array );
        $this->V( 'da/index' );
    }

    public function about()
    {
        $this->V( 'da/about' );
    }

    public function seller()
    {
        $this->V( 'da/seller' );
    }

    public function supplier()
    {
        $this->V( 'da/supplier' );
    }

}
