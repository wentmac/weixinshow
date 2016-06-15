<?php

/**
 * crontab 批量更新
 * 过期订单的付款状态
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Confirm.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Confirm_crontab extends service_Order_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function executeOrderConfirm()
    {
        //查询出order_status=3
        $where = 'order_status=' . service_Order_base::order_status_seller_delivery . ' AND confirm_deadline_time<=' . $this->now;
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setWhere( $where );
        $dao->setField( 'order_id,order_sn,uid,confirm_deadline_time' );
        $res = $dao->getListByWhere();
        if ( empty( $res ) ) {
            return true;
        }
        $model = new service_order_BuyerHandle_mobile();

        $fail_order_id_string = '';
        foreach ( $res as $order_info ) {
            //判断时间
            if ( $order_info->confirm_deadline_time > $this->now ) {
                continue;
            }
            $model = new service_order_BuyerHandle_mobile();
            $model->setUid( $order_info->uid );
            $model->setOrder_sn( $order_info->order_sn );
            $model->setAuto_confirm_status( true );
            try {
                $res = $model->confirmOrderInfo();
                if ( $res == false ) {
                    $fail_order_id_string.=',' . $order_info->order_id;
                }
            } catch (TmacClassException $exc) {
                throw new ApiException( $exc->getMessage() );
            }            
        }
        if ( !empty( $fail_order_id_string ) ) {
            Log::getInstance( 'crontab_auto_order_confirm' )->write( $fail_order_id_string );
        }
        return true;
    }

}
