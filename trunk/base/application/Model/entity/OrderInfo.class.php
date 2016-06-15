<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: OrderInfo.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of OrderInfo.class.php
 *
 * @author Tracy McGrady
 */
class entity_OrderInfo_base
{
    public $order_id;
    public $order_sn;
    public $uid;
    public $order_status;
    public $shipping_status;
    public $pay_status;
    public $refund_status;
    public $close_status;
    public $comment_status;
    public $order_type;
    public $consignee;
    public $mobile;
    public $address_id;
    public $country;
    public $province;
    public $city;
    public $district;
    public $full_address;
    public $address;
    public $postscript;
    public $weixin_id;
    public $trade_no;
    public $trade_vendor;
    public $shipping_fee;
    public $order_total_price;
    public $order_payable_amount;
    public $order_amount;
    public $order_integral_amount;
    public $commission_fee;
    public $commission_fee_rank;
    public $referer;
    public $create_time;
    public $confirm_time;
    public $pay_time;
    public $shipping_time;
    public $confirm_deadline_time;
    public $express_id;
    public $express_code;
    public $express_name;
    public $express_no;
    public $to_buyer;
    public $order_note;
    public $item_uid;
    public $item_mobile;
    public $shop_name;
    public $supplier_mobile;
    public $item_weixin_id;
    public $order_goods_detail;
    public $have_return_service;
    public $order_refund_id;
    public $goods_uid;
    public $demo_order;
    public $agent_uid;
    public $rank_uid;
    public $coupon_code;
    public $coupon_money;
    public $goods_member_level;
    public $goods_member_level_refund;
    public $is_delete;
}