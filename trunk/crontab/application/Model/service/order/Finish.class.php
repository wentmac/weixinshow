<?php

/**
 * crontab 批量更新
 * 过期订单的付款状态
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Finish.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Finish_crontab extends service_Order_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function executeMemberCurrentMoneyUpdate()
    {
        $bill_time = $this->now - 86400 * service_Order_base::return_service_max_day;
        //查询出order_finish=0 AND bill_time>={$bill_time}
        $where = 'order_finish=' . service_Member_base::order_finish_no . ' AND order_complete=' . service_Member_base::order_complete_yes . ' AND confirm_time<=' . $bill_time;
        $dao = dao_factory_base::getMemberBillDao();
        $dao->setWhere( $where );
        $dao->setField( 'member_bill_id,uid,order_id,money,bill_type,bill_type_class,bill_expend_type,order_complete,order_finish' );
        $res = $dao->getListByWhere();
        if ( empty( $res ) ) {
            return true;
        }


        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $count = 0;
        $member_bill_id_array = array();
        foreach ( $res as $member_bill ) {
            //如果是提现 就跳过
            if ( $member_bill->bill_expend_type == service_Member_base::bill_expend_type_withdrawals ) {
                continue;
            }
            //如果是收银台的，跳过
            if ( $member_bill->bill_type == service_Member_base::bill_type_income 
                    && $member_bill->bill_type_class == service_Member_base::bill_type_class_receivable ) {
                continue;
            }

            $dao->getDb()->startTrans();
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money+' . $member_bill->money );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $member_bill->uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
            $count++;
            $member_bill_id_array[] = $member_bill->member_bill_id;

            //更新member_bill表中的 order_finish
            $entity_MemberBill_base = new entity_MemberBill_base();
            $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
            $dao->setPk( $member_bill->member_bill_id );
            $dao->updateByPk( $entity_MemberBill_base );
            if ( $dao->getDb()->isSuccess() ) {
                $dao->getDb()->commit();
            } else {
                $dao->getDb()->rollback();
            }
        }       
        Log::getInstance( 'crontab_update_current_money' )->write( var_export( $member_bill_id_array, true ) );
        return $count;
    }

    /**
     * 更新收银台到账数据
     * @return boolean|int
     */
    public function executeMemberReceivableCurrentMoneyUpdate()
    {
        //查询出order_finish=0 AND bill_time>={$bill_time}
        $where = 'order_finish=' . service_Member_base::order_finish_no
                . ' AND order_complete=' . service_Member_base::order_complete_yes
                . ' AND bill_type=' . service_Member_base::bill_type_income
                . ' AND bill_type_class=' . service_Member_base::bill_type_class_receivable
                . ' AND bill_expend_type=' . service_Member_base::bill_expend_type_no;
        $dao = dao_factory_base::getMemberBillDao();
        $dao->setWhere( $where );
        $dao->setField( 'member_bill_id,uid,order_id,money,bill_type,bill_type_class,bill_expend_type,order_complete,order_finish' );
        $res = $dao->getListByWhere();
        if ( empty( $res ) ) {
            return true;
        }


        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $count = 0;
        $member_bill_id_array = array();
        foreach ( $res as $member_bill ) {
            $member_current_money = $this->getMemberCurrentMoney( $member_bill->uid );
            if ( $member_current_money == 0 || $member_current_money == 0.00 ) {
                continue;
            }
            $dao->getDb()->startTrans();
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->current_money = new TmacDbExpr( 'current_money+' . $member_bill->money );
            //更新卖家的金钱 商品供应商UID
            $member_setting_dao->setPk( $member_bill->uid );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
            $count++;
            $member_bill_id_array[] = $member_bill->member_bill_id;

            //更新member_bill表中的 order_finish
            $entity_MemberBill_base = new entity_MemberBill_base();
            $entity_MemberBill_base->order_finish = service_Member_base::order_finish_yes;
            $dao->setPk( $member_bill->member_bill_id );
            $dao->updateByPk( $entity_MemberBill_base );
            if ( $dao->getDb()->isSuccess() ) {
                $dao->getDb()->commit();
            } else {
                $dao->getDb()->rollback();
            }
        }        
        Log::getInstance( 'crontab_update_receivable_current_money' )->write( var_export( $member_bill_id_array, true ) );
        return $count;
    }

    /**
     * 取用户的member_setting
     */
    private function getMemberCurrentMoney( $uid )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $uid );
        $dao->setField( 'current_money' );
        $res = $dao->getInfoByPk();
        return floatval( $res->current_money );
    }

}
