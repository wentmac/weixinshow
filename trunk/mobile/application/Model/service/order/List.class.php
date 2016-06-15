<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_List_mobile extends service_order_List_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取订单列表的where语句
     * $this->order_status;
     * $this->uid;
     * $this->getOrderListWhere();
     */
    public function getOrderListWhere()
    {
        $where = "uid={$this->uid}";        
        switch ( $this->order_status )
        {
            /**
             * 买家订单状态
             * 待付款
             */
            case 'order_status_buyer_waiting_payment':
                $where .= " AND order_status=" . service_Order_base::order_status_buyer_order_create
                        . " AND is_delete=0";
                break;
            /**
             * 买家订单状态
             * 待发货
             * 买家等待卖家发货
             */
            case 'order_status_buyer_wating_seller_delivery':
                $where .= " AND order_status=" . service_Order_base::order_status_buyer_payment
                        . " AND is_delete=0";
                break;
            /**
             * 买家订单状态
             * 待收货
             * 买家等待收货
             */
            case 'order_status_buyer_wating_receiving':
                $where .= " AND order_status=" . service_Order_base::order_status_seller_delivery
                        . " AND is_delete=0";
                break;
            /**
             * 买家订单状态
             * 已经完成|买家已收货|待评价
             * 订单已完成
             */
            case 'order_status_buyer_wating_comment':
                $where .= " AND order_status=" . service_Order_base::order_status_complete
                        . " AND is_delete=0"
                        . " AND comment_status=0";
                break;

            /**
             * 买家订单状态
             * 已经完成|买家已收货
             * 订单已完成
             */
            case 'order_status_buyer_complete':
                $where .= " AND order_status=" . service_Order_base::order_status_complete
                        . " AND is_delete=0";
                break;
            /**
             * 买家订单状态
             * 交易关闭
             * 订单无效关闭
             */
            case 'order_status_buyer_close':
                $where .= " AND order_status=" . service_Order_base::order_status_close
                        . " AND is_delete=0";
                break;

            default:
                $where .= " AND is_delete=0";
                break;
        }
        return $where;
    }

}
