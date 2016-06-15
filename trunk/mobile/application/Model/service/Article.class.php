<?php

/**
 * 后台文章模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Article.class.php 331 2016-06-01 19:34:34Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Article_mobile extends service_Article_base
{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 获取所有资讯
     * return article_class,pages
     */
    public function getArticleList( entity_parameter_Article_base $entity_parameter_Article_base )
    {

        if ( $entity_parameter_Article_base->getUrl() == null ) {
            $url = PHP_SELF . '?m=article';
        } else {
            $url = $entity_parameter_Article_base->getUrl();
        }
        if ( $entity_parameter_Article_base->getCat_id() != null ) {
            $cat_ids = $this->M( 'Category' )->getSonTreeList( $entity_parameter_Article_base->getCat_id() );
            if ( $cat_ids != $entity_parameter_Article_base->getCat_id() ) {
                $entity_parameter_Article_base->setCat_ids( $cat_ids );
            }
            $url .= "&cat_id={$entity_parameter_Article_base->getCat_id()}";
        }
        if ( $entity_parameter_Article_base->getQuery() != null ) {
            $url .= "&query={$entity_parameter_Article_base->getQuery()}";
        }
        if ( $entity_parameter_Article_base->getChannelid() != null ) {
            $url .= "&channelid={$entity_parameter_Article_base->getChannelid()}";
        }

        $dao = dao_factory_base::getArticleDao();
        $where = $dao->getListWhere( $entity_parameter_Article_base );
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setPrepage( $entity_parameter_Article_base->getPagesize() );
        $limit = $pages->getSqlLimit();

        $dao->setField( 'article_id, cat_id, title, time, click_count, channel, status, edit_time' );
        $dao->setOrderby( 'article_id DESC' );
        $dao->setLimit( $limit );

        $article_array = array();
        if ( $count > 0 ) {
            $rs = $dao->getListByWhere();

            //取所有的资讯栏目 不用LEFT JOIN取class_name
            $dao_category = dao_factory_base::getCategoryDao();
            $rs_class = $dao_category->getListByWhere();

            //取内容模型
            $channeltype = Tmac::config( 'channel.channeltype', APP_ADMIN_NAME );

            //取状态
            $status_array = Tmac::config( 'article.status.boolean', APP_ADMIN_NAME );

            $category_name_array = array();
            //重组栏目category信息数组
            foreach ( $rs_class AS $vv ) {
                $category_name_array[ $vv->cat_id ] = $vv->cat_name;
            }
            //遍历通过class_id取class_name
            foreach ( $rs AS $k => $v ) {
                $rs[ $k ]->cat_name = $category_name_array[ $v->cat_id ];
                $rs[ $k ]->time = date( 'Y/m/d H:i:s', $v->time );
                $rs[ $k ]->edit_time = date( 'Y/m/d H:i:s', $v->edit_time );
                $rs[ $k ]->channeltype = isset( $channeltype[ $v->channel ] ) ? $channeltype[ $v->channel ] : '';
                $rs[ $k ]->status = $status_array[ $v->status ];
            }

            $article_array = $rs;
        }
        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / $entity_parameter_Article_base->getPagesize() ) ),
            'pagesize' => $entity_parameter_Article_base->getPagesize(),
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'seller_order_list',
            'retmsg' => $retmsg,
            'reqdata' => $article_array,
        );
        return $return;
    }

    
}
