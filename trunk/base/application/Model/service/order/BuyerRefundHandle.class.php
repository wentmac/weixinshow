<?php

/**
 * api 会员账户 管理模块 Model
 * 买家退款操作类
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: BuyerRefundHandle.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_BuyerRefundHandle_base extends service_order_RefundHandle_base
{

    protected $order_refund_id;
    protected $express_id;
    protected $express_code;
    protected $express_name;
    protected $express_no;
    protected $is_modify = false;
    protected $service_status;
    protected $refund_status;
    protected $return_status;

    function setOrder_refund_id( $order_refund_id )
    {
        $this->order_refund_id = $order_refund_id;
    }

    function setExpress_id( $express_id )
    {
        $this->express_id = $express_id;
    }

    function setExpress_name( $express_name )
    {
        $this->express_name = $express_name;
    }

    function setExpress_no( $express_no )
    {
        $this->express_no = $express_no;
    }

    function setIs_modify( $is_modify )
    {
        $this->is_modify = $is_modify;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测买家
     * $this->order_refund_id;
     * $this->uid;     
     * $this->checkBuyerOrderReturnPurview();
     * 
     */
    public function checkBuyerOrderReturnPurview()
    {
        $order_refund_info = $this->order_refund_info;
        $order_refund_info instanceof entity_OrderRefund_base;
        $this->entity_OrderRefund = $order_refund_info;

        //判断是否在退款中
        if ( $this->entity_OrderRefund->refund_ing == 1 ) {
            $this->errorMessage = '系统正在退款中，请不要重复执行';
            return false;
        }

        if ( $order_refund_info->uid <> $this->uid ) {
            $this->errorMessage = '只能发自己的维权订单哟';
            return false;
        }


        //订单售后记录表 买家申请售后，卖家处理售后，处理结果 order_service
        $order_service_model = new service_order_Service_base();
        $order_service_model->setIdentity( 'buyer' );
        $order_service_model->setService_status( $this->service_status );
        $order_service_model->setRefund_status( $this->refund_status );
        $order_service_model->setReturn_status( $this->return_status );
        $order_service_purview = $order_service_model->checkOrderRefundServicePurview( $order_refund_info );
        if ( $order_service_purview == FALSE ) {
            $this->errorMessage = $order_service_model->getErrorMessage();
            return false;
        }
        return true;
    }

    /**
     * 卖家同意买家退货
     * 买家进行发退货处理
     */
    public function executeReturn()
    {
        $this->service_status = service_order_Service_base::service_status_waiting_seller_confirm;
        $this->refund_status = service_order_Service_base::refund_status_seller_agree;
        $this->return_status = service_order_Service_base::return_status_buyer_delivery;

        $check_result = $this->checkBuyerOrderReturnPurview();
        if ( $check_result == false ) {
            return false;
        }
        if ( $this->order_refund_info->refund_service_status <> service_order_Service_base::refund_service_status_return ) {
            $this->errorMessage = '亲，此维权申请不需要发货哟';
            return false;
        }
        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_service_dao = dao_factory_base::getOrderServiceDao();

        $order_refund_dao->getDb()->startTrans();
        //执行退款发货快递信息保存
        //取快递名称
        $this->getExpressNameById( $this->express_id );
        if ( $this->express_id == service_order_SellerHandle_base::express_id_no_required ) {
            $action_note = '无需发货';
        } else {
            $action_note = $this->express_name . ',快递单号：' . $this->express_no;
        }

        if ( $this->is_modify ) {
            $action_note = '(修改退货快递信息)' . $action_note;
        }

        //更新 order_refund 表
        $entity_OrderRefund_base = new entity_OrderRefund_base();
        $entity_OrderRefund_base->service_status = $this->service_status;
        $entity_OrderRefund_base->refund_status = $this->refund_status;
        $entity_OrderRefund_base->return_status = $this->return_status;
        $entity_OrderRefund_base->service_note = '买家，发出退的货 ' . $action_note;
        $order_refund_dao->setPk( $this->order_refund_info->order_refund_id );
        $order_refund_dao->updateByPk( $entity_OrderRefund_base );

        //更新 order_service 表
        $entity_OrderService_base = new entity_OrderService_base();
        $entity_OrderService_base->order_refund_id = $this->order_refund_id;
        $entity_OrderService_base->order_goods_id = $this->order_refund_info->order_goods_id;
        $entity_OrderService_base->order_id = $this->order_refund_info->order_id;
        $entity_OrderService_base->money = $this->order_refund_info->money;
        $entity_OrderService_base->service_status = $this->service_status;
        $entity_OrderService_base->refund_status = $this->refund_status;
        $entity_OrderService_base->return_status = $this->return_status;
        $entity_OrderService_base->service_note = $action_note;
        $entity_OrderService_base->service_uid = $this->uid;
        $entity_OrderService_base->service_username = '买家';
        $entity_OrderService_base->express_id = $this->express_id;
        $entity_OrderService_base->express_code = $this->express_code;
        $entity_OrderService_base->express_name = $this->express_name;
        $entity_OrderService_base->express_no = $this->express_no;
        $entity_OrderService_base->service_time = $this->now;
        $order_service_dao->insert( $entity_OrderService_base );

        if ( $order_refund_dao->getDb()->isSuccess() ) {
            $order_refund_dao->getDb()->commit();
            return true;
        } else {
            $order_refund_dao->getDb()->rollback();
            return false;
        }
    }

    private function getExpressNameById( $express_id )
    {
        if ( $express_id == -1 ) {
            $this->express_name = '无需物流';
            return $this->express_name;
        }
        if ( empty( $express_id ) ) {
            return $this->express_name;
        }
        $dao = dao_factory_base::getExpressDao();
        $dao->setField( 'express_name,express_code' );
        $dao->setPk( $express_id );
        $express_info = $dao->getInfoByPk();
        if ( !$express_info ) {
            $this->express_id = 0;
            $this->express_name = '其他';
        } else {
            $this->express_name = $express_info->express_name;
            $this->express_code = $express_info->express_code;
        }
        return $this->express_name;
    }

    /**
     * 买家取消退款申请
     * 买家进行发退货处理
     */
    public function executeCancel()
    {
        if ( $this->order_refund_info->service_status == service_order_Service_base::service_status_waiting_buyer_confirm 
                && $this->order_refund_info->refund_status == service_order_Service_base::refund_status_seller_disagree ) {
            //卖家拒绝退款退货申请，买家取消退款退货申请
            $this->refund_status = service_order_Service_base::refund_status_seller_disagree;
        } else {
            //买家主动取消退款维权申请
            $this->refund_status = service_order_Service_base::refund_status_default;
        }        
        $this->service_status = service_order_Service_base::service_status_close;        
        $this->return_status = service_order_Service_base::return_status_default;
                
        $check_result = $this->checkBuyerOrderReturnPurview();
        if ( $check_result == false ) {
            return false;
        }
        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_service_dao = dao_factory_base::getOrderServiceDao();
        $member_bill_dao = dao_factory_base::getMemberBillDao();
        $order_info_dao = dao_factory_base::getOrderInfoDao();

        $order_refund_dao->getDb()->startTrans();
        //执行退款        
        //更新 order_refund 表
        $entity_OrderRefund_base = new entity_OrderRefund_base();
        $entity_OrderRefund_base->service_status = $this->service_status;
        $entity_OrderRefund_base->refund_status = $this->refund_status;
        $entity_OrderRefund_base->return_status = $this->return_status;
        $entity_OrderRefund_base->service_note = '买家，取消退款申请，售后关闭';
        $order_refund_dao->setPk( $this->order_refund_info->order_refund_id );
        $order_refund_dao->updateByPk( $entity_OrderRefund_base );

        //更新 order_service 表
        $entity_OrderService_base = new entity_OrderService_base();
        $entity_OrderService_base->order_refund_id = $this->order_refund_id;
        $entity_OrderService_base->order_goods_id = $this->order_refund_info->order_goods_id;
        $entity_OrderService_base->order_id = $this->order_refund_info->order_id;
        $entity_OrderService_base->money = $this->order_refund_info->money;
        $entity_OrderService_base->service_status = $this->service_status;
        $entity_OrderService_base->refund_status = $this->refund_status;
        $entity_OrderService_base->return_status = $this->return_status;
        $entity_OrderService_base->service_note = '取消退款申请，售后关闭';
        $entity_OrderService_base->service_uid = $this->uid;
        $entity_OrderService_base->service_username = '买家';
        $entity_OrderService_base->service_time = $this->now;
        $order_service_dao->insert( $entity_OrderService_base );


        //member_bill表 更新账单表中的 确认收货状态
        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
        $entity_MemberBill_base->order_finish = service_Member_base::order_finish_no;
        $entity_MemberBill_base->confirm_time = $this->now;
        $where = "order_id={$this->order_refund_info->order_id}";
        $member_bill_dao->setWhere( $where );
        $member_bill_dao->updateByWhere( $entity_MemberBill_base );

        //判断如果是会员商品，更新会员商品的退款状态
        //如果是会员商品，会员商品退款状态改成正在退。当用户取消退款时再改回来
        if ( $this->order_refund_info->order_type == service_Order_base::order_type_member ) {
            $entity_OrderInfo_base = new entity_OrderInfo_base();
            $entity_OrderInfo_base->goods_member_level_refund = service_Order_base::goods_member_level_refund_no;
            $order_info_dao->setPk( $this->order_refund_info->order_id );
            $order_info_dao->updateByPk( $entity_OrderInfo_base );
        }

        if ( $order_refund_dao->getDb()->isSuccess() ) {
            $order_refund_dao->getDb()->commit();
            self::setRefundStatusText( $this->service_status, $this->refund_status, $this->return_status );
            return true;
        } else {
            $order_refund_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 退款的介入
     */
    public function refundOrderIntervene()
    {
        $this->service_status = service_order_Service_base::service_status_waiting_customer_confirm;
        $this->refund_status = service_order_Service_base::refund_status_seller_disagree;
        $this->return_status = service_order_Service_base::return_status_default;
    }

    /**
     * 退货时的介入
     * 卖家没有收到货
     */
    public function returnOrderIntervene()
    {
        $this->service_status = service_order_Service_base::service_status_waiting_customer_confirm;
        $this->refund_status = service_order_Service_base::refund_status_seller_agree;
        $this->return_status = service_order_Service_base::return_status_seller_donot_receive;
    }

    /**
     * 卖家同意买家退货
     * 买家进行发退货处理
     */
    public function createOrderIntervene()
    {
        $check_result = $this->checkBuyerOrderReturnPurview();
        if ( $check_result == false ) {
            return false;
        }

        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_service_dao = dao_factory_base::getOrderServiceDao();

        $order_refund_dao->getDb()->startTrans();
        //更新 order_refund 表
        $entity_OrderRefund_base = new entity_OrderRefund_base();
        $entity_OrderRefund_base->service_status = $this->service_status;
        $entity_OrderRefund_base->refund_status = $this->refund_status;
        $entity_OrderRefund_base->return_status = $this->return_status;
        $entity_OrderRefund_base->service_note = '买家，申请银品惠客服介入';
        $order_refund_dao->setPk( $this->order_refund_info->order_refund_id );
        $order_refund_dao->updateByPk( $entity_OrderRefund_base );

        //更新 order_service 表
        $entity_OrderService_base = new entity_OrderService_base();
        $entity_OrderService_base->order_refund_id = $this->order_refund_id;
        $entity_OrderService_base->order_goods_id = $this->order_refund_info->order_goods_id;
        $entity_OrderService_base->order_id = $this->order_refund_info->order_id;
        $entity_OrderService_base->money = $this->order_refund_info->money;
        $entity_OrderService_base->service_status = $this->service_status;
        $entity_OrderService_base->refund_status = $this->refund_status;
        $entity_OrderService_base->return_status = $this->return_status;
        $entity_OrderService_base->service_note = '申请银品惠客服介入';
        $entity_OrderService_base->service_uid = $this->uid;
        $entity_OrderService_base->service_username = '买家';
        $entity_OrderService_base->service_time = $this->now;
        $order_service_dao->insert( $entity_OrderService_base );

        if ( $order_refund_dao->getDb()->isSuccess() ) {
            $order_refund_dao->getDb()->commit();
            self::setRefundStatusText( $this->service_status, $this->refund_status, $this->return_status );
            return true;
        } else {
            $order_refund_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 设置 售后维权 当前的 状态描述
     */
    private function setRefundStatusText( $service_status, $refund_status, $return_status )
    {
        $service_status_map = service_order_RefundList_base::getServiceStatusText( $service_status, $refund_status, $return_status );
        $this->refund_status_text = $service_status_map[ 'buyer' ];
    }

}
