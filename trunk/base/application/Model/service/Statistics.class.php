<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
abstract class service_Statistics_base extends service_Model_base
{

    protected $uid;
    protected $memberInfo;
    protected $errorMessage;

    public function __construct()
    {
        parent::__construct();
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 工厂创建
     * @param type $source
     * @return type 
     */
    public static function factory( $type )
    {
        $model = 'statistics/' . ucfirst( $type );
        return Tmac::model( $model, APP_BASE_NAME );
    }

    /**
     * 计算出起始时间和结束时间之间的时间
     * @param type $d1
     * @param type $d2
     * @return type 
     */
    protected function dateRange( $d1, $d2 )
    {

        $timestamp1 = strtotime( $d1 );
        $timestamp2 = strtotime( $d2 );
        if ( $timestamp1 == $timestamp2 )
            return;
        if ( $timestamp1 > $timestamp2 )
            return false;
        $n = round( ($timestamp2 - $timestamp1) / 3600 / 24 );
        $arr = array();
        for ( $i = 0; $i <= $n; $i++ ) {
            $arr[] = date( 'm-d', $timestamp1 + ($i * 24 * 3600) );
        }
        return $arr;
    }

    /**
     * 根据开始结果时间取周数
     * @param type $d1
     * @param type $d2
     * @return type 
     */
    protected function weekRange( $d1, $d2 )
    {
        $timestamp1 = strtotime( $d1 );
        $timestamp2 = strtotime( $d2 );
        if ( $timestamp1 == $timestamp2 )
            return;
        if ( $timestamp1 > $timestamp2 )
            return false;
        $n = round( ($timestamp2 - $timestamp1) / 3600 / 24 / 7 );
        $arr = array();
        for ( $i = 0; $i <= $n; $i++ ) {
            if ( $i == 0 ) {
                if ( date( 'W', $timestamp1 + ($i * 7 * 24 * 3600) ) > '01' )
                    continue;
            }
            $arr[] = date( 'Y-W', $timestamp1 + ($i * 7 * 24 * 3600) );
        }
        return $arr;
    }

    /**
     * 根据开始结果时间取周数
     * @param type $d1
     * @param type $d2
     * @return type 
     */
    protected function monthRange( $d1, $d2 )
    {
        $timestamp1 = strtotime( $d1 );
        $timestamp2 = strtotime( $d2 );
        if ( $timestamp1 == $timestamp2 )
            return;
        if ( $timestamp1 > $timestamp2 )
            return false;

        $year_s = date( 'Y', $timestamp1 );
        $year_e = date( 'Y', $timestamp2 );

        $month_s = date( 'm', $timestamp1 );
        $month_e = date( 'm', $timestamp2 );


        $arr = array();
        $year_diff = $year_e - $year_s;
        for ( $i = $year_s; $i <= $year_e; $i++ ) {
            if ( $i == $year_s ) {
                if ( $year_diff > 0 ) {
                    $month_end = 12;
                } else {
                    $month_end = $month_e;
                }
                for ( $k = (int) $month_s; $k <= $month_end; $k++ ) {
                    $value = $k < 10 ? '0' . $k : $k;
                    $arr[] = $i . '-' . $value;
                }
            }

            if ( $i > $year_s && $i < $year_e ) {
                for ( $y = $year_s + 1; $y <= $year_e - 1; $y++ ) {
                    for ( $k = 1; $k <= 12; $k++ ) {
                        $value = $k < 10 ? '0' . $k : $k;
                        $arr[] = $y . '-' . $value;
                    }
                }
            }

            if ( $i == $year_e && $year_e > $year_s ) {
                for ( $k = 1; $k <= $month_e; $k++ ) {
                    $value = $k < 10 ? '0' . $k : $k;
                    $arr[] = $i . '-' . $value;
                }
            }
        }
        return $arr;
    }

    /**
     * 取不同月份的天数
     * @param type $month
     * @return int 
     */
    public function getMonthDay( $month )
    {
        //构造月的option下拉菜单        
        $day31 = array( 1, 3, 5, 7, 8, 10, 12 );
        if ( in_array( $month, $day31 ) ) {
            $day = 31;
        } else {
            $day = 30;
        }
        if ( $month == 2 )
            $day = 28;     //今年的2月是几天啊？？？？            
        return $day;
    }

}
