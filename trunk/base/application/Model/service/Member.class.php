<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Member.class.php 362 2016-06-10 15:28:54Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Member_base extends service_Model_base
{

    /**
     * 库存设置状态
     * 1：拍下减库存
     */
    const stock_setting_order_save = 1;

    /**
     * 库存设置状态
     * 2：付款减库存
     */
    const stock_setting_order_pay = 2;

    /**
     * 账单资金变动类型 收入
     */
    const bill_type_income = 1;

    /**
     * 账单资金变动类型 支出
     */
    const bill_type_expend = 2;

    /**
     * 当bill_type=1时（收入）
     * 自营收入
     */
    const bill_type_class_no = 0;

    /**
     * 当bill_type=1时（收入）
     * 自营收入
     */
    const bill_type_class_business = 1;

    /**
     * 当bill_type=1时（收入）
     * 代销收入
     */
    const bill_type_class_wholesale = 2;

    /**
     * 当bill_type=1时（收入）
     * 直接收款（收银台）
     */
    const bill_type_class_receivable = 3;

    /**
     * 当bill_type=1时（收入）
     * 系统佣金
     */
    const bill_type_class_system = 4;

    /**
     * 没有金额流水
     */
    const bill_expend_type_no = 0;

    /**
     * 提现
     */
    const bill_expend_type_withdrawals = 1;

    /**
     * 退款
     */
    const bill_expend_type_refund = 2;

    /**
     * 佣金退款
     */
    const bill_expend_type_commission_refund = 3;

    /**
     * 订单是否完成
     * 未完成
     * 等待买家确认收货
     */
    const order_complete_no = 0;

    /**
     * 订单是否完成
     * 已经完成
     * 买家已经确认收货
     */
    const order_complete_yes = 1;

    /**
     * 订单是否完成
     * 已经完成
     * 订单有售后问题处理中
     */
    const order_complete_refund = 2;

    /**
     * 用户类型
     * 普通用户[买家+分销商]
     */
    const member_type_seller = 1;

    /**
     * 用户类型
     * 供应商
     */
    const member_type_supplier = 2;

    /**
     * 用户类型
     * 买家身份 不能登录app和供应商后台
     */
    const member_type_buyer = 3;

    /**
     * 用户类型
     * 商城用户
     */
    const member_type_mall = 4;

    /**
     * 分销商
     * 免费
     * @var type 
     */
    const member_class_seller_free = 0;

    /**
     * 分销商
     * VIP
     * @var type 
     */
    const member_class_seller_vip = 1;

    /**
     * 分销商
     * SVIP
     * @var type 
     */
    const member_class_seller_svip = 2;

    /**
     * 供应商
     * 免费供应商
     * @var type 
     */
    const member_class_supplier_free = 0;

    /**
     * 供应商
     * 铜牌
     * @var type 
     */
    const member_class_supplier_copper = 1;

    /**
     * 供应商
     * 银牌
     * @var type 
     */
    const member_class_supplier_silver = 2;

    /**
     * 供应商
     * 金牌
     * @var type 
     */
    const member_class_supplier_gold = 3;

    /**
     * 聚店商城
     * 省代
     * @var type 
     */
    const member_class_mall_province = 1;

    /**
     * 聚店商城
     * 市代
     * @var type 
     */
    const member_class_mall_city = 2;

    /**
     * 聚店商城
     * 普代
     * @var type 
     */
    const member_class_mall_general = 3;

    /**
     * 订单完结状态
     * 0：未完结[未结算收入]
     */
    const order_finish_no = 0;

    /**
     * 订单完结状态
     * 已经完结[已结算收入]
     */
    const order_finish_yes = 1;

    /**
     * 用户锁定类型
     * 未锁定
     */
    const locked_type_none = 0;

    /**
     * 用户锁定类型
     * 提现锁定
     */
    const locked_type_settle = 1;

    /**
     * 提现执行状态
     * 申请提现
     */
    const is_execute_default = 0;

    /**
     * 提现执行状态
     * 成功
     */
    const is_execute_success = 1;

    /**
     * 提现执行状态
     * 申请提现失败
     */
    const is_execute_fail = 2;

    /**
     * 聚店店铺UID
     */
    const yph_uid = 46;

    /**
     * 排位赛 的粉丝额度
     * 排位赛中一个会员下面 排位粉丝的数量
     */
    const rank_limit = 3;

    /**
     * 会员级别
     * 1级     
     */
    const member_level_1 = 1;

    /**
     * 会员级别
     * 1级     
     */
    const member_level_2 = 2;

    /**
     * 会员级别
     * 3级     
     */
    const member_level_3 = 3;

    /**
     * 会员级别
     * 4级     
     */
    const member_level_4 = 4;

    /**
     * 会员级别
     * 5级     
     */
    const member_level_5 = 5;

    /**
     * 会员级别
     * 6级     
     */
    const member_level_6 = 6;

    /**
     * 会员级别
     * 7级     
     */
    const member_level_7 = 7;

    /**
     * 会员级别
     * 8级     
     */
    const member_level_8 = 8;

    /**
     * 会员级别
     * 9级     
     */
    const member_level_9 = 9;

    /**
     * 东家ID更换锁(0:可以换)     
     */
    const agent_lock_no = 0;

    /**
     * 东家ID更换锁
     * (不能换了,只要成功购买一次lv1后都不能再换东家ID了)     
     */
    const agent_lock_yes = 1;

    /**
     * 企业付款到个人
     * 正常
     */
    const settle_status_success = 0;

    /**
     * 企业付款到个人
     * 异常
     */
    const settle_status_error = 1;

    /**
     * 扫码注册后给多少积分
     * 1个积分
     * @var type 
     */
    const agent_integral_value = 1;

    /**
     * 扫码注册得到的积分
     * 10个积分
     * @var type 
     */
    const agent_register_integral_value = 60;

    /**
     * 历史总积分
     * 100个积分
     * @var type 
     */
    const agent_integral_max_value = 100;

    /**
     * 每天最多的积分
     * 20个积分
     * @var type 
     */
    const agent_integral_day_max_value = 20;

    protected $uid;
    protected $errorMessage;

    function setUid( $uid )
    {
        $this->uid = $uid;
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
     * 通过UID取member
     * @param type $uid
     * @return type
     */
    public function getMemberInfoByUid( $uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $uid );
        return $dao->getInfoByPk();
    }

    /**
     * 通过UID取member
     * @param type $uid
     * @return type
     */
    public function getMemberInfoByMobile( $mobile )
    {
        $dao = dao_factory_base::getMemberDao();
        $where = "mobile='{$mobile}'";
        $dao->setWhere( $where );
        return $dao->getInfoByWhere();
    }

    /**
     * 通过UID取member
     * @param type $uid
     * @return type
     */
    public function getMemberSettingInfoByUid( $uid )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $uid );
        return $dao->getInfoByPk();
    }

    /**
     * 更新会员信息表
     * @param entity_Member_base $entity_Member_base
     */
    public function updateMemberInfo( entity_Member_base $entity_Member_base )
    {
        $dao = dao_factory_base::getMemberDao();
        $member_setting_dao = dao_factory_base::getMemberSettingDao();

        $dao->getDb()->startTrans();

        $dao->setpk( $this->uid );
        $dao->updateByPk( $entity_Member_base );

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->member_type = $entity_Member_base->member_type;
        $entity_MemberSetting_base->member_class = $entity_Member_base->member_class;

        $member_setting_dao->setPk( $this->uid );
        $member_setting_dao->updateByPk( $entity_MemberSetting_base );

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
     * 更新会员信息表
     * @param entity_MemberSetting_base $entity_MemberSetting_base
     */
    public function updateMemberSettingInfo( entity_MemberSetting_base $entity_MemberSetting_base )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setpk( $this->uid );
        return $dao->updateByPk( $entity_MemberSetting_base );
    }

}
