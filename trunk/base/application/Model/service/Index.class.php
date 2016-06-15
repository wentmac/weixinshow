<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Index_base extends service_Model_base
{

    protected $uid;
    protected $member_type;
    protected $errorMessage;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setMember_type( $member_type )
    {
        $this->member_type = $member_type;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 商品总数
     * @return type
     */
    public function getItemCount()
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setWhere( "uid={$this->uid} AND is_delete=0" );
        return $dao->getCountByWhere();
    }

    /**
     * 客户总数
     * @return type
     */
    public function getCustomerCount()
    {
        $dao = dao_factory_base::getCustomerDao();
        $dao->setWhere( "item_uid={$this->uid}" );
        return $dao->getCountByWhere();
    }

    /**
     * 未处理订单总数
     * @return type
     */
    public function getUnHandleOrderCount()
    {
        if ( empty( $this->member_type ) || $this->member_type == service_Member_base::member_type_seller || $this->member_type == service_Member_base::member_type_mall ) {
            $where = "item_uid={$this->uid}";
        } else if ( $this->member_type == service_Member_base::member_type_supplier ) {
            $where = "goods_uid={$this->uid}";
        }
        $order_status = service_Order_base::order_status_buyer_payment;
        $where .= " AND order_status={$order_status} AND is_delete=0";
        //未发货订单
        $dao = dao_factory_base::getOrderInfoDao();

        $dao->setWhere( $where );
        $order_count = $dao->getCountByWhere();
        //未处理退款订单
        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $where = "goods_uid={$this->uid} AND service_status=" . service_order_Service_base::service_status_waiting_seller_confirm;
        $order_refund_dao->setWhere( $where );
        $order_refund_count = $order_refund_dao->getCountByWhere();
        return $order_count + $order_refund_count;
    }

}
