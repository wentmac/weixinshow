<?php

/*
 * Tmac PHP MVC framework
 * $Author: ZhangWentao<zwt007@gmail.com> $
 * $Id: Category.php 16881 2009-12-14 09:19:16Z ZhangWentao $
 */

/**
 * Description of Category
 *
 * @author Tracy McGrady
 */
class dao_impl_Category_base extends dao_BaseDao_base
{

    public function __construct($link_identifier)
    {
        parent::__construct($link_identifier);
        $this->table = DB_WS_PREFIX . 'category';
        $this->setPrimaryKeyField('cat_id');
    }


}
