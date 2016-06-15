<?php

/**
 * 后台文章栏目模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Article.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_help_Article_admin extends service_help_Article_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 保存
     * @param string $menusMain
     * return Boole
     */
    public function modifyHelpArticle( entity_HelpArticle_base $entity_HelpArticle_base )
    {
        $dao = dao_factory_base::getHelpArticleDao();

        $dao->setPk( $entity_HelpArticle_base->help_article_id );
        return $dao->updateByPk( $entity_HelpArticle_base );
    }

    /**
     * insert
     * @param string $menusMain
     * return Boole
     */
    public function createHelpArticle( entity_HelpArticle_base $entity_HelpArticle_base )
    {
        $dao = dao_factory_base::getHelpArticleDao();
        $entity_HelpArticle_base->help_time = $this->now;
        return $dao->insert( $entity_HelpArticle_base );
    }

    /**
     * 获取所有资讯
     * return article_class,pages
     */
    public function getHelpArticleList()
    {

        if ( empty( $this->url ) ) {
            $url = PHP_SELF . '?m=help/article';
        } else {
            $url = $this->url;
        }
        $dao = dao_factory_base::getHelpArticleDao();
        $where = 'is_delete=0';
        if ( !empty( $this->help_cat_id ) ) {
            $where .= " AND help_cat_id={$this->help_cat_id}";
            $url .= "&cat_id={$this->help_cat_id}";
        }
        if ( !empty( $this->help_recommend ) ) {
            $where .= " AND help_recommend={$this->help_recommend}";
            $url .= "&help_recommend={$this->help_recommend}";
        }
        if ( !empty( $this->query_string ) ) {
            $where .= " AND help_title LIKE '%{$this->query_string}%'";
            $url .= "&search_keyword={$this->query_string}";
        }
        $url .= '&page=';
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $url );
        $pages->setPrepage( 20 );
        $limit = $pages->getSqlLimit();

        $dao->setField( 'help_article_id,help_cat_id,help_title,help_keywords,help_status,help_time' );
        $dao->setOrderby( 'help_sort DESC,help_article_id DESC' );
        $dao->setLimit( $limit );

        $rs = $dao->getListByWhere();

        //取所有的资讯栏目 不用LEFT JOIN取class_name
        $dao_category = dao_factory_base::getHelpCategoryDao();
        $rs_class = $dao_category->getListByWhere();

        $category_name_array = array();
        //重组栏目category信息数组
        foreach ( $rs_class AS $vv ) {
            $category_name_array[ $vv->help_cat_id ] = $vv->cat_name;
        }
        //遍历通过class_id取class_name
        if ( is_array( $rs ) ) {
            foreach ( $rs AS $v ) {
                $v->cat_name = $category_name_array[ $v->help_cat_id ];
                $v->help_time = date( 'Y/m/d H:i:s', $v->help_time );
            }
        }

        //把文章的当前page写到cookies里
        HttpResponse::setCookie( 'article_page_', $pages->getNowPage() );

        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无资讯文章!";
        }

        $result = array(
            'rs' => $rs,
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg
        );
        return $result;
    }

    /**
     * del
     * @param int $class_id
     */
    public function deleteHelpArticleId( $id )
    {
        $dao = dao_factory_base::getHelpArticleDao();

        $dao->getDb()->startTrans();
        $entity_HelpArticle_base = new entity_HelpArticle_base();
        $entity_HelpArticle_base->is_delete = 1;
        if ( strpos( $id, ',' ) === false ) {
            $dao->setPk( $id );
            $dao->updateByPk( $entity_HelpArticle_base );
        } else {
            $dao->setWhere( "help_article_id IN({$id})" );
            $dao->updateByWhere( $entity_HelpArticle_base );
        }

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

}
