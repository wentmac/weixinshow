<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: GoodsCategoryMap.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_GoodsCategoryMap_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->goods_category_table = DB_WS_PREFIX . 'goods_category';
        $this->table = DB_WS_PREFIX . 'goods_category_map';
        $this->setPrimaryKeyField( 'goods_cat_map_id' );
    }

    public function updateGoodsCategoryGoodsCount( $goods_id )
    {
        $where = $this->getWhereInStatement( 'goods_id', $goods_id );
        $sql = "UPDATE {$this->goods_category_table} SET goods_count=goods_count-1 WHERE goods_cat_id IN("
                . "SELECT goods_cat_id FROM {$this->table} WHERE {$where} AND is_delete=0"
                . ")";
        $res = $this->getDb()->execute( $sql );
        return $res;
    }

    public function updateCategoryGoodsCount()
    {
        $sql = "UPDATE {$this->goods_category_table} SET goods_count=("
                . "SELECT COUNT(*) FROM {$this->table} WHERE {$this->table}.goods_cat_id={$this->goods_category_table}.goods_cat_id AND is_delete=0"
                . ")";
        $res = $this->getDb()->execute( $sql );
        return $res;
    }

}
