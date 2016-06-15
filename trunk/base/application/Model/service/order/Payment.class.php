<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Payment.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Payment_base extends service_Order_base
{

    protected $trade_vendor;
    protected $trade_no;
    protected $entity_OrderInfo;
    protected $entity_MemberSetting;
    protected $pay_time;
    protected $order_goods_array;

    function setTrade_vendor( $trade_vendor )
    {
        $this->trade_vendor = $trade_vendor;
    }

    function setTrade_no( $trade_no )
    {
        $this->trade_no = $trade_no;
    }

    function setEntity_OrderInfo( $entity_OrderInfo )
    {
        $this->entity_OrderInfo = $entity_OrderInfo;
    }

    /**
     * 自定义付款时间
     * @param type $pay_time
     */
    function setPay_time( $pay_time )
    {
        $this->pay_time = $pay_time;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取订单的所有商品
     * @param type $goods_id
     * @param type $field
     * @return type
     */
    private function getOrderGoodsArray( $goods_id, $field = '*' )
    {
        $order_goods_dao = dao_factory_base::getOrderGoodsDao();
        $order_goods_dao->setWhere( "order_id={$goods_id}" );
        $order_goods_dao->setField( $field );
        $res = $order_goods_dao->getListByWhere();
        return $res;
    }

    /**
     * 取订单的所有商品
     * @param type $goods_id
     * @param type $field
     * @return type
     */
    private function getOrderGoodsInfo( $goods_id, $field = '*' )
    {
        $order_goods_dao = dao_factory_base::getOrderGoodsDao();
        $order_goods_dao->setWhere( "order_id={$goods_id}" );
        $order_goods_dao->setField( $field );
        $res = $order_goods_dao->getInfoByWhere();
        return $res;
    }

    /**
     * 订单支付成功，需要处理的事务｜商品库存减少，订单状态修改
     * $this->trade_vendor;
     * $this->trade_no;
     * $this->orderPaySuccess($entity_OrderInfo_base);
     */
    public function orderPaySuccess()
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_info_dao->getDb()->startTrans();

        //修改订单状态和支付状态 order_info表        
        $entity_OrderInfo_base = new entity_OrderInfo_base();

        $entity_OrderInfo_base->order_status = $this->entity_OrderInfo->order_status = service_Order_base::order_status_buyer_payment;
        $entity_OrderInfo_base->pay_status = $this->entity_OrderInfo->pay_status = service_Order_base::pay_status_success;
        $entity_OrderInfo_base->trade_vendor = $this->trade_vendor;
        $entity_OrderInfo_base->trade_no = $this->trade_no;
        $entity_OrderInfo_base->pay_time = empty( $this->pay_time ) ? $this->now : $this->pay_time;

        $order_info_dao->setPk( $this->entity_OrderInfo->order_id );
        $order_info_dao->updateByPk( $entity_OrderInfo_base );

        //取供应商的个人信息，用来判断 如果是收钱台的时候。如果账户余额为0的话，不能提现。限制收银台套现用的。
        //20151023取消，收银台提现限制。因加入了收银台提现手续费。
        //$this->getMemberSettingInfo();
        //item,goods,goods_sku表,库存更新 start
        $this->updateGoodsStock();

        //order_action 订单操作记录表
        $this->insertOrderAction();

        if ( $this->entity_OrderInfo->order_type == service_Order_base::order_type_member ) {
            $order_goods_info = $this->getOrderGoodsInfo( $this->entity_OrderInfo->order_id, 'goods_member_level' );
            $goods_member_level = $order_goods_info->goods_member_level;
            $member_rank_model = new service_member_Rank_base();
            $member_rank_model->setGoods_member_level( $goods_member_level );
            $member_rank_model->setOrderInfo( $this->entity_OrderInfo );
            $member_rank_model->setTrade_no( $this->trade_no );
            $member_rank_model->setTrade_vendor( $this->trade_vendor );
            $member_rank_model->init();
        } else if ( $this->entity_OrderInfo->order_type == service_Order_base::order_type_member_monopoly ) {
            $member_monopoly_model = new service_order_money_MemberMonopoly_base();
            $member_monopoly_model->setOrderInfo( $this->entity_OrderInfo );
            $member_monopoly_model->setTrade_no( $this->trade_no );
            $member_monopoly_model->setTrade_vendor( $this->trade_vendor );
            $member_monopoly_model->payment();
        } else if ( $this->entity_OrderInfo->order_type == service_Order_base::order_type_mall ) {
            $member_mall_model = new service_order_money_MallGoods_base();
            $member_mall_model->setOrderInfo( $this->entity_OrderInfo );
            $member_mall_model->setTrade_no( $this->trade_no );
            $member_mall_model->setTrade_vendor( $this->trade_vendor );
            $member_mall_model->payment();
        } else {
            //member_setting 表中的money相关字段更新
            $this->updateMemberSettingMoney();
            //member_bill 会员账单表更新
            $this->insertMemberBill();
        }


        //如果是开通vip自助服务的
        //$this->updateMemberClassToVIP();
        //TODO 发短信
        $push_message_model = new service_PushMessage_base();
        $push_message_model->setMessageType( service_PushMessage_base::message_type_order );
        $push_message_model->setOrderInfo( $this->entity_OrderInfo );
        //$push_message_model->push();
        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $order_info_dao->getDb()->isSuccess() ) {
            $order_info_dao->getDb()->commit();
            return true;
        } else {
            $order_info_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 自主充值的的用户开通vip权限
     * @param type $param
     */
    private function updateMemberClassToVIP()
    {
        if ( $this->entity_OrderInfo->goods_uid <> service_order_Save_base::judian_shop_id ) {
            return true;
        }
        //需要更新用户的身份
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->member_type = service_Member_base::member_type_seller;
        $entity_Member_base->member_class = service_Member_base::member_class_seller_vip;

        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->entity_OrderInfo->uid );
        return $dao->updateByPk( $entity_Member_base );
    }

    /**
     * order_action 订单操作记录表
     * @return type
     */
    private function insertOrderAction()
    {
        $entity_OrderInfo_base = $this->entity_OrderInfo;
        $order_action_dao = dao_factory_base::getOrderActionDao();

        $entity_OrderAction_base = new entity_OrderAction_base();
        $entity_OrderAction_base->order_id = $entity_OrderInfo_base->order_id;
        $entity_OrderAction_base->action_uid = $entity_OrderInfo_base->uid;
        $entity_OrderAction_base->action_username = $entity_OrderInfo_base->consignee . "[{$entity_OrderInfo_base->mobile}]";
        $entity_OrderAction_base->order_status = $entity_OrderInfo_base->order_status;
        $entity_OrderAction_base->pay_status = $entity_OrderInfo_base->pay_status;
        $entity_OrderAction_base->action_note = "用户为订单（{$entity_OrderInfo_base->order_id}）付款{$entity_OrderInfo_base->order_amount}元";
        $entity_OrderAction_base->action_time = $this->now;
        return $order_action_dao->insert( $entity_OrderAction_base );
    }

    /**
     * member_bill 会员账单表更新
     * @2015-07-08增加 判断如果有分销商。分别向分销商和供应商写入账户金额变动 历史记录
     * @return type
     */
    private function insertMemberBill()
    {
        $entity_OrderInfo_base = $this->entity_OrderInfo;
        $member_bill_dao = dao_factory_base::getMemberBillDao();

        $order_goods_array = unserialize( $entity_OrderInfo_base->order_goods_detail );
        $goods_count = count( $order_goods_array );

        $bill_note = "购买{$goods_count}件商品";
        $order_complete = service_Member_base::order_complete_no;
        $order_finish = service_Member_base::order_finish_no;
        $confirm_time = 0;

        if ( $entity_OrderInfo_base->order_type == service_Order_base::order_type_goods && !empty( $entity_OrderInfo_base->agent_uid ) && !empty( $entity_OrderInfo_base->commission_fee ) ) {
            //普通商品直推人佣金 写入 开始
            $entity_MemberBill_base = new entity_MemberBill_base();
            $entity_MemberBill_base->uid = $entity_OrderInfo_base->agent_uid;
            $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
            $entity_MemberBill_base->money = $entity_OrderInfo_base->commission_fee;
            $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
            $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_wholesale; //代销
            $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no;
            $entity_MemberBill_base->bill_note = $bill_note . "(商品直推佣金)";
            $entity_MemberBill_base->bill_time = $this->now;
            $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
            $entity_MemberBill_base->trade_no = $this->trade_no;
            $entity_MemberBill_base->order_complete = $order_complete;
            $entity_MemberBill_base->order_finish = $order_finish;
            $entity_MemberBill_base->bill_uid = $entity_OrderInfo_base->uid;
            $entity_MemberBill_base->bill_realname = $entity_OrderInfo_base->consignee;
            $entity_MemberBill_base->bill_image_id = $order_goods_array[ 0 ]->goods_image_id;
            $entity_MemberBill_base->confirm_time = $confirm_time;
            $member_bill_dao->insert( $entity_MemberBill_base );
            //普通商品直推人佣金 写入 结束
        }
        $supplier_amount = $entity_OrderInfo_base->order_amount - $entity_OrderInfo_base->commission_fee; //供应商的金额 订单总金额减去给分销商的佣金
        //供应商金额变动日志 写入 开始       
        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->uid = $entity_OrderInfo_base->goods_uid;
        $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
        $entity_MemberBill_base->money = $supplier_amount;
        $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
        $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_business; //自营 或 收银台
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
        $entity_MemberBill_base->confirm_time = $confirm_time;
        $member_bill_dao->insert( $entity_MemberBill_base );
        //供应商金额变动日志 写入 结束                        
        //系统佣金
        //$this->insertSystemMemberBill( $entity_OrderInfo_base->commission_fee_rank, $order_goods_array[ 0 ]->goods_image_id, $order_complete, $order_finish );
        return true;
    }

    /**
     * member_setting 表中的money相关字段更新
     * @2015-07-08增加 判断如果有分销商。分别向分销商和供应商写入账户金额变动
     * @return type
     */
    private function updateMemberSettingMoney()
    {
        $entity_OrderInfo_base = $this->entity_OrderInfo;
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        /**
          if ( $this->checkIsSelfGoods( $entity_OrderInfo_base ) ) {//如果非分销
          $entity_MemberSetting_base = new entity_MemberSetting_base();
          $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $entity_OrderInfo_base->order_amount );
          //更新卖家的金钱 商品供应商UID
          $member_setting_dao->setPk( $entity_OrderInfo_base->goods_uid );
          $member_setting_dao->updateByPk( $entity_MemberSetting_base );

          //系统佣金更新，如果有的话
          //$this->updateSystemMemberSettingMoney( $entity_OrderInfo_base->commission_fee_rank );
          } else {//分销商品
         */
        if ( $entity_OrderInfo_base->order_type == service_Order_base::order_type_goods && !empty( $entity_OrderInfo_base->agent_uid ) && !empty( $entity_OrderInfo_base->commission_fee ) ) {
            //普通商品真推人的钱更新开始
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $entity_OrderInfo_base->commission_fee );
            //更新卖家的金钱 商品分销商UID
            $member_setting_dao->setPk( $entity_OrderInfo_base->agent_uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
            //分销商的钱更新结束
        }
        $supplier_amount = $entity_OrderInfo_base->order_amount - $entity_OrderInfo_base->commission_fee;
        //供应商的钱更新开始            
        $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $supplier_amount );
        //更新卖家的金钱 商品供应商UID
        $member_setting_dao->setPk( $entity_OrderInfo_base->goods_uid );
        $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        //供应商的钱更新结束       
        //系统佣金更新，如果有的话
        //$this->updateSystemMemberSettingMoney( $commission_system_fee_amount );
        /* } */

        return true;
    }

    /**
     * 更新系统用户的余额变动
     * 增加余额
     */
    private function updateSystemMemberSettingMoney( $money )
    {
        $commission_system_fee = floatval( $money );
        if ( empty( $commission_system_fee ) ) {
            return true;
        }
        $member_setting_dao = dao_factory_base::getMemberSettingDao();

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        //if ( $this->entity_OrderInfo->order_type == service_Order_base::order_type_member && $this->entity_MemberSetting->current_money > 0 ) {
        if ( $this->entity_OrderInfo->order_type == service_Order_base::order_type_member ) {
            //收银台直接付款的，用户可用余额直接更新到位|商品类型的订单，等买付确认收款后再单独把可用余额current_money给更新到位
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money+' . $commission_system_fee );
        }
        $entity_MemberSetting_base->history_money = new TmacDbExpr( 'history_money+' . $commission_system_fee );
        //更新卖家的金钱 商品供应商UID
        $member_setting_dao->setPk( 1 );
        return $member_setting_dao->updateByPk( $entity_MemberSetting_base );
    }

    /**
     * 插入系统佣金记录
     * @param type $money
     */
    private function insertSystemMemberBill( $money, $goods_image_id, $order_complete, $order_finish )
    {
        $commission_system_fee = floatval( $money );
        if ( empty( $commission_system_fee ) ) {
            return true;
        }
        $entity_OrderInfo_base = $this->entity_OrderInfo;
        $member_bill_dao = dao_factory_base::getMemberBillDao();

        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->uid = 1;
        $entity_MemberBill_base->order_id = $entity_OrderInfo_base->order_id;
        $entity_MemberBill_base->money = $commission_system_fee;
        $entity_MemberBill_base->bill_type = service_Member_base::bill_type_income;
        $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_system; //系统佣金
        $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_no; //没有金额流出
        $entity_MemberBill_base->bill_note = '系统佣金';
        $entity_MemberBill_base->bill_time = $this->now;
        $entity_MemberBill_base->trade_vendor = $this->trade_vendor;
        $entity_MemberBill_base->trade_no = $this->trade_no;
        $entity_MemberBill_base->order_complete = $order_complete;
        $entity_MemberBill_base->order_finish = $order_finish;
        $entity_MemberBill_base->bill_uid = $entity_OrderInfo_base->uid;
        $entity_MemberBill_base->bill_realname = $entity_OrderInfo_base->consignee;
        $entity_MemberBill_base->bill_image_id = $goods_image_id;
        return $member_bill_dao->insert( $entity_MemberBill_base );
    }

    /**
     * item,goods,goods_sku表,库存更新 start
     * @return boolean
     */
    private function updateGoodsStock()
    {
        $entity_OrderInfo_base = $this->entity_OrderInfo;

        //更新商品库存
        $order_save_model = new service_order_Save_mobile();
        $order_save_model->setItem_uid( $entity_OrderInfo_base->item_uid );
        $seller_member_setting = $order_save_model->getSellerMemberSetting();
        //更新商品表的 销量
        if ( $seller_member_setting->stock_setting == service_Member_base::stock_setting_order_pay ) {
            $order_goods_array = $this->getOrderGoodsArray( $entity_OrderInfo_base->order_id, $field = 'goods_id,item_id,item_number,goods_sku_id' );
            foreach ( $order_goods_array as $order_goods ) {
                //如果卖家设置的是（付款减库存）
                $order_save_model->updateGoodsStock( $order_goods );
            }
        }
        return true;
    }

    /**
     * 取订单标题，给支付时候跳转过去看的
     */
    public function getOrderSubject()
    {
        $dao = dao_factory_base::getOrderGoodsDao();
        $dao->setWhere( "order_id={$this->order_id}" );
        $dao->setField( 'item_id,item_name,item_number,goods_sku_name,goods_sku_id,goods_id' );
        $res = $dao->getListByWhere();
        $this->order_goods_array = $res;
        $order_subject = '';
        if ( $res ) {
            foreach ( $res as $order_goods ) {
                if ( empty( $order_goods->goods_sku_name ) ) {
                    $subject = "|{$order_goods->item_name}(数量：{$order_goods->item_number})";
                } else {
                    $subject = "|{$order_goods->item_name}{$order_goods->goods_sku_name}(数量：{$order_goods->item_number})";
                }

                $order_subject .= $subject;
            }
        }
        return substr( $order_subject, 1 );
    }

    /**
     * 插入支付记录
     * @param entity_PayLog_base $entity_PayLog_base
     * @return type
     */
    public function insertPayLog( entity_PayLog_base $entity_PayLog_base )
    {
        $dao = dao_factory_base::getPayLogDao();
        return $dao->insert( $entity_PayLog_base );
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
     * 检测是否已经付款
     * @param type $order_sn
     * @return boolean|int
     */
    public function getOrderPaymentStatus( $order_sn )
    {
        $order_info = parent::getOrderInfoBySN( $order_sn, 'order_id,uid,order_status,pay_status' );
        if ( !$order_info ) {
            $this->errorMessage = '订单不存在';
            return false;
        }
        if ( $order_info->uid <> $this->uid ) {
            $this->errorMessage = '只能查看自己的订单哟';
            return false;
        }
        if ( $order_info->order_status > service_Order_base::order_status_buyer_payment ) {
            $this->errorMessage = '只能查看自己的订单哟';
            return false;
        }

        $result = 0;
        if ( $order_info->order_status >= service_Order_base::order_status_buyer_payment && $order_info->pay_status == service_Order_base::pay_status_success ) {
            $result = 1;
        }
        return $result;
    }

    /**
     * 取用户的member_setting
     */
    private function getMemberSettingInfo()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->entity_OrderInfo->goods_uid );
        $dao->setField( 'member_type,member_class,current_money,history_money' );
        $this->entity_MemberSetting = $dao->getInfoByPk();
        return $this->entity_MemberSetting;
    }

    /**
     * order专用详情
     */
    private function getGoodsInfo( $goods_id, $field = '*' )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( $field );
        $dao->setPk( $goods_id );
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
     * 付款前的库存检测
     * 放在$this->getOrderSubject();后面
     */
    public function checkStockBeforePayment()
    {
        foreach ( $this->order_goods_array AS $order_goods ) {
            $order_goods instanceof entity_OrderGoods_base;
            //有sku_id的检测sku库存。没有的检测goods_id的库存
            if ( !empty( $order_goods->goods_sku_id ) ) {//goods_sku表 
                $goods_sku_info = self::getGoodsSkuInfo( $order_goods->goods_sku_id, 'stock,goods_id' );
                if ( $goods_sku_info == false ) {
                    $this->errorMessage = '商品规格不存在';
                    return false;
                }
                if ( $goods_sku_info->goods_id <> $order_goods->goods_id ) {
                    $this->errorMessage = '商品的规格不正确';
                    return false;
                }
                $stock = $goods_sku_info->stock;
            } else {
                $goods_info = self::getGoodsInfo( $order_goods->goods_id, 'goods_id,goods_stock,is_delete' );
                if ( $goods_info == false ) {
                    $this->errorMessage = '商品项目不存在';
                    return false;
                }
                if ( $goods_info->is_delete == 1 ) {
                    $this->errorMessage = '商品项目已经下线';
                    return false;
                }
                $stock = $goods_info->goods_stock;
            }
            if ( $stock < $order_goods->item_number ) {
                $this->errorMessage = '您选的商品:"' . $order_goods->item_name . '"数量已经超过商品库存了';
                return false;
            }
        }
        return true;
    }

}
