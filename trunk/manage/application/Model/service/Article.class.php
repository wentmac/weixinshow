<?php

/**
 * 后台文章模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Article.class.php 331 2016-06-01 19:34:34Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Article_manage extends service_Article_base
{

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        
    }

    /**
     * 保存
     * @param string $menusMain
     * return Boole
     */
    public function modifyArticleAll( entity_Article_base $entity_Article_base, entity_AddArticle_base $entity_AddArticle_base )
    {

        $dao = dao_factory_base::getArticleDao();

        $dao->getDb()->startTrans();

        $dao->setPk( $entity_Article_base->article_id );
        $dao->updateByPk( $entity_Article_base );

        $dao_addonarticle = dao_factory_base::getAddArticleDao();
        $dao_addonarticle->setPk( $entity_AddArticle_base->article_id );
        $dao_addonarticle->updateByPk( $entity_AddArticle_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * insert
     * @param string $menusMain
     * return Boole
     */
    public function createArticleAll( entity_Article_base $entity_Article_base, entity_AddArticle_base $entity_AddArticle_base )
    {

        $dao = dao_factory_base::getArticleDao();

        $dao->getDb()->startTrans();
        $entity_Article_base->time = $this->now;
        $article_id = $dao->insert( $entity_Article_base );

        $dao_addonarticle = dao_factory_base::getAddArticleDao();
        $entity_AddArticle_base->article_id = $article_id;
        $dao_addonarticle->insert( $entity_AddArticle_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return $article_id;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 获取一个资讯栏目信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getArticleInfo( $aid, $image_size = '200x150' )
    {
        $dao = dao_factory_base::getArticleDao();
        $articleInfo = $dao->getArticleInfoById( $aid );
        $articleInfo->article_image_url = $this->getImage( $articleInfo->article_image_id, $image_size, 'article' );
        return $articleInfo;
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

    /**
     * del
     * @param int $class_id
     */
    public function deleteByArticleId( $id )
    {
        $dao = dao_factory_base::getArticleDao();
        $add_article_dao = dao_factory_base::getAddArticleDao();

        $dao->getDb()->startTrans();
        $entity_Article_base = new entity_Article_base();
        $entity_Article_base->status = service_Article_base::status_delete;

        $entity_AddArticle_base = new entity_AddArticle_base();
        $entity_AddArticle_base->status = service_Article_base::status_delete;

        $where = $dao->getWhereInStatement( 'article_id', $id );

        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_Article_base );
        $add_article_dao->setWhere( $id );
        $add_article_dao->updateByWhere( $entity_AddArticle_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 取用户Array
     * @param type $uid
     * @return type 
     */
    public function getUserArray( $uid )
    {
        $dao = dao_factory_base::getUserDao();
        if ( $uid > 1 ) {
            $dao->setPk( $uid );
            $dao->setField( 'uid,nicename' );
            $res = $dao->getInfoByPk();
        } else {
            $dao->setField( 'uid, nicename' );
            $dao->setOrderby( 'uid ASC' );
            $res = $dao->getListByWhere();
        }
        return $res;
    }

}
