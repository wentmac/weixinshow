<?php

/**
 * 买家订单处理的类 
 * 取消订单 
 * 确认收货
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: MallGoods.class.php 360 2016-06-09 16:23:43Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_money_MallGoods_base extends service_Order_base
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
        if ( empty( $this->agent_array[ 'agent_uid' ] ) || $this->agent_array[ 'commission_fee' ] == 0 ) {
            $commission_fee = 0;
        } else {
            //是会员 给直接佣金
            $commission_fee = $this->agent_array[ 'commission_fee' ];
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee );
            $member_setting_dao->setPk( $this->agent_array[ 'agent_uid' ] );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        }
        //判断给不给排位佣金
        if ( empty( $this->agent_array[ 'rank_uid' ] ) || $this->agent_array[ 'commission_fee_rank' ] == 0 ) {
            $commission_fee_rank = 0;
        } else {
            $commission_fee_rank = $this->agent_array[ 'commission_fee_rank' ]; //排位佣金    
            //供应商的钱更新开始            
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee_rank );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $this->agent_array[ 'rank_uid' ] );
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
        $entity_OrderInfo_base->rank_uid = $this->agent_array[ 'rank_uid' ];
        $order_info_dao->setPk( $this->orderInfo->order_id );
        $order_info_dao->updateByPk( $entity_OrderInfo_base );
        $this->orderInfo->commission_fee = $commission_fee;
        $this->orderInfo->commission_fee_rank = $commission_fee_rank;
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


        $bill_note = "购买{$goods_count}件商品|商城商品";
        $order_complete = service_Member_base::order_complete_no;
        $order_finish = service_Member_base::order_finish_no;
        $entity_MemberBill_base = new entity_MemberBill_base();

        //判断上家是不是会员
        if ( empty( $this->agent_array[ 'agent_uid' ] ) || $this->agent_array[ 'commission_fee' ] == 0 ) {
            $commission_fee = 0;
        } else {
            //是会员 给直接佣金
            $commission_fee = $this->agent_array[ 'commission_fee' ]; //直推佣金            
            //分销商金额变动日志 写入 开始
            $entity_MemberBill_base = new entity_MemberBill_base();
            $entity_MemberBill_base->uid = $this->agent_array[ 'agent_uid' ];
            $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
            $entity_MemberBill_base->money = $commission_fee;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale;
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
            $entity_MemberBill_base->bill_note = $bill_note . "(商城商品直推佣金)";
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
        if ( empty( $this->agent_array[ 'rank_uid' ] ) || $this->agent_array[ 'commission_fee_rank' ] == 0 ) {
            $commission_fee_rank = 0;
        } else {
            $commission_fee_rank = $this->agent_array[ 'commission_fee_rank' ]; //排位佣金    
            //供应商金额变动日志 写入 开始            
            $entity_MemberBill_base->uid = $this->agent_array[ 'rank_uid' ];
            $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
            $entity_MemberBill_base->money = $commission_fee_rank;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale; //代销
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
            $entity_MemberBill_base->bill_note = $bill_note . "(商城商品第二级佣金)";
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
            $entity_MemberBill_base->bill_note = "申请商城商品[直推佣金]退款";
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
            $entity_MemberBill_base->bill_note = "申请商城商品[第二级佣金]退款";
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
        $entity_MemberBill_base->bill_note = "申请商城商品退款";
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
            'commission_fee' => 0,
            'rank_uid' => 0,
            'commission_fee_rank' => 0
        );
        //如果实付金额为0，有使用积分抵扣
        if ( $this->orderInfo->order_amount == 0 ) {
            $this->agent_array = $agent_array;
            return $agent_array;
        }
        if ( $this->orderInfo->order_integral_amount > 0 ) {
            //使用过积分
            $order_total_price = $this->orderInfo->order_amount - $this->orderInfo->shipping_fee;//积分分润要减去运费
        } else {
            //没有用过积分
            $order_total_price = $this->orderInfo->order_total_price;
        }
        $goods_offer_rate_array = Tmac::config( 'goods.goods.goods_mall_offer_rate', APP_BASE_NAME );
        if ( $memberInfo->member_level > 0 ) {//如果自己是会员，没有直推奖            
            if ( $memberInfo->agent_uid > 0 ) {
                $buyerMemberInfo = $memberInfo; //买家
                $agentMemberInfo = $this->getMemberInfo( $buyerMemberInfo->agent_uid ); //买家的东家
                //判断东家级别是否大于他
                if ( $agentMemberInfo->member_level > $buyerMemberInfo->member_level ) {
                    //东家的级别大于等于买家。东家拿排位奖 级差+固定 （一个产品卖10元，东家的东家是lv9打6折，东家是lv1打7折 ）所以这个差价就是 7-6=1
                    $rate = ($goods_offer_rate_array[ $buyerMemberInfo->member_level ] - $goods_offer_rate_array[ $agentMemberInfo->member_level ]) / 10;
                    //优惠差值
                    $commission_fee_rank_diff = round( $order_total_price * $rate, 2 );
                    $agent_array[ 'rank_uid' ] = $agentMemberInfo->uid;
                    $agent_array[ 'commission_fee_rank' ] = $this->orderInfo->commission_fee_rank + $commission_fee_rank_diff;
                } else if ( $agentMemberInfo->member_level == $buyerMemberInfo->member_level ) {
                    //级差相等就是纯排位奖。
                    $agent_array[ 'rank_uid' ] = $agentMemberInfo->uid;
                    $agent_array[ 'commission_fee_rank' ] = $this->orderInfo->commission_fee_rank;
                }
            }
        } else {//自己不是会员
            if ( $memberInfo->agent_uid > 0 ) {
                $buyerMemberInfo = $memberInfo;
                $agentMemberInfo = $this->getMemberInfo( $memberInfo->agent_uid );
                if ( $agentMemberInfo->member_level > 0 ) {//东家是会员
                    //优惠差值
                    $rate = (10 - $goods_offer_rate_array[ $agentMemberInfo->member_level ]) / 10;
                    //直推奖=纯差价（一个产品卖10元，东家是lv9打6折）所以这个差价就是 原价-折后价
                    $commission_fee_diff = round( $order_total_price * $rate, 2 );
                    $agent_array[ 'agent_uid' ] = $agentMemberInfo->uid;
                    $agent_array[ 'commission_fee' ] = $commission_fee_diff;
                    if ( $agentMemberInfo->agent_uid > 0 ) {//排位奖给东家的东家
                        $agentAgentMemberInfo = $this->getMemberInfo( $agentMemberInfo->agent_uid ); //东家的东家
                        if ( $agentAgentMemberInfo->member_level > $agentMemberInfo->member_level ) {//东家的东家会员级别大于东家的级别
                            //东家的级别大于等于买家。东家拿排位奖 级差+固定 （一个产品卖10元，东家的东家是lv9打6折，东家是lv1打7折 ）所以这个差价就是 7-6=1
                            $rate = ($goods_offer_rate_array[ $agentMemberInfo->member_level ] - $goods_offer_rate_array[ $agentAgentMemberInfo->member_level ]) / 10;
                            //优惠差值
                            $commission_fee_rank_diff = round( $order_total_price * $rate, 2 );
                            $agent_array[ 'rank_uid' ] = $agentAgentMemberInfo->uid;
                            $agent_array[ 'commission_fee_rank' ] = $this->orderInfo->commission_fee_rank + $commission_fee_rank_diff;
                        } else if ( $agentAgentMemberInfo->member_level == $agentMemberInfo->member_level ) {
                            $agent_array[ 'rank_uid' ] = $agentAgentMemberInfo->uid;
                            $agent_array[ 'commission_fee_rank' ] = $this->orderInfo->commission_fee_rank;
                        }
                    }
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
