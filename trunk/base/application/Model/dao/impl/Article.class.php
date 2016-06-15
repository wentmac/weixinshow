<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Article.class.php 330 2016-06-01 14:04:25Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_Article_base extends dao_BaseDao_base implements dao_Article_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'article';
        $this->addonarticle_table = DB_WS_PREFIX . 'add_article';
        $this->setPrimaryKeyField( 'article_id' );
    }

    /**
     * 取所有的Article
     * @param type $id
     * @return type
     */
    public function getArticleInfoById( $id )
    {
        $sql = "SELECT a.*, b.content "
                . "FROM {$this->getTable()} a LEFT JOIN {$this->addonarticle_table} b "
                . "ON a.article_id = b.article_id "
                . "WHERE a.article_id ={$id} "
                . "LIMIT 0, 1";
        $rs = $this->getDb()->getRowObject( $sql );
        return $rs;
    }

    /**
     * 根据筛选条件返回where语句
     * @param entity_parameter_Article_base $entity_parameter_Article_base


     */
    public function getListWhere( entity_parameter_Article_base $entity_parameter_Article_base )
    {
        $where = "status =" . service_Article_base::status_public.' ';
        if ( $entity_parameter_Article_base->getCat_ids() != null ) {
            $where .= " AND cat_id IN({$entity_parameter_Article_base->getCat_ids()})";
        } else if ( $entity_parameter_Article_base->getCat_id() != null ) {
            $where .= "AND cat_id = {$entity_parameter_Article_base->getCat_id()} ";
        }

        if ( $entity_parameter_Article_base->getQuery() != null ) {
            $where .= "AND title LIKE '%" . $entity_parameter_Article_base->getQuery() . "%' ";
        }

        if ( $entity_parameter_Article_base->getChannelid() != null ) {
            $where .= "AND channel = '{$entity_parameter_Article_base->getChannelid()}' ";
        }
        return $where;
    }

}
