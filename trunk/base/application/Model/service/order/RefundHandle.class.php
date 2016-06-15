<?php

/**
 * api 会员账户 管理模块 Model
 * 单个订单商品退款
 * 整个订单在未发货前退款
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: RefundHandle.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_RefundHandle_base extends service_order_RefundDetail_base
{

    protected $refund_status_text;

    function getRefund_status_text()
    {
        return $this->refund_status_text;
    }

    public function __construct()
    {
        parent::__construct();
    }

}
