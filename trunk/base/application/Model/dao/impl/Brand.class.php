<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Brand.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_Brand_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'brand';
        $this->setPrimaryKeyField( 'brand_id' );
    }

}
