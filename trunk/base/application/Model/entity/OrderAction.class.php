<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: OrderAction.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of OrderAction.class.php
 *
 * @author Tracy McGrady
 */
class entity_OrderAction_base
{
    public $order_action_id;
    public $order_id;
    public $action_uid;
    public $action_username;
    public $order_status;
    public $shipping_status;
    public $pay_status;
    public $refund_status;
    public $action_note;
    public $action_time;
}