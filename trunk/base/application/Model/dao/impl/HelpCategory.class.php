<?php

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_HelpCategory_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'help_category';
        $this->setPrimaryKeyField( 'help_cat_id' );
    }

}
