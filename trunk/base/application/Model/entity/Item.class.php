<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Item.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of Item.class.php
 *
 * @author Tracy McGrady
 */
class entity_Item_base
{
    public $item_id;
    public $goods_id;
    public $item_cat_id;
    public $item_name;
    public $item_stock;
    public $item_price;
    public $outer_code;
    public $brand_id;
    public $brand_name;
    public $goods_image_id;
    public $item_sort;
    public $item_time;
    public $item_modify_time;
    public $comment_count;
    public $click_count;
    public $is_on_sale;
    public $sales_volume;
    public $uid;
    public $goods_uid;
    public $collect_count;
    public $collect_count_variable;
    public $recommend;
    public $shipping_fee;
    public $commission_fee;
    public $commission_seller_different;
    public $commission_different_object;
    public $supplier_offline;
    public $goods_type;
    public $is_integral;
    public $is_self;
    public $is_delete;
}