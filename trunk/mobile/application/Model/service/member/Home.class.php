<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Home.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_Home_mobile extends service_Member_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取买家不同的订单环节总数
     * 待付款｜待发货｜待收货｜已完成|已关闭|已取消｜退款中的订单
     */
    public function getBuyerOrderCountArray()
    {
        return $array = array(
            //待付款  order_status_buyer_waiting_payment      
            'order_status_buyer_waiting_payment' => $this->getBuyerOrderCount( 'order_status_buyer_waiting_payment' ),
            //待发货       order_status_buyer_wating_seller_delivery
            'order_status_buyer_wating_seller_delivery' => $this->getBuyerOrderCount( 'order_status_buyer_wating_seller_delivery' ),
            //待收货       order_status_buyer_wating_receiving 
            'order_status_buyer_wating_receiving' => $this->getBuyerOrderCount( 'order_status_buyer_wating_receiving' ),
            //待评论
            'order_status_buyer_wating_comment' => $this->getBuyerOrderCount( 'order_status_buyer_wating_comment' ),
            //已完成｜交易成功  order_status_buyer_complete
            'order_status_buyer_complete' => $this->getBuyerOrderCount( 'order_status_buyer_complete' ),
            //交易关闭        order_status_buyer_close
            'order_status_buyer_close' => $this->getBuyerOrderCount( 'order_status_buyer_close' )
        );
    }

    /**
     * 取买家未付款订单总数     
     */
    public function getBuyerUnpayOrderCountArray()
    {
        return $this->getBuyerOrderCount( 'order_status_buyer_waiting_payment' );
    }

    private function getBuyerOrderCount( $order_status )
    {
        $order_list_model = new service_order_List_mobile();
        $order_list_model->setUid( $this->uid );

        $order_list_model->setOrder_status( $order_status );
        $where = $order_list_model->getOrderListWhere();
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setWhere( $where );
        return $dao->getCountByWhere();
    }

    /**
     * 取买家不同订单状态的 array
     * 等待卖家处理 waiting_seller
     * 等待买家处理 waiting_buyer
     * 银品惠客服介入 waiting_customer
     */
    public function getBuyerOrderRefundCountArray()
    {
        return $array = array(
            //等待卖家处理  order_status_buyer_waiting_payment      
            'waiting_seller' => $this->getBuyerOrderRefundCount( 'waiting_seller' ),
            //等待买家处理       order_status_buyer_wating_seller_delivery
            'waiting_buyer' => $this->getBuyerOrderRefundCount( 'waiting_buyer' ),
            //银品惠客服介入       order_status_buyer_wating_receiving 
            'waiting_customer' => $this->getBuyerOrderRefundCount( 'waiting_customer' ),
            //完成       order_status_buyer_wating_receiving 
            'complate' => $this->getBuyerOrderRefundCount( 'complate' ),
            //关闭       order_status_buyer_wating_receiving 
            'close' => $this->getBuyerOrderRefundCount( 'close' )
        );
    }

    private function getBuyerOrderRefundCount( $service_status )
    {
        $where = 'uid=' . $this->uid;
        switch ( $service_status )
        {
            case 'waiting_seller':
            default:
                $where .= ' AND service_status=' . service_order_Service_base::service_status_waiting_seller_confirm;
                break;

            case 'waiting_buyer':
                $where .= ' AND service_status=' . service_order_Service_base::service_status_waiting_buyer_confirm;
                break;

            case 'waiting_customer':
                $where .= ' AND service_status=' . service_order_Service_base::service_status_waiting_customer_confirm;
                break;
            case 'complate':
                $where .= ' AND service_status=' . service_order_Service_base::service_status_success;
                break;
            case 'close':
                $where .= ' AND service_status=' . service_order_Service_base::service_status_close;
                break;
        }


        $dao = dao_factory_base::getOrderRefundDao();
        $dao->setWhere( $where );
        return $dao->getCountByWhere();
    }

}
