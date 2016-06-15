<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Coupon.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of Coupon.class.php
 *
 * @author Tracy McGrady
 */
class entity_Coupon_base
{
    public $coupon_id;
    public $uid;
    public $coupon_code;
    public $coupon_money;
    public $coupon_status;
    public $order_id;
    public $order_sn;
    public $create_time;
    public $use_time;
}