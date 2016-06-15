<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Item.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_Item_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'item';
        $this->item_category_map_table = DB_WS_PREFIX . 'item_category_map';
        $this->setPrimaryKeyField( 'item_id' );
    }

    public function getItemListWhereByCid( $where, $item_cat_id = 0 )
    {
        $item_cat_id_where = $this->getWhereInStatement( 'item_cat_id', $item_cat_id );

        $new_where = "{$where}";
        if ( !empty( $item_cat_id ) ) {
            $new_where .= " AND item_id IN(SELECT item_id FROM {$this->item_category_map_table} WHERE {$item_cat_id_where} AND is_delete=0)";
        }
        return $new_where;
    }

}
