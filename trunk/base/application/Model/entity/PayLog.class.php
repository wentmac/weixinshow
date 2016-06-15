<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: PayLog.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of PayLog.class.php
 *
 * @author Tracy McGrady
 */
class entity_PayLog_base
{
    public $pay_log_id;
    public $uid;
    public $order_id;
    public $trade_no;
    public $trade_vendor;
    public $order_note;
    public $trade_fee;
    public $pay_status;
    public $pay_time;
    public $pay_type;
    public $buyer_email;
    public $buyer_id;
    public $pay_class;
}