<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: OrderGoods.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of OrderGoods.class.php
 *
 * @author Tracy McGrady
 */
class entity_OrderGoods_base
{
    public $order_goods_id;
    public $order_id;
    public $receivable_id;
    public $goods_id;
    public $item_id;
    public $item_name;
    public $item_number;
    public $item_total_price;
    public $item_price;
    public $outer_code;
    public $goods_image_id;
    public $goods_sku_id;
    public $goods_sku_name;
    public $comment_status;
    public $order_refund_id;
    public $commission_fee;
    public $commission_fee_rank;
    public $goods_member_level;
    public $goods_type;
    public $is_integral;
    public $order_integral_amount;
}