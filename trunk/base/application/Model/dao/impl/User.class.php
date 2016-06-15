<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: User.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_User_base extends dao_BaseDao_base implements dao_User_base
{

    public function __construct($link_identifier)
    {
        parent::__construct($link_identifier);
        $this->table = DB_PREFIX . 'user';
        $this->usertype_table = DB_PREFIX . 'user_type';
        $this->setPrimaryKeyField('uid');
    }

    public function getUserInfoByUsername($username)
    {
        $sql = "SELECT a.type_purviews, b.* "
                . "FROM $this->usertype_table a LEFT JOIN {$this->getTable()} b "
                . "ON a.rank = b.rank "
                . "WHERE b.username = '{$username}' "
                . "LIMIT 0, 1";
        $info = $this->getDb()->getRowObject($sql);
        return $info;
    }

}
