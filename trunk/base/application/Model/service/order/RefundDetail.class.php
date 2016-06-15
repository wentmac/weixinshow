<?php

/**
 * api 会员账户 管理模块 Model
 * 单个订单商品退款
 * 整个订单在未发货前退款
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: RefundDetail.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_RefundDetail_base extends service_Order_base
{

    protected $order_refund_id;
    protected $uid;
    protected $order_refund_info;
    protected $identity;

    function setOrder_refund_id( $order_refund_id )
    {
        $this->order_refund_id = $order_refund_id;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setIdentity( $identity )
    {
        $this->identity = $identity;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取退款详情
     */
    public function getOrderRefundInfo()
    {
        $dao = dao_factory_base::getOrderRefundDao();
        $dao->setPk( $this->order_refund_id );
        $result = $dao->getInfoByPk();
        if ( !$result ) {
            throw new TmacClassException( '退款详细不存在' );
        }
        $this->order_refund_info = $result;
        $result->supplier_mobile = '02750243596';
        return $result;
    }

    /**
     * 检测卖家对退款详情的权限
     */
    public function checkPurviewBySeller()
    {
        self::getOrderRefundInfo();
        if ( $this->order_refund_info->goods_uid <> $this->uid ) {
            throw new TmacClassException( '没有权限：－）' );
        }
        return $this->order_refund_info;
    }

    /**
     * 检测买家对退款详情的权限
     */
    public function checkPurviewByBuyer()
    {
        self::getOrderRefundInfo();
        if ( $this->order_refund_info->uid <> $this->uid ) {
            throw new TmacClassException( '没有权限：－）' );
        }
        return $this->order_refund_info;
    }

    /**
     * 取售后纪录
     */
    public function getOrderServiceArray()
    {
        $dao = dao_factory_base::getOrderServiceDao();
        $dao->setWhere( "order_refund_id={$this->order_refund_id}" );
        $dao->setField( 'order_service_id,service_status,refund_status,return_status,service_note,service_uid,service_username,service_time' );
        $dao->setOrderby( 'order_service_id DESC' );
        $res = $dao->getListByWhere();

        if ( $res ) {
            $order_refund_list_model = new service_order_RefundList_base();
            foreach ( $res as $value ) {
                $value->service_time = date( 'Y-m-d H:i:s', $value->service_time );
                $value->icon = 'info';
                if ( $value->service_status == service_order_Service_base::service_status_success ) {
                    $value->icon = 'yes';
                } else if ( $value->service_status == service_order_Service_base::service_status_waiting_buyer_confirm && $value->refund_status == service_order_Service_base::refund_status_seller_disagree ) {
                    $value->icon = 'no';
                } else if ( $value->service_status == service_order_Service_base::service_status_waiting_buyer_confirm && $value->return_status == service_order_Service_base::return_status_seller_donot_receive ) {
                    $value->icon = 'no';
                }

                $service_title_array = $order_refund_list_model->getServiceStatusText( $value->service_status, $value->refund_status, $value->return_status );

                $value->service_title = $service_title_array[ $this->identity ];
                unset( $value->service_status );
                unset( $value->refund_status );
                unset( $value->return_status );
            }
        }
        return $res;
    }

}
