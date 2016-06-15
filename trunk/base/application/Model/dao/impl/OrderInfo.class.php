<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: OrderInfo.class.php 366 2016-06-13 11:01:36Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_OrderInfo_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'order_info';
        $this->order_goods_table = DB_WS_PREFIX . 'order_goods';
        $this->setPrimaryKeyField( 'order_id' );
    }

    public function getOrderListWhereByOrderId( $goods_id )
    {
        $new_where = "order_id IN(SELECT order_id FROM {$this->order_goods_table} WHERE goods_id={$goods_id})";
        return $new_where;
    }


}
