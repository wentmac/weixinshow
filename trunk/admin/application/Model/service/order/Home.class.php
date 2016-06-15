<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Home.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Home_manage extends service_order_List_mobile
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取买家不同的订单环节总数
     * 待付款｜待发货｜待收货｜已完成|已关闭|已取消｜退款中的订单
     * 待付款：waiting_payment｜待发货：wating_seller_delivery｜已发货：wating_receiving｜已完成：complete｜已关闭：close｜退款中：refund
     * $this->uid;     
     */
    public function getSellerOrderCountArray()
    {
        return $array = array(
            //待付款  order_status_buyer_waiting_payment      
            'waiting_payment' => $this->getSellerOrderCount( 'order_status_buyer_waiting_payment' ),
            //待发货       order_status_buyer_wating_seller_delivery
            'wating_seller_delivery' => $this->getSellerOrderCount( 'order_status_buyer_wating_seller_delivery' ),
            //待收货       order_status_buyer_wating_receiving 
            'wating_receiving' => $this->getSellerOrderCount( 'order_status_buyer_wating_receiving' ),
            //已完成｜交易成功  order_status_buyer_complete
            'complete' => $this->getSellerOrderCount( 'order_status_buyer_complete' ),
            //交易关闭        order_status_buyer_close
            'close' => $this->getSellerOrderCount( 'order_status_buyer_close' ),
            //退款中
            'refund' => $this->getSellerOrderCount( 'order_status_buyer_refund' )
        );
    }

    private function getSellerOrderCount( $order_status )
    {

        $this->order_status = $order_status;
        $where = parent::getOrderWhere();

        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setWhere( $where );
        return $dao->getCountByWhere();
    }

    /**
     * 取卖家不同订单状态的 array
     * 等待卖家处理 waiting_seller
     * 等待买家处理 waiting_buyer
     * 银品惠客服介入 waiting_customer
     */
    public function getSellerOrderRefundCountArray()
    {
        return $array = array(
            //等待卖家处理  order_status_buyer_waiting_payment      
            'waiting_seller' => $this->getSellerOrderRefundCount( 'waiting_seller' ),
            //等待买家处理       order_status_buyer_wating_seller_delivery
            'waiting_buyer' => $this->getSellerOrderRefundCount( 'waiting_buyer' ),
            //银品惠客服介入       order_status_buyer_wating_receiving 
            'waiting_customer' => $this->getSellerOrderRefundCount( 'waiting_customer' )
        );
    }

    private function getSellerOrderRefundCount( $service_status )
    {
        $where = 'goods_uid=' . $this->uid;
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
        }


        $dao = dao_factory_base::getOrderRefundDao();
        $dao->setWhere( $where );
        return $dao->getCountByWhere();
    }

}
