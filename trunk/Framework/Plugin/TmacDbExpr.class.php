<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: TmacDbExpr.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of TmacDbExpr
 *
 * @author Tracy McGrady
 * $entity_HelpArticle_base->setHelp_good_count(new TmacDbExpr('help_good_count-1'));        
   $entity_HelpArticle_base->setHelp_description(new TmacDbExpr('NOW()'));
 * UPDATE zu_help_article SET `help_title` = 'abcd20141', `help_description` = NOW(), `help_good_count` = help_good_count-1 WHERE help_article_id=33
 */
class TmacDbExpr
{

    /**
     * Storage for the SQL expression.
     *
     * @var string
     */
    protected $_expression;

    /**
     * Instantiate an expression, which is just a string stored as
     * an instance member variable.
     *
     * @param string $expression The string containing a SQL expression.
     */
    public function __construct($expression)
    {
        $this->_expression = (string) $expression;
    }

    /**
     * @return string The string of the SQL expression stored in this object.
     */
    public function __toString()
    {
        return $this->_expression;
    }

}
