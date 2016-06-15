<?php

/**
 * 买家订单处理的类 
 * 取消订单 
 * 确认收货
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: BuyerHandle.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_BuyerHandle_base extends service_Order_base
{

    protected $uid;
    protected $order_sn;
    protected $image_size;    
    protected $auto_confirm_status = false;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setOrder_sn( $order_sn )
    {
        $this->order_sn = $order_sn;
    }

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setAuto_confirm_status( $auto_confirm_status )
    {
        $this->auto_confirm_status = $auto_confirm_status;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 买家取消订单
     * $this->uid;
     * $this->order_sn;
     * $this->cancelOrderInfo();
     */
    public function cancelOrderInfo()
    {
        //检测订单权限
        $this->checkBuyerPriview();
        //检测订单当前状态
        if ( $this->orderInfo->order_status <> service_Order_base::order_status_buyer_order_create ) {
            throw new TmacClassException( '订单已经付款了，请申请退款' );
        }
        //执行取消事务
        $dao = dao_factory_base::getOrderInfoDao();
        $order_action_dao = dao_factory_base::getOrderActionDao();
        $dao->getDb()->startTrans();

        //order_info表
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->order_status = service_Order_base::order_status_close;
        $entity_OrderInfo_base->close_status = service_Order_base::close_status_cancel;
        $dao->setPk( $this->orderInfo->order_id );
        $dao->updateByPk( $entity_OrderInfo_base );
        //order_action表
        $entity_OrderAction_base = new entity_OrderAction_base();
        $entity_OrderAction_base->order_id = $this->orderInfo->order_id;
        $entity_OrderAction_base->action_uid = $this->orderInfo->uid;
        $entity_OrderAction_base->action_username = '买家';
        $entity_OrderAction_base->order_status = $entity_OrderInfo_base->order_status;
        $entity_OrderAction_base->shipping_status = $this->orderInfo->shipping_status;
        $entity_OrderAction_base->pay_status = $this->orderInfo->pay_status;
        $entity_OrderAction_base->refund_status = $this->orderInfo->refund_status;
        $entity_OrderAction_base->action_note = "取消订单";
        $entity_OrderAction_base->action_time = $this->now;
        $order_action_dao->insert( $entity_OrderAction_base );

        //取消订单恢复库存
        $this->plusGoodsStock( $this->orderInfo->order_id );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $order_status_map = Tmac::config( 'order.buyer.order_status', APP_BASE_NAME );
            $this->order_status_text = $order_status_map[ $entity_OrderInfo_base->order_status ];
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 检测买家的操作订单权限
     * $this->order_sn;
     * $this->uid;
     * $this->checkBuyerPriview();
     * @return type
     * @throws TmacClassException
     */
    public function checkBuyerPriview()
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setWhere( "order_sn='{$this->order_sn}'" );
        $orderInfo = $dao->getInfoByWhere();
        if ( !$orderInfo ) {
            throw new TmacClassException( '要操作的订单不存在' );
        }
        if ( $orderInfo->uid <> $this->uid && $this->adminPurview === false ) {
            throw new TmacClassException( '只能操作自己订单' );
        }
        $this->orderInfo = $orderInfo;
        return $orderInfo;
    }

    /**
     * 检测买家/卖家的操作订单权限
     * $this->order_sn;
     * $this->uid;
     * $this->checkViewPriview();
     * @return type
     * @throws TmacClassException
     */
    public function checkViewPriview()
    {
        $dao = dao_factory_base::getOrderInfoDao();
        if ( empty( $this->order_sn ) ) {
            $dao->setWhere( "order_id={$this->order_id}" );
        } else {
            $dao->setWhere( "order_sn='{$this->order_sn}'" );
        }
        $orderInfo = $dao->getInfoByWhere();
        if ( !$orderInfo ) {
            throw new TmacClassException( '要操作的订单不存在' );
        }
        if ( $orderInfo->uid <> $this->uid && $this->adminPurview === false && $orderInfo->item_uid <> $this->uid && $orderInfo->goods_uid <> $this->uid ) {
            throw new TmacClassException( '只能操作自己订单' );
        }
        $this->orderInfo = $orderInfo;
        return $orderInfo;
    }

    /**
     * 买家订单确认收货
     * 执行打款操作
     * $this->uid;
     * $this->order_sn;
     * $this->confirmOrderInfo();
     */
    public function confirmOrderInfo()
    {
        //检测订单权限
        $this->checkBuyerPriview();
        //检测订单当前状态
        if ( $this->orderInfo->order_status <> service_Order_base::order_status_seller_delivery ) {
            throw new TmacClassException( '订单还没有发货，不能确认收货' );
        }
        //执行确认收货的事务
        $dao = dao_factory_base::getOrderInfoDao();
        $member_bill_dao = dao_factory_base::getMemberBillDao();
        $order_action_dao = dao_factory_base::getOrderActionDao();
        $dao->getDb()->startTrans();

        //order_info表
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->order_status = service_Order_base::order_status_complete;
        $entity_OrderInfo_base->confirm_time = $this->now;
        $dao->setPk( $this->orderInfo->order_id );
        $dao->updateByPk( $entity_OrderInfo_base );

        //member_bill表 更新账单表中的 确认收货状态
        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
        $entity_MemberBill_base->order_finish = service_Member_base::order_finish_no;
        $entity_MemberBill_base->confirm_time = $this->now;
        $where = "order_id={$this->orderInfo->order_id}";
        $member_bill_dao->setWhere( $where );
        $member_bill_dao->updateByWhere( $entity_MemberBill_base );

        /**
         * member_setting表中的current_money字段更新 
         * 这一步操作放在crontab中做 超过15天的 订单 完成的。执行状态更新。
         */
        //$this->updateMemberSettingMoney();

        if ( $this->auto_confirm_status ) {
            $action_uid = 0;
            $action_username = '聚店';
            $action_note = '系统自动确认收货，订单完成';
        } else {
            $action_uid = $this->orderInfo->uid;
            $action_username = '买家';
            $action_note = '确认收货，订单完成';
        }
        //order_action表
        $entity_OrderAction_base = new entity_OrderAction_base();
        $entity_OrderAction_base->order_id = $this->orderInfo->order_id;
        $entity_OrderAction_base->action_uid = $action_uid;
        $entity_OrderAction_base->action_username = $action_username;
        $entity_OrderAction_base->order_status = $entity_OrderInfo_base->order_status;
        $entity_OrderAction_base->shipping_status = $this->orderInfo->shipping_status;
        $entity_OrderAction_base->pay_status = $this->orderInfo->pay_status;
        $entity_OrderAction_base->refund_status = $this->orderInfo->refund_status;
        $entity_OrderAction_base->action_note = $action_note;
        $entity_OrderAction_base->action_time = $this->now;
        $order_action_dao->insert( $entity_OrderAction_base );

        //消息push
        $push_message_model = new service_PushMessage_base();
        $push_message_model->setMessageType( service_PushMessage_base::message_type_confirm_receipt );
        $push_message_model->setOrderInfo( $this->orderInfo );
        $push_message_model->push();

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $order_status_map = Tmac::config( 'order.buyer.order_status', APP_BASE_NAME );
            $this->order_status_text = $order_status_map[ $entity_OrderInfo_base->order_status ];
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 买家订单延长确认收货时间
     * 执行打款操作
     * $this->uid;
     * $this->order_sn;
     * $this->confirmOrderInfo();
     */
    public function extendConfirmOrderInfo()
    {
        //检测订单权限
        $this->checkBuyerPriview();
        //检测订单当前状态
        if ( $this->orderInfo->order_status <> service_Order_base::order_status_seller_delivery ) {
            throw new TmacClassException( '订单还没有发货，不能延长收货哟' );
        }
        if ( ($this->orderInfo->confirm_deadline_time - $this->orderInfo->shipping_time) > (8 * 86400) ) {
            throw new TmacClassException( '只有一次延长收货时间的机会，如果还没有收到货，请联系银品惠客服' );
        }
        //执行确认收货的事务
        $dao = dao_factory_base::getOrderInfoDao();
        $order_action_dao = dao_factory_base::getOrderActionDao();
        $dao->getDb()->startTrans();

        //order_info表
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->confirm_deadline_time = new TmacDbExpr( 'confirm_deadline_time+259200' );
        $dao->setPk( $this->orderInfo->order_id );
        $dao->updateByPk( $entity_OrderInfo_base );

        $action_uid = $this->orderInfo->uid;
        $action_username = '买家';
        $action_note = '延长确认收货时间3天';
        //order_action表
        $entity_OrderAction_base = new entity_OrderAction_base();
        $entity_OrderAction_base->order_id = $this->orderInfo->order_id;
        $entity_OrderAction_base->action_uid = $action_uid;
        $entity_OrderAction_base->action_username = $action_username;
        $entity_OrderAction_base->order_status = $this->orderInfo->order_status;
        $entity_OrderAction_base->shipping_status = $this->orderInfo->shipping_status;
        $entity_OrderAction_base->pay_status = $this->orderInfo->pay_status;
        $entity_OrderAction_base->refund_status = $this->orderInfo->refund_status;
        $entity_OrderAction_base->action_note = $action_note;
        $entity_OrderAction_base->action_time = $this->now;
        $order_action_dao->insert( $entity_OrderAction_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $order_status_map = Tmac::config( 'order.buyer.order_status', APP_BASE_NAME );
            $this->order_status_text = $order_status_map[ $this->orderInfo->order_status ];
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * member_setting 表中的money相关字段更新
     * @2015-07-16增加 判断如果有分销商。分别向分销商和供应商
     * 在用户确认付款后，把对应的可用余额 增加到卖家的 current_money 字段中
     * @return type
     */
    private function updateMemberSettingMoney()
    {
        $entity_OrderInfo_base = $this->orderInfo;
        $entity_OrderInfo_base instanceof entity_OrderInfo_base;
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        if ( $this->checkIsSelfGoods( $entity_OrderInfo_base ) ) {//如果非分销
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money+' . $entity_OrderInfo_base->order_amount );

            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $entity_OrderInfo_base->goods_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        } else {//分销商品
            $seller_amount = $entity_OrderInfo_base->commission_fee; //分销商的金额
            $supplier_amount = $entity_OrderInfo_base->order_amount - $entity_OrderInfo_base->commission_fee; //供应商的金额 订单总金额减去给分销商的佣金
            //分销商的钱更新开始
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money+' . $seller_amount );
            //更新卖家的金钱 商品分销商UID
            $member_setting_dao->setPk( $entity_OrderInfo_base->item_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
            //分销商的钱更新结束
            //供应商的钱更新开始       

            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money+' . $supplier_amount );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $entity_OrderInfo_base->goods_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
            //供应商的钱更新结束                       
        }

        return true;
    }

    /**
     * 判断订单是不是卖家自营的商品
     * @param type $item_uid
     * @param type $item_id
     * @return boolean
     */
    private function checkIsSelfGoods( $entity_OrderInfo_base )
    {
        if ( $entity_OrderInfo_base->item_uid <> $entity_OrderInfo_base->goods_uid ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * $this->order_id;
     * $this->getOrderInfoDetail();
     */
    public function getOrderInfoDetail()
    {
        $order_info = $this->orderInfo;
        $order_config_array = Tmac::config( 'order.buyer.order_status', APP_BASE_NAME );

        $order_info->order_goods_array = self::getOrderGoodsArray( $order_info->order_id, $order_info->have_return_service );
        $order_info->order_status_text = $order_config_array[ $order_info->order_status ];
        $order_info->order_item_count = count( $order_info->order_goods_array );
        $order_info->create_time = date( 'Y-m-d H:i:s', $order_info->create_time );
        $order_info->pay_time = empty( $order_info->pay_time ) ? '' : date( 'Y-m-d H:i:s', $order_info->pay_time );
        $order_info->confirm_time = empty( $order_info->confirm_time ) ? '' : date( 'Y-m-d H:i:s', $order_info->confirm_time );
        $order_info->shipping_time = empty( $order_info->shipping_time ) ? '' : date( 'Y-m-d H:i:s', $order_info->shipping_time );
        $order_info->shop_logo_url = $this->getShopImage( $order_info->item_uid );
        return $order_info;
    }

    /**
     * 取店铺的url
     * @param type $uid
     * @return type
     */
    private function getShopImage( $uid )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $uid );
        $dao->setField( 'shop_image_id' );
        $member_setting_info = $dao->getInfoByPk();
        $logo = '';
        if ( $member_setting_info ) {
            $logo = $this->getImage( $member_setting_info->shop_image_id, '110', 'shop' );
        }
        return $logo;
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
    public function getOrderGoodsArray( $order_id, $have_return_service )
    {
        $dao = dao_factory_base::getOrderGoodsDao();
        $dao->setWhere( "order_id={$order_id}" );
        $dao->setField( 'order_goods_id,item_id,goods_id,item_name,item_number,item_price,goods_sku_name,goods_image_id' );
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
                $value->return_service_status = false; //是否能售后

                if ( $have_return_service > 0 && !empty( $order_refund_map_array[ $value->order_goods_id ] ) ) {
                    $order_refund_object = $order_refund_map_array[ $value->order_goods_id ];

                    $value->service_status = $order_refund_object->service_status;
                    $value->service_status_text = $service_status_array[ $order_refund_object->service_status ];
                    $value->order_refund_id = $order_refund_object->order_refund_id;
                } else if ( $this->orderInfo->order_status == service_Order_base::order_status_complete && empty( $value->service_status ) && ($this->now - $this->orderInfo->confirm_time) < parent::return_service_max_day * 86400 ) {
                    //判断在订单详细页面中 订单商品是否能进行售后
                    $value->return_service_status = true; //可以申请售后,这里还是有一些 不太完整，比如一个订单有两个商品时，只要申请了一个售后，其他的商品就不能在列表页面中申请了
                }
                unset( $value->goods_image_id );
                unset( $value->refund_status );
                unset( $value->return_status );
            }
        }
        return $res;
    }

}
