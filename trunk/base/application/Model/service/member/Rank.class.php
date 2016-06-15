<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Rank.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_Rank_base extends service_Member_base
{

    private $orderInfo;
    private $goods_member_level;
    private $memberInfo;
    private $agent_vip_status;
    private $agent_rank_uid;
    protected $trade_vendor;
    protected $trade_no;

    function setTrade_vendor( $trade_vendor )
    {
        $this->trade_vendor = $trade_vendor;
    }

    function setTrade_no( $trade_no )
    {
        $this->trade_no = $trade_no;
    }

    function setOrderInfo( $orderInfo )
    {
        $this->orderInfo = $orderInfo;
    }

    function setGoods_member_level( $goods_member_level )
    {
        $this->goods_member_level = $goods_member_level;
    }

    public function __construct()
    {
        parent::__construct();
        $this->orderInfo instanceof entity_OrderInfo_base;
    }

    /**
     * 付款后|更新member表中的member_level
     * 导会员专用|用完可以删除
     * todo delete
     */
    private function importUpdateMemberLevel( $memberInfo )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $memberInfo->uid );

        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->member_level = 1;
        return $dao->updateByPk( $entity_Member_base );
    }

    /**
     * 判断上家是不是会员
     * 导会员专用|用完可以删除
     * todo delete
     */
    private function importCheckAgentUidIsVIP( $agent_uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $agent_uid );
        $agent_member_info = $dao->getInfoByPk();
        $this->agent_vip_status = false;
        if ( $agent_member_info && $agent_member_info->member_level > 0 ) {
            $this->agent_vip_status = true;
        }
        return $this->agent_vip_status;
    }

    /**
     * 导数据
     * 导会员专用|用完可以删除
     * todo delete
     * @return boolean
     */
    public function import_init( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
        //付款后|更新member表中的member_level
        $this->importUpdateMemberLevel( $memberInfo );
        //判断上家是不是会员
        $this->importCheckAgentUidIsVIP( $memberInfo->agent_uid );
        //排位佣金
        $this->handleRank();
    }

    /**
     * 会员商品购买后.付款后
     * 付款后|更新member表中的member_level
     * ---------------------------------
      直推佣金
      --上家如果是会员
      ----直推的agent_uid给直推佣金
      --上家还不是会员
      ----不给直推佣金
     * ---------------------------------
     * 排位佣金
      --上家如果是会员
      ----直接从直接人下面开始排。上到下，左到右。|设置排位
      ----第一级的给排位佣金|付排位佣金
      --上家还不是会员
      ----直接从系统0级下面开始排，上到下，左到右。
     */
    public function init()
    {
        if ( $this->orderInfo->order_type <> service_Order_base::order_type_member ) {
            return true;
        }
        //判断上家是不是会员
        $this->checkAgentUidIsVIP();
        //付款后|更新member表中的member_level
        $this->updateMemberLevel();
        //直推佣金
        //$this->handleAgent();
        //排位佣金
        $this->handleRank();
        //更新订单的直推佣金uid和排位佣金的uid
        $this->updateOrderInfoAgent();
        //member_setting 表中的money相关字段更新
        $this->updateMemberSettingMoney();
        //member_bill 会员账单表更新
        $this->insertMemberBill();
    }

    /**
     * 判断上家是不是会员
     */
    private function checkAgentUidIsVIP()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->orderInfo->uid );
        $this->memberInfo = $dao->getInfoByPk();

        $dao->setPk( $this->memberInfo->agent_uid );
        $agent_member_info = $dao->getInfoByPk();
        $this->agent_vip_status = false;
        if ( $agent_member_info && $agent_member_info->member_level > 0 ) {
            $this->agent_vip_status = true;
        }
        return $this->agent_vip_status;
    }

    /**
     * 处理直推及佣金
     */
    private function handleAgent()
    {
        //判断上家是不是会员
        if ( $this->agent_vip_status ) {
            //是会员 给直接佣金
            //insert $this->orderInfo->commission_fee;
            //member_setting 表中的money相关字段更新
            //$this->updateMemberSettingMoney();
            //member_bill 会员账单表更新
            //$this->insertMemberBill();
            return true;
        } else {
            //不是会员.没有直推佣金喽
            return true;
        }
    }

    /**
     * 处理排名及佣金
     */
    private function handleRank()
    {
        $member_tree_model = new service_member_Tree_base();
        $member_tree_model->setMemberInfo( $this->memberInfo );
        //判断会员有没有 agent_rank_uid 时才去设置排位|有的话（不用设置排位）
        if ( empty( $this->memberInfo->agent_rank_uid ) ) {
            if ( $this->agent_vip_status ) {
                //直接从直接人下面开始排。上到下，左到右。|设置排位
                //第一级的给排位佣金|付排位佣金
                $member_tree_model->modifyAgentRankUid( $this->memberInfo->agent_uid );
            } else {
                //直接从系统0级下面开始排，上到下，左到右
                $member_tree_model->modifyAgentRankUid();
            }
        }
        $this->agent_rank_uid = $member_tree_model->getCommissionFeeRank( $this->goods_member_level );
        //var_dump($this->memberInfo->uid.'|$this->agent_rank_uid:'.$this->agent_rank_uid);        
        //todo 打钱
        return true;
    }

    /**
     * 付款后|更新member表中的member_level
     */
    private function updateMemberLevel()
    {
        //如果当前的会员级别小于会员商品级别就不用更新
        if ( $this->memberInfo->member_level > $this->goods_member_level ) {
            return true;
        }
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->orderInfo->uid );

        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->member_level = $this->goods_member_level;
        return $dao->updateByPk( $entity_Member_base );
    }

    /**
     * 更新订单的直推佣金uid和排位佣金的uid     
     * @return type
     */
    private function updateOrderInfoAgent()
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        if ( $this->agent_vip_status ) {
            $entity_OrderInfo_base->agent_uid = $this->memberInfo->agent_uid;
        } else {
            $entity_OrderInfo_base->agent_uid = 0;
        }
        if ( $this->agent_rank_uid !== false ) {
            $entity_OrderInfo_base->rank_uid = $this->agent_rank_uid;
        }
        $order_info_dao->setPk( $this->orderInfo->order_id );
        $order_info_dao->updateByPk( $entity_OrderInfo_base );
        return true;
    }

    /**
     * member_setting 表中的money相关字段更新
     * @2015-07-08增加 判断如果有分销商。分别向分销商和供应商写入账户金额变动
     * @return type
     */
    private function updateMemberSettingMoney()
    {
        $entity_OrderInfo_base = $this->orderInfo;
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $entity_MemberSetting_base = new entity_MemberSetting_base();
        //判断上家是不是会员
        if ( $this->agent_vip_status ) {
            //是会员 给直接佣金
            $commission_fee = $entity_OrderInfo_base->commission_fee; //直推佣金
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee );
            $member_setting_dao->setPk( $this->memberInfo->agent_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        } else {
            $commission_fee = 0;
        }
        //判断给不给排位佣金
        if ( $this->agent_rank_uid === false ) {
            $commission_fee_rank = 0;
        } else {
            $commission_fee_rank = $entity_OrderInfo_base->commission_fee_rank; //排位佣金    
            //供应商的钱更新开始            
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee_rank );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $this->agent_rank_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        }
        $commission_fee_rank_amount = $entity_OrderInfo_base->order_amount - $commission_fee - $commission_fee_rank;
        //系统佣金更新，如果有的话
        $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_fee_rank_amount );
        //更新卖家的金钱 商品供应商UID
        $member_setting_dao->setPk( $entity_OrderInfo_base->goods_uid );
        $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        return true;
    }

    /**
     * member_bill 会员账单表更新
     * @2015-07-08增加 判断如果有分销商。分别向分销商和供应商写入账户金额变动 历史记录
     * @return type
     */
    private function insertMemberBill()
    {
        $entity_OrderInfo_base = $this->orderInfo;
        $member_bill_dao = dao_factory_base::getMemberBillDao();

        $order_goods_array = unserialize( $entity_OrderInfo_base->order_goods_detail );
        $goods_count = count( $order_goods_array );


        $bill_note = "购买{$goods_count}件商品|LV{$this->goods_member_level}会员商品";
        $order_complete = service_Member_base::order_complete_no;
        $order_finish = service_Member_base::order_finish_no;
        $entity_MemberBill_base = new entity_MemberBill_base();

        //判断上家是不是会员
        if ( $this->agent_vip_status ) {
            //是会员 给直接佣金
            $commission_fee = $entity_OrderInfo_base->commission_fee; //直推佣金            
            //分销商金额变动日志 写入 开始
            $entity_MemberBill_base = new entity_MemberBill_base();
            $entity_MemberBill_base->uid = $this->memberInfo->agent_uid;
            $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
            $entity_MemberBill_base->money = $commission_fee;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale;
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
            $entity_MemberBill_base->bill_note = $bill_note . "(会员商品直推佣金)";
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
        } else {
            $commission_fee = 0;
        }
        //判断给不给排位佣金
        if ( $this->agent_rank_uid === false ) {
            $commission_fee_rank = 0;
        } else {
            $commission_fee_rank = $entity_OrderInfo_base->commission_fee_rank; //排位佣金    
            //供应商金额变动日志 写入 开始            
            $entity_MemberBill_base->uid = $this->agent_rank_uid;
            $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
            $entity_MemberBill_base->money = $commission_fee_rank;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale; //代销
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
            $entity_MemberBill_base->bill_note = $bill_note . "(会员商品排位佣金)";
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

}
