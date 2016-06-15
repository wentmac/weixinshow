<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: GoodsPrice.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_GoodsPrice_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'goods_price';
        $this->setPrimaryKeyField( 'goods_price_id' );
    }

}
