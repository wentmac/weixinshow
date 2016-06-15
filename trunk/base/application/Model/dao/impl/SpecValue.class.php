<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: SpecValue.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_SpecValue_base extends dao_BaseDao_base
{

    public function __construct($link_identifier)
    {
        parent::__construct($link_identifier);
        $this->table = DB_WS_PREFIX . 'spec_value';        
        $this->setPrimaryKeyField('spec_value_id');
    }
}
