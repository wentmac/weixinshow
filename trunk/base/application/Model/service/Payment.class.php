<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
abstract class service_Payment_base extends service_Model_base
{

    protected $order_refund_id;
    protected $money;
    protected $orderInfo;
    protected $errorMessage;

    function setOrder_refund_id( $order_refund_id )
    {
        $this->order_refund_id = $order_refund_id;
    }

    function setMoney( $money )
    {
        $this->money = $money;
    }

    function setOrderInfo( $orderInfo )
    {
        $this->orderInfo = $orderInfo;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 工厂创建
     * @param type $source
     * @return type 
     */
    public static function factory( $trade_vendor = 1 )
    {
        $tradeVendorConfig = Tmac::config( 'payment.payment.trade_vendor', APP_BASE_NAME );
        $vendor = $tradeVendorConfig[ $trade_vendor ];
        $model = 'payment/' . $vendor;
        return Tmac::model( $model, APP_BASE_NAME );
    }

}
