<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: RefundList.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_RefundList_base extends service_Order_base
{

    protected $where;
    protected $query_string;
    protected $image_size;
    protected $pagesize;
    protected $order_status;
    protected $uid;

    function setWhere( $where )
    {
        $this->where = $where;
    }

    function setQuery_string( $query_string )
    {
        $this->query_string = $query_string;
    }

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    function setOrder_status( $order_status )
    {
        $this->order_status = $order_status;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    public function __construct()
    {
        parent::__construct();
    }

    static public function getServiceStatusText( $service_status, $refund_status, $return_status )
    {
        $text_array = array(
            'seller' => '',
            'buyer' => ''
        );
        if ( $service_status == service_order_Service_base::service_status_waiting_seller_confirm ) {
            $text_array = array(
                'seller' => '卖家处理中',
                'buyer' => '卖家处理中'
            );
            if ( $refund_status == service_order_Service_base::refund_status_buyer_return ) {
                $text_array = array(
                    'seller' => '买家申请退款',
                    'buyer' => '卖家处理中'
                );
            } else if ( $refund_status == service_order_Service_base::refund_status_seller_agree && $return_status == service_order_Service_base::return_status_buyer_delivery ) {
                $text_array = array(
                    'seller' => '买家已经退货',
                    'buyer' => '等待卖家接收退货'
                );
            }
        } else if ( $service_status == service_order_Service_base::service_status_waiting_buyer_confirm ) {
            $text_array = array(
                'seller' => '等待买家处理',
                'buyer' => '等待买家处理'
            );
            if ( $refund_status == service_order_Service_base::refund_status_seller_agree && $return_status == service_order_Service_base::return_status_waiting_buyer_delivery ) {
                $text_array = array(
                    'seller' => '等待买家退货',
                    'buyer' => '卖家同意退款退货'
                );
            }
        } else if ( $service_status == service_order_Service_base::service_status_waiting_customer_confirm ) {
            $text_array = array(
                'seller' => '等待银品惠处理',
                'buyer' => '等待银品惠处理'
            );
        } else if ( $service_status == service_order_Service_base::service_status_success ) {
            $text_array = array(
                'seller' => '退款操作成功',
                'buyer' => '卖家同意退款'
            );
        } else if ( $service_status == service_order_Service_base::service_status_close ) {
            $text_array = array(
                'seller' => '买家撤销维权',
                'buyer' => '成功撤销维权'
            );
        }
        return $text_array;
    }

}
