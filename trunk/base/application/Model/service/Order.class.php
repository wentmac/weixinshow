<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Order_base extends service_Model_base
{

    /**
     * 订单类型
     * 买家订单
     */
    const order_list_type_buyer = 1;

    /**
     * 订单状态
     * 卖家订单
     */
    const order_list_type_seller = 2;

    /**
     * 订单状态
     * 买家已下单未付款     
     */
    const order_status_buyer_order_create = 1;

    /**
     * 订单状态
     * 买家已付款
     */
    const order_status_buyer_payment = 2;

    /**
     * 订单状态
     * 卖家已发货
     */
    const order_status_seller_delivery = 3;

    /**
     * 订单状态
     * 订单过期无效关闭
     * 订单完成
     * 取消订单 close_status     
     */
    const order_status_close = 4;

    /**
     * 订单状态     
     */
    const order_status_complete = 5;

    /**
     * 支付状态 支付未成功
     */
    const pay_status_unsuccess = -1;

    /**
     * 支付状态 未支付
     */
    const pay_status_unpay = 0;

    /**
     * 支付状态 支付成功
     */
    const pay_status_success = 1;

    /**
     * 支付平台
     * PC端支付
     */
    const pay_class_web = 0;

    /**
     * 支付平台
     * wap手机支付
     */
    const pay_class_wap = 1;

    /**
     * 支付宝
     */
    const trade_vendor_alipay = 1;

    /**
     * 微信支付
     */
    const trade_vendor_weixin = 2;

    /**
     * 发货状态
     * 买家付款成功后
     * 等待卖家发货
     */
    const shipping_status_seller_un_delivery = 0;

    /**
     * 发货状态
     * 买家付款成功后
     * 卖家已发货
     */
    const shipping_status_seller_delivery = 1;

    /**
     * 发货状态     
     * 卖家已发货
     * 买家已经确认收货
     */
    const shipping_status_buyer_confirm_receipt = 2;

    /**
     * 发货状态     
     * 买家已发货
     * 卖家正在备货中
     */
    const shipping_status_seller_deliverying = 3;

    /**
     * 订单类型
     * 商品订单
     */
    const order_type_goods = 0;

    /**
     * 订单类型
     * 会员商品订单
     */
    const order_type_member = 1;

    /**
     * 订单类型
     * 会员专卖商品
     * 这里不同的类型对应不同的分佣策略
     */
    const order_type_member_monopoly = 2;

    /**
     * 订单类型
     * 商城商品
     * 这里不同的类型对应不同的分佣策略
     */
    const order_type_mall = 3;

    /**
     * 订单关闭状态
     * 买家超时未支付
     */
    const close_status_overtime_unpay = 1;

    /**
     * 订单关闭状态
     * 买家取消
     */
    const close_status_cancel = 2;

    /**
     * 订单关闭状态
     * 退款
     */
    const close_status_refund = 3;

    /**
     * 收到货后申请售后收到货后最大时长
     * 7天
     */
    const return_service_max_day = 7;

    /**
     * 演示订单
     * 是
     */
    const demo_order_yes = 1;

    /**
     * 演示订单
     * 否
     */
    const demo_order_no = 0;

    /**
     * 佣金占总价的最高比例
     */
    const max_commission_rate = 0.7;

    /**
     * 会员商品正常
     */
    const goods_member_level_refund_no = 0;

    /**
     * 会员商品退款了
     */
    const goods_member_level_refund_yes = 1;

    protected $errorMessage;
    protected $goods_id;
    protected $item_id;
    protected $uid;
    protected $order_id;
    protected $orderInfo;
    protected $memberInfo;
    protected $adminPurview = false;

    /**
     * 操作完后，最新的订单状态
     * @var type 
     */
    protected $order_status_text;

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setOrderInfo( $orderInfo )
    {
        $this->orderInfo = $orderInfo;
    }

    function setItem_id( $item_id )
    {
        $this->item_id = $item_id;
    }

    function setGoods_id( $goods_id )
    {
        $this->goods_id = $goods_id;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setOrder_id( $order_id )
    {
        $this->order_id = $order_id;
    }

    function getOrder_status_text()
    {
        return $this->order_status_text;
    }

    function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
    }

    function setAdminPurview( $adminPurview )
    {
        $this->adminPurview = $adminPurview;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取订单详情
     * @param type $field
     * @return type
     */
    public function getOrderInfoById( $field = '*' )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setField( $field );
        $dao->setPk( $this->order_id );
        return $dao->getInfoByPk();
    }

    /**
     * 通过订单号取订单详情
     * @param type $field
     * @return type
     */
    public function getOrderInfoBySN( $order_sn, $field = '*' )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setField( $field );
        $dao->setWhere( "order_sn='{$order_sn}'" );
        $orderInfo = $dao->getInfoByWhere();
        if ( $orderInfo ) {
            $this->order_id = $orderInfo->order_id;
        }
        return $orderInfo;
    }

    /**
     * order专用详情
     */
    public function getItemInfoById( $field = '*' )
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setField( $field );
        $dao->setPk( $this->item_id );
        return $dao->getInfoByPk();
    }

    /**
     * 取商品SKU信息
     * @param type $goods_sku_id
     * @param type $field
     * @return type
     */
    public function getGoodsSkuById( $goods_sku_id )
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $field = 'goods_sku_id,goods_id,goods_sku,goods_sku_json,price,stock,outer_code,commission_fee,sales_volume';
        $dao->setField( $field );
        $dao->setWhere( "goods_sku_id=$goods_sku_id AND is_delete=0" );
        $goods_sku_info = $dao->getInfoByWhere();
        if ( $goods_sku_info ) {
            $goods_sku_info->sku_name = self::getSkuNameFromSkuJson( $goods_sku_info->goods_sku_json );
        }
        return $goods_sku_info;
    }

    protected function getSkuNameFromSkuJson( $goods_sku_json )
    {
        if ( empty( $goods_sku_json ) ) {
            return '';
        }
        $goods_sku_json_array = unserialize( $goods_sku_json );
        $sku_name = '';
        foreach ( $goods_sku_json_array AS $sku_name_object ) {
            $sku_name .= '[' . $sku_name_object[ 'spec_value_name' ] . ']';
        }
        return $sku_name;
    }

    /**
     * 检测item_uid针对order_id的权限
     * $this->order_id;
     * $this->checkPurviewByItemUid($item_uid);
     */
    public function checkPurviewByItemUid( $item_uid )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $this->order_id );
        $dao->setField( 'item_uid,goods_uid,pay_status,refund_status' );
        $order_info = $dao->getInfoByPk();
        if ( !$order_info ) {
            $this->errorMessage = '订单不存在';
            return false;
        }
        if ( $order_info->item_uid <> $item_uid && $order_info->goods_uid <> $item_uid ) {
            $this->errorMessage = '订单没有权限';
            return false;
        }
        return $order_info;
    }

    /**
     * 检测卖家的操作订单权限
     * $this->order_id;
     * $this->uid;
     * $this->checkSellerPriview();
     * @return type
     * @throws TmacClassException
     */
    public function checkSellerPriview()
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $this->order_id );
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
     * 处理免费供应商的订单显示
     * @param type $order_info
     */
    protected function handleFreeSupplierOrderShow( $order_info )
    {
        if ( ($this->memberInfo->member_type == service_Member_base::member_type_supplier && $this->memberInfo->member_class == service_Member_base::member_class_supplier_free) || $this->memberInfo->member_type == service_Member_base::member_type_mall ) {
            if ( isset( $order_info->mobile ) ) {
                //免费供应商，不能看到手机号全部
                $order_info->mobile = substr_replace( $order_info->mobile, '****', 3, 4 );
            }

            if ( isset( $order_info->full_address ) ) {
                $order_info->full_address = str_replace( $order_info->address, '', $order_info->full_address );

                $address_length = mb_strlen( $order_info->address, 'utf8' );
                $order_info->full_address .= str_repeat( '*', $address_length ) . '(请联系银品惠客服4008-456-090查看该订单详情)';
            }
        } else if ( $this->memberInfo->member_type == service_Member_base::member_type_seller ) {
            if ( isset( $order_info->mobile ) ) {
                //免费供应商，不能看到手机号全部
                $order_info->mobile = substr_replace( $order_info->mobile, '****', 3, 4 );
            }
        }
        $order_info->supplier_mobile = '02750243596';
        return $order_info;
    }

    /**
     * 取消订单或订单退款回加商品库存
     * 根据专家的设置（拍下减库存｜或者付款减库存）
     */
    protected function plusGoodsStock( $order_id )
    {
        if ( empty( $this->orderInfo ) ) {
            $this->order_id = $order_id;
            $this->orderInfo = $this->getOrderInfoById();
        }
        //更新商品库存
        $order_save_model = new service_order_Save_mobile();
        $order_save_model->setItem_uid( $this->orderInfo->item_uid );
        $seller_member_setting = $order_save_model->getSellerMemberSetting();
        if ( $seller_member_setting->stock_setting == service_Member_base::stock_setting_order_pay ) {
            //付款减库存
            if ( $this->orderInfo->pay_status <> service_Order_base::pay_status_success ) {
                //没有付款
                return true;
            }
        }

        $order_goods_dao = dao_factory_base::getOrderGoodsDao();
        $item_dao = dao_factory_base::getItemDao();
        $goods_dao = dao_factory_base::getGoodsDao();

        $order_goods_dao->setField( 'order_goods_id,order_id,goods_id,item_id,item_number,goods_sku_id' );
        $where = "order_id={$order_id}";
        $order_goods_dao->setWhere( $where );
        $order_goods_array = $order_goods_dao->getListByWhere();
        foreach ( $order_goods_array as $order_goods ) {
            $entity_OrderGoods_base = $order_goods;
            //更新所有goods_id的item中分销的库存和销量
            $entity_Item = new entity_Item_base();
            $entity_Item->item_stock = new TmacDbExpr( "item_stock+{$entity_OrderGoods_base->item_number}" );
            $entity_Item->sales_volume = new TmacDbExpr( "sales_volume-{$entity_OrderGoods_base->item_number}" );
            $item_dao->setWhere( "goods_id={$entity_OrderGoods_base->goods_id} AND is_self=1" );
            $item_dao->updateByWhere( $entity_Item );

            //更新goods表中的原商品的库存和销量
            $entity_Goods_base = new entity_Goods_base();
            $entity_Goods_base->goods_stock = new TmacDbExpr( 'goods_stock+' . $entity_OrderGoods_base->item_number );
            $entity_Goods_base->sales_volume = new TmacDbExpr( 'sales_volume-' . $entity_OrderGoods_base->item_number );
            $goods_dao->setPk( $entity_OrderGoods_base->goods_id );
            $goods_dao->updateByPk( $entity_Goods_base );

            if ( !empty( $entity_OrderGoods_base->goods_sku_id ) ) {//goods_sku表 
                $goods_sku_dao = dao_factory_base::getGoodsSkuDao();
                $entity_GoodsSku_base = new entity_GoodsSku_base();
                $entity_GoodsSku_base->stock = new TmacDbExpr( 'stock+' . $entity_OrderGoods_base->item_number );
                $entity_GoodsSku_base->sales_volume = new TmacDbExpr( 'sales_volume-' . $entity_OrderGoods_base->item_number );
                $goods_sku_dao->setPk( $entity_OrderGoods_base->goods_sku_id );
                $goods_sku_dao->updateByPk( $entity_GoodsSku_base );
            }
        }
        return true;
    }

}
