<?php

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_HelpArticle_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'help_article';
        $this->setPrimaryKeyField( 'help_article_id' );
    }

}
