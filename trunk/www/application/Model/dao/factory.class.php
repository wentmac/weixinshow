<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: factory.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of DaoFactory
 *
 * @author Tracy McGrady
 */
abstract class dao_factory_api
{

//put your code here
    /**
     * dao_factory_base::createDao('article',$this->db);
     * @param type $name
     * @param type $link_identifier
     * @throws TmacException
     */
    public static function createDao($name, $link_identifier)
    {
        $className = $className = 'dao_impl_' . $name . '_' . APP_API_NAME; //java版    Class clazz = Class.forName(className);            
        return new $className($link_identifier);
    }

    public static function getArticleDao($link_identifier)
    {
        return new dao_impl_article_api($link_identifier);
    }

}
