<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Goods.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_Goods_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'goods';
        $this->goods_category_map_table = DB_WS_PREFIX . 'goods_category_map';
        $this->item_table = DB_WS_PREFIX . 'item';
        $this->setPrimaryKeyField( 'goods_id' );
    }

    public function getGoodsListWhereByCid( $goods_cat_id = 0, $just_this_goods_cat_id = 0 )
    {
        $is_cloud_product = service_GoodsCategory_base::is_cloud_product_yes;
        $goods_category_model = new service_GoodsCategory_base();
        if ( empty( $just_this_goods_cat_id ) ) {
            $goods_cat_id = $goods_category_model->getSonTreeList( $goods_cat_id, $is_cloud_product );
        }
        $goods_cat_id_where = $this->getWhereInStatement( 'goods_cat_id', $goods_cat_id );

        $new_where = '';
        if ( !empty( $goods_cat_id ) ) {
            $new_where .= " AND goods_id IN(SELECT goods_id FROM {$this->goods_category_map_table} WHERE {$goods_cat_id_where} AND is_delete=0)";
        }
        return $new_where;
    }

    public function updateGoodsSellerCount( $goods_id_string )
    {
        $where = $this->getWhereInStatement( 'goods_id', $goods_id_string );
        $sql = "UPDATE {$this->table} SET seller_count=(SELECT COUNT(*) FROM {$this->item_table} WHERE {$where} AND is_delete=0) WHERE {$where}";
        return $this->getDb()->execute( $sql );
    }

}
