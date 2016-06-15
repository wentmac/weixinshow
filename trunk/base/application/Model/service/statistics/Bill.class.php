<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_statistics_Bill_base extends service_Statistics_base implements service_statistics_Interface_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取一周数据总数
     * 后面建一个数据统计表。每天晚上定时跑当天的订单 存进统计表中。统计表直接查询
     */
    public function getWeekCount()
    {
        $dao = dao_factory_base::getMemberBillDao();
        $week_time = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 7, date( "Y" ) );
        $where = "uid={$this->uid} AND bill_type=" . service_Member_base::bill_type_income . " AND bill_time>={$week_time}";
        $dao->setWhere( $where );
        $dao->setField( 'SUM(money) AS total' );
        $res = $dao->getInfoByWhere();
        if ( empty( $res ) ) {
            return 0;
        }
        return $res->total;
    }

    /**
     * 取一周数据
     * 后面建一个数据统计表。每天晚上定时跑当天的订单 存进统计表中。统计表直接查询
     */
    public function getWeekList()
    {
        $dao = dao_factory_base::getMemberBillDao();
        $week_time = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 7, date( "Y" ) );
        $where = "uid={$this->uid} AND bill_type=" . service_Member_base::bill_type_income . " AND bill_time>={$week_time} GROUP BY `date` ";
        $dao->setWhere( $where );
        $dao->setField( "FROM_UNIXTIME(bill_time,'%m-%d') AS `date`,SUM(money) AS total" );
        $dao->setOrderby( 'bill_time DESC' );
        $res = $dao->getListByWhere();

        $week_day_array = $this->dateRange( date( 'Y-m-d', $week_time ), date( "Y-m-d" ) );
        $day_have_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $day_have_array[ $value->date ] = $value->total;
            }
        }

        $result_array = array();
        foreach ( $week_day_array as $value ) {
            $rs = array();
            $rs[ 'date' ] = $value;
            $rs[ 'total' ] = 0;
            if ( !empty( $day_have_array[ $value ] ) ) {
                $rs[ 'total' ] = $day_have_array[ $value ];
            }
            $result_array[] = $rs;
        }
        return $result_array;
    }

    public function getDetailList()
    {
        $dao = dao_factory_base::getMemberBillDao();
        $week_time = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 30, date( "Y" ) );
        $where = "uid={$this->uid} AND bill_type=" . service_Member_base::bill_type_income . " AND bill_time>={$week_time} GROUP BY `date` ";
        $dao->setWhere( $where );
        $dao->setField( "FROM_UNIXTIME(bill_time,'%Y-%m-%d') AS `date`,SUM(money) AS total" );
        $dao->setOrderby( 'bill_time DESC' );
        $res = $dao->getListByWhere();
        return $res;
    }

}
