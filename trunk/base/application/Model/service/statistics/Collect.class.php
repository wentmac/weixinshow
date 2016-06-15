<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_statistics_Collect_base extends service_Statistics_base implements service_statistics_Interface_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取一周数据
     * 后面建一个数据统计表。每天晚上定时跑当天的订单 存进统计表中。统计表直接查询
     */
    public function getWeekCount()
    {
        $dao = dao_factory_base::getCollectItemDao();
        $week_time = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 7, date( "Y" ) );
        $where = "uid={$this->uid} AND collect_time>={$week_time}";
        $dao->setWhere( $where );
        $dao->setField( "COUNT(*) AS total" );
        $res_item = $dao->getInfoByWhere();
        if ( empty( $res_item ) ) {
            $collect_item_count = 0;
        } else {
            $collect_item_count = $res_item->total;
        }

        /**
          $collect_shop_dao = dao_factory_base::getCollectShopDao();
          $where = "uid={$this->uid} AND collect_time>={$week_time}";
          $collect_shop_dao->setWhere( $where );
          $collect_shop_dao->setField( "COUNT(*) AS total" );
          $res_shop = $dao->getInfoByWhere();
          if ( empty( $res_shop ) ) {
          $collect_shop_count = 0;
          } else {
          $collect_shop_count = $res_shop->total;
          }
         * 
         */
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $member_setting_dao->setPk( $this->uid );
        $member_setting_dao->setField( 'collect_count,collect_count_variable' );
        $memberSettingInfo = $member_setting_dao->getInfoByPk();

        return $collect_item_count + $memberSettingInfo->collect_count + $memberSettingInfo->collect_count_variable;
    }

    /**
     * 取一周数据
     * 后面建一个数据统计表。每天晚上定时跑当天的订单 存进统计表中。统计表直接查询
     */
    public function getWeekList()
    {
        $dao = dao_factory_base::getCollectItemDao();
        $week_time = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 7, date( "Y" ) );
        $where = "uid={$this->uid} AND collect_time>={$week_time} GROUP BY `date` ";
        $dao->setWhere( $where );
        $dao->setField( "FROM_UNIXTIME(collect_time,'%m-%d') AS `date`,COUNT(*) AS total" );
        $dao->setOrderby( 'collect_time DESC' );
        $res_item = $dao->getListByWhere();

        $collect_shop_dao = dao_factory_base::getCollectShopDao();
        $where = "uid={$this->uid} AND collect_time>={$week_time} GROUP BY `date` ";
        $collect_shop_dao->setWhere( $where );
        $collect_shop_dao->setField( "FROM_UNIXTIME(collect_time,'%m-%d') AS `date`,COUNT(*) AS total" );
        $collect_shop_dao->setOrderby( 'collect_time DESC' );
        $res_shop = $dao->getListByWhere();


        $week_day_array = $this->dateRange( date( 'Y-m-d', $week_time ), date( "Y-m-d" ) );
        $day_have_array = array();
        if ( $res_item ) {
            foreach ( $res_item as $value ) {
                $day_have_array[ $value->date ] = $value->total;
            }
        }
        if ( $res_shop ) {
            foreach ( $res_shop as $value ) {
                if ( isset( $day_have_array[ $value->date ] ) ) {
                    $day_have_array[ $value->date ] += $value->total;
                } else {
                    $day_have_array[ $value->date ] = $value->total;
                }
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
        $dao = dao_factory_base::getCollectItemDao();
        $week_time = mktime( 0, 0, 0, date( "m" ), date( "d" ) - 30, date( "Y" ) );
        $where = "uid={$this->uid} AND collect_time>={$week_time} GROUP BY `date` ";
        $dao->setWhere( $where );
        $dao->setField( "FROM_UNIXTIME(collect_time,'%Y-%m-%d') AS `date`,COUNT(*) AS total" );
        $dao->setOrderby( 'collect_time DESC' );
        $res_item = $dao->getListByWhere();

        $collect_shop_dao = dao_factory_base::getCollectShopDao();
        $where = "uid={$this->uid} AND collect_time>={$week_time} GROUP BY `date` ";
        $collect_shop_dao->setWhere( $where );
        $collect_shop_dao->setField( "FROM_UNIXTIME(collect_time,'%Y-%m-%d') AS `date`,COUNT(*) AS total" );
        $collect_shop_dao->setOrderby( 'collect_time DESC' );
        $res_shop = $dao->getListByWhere();


        $week_day_array = $this->dateRange( date( 'Y-m-d', $week_time ), date( "Y-m-d" ) );
        $day_have_array = array();
        if ( $res_item ) {
            foreach ( $res_item as $value ) {
                $day_have_array[ $value->date ] = $value->total;
            }
        }
        if ( $res_shop ) {
            foreach ( $res_shop as $value ) {
                if ( isset( $day_have_array[ $value->date ] ) ) {
                    $day_have_array[ $value->date ] += $value->total;
                } else {
                    $day_have_array[ $value->date ] = $value->total;
                }
            }
        }

        $return_array = array();
        foreach ( $day_have_array as $key => $value ) {
            $rs = array();
            $rs[ 'date' ] = $key;
            $rs[ 'total' ] = $value;
            $return_array[] = $rs;
        }
        return $return_array;
    }

}
