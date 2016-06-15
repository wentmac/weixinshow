<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Coupon.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Coupon_manage extends service_Coupon_base
{

    private $pagesize;
    private $coupon_num;
    private $coupon_value;
    private $coupon_money_count;
    private $coupon_status;
    private $coupon_code;

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    function setCoupon_status( $coupon_status )
    {
        $this->coupon_status = $coupon_status;
    }

    function setCoupon_code( $coupon_code )
    {
        $this->coupon_code = $coupon_code;
    }

    function setCoupon_num( $coupon_num )
    {
        $this->coupon_num = $coupon_num;
    }

    function setCoupon_value( $coupon_value )
    {
        $this->coupon_value = $coupon_value;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取已经生成的额度
     * $this->setMemberInfo( $memberInfo );
     * $this->getCouponCreatedSum();
     */
    public function getCouponCreatedSum()
    {
        $dao = dao_factory_base::getCouponDao();
        $dao->setField( 'SUM(coupon_money) AS coupon_money_count' );
        $where = "uid={$this->memberInfo->uid}";
        $dao->setWhere( $where );
        $res = $dao->getInfoByWhere();
        $coupon_money_count = $this->coupon_money_count = empty( $res->coupon_money_count ) ? 0 : $res->coupon_money_count;
        return $coupon_money_count;
    }

    /**
     * $this->setCoupon_num ( $coupon_num );
     * $this->setCoupon_value ( $coupon_value );
     * $this->createCoupon();
     */
    public function createCoupon()
    {
        //wsw_coupon insert rows
        $entity_Coupon_base = new entity_Coupon_base();
        $entity_Coupon_base->uid = $this->memberInfo->uid;
        $entity_Coupon_base->coupon_money = $this->coupon_value;
        $entity_Coupon_base->coupon_status = service_Coupon_base::coupon_status_unused;
        $entity_Coupon_base->create_time = $this->now;

        $dao = dao_factory_base::getCouponDao();
        //$dao->getDb()->startTrans();

        for ( $i = 0; $i <= $this->coupon_num; $i++ ) {
            $entity_Coupon_base->coupon_code = $this->getCouponCode();
            $res = $dao->insert( $entity_Coupon_base );
        }
        return $res;
        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {

          if ( $dao->getDb()->isSuccess() ) {
          $dao->getDb()->commit();
          return true;
          } else {
          $dao->getDb()->rollback();
          return false;
          }
         * 
         * @return type         
         */
    }

    private function getCouponCode()
    {
        $uuid = service_utils_Function_base::guid();
        $coupon_code = substr( md5( $uuid ), 8, 16 );
        $couponCode = substr( $coupon_code, 0, 4 ) . '-' . substr( $coupon_code, 4, 4 ) . '-' . substr( $coupon_code, 8, 4 ) . '-' . substr( $coupon_code, 12, 4 );
        return strtoupper( $couponCode );
    }

    /**
     * 获取所有资讯
     * return article_class,pages
     */
    public function getCouponList()
    {
        $dao = dao_factory_base::getCouponDao();
        $where = 'uid=' . $this->memberInfo->uid;

        if ( !empty( $this->coupon_status ) ) {
            if ( $this->coupon_status == -1 ) {
                $this->coupon_status = 0;
            }
            $where.=' AND coupon_status=' . $this->coupon_status;
        }

        if ( !empty( $this->coupon_code ) ) {
            $where.=" AND coupon_code='{$this->coupon_code}'";
        }

        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $dao->setOrderby( 'coupon_id DESC' );
        $dao->setLimit( $limit );
        $result_array = array();
        if ( $count > 0 ) {
            $result_array = $dao->getListByWhere();
            $coupon_status_array = Tmac::config( 'coupon.coupon.coupon_status', APP_BASE_NAME );
            foreach ( $result_array as $value ) {
                $value->create_time = date( 'Y-m-d H:i:s', $value->create_time );
                $value->use_time = empty( $value->use_time ) ? '' : date( 'Y-m-d H:i:s', $value->use_time );
                $value->coupon_status = $coupon_status_array[ $value->coupon_status ];
            }
        }


        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / $this->pagesize ) ),
            'pagesize' => $this->pagesize,
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'coupon_list',
            'retmsg' => $retmsg,
            'reqdata' => $result_array,
        );
        return $return;
    }

}
