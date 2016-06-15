<?php

/**
 *
 * @Authors
 * @DateTime    2014-07-26 02:54:24
 * @version $1.0$
 */
class service_Region_base extends Model
{

    public function getRegionListByPid( $pid )
    {
        $dao = dao_factory_base::getRegionDao();
        $dao->setField( 'region_id,region_name' );
        $dao->setWhere( 'parent_id=' . $pid );
        return $dao->getListByWhere();
    }

    public function getRegionNameById( $rid )
    {
        $dao = dao_factory_base::getRegionDao();
        $dao->setField( 'region_name' );
        $dao->setPk( $rid );
        return $dao->getInfoByPk();
    }

}
