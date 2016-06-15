<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: SpecMap.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_SpecMap_base extends dao_BaseDao_base
{

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'spec_map';
        $this->setPrimaryKeyField( 'spec_map_id' );
    }

    /**
     * 为新用户创建新的商品规格类型
     */
    public function createMemberSpecMap( $uid )
    {
        $sql = "insert into `" . DB_WS_PREFIX . "spec_map` (`spec_id`, `spec_name`, `spec_sort`, `uid`) values('1','商品规格','0','{$uid}')"
                . ",('2','容量','0','{$uid}')"
                . ",('4','规格','0','{$uid}')"
                . ",('6','颜色','0','{$uid}')"
                . ",('7','系列','0','{$uid}')"
                . ",('27','材质','0','{$uid}')"
                . ",('30','尺寸','0','{$uid}')";
        $res = $this->getDb()->execute( $sql );
        return $res;
    }

}
