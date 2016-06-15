<?php

/**
 * api 会员账户 管理模块 Model
 * 单个订单商品退款
 * 整个订单在未发货前退款
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: SellerRefundHandle.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_SellerRefundHandle_manage extends service_order_RefundHandle_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 卖家同意退款
     */
    public function refundAgree()
    {
        //进行退款退货 还是 退款判断
        if ( $this->order_refund_info->refund_service_status == service_order_Service_base::refund_service_status_return ) {//退款退货
            $service_status = service_order_Service_base::service_status_waiting_buyer_confirm;
            $refund_status = service_order_Service_base::refund_status_seller_agree;
            $return_status = service_order_Service_base::return_status_waiting_buyer_delivery;
        } else {//退款
            $service_status = service_order_Service_base::service_status_success;
            $refund_status = service_order_Service_base::refund_status_seller_agree;
            $return_status = service_order_Service_base::return_status_default;
        }

        //检测退款流程是否合法
        $order_model = new service_order_Refund_manage();
        $order_model->setOrder_refund_id( $this->order_refund_id );
        $order_model->setUid( $this->uid );
        $order_model->setService_status( $service_status );
        $order_model->setRefund_status( $refund_status );
        $order_model->setReturn_status( $return_status );
        $orderInfo = $order_model->checkOrderRefundPurview( $this->order_refund_info );
        if ( $orderInfo == false ) {
            $this->errorMessage = $order_model->getErrorMessage();
            return false;
        }
        //执行退款操作
        if ( $this->order_refund_info->refund_service_status == service_order_Service_base::refund_service_status_refund ) {
            $model = service_Payment_base::factory( $orderInfo->trade_vendor );
            $model->setOrder_refund_id( $this->order_refund_id );
            $model->setMoney( $this->order_refund_info->money );
            $model->setOrderInfo( $orderInfo );
            $res = $model->refund();
            if ( $res == false ) {
                $this->errorMessage = $model->getErrorMessage();
                return FALSE;
            }
            //消息 push
            $push_message_model = new service_PushMessage_base();
            $push_message_model->setMessageType( service_PushMessage_base::message_type_refund_yes );
            $push_message_model->setOrderRefund( $this->order_refund_info );
            $push_message_model->push();

            self::setRefundStatusText( $service_status, $refund_status, $return_status );
            return true;
        } else {//更新状态
            //订单售后记录表 买家申请售后，卖家处理售后，处理结果 order_service
            $order_service_model = new service_order_Service_base();

            //未发货前对整个订单进行退款
            $order_service_model->setIdentity( 'seller' );

            $order_service_model->setService_status( $service_status );
            $order_service_model->setRefund_status( $refund_status );
            $order_service_model->setReturn_status( $return_status );

            $order_service_model->setService_uid( $this->order_refund_info->goods_uid );
            $modify_order_service_res = $order_service_model->modifyOrderRefundService( $this->order_refund_info );

            if ( !$modify_order_service_res ) {
                $this->errorMessage = $order_service_model->getErrorMessage();
                return false;
            }
            //消息 push
            $push_message_model = new service_PushMessage_base();
            $push_message_model->setMessageType( service_PushMessage_base::message_type_refund_yes );
            $push_message_model->setOrderRefund( $this->order_refund_info );
            $push_message_model->push();
            self::setRefundStatusText( $service_status, $refund_status, $return_status );
            return true;
        }
    }

    /**
     * 卖家不同意退款
     */
    public function refundDisagree( $reason )
    {
        $service_status = service_order_Service_base::service_status_waiting_buyer_confirm;
        $refund_status = service_order_Service_base::refund_status_seller_disagree;
        $return_status = service_order_Service_base::return_status_default;

        //检测退款流程是否合法
        $order_model = new service_order_Refund_manage();
        $order_model->setOrder_refund_id( $this->order_refund_id );
        $order_model->setUid( $this->uid );
        $order_model->setService_status( $service_status );
        $order_model->setRefund_status( $refund_status );
        $order_model->setReturn_status( $return_status );
        $orderInfo = $order_model->checkOrderRefundPurview( $this->order_refund_info );
        if ( $orderInfo == false ) {
            $this->errorMessage = $order_model->getErrorMessage();
            return false;
        }

        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_info_dao->getDb()->startTrans();

        //更新order_info表中的退款状态
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->refund_status = $refund_status;
        $order_info_dao->setPk( $this->order_refund_info->order_id );
        $order_info_dao->updateByPk( $entity_OrderInfo_base );

        $order_service_model = new service_order_Service_base();

        //未发货前对整个订单进行退款
        $order_service_model->setIdentity( 'seller' );

        $order_service_model->setService_status( $service_status );
        $order_service_model->setRefund_status( $refund_status );
        $order_service_model->setReturn_status( $return_status );

        $order_service_model->setService_uid( $this->uid );
        $order_service_model->setReason( ' 拒绝理由：' . $reason );
        $modify_order_service_res = $order_service_model->modifyOrderRefundService( $this->order_refund_info );
        
        //消息 push
        $push_message_model = new service_PushMessage_base();
        $push_message_model->setMessageType( service_PushMessage_base::message_type_refund_no );        
        $push_message_model->setOrderRefund( $this->order_refund_info );        
        $push_message_model->push();

        if ( $order_info_dao->getDb()->isSuccess() && $modify_order_service_res === true ) {
            $order_info_dao->getDb()->commit();
            //TODO 发短信
            self::setRefundStatusText( $service_status, $refund_status, $return_status );
            return true;
        } else {
            $order_info_dao->getDb()->rollback();
            $this->errorMessage = $order_service_model->getErrorMessage();
            return false;
        }
    }

    /**
     * 卖家收到货
     * 需要进行打款
     */
    public function receiptYes()
    {
        $service_status = service_order_Service_base::service_status_success;
        $refund_status = service_order_Service_base::refund_status_seller_agree;
        $return_status = service_order_Service_base::return_status_seller_receive;

        //检测退款流程是否合法
        $order_model = new service_order_Refund_manage();
        $order_model->setOrder_refund_id( $this->order_refund_id );
        $order_model->setUid( $this->uid );
        $order_model->setService_status( $service_status );
        $order_model->setRefund_status( $refund_status );
        $order_model->setReturn_status( $return_status );
        $orderInfo = $order_model->checkOrderRefundPurview( $this->order_refund_info );
        if ( $orderInfo == false ) {
            $this->errorMessage = $order_model->getErrorMessage();
            return false;
        }
        //执行退款操作
        $model = service_Payment_base::factory( $orderInfo->trade_vendor );
        $model->setOrder_refund_id( $this->order_refund_id );
        $model->setMoney( $this->order_refund_info->money );
        $model->setOrderInfo( $orderInfo );
        $res = $model->refund();
        if ( $res == false ) {
            $this->errorMessage = '执行退款失败，请联系客服MM';
            return FALSE;
        }
        self::setRefundStatusText( $service_status, $refund_status, $return_status );
        return true;
    }

    /**
     * 卖家没有收到货
     */
    public function receiptNo()
    {
        $service_status = service_order_Service_base::service_status_waiting_buyer_confirm;
        $refund_status = service_order_Service_base::refund_status_seller_agree;
        $return_status = service_order_Service_base::return_status_seller_donot_receive;

        //检测退款流程是否合法
        $order_model = new service_order_Refund_manage();
        $order_model->setOrder_refund_id( $this->order_refund_id );
        $order_model->setUid( $this->uid );
        $order_model->setService_status( $service_status );
        $order_model->setRefund_status( $refund_status );
        $order_model->setReturn_status( $return_status );
        $orderInfo = $order_model->checkOrderRefundPurview( $this->order_refund_info );
        if ( $orderInfo == false ) {
            $this->errorMessage = $order_model->getErrorMessage();
            return false;
        }

        $order_service_model = new service_order_Service_base();

        //未发货前对整个订单进行退款
        $order_service_model->setIdentity( 'seller' );

        $order_service_model->setService_status( $service_status );
        $order_service_model->setRefund_status( $refund_status );
        $order_service_model->setReturn_status( $return_status );

        $order_service_model->setService_uid( $this->order_refund_info->goods_uid );
        $modify_order_service_res = $order_service_model->modifyOrderRefundService( $this->order_refund_info );

        if ( !$modify_order_service_res ) {
            $this->errorMessage = $order_service_model->getErrorMessage();
            return false;
        }
        self::setRefundStatusText( $service_status, $refund_status, $return_status );
        return true;
    }

    /**
     * 设置 售后维权 当前的 状态描述
     */
    private function setRefundStatusText( $service_status, $refund_status, $return_status )
    {
        $service_status_map = service_order_RefundList_base::getServiceStatusText( $service_status, $refund_status, $return_status );
        $this->refund_status_text = $service_status_map[ 'seller' ];
    }

}
