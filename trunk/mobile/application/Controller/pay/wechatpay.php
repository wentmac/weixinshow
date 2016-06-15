<?php

/**
 * 前台 支付 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: wechatpay.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class wechatpayAction extends service_Controller_mobile
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
//        $data = '{"appId" : "wx2421b1c4370ec43b",     //公众号名称，由商户传入     
//           "timeStamp":" 1395712654",         //时间戳，自1970年以来的秒数     
//           "nonceStr" : "e61463f8efa94090b1f366cccfbbb444", //随机串     
//           "package" : "prepay_id=u802345jgfjsdfgsdg888",     
//           "signType" : "MD5",         //微信签名方式:     
//           "paySign" : "70EA570631E4BB79628FBCA90534C63FF7FADD89" //微信签名 
//       }';
//        $this->apiReturn($data);die;
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
            parent::headerRedirect( MOBILE_URL . 'member/order.detail?sn=' . $orderInfo->order_sn );
            exit();
            //throw new ApiException( '操作流程错误', -1, $callback );
        }
        $order_id = $orderInfo->order_id;
        //获取支付金额
        $total_fee = $orderInfo->order_amount * 100;
        $subject = $order_model->getOrderSubject();
        $body = mb_strcut( $subject, 0, 32, 'utf-8' );
        $out_trade_no = $order_id;

        //付款前判断库存
        $check_stock = $order_model->checkStockBeforePayment();
        if ( $check_stock == false ) {
            throw new ApiException( $order_model->getErrorMessage(), -1, $callback );
        }
        try {
            //获取用户openid
            $tools = new JsApiPay();
            $openId = $tools->GetOpenid();
            //统一下单

            $input = new WxPayUnifiedOrder();
            $input->SetBody( $body );
            $input->SetDetail( $subject );
            $input->SetAttach( $body . '_订单号：' . $out_trade_no );
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
            if ( $order[ 'result_code' ] == 'FAIL' ) {
                die( $order[ 'err_code_des' ] );
            }
//            echo '<pre>';
//            print_r( $order );


            $jsApiParameters = $tools->GetJsApiParameters( $order );
        } catch (WxPayException $exc) {
            echo $exc->getMessage();
        }

        $array[ 'order_subject' ] = $subject;
        $array[ 'total_amount' ] = $total_fee / 100;
        $array[ 'order_sn' ] = $order_sn;
        $this->assign( $array );
        $this->assign( 'jsApiParameters', $jsApiParameters );
        //$this->apiReturn($jsApiParameters);
        $this->V( 'wechatpay_unifiedorder' );
    }

    public function notify()
    {
        //Log::getInstance( 'mobile_order_payment_wechatpay_error' )->write( var_export( $_GET, true ) . var_export( $GLOBALS['HTTP_RAW_POST_DATA'], true ) );
        //获取通知的数据        
        /**
          $xml = '<xml><appid><![CDATA[wx7bf2888c2d9d1446]]></appid>
          <attach><![CDATA[休闲服(数量：1)_订单号：52]]></attach>
          <bank_type><![CDATA[CFT]]></bank_type>
          <cash_fee><![CDATA[1]]></cash_fee>
          <fee_type><![CDATA[CNY]]></fee_type>
          <is_subscribe><![CDATA[Y]]></is_subscribe>
          <mch_id><![CDATA[1242720702]]></mch_id>
          <nonce_str><![CDATA[svb44zon696d72s3z4mk42mw8jyzm4pv]]></nonce_str>
          <openid><![CDATA[oE9F_t8s7E9uXxZUnJmPa2hGt8Tw]]></openid>
          <out_trade_no><![CDATA[52]]></out_trade_no>
          <result_code><![CDATA[SUCCESS]]></result_code>
          <return_code><![CDATA[SUCCESS]]></return_code>
          <sign><![CDATA[70BB12081222AAFC458509F20FBF7DBD]]></sign>
          <time_end><![CDATA[20150605172355]]></time_end>
          <total_fee>1</total_fee>
          <trade_type><![CDATA[JSAPI]]></trade_type>
          <transaction_id><![CDATA[1004990300201506050211902920]]></transaction_id>
          </xml>';
         * 
         */
        $notify_reply = new WxPayNotifyReply();
        //如果返回成功则验证签名
        try {
            $xml = $GLOBALS[ 'HTTP_RAW_POST_DATA' ];
            $result = WxPayResults::Init( $xml );
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            $notify_reply->SetReturn_code( "FAIL" );
            $notify_reply->SetReturn_msg( $msg );
            die( $notify_reply->ToXml() );
        }

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。        
        if ( $result[ "return_code" ] == "FAIL" ) {
            //此处应该更新一下订单状态，商户自行增删操作            
            Log::getInstance( 'mobile_order_payment_wechatpay_error' )->write( "【通信出错】:\n" . $xml . "\n" );
        } elseif ( $result[ "result_code" ] == "FAIL" ) {
            //此处应该更新一下订单状态，商户自行增删操作
            Log::getInstance( 'mobile_order_payment_wechatpay_error' )->write( "【业务出错】:\n" . $xml . "\n" );
        } else {
            //此处应该更新一下订单状态，商户自行增删操作        
            $notify_reply->SetReturn_code( "SUCCESS" );
            $notify_reply->SetReturn_msg( "OK" );
        }

        //商户自行增加处理流程,
        //例如：更新订单状态
        //例如：数据库操作
        //例如：推送支付完成信息     
        $trade_no = $result[ 'transaction_id' ];
        $total_fee = $result[ 'total_fee' ] / 100;
        $buyer_id = $result[ 'openid' ];
        $buyer_email = $result[ 'openid' ];

        $order_model = new service_order_Payment_www();
        $order_model->setOrder_id( $result[ 'out_trade_no' ] );
        $orderInfo = $order_model->getOrderInfoById();

        if ( !$orderInfo || $orderInfo->pay_status == service_Order_base::pay_status_success ) {//订单不存在 /订单状态不等于待支付的  已经支付的
            Log::getInstance( 'mobile_order_payment_wechatpay_error' )->write( 'notify_(订单不存在 或 订单已经支付的):' . " \n" . $xml . "\n" );
            die( $notify_reply->ToXml() );
        }
        $orderInfo instanceof entity_OrderInfo_base;

        $pay_type = 0;
        //记录订单支付日志        
        $entity_PayLog_base = new entity_PayLog_base();
        $entity_PayLog_base->uid = $orderInfo->uid;
        $entity_PayLog_base->order_id = $orderInfo->order_id;
        $entity_PayLog_base->trade_no = $trade_no;
        $entity_PayLog_base->trade_vendor = service_Order_base::trade_vendor_weixin;
        $entity_PayLog_base->trade_fee = $total_fee;
        $entity_PayLog_base->pay_status = -1;
        $entity_PayLog_base->pay_time = $this->now;
        $entity_PayLog_base->pay_type = $pay_type;
        $entity_PayLog_base->buyer_id = $buyer_id;
        $entity_PayLog_base->buyer_email = $buyer_email;
        $entity_PayLog_base->pay_class = service_Order_base::pay_class_web;

        if ( $orderInfo->order_amount == $total_fee ) {
            $order_model->setTrade_vendor( service_Order_base::trade_vendor_weixin );
            $order_model->setTrade_no( $trade_no );
            $order_model->setEntity_OrderInfo( $orderInfo );
            if ( $order_model->orderPaySuccess() ) {
                $entity_PayLog_base->order_note = '支付成功';
                $entity_PayLog_base->pay_status = service_Order_base::pay_status_success;
            } else {
                $entity_PayLog_base->order_note = '支付成功 本站订单状态修改失败';
            }
        } else {
            $entity_PayLog_base->order_note = '支付成功但是支付金额错误 设为支付失败';
        }

        if ( !empty( $entity_PayLog_base->order_note ) ) {
            $entity_PayLog_base->order_note .= '-异步';
            $order_model->insertPayLog( $entity_PayLog_base );
        }
        echo $notify_reply->ToXml();
    }

    /**
     * 扫码支付给微信支付回调生成prepay_id用的
     * 在支付配置回调url
     */
    public function nativeCallback()
    {
        //Log::getInstance( 'mobile_order_payment_wechatpay_error' )->write( var_export( $_GET, true ) . var_export( $GLOBALS[ 'HTTP_RAW_POST_DATA' ], true ) . var_export( $_POST, true ) );
        /**
          $xml = '<xml><appid><![CDATA[wx7bf2888c2d9d1446]]></appid>
          <openid><![CDATA[oE9F_t8s7E9uXxZUnJmPa2hGt8Tw]]></openid>
          <mch_id><![CDATA[1242720702]]></mch_id>
          <is_subscribe><![CDATA[Y]]></is_subscribe>
          <nonce_str><![CDATA[UpXEDlqq7IxefCbe]]></nonce_str>
          <product_id><![CDATA[39]]></product_id>
          <sign><![CDATA[B9D9339A3DD95EC5C6E814878EF1DCE7]]></sign>
          </xml>';
         */
        $notify_reply = new WxPayNotifyReply();
        //如果返回成功则验证签名
        try {
            $xml = $GLOBALS[ 'HTTP_RAW_POST_DATA' ];
            $result = WxPayResults::Init( $xml );
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            $notify_reply->SetReturn_code( "FAIL" );
            $notify_reply->SetReturn_msg( $msg );
            die( $notify_reply->ToXml() );
        }
        $order_sn = $result[ 'product_id' ];

        $order_model = new service_order_Payment_www();
        $orderInfo = $order_model->getOrderInfoBySN( $order_sn );

        if ( !$orderInfo || $orderInfo->pay_status == service_Order_base::pay_status_success ) {//订单不存在 /订单状态不等于待支付的  已经支付的
            Log::getInstance( 'mobile_order_payment_wechatpay_error' )->write( 'nativeCallback_notify_(订单不存在 或 订单已经支付的):' . " \n" . $xml . "\n" );
            $notify_reply->SetReturn_code( "SUCCESS" );
            $notify_reply->SetData( 'result_code', 'FAIL' ); //业务结果
            $notify_reply->SetData( 'err_code_des', '此商品无效' ); //业务结果
            die( $notify_reply->ToXml() );
        }
        $order_id = $orderInfo->order_id;
        //统一下单       
        //获取支付金额
        $total_fee = $orderInfo->order_amount * 100;
        $subject = $order_model->getOrderSubject();
        $body = $subject;
        $out_trade_no = $order_id;
        $openId = $result[ 'openid' ];
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
        $input->SetOpenid( $openId );
        $input->SetProduct_id( $order_id );
        $order = WxPayApi::unifiedOrder( $input );
        if ( !array_key_exists( "appid", $order ) ||
                !array_key_exists( "mch_id", $order ) ||
                !array_key_exists( "prepay_id", $order ) ) {
            Log::getInstance( 'mobile_order_payment_wechatpay_error' )->write( 'nativeCallback_notify_(下单失败):' . " \n" . var_export( $order, true ) . "\n" );

            $notify_reply->SetReturn_code( "SUCCESS" );
            $notify_reply->SetData( 'result_code', 'FAIL' ); //业务结果
            $notify_reply->SetData( 'err_code_des', $order[ 'err_code_des' ] ); //业务结果

            $notify_reply->SetData( 'prepay_id', 0 ); //业务结果        
            $notify_reply->SetData( "appid", $order[ "appid" ] );
            $notify_reply->SetData( "mch_id", $order[ "mch_id" ] );
            $notify_reply->SetData( "nonce_str", WxPayApi::getNonceStr() );
            $notify_reply->SetSign();
            die( $notify_reply->ToXml() );
        }

        $prepay_id = $order[ 'prepay_id' ];
        $notify_reply->SetReturn_code( "SUCCESS" ); //返回状态码
        $notify_reply->SetData( 'result_code', 'SUCCESS' ); //业务结果
        $notify_reply->SetData( 'prepay_id', $prepay_id ); //业务结果        


        $notify_reply->SetData( "appid", $order[ "appid" ] );
        $notify_reply->SetData( "mch_id", $order[ "mch_id" ] );
        $notify_reply->SetData( "nonce_str", WxPayApi::getNonceStr() );
        $notify_reply->SetData( "err_code_des", "OK" );
        $notify_reply->SetSign();
        echo $notify_reply->ToXml();
    }

    /**
     * 微信支付退款
     * Test 将来会移到Service层中进行封装。供业务调用
     */
    public function refund()
    {
        /*         * ***********************请求参数************************* */
        //必填参数//
        //请与贵网站订单系统中的唯一订单号匹配
        $order_refund_id = Input::get( 'order_refund_id', 0 )->required( '订单商品号不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容            
        }
        $order_model = new service_order_Refund_www();
        $order_model->setOrder_refund_id( $order_refund_id );
        $order_model->setService_status( service_order_Service_base::service_status_success );
        $order_model->setRefund_status( service_order_Service_base::refund_status_seller_agree );
        $order_model->setReturn_status( service_order_Service_base::return_status_default );
        $order_model->setTrade_vendor( service_Order_base::trade_vendor_weixin );
        $total_feee = 0.01;
        $order_model->setTotal_fee( $total_feee );
        $order_model->setUid( 37 );
        $orderInfo = $order_model->checkOrderRefundPurview();
        if ( $orderInfo == false ) {
            die( $order_model->getErrorMessage() );
        }

        //商户退款单号        
        $out_refund_no = date( 'YmdHis' ) . '_' . $order_refund_id;
        $transaction_id = $orderInfo->trade_no;
        $total_fee = $total_feee * 100;
        $refund_fee = $total_feee * 100;

        //$uid = $this->memberInfo->uid;
        $uid = 1;

        try {
            $input = new WxPayRefund();
            $input->SetTransaction_id( $transaction_id );
            $input->SetTotal_fee( $total_fee );
            $input->SetRefund_fee( $refund_fee );
            $input->SetOut_refund_no( $out_refund_no );
            $input->SetOp_user_id( $uid );
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
                Log::getInstance( 'mobile_order_payment_wechatpay_refund_error' )->write( 'refund_notify_(退款微信支付返回失败):order_goods_id:' . $order_goods_id . var_export( $res, true ) );
                exit;
            }
            $batch_no = $res[ 'out_refund_no' ];
            /**
             * 订单售后 退款流程     
             * $this->trade_no;
             * $this->trade_vendor;
             * $this->batch_no;
             * $this->refund_id;
             * $this->order_goods_id;
             * $this->total_fee;
             * $this->service_status;
             * $this->refund_status;
             * $this->return_status;
             * $this->executeOrderRefund();
             * @return type
             */
            $refund_fee = $res[ 'refund_fee' ] / 100;
            $order_model->setBatch_no( $batch_no );
            $order_model->setTotal_fee( $refund_fee );
            $order_model->setTrade_no( $res[ 'transaction_id' ] );
            $order_model->setRefund_id( $res[ 'refund_id' ] );

            //设置状态为：买家收到货后，买家申请退款，卖家同意
            $order_model->setService_status( service_order_Service_base::service_status_success );
            $order_model->setRefund_status( service_order_Service_base::refund_status_seller_agree );
            $order_model->setReturn_status( service_order_Service_base::return_status_default );
            $check = $order_model->executeOrderRefund();
            if ( $check == false ) {
                Log::getInstance( 'mobile_order_payment_wechatpay_refund_error' )->write( 'notify_更新本地退款数据失败:' . $order_model->getErrorMessage() . ':order_goods_id:' . $order_goods_id . var_export( $res, true ) );
            }
            //根据原始第三方订单号 修改订单状态 记录日志等            
            $log = 'success--商户退款单号：';
            $log .= $batch_no . "---退款金额：" . $refund_fee . "---" . '微信微信退款单号:' . $res[ 'refund_id' ] . "--- order_goods_id:" . $order_goods_id . "\r\n";
            Log::getInstance( 'mobile_order_payment_wechatpay_refund' )->write( $log );
            echo $log;
        } catch (WxPayException $exc) {
            echo $exc->errorMessage();
        }
    }

    /**
     * 退款查询
     */
    public function refundquery()
    {
        //out_trade_no 商户订单号 $order_id;
        //out_refund_no 商户退款单号
        //refund_id 微信退款单号
        $transaction_id = Input::get( 'transaction_id', 0 )->required( '交易号不能为空' )->bigint();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容            
        }
        $input = new WxPayRefundQuery();
        $input->SetTransaction_id( $transaction_id );

        try {
            $res = WxPayApi::refundQuery( $input );
            echo '<pre>';
            print_r( $res );
        } catch (WxPayException $exc) {
            echo $exc->errorMessage();
        }
    }

}
