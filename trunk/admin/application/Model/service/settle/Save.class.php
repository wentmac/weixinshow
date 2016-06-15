<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Save.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_settle_Save_admin extends service_settle_Save_base
{

    protected $settle_id;
    protected $current_money;
    protected $settleInfo;

    function getCurrent_money()
    {
        return $this->current_money;
    }

    function setSettle_id( $settle_id )
    {
        $this->settle_id = $settle_id;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getSettleInfo()
    {
        $dao = dao_factory_base::getSettleDao();
        $dao->setPk( $this->settle_id );
        $this->settleInfo = $dao->getInfoByPk();
        return $this->settleInfo;
    }

    public function handleSettleInfo( $entity_Settle_base )
    {
        if ( empty( $entity_Settle_base->settle_bank_id ) ) {
            $entity_Settle_base->settle_bank_id = 'alipay';
        }
        $account_type_array = Tmac::config( 'bill.bill.account_type', APP_BASE_NAME );
        $settle_status_array = Tmac::config( 'bill.bill.settle_status', APP_BASE_NAME );

        //$entity_Settle_base->account_type_text = $account_type_array[ $entity_Settle_base->account_type ];

        $bank_id_array = Tmac::config( 'member.member_setting.bank_id', APP_MANAGE_NAME );
        if ( !empty( $entity_Settle_base->bank_id ) ) {
            $entity_Settle_base->bank_name = $bank_id_array[ $entity_Settle_base->bank_id ];
        } else {
            $entity_Settle_base->bank_name = '';
        }

        $entity_Settle_base->settle_apply_time = date( 'Y-m-d H:i:s', $entity_Settle_base->settle_apply_time );
        $entity_Settle_base->settle_status_option = Utility::Option( $settle_status_array, $entity_Settle_base->settle_status );


        $settle_bank_id_array = Tmac::config( 'member.member_setting.settle_bank_id', APP_MANAGE_NAME );
        $entity_Settle_base->settle_bank_id_option = Utility::Option( $settle_bank_id_array, $entity_Settle_base->settle_bank_id );

        $entity_Settle_base->settle_image_url = $this->getImage( $entity_Settle_base->settle_image_id, '800x0', 'settle' );

        return $entity_Settle_base;
    }

    /**
     * 检测是否能修改 的按钮
     * @param type $entity_Settle_base
     */
    public function checkSettleSaveBottonPurview( $entity_Settle_base )
    {
        $entity_Settle_base instanceof entity_Settle_base;
        //检测是否是打款。以及是否有提现打款的权限
        if ( $entity_Settle_base->settle_status == service_settle_List_base::settle_status_success ) {
            //检测 是否已经打款    
            throw new TmacClassException( '已经打过款，提现完成' );
        } else if ( $entity_Settle_base->settle_status == service_settle_List_base::settle_status_fail ) {
            //检测 是否已经打款    
            throw new TmacClassException( '提现被拒' );
        }
        //检测金额 和 余额
        $this->setMoney( $entity_Settle_base->money );
        $this->setUid( $entity_Settle_base->uid );
        $member_setting_info = $this->checkOverage();
        //检测 余额是否合法
        $member_bill_money = $this->getMemberMoneyOverage( $entity_Settle_base->uid );
        $this->current_money = $member_bill_money;
        if ( $member_bill_money <> $member_setting_info->current_money ) {
            throw new TmacClassException( "账户余额是:{$member_setting_info->current_money},账户实时余额是：{$member_bill_money}。请联系先技术核对账单。。" );
        }
        return true;
    }

    /**
     * 取真实价格
     * @param type $uid
     * @return type
     */
    private function getMemberMoneyOverage( $uid )
    {
        $dao = dao_factory_base::getMemberBillDao();
        $dao->setField( 'SUM(money) AS money' );
        $where = "uid={$uid} AND order_finish=" . service_Member_base::order_finish_yes;
        $dao->setWhere( $where );
        $info = $dao->getInfoByWhere();
        return $info->money;
    }

    public function settleSave( entity_Settle_base $entity_Settle_base )
    {
        if ( $entity_Settle_base->settle_status <> service_settle_List_base::settle_status_success && $entity_Settle_base->settle_status <> service_settle_List_base::settle_status_fail ) {
            $this->errorMessage = '提现类型不正确';
            return false;
        }
        if ( $entity_Settle_base->settle_status == service_settle_List_base::settle_status_success && $this->settleInfo->settle_status <> service_settle_List_base::settle_status_verify ) {
            $this->errorMessage = '还未审核通过的提现申请不能打款！！！';
            return false;
        }
        $settle_dao = dao_factory_base::getSettleDao();
        $member_bill_dao = dao_factory_base::getMemberBillDao();

        $member_bill_dao->setPk( $entity_Settle_base->member_bill_id );
        $member_bill_info = $member_bill_dao->getInfoByPk();
        if ( empty( $member_bill_info ) ) {
            $this->errorMessage = '提现账单ID不存在';
            return false;
        }

        if ( $member_bill_info->order_finish == service_Member_base::order_finish_yes ) {
            $this->errorMessage = '提现账单ID状态不正确';
            return false;
        }

        if ( $entity_Settle_base->settle_status == service_settle_List_base::settle_status_success ) {
            $member_model = new service_Member_base();
            $member_setting_info = $member_model->getMemberSettingInfoByUid( $this->settleInfo->uid );

            if ( empty( $member_setting_info->openid ) ) {
                $this->errorMessage = '用户还没有绑定提现在微信账号，不能执行提现打款';
                return false;
            }
            $wx_amount = $this->settleInfo->money * 100;
            //执行打款的Api
            require_once Tmac::findFile( 'payment/wechat_pay_transfers/WechatPayTransfers', APP_WWW_NAME );
            $wxpay_transfers_model = new WechatPayTransfers();

            $wxpay_transfers_model->setPartner_trade_no( $entity_Settle_base->member_bill_id );
            $wxpay_transfers_model->setOpenid( $member_setting_info->openid );
            $wxpay_transfers_model->setCheck_name( 'NO_CHECK' );
            $wxpay_transfers_model->setAmount( $wx_amount );
            $wxpay_transfers_model->setDesc( '用户：' . $member_setting_info->nickname . '的理赔退款￥' . $this->settleInfo->money );
            try {
                $result = $wxpay_transfers_model->payToUser();
            } catch (TmacClassException $exc) {
                Log::getInstance( 'mobile_order_payment_wechatpay_transfers_error' )->write( $exc->getMessage() . '|' . var_export( $member_setting_info, true ) . '|' . var_export( $this->settleInfo, true ) );
                $this->errorMessage = $exc->getMessage();
                return false;
            }
            $member_setting_dao = dao_factory_base::getMemberSettingDao();
            $return_code = $result[ 'return_code' ];
            $return_msg = $result[ 'return_msg' ];
            $result_code = $result[ 'result_code' ];
            $err_code = $result[ 'err_code' ]; //错误码
            $err_code_des = $result[ 'err_code_des' ]; //错误描述

            if ( $return_code == 'FAIL' ) {
                Log::getInstance( 'mobile_order_payment_wechatpay_transfers_error' )->write( $return_msg . '|' . var_export( $member_setting_info, true ) . '|' . var_export( $this->settleInfo, true ) );
                $this->errorMessage = $return_msg;
                return false;
            }
            if ( $result_code == 'FAIL' ) {
                if ( $err_code == 'OPENID_ERROR' ) {
                    //openid错误。更新用户的member_setting
                    $entity_MemberSetting_base = new entity_MemberSetting_base();
                    $entity_MemberSetting_base->settle_status = service_Member_base::settle_status_error;
                    $entity_MemberSetting_base->error_message = '您的收款微信账号绑定异常，请点击上面重新绑定微信账号';
                    $member_setting_dao->setPk( $entity_Settle_base->uid );
                    $member_setting_dao->updateByPk( $entity_MemberSetting_base );
                }
                Log::getInstance( 'mobile_order_payment_wechatpay_transfers_error' )->write( $err_code_des . '|' . var_export( $member_setting_info, true ) . '|' . var_export( $this->settleInfo, true ) );
                $this->errorMessage = $err_code_des;
                return false;
            }

            $payment_no = $result[ 'payment_no' ]; //微信订单号        
            $entity_Settle_base->settle_note = '提现微信支付账号:' . $payment_no;
        }
        $settle_dao->getDb()->startTrans();

        $settle_dao->setPk( $entity_Settle_base->settle_id );
        unset( $entity_Settle_base->member_bill_id );
        $settle_dao->updateByPk( $entity_Settle_base );

        $is_execute = service_Member_base::is_execute_fail;
        $message_type = service_PushMessage_base::message_type_settle_fail;
        if ( $entity_Settle_base->settle_status == service_settle_List_base::settle_status_success ) {
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money-' . $entity_Settle_base->money );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $entity_Settle_base->uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
            $is_execute = service_Member_base::is_execute_success;
            $message_type = service_PushMessage_base::message_type_settle_success;
        }

        //更新member_bill表中的 order_finish
        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
        $entity_MemberBill_base->order_complete = service_Member_base::order_complete_yes;
        $entity_MemberBill_base->is_execute = $is_execute;
        $member_bill_dao->updateByPk( $entity_MemberBill_base );

        //消息 push
        /**
          $settle_dao->setPk( $entity_Settle_base->settle_id );
          $settleInfo = $settle_dao->getInfoByPk();
          $push_message_model = new service_PushMessage_base();
          $push_message_model->setMessageType( service_PushMessage_base::message_type_settle_success );
          $push_message_model->setSettleInfo( $settleInfo );
          $push_message_model->push();
         */
        if ( $settle_dao->getDb()->isSuccess() ) {
            $settle_dao->getDb()->commit();
            return true;
        } else {
            $settle_dao->getDb()->rollback();
            return false;
        }
    }

    public function settleVerify( $settle_id )
    {
        if ( $this->settleInfo->settle_status <> service_settle_List_base::settle_status_untreated ) {
            $this->errorMessage = '提现审核的状态不正确';
            return false;
        }
        $settle_dao = dao_factory_base::getSettleDao();
        $settle_dao->getDb()->startTrans();
        $entity_Settle_base = new entity_Settle_base();
        $entity_Settle_base->settle_status = service_settle_List_base::settle_status_verify;
        $settle_dao->setPk( $settle_id );
        $settle_dao->updateByPk( $entity_Settle_base );
        if ( $settle_dao->getDb()->isSuccess() ) {
            $settle_dao->getDb()->commit();
            return true;
        } else {
            $settle_dao->getDb()->rollback();
            return false;
        }
    }

}
