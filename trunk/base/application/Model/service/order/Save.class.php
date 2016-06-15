<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Save.class.php 360 2016-06-09 16:23:43Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Save_base extends service_Order_base
{

    /**
     * 聚店官方店铺
     */
    const judian_shop_id = 2690;

    protected $goods_uid;
    protected $item_uid;
    protected $address_id;
    protected $cart_id_string;
    protected $postscript;
    protected $weixin_id;
    protected $cart_array;
    protected $order_action_username;
    protected $order_time;
    protected $demo_order;
    protected $coupon_code;
    protected $agent_uid;

    function setAgent_uid( $agent_uid )
    {
        $this->agent_uid = $agent_uid;
    }

    function setGoods_uid( $goods_uid )
    {
        $this->goods_uid = $goods_uid;
    }

    function setItem_uid( $item_uid )
    {
        $this->item_uid = $item_uid;
    }

    function setAddress_id( $address_id )
    {
        $this->address_id = $address_id;
    }

    function setCart_id_string( $cart_id_string )
    {
        $this->cart_id_string = $cart_id_string;
    }

    function setPostscript( $postscript )
    {
        $this->postscript = $postscript;
    }

    function setWeixin_id( $weixin_id )
    {
        $this->weixin_id = $weixin_id;
    }

    function setOrder_action_username( $order_action_username )
    {
        $this->order_action_username = $order_action_username;
    }

    function setOrder_time( $order_time )
    {
        $this->order_time = $order_time;
    }

    function setDemo_order( $demo_order )
    {
        $this->demo_order = $demo_order;
    }

    function setCoupon_code( $coupon_code )
    {
        $this->coupon_code = $coupon_code;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 订单保存 
     * $this->uid;
     * $this->item_uid;
     * $this->address_id;
     * $this->cart_id_string;
     * $this->createOrder();
     */
    public function createOrder()
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_goods_dao = dao_factory_base::getOrderGoodsDao();
        $order_action_dao = dao_factory_base::getOrderActionDao();
        $cart_dao = dao_factory_base::getCartDao();
        $member_dao = dao_factory_base::getMemberDao();

        //检查购物车中是否有商品
        $cart_dao->setWhere( "uid={$this->uid} AND item_uid={$this->item_uid} AND goods_uid={$this->goods_uid} AND " . $cart_dao->getWhereInStatement( 'cart_id', $this->cart_id_string ) );
        $cart_count = $cart_dao->getCountByWhere();
        if ( $cart_count == 0 ) {
            throw new TmacClassException( '购物车中的商品不能为空' );
        }
        //取收货人信息
        $address_info = $this->getMemberAddress();
        //TODO 配送方式
        if ( !empty( $this->order_time ) ) {
            $this->now = $this->order_time;
        }
        //order_info表
        $address_info instanceof entity_MemberAddress_base;
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->order_sn = $this->get_order_sn();
        $entity_OrderInfo_base->uid = $this->uid;
        $entity_OrderInfo_base->order_status = service_Order_base::order_status_buyer_order_create;
        $entity_OrderInfo_base->shipping_status = 0;
        $entity_OrderInfo_base->pay_status = 0;
        $entity_OrderInfo_base->consignee = $address_info->consignee;
        $entity_OrderInfo_base->mobile = $address_info->mobile;
        $entity_OrderInfo_base->address_id = $address_info->address_id;
        $entity_OrderInfo_base->country = $address_info->country;
        $entity_OrderInfo_base->province = $address_info->province;
        $entity_OrderInfo_base->city = $address_info->city;
        $entity_OrderInfo_base->district = $address_info->district;
        $entity_OrderInfo_base->address = $address_info->address;
        $entity_OrderInfo_base->full_address = $address_info->full_address;
        $entity_OrderInfo_base->postscript = $this->postscript;
        $entity_OrderInfo_base->weixin_id = $this->weixin_id;
        $order_price = $this->getOrderAmount(); //取订单总价和订单总佣金和运费
        $entity_OrderInfo_base->order_total_price = $order_price[ 'order_total_price' ];
        $entity_OrderInfo_base->order_payable_amount = $order_price[ 'order_payable_amount' ];
        $entity_OrderInfo_base->order_amount = $order_price[ 'order_amount' ];
        $entity_OrderInfo_base->order_integral_amount = $order_price[ 'order_integral_amount' ];
        $entity_OrderInfo_base->commission_fee = $order_price[ 'commission_fee' ];
        $entity_OrderInfo_base->commission_fee_rank = $order_price[ 'commission_fee_rank' ];
        $entity_OrderInfo_base->shipping_fee = $order_price[ 'shipping_fee' ]; //配送费用
        $entity_OrderInfo_base->referer = isset( $_SERVER [ 'HTTP_REFERER' ] ) ? filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING ) : '';
        $entity_OrderInfo_base->create_time = $this->now;
        $entity_OrderInfo_base->item_uid = $this->item_uid;
        $seller_member = $this->getSellerMember();
        $entity_OrderInfo_base->item_mobile = $seller_member->mobile;
        $seller_member_setting = $this->getSellerMemberSetting();
        $entity_OrderInfo_base->shop_name = $seller_member_setting->shop_name;
        $entity_OrderInfo_base->supplier_mobile = $this->getSupplierMobile();
        $entity_OrderInfo_base->item_weixin_id = $seller_member_setting->weixin_id;
        $entity_OrderInfo_base->order_goods_detail = $this->getOrderGoodsDetail();
        $entity_OrderInfo_base->goods_uid = $this->goods_uid;
        $entity_OrderInfo_base->order_type = $order_price[ 'order_type' ];
        $entity_OrderInfo_base->agent_uid = $this->memberInfo->agent_uid; //推荐UID|付款的时候再更新 普通商品如果有佣金，付给直推人佣金；
        $entity_OrderInfo_base->rank_uid = 0; //如果是会员商品，本订单得到排位佣金的。对应第N层上家排位的uid.|付款的时候再更新
        $entity_OrderInfo_base->demo_order = $this->demo_order;
        $entity_OrderInfo_base->coupon_code = $order_price[ 'coupon_code' ];
        $entity_OrderInfo_base->coupon_money = $order_price[ 'coupon_money' ];
        $entity_OrderInfo_base->goods_member_level = $order_price[ 'goods_member_level' ];

        //检测会员商品是否还有未处理的退款
        $this->checkGoodsMemberRefundPurview( $entity_OrderInfo_base->order_type, $order_price[ 'goods_member_level' ] );
        //库存检查
        if ( $this->checkStock() == false ) {
            return false;
        }
        //检查会员商品和其他商品一起提交订单，会员商品只能单独结算
        if ( $this->checkGoodsMemberOnly() == false ) {
            return FALSE;
        }
        //检查是否是自营商品订单

        $order_info_dao->getDb()->startTrans();
        $order_id = $order_info_dao->insert( $entity_OrderInfo_base );
        $entity_OrderInfo_base->order_id = $order_id;

        $cart_id_array = array();
        $cart_array = $this->cart_array; //$this->cart_array 已经在上面getOrderAmount(); 中取过
        $cart_i = 0;
        $goods_uid = 0;
        $cart_count = count( $cart_array );

        $order_integral_amount = $order_price[ 'order_integral_amount' ];
        foreach ( $cart_array AS $cart_info ) {
            $cart_i === 0 && $goods_uid = $cart_info->goods_uid;
            //预防不同供应商的商品下在一个订单中了
            if ( $goods_uid <> $cart_info->goods_uid ) {
                continue;
            }
            $cart_i++;

            $entity_OrderGoods_base = new entity_OrderGoods_base();
            $cart_info instanceof entity_Cart_base;
            $entity_OrderGoods_base->order_id = $order_id;
            $entity_OrderGoods_base->goods_id = $cart_info->goods_id;
            $entity_OrderGoods_base->item_id = $cart_info->item_id;
            $entity_OrderGoods_base->item_name = $cart_info->goods_name;
            $entity_OrderGoods_base->item_number = $cart_info->item_number;
            $entity_OrderGoods_base->item_total_price = $cart_info->item_total_price;
            $entity_OrderGoods_base->item_price = $cart_info->item_price;
            $entity_OrderGoods_base->outer_code = $cart_info->outer_code;
            $entity_OrderGoods_base->goods_image_id = $cart_info->goods_image_id;
            $entity_OrderGoods_base->goods_sku_id = $cart_info->goods_sku_id;
            $entity_OrderGoods_base->goods_sku_name = $cart_info->goods_sku_name;
            $entity_OrderGoods_base->commission_fee = round( $cart_info->commission_fee * $cart_info->item_number, 2 );
            $entity_OrderGoods_base->commission_fee_rank = round( $cart_info->commission_fee_rank * $cart_info->item_number, 2 );
            $entity_OrderGoods_base->goods_member_level = $cart_info->goods_member_level;
            $entity_OrderGoods_base->goods_type = $cart_info->goods_type;

            $entity_OrderGoods_base->order_integral_amount = 0;
            if ( $cart_info->is_integral == service_Goods_base::is_integral_yes && $order_integral_amount > 0 ) {
                //订单商品总价
                $item_total_price = $cart_info->item_price * $cart_info->item_number;

                //订单商品使用的积分
                $integral_amount = $order_integral_amount > $item_total_price ? $item_total_price : $order_integral_amount;
                $entity_OrderGoods_base->order_integral_amount = $integral_amount;
                $order_integral_amount -= $integral_amount;
            }

            $order_goods_dao->insert( $entity_OrderGoods_base );

            //更新商品表的 销量
            if ( $seller_member_setting->stock_setting == service_Member_base::stock_setting_order_save ) {
                $this->updateGoodsStock( $entity_OrderGoods_base );
            }
            $cart_id_array[] = $cart_info->cart_id;
        }

        //order action 表
        $entity_OrderAction_base = new entity_OrderAction_base();
        $entity_OrderAction_base->order_id = $order_id;
        $entity_OrderAction_base->action_uid = $this->uid;
        $entity_OrderAction_base->action_username = $this->order_action_username;
        $entity_OrderAction_base->order_status = 0;
        $entity_OrderAction_base->shipping_status = 0;
        $entity_OrderAction_base->pay_status = 0;
        $entity_OrderAction_base->action_note = "用户{{$this->order_action_username}}下单成功";
        $entity_OrderAction_base->action_time = $this->now;
        $order_action_dao->insert( $entity_OrderAction_base );

        //customer 客户管理表
        $this->saveCustomer( $entity_OrderInfo_base );
        //删除cart_id_string
        self::deleteCart( implode( ',', $cart_id_array ) );
        //代金券使用
        $this->usedCouponCode( $entity_OrderInfo_base );
        //第一次购买lv1时填写推荐人的更新
        $this->updateMemberAgentUid();

        //减去余额
        if ( !empty( $entity_OrderInfo_base->order_integral_amount ) ) {
            $entity_Member_base = new entity_Member_base();
            $entity_Member_base->available_integral = new TmacDbExpr( 'available_integral-' . $entity_OrderInfo_base->order_integral_amount );
            $member_dao->setPk( $entity_OrderInfo_base->uid );
            $member_dao->updateByPk( $entity_Member_base );
        }

        if ( $order_info_dao->getDb()->isSuccess() ) {
            $order_info_dao->getDb()->commit();
            return $entity_OrderInfo_base->order_sn;
        } else {
            $order_info_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 第一次购买lv1时可以更新填写的agent_uid;
     */
    private function updateMemberAgentUid()
    {
        //级别大于1级或者已经有过推荐东家ID。不能再更新
        if ( $this->memberInfo->agent_lock == service_Member_base::agent_lock_yes ) {
            return true;
        }
        //如果agent_uid==自己退出
        if ( $this->agent_uid == $this->memberInfo->uid ) {
            return true;
        }
        if ( empty( $this->agent_uid ) ) {
            return true;
        }
        $register_model = new service_account_Register_base();
        $agent_info = $register_model->getUidByUid( $this->agent_uid );
        $agent_uid = $agent_info[ 'agent_uid' ];
        if ( !empty( $agent_uid ) ) {
            $entity_Member_base = new entity_Member_base();
            $entity_Member_base->agent_uid = $agent_uid;
            $dao = dao_factory_base::getMemberDao();
            $dao->setPk( $this->memberInfo->uid );
            $dao->updateByPk( $entity_Member_base );
        }
        return true;
    }

    /**
     * 检测会员商品是否有还在处理中的退款
     */
    private function checkGoodsMemberRefundPurview( $order_type, $goods_member_level )
    {
        if ( $order_type <> service_Order_base::order_type_member ) {
            return true;
        }
        $next_member_level = $this->memberInfo->member_level == service_Member_base::member_level_9 ? $this->memberInfo->member_level : $this->memberInfo->member_level + 1;
        if ( $goods_member_level > $next_member_level ) {
            throw new TmacClassException( '您目前只能购买LV' . $next_member_level . '级别或以下的会员哟' );
        }
        //判断当前没有没未结果的会员商品退款
        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_refund_dao->setField( 'order_refund_id' );
        $where = "uid={$this->uid} AND order_type=" . service_Order_base::order_type_member . " AND service_status=" . service_order_Service_base::service_status_waiting_seller_confirm;
        $order_refund_dao->setWhere( $where );
        $res = $order_refund_dao->getInfoByWhere();
        if ( $res ) {
            throw new TmacClassException( '您还有会员商品正在售后退款中,请先处理完退款后才能购买新的会员商品' );
        }
        $where = "uid={$this->uid} AND order_type=" . service_Order_base::order_type_member . " AND service_status=" . service_order_Service_base::service_status_waiting_buyer_confirm;
        $order_refund_dao->setWhere( $where );
        $res = $order_refund_dao->getInfoByWhere();
        if ( $res ) {
            throw new TmacClassException( '您还有会员商品正在售后退款中,请先处理完退款后才能购买新的会员商品' );
        }
        $where = "uid={$this->uid} AND order_type=" . service_Order_base::order_type_member . " AND service_status=" . service_order_Service_base::service_status_waiting_customer_confirm;
        $order_refund_dao->setWhere( $where );
        $res = $order_refund_dao->getInfoByWhere();
        if ( $res ) {
            throw new TmacClassException( '您还有会员商品正在售后退款中,请先处理完退款后才能购买新的会员商品' );
        }
        return true;
    }

    /**
     * 代金券使用
     * @param entity_OrderInfo_base $entity_OrderInfo_base
     */
    private function usedCouponCode( entity_OrderInfo_base $entity_OrderInfo_base )
    {
        if ( empty( $entity_OrderInfo_base->coupon_code ) && empty( $entity_OrderInfo_base->coupon_money ) ) {
            return true;
        }
        $entity_Coupon_base = new entity_Coupon_base();
        $entity_Coupon_base->coupon_status = service_Coupon_base::coupon_status_used;
        $entity_Coupon_base->use_time = $this->now;
        $entity_Coupon_base->order_id = $entity_OrderInfo_base->order_id;
        $entity_Coupon_base->order_sn = $entity_OrderInfo_base->order_sn;
        $dao = dao_factory_base::getCouponDao();
        $where = "coupon_code='{$entity_OrderInfo_base->coupon_code}'";
        $dao->setWhere( $where );
        return $dao->updateByWhere( $entity_Coupon_base );
    }

    private function deleteCart( $cart_id_string )
    {
        $dao = dao_factory_base::getCartDao();
        $where = $dao->getWhereInStatement( 'cart_id', $cart_id_string );
        $dao->setWhere( $where );
        return $dao->deleteByWhere();
    }

    private function getOrderGoodsDetail()
    {
        $order_goods_detail_array = array();
        foreach ( $this->cart_array AS $cart_info ) {
            $entity_OrderGoods_base = new stdClass();
            $cart_info instanceof entity_Cart_base;
            $entity_OrderGoods_base->item_id = $cart_info->item_id;
            $entity_OrderGoods_base->item_name = $cart_info->goods_name;
            $entity_OrderGoods_base->item_total_price = $cart_info->item_total_price;
            $entity_OrderGoods_base->item_number = $cart_info->item_number;
            $entity_OrderGoods_base->item_price = $cart_info->item_price;
            $entity_OrderGoods_base->goods_id = $cart_info->goods_id;
            $entity_OrderGoods_base->goods_image_id = $cart_info->goods_image_id;
            $entity_OrderGoods_base->goods_sku_name = $cart_info->goods_sku_name;
            $entity_OrderGoods_base->goods_member_level = $cart_info->goods_member_level;
            $entity_OrderGoods_base->outer_code = $cart_info->outer_code;
            $entity_OrderGoods_base->goods_type = $cart_info->goods_type;
            $order_goods_detail_array[] = $entity_OrderGoods_base;
        }
        return serialize( $order_goods_detail_array );
    }

    /**
     * 取分销商卖家的设置（商店名称｜库存设置）
     * $this->item_uid;
     * $this->getSellerMemberSetting();
     * @return type
     */
    public function getSellerMember()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->item_uid );
        $dao->setField( 'mobile' );
        return $dao->getInfoByPk();
    }

    /**
     * 取分销商卖家的设置（商店名称｜库存设置）
     * $this->item_uid;
     * $this->getSellerMemberSetting();
     * @return type
     */
    public function getSellerMemberSetting()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->item_uid );
        $dao->setField( 'shop_name,stock_setting,weixin_id' );
        return $dao->getInfoByPk();
    }

    /**
     * 取供应商卖家的手机号
     * $this->goods_uid;
     * $this->getSupplierMobile();
     * @return type
     */
    private function getSupplierMobile()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->goods_uid );
        $dao->setField( 'mobile' );
        $supplier_info = $dao->getInfoByPk();
        if ( $supplier_info ) {
            return $supplier_info->mobile;
        }
        return '';
    }

    /**
     * 更新商品库存
     * 根据专家的设置（拍下减库存｜或者付款减库存）
     */
    public function updateGoodsStock( $entity_OrderGoods_base )
    {
        $item_dao = dao_factory_base::getItemDao();
        $goods_dao = dao_factory_base::getGoodsDao();

        //更新所有goods_id的item中分销的库存和销量
        $entity_Item = new entity_Item_base();
        $entity_Item->item_stock = new TmacDbExpr( "item_stock-{$entity_OrderGoods_base->item_number}" );
        $entity_Item->sales_volume = new TmacDbExpr( "sales_volume+{$entity_OrderGoods_base->item_number}" );
        $item_dao->setWhere( "goods_id={$entity_OrderGoods_base->goods_id} AND is_self=1" );
        $item_dao->updateByWhere( $entity_Item );

        //更新goods表中的原商品的库存和销量
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->goods_stock = new TmacDbExpr( 'goods_stock-' . $entity_OrderGoods_base->item_number );
        $entity_Goods_base->sales_volume = new TmacDbExpr( 'sales_volume+' . $entity_OrderGoods_base->item_number );
        $goods_dao->setPk( $entity_OrderGoods_base->goods_id );
        $goods_dao->updateByPk( $entity_Goods_base );

        if ( !empty( $entity_OrderGoods_base->goods_sku_id ) ) {//goods_sku表 
            $goods_sku_dao = dao_factory_base::getGoodsSkuDao();
            $entity_GoodsSku_base = new entity_GoodsSku_base();
            $entity_GoodsSku_base->stock = new TmacDbExpr( 'stock-' . $entity_OrderGoods_base->item_number );
            $entity_GoodsSku_base->sales_volume = new TmacDbExpr( 'sales_volume+' . $entity_OrderGoods_base->item_number );
            $goods_sku_dao->setPk( $entity_OrderGoods_base->goods_sku_id );
            $goods_sku_dao->updateByPk( $entity_GoodsSku_base );
        }
        return true;
    }

    /**
     * 取用户收货地址
     * @return type
     */
    private function getMemberAddress()
    {
        $member_address_dao = dao_factory_base::getMemberAddressDao();
        $member_address_dao->setPk( $this->address_id );
        $address_info = $member_address_dao->getInfoByPk();
        if ( $address_info == false || $address_info->uid <> $this->uid ) {//判断收货地址的权限
            $where = "uid={$this->uid} AND is_delete=0 AND is_default=1";
            $member_address_dao->setWhere( $where );
            $address_info = $member_address_dao->getInfoByWhere();
        }
        if ( !$address_info ) {
            throw new TmacClassException( '默认收货地址不存在' );
        }
        return $address_info;
    }

    /**
     * 得到新订单号
     * @return  string
     */
    private function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand( (double) microtime() * 1000000 );

        return date( 'YmdHis' ) . str_pad( mt_rand( 1, 99999 ), 5, '0', STR_PAD_LEFT );
    }

    /**
     * 取订单总金额
     * $this->uid;
     * $this->item_uid;
     * $this->cart_id_string;
     * $this->getOrderAmount();
     * @return type
     */
    public function getOrderAmount()
    {
        $cart_dao = dao_factory_base::getCartDao();
        //检查购物车中是否有商品
        $cart_dao->setWhere( "uid={$this->uid} AND item_uid={$this->item_uid} AND " . $cart_dao->getWhereInStatement( 'cart_id', $this->cart_id_string ) );
        $cart_dao->setOrderby( 'cart_id DESC' );
        $cart_array = $cart_dao->getListByWhere();
        $this->cart_array = $cart_array;
        $order_total_price = 0;
        $order_amount = 0;
        $commission_fee = 0; //分销商订单总佣金
        $commission_fee_rank = 0; //系统的订单总佣金
        $cart_i = 0;
        $goods_uid = 0;
        $use_integral = false; //有没有积分商品
        $integral_value = 0; //可抵扣的积分
        $free_shipping_status = false; //邮费 有包邮按包邮算 没有包邮按最高算 之前邮费全是按最低的算的 现在邮费全是按最高的算的
        $shipping_fee_array = $shipping_fee_goods_sale_array = $member_level_array = array(); //运费
        foreach ( $cart_array as $cart_info ) {
            $cart_info instanceof entity_Cart_base;
            $cart_i === 0 && $goods_uid = $cart_info->goods_uid;
            //预防不同供应商的商品下在一个订单中了
            if ( $goods_uid <> $cart_info->goods_uid ) {
                continue;
            }
            $cart_i++;
            $order_total_price+=$cart_info->item_total_price * $cart_info->item_number;
            $order_amount+=$cart_info->item_price * $cart_info->item_number;
            $commission_fee+=$cart_info->commission_fee * $cart_info->item_number;
            $commission_fee_rank+=$cart_info->commission_fee_rank * $cart_info->item_number;
            $shipping_fee_array[] = $cart_info->shipping_fee;
            if ( $cart_info->shipping_fee == 0 ) {
                $free_shipping_status = true; //包邮
            }
            $member_level_array[] = $cart_info->goods_member_level;
            if ( $cart_info->goods_type == service_Goods_base::goods_type_sale ) {
                $shipping_fee_goods_sale_array[] = $cart_info->shipping_fee;
            }
            if ( $cart_info->is_integral == service_Goods_base::is_integral_yes ) {
                $use_integral = true;
                $integral_value += $cart_info->item_price * $cart_info->item_number;
            }
        }
        $shipping_fee = empty( $shipping_fee_goods_sale_array ) ? max( $shipping_fee_array ) : max( $shipping_fee_goods_sale_array );
        $goods_member_level = max( $member_level_array );

        if ( $goods_member_level > 0 ) {
            $order_type = service_Order_base::order_type_member;
        } else {
            $goods_type_order_type_map = Tmac::config( 'goods.goods.goods_type_order_type_map', APP_BASE_NAME );
            $order_type = $goods_type_order_type_map[ $cart_info->goods_type ];
            //这样写的原因是不同类型商品不能一起结算 ，一起提交订单结算的都是一种商品类型的。
        }
        /**
          if ( $goods_uid == service_Member_base::yph_uid ) {
          $shipping_fee = $order_amount > service_order_Cart_base::free_shipping_amount ? 0 : $shipping_fee;
          }
         * */
        if ( $free_shipping_status ) {//包邮邮费
            $shipping_fee = 0;
        }
        $order_total_amount = $order_amount + $shipping_fee;
        $coupon_money = 0;
        if ( !empty( $this->coupon_code ) ) {
            $oupon_model = new service_order_Coupon_mall();
            $oupon_model->setMall_uid( $this->item_uid );
            $oupon_model->setTotal_amount( $order_total_amount );
            $oupon_model->setCoupon_code( $this->coupon_code );
            $res = $oupon_model->checkCoupon();
            if ( $res ) {
                $couponInfo = $oupon_model->getCouponInfo();
                $coupon_money = $couponInfo->coupon_money;
                $this->coupon_code = $couponInfo->coupon_code;
            } else {
                $this->coupon_code = '';
            }
        }

        if ( $use_integral ) {//如果有积分抵扣
            $use_available_integral = $this->memberInfo->available_integral >= $integral_value ? $integral_value : $this->memberInfo->available_integral;
            $total_amount = $order_amount - $use_available_integral + $shipping_fee;
        } else {
            $use_available_integral = 0;
            $total_amount = $order_total_amount;
        }
        //判断代金券
        $return = array(
            'shipping_fee' => $shipping_fee,
            'order_total_price' => $order_total_price,
            'order_payable_amount' => $order_total_amount, //订单应付金额
            'order_amount' => $total_amount, //实际付款金额
            'order_integral_amount' => $use_available_integral, //订单使用积分的数量
            'commission_fee' => round( $commission_fee, 2 ),
            'commission_fee_rank' => round( $commission_fee_rank, 2 ),
            'coupon_code' => $this->coupon_code,
            'coupon_money' => $coupon_money,
            'goods_member_level' => $goods_member_level,
            'order_type' => $order_type
        );
        return $return;
    }

    /**
     * 下单成功后更新客户交易
     * @param entity_OrderInfo_base $entity_OrderInfo_base
     * @return type
     */
    private function saveCustomer( entity_OrderInfo_base $entity_OrderInfo_base )
    {
        $dao = dao_factory_base::getCustomerDao();
        $where = "item_uid={$entity_OrderInfo_base->item_uid} AND customer_uid={$entity_OrderInfo_base->uid}";
        $dao->setField( 'customer_id' );
        $dao->setWhere( $where );
        $customer_info = $dao->getInfoByWhere();
        $entity_Customer_base = new entity_Customer_base();
        $entity_Customer_base->realname = $entity_OrderInfo_base->consignee;
        $entity_Customer_base->full_address = $entity_OrderInfo_base->full_address;
        $entity_Customer_base->mobile = $entity_OrderInfo_base->mobile;
        $entity_Customer_base->weixin_id = $entity_OrderInfo_base->weixin_id;
        if ( $customer_info ) {
            $entity_Customer_base->transaction_count = new TmacDbExpr( 'transaction_count+1' );
            $entity_Customer_base->transaction_amount = new TmacDbExpr( 'transaction_amount+' . $entity_OrderInfo_base->order_amount );
            $dao->setPk( $customer_info->customer_id );
            return $dao->updateByPk( $entity_Customer_base );
        } else {
            $entity_Customer_base->item_uid = $entity_OrderInfo_base->item_uid;
            $entity_Customer_base->customer_uid = $entity_OrderInfo_base->uid;
            $entity_Customer_base->transaction_count = 1;
            $entity_Customer_base->transaction_amount = $entity_OrderInfo_base->order_amount;
            return $dao->insert( $entity_Customer_base );
        }
    }

    /**
     * 下单前的库存检测
     */
    private function checkStock()
    {
        foreach ( $this->cart_array AS $cart_info ) {
            $cart_info instanceof entity_Cart_base;
            //有sku_id的检测sku库存。没有的检测goods_id的库存
            if ( !empty( $cart_info->goods_sku_id ) ) {//goods_sku表 
                $goods_sku_info = self::getGoodsSkuInfo( $cart_info->goods_sku_id, 'stock,goods_id' );
                if ( $goods_sku_info == false ) {
                    $this->errorMessage = '商品规格不存在';
                    return false;
                }
                if ( $goods_sku_info->goods_id <> $cart_info->goods_id ) {
                    $this->errorMessage = '商品的规格不正确';
                    return false;
                }
                $stock = $goods_sku_info->stock;
            } else {
                $item_info = self::getItemInfo( $cart_info->item_id, 'goods_id,item_stock,is_delete' );
                if ( $item_info == false ) {
                    $this->errorMessage = '商品项目不存在';
                    return false;
                }
                if ( $item_info->is_delete == 1 ) {
                    $this->errorMessage = '商品项目已经下线';
                    return false;
                }
                $stock = $item_info->item_stock;
            }
            if ( $stock < $cart_info->item_number ) {
                $this->errorMessage = '您选的商品:"' . $cart_info->goods_name . '"数量已经超过商品库存了';
                return false;
            }
        }
        return true;
    }

    /**
     * 会员商品不能和其他类型的商品一起下单
     * 检测
     */
    private function checkGoodsMemberOnly()
    {
        $goods_type_member_count = 0;
        $goods_type_sale_count = 0;
        $goods_type_member_monopoly_count = 0;
        $goods_type_mall_count = 0;
        $goods_type_other = 0;
        $goods_type = 0;

        $order_type_array = Tmac::config( 'order.order_type', APP_BASE_NAME );
        $goods_type_order_type_map = Tmac::config( 'goods.goods.goods_type_order_type_map', APP_BASE_NAME );
        $order_type = 0;
        foreach ( $this->cart_array AS $key => $cart_info ) {
            $cart_info instanceof entity_Cart_base;
            if ( $key > 0 && $goods_type_order_type_map[ $cart_info->goods_type ] <> $order_type ) {
                $this->errorMessage = $order_type_array[ $order_type ] . '不能和其他类型的商品一起结算哟~';
                return false;
            }
            $order_type = $goods_type_order_type_map[ $cart_info->goods_type ];

            if ( $cart_info->goods_type == service_Goods_base::goods_type_member ) {
                $goods_type_member_count++;
            } else if ( $cart_info->goods_type == service_Goods_base::goods_type_sale ) {
                $goods_type_sale_count++;
            } else if ( $cart_info->goods_type == service_Goods_base::goods_type_member_monopoly ) {
                $goods_type_member_monopoly_count++;
            } else if ( $cart_info->goods_type == service_Goods_base::goods_type_mall ) {
                $goods_type_mall_count++;
            } else {
                $goods_type_other++;
            }
        }
        $goods_count = count( $this->cart_array );
        if ( $goods_type_member_count > 0 && $goods_type_other > 0 ) {
            $this->errorMessage = '会员商品不能和其他商品一起结算哟~';
            return false;
        }
        if ( $goods_type_sale_count > 1 ) {
            //$this->errorMessage = '特惠商品一次只让买一个~';
            //return false;
        }
        if ( $goods_type_sale_count > 0 && $goods_type_member_count > 0 ) {
            $this->errorMessage = '特惠商品不能和会员商品一起结算哟~';
            return false;
        }
        if ( $goods_type_member_monopoly_count > 0 && $goods_count > $goods_type_member_monopoly_count ) {
            $this->errorMessage = '会员专卖不能和其他商品一起结算哟~';
            return false;
        }
        if ( $goods_type_mall_count > 0 && $goods_count > $goods_type_mall_count ) {
            $this->errorMessage = '商城商品不能和其他商品一起结算哟~';
            return false;
        }
        return true;
    }

    /**
     * order专用详情
     */
    private function getItemInfo( $item_id, $field = '*' )
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setField( $field );
        $dao->setPk( $item_id );
        return $dao->getInfoByPk();
    }

    /**
     * 取商品SKU信息
     * @param type $goods_sku_id
     * @param type $field
     * @return type
     */
    private function getGoodsSkuInfo( $goods_sku_id, $field = '*' )
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setField( $field );
        $dao->setWhere( "goods_sku_id=$goods_sku_id AND is_delete=0" );
        $goods_sku_info = $dao->getInfoByWhere();
        return $goods_sku_info;
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

}
