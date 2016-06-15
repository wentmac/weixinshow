<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Service.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Service_base extends service_Order_base
{

    /**
     * 申请售后处理方式
     * 退货退款
     */
    const refund_service_status_return = 1;

    /**
     * 申请售后处理方式
     * 仅退款
     */
    const refund_service_status_refund = 2;

    /**
     * 等待卖家处理
     */
    const service_status_waiting_seller_confirm = 1;

    /**
     * 待买家处理
     */
    const service_status_waiting_buyer_confirm = 2;

    /**
     * 银品惠客服介入
     */
    const service_status_waiting_customer_confirm = 3;

    /**
     * 同意退款
     */
    const service_status_success = 4;

    /**
     * 撤销维权
     */
    const service_status_close = 5;

    /**
     * 收到货后单个商品申请的退款状态
     * 默认退款状态
     */
    const refund_status_default = 0;

    /**
     * 收到货后单个商品申请的退款状态
     * 买家申请退款
     */
    const refund_status_buyer_return = 1;

    /**
     * 收到货后单个商品申请的退款状态
     * 卖家同意退款
     */
    const refund_status_seller_agree = 2;

    /**
     * 收到货后单个商品申请的退款状态
     * 卖家不同意退款
     */
    const refund_status_seller_disagree = 3;

    /**
     * 退货状态
     * 默认退货状态
     */
    const return_status_default = 0;

    /**
     * 退货状态
     * 卖家同意退款，等待买家发货
     */
    const return_status_waiting_buyer_delivery = 1;

    /**
     * 退货状态
     * 买家已经快递发出退的货
     */
    const return_status_buyer_delivery = 2;

    /**
     * 退货状态
     * 卖家已经收到买家退的货
     */
    const return_status_seller_receive = 3;

    /**
     * 退货状态
     * 卖家未收到退货，卖家拒绝确认收货 
     */
    const return_status_seller_donot_receive = 4;

    protected $identity;
    protected $order_goods_id;
    protected $service_status;
    protected $refund_status;
    protected $return_status;
    protected $service_uid;
    protected $money;
    protected $member_bill_id;
    protected $service_status_map;
    protected $service_level_status = false;
    protected $reason;

    function setIdentity( $identity )
    {
        $this->identity = $identity;
    }

    function setOrder_goods_id( $order_goods_id )
    {
        $this->order_goods_id = $order_goods_id;
    }

    function setService_status( $service_status )
    {
        $this->service_status = $service_status;
    }

    function setRefund_status( $refund_status )
    {
        $this->refund_status = $refund_status;
    }

    function setReturn_status( $return_status )
    {
        $this->return_status = $return_status;
    }

    function setService_uid( $service_uid )
    {
        $this->service_uid = $service_uid;
    }

    function setMoney( $money )
    {
        $this->money = $money;
    }

    function setMember_bill_id( $member_bill_id )
    {
        $this->member_bill_id = $member_bill_id;
    }

    /**
     * 拒绝理由，可选
     * @param type $reason
     */
    function setReason( $reason )
    {
        $this->reason = $reason;
    }

    /**
     * 设置业务重新处理级别
     * 买家重新发起退款申请｜买家重新发起退款退货申请｜买起重新修改退货的物流信息
     * boolen
     * @param type $service_level_status
     */
    function setService_level_status( $service_level_status )
    {
        $this->service_level_status = $service_level_status;
    }

    public function __construct()
    {
        parent::__construct();
        $this->service_status_map = array(
            //退款退货
            self::refund_service_status_return => array(
                //买家申请退款
                self::service_status_waiting_seller_confirm . '_' . self::refund_status_buyer_return . '_' . self::return_status_default => array(
                    'seller' => array(
                        //卖家同意,等待买家发货
                        self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_waiting_buyer_delivery,
                        //卖家不同意
                        self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default
                    ),
                    'description' => '{service_level}发起了退款退货申请,等待卖家处理'
                ),
                //卖家同意 
                self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_waiting_buyer_delivery => array(
                    'buyer' => array(
                        //发货
                        self::service_status_waiting_seller_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_buyer_delivery,
                        //买家取消退款维权申请
                        self::service_status_close . '_' . self::refund_status_default . '_' . self::return_status_default
                    ),
                    'description' => '已同意退货退款申请,等待买家退货'
                ),
                //买家发货
                self::service_status_waiting_seller_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_buyer_delivery => array(
                    'seller' => array(
                        //卖家确认收到货
                        self::service_status_success . '_' . self::refund_status_seller_agree . '_' . self::return_status_seller_receive,
                        //卖家没有收到货
                        self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_seller_donot_receive
                    ),
                    'description' => '已退货,等待商家确认收货'
                ),
                //卖家确认收到货
                self::service_status_success . '_' . self::refund_status_seller_agree . '_' . self::return_status_seller_receive => array(
                    'description' => '同意退款给买家，本次维权结束'
                ),
                //卖家没有收到货
                self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_seller_donot_receive => array(
                    'buyer' => array(
                        //买家再次发货
                        self::service_status_waiting_seller_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_buyer_delivery,
                        //联系银品惠客服处理
                        self::service_status_waiting_customer_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_seller_donot_receive,
                        //买家取消退款维权申请
                        self::service_status_close . '_' . self::refund_status_default . '_' . self::return_status_default
                    ),
                    'description' => '卖家没有收到货，等待买家处理/发货'
                ),
                //卖家不同意
                self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default => array(
                    'buyer' => array(
                        //买家取消退款申请
                        self::service_status_close . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default,
                        //买家再次发起退款申请
                        self::service_status_waiting_seller_confirm . '_' . self::refund_status_buyer_return . '_' . self::return_status_default,
                        //联系银品惠客服处理
                        self::service_status_waiting_customer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default                        
                    ),
                    'description' => '卖家拒绝退款退货申请，等待买家处理'
                ),
                //联系银品惠客服
                self::service_status_waiting_customer_confirm . '_' . self::refund_status_seller_agree . '_' . self::return_status_seller_donot_receive => array(
                    'description' => '卖家没有收到货，买家申请银品惠客服介入'
                ),
                //联系银品惠客服
                self::service_status_waiting_customer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default => array(
                    'description' => '卖家拒绝退款退货申请，买家申请银品惠客服介入'
                ),
                //卖家不同意，买家取消退款申请
                self::service_status_close . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default => array(
                    'description' => '卖家拒绝退款退货申请，买家取消退款退货申请'
                ),
                //买家主动取消退款维权申请
                self::service_status_close . '_' . self::refund_status_default . '_' . self::return_status_default => array(
                    'description' => '买家主动取消退款维权申请'
                )
            ),
            //仅退款
            self::refund_service_status_refund => array(
                //买家申请
                self::service_status_waiting_seller_confirm . '_' . self::refund_status_buyer_return . '_' . self::return_status_default => array(
                    'seller' => array(
                        //卖家同意
                        self::service_status_success . '_' . self::refund_status_seller_agree . '_' . self::return_status_default,
                        //卖家不同意
                        self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default
                    ),
                    'description' => '{service_level}发起了退款申请,等待卖家处理'
                ),
                //卖家同意
                self::service_status_success . '_' . self::refund_status_seller_agree . '_' . self::return_status_default => array(
                    'description' => '同意退款给买家，本次维权结束'
                ),
                //卖家拒绝
                self::service_status_waiting_buyer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default => array(
                    'buyer' => array(
                        //买家再次申请
                        self::service_status_waiting_seller_confirm . '_' . self::refund_status_buyer_return . '_' . self::return_status_default,
                        //买家取消退款申请
                        self::service_status_close . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default,
                        //买家申请客服
                        self::service_status_waiting_customer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default                        
                    ),
                    'description' => '卖家拒绝退款，等待买家处理'
                ),
                //客服处理
                self::service_status_waiting_customer_confirm . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default => array(
                    'description' => '卖家拒绝退款，等待买家处理'
                ),
                //卖家不同意，买家取消退款申请
                self::service_status_close . '_' . self::refund_status_seller_disagree . '_' . self::return_status_default => array(
                    'description' => '卖家拒绝退款申请，买家取消退款申请'
                )                
            )
        );
    }

    /**
     * 检测售后订单下一步的操作权限
     * 用于订单单个商品退款的检测
     * =============================
     * $this->identity;     
     * $this->service_status;
     * $this->refund_status;
     * $this->return_status;    
     * $this->checkOrderRefundServicePurview($entity_OrderRefund);
     */
    public function checkOrderRefundServicePurview( $entity_OrderRefund )
    {
        $entity_OrderRefund instanceof entity_OrderRefund_base;
        $order_refund_info = $entity_OrderRefund;
        //判断当前的执行进度流程是否符合要求        
        $current_service_status = $order_refund_info->service_status . '_' . $order_refund_info->refund_status . '_' . $order_refund_info->return_status;
        $service_status_purview_array = isset( $this->service_status_map[ $order_refund_info->refund_service_status ][ $current_service_status ][ $this->identity ] ) ? $this->service_status_map[ $order_refund_info->refund_service_status ][ $current_service_status ][ $this->identity ] : array();

        $next_service_status = $this->service_status . '_' . $this->refund_status . '_' . $this->return_status;        
        if ( !in_array( $next_service_status, $service_status_purview_array ) ) {
            $this->errorMessage = '售后流程没有对应的权限';
            return false;
        }
        return true;
    }

    /**
     * 修改订单售后的状态
     * 根据当前的售后状态，及新的状态，修改成下一个状态
     * =======================================          
     * $this->identity;     
     * $this->service_status;
     * $this->refund_status;
     * $this->return_status;     
     * $this->service_uid;          
     * $this->service_level_status;
     * $this->member_bill_id;
     * $this->reason;//可选
     * $this->modifyOrderGoodsService();
     */
    public function modifyOrderRefundService( $entity_OrderRefund )
    {
        $entity_OrderRefund instanceof entity_OrderRefund_base;
        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_service_dao = dao_factory_base::getOrderServiceDao();
        //权限检测放在外层
        $order_refund_dao->setPk( $entity_OrderRefund->order_refund_id );
        //判断当前的执行进度流程是否符合要求    
        if ( $this->checkOrderRefundServicePurview( $entity_OrderRefund ) == false ) {
            return FALSE;
        }
        $service_username_array = Tmac::config( 'order.order_service.identity_name', APP_BASE_NAME );
        $service_username = $service_username_array[ $this->identity ];
        $next_service_status = $this->service_status . '_' . $this->refund_status . '_' . $this->return_status;
        $service_note = $this->service_status_map[ $entity_OrderRefund->refund_service_status ][ $next_service_status ][ 'description' ];
        //更新order_goods表
        $entity_OrderRefund_base = new entity_OrderRefund_base();
        $entity_OrderRefund_base->service_status = $this->service_status;
        $entity_OrderRefund_base->refund_status = $this->refund_status;
        $entity_OrderRefund_base->return_status = $this->return_status;
        if ( $entity_OrderRefund->refund_ing == 1 ) {
            $entity_OrderRefund_base->refund_ing = 0;
        }
        if ( $this->service_level_status ) {
            //买家重新发起退款申请｜买家重新发起退款退货申请｜买起重新修改退货的物流信息
            $entity_OrderRefund_base->service_level = new TmacDbExpr( 'service_level+1' );
            $service_note = str_replace( '{service_level}', '再次', $service_note );
        } else {
            $service_note = str_replace( '{service_level}', '', $service_note );
        }
        $entity_OrderRefund_base->service_note = $service_note;
        $order_refund_dao->updateByPk( $entity_OrderRefund_base );

        if ( !empty( $this->reason ) ) {//拒绝理由
            $service_note .= $this->reason;
        }
        //插入order_service表
        $entity_OrderService_base = new entity_OrderService_base();
        $entity_OrderService_base->order_refund_id = $entity_OrderRefund->order_refund_id;
        $entity_OrderService_base->order_goods_id = $entity_OrderRefund->order_goods_id;
        $entity_OrderService_base->order_id = $entity_OrderRefund->order_id;
        $entity_OrderService_base->money = $entity_OrderRefund->money;
        $entity_OrderService_base->service_status = $this->service_status;
        $entity_OrderService_base->refund_status = $this->refund_status;
        $entity_OrderService_base->return_status = $this->return_status;
        $entity_OrderService_base->service_note = $service_note;
        $entity_OrderService_base->service_uid = $this->service_uid;
        $entity_OrderService_base->service_username = $service_username;
        $entity_OrderService_base->member_bill_id = $this->member_bill_id;
        $entity_OrderService_base->service_time = $this->now;
        $order_service_dao->insert( $entity_OrderService_base );
        return true;
    }

}
