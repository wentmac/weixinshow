<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_payment_Wechatpay_base extends service_Payment_base
{

    public function __construct()
    {
        parent::__construct();
        require_once Tmac::findFile( 'payment/wechatpay/lib/WxPay.Api', APP_WWW_NAME, '.php' );
    }

    /**
     * 执行支付宝异步退款操作
     * $this->order_refund_id;
     * $this->money;
     * $this->orderInfo;
     * $this->refund();
     */
    public function refund()
    {
        if ( $this->orderInfo->demo_order == service_Order_base::demo_order_yes ) {
            return $this->demoOrderRefund();
        }
        $orderInfo = $this->orderInfo;
        $orderInfo instanceof entity_OrderInfo_base;
        //商户退款单号        
        $out_refund_no = date( 'YmdHis' ) . '_' . $this->order_refund_id;
        $transaction_id = $orderInfo->trade_no;
        $total_fee = $orderInfo->order_amount * 100;
        $refund_fee = $this->money * 100;

        try {
            $input = new WxPayRefund();
            $input->SetTransaction_id( $transaction_id );
            $input->SetTotal_fee( $total_fee );
            $input->SetRefund_fee( $refund_fee );
            $input->SetOut_refund_no( $out_refund_no );
            $input->SetOp_user_id( $orderInfo->goods_uid );
            $res = WxPayApi::refund( $input );
            /**
              Array
              (
              [appid] => wx7bf2888c2d9d1446
              [cash_fee] => 1
              [cash_refund_fee] => 1
              [coupon_refund_count] => 0
              [coupon_refund_fee] => 0
              [mch_id] => 1242720702
              [nonce_str] => qYpT8cE4PIXASphC
              [out_refund_no] => 20150606180927_39
              [out_trade_no] => 39
              [refund_channel] => Array
              (
              )

              [refund_fee] => 1
              [refund_id] => 2004990300201506060008590726
              [result_code] => SUCCESS
              [return_code] => SUCCESS
              [return_msg] => OK
              [sign] => 3984F47E37247456D96D0C2235942109
              [total_fee] => 1
              [transaction_id] => 1004990300201506060215725549
              )
             */
            if ( $res[ 'result_code' ] == 'FAIL' || $res[ 'return_code' ] == 'FAIL' ) {
                Log::getInstance( 'mobile_order_payment_wechatpay_refund_error' )->write( 'refund_notify_(退款微信支付返回失败):order_refund_id:' . $this->order_refund_id . var_export( $res, true ) );
                exit;
            }
            $batch_no = $res[ 'out_refund_no' ];
            /**
             * 订单售后 退款流程     
             * $this->trade_no;
             * $this->trade_vendor;
             * $this->batch_no;
             * $this->refund_id;     
             * $this->order_refund_id；
             * $this->total_fee;     
             * $this->service_status;
             * $this->refund_status;
             * $this->return_status;  
             * $this->executeOrderRefund();
             * @return type
             */
            $refund_fee = $res[ 'refund_fee' ] / 100;
            $order_model = new service_order_Refund_base();
            $order_model->setBatch_no( $batch_no );
            $order_model->setTotal_fee( $refund_fee );
            $order_model->setTrade_no( $res[ 'transaction_id' ] );
            $order_model->setTrade_vendor( service_Order_base::trade_vendor_weixin );
            $order_model->setRefund_id( $res[ 'refund_id' ] );
            $order_model->setOrder_refund_id( $this->order_refund_id );

            //设置状态为：买家收到货后，买家申请退款，卖家同意            
            $check = $order_model->executeOrderRefund();
            if ( $check == false ) {
                Log::getInstance( 'mobile_order_payment_wechatpay_refund_error' )->write( 'notify_更新本地退款数据失败:' . $order_model->getErrorMessage() . ':order_refund_id:' . $this->order_refund_id . var_export( $res, true ) );
            }
            //根据原始第三方订单号 修改订单状态 记录日志等            
            $log = 'success--商户退款单号：';
            $log .= $batch_no . "---退款金额：" . $refund_fee . "---" . '微信微信退款单号:' . $res[ 'refund_id' ] . "--- order_refund_id:" . $this->order_refund_id . "\r\n";
            Log::getInstance( 'mobile_order_payment_wechatpay_refund' )->write( $log );
            return true;
        } catch (WxPayException $exc) {
            $this->errorMessage = $exc->errorMessage();
            return FALSE;
        }
    }

    /**
     * 演示订单的退款执行
     */
    private function demoOrderRefund()
    {
        //商户退款单号        
        $out_refund_no = date( 'YmdHis' ) . '_' . $this->order_refund_id;
        $transaction_id = $this->orderInfo->trade_no;
        $total_fee = $this->orderInfo->order_amount * 100;
        $refund_fee = $this->money * 100;
        $batch_no = $out_refund_no;
        $refund_id = '20020507702015081200273688';
        /**
         * 订单售后 退款流程     
         * $this->trade_no;
         * $this->trade_vendor;
         * $this->batch_no;
         * $this->refund_id;     
         * $this->order_refund_id；
         * $this->total_fee;     
         * $this->service_status;
         * $this->refund_status;
         * $this->return_status;  
         * $this->executeOrderRefund();
         * @return type
         */
        $refund_fee = $refund_fee / 100;
        $order_model = new service_order_Refund_base();
        $order_model->setBatch_no( $batch_no );
        $order_model->setTotal_fee( $refund_fee );
        $order_model->setTrade_no( $transaction_id );
        $order_model->setTrade_vendor( service_Order_base::trade_vendor_weixin );
        $order_model->setRefund_id( $refund_id );
        $order_model->setOrder_refund_id( $this->order_refund_id );

        //设置状态为：买家收到货后，买家申请退款，卖家同意            
        $check = $order_model->executeOrderRefund();
        if ( $check == false ) {
            Log::getInstance( 'mobile_order_payment_wechatpay_refund_error' )->write( 'notify_更新本地退款数据失败:' . $order_model->getErrorMessage() . ':order_refund_id:' . $this->order_refund_id );
            return false;
        }
        //根据原始第三方订单号 修改订单状态 记录日志等            
        $log = 'success--商户退款单号：';
        $log .= $batch_no . "---退款金额：" . $refund_fee . "---" . '微信微信退款单号:' . $refund_id . "--- order_refund_id:" . $this->order_refund_id . "\r\n";
        Log::getInstance( 'mobile_order_payment_wechatpay_refund' )->write( $log );
        return true;
    }

    /**
     * 房客取消时 退款给房客
     * @param type $order_id //退款金额
     * @param type $price //退款金额
     * @return type 
     */
    public function tenantRefund( $order_id, $trade_no, $price )
    {
        $alipay_target = "https://mapi.alipay.com/gateway.do";

        $alipay_merchant_id = $this->alipay_config[ 'partner' ];   //*  商家用户编号		
        $alipay_key = $this->alipay_config[ 'key' ];   //* 

        $service = 'refund_fastpay_by_platform_nopwd';
        $sign_type = 'MD5';
        $batch_num = 1;
        $batch_no = date( 'Y' ) . date( 'm' ) . date( 'd' ) . date( 'h' ) . date( 'i' ) . date( 's' ) . $order_id;
        $refund_date = date( "Y-m-d h:i:s" );
        $detail_data = $trade_no . '^' . $price . '^' . '取消订单退款';
        $notify_url = $this->alipay_config[ 'refund_auto_url' ];
        $alistr = "_input_charset=utf-8&batch_no=$batch_no&batch_num=$batch_num&detail_data=$detail_data&notify_url=$notify_url&partner=$alipay_merchant_id&refund_date=$refund_date&service=$service";
        $sign = md5( $alistr . $alipay_key );
        $aliUrl = $alipay_target . '?' . $alistr . "&sign=$sign&sign_type=$sign_type";
        //获取XML返回值
        $xmlInfo = simplexml_load_file( $aliUrl );
        $restul = $xmlInfo->is_success;

        if ( $restul == 'T' ) {
            return 'T';
        } else {
            return false;
        }
    }

}
