<?php

/**
 * 订单支付 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Refund.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Refund_admin extends service_order_Refund_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 批量订单退款
     */
    public function batchOrderRefund( $order_id )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $where = $dao->getWhereInStatement( 'order_id', $order_id );
        $dao->setWhere( $where );
        $dao->setField( 'order_id,order_sn,demo_order,order_status,uid,order_amount' );
        $order_res = $dao->getListByWhere();
        if ( empty( $order_res ) ) {
            return true;
        }

        $error_message = '';
        $model = new service_order_RefundSave_mobile();
        foreach ( $order_res as $orderInfo ) {
            $orderInfo instanceof entity_OrderInfo_base;
            //检测权限                
            $model->setOrder_sn( $orderInfo->order_sn );
            $model->setOrder_goods_id( 0 );
            $model->setMoney( $orderInfo->order_amount );
            $model->setAdminPurview( true );

            if ( $orderInfo->demo_order == service_Order_base::demo_order_no ) {
                $error_message.='|' . $orderInfo->order_sn . ':正常用户的订单不能取消，只能取消APP订单的';
                continue;
            }
            try {
                $model->checkPurviewByBuyer();
            } catch (TmacClassException $exc) {
                $error_message.='|' . $orderInfo->order_sn . $exc->getMessage();
                continue;
            }

            $entity_OrderRefund_base = new entity_OrderRefund_base();
            $entity_OrderRefund_base->order_goods_id = 0;
            $entity_OrderRefund_base->refund_service_status = service_order_Service_base::refund_service_status_refund;
            $entity_OrderRefund_base->refund_service_reason = 4; //未收到货品
            $entity_OrderRefund_base->money = $orderInfo->order_amount;
            $entity_OrderRefund_base->uid = $orderInfo->uid;
            $entity_OrderRefund_base->refund_time = $this->now;
            $entity_OrderRefund_base->refund_note = ''; //维权申请描述
            /*
              if ( $refund_images ) {
              $entity_OrderRefund_base->refund_images = json_encode( array_unique( $refund_images ) );
              }
             * 
             */
            try {
                $model->createOrderRefund( $entity_OrderRefund_base );
            } catch (TmacClassException $exc) {
                $error_message.='|' . $orderInfo->order_sn . $exc->getMessage();
            }
            
        }                
        $this->errorMessage = $error_message;
        return true;
    }

}
