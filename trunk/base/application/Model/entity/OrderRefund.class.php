<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: OrderRefund.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of OrderRefund.class.php
 *
 * @author Tracy McGrady
 */
class entity_OrderRefund_base
{
    public $order_refund_id;
    public $order_id;
    public $order_sn;
    public $order_goods_id;
    public $order_goods_detail;
    public $item_uid;
    public $item_mobile;
    public $shop_name;
    public $goods_uid;
    public $supplier_mobile;
    public $order_type;
    public $refund_service_status;
    public $refund_service_reason;
    public $money;
    public $refund_note;
    public $refund_images;
    public $uid;
    public $consignee;
    public $mobile;
    public $weixin_id;
    public $item_weixin_id;
    public $service_status;
    public $refund_status;
    public $return_status;
    public $service_level;
    public $service_note;
    public $refund_ing;
    public $goods_member_level;
    public $agent_uid;
    public $rank_uid;
    public $commission_fee;
    public $commission_fee_rank;
    public $refund_time;
}