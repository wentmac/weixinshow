<?php

/**
 * api 会员账户 管理模块 Model
 * 单个订单商品退款
 * 整个订单在未发货前退款
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: RefundSave.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_RefundSave_base extends service_Order_base
{

    protected $order_sn;
    protected $order_goods_id;
    protected $money;
    private $order_info;
    private $order_goods_info;

    function setOrder_sn( $order_sn )
    {
        $this->order_sn = $order_sn;
    }

    function setOrder_goods_id( $order_goods_id )
    {
        $this->order_goods_id = $order_goods_id;
    }

    function setMoney( $money )
    {
        $this->money = $money;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测item_uid针对order_id的权限
     * $this->order_id;
     * $this->checkPurviewByItemUid($item_uid);
     */
    public function checkOrderInfoPurviewByUid( $order_id )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $order_id );
        $order_info = $dao->getInfoByPk();
        if ( !$order_info ) {
            throw new TmacClassException( '订单不存在' );
        }
        if ( $order_info->uid <> $this->uid && $this->adminPurview === FALSE ) {
            throw new TmacClassException( '订单没有权限' );
        }
        return $order_info;
    }

    /**
     * 检测item_uid针对order_id的权限
     * $this->order_sn;
     * $this->checkPurviewByItemUid($item_uid);
     */
    public function checkOrderInfoPurviewByOrderSN( $order_sn )
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setWhere( "order_sn='{$order_sn}'" );
        $order_info = $dao->getInfoByWhere();
        if ( !$order_info ) {
            throw new TmacClassException( '订单不存在' );
        }
        if ( $order_info->uid <> $this->uid && $this->adminPurview === false ) {
            throw new TmacClassException( '订单没有权限' );
        }
        return $order_info;
    }

    /**
     * 取订单商品商品
     * @param type $field
     * @return type
     * @throws TmacClassException
     */
    protected function getOrderGoodsInfo( $field = '*' )
    {
        $dao = dao_factory_base::getOrderGoodsDao();
        $dao->setPk( $this->order_goods_id );
        $dao->setField( $field );
        $order_goods_info = $dao->getInfoByPk();
        if ( !$order_goods_info ) {
            throw new TmacClassException( '订单商品不存在' );
        }
        return $order_goods_info;
    }

    /**
     * 检测买家申请退款时的权限
     * $this->order_sn;
     * $this->order_goods_id;
     * $this->uid;
     * $this->money;
     * $this->checkPurviewByBuyer();
     */
    public function checkPurviewByBuyer()
    {
        //检测$order_id或$order_goods_id;
        //判断money金额合法
        //坑爹 货不能退款少于1分钱
        if ( $this->money < 0.01 ) {
            throw new TmacClassException( '亲，退款金额怎么能少于1分呢' );
        }
        if ( !empty( $this->order_goods_id ) ) {//单个订单商品退款
            $order_goods_info = $this->order_goods_info = $this->getOrderGoodsInfo();
            $order_info = $this->checkOrderInfoPurviewByUid( $order_goods_info->order_id );
            if ( !$order_info ) {
                throw new TmacClassException( '订单不存在' );
            }
            if ( $order_info->demo_order == service_Order_base::demo_order_yes ) {
                //throw new TmacClassException( '演示订单不能申请退款' );
            }
            if ( $order_info->order_status <> service_Order_base::order_status_complete ) {
                throw new TmacClassException( '只有确认收货的订单商品才能申请退款' );
            }
            //只能付款的订单才能申请退款            
            if ( $order_info->pay_status <> service_Order_base::pay_status_success ) {
                throw new TmacClassException( '只付款成功的订单才能申请退款' );
            }
            $money = $order_goods_info->item_price * $order_goods_info->item_number + $order_info->shipping_fee - $order_info->coupon_money - $order_goods_info->order_integral_amount;
            if ( $this->money > $money ) {
                throw new TmacClassException( '退款金额不能大于订单商品金额' );
            }
            //判断订单结束时间 
            $expired_time = 86400 * 15;
            if ( ($this->now - $order_info->confirm_time) > $expired_time ) {//付款后15天内不能申请退款
                throw new TmacClassException( '确认收货后超过15天不能退款' );
            }
        } else {//未收到货，整个订单进行退款
            $order_info = $this->checkOrderInfoPurviewByOrderSN( $this->order_sn );
            if ( !$order_info ) {
                throw new TmacClassException( '订单不存在' );
            }
            if ( $order_info->demo_order == service_Order_base::demo_order_yes ) {
                //throw new TmacClassException( '演示订单不能申请退款' );
            }
            //只能付款的订单才能申请退款            
            if ( $order_info->pay_status <> service_Order_base::pay_status_success ) {
                throw new TmacClassException( '只付款成功的订单才能申请退款' );
            }
            //只能未收到货时才能进行整个订单申请退款
            if ( $order_info->order_status <> service_Order_base::order_status_buyer_payment ) {
                throw new TmacClassException( '只有卖家未发货时才能申请整个订单的退款' );
            }
            if ( $this->money > $order_info->order_amount ) {
                throw new TmacClassException( '退款金额不能大于订单金额' );
            }
            $this->order_goods_id = 0;
        }
        $this->order_info = $order_info;
        //判断会员商品退款规则       退级只能一级一级的退,想退三级 就要先退四缘的
        $this->checkOrderMemberPriview();
        $this->checkRepeat();
        return $order_info;
    }

    /**
     * 会员商品退款权限检测
     * 退级只能一级一级的退,想退三级 就要先退四缘的     
     */
    private function checkOrderMemberPriview()
    {
        if ( $this->order_info->order_type <> service_Order_base::order_type_member ) {
            return true;
        }
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'member_level,member_level_source' );
        $dao->setPk( $this->order_info->uid );
        $memberInfo = $dao->getInfoByPk();
        if ( $memberInfo->member_level < service_Member_base::member_level_1 ) {
            throw new TmacClassException( '会员级别和会员商品不匹配' );
        }

        //检测order_info表中有多少个本级的订单
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        //不能越级退.所以当前退的肯定是member中的级别
        $where = "uid={$this->order_info->uid}"
                . " AND order_type=" . service_Order_base::order_type_member
                . " AND goods_member_level={$this->order_info->goods_member_level}"
                . " AND pay_status=" . service_Order_base::pay_status_success
                . " AND goods_member_level_refund=" . service_Order_base::goods_member_level_refund_no;
        $order_info_dao->setWhere( $where );
        $member_level_count = $order_info_dao->getCountByWhere();
        if ( $memberInfo->member_level_source >= $this->order_info->goods_member_level ) {//如果原来的会员级别大于等于当前退款的级别
            //会员级别总数加1
            $member_level_count++;
        }
        if ( $member_level_count > 1 ) {//如果有2个以上的订单.不需要更新member表的当前状态
            return true;
        }
        //取当前退款的会员商品级别
        if ( $this->order_info->goods_member_level <> $memberInfo->member_level ) {
            throw new TmacClassException( '会员商品只能一级一级的退，请先退掉lv' . $memberInfo->member_level );
        }
        return true;
    }

    private function checkRepeat()
    {
        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_refund_dao->setField( 'order_refund_id' );
        $order_refund_dao->setWhere( "order_id={$this->order_info->order_id} AND order_goods_id={$this->order_goods_id}" );
        $res = $order_refund_dao->getInfoByWhere();
        if ( $res ) {
            throw new TmacClassException( '订单已经申请过了，不能重复申请售后维权哟~' );
        }
        return true;
    }

    /**
     * 创建新的退款维权
     */
    public function createOrderRefund( entity_OrderRefund_base $entity_OrderRefund_base )
    {
        $entity_OrderRefund_base->order_id = $this->order_info->order_id;
        if ( empty( $this->order_goods_id ) ) {//卖家未发货前 整个订单的退款
            $entity_OrderRefund_base->order_goods_detail = $this->order_info->order_goods_detail;
            $entity_OrderRefund_base->commission_fee = $this->order_info->commission_fee; //退款的佣金是全部订单所有。 只要有退款，所有的佣金都扣除
            $entity_OrderRefund_base->commission_fee_rank = $this->order_info->commission_fee_rank; //退款 系统的佣金。 只要有退款，所有的佣金都扣除
            $order_type = $this->order_info->order_type;
        } else {//收到货后，单个商品的申请退款
            $order_goods_detail_array = array();
            $entity_OrderGoods_base = new stdClass();
            $entity_OrderGoods_base->item_id = $this->order_goods_info->item_id;
            $entity_OrderGoods_base->item_name = $this->order_goods_info->item_name;
            $entity_OrderGoods_base->item_number = $this->order_goods_info->item_number;
            $entity_OrderGoods_base->item_price = $this->order_goods_info->item_price;
            $entity_OrderGoods_base->goods_image_id = $this->order_goods_info->goods_image_id;
            $entity_OrderGoods_base->goods_sku_name = $this->order_goods_info->goods_sku_name;
            $order_goods_detail_array[] = $entity_OrderGoods_base;
            $entity_OrderRefund_base->order_goods_detail = serialize( $order_goods_detail_array );
            $entity_OrderRefund_base->commission_fee = $this->order_goods_info->commission_fee; //退款的 分销商的 佣金是单个商品佣金X商品总数的退款佣金。  只要有退款，所有的佣金都扣除
            $entity_OrderRefund_base->commission_fee_rank = $this->order_goods_info->commission_fee_rank; //退款的系统佣金是单个商品佣金X商品总数的退款佣金。 只要有退款，所有的佣金都扣除
            $goods_type_order_type_map = Tmac::config( 'goods.goods.goods_type_order_type_map', APP_BASE_NAME );
            $order_type = $goods_type_order_type_map[ $this->order_goods_info->goods_type ];
        }
        $order_refund_array = Tmac::config( 'order.order_refund', APP_BASE_NAME );

        $entity_OrderRefund_base->goods_member_level = $this->order_info->goods_member_level;
        $entity_OrderRefund_base->agent_uid = $this->order_info->agent_uid;
        $entity_OrderRefund_base->rank_uid = $this->order_info->rank_uid;
        $entity_OrderRefund_base->order_sn = $this->order_info->order_sn;
        $entity_OrderRefund_base->item_uid = $this->order_info->item_uid;
        $entity_OrderRefund_base->item_mobile = $this->order_info->item_mobile;
        $entity_OrderRefund_base->shop_name = $this->order_info->shop_name;
        $entity_OrderRefund_base->goods_uid = $this->order_info->goods_uid;
        $entity_OrderRefund_base->supplier_mobile = $this->order_info->supplier_mobile;
        $entity_OrderRefund_base->consignee = $this->order_info->consignee;
        $entity_OrderRefund_base->mobile = $this->order_info->mobile;
        $entity_OrderRefund_base->order_type = $order_type;
        $entity_OrderRefund_base->weixin_id = $this->order_info->weixin_id;
        $entity_OrderRefund_base->item_weixin_id = $this->order_info->item_weixin_id;
        //处理状态（1：等待卖家处理｜2：等待买家处理｜3：等待银品惠客服处理｜4：维权处理完成｜5：维权结束/取消）
        $entity_OrderRefund_base->service_status = service_order_Service_base::service_status_waiting_seller_confirm;
        //收到货后单个商品申请的退款状态（ 0：默认状态｜ 1：买家申请退款｜ 2：卖家同意退款｜ 3：卖家不同意退款 ）
        $entity_OrderRefund_base->refund_status = service_order_Service_base::refund_status_buyer_return;
        $entity_OrderRefund_base->service_level = 0;
        $entity_OrderRefund_base->service_note = $order_refund_array[ 'refund_service_status' ][ $entity_OrderRefund_base->refund_service_status ] . '|'
                . $order_refund_array[ 'refund_service_reason' ][ $entity_OrderRefund_base->refund_service_reason ];

        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_service_dao = dao_factory_base::getOrderServiceDao();
        $member_bill_dao = dao_factory_base::getMemberBillDao();

        $order_refund_dao->getDb()->startTrans();
        $order_refund_id = $order_refund_dao->insert( $entity_OrderRefund_base );
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->refund_status = service_order_Service_base::refund_status_buyer_return; //have_return_service字段来区分是否 发货前的退款

        if ( !empty( $this->order_goods_id ) ) {
            $entity_OrderGoods_base = new entity_OrderGoods_base();
            $entity_OrderGoods_base->order_refund_id = $order_refund_id;
            //确认收货后，订单完成后，有售后服务
            $entity_OrderInfo_base->have_return_service = 1;

            $order_goods_dao = dao_factory_base::getOrderGoodsDao();
            $order_goods_dao->setPk( $this->order_goods_id );
            $order_goods_dao->updateByPk( $entity_OrderGoods_base );
        } else {
            //在未发货前有退款申请，把订单状态改成 结束/关闭
            $entity_OrderInfo_base->order_status = service_Order_base::order_status_close;
            $entity_OrderInfo_base->close_status = service_Order_base::close_status_refund;
            $entity_OrderInfo_base->order_refund_id = $order_refund_id; //未发货退款申请的 售后ID
        }

        //如果是会员商品，会员商品退款状态改成正在退。当用户取消退款时再改回来
        if ( $entity_OrderRefund_base->order_type == service_Order_base::order_type_member ) {
            $entity_OrderInfo_base->goods_member_level_refund = service_Order_base::goods_member_level_refund_yes;
        }
        $order_info_dao->setPk( $this->order_info->order_id );
        $order_info_dao->updateByPk( $entity_OrderInfo_base );

        //order_service表
        $entity_OrderService_base = new entity_OrderService_base();
        $entity_OrderService_base->order_refund_id = $order_refund_id;
        $entity_OrderService_base->order_goods_id = $this->order_goods_id;
        $entity_OrderService_base->order_id = $this->order_info->order_id;
        $entity_OrderService_base->money = $this->money;
        $entity_OrderService_base->service_status = $entity_OrderRefund_base->service_status;
        $entity_OrderService_base->refund_status = $entity_OrderRefund_base->refund_status;
        $entity_OrderService_base->return_status = 0;
        $entity_OrderService_base->service_note = $order_refund_array[ 'refund_service_status' ][ $entity_OrderRefund_base->refund_service_status ] . '|'
                . $order_refund_array[ 'refund_service_reason' ][ $entity_OrderRefund_base->refund_service_reason ];
        $entity_OrderService_base->service_uid = $this->uid;
        $entity_OrderService_base->service_username = '买家';
        $entity_OrderService_base->service_time = $this->now;

        $order_service_dao->insert( $entity_OrderService_base );

        //member_bill表中的金额冻结，order_compleate状态改成售后问题.等同意退款或拒绝退款时再改回 order_compleate_yes
        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->order_complete = service_Member_base::order_complete_refund;
        $member_bill_dao->setWhere( 'order_id=' . $this->order_info->order_id );
        $member_bill_dao->updateByWhere( $entity_MemberBill_base );

        //TODO 090 给卖家push,app,push
        //消息 push
        $push_message_model = new service_PushMessage_base();
        $push_message_model->setMessageType( service_PushMessage_base::message_type_refund );
        $push_message_model->setOrderRefund( $entity_OrderRefund_base );
        $push_message_model->push();

        if ( $order_refund_dao->getDb()->isSuccess() ) {
            $order_refund_dao->getDb()->commit();
            return true;
        } else {
            $order_refund_dao->getDb()->rollback();
            return false;
        }
    }

}
