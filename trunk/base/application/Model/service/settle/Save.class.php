<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Save.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_settle_Save_base extends service_Model_base
{

    /**
     * 最小提现金额
     */
    const min_setting_money = 1;
    const default_account_type_bank = 1;
    const default_account_type_alipay = 2;

    protected $uid;
    protected $money;
    protected $account_type;
    protected $mobile;
    protected $memberInfo;
    protected $errorMessage;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setMoney( $money )
    {
        $this->money = $money;
    }

    function setAccount_type( $account_type )
    {
        $this->account_type = $account_type;
    }

    function setMobile( $mobile )
    {
        $this->mobile = $mobile;
    }

    function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取默认的提现账户类型
     */
    public function getDefaultAccountType()
    {
        $member_setting_info = $this->getMemberSettingInfo();
        $account_type = $member_setting_info->default_account_type;
        $return = array(
            'account_type' => $account_type,
            'current_money' => $member_setting_info->current_money
        );
        return $return;
    }

    public function getMemberAccountBankArray()
    {
        $member_setting_info = $this->getMemberSettingInfo();

        $return = array(
            self::default_account_type_alipay => array(
                'alipay_account' => $member_setting_info->alipay_account,
                'alipay_username' => $member_setting_info->alipay_username
            ),
            self::default_account_type_bank => array(
                'bank_id' => $member_setting_info->bank_id,
                'bank_account' => $member_setting_info->bank_account,
                'bank_cardnum' => $member_setting_info->bank_cardnum
            )
        );
        return $return;
    }

    /**
     * 取默认的账户设置
     */
    protected function getMemberSettingInfo()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $where = "uid={$this->uid}";
        $dao->setWhere( $where );
        return $dao->getInfoByWhere();
    }

    /**
     * $this->uid;
     * $this->money;
     * $this->account_type;
     * $this->createSettle();
     */
    public function createSettle()
    {
        //判断是否账户提现锁定
        if ( $this->memberInfo->locked_type == service_Member_base::locked_type_settle ) {
            throw new TmacClassException( '您的账户出现异常，暂时无法提现。请联系银品惠客服解决！' );
        }
        //判断金额
        if ( $this->money < self::min_setting_money ) {
            throw new TmacClassException( '提现金额不能小于：￥' . self::min_setting_money . '元' );
        }
        //判断用户是否账户余额是否足够
        $member_setting_info = $this->checkOverage();
        //检测是否有没有处理过的申请
        $this->checkIsUnHandleSettle();

        $dao = dao_factory_base::getSettleDao();
        $member_bill_dao = dao_factory_base::getMemberBillDao();
        $dao->getDb()->startTrans();

        $entity_MemberBill_base = new entity_MemberBill_base();
        $entity_MemberBill_base->uid = $this->uid;
        $entity_MemberBill_base->order_id = 0;
        $entity_MemberBill_base->money = -$this->money;
        $entity_MemberBill_base->bill_type = service_Member_base::bill_type_expend;
        $entity_MemberBill_base->bill_type_class = service_Member_base::bill_type_class_no; //提现没有金额流入
        $entity_MemberBill_base->bill_expend_type = service_Member_base::bill_expend_type_withdrawals;
        $entity_MemberBill_base->bill_note = '申请提现';
        $entity_MemberBill_base->bill_time = $this->now;
        $entity_MemberBill_base->is_execute = 0;
        $entity_MemberBill_base->execute_time = 0;
        $entity_MemberBill_base->trade_vendor = '';
        $entity_MemberBill_base->trade_no = '';
        $entity_MemberBill_base->batch_no = '';
        $entity_MemberBill_base->refund_id = '';
        $entity_MemberBill_base->bill_uid = 0;
        $entity_MemberBill_base->bill_realname = '';
        $entity_MemberBill_base->bill_image_id = '';
        $member_bill_id = $member_bill_dao->insert( $entity_MemberBill_base );

        //写入提现申请表 settle表
        $entity_Settle_base = new entity_Settle_base();
        $entity_Settle_base->uid = $this->uid;
        $entity_Settle_base->mobile = $this->mobile;
        $entity_Settle_base->realname = $member_setting_info->nickname;
        $entity_Settle_base->shop_name = $this->getShopNameByUid( $this->uid );
        $entity_Settle_base->money = $this->money;
        $entity_Settle_base->settle_status = service_settle_List_base::settle_status_verify;

        $entity_Settle_base->settle_apply_time = $this->now;
        $entity_Settle_base->member_bill_id = $member_bill_id;
        $dao->insert( $entity_Settle_base );

        //消息 push
        /**
        $push_message_model = new service_PushMessage_base();
        $push_message_model->setMessageType( service_PushMessage_base::message_type_settle );
        $push_message_model->setSettleInfo( $entity_Settle_base );
        $push_message_model->push();         
         */
        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            //TODO 发短信
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 判断用户是否账户余额是否足够
     */
    protected function checkOverage()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setWhere( "uid={$this->uid}" );
        $dao->setField( 'current_money,alipay_account,alipay_username,bank_id,bank_cardnum,bank_account,openid,nickname,settle_status' );
        $member_setting_info = $dao->getInfoByWhere();
        if ( $member_setting_info->current_money < $this->money ) {
            throw new TmacClassException( "提现金额：{$this->money}大于账户余额:{$member_setting_info->current_money}" );
        }
        return $member_setting_info;
    }

    /**
     * 判断用户是否已经有未处理的提现申请
     */
    private function checkIsUnHandleSettle()
    {
        $dao = dao_factory_base::getMemberBillDao();
        $where = "uid={$this->uid} AND bill_type=" . service_Member_base::bill_type_expend . ' AND bill_expend_type='
                . service_Member_base::bill_expend_type_withdrawals . ' AND is_execute=0';
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();
        if ( $count > 0 ) {
            throw new TmacClassException( '您已经申请过一次提现，请等上一次提现申请处理完毕后再重新提现' );
        }
        return true;
    }

    private function getShopNameByUid( $uid )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setField( 'shop_name' );
        $dao->setWhere( "uid={$uid}" );
        $shopInfo = $dao->getInfoByWhere();
        return $shopInfo->shop_name;
    }

}
