<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: CollectItem.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_CollectItem_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'collect_item';
        $this->item_table = DB_WS_PREFIX . 'item';
        $this->setPrimaryKeyField( 'collect_item_id' );
    }

    public function getCollectItemArray( $uid )
    {
        $sql = "SELECT a.*, b.item_name,b.item_price,b.goods_image_id "
                . "FROM {$this->getTable()} a INNER JOIN {$this->item_table} b "
                . "ON a.item_id = b.item_id "
                . "WHERE a.uid={$uid} AND a.is_delete=0";
        if ( $this->getOrderby() != null ) {            
            $sql .= " ORDER BY {$this->getOrderby()}";
        }
        if ( $this->getLimit() != null ) {
            $sql .= " LIMIT {$this->getLimit()}";
        }
        return $this->getDb()->getAllObject( $sql );
    }

}
