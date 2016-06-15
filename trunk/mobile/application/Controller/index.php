<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: index.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class indexAction extends service_Controller_mobile
{

    public function index()
    {
        header( 'location:' . MOBILE_URL . 'shop/' . service_Member_base::yph_uid );
        $union = Input::get( 'union', '' )->string();

        $array[ 'union' ] = $union;
        $this->assign( $array );
        $this->V( 'index_2016' );
    }

    public function download()
    {
        $union = Input::get( 'union', '' )->string();

        $array[ 'union' ] = $union;
        $array[ 'download_url' ] = INDEX_URL . 'download/090.apk';
        $array[ 'qq_download_url' ] = 'http://a.app.qq.com/o/simple.jsp?pkgname=cn.wd090.wd&g_f=100';
        $this->assign( $array );
        $this->V( 'index_download' );
    }

    public function mtext()
    {
        /*
          $cache = CacheDriver::getInstance('Memcached');
          $cache->set('key', 'valuerepl2ce11',600);
          $val = $cache->get('key');
          echo $val; //输出结果为“value”
         */
        $this->common_model->checkToken( 22, 'ddd' );
    }

}
