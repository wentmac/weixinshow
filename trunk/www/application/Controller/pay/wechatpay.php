<?php

/**
 * 前台 支付 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: wechatpay.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class wechatpayAction extends service_Controller_www
{

    private $notify_url;

    public function _init()
    {
        require_once Tmac::findFile( 'payment/wechatpay/lib/WxPay.Api', APP_WWW_NAME, '.php' );
        require_once Tmac::findFile( 'payment/wechatpay/unit/WxPay.JsApiPay', APP_WWW_NAME, '.php' );
        $this->notify_url = MOBILE_URL . 'pay/wechatpay.notify';
    }

    /**
     * 统一下单
     * 用户在微信内进入商家H5页面，页面内调用JSSDK完成支付
     */
    public function unifiedorder()
    {
        //mobile_order_payment_wechatpay_error
        /*         * ************************请求参数************************* */
        //必填参数//
        //请与贵网站订单系统中的唯一订单号匹配
        $order_sn = Input::get( 'sn', 0 )->required( '订单号不能为空' )->bigint();
        $callback = Input::get( 'callback', 'callback' )->string();

        $order_model = new service_order_Payment_www();
        $orderInfo = $order_model->getOrderInfoBySN( $order_sn );
        if ( !$orderInfo ) {
            throw new ApiException( '没有此订单', -1, $callback );
        }
        $orderInfo instanceof entity_OrderInfo_base;
        if ( $orderInfo->order_status <> service_Order_base::order_status_buyer_order_create ) {
            //throw new ApiException( '操作流程错误', -1, $callback );
        }
        $order_id = $orderInfo->order_id;
        //获取支付金额
        $total_fee = $orderInfo->order_amount * 100;
        $subject = $order_model->getOrderSubject();
        $body = '';
        $out_trade_no = $order_id;

        //获取用户openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        //统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody( $body );
        $input->SetDetail( $subject );
        $input->SetAttach( $subject . '_订单号：' . $out_trade_no );
        $input->SetOut_trade_no( $out_trade_no );
        $input->SetTotal_fee( $total_fee );
        $input->SetTime_start( date( "YmdHis", $orderInfo->create_time ) );
        $input->SetTime_expire( date( "YmdHis", $orderInfo->create_time + 7200 ) );
        $input->SetGoods_tag( "" );
        $input->SetNotify_url( $this->notify_url );
        $input->SetTrade_type( "JSAPI" );
        $input->SetOpenid( $openId );
        $order = WxPayApi::unifiedOrder( $input );
        //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        //printf_info( $order );
        $jsApiParameters = $tools->GetJsApiParameters( $order );
        $this->apiReturn( $jsApiParameters );
    }

    public function native()
    {
        $order_sn = Input::get( 'sn', 0 )->required( '订单号不能为空' )->bigint();
        $callback = Input::get( 'callback', 'callback' )->string();

        $order_model = new service_order_Payment_www();
        $orderInfo = $order_model->getOrderInfoBySN( $order_sn );
        if ( !$orderInfo ) {
            throw new ApiException( '没有此订单', -1, $callback );
        }
        $orderInfo instanceof entity_OrderInfo_base;
        if ( $orderInfo->order_status <> service_Order_base::order_status_buyer_order_create ) {
            //throw new ApiException( '操作流程错误', -1, $callback );
        }
        $order_id = $orderInfo->order_id;
        //获取支付金额
        $total_fee = $orderInfo->order_amount * 100;
        $subject = $order_model->getOrderSubject();
        $body = mb_strcut( $subject, 0, 32, 'utf-8' );
        $out_trade_no = $order_id;

        //获取用户openid


        require_once Tmac::findFile( 'payment/wechatpay/lib/WxPay.Api', APP_WWW_NAME, '.php' );
        require_once Tmac::findFile( 'payment/wechatpay/unit/WxPay.NativePay', APP_WWW_NAME, '.php' );
        $tools = new NativePay();
        /**
          //模式一
          try {
          $notify = new NativePay();
          $qrcode_url = $notify->GetPrePayUrl( $order_sn );
          } catch (WxPayException $exc) {
          $qrcode_url = '';
          die( $exc->getMessage() );
          }
         * 
         */
        try {
            //统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody( $body );
            $input->SetDetail( $subject );
            $input->SetAttach( $subject . '_订单号：' . $out_trade_no );
            $input->SetOut_trade_no( $out_trade_no );
            $input->SetTotal_fee( $total_fee );
            $input->SetTime_start( date( "YmdHis", $orderInfo->create_time ) );
            $input->SetTime_expire( date( "YmdHis", $orderInfo->create_time + 7200 ) );
            $input->SetGoods_tag( "" );
            $input->SetNotify_url( $this->notify_url );
            $input->SetTrade_type( "NATIVE" );
            $input->SetProduct_id( $out_trade_no );
            $result = $tools->GetPayUrl( $input );
            $qrcode_url = $result[ "code_url" ];
        } catch (WxPayException $exc) {
            $qrcode_url = '';
            die( $exc->getMessage() );
        }
        $array[ 'qrcode_url' ] = $qrcode_url;
        $array[ 'orderInfo' ] = $orderInfo;
        $array[ 'total_fee' ] = $total_fee;
        $array[ 'subject' ] = $subject;
        $this->assign( $array );
        $this->V( 'wechatpay_native' );
    }

    public function notify()
    {
        
    }

}
