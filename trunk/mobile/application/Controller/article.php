<?php

/**
 * mobile 购物车 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: article.php 331 2016-06-01 19:34:34Z zhangwentao $
 * http://www.t-mac.org；
 */
class articleAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $article_id = Input::get( 'id', 0 )->required( '文章不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            parent::no( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_Article_mobile();
        $article_info = $model->getArticleInfo( $article_id );

        if ( !$article_info ) {
            parent::no( '文章不存在！' );
        } else {
            if ( $article_info->status == service_Article_base::status_delete ) {
                parent::no( '文章已经删除' );
            }
        }

        $array[ 'article_info' ] = $article_info;
//           echo '<pre>';
//           print_r( $array );
//         echo "</pre>";
//        die;

        $this->assign( $array );
        $this->V( 'article' );
    }

}
