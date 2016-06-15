<?php

/**
 * 买家订单处理的类 
 * 取消订单 
 * 确认收货
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: MemberMonopoly.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_money_MemberMonopoly_base extends service_Order_base
{

    protected $trade_vendor;
    protected $trade_no;
    protected $agent_array;
    protected $batch_no;
    protected $refund_id;
    protected $entity_OrderRefund;
    protected $total_fee;

    function setTrade_vendor( $trade_vendor )
    {
        $this->trade_vendor = $trade_vendor;
    }

    function setTrade_no( $trade_no )
    {
        $this->trade_no = $trade_no;
    }

    function setEntity_OrderRefund( $entity_OrderRefund )
    {
        $this->entity_OrderRefund = $entity_OrderRefund;
    }

    function setBatch_no( $batch_no )
    {
        $this->batch_no = $batch_no;
    }

    function setRefund_id( $refund_id )
    {
        $this->refund_id = $refund_id;
    }

    function setTotal_fee( $total_fee )
    {
        $this->total_fee = $total_fee;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 付款时更新金额
     * $model->setOrderInfo( $this->entity_OrderInfo );
     * $model->setTrade_no( $this->trade_no );
     * $model->setTrade_vendor( $this->trade_vendor );
     * $model->payment();
     */
    public function payment()
    {
        $this->getAgentArray();
        $this->updateMemberSettingMoney();
        $this->insertMemberBill();
        return true;
    }

    /**
     * 扣款时更新金额
     * $this->batch_no;
     * $this->refund_id;
     * $this->trade_no;
     * $this->trade_vendor;
     * $this->refund();
     */
    public function refund()
    {
        $this->refundMemberSettingMoney();
        $this->refundMemberBill();
        return true;
    }

    /**
     * 计算直推奖佣金
     * 根据东家的会员级别来确认
     */
    private function getCommissionFee()
    {
        //直推佣金        
        $rate = ($this->agent_array[ 'agent_uid_member_level' ] - 1) * 0.1;
        $commission_fee = $this->orderInfo->commission_fee + round( $this->orderInfo->commission_fee * $rate, 2 );
        return $commission_fee;
    }

    /**
     * member_setting 表中的money相关字段更新
     * 直推奖=>东家，如果东家是东家是lv3就是  = 排位奖+（排位奖*30%); |会员才给
     * 排位奖=>东家的东家 | 会员才给
     * 
     * @return type
     */
    private function updateMemberSettingMoney()
    {
        $entity_OrderInfo_base = $this->orderInfo;
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $entity_MemberSetting_base = new entity_MemberSetting_base();
        //判断上家是不是会员
        if ( empty( $this->agent_array[ 'agent_uid' ] ) || $this->agent_array[ 'agent_uid_member_level' ] == 0 ) {
            $commission_fee = 0;
        } else {
            //是会员 给直接佣金
            $commission_fee = $this->getCommissionFee();
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee );
            $member_setting_dao->setPk( $this->agent_array[ 'agent_uid' ] );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        }
        //判断给不给排位佣金
        if ( empty( $this->agent_array[ 'agent_agent_uid' ] ) || $this->agent_array[ 'agent_agent_uid_member_level' ] == 0 ) {
            $commission_fee_rank = 0;
        } else {
            $commission_fee_rank = $entity_OrderInfo_base->commission_fee_rank; //排位佣金    
            //供应商的钱更新开始            
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee_rank );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $this->agent_array[ 'agent_agent_uid' ] );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        }
        $commission_fee_rank_amount = $entity_OrderInfo_base->order_amount - $commission_fee - $commission_fee_rank;
        //系统佣金更新，如果有的话
        $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee_rank_amount );
        //更新卖家的金钱 商品供应商UID
        $member_setting_dao->setPk( $entity_OrderInfo_base->goods_uid );
        $member_setting_dao->updateByPk( $entity_MemberSetting_base );

        //更新order_info表
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->commission_fee = $commission_fee;
        $entity_OrderInfo_base->commission_fee_rank = $commission_fee_rank;
        $entity_OrderInfo_base->agent_uid = $this->agent_array[ 'agent_uid' ];
        $entity_OrderInfo_base->rank_uid = $this->agent_array[ 'agent_agent_uid' ];
        $order_info_dao->setPk( $this->orderInfo->order_id );
        $order_info_dao->updateByPk( $entity_OrderInfo_base );
        $this->orderInfo->commission_fee = $commission_fee;
        return true;
    }

    /**
     * member_bill 会员账单表更新     
     * @return type
     */
    private function insertMemberBill()
    {
        $entity_OrderInfo_base = $this->orderInfo;
        $member_bill_dao = dao_factory_base::getMemberBillDao();

        $order_goods_array = unserialize( $entity_OrderInfo_base->order_goods_detail );
        $goods_count = count( $order_goods_array );


        $bill_note = "购买{$goods_count}件商品|会员专卖商品";
        $order_complete = service_Member_base::order_complete_no;
        $order_finish = service_Member_base::order_finish_no;
        $entity_MemberBill_base = new entity_MemberBill_base();

        //判断上家是不是会员
        if ( empty( $this->agent_array[ 'agent_uid' ] ) || $this->agent_array[ 'agent_uid_member_level' ] == 0 ) {
            $commission_fee = 0;
        } else {
            //是会员 给直接佣金
            $commission_fee = $entity_OrderInfo_base->commission_fee; //直推佣金            
            //分销商金额变动日志 写入 开始
            $entity_MemberBill_base = new entity_MemberBill_base();
            $entity_MemberBill_base->uid = $this->agent_array[ 'agent_uid' ];
            $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
            $entity_MemberBill_base->money = $commission_fee;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale;
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
            $entity_MemberBill_base->bill_note = $bill_note . "(会员专卖商品直推佣金)";
            $entity_MemberBill_base->bill_time = $this->now;
            $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
            $entity_MemberBill_base->trade_no = $this->trade_no;
            $entity_MemberBill_base->order_complete = $order_complete;
            $entity_MemberBill_base->order_finish = $order_finish;
            $entity_MemberBill_base->bill_uid = $entity_OrderInfo_base->uid;
            $entity_MemberBill_base->bill_realname = $entity_OrderInfo_base->consignee;
            $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
            $member_bill_dao->insert( $entity_MemberBill_base );
            //分销商金额变动日志 写入 结束
        }
        //判断给不给排位佣金
        if ( empty( $this->agent_array[ 'agent_agent_uid' ] ) || $this->agent_array[ 'agent_agent_uid_member_level' ] == 0 ) {
            $commission_fee_rank = 0;
        } else {
            $commission_fee_rank = $entity_OrderInfo_base->commission_fee_rank; //排位佣金    
            //供应商金额变动日志 写入 开始            
            $entity_MemberBill_base->uid = $this->agent_array[ 'agent_agent_uid' ];
            $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
            $entity_MemberBill_base->money = $commission_fee_rank;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale; //代销
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
            $entity_MemberBill_base->bill_note = $bill_note . "(会员专卖商品第二级佣金)";
            $entity_MemberBill_base->bill_time = $this->now;
            $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
            $entity_MemberBill_base->trade_no = $this->trade_no;
            $entity_MemberBill_base->order_complete = $order_complete;
            $entity_MemberBill_base->order_finish = $order_finish;
            $entity_MemberBill_base->bill_uid = $entity_OrderInfo_base->uid;
            $entity_MemberBill_base->bill_realname = $entity_OrderInfo_base->consignee;
            $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
            $member_bill_dao->insert( $entity_MemberBill_base );
            //供应商金额变动日志 写入 结束       
        }
        $system_amount = $entity_OrderInfo_base->order_amount - $commission_fee - $commission_fee_rank;
        //供应商金额变动日志 写入 开始            
        $entity_MemberBill_base->uid = $entity_OrderInfo_base->goods_uid;
        $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
        $entity_MemberBill_base->money = $system_amount;
        $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
        $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_business; //自营
        $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
        $entity_MemberBill_base->bill_note = $bill_note . "(订单总金额￥{$entity_OrderInfo_base->order_amount})";
        $entity_MemberBill_base->bill_time = $this->now;
        $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
        $entity_MemberBill_base->trade_no = $this->trade_no;
        $entity_MemberBill_base->order_complete = $order_complete;
        $entity_MemberBill_base->order_finish = $order_finish;
        $entity_MemberBill_base->bill_uid = $entity_OrderInfo_base->uid;
        $entity_MemberBill_base->bill_realname = $entity_OrderInfo_base->consignee;
        $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
        $member_bill_dao->insert( $entity_MemberBill_base );
        return true;
    }

    /**
     * 退款时扣款 
     * member_setting表的money字段
     * 
     */
    private function refundMemberSettingMoney()
    {
        $member_setting_dao = dao_factory_base::getMemberSettingDao();

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

        return true;
    }

    /**
     * 退款时扣款流水记录
     * member_setting表的money字段
     * 
     */
    private function refundMemberBill()
    {
        $member_bill_dao = dao_factory_base::getMemberBillDao();
        $order_goods_array = unserialize( $this->entity_OrderRefund->order_goods_detail );

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
            $entity_MemberBill_base->bill_note = "申请会员专卖商品[直推佣金]退款";
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
            $entity_MemberBill_base->bill_note = "申请会员专卖商品[第二级佣金]退款";
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
        $entity_MemberBill_base->bill_note = "申请会员专卖商品退款";
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

        return true;
    }

    private function getAgentArray()
    {
        $uid = $this->orderInfo->uid;
        $memberInfo = $this->getMemberInfo( $uid );
        $agent_array = array(
            'agent_uid' => 0,
            'agent_uid_member_level' => 0,
            'agent_agent_uid' => 0,
            'agent_agent_uid_member_level' => 0
        );
        if ( $memberInfo->member_level > 0 ) {//如果自己是会员，直推奖给自己。排位奖给东家
            $agent_array[ 'agent_uid' ] = $memberInfo->uid;
            $agent_array[ 'agent_uid_member_level' ] = $memberInfo->member_level;
            if ( $memberInfo->agent_uid > 0 ) {
                $memberInfo = $this->getMemberInfo( $memberInfo->agent_uid );
                $agent_array[ 'agent_agent_uid' ] = $memberInfo->uid;
                $agent_array[ 'agent_agent_uid_member_level' ] = $memberInfo->member_level;
            }
        } else {//如果自己不是会员，直推奖给东家。排位奖给东家的东家            
            if ( $memberInfo->agent_uid > 0 ) {
                $memberInfo = $this->getMemberInfo( $memberInfo->agent_uid );
                $agent_array[ 'agent_uid' ] = $memberInfo->uid;
                $agent_array[ 'agent_uid_member_level' ] = $memberInfo->member_level;
                if ( $memberInfo->agent_uid > 0 ) {
                    $memberInfo = $this->getMemberInfo( $memberInfo->agent_uid );
                    $agent_array[ 'agent_agent_uid' ] = $memberInfo->uid;
                    $agent_array[ 'agent_agent_uid_member_level' ] = $memberInfo->member_level;
                }
            }
        }

        $this->agent_array = $agent_array;
        return $agent_array;
    }

    private function getMemberInfo( $uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,agent_uid,member_level' );
        $dao->setPk( $uid );
        $memberInfo = $dao->getInfoByPk();
        return $memberInfo;
    }

}
