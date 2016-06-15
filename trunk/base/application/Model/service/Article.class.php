<?php

/**
 * 后台文章模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Article.class.php 331 2016-06-01 19:34:34Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Article_base extends service_Model_base
{

    /**
     * 文章状态正常
     */
    const status_public = 0;

    /**
     * 文章状态删除
     */
    const status_delete = 1;

    public function __construct()
    {
        parent::__construct();
    }

   
    /**
     * 获取一个资讯栏目信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getArticleInfo( $aid )
    {
        $dao = dao_factory_base::getArticleDao();
        $articleInfo = $dao->getArticleInfoById( $aid );
        $articleInfo->article_image_url = $this->getImage( $articleInfo->article_image_id, '200x150', 'article' );
        return $articleInfo;
    }
}
