<?php
/**
 *
 * @Authors k-feng (wfylife@163.com)
 * @DateTime    2014-08-21 23:26:44
 * @version $1.0$
 */

class dao_impl_Region_base extends dao_BaseDao_base
{
    public function __construct($link_identifier)
    {
        parent::__construct($link_identifier);
        $this->table = DB_WS_PREFIX . 'region';
        $this->setPrimaryKeyField('region_id');
    }
}