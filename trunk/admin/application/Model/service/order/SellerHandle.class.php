<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: SellerHandle.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_SellerHandle_admin extends service_order_SellerHandle_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * $this->order_id;
     * $this->getOrderInfoDetail();
     */
    public function getOrderInfoDetail()
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $this->order_id );
        $dao->setField( 'order_id,uid,shop_name,item_mobile,order_sn,order_status,mobile,consignee,full_address,postscript,refund_status,have_return_service,shipping_fee,order_amount,order_note,create_time,pay_time,confirm_time,shipping_time,express_id,express_name,express_no,order_refund_id,item_uid,supplier_mobile,goods_uid,demo_order,commission_fee,commission_fee_rank,confirm_deadline_time,order_type,coupon_code,coupon_money' );

        $order_info = $dao->getInfoByPk();        
        $order_config_array = Tmac::config( 'order.seller.order_status', APP_BASE_NAME );
        $demo_order_config_array = Tmac::config( 'order.system.demo_order', APP_BASE_NAME );

        $model = new service_order_BuyerHandle_mobile();
        $model->setImage_size( $this->image_size );
        $model->setOrderInfo( $order_info );        
        $order_info->order_goods_array = $model->getOrderGoodsArray( $order_info->order_id, $order_info->have_return_service );
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
        $order_info->demo_order = $demo_order_config_array[ $order_info->demo_order ];

        if ( $order_info->confirm_deadline_time > $this->now ) {
            //true:可以延长收货 false:不能延长收货
            $order_info->extend_confirm_deadline_time_status = ($order_info->confirm_deadline_time - $order_info->shipping_time) > (8 * 86400) ? false : true;
            //离自动确认收货还剩的秒数
            $order_info->confirm_deadline_time = $order_info->confirm_deadline_time - $this->now;
        } else {
            $order_info->confirm_deadline_time = 0;
            $order_info->extend_confirm_deadline_time_status = false;
        }
        //parent::handleFreeSupplierOrderShow( $order_info );        
        return $order_info;
    }

}
