<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: MemberBill.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of MemberBill.class.php
 *
 * @author Tracy McGrady
 */
class entity_MemberBill_base
{
    public $member_bill_id;
    public $uid;
    public $order_id;
    public $money;
    public $bill_type;
    public $bill_type_class;
    public $bill_expend_type;
    public $bill_note;
    public $bill_time;
    public $is_execute;
    public $execute_time;
    public $confirm_time;
    public $trade_vendor;
    public $trade_no;
    public $batch_no;
    public $refund_id;
    public $order_complete;
    public $order_finish;
    public $bill_uid;
    public $bill_realname;
    public $bill_image_id;
}