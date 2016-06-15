<?php

/**
 * api 会员账户 管理模块 Model
 * 单个订单商品退款
 * 整个订单在未发货前退款
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Refund.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Refund_base extends service_Order_base
{

    protected $trade_vendor;
    protected $trade_no;
    protected $batch_no;
    protected $refund_id;
    protected $order_refund_id;
    protected $total_fee;
    protected $service_status;
    protected $refund_status;
    protected $return_status;
    protected $entity_OrderInfo;
    protected $entity_OrderRefund;

    function setTrade_vendor( $trade_vendor )
    {
        $this->trade_vendor = $trade_vendor;
    }

    function setTrade_no( $trade_no )
    {
        $this->trade_no = $trade_no;
    }

    function setBatch_no( $batch_no )
    {
        $this->batch_no = $batch_no;
    }

    /**
     * wxpay的退款ID
     * @param type $refund_id
     */
    function setRefund_id( $refund_id )
    {
        $this->refund_id = $refund_id;
    }

    function setOrder_refund_id( $order_refund_id )
    {
        $this->order_refund_id = $order_refund_id;
    }

    function setTotal_fee( $total_fee )
    {
        $this->total_fee = $total_fee;
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

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据交易号取支付记录数据
     * $this->trade_vendor;
     * $this->trade_no;
     * $this->getPayLogByTradeNo();
     * @return type
     */
    public function getPayLogByTradeNo()
    {
        $dao = dao_factory_base::getPayLogDao();
        $where = "trade_vendor={$this->trade_vendor} AND trade_no='{$this->trade_no}'";
        $dao->setWhere( $where );
        $dao->setOrderby( 'pay_log_id DESC' );
        return $dao->getInfoByWhere();
    }

    /**
     * 根据交易号取支付记录数据
     * $this->order_id;
     * $this->trade_vendor;     
     * $this->getPayLogByTradeNo();
     * @return type
     */
    public function getPayLogByOrderId()
    {
        $dao = dao_factory_base::getPayLogDao();
        $where = "order_id={$this->order_id} AND trade_vendor='{$this->trade_vendor}'";
        $dao->setWhere( $where );
        $dao->setOrderby( 'pay_log_id DESC' );
        return $dao->getInfoByWhere();
    }

    private function getOrderRefundInfo()
    {
        $order_refund_dao = dao_factory_base::getOrderRefundDao();
        $order_refund_dao->setPk( $this->order_refund_id );
        $order_refund_info = $order_refund_dao->getInfoByPk();
        $order_refund_info instanceof entity_OrderRefund_base;
        $this->entity_OrderRefund = $order_refund_info;
        return $order_refund_info;
    }

    /**
     * 检测商品退款的退款权限
     * $this->order_refund_id;
     * $this->uid;     
     * $this->service_status;
     * $this->refund_status;
     * $this->return_status;     
     * $this->checkOrderRefundPurview();
     * 
     */
    public function checkOrderRefundPurview( $order_refund_info )
    {
        //$order_refund_info = self::getOrderRefundInfo();
        $order_refund_info instanceof entity_OrderRefund_base;
        $this->entity_OrderRefund = $order_refund_info;

        //判断是否在退款中
        if ( $this->entity_OrderRefund->refund_ing == 1 ) {
            $this->errorMessage = '系统正在退款中，请不要重复执行';
            return false;
        }

        if ( $order_refund_info->goods_uid <> $this->uid ) {
            $this->errorMessage = '订单退款没有执行权限';
            return false;
        }

        //查询订单表
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_info_dao->setPk( $order_refund_info->order_id );
        $order_info = $order_info_dao->getInfoByPk();
        $order_info instanceof entity_OrderInfo_base;
        $this->entity_OrderInfo = $order_info;
        $this->trade_vendor = $order_info->trade_vendor;
        //判断是否付款
        if ( $order_info->pay_status <> service_Order_base::pay_status_success ) {
            $this->errorMessage = '退款的交易号平台不正确，订单未付款';
            return false;
        }

        //查询付款表            
        $this->order_id = $order_info->order_id;
        $pay_log = $this->getPayLogByOrderId();
        $pay_log instanceof entity_PayLog_base;
        if ( !$pay_log || $pay_log->pay_status <> service_Order_base::pay_status_success ) {//订单不存在 /订单状态不等于待支付的  已经支付的
            $this->errorMessage = 'notify_(订单不存在 或 订单未支付成功不能退款)';
            return false;
        }

        //订单售后记录表 买家申请售后，卖家处理售后，处理结果 order_service
        $order_service_model = new service_order_Service_base();
        $order_service_model->setIdentity( 'seller' );
        $order_service_model->setService_status( $this->service_status );
        $order_service_model->setRefund_status( $this->refund_status );
        $order_service_model->setReturn_status( $this->return_status );
        $order_service_purview = $order_service_model->checkOrderRefundServicePurview( $order_refund_info );
        if ( $order_service_purview == FALSE ) {
            $this->errorMessage = $order_service_model->getErrorMessage();
            return false;
        }
        //更新退款表中状态为系统退款中
        if ( $this->service_status == service_order_Service_base::service_status_success ) {
            $order_refund_dao = dao_factory_base::getOrderRefundDao();
            $order_refund_dao->setPk( $this->order_refund_id );

            $entity_OrderRefund_base = new entity_OrderRefund_base();
            $entity_OrderRefund_base->refund_ing = 1;
            $order_refund_dao->updateByPk( $entity_OrderRefund_base );

            $this->entity_OrderRefund->refund_ing = 1;
        }
        return $order_info;
    }

    /**
     * 订单售后 退款流程     
     * $this->trade_no;
     * $this->trade_vendor;
     * $this->batch_no;
     * $this->refund_id;     
     * $this->order_refund_id；
     * $this->total_fee;          
     * $this->executeOrderRefund();
     * @return type
     */
    public function executeOrderRefund()
    {
        if ( empty( $this->entity_OrderRefund ) ) {
            $order_refund_info = self::getOrderRefundInfo();
            if ( !$order_refund_info ) {
                $this->errorMessage = '订单退款不存在';
                return false;
            }
        }
        $this->service_status = service_order_Service_base::service_status_success;
        $this->refund_status = service_order_Service_base::refund_status_seller_agree;
        //判断是退款退货 还是 仅退款
        if ( $this->entity_OrderRefund->refund_service_status == service_order_Service_base::refund_service_status_refund ) {//仅退款
            $this->return_status = service_order_Service_base::return_status_default;
        } else if ( $this->entity_OrderRefund->refund_service_status == service_order_Service_base::refund_service_status_return ) {//退款退货
            $this->return_status = service_order_Service_base::return_status_seller_receive;
        }

        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_info_dao->getDb()->startTrans();

        //执行订单之前的未到账的状态 和 售后冻结的状态
        $this->executeOrderCurrentMoney();
        if ( $this->entity_OrderRefund->order_type == service_Order_base::order_type_member_monopoly ) {//会员专卖商品
            $member_monopoly_model = new service_order_money_MemberMonopoly_base();
            $member_monopoly_model->setBatch_no( $this->batch_no );
            $member_monopoly_model->setRefund_id( $this->refund_id );
            $member_monopoly_model->setTrade_no( $this->trade_no );
            $member_monopoly_model->setTrade_vendor( $this->trade_vendor );
            $member_monopoly_model->setTotal_fee( $this->total_fee );
            $member_monopoly_model->setEntity_OrderRefund( $this->entity_OrderRefund );
            $member_monopoly_model->refund();
        } elseif ( $this->entity_OrderRefund->order_type == service_Order_base::order_type_mall ) {
            $member_mall_model = new service_order_money_MallGoods_base();
            $member_mall_model->setBatch_no( $this->batch_no );
            $member_mall_model->setRefund_id( $this->refund_id );
            $member_mall_model->setTrade_no( $this->trade_no );
            $member_mall_model->setTrade_vendor( $this->trade_vendor );
            $member_mall_model->setTotal_fee( $this->total_fee );
            $member_mall_model->setEntity_OrderRefund( $this->entity_OrderRefund );
            $member_mall_model->refund();
        } else {
            //member_bill 会员账单表更新
            $member_bill_id = $this->insertMemberBill();
            //member_setting 表中的money相关字段更新 扣钱
            $this->updateMemberSettingMoney();
        }

        //订单售后记录表 买家申请售后，卖家处理售后，处理结果 order_service
        $order_service_model = new service_order_Service_base();

        //未发货前对整个订单进行退款
        $order_service_model->setIdentity( 'seller' );

        $order_service_model->setService_status( $this->service_status );
        $order_service_model->setRefund_status( $this->refund_status );
        $order_service_model->setReturn_status( $this->return_status );

        $order_service_model->setService_uid( $this->entity_OrderRefund->goods_uid );
        $order_service_model->setMember_bill_id( $member_bill_id );
        $modify_order_service_res = $order_service_model->modifyOrderRefundService( $this->entity_OrderRefund );

        if ( !$modify_order_service_res ) {
            $this->errorMessage = $order_service_model->getErrorMessage();
            $order_info_dao->getDb()->rollback();
            return false;
        }

        //未发货前的整个订单退款成功后。库存恢复
        if ( empty( $this->entity_OrderRefund->order_goods_id ) ) {
            $this->plusGoodsStock( $this->entity_OrderRefund->order_id );
        }

        //会员商品退款取消会员
        $this->updateGoodsMemberLevel();
        //更新order_info表中的退款状态
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->refund_status = $this->refund_status;
        $order_info_dao->setPk( $this->entity_OrderRefund->order_id );
        $order_info_dao->updateByPk( $entity_OrderInfo_base );
        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $order_info_dao->getDb()->isSuccess() && $modify_order_service_res === true ) {
            $order_info_dao->getDb()->commit();
            //TODO 发短信
            return true;
        } else {
            $order_info_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 会员商品退款。取消会员
     */
    private function updateGoodsMemberLevel()
    {
        if ( $this->entity_OrderRefund->order_type == service_Order_base::order_type_goods ) {//普通商品
            return true;
        }
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'member_level,member_level_source' );
        $dao->setPk( $this->entity_OrderRefund->uid );
        $memberInfo = $dao->getInfoByPk();
        if ( $memberInfo->member_level < service_Member_base::member_level_1 ) {
            return true;
        }
        //检测order_info表中有多少个本级的订单
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        //不能越级退.所以当前退的肯定是member中的级别
        $where = "uid={$this->entity_OrderRefund->uid}"
                . " AND order_type=" . service_Order_base::order_type_member
                . " AND goods_member_level={$this->entity_OrderRefund->goods_member_level}"
                . " AND pay_status=" . service_Order_base::pay_status_success
                . " AND goods_member_level_refund=" . service_Order_base::goods_member_level_refund_no;
        $order_info_dao->setWhere( $where );
        $member_level_count = $order_info_dao->getCountByWhere();
        if ( $memberInfo->member_level_source >= $this->entity_OrderRefund->goods_member_level ) {//如果原来的会员级别大于等于当前退款的级别
            //会员级别总数加1
            $member_level_count++;
        }
        if ( $member_level_count > 0 ) {//如果有1个以上的订单.不需要更新member表的当前状态
            return true;
        }
        $member_level = $memberInfo->member_level - 1;
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->member_level = $member_level;

        return $dao->updateByPk( $entity_Member_base );
    }

    /**
     * member_setting 表中的money相关字段更新
     * @2015-07-08增加 判断如果有分销商。分别向分销商和供应商写入账户金额变动
     * @return type
     */
    private function updateMemberSettingMoney()
    {
        $member_setting_dao = dao_factory_base::getMemberSettingDao();

        if ( $this->entity_OrderRefund->order_type == service_Order_base::order_type_goods ) {//普通商品
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $supplier_money = $this->total_fee;
            if ( !empty( $this->entity_OrderRefund->agent_uid ) && !empty( $this->entity_OrderRefund->commission_fee ) ) {//订单有直推佣金
                $supplier_money -= $this->entity_OrderRefund->commission_fee;
                //直推佣金的钱更新 开始
                $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money-' . $this->entity_OrderRefund->commission_fee );
                $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money-' . $this->entity_OrderRefund->commission_fee );
                //更新卖家的金钱 商品分销商UID
                $member_setting_dao->setPk( $this->entity_OrderRefund->agent_uid );
                $member_setting_dao->updateByPk( $entity_MemberSetting_base );
                //直推佣金的钱更新 结束
            }
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money-' . $supplier_money );
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money-' . $supplier_money );
            //更新卖家的金钱 商品分销商UID
            $member_setting_dao->setPk( $this->entity_OrderRefund->goods_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );

            //系统佣金更新，如果有的话
            //$this->updateSystemMemberSettingMoney( $this->entity_OrderRefund->commission_fee_rank );
        } else {//会员商品            
            $supplier_amount = $this->total_fee;
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            if ( !empty( $this->entity_OrderRefund->agent_uid ) ) {//订单有直推佣金
                $supplier_amount -= $this->entity_OrderRefund->commission_fee;
                //直推佣金的钱更新 开始
                $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money-' . $this->entity_OrderRefund->commission_fee );
                $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money-' . $this->entity_OrderRefund->commission_fee );
                //更新卖家的金钱 商品分销商UID
                $member_setting_dao->setPk( $this->entity_OrderRefund->agent_uid );
                $member_setting_dao->updateByPk( $entity_MemberSetting_base );
                //直推佣金的钱更新 结束
            }
            if ( !empty( $this->entity_OrderRefund->rank_uid ) ) {//订单有排位佣金
                $supplier_amount -= $this->entity_OrderRefund->commission_fee_rank;
                //排位佣金 开始
                $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money-' . $this->entity_OrderRefund->commission_fee_rank );
                $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money-' . $this->entity_OrderRefund->commission_fee_rank );
                //更新卖家的金钱 商品分销商UID
                $member_setting_dao->setPk( $this->entity_OrderRefund->rank_uid );
                $member_setting_dao->updateByPk( $entity_MemberSetting_base );
                //排位佣金 结束
            }
            //供应商的钱更新 开始                                    
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money-' . $supplier_amount );
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money-' . $supplier_amount );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $this->entity_OrderRefund->goods_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
            //供应商的钱更新 结束            
            //系统佣金更新，如果有的话
            //$this->updateSystemMemberSettingMoney( $commission_fee_rank );
        }
        return true;
    }

    /**
     * 更新系统用户的余额变动
     * 扣除余额
     */
    private function updateSystemMemberSettingMoney( $money )
    {
        $commission_fee_rank = floatval( $money );
        if ( empty( $commission_fee_rank ) ) {
            return true;
        }
        $member_setting_dao = dao_factory_base::getMemberSettingDao();

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money-' . $commission_fee_rank );
        $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money-' . $commission_fee_rank );
        //更新卖家的金钱 商品供应商UID
        $member_setting_dao->setPk( 1 );
        return $member_setting_dao->updateByPk( $entity_MemberSetting_base );
    }

    /**
     * member_bill 会员账单表更新
     * @2015-07-08增加 判断如果有分销商。分别向分销商和供应商写入账户金额变动
     * @return type
     */
    private function insertMemberBill()
    {
        $member_bill_dao = dao_factory_base::getMemberBillDao();
        $order_goods_array = unserialize( $this->entity_OrderRefund->order_goods_detail );
        if ( $this->entity_OrderRefund->order_type == service_Order_base::order_type_goods ) {//普通商品            
            $entity_MemberBill_base = new entity_MemberBill_base();
            $supplier_money = $this->total_fee; //普通商品没有佣金
            if ( !empty( $this->entity_OrderRefund->agent_uid ) && !empty( $this->entity_OrderRefund->commission_fee ) ) {//订单有直推佣金
                $supplier_money -= $this->entity_OrderRefund->commission_fee;
                //直推佣金变动日志 写入 开始
                $entity_MemberBill_base->uid = $this->entity_OrderRefund->agent_uid;
                $entity_MemberBill_base->order_id = $this->entity_OrderRefund->order_id;
                $entity_MemberBill_base->money = -$this->entity_OrderRefund->commission_fee;
                $entity_MemberBill_base->bill_type = service_Member_base::bill_type_expend;
                $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale; //代销佣金
                $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_commission_refund; //直推佣金 退款
                $entity_MemberBill_base->bill_note = "商品[直推佣金]退款";
                $entity_MemberBill_base->bill_time = $this->now;
                $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
                $entity_MemberBill_base->trade_no = $this->trade_no;
                $entity_MemberBill_base->batch_no = $this->batch_no;
                $entity_MemberBill_base->refund_id = $this->refund_id;
                $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
                $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
                $entity_MemberBill_base->bill_uid = $this->entity_OrderRefund->uid;
                $entity_MemberBill_base->bill_realname = $this->entity_OrderRefund->consignee;
                $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
                $member_bill_dao->insert( $entity_MemberBill_base );
                //直推佣金变动日志 写入 结束
            }
            $entity_MemberBill_base->uid = $this->entity_OrderRefund->goods_uid;
            $entity_MemberBill_base->order_id = $this->entity_OrderRefund->order_id;
            $entity_MemberBill_base->money = -$supplier_money;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_expend;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_business;
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_refund;
            $entity_MemberBill_base->bill_note = "申请商品退款";
            $entity_MemberBill_base->bill_time = $this->now;
            $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
            $entity_MemberBill_base->trade_no = $this->trade_no;
            $entity_MemberBill_base->batch_no = $this->batch_no;
            $entity_MemberBill_base->refund_id = $this->refund_id;
            $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
            $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
            $entity_MemberBill_base->bill_uid = $this->entity_OrderRefund->uid;
            $entity_MemberBill_base->bill_realname = $this->entity_OrderRefund->consignee;
            $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
            $member_bill_dao->insert( $entity_MemberBill_base );

            //系统佣金扣除
            //$this->insertSystemMemberBill( $this->entity_OrderRefund->commission_fee_rank, $order_goods_array[ 0 ]->goods_image_id );
        } else {//会员商品            
            $entity_MemberBill_base = new entity_MemberBill_base();
            $supplier_amount = $this->total_fee;
            if ( !empty( $this->entity_OrderRefund->agent_uid ) ) {//订单有直推佣金
                $supplier_amount -= $this->entity_OrderRefund->commission_fee;
                //直推佣金变动日志 写入 开始
                $entity_MemberBill_base->uid = $this->entity_OrderRefund->agent_uid;
                $entity_MemberBill_base->order_id = $this->entity_OrderRefund->order_id;
                $entity_MemberBill_base->money = -$this->entity_OrderRefund->commission_fee;
                $entity_MemberBill_base->bill_type = service_Member_base::bill_type_expend;
                $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale; //代销佣金
                $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_commission_refund; //直推佣金退款
                $entity_MemberBill_base->bill_note = "申请会员商品[直推佣金]退款";
                $entity_MemberBill_base->bill_time = $this->now;
                $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
                $entity_MemberBill_base->trade_no = $this->trade_no;
                $entity_MemberBill_base->batch_no = $this->batch_no;
                $entity_MemberBill_base->refund_id = $this->refund_id;
                $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
                $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
                $entity_MemberBill_base->bill_uid = $this->entity_OrderRefund->uid;
                $entity_MemberBill_base->bill_realname = $this->entity_OrderRefund->consignee;
                $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
                $member_bill_dao->insert( $entity_MemberBill_base );
                //直推佣金变动日志 写入 结束
            }
            if ( !empty( $this->entity_OrderRefund->rank_uid ) ) {//订单有排位佣金
                $supplier_amount -= $this->entity_OrderRefund->commission_fee_rank;
                //排位佣金 写入 结束
                $entity_MemberBill_base->uid = $this->entity_OrderRefund->rank_uid;
                $entity_MemberBill_base->order_id = $this->entity_OrderRefund->order_id;
                $entity_MemberBill_base->money = -$this->entity_OrderRefund->commission_fee_rank;
                $entity_MemberBill_base->bill_type = service_Member_base::bill_type_expend;
                $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale; //代销佣金
                $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_commission_refund; //排位佣金退款
                $entity_MemberBill_base->bill_note = "申请会员商品[排位佣金]退款";
                $entity_MemberBill_base->bill_time = $this->now;
                $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
                $entity_MemberBill_base->trade_no = $this->trade_no;
                $entity_MemberBill_base->batch_no = $this->batch_no;
                $entity_MemberBill_base->refund_id = $this->refund_id;
                $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
                $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
                $entity_MemberBill_base->bill_uid = $this->entity_OrderRefund->uid;
                $entity_MemberBill_base->bill_realname = $this->entity_OrderRefund->consignee;
                $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
                $member_bill_dao->insert( $entity_MemberBill_base );
                //排位佣金 写入 结束
            }
            $entity_MemberBill_base->uid = $this->entity_OrderRefund->goods_uid;
            $entity_MemberBill_base->order_id = $this->entity_OrderRefund->order_id;
            $entity_MemberBill_base->money = -$supplier_amount;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_expend;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_business;
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_refund;
            $entity_MemberBill_base->bill_note = "申请会员商品退款";
            $entity_MemberBill_base->bill_time = $this->now;
            $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
            $entity_MemberBill_base->trade_no = $this->trade_no;
            $entity_MemberBill_base->batch_no = $this->batch_no;
            $entity_MemberBill_base->refund_id = $this->refund_id;
            $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
            $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
            $entity_MemberBill_base->bill_uid = $this->entity_OrderRefund->uid;
            $entity_MemberBill_base->bill_realname = $this->entity_OrderRefund->consignee;
            $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
            $member_bill_dao->insert( $entity_MemberBill_base );
            //系统佣金
            //$this->insertSystemMemberBill( $this->entity_OrderRefund->commission_fee_rank, $order_goods_array[ 0 ]->goods_image_id );
        }
        return true;
    }

    /**
     * 插入系统佣金记录
     * @param type $money
     */
    private function insertSystemMemberBill( $money, $goods_image_id )
    {
        $commission_fee_rank = floatval( $money );
        if ( empty( $commission_fee_rank ) ) {
            return true;
        }
        $member_bill_dao = dao_factory_base::getMemberBillDao();

        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->uid = 1;
        $entity_MemberBill_base->order_id = $this->entity_OrderRefund->order_id;
        $entity_MemberBill_base->money = -$commission_fee_rank;
        $entity_MemberBill_base->bill_type = service_Member_base::bill_type_expend;
        $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_system; //系统佣金收入
        $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_commission_refund; //系统佣金退款
        $entity_MemberBill_base->bill_note = "申请商品退款,系统佣金退款";
        $entity_MemberBill_base->bill_time = $this->now;
        $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
        $entity_MemberBill_base->trade_no = $this->trade_no;
        $entity_MemberBill_base->batch_no = $this->batch_no;
        $entity_MemberBill_base->refund_id = $this->refund_id;
        $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
        $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
        $entity_MemberBill_base->bill_uid = $this->entity_OrderRefund->uid;
        $entity_MemberBill_base->bill_realname = $this->entity_OrderRefund->consignee;
        $entity_MemberBill_base->bill_image_id = $goods_image_id;
        return $member_bill_dao->insert( $entity_MemberBill_base );
    }

    /**
     * 执行订单之前的未到账的状态
     * @return boolean|int
     */
    public function executeOrderCurrentMoney()
    {
        //查询出order_finish=0 AND bill_time>={$bill_time}
        $where = 'order_id=' . $this->entity_OrderRefund->order_id;
        $dao = dao_factory_base::getMemberBillDao();
        $dao->setWhere( $where );
        $dao->setField( 'member_bill_id,uid,order_id,money,bill_type,bill_type_class,bill_expend_type,order_complete,order_finish' );
        $res = $dao->getListByWhere();
        if ( empty( $res ) ) {
            return true;
        }


        $member_setting_dao = dao_factory_base::getMemberSettingDao();

        foreach ( $res as $member_bill ) {
            //如果是提现 就跳过
            if ( $member_bill->bill_expend_type == service_Member_base::bill_expend_type_withdrawals ) {
                continue;
            }

            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money+' . $member_bill->money );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $member_bill->uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );

            //更新member_bill表中的 order_finish
            $entity_MemberBill_base = new entity_MemberBill_base();
            $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
            $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
            $dao->setPk( $member_bill->member_bill_id );
            $dao->updateByPk( $entity_MemberBill_base );
        }
        return true;
    }

    //item,goods,goods_sku表,库存更新 start        
    //member_setting 表中的money相关字段更新
    //member_bill 会员账单表更新
    //order_action 订单操作记录表
    //取order_goods表中当前的状态，并且更新为下一步状态

    /**
     * 判断订单是不是卖家自营的商品
     * @param type $item_uid
     * @param type $item_id
     * @return boolean
     */
    private function checkIsSelfGoods( $entity_OrderRefund_base )
    {
        if ( $entity_OrderRefund_base->item_uid <> $entity_OrderRefund_base->goods_uid ) {
            return false;
        } else {
            return true;
        }
    }

}
