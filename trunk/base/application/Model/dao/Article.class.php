<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Article.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
interface dao_Article_base
{        
    public function getListWhere(entity_parameter_Article_base $entity_parameter_Article_base);
}
