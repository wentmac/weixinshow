<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: SellerHandle.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_SellerHandle_base extends service_Order_base
{

    /**
     * 快递公司，其他
     */
    const express_id_other = 0;

    /**
     * 快递公司，－1：不需要发货
     */
    const express_id_no_required = -1;

    protected $express_id;
    protected $express_name;
    protected $express_code;
    protected $express_no;
    protected $image_size;
    protected $shipping_time;
    protected $order_id_string;

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

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setShipping_time( $shipping_time )
    {
        $this->shipping_time = $shipping_time;
    }

    function setOrder_id_string( $order_id_string )
    {
        $this->order_id_string = $order_id_string;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测卖家的操作订单权限
     * $this->order_id;
     * $this->uid;
     * $this->checkSellerPriview();
     * @return type
     * @throws TmacClassException
     */
    public function checkSellerPriviewByOrderId( $order_id )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $order_id );
        $orderInfo = $dao->getInfoByPk();
        if ( !$orderInfo ) {
            $this->errorMessage = '要操作的订单不存在';
            return false;
        }
        if ( $orderInfo->goods_uid <> $this->uid ) {
            $this->errorMessage = '只能操作自己订单';
            return false;
        }
        return $orderInfo;
    }

    /**
     * 卖家执行发货操作
     * 
     * $this->order_id;
     * $this->express_id;
     * $this->express_name;
     * $this->express_no;
     * $this->seller_delivery();
     */
    public function sellerDelivery()
    {
        if ( !empty( $this->shipping_time ) ) {
            $this->now = $this->shipping_time;
        }
        if ( $this->express_id == -1 ) {
            $this->errorMessage = '请填写快递公司信息';
        }
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_action_dao = dao_factory_base::getOrderActionDao();

        //取快递名称
        $this->getExpressNameById( $this->express_id );

        $order_id_array = explode( ',', $this->order_id_string );
        if ( empty( $order_id_array ) ) {
            $order_id_array = array( $this->order_id );
        }
        foreach ( $order_id_array as $order_id ) {
            //判断是否有权限        
            $order_info = $this->checkSellerPriviewByOrderId( $order_id );
            if ( $order_info === false ) {
                $this->errorMessage = '订单号：' . $order_info->order_sn . '|没有操作权限~';
                return false;
            }
            //检测订单的发货权限流程              
            if ( $order_info->order_status <> service_Order_base::order_status_buyer_payment && $order_info->order_status <> service_Order_base::order_status_seller_delivery ) {
                $this->errorMessage = '订单号：' . $order_info->order_sn . '|不能再处理发货信息了~';
                return false;
            }

            $order_info_dao->getDb()->startTrans();
            //order_info表
            $entity_OrderInfo_base = new entity_OrderInfo_base();
            $entity_OrderInfo_base->order_status = service_Order_base::order_status_seller_delivery;
            $entity_OrderInfo_base->shipping_status = service_Order_base::shipping_status_seller_delivery;
            $entity_OrderInfo_base->shipping_time = $this->now;
            $entity_OrderInfo_base->confirm_deadline_time = $this->now + 86400 * 7;
            $entity_OrderInfo_base->express_id = $this->express_id;
            $entity_OrderInfo_base->express_code = $this->express_code;
            $entity_OrderInfo_base->express_name = $this->express_name;
            $entity_OrderInfo_base->express_no = $this->express_no;
            $order_info_dao->setPk( $order_id );
            $order_info_dao->updateByPk( $entity_OrderInfo_base );


            if ( $this->express_id == self::express_id_no_required ) {
                $action_note = '无需发货';
            } else {
                $action_note = $this->express_name . ',快递单号：' . $this->express_no;
            }
            if ( $order_info->order_status == service_Order_base::order_status_seller_delivery ) {
                $action_note .= '（修改快递信息）';
            }
            //order_action表
            $entity_OrderAction_base = new entity_OrderAction_base();
            $entity_OrderAction_base->order_id = $order_id;
            $entity_OrderAction_base->action_uid = $this->uid;
            $entity_OrderAction_base->action_username = '卖家';
            $entity_OrderAction_base->order_status = $entity_OrderInfo_base->order_status;
            $entity_OrderAction_base->shipping_status = $entity_OrderInfo_base->shipping_status;
            $entity_OrderAction_base->pay_status = $order_info->pay_status;
            $entity_OrderAction_base->refund_status = $order_info->refund_status;
            $entity_OrderAction_base->action_note = "卖家发货，{$action_note}";
            $entity_OrderAction_base->action_time = $this->now;
            $order_action_dao->insert( $entity_OrderAction_base );

            //push message
            //TODO 发短信
            $push_message_model = new service_PushMessage_base();
            $push_message_model->setMessageType( service_PushMessage_base::message_type_delivery );
            $push_message_model->setOrderInfo( $order_info );
            $push_message_model->push();

            if ( $order_info_dao->getDb()->isSuccess() ) {
                $order_info_dao->getDb()->commit();
                $order_status_map = Tmac::config( 'order.seller.order_status', APP_BASE_NAME );
                $this->order_status_text = $order_status_map[ $entity_OrderInfo_base->order_status ];
            } else {
                $order_info_dao->getDb()->rollback();
                $this->errorMessage = '订单号：' . $order_info->order_sn . '|执行失败~';
                return false;
            }
        }
        return true;
    }

    private function getExpressNameById( $express_id )
    {
        if ( $express_id == -1 ) {
            $this->express_name = '无需物流';
            return $this->express_name;
        }
        $dao = dao_factory_base::getExpressDao();
        $dao->setField( 'express_name,express_code' );
        $dao->setPk( $express_id );
        $express_info = $dao->getInfoByPk();
        if ( !$express_info ) {
            $this->express_id = 0;
            //$this->express_name = '其他';
        } else {
            $this->express_name = $express_info->express_name;
            $this->express_code = $express_info->express_code;
        }
        return $this->express_name;
    }

    /**
     * $this->order_id;
     * $this->getOrderInfoDetail();
     */
    public function getOrderInfoDetail()
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $this->order_id );
        $dao->setField( 'uid,order_id,order_sn,order_status,mobile,consignee,full_address,address,postscript,refund_status,have_return_service,shipping_fee,order_total_price,order_amount,order_note,create_time,pay_time,confirm_time,shipping_time,express_id,express_name,express_no,order_refund_id,item_uid,supplier_mobile,goods_uid,commission_fee,commission_fee_rank,coupon_code,coupon_money,agent_uid,rank_uid,address_id' );

        $order_info = $dao->getInfoByPk();
        $order_config_array = Tmac::config( 'order.seller.order_status', APP_BASE_NAME );

        $order_info->order_goods_array = self::getOrderGoodsArray( $order_info->order_id, $order_info->have_return_service );
        $order_info->order_status_text = $order_config_array[ $order_info->order_status ];
        $order_info->order_item_count = count( $order_info->order_goods_array );
        $order_info->create_time = date( 'Y-m-d H:i:s', $order_info->create_time );
        $order_info->pay_time = empty( $order_info->pay_time ) ? '' : date( 'Y-m-d H:i:s', $order_info->pay_time );
        $order_info->confirm_time = empty( $order_info->confirm_time ) ? '' : date( 'Y-m-d H:i:s', $order_info->confirm_time );
        $order_info->shipping_time = empty( $order_info->shipping_time ) ? '' : date( 'Y-m-d H:i:s', $order_info->shipping_time );
        //增加一个新的状态 判断卖家是否能修改物流信息
        $order_info->is_edit_delivery = false;
        if ( $order_info->order_status == service_Order_base::order_status_buyer_payment || $order_info->order_status == service_Order_base::order_status_seller_delivery ) {
            $order_info->is_edit_delivery = true;
        }
        $order_info->supplier_status = ($this->uid == $order_info->goods_uid) ? true : false;

        $order_info->supplier_amount = $order_info->order_amount;
        if ( !empty( $order_info->agent_uid ) ) {
            $order_info->supplier_amount -= $order_info->commission_fee;
        }
        if ( !empty( $order_info->rank_uid ) ) {
            $order_info->supplier_amount -= $order_info->commission_fee_rank;
        }

        //parent::handleFreeSupplierOrderShow( $order_info );
        $member_model = new service_Member_base();
        $member_info = $member_model->getMemberInfoByUid( $order_info->uid );

        $order_info->nickname = $member_info->nickname;
        unset( $order_info->goods_uid );
        return $order_info;
    }

    /**
     * 取订单的退款列表
     * @param type $order_id
     * @return type
     */
    private function getOrderRefundMapArray( $order_id )
    {
        $dao = dao_factory_base::getOrderRefundDao();
        $dao->setWhere( "order_id={$order_id}" );
        $dao->setField( 'order_refund_id,order_goods_id,service_status,refund_status,return_status' );
        $res = $dao->getListByWhere();
        $result_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $result_array[ $value->order_goods_id ] = $value;
            }
        }
        return $result_array;
    }

    /**
     * 通过订单号取订单商品
     * @param type $order_id
     */
    protected function getOrderGoodsArray( $order_id, $have_return_service )
    {
        $dao = dao_factory_base::getOrderGoodsDao();
        $dao->setWhere( "order_id={$order_id}" );
        $dao->setField( 'order_goods_id,item_id,item_name,item_number,item_price,goods_sku_name,goods_image_id' );
        $res = $dao->getListByWhere();
        $order_refund_map_array = array();
        if ( $have_return_service > 0 ) {
            $order_refund_map_array = self::getOrderRefundMapArray( $order_id );
        }
        if ( $res ) {
            $service_status_array = Tmac::config( 'order.order_refund.service_status', APP_BASE_NAME );
            foreach ( $res as $value ) {
                $value->goods_image_url = $this->getImage( $value->goods_image_id, $this->image_size, 'goods' );
                $value->service_status = 0;
                $value->service_status_text = '';
                $value->order_refund_id = 0;

                if ( $have_return_service > 0 && !empty( $order_refund_map_array[ $value->order_goods_id ] ) ) {
                    $order_refund_object = $order_refund_map_array[ $value->order_goods_id ];

                    $value->service_status = $order_refund_object->service_status;
                    $value->service_status_text = $service_status_array[ $order_refund_object->service_status ];
                    $value->order_refund_id = $order_refund_object->order_refund_id;
                }
                unset( $value->goods_image_id );
                unset( $value->refund_status );
                unset( $value->return_status );
            }
        }
        return $res;
    }

    /**
     * $this->order_id;
     * $this->updateOrderInfo();
     */
    public function updateOrderInfo( entity_OrderInfo_base $entity_OrderInfo_base )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $this->order_id );

        return $dao->updateByPk( $entity_OrderInfo_base );
    }

}
