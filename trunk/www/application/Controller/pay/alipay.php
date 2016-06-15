<?php

/**
 * 前台 支付 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: alipay.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class alipayAction extends Action
{

    private $alipay_config;

    public function _init()
    {
        $this->alipay_config = Tmac::config( 'alipay.alipay_config', APP_WWW_NAME );
        require_once Tmac::findFile( 'payment/alipay/alipay_notify', APP_WWW_NAME );
        require_once Tmac::findFile( 'payment/alipay/alipay_submit', APP_WWW_NAME );
    }

    /**
     * 即时到帐接口接入页
     * *************************注意*************************
     * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
     * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
     * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
     * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
     * 如果不想使用扩展功能请把扩展功能参数赋空值。
     */
    public function alipayto()
    {
        /*         * ************************请求参数************************* */
        //必填参数//
        //请与贵网站订单系统中的唯一订单号匹配
        $order_sn = Input::get( 'sn', 0 )->required( '订单号不能为空' )->bigint();

        $order_model = new service_order_Payment_www();
        $orderInfo = $order_model->getOrderInfoBySN( $order_sn );
        if ( !$orderInfo ) {
            die( $this->redirect( '没有此订单' ) );
        }
        $orderInfo instanceof entity_OrderInfo_base;
        if ( $orderInfo->order_status <> service_Order_base::order_status_buyer_order_create ) {
            die( $this->redirect( '操作流程错误' ) );
        }
        $order_id = $orderInfo->order_id;
        //获取支付金额
        $total_fee = $orderInfo->order_amount;
        $subject = $order_model->getOrderSubject();
        $body = '';
        $out_trade_no = $order_id;
        $show_url = ''; //商品展示地址
        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数
        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
        //默认支付方式，取值见“即时到帐接口”技术文档中的请求参数列表
        $paymethod = '';
        //默认网银代号，代号列表见“即时到帐接口”技术文档“附录”→“银行列表”
        $defaultbank = '';
        //自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
        $extra_common_param = '';

        //扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
        $royalty_type = "";   //提成类型，该值为固定值：10，不需要修改
        $royalty_parameters = "";
        // if ($use == 1) {
        //     $couponPrice = $this->order_action_model->setOrderInfo($orderInfo)->setCouponOfKLZ();
        //     if ($couponPrice) {
        //         $total_fee = $orderInfo->goods_amount - $couponPrice;
        //     }
        // }        
        //构造要请求的参数数组
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => $this->alipay_config[ 'partner' ],
            "seller_email" => $this->alipay_config[ 'seller_email' ],
            "payment_type" => "1", //支付类型
            "notify_url" => $this->alipay_config[ 'notify_url' ],
            "return_url" => $this->alipay_config[ 'return_url' ],
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => strtolower( $this->alipay_config[ 'input_charset' ] ),
            "paymethod" => $paymethod,
            "defaultbank" => $defaultbank,
            "extra_common_param" => $extra_common_param,
            "royalty_type" => $royalty_type,
            "royalty_parameters" => $royalty_parameters
        );
        $qrpay = 1;
        //支付宝扫码支付
        if ( $qrpay == 1 ) {
            $parameter[ "qr_pay_mode" ] = 2;
        }
        //建立请求                
        $alipaySubmit = new AlipaySubmit( $this->alipay_config );
        //$html_text = $alipaySubmit->buildRequestForm( $parameter, 'get', '确认' );
        $html_text = $alipaySubmit->buildRequestParaToString( $parameter );
        parent::headerRedirect( $html_text );
    }

    /**
     * 服务器异步通知页面文件
     * ************************页面功能说明*************************
     * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
     * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
     * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
     * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知

     * TRADE_FINISHED(表示交易已经成功结束，并不能再对该交易做后续操作);
     * TRADE_SUCCESS(表示交易已经成功结束，可以对该交易做后续操作，如：分润、退款等);
     */
    public function notify()
    {
        /**
          [2015-05-29 17:56:36]  --> array (
          'discount' => '0.00',
          'payment_type' => '1',
          'subject' => 'Optimum英式橄榄球五号训练球(数量：3)',
          'trade_no' => '2015052900001000570066991893',
          'buyer_email' => 'zwt007@gmail.com',
          'gmt_create' => '2015-05-29 17:56:04',
          'notify_type' => 'trade_status_sync',
          'quantity' => '1',
          'out_trade_no' => '38',
          'seller_id' => '2088411147045803',
          'notify_time' => '2015-05-29 17:56:36',
          'trade_status' => 'TRADE_SUCCESS',
          'is_total_fee_adjust' => 'N',
          'total_fee' => '0.01',
          'gmt_payment' => '2015-05-29 17:56:36',
          'seller_email' => 'admin@9580.com',
          'price' => '0.01',
          'buyer_id' => '2088002388365571',
          'notify_id' => '991bb9371c3a5f5c139768cbe05a18ad56',
          'use_coupon' => 'N',
          'sign_type' => 'MD5',
          'sign' => '08d7c8b8bcf9571fac1fef4eeeb60ebb',
          )
         */
        $order_id = Input::post( 'out_trade_no', 0 )->required( '商品ID不能为空' )->int();
        $trade_no = Input::post( 'trade_no', '' )->required( '支付宝订单号不能为空' )->bigint();
        $total_fee = Input::post( 'total_fee', 0 )->required( '支付总价不能为空' )->float();
        $buyer_id = Input::post( 'buyer_id', 0 )->required( '支付用户ID不能为空' )->bigint();
        $buyer_email = Input::post( 'buyer_email', '' )->required( '支付用户EMAIL不能为空' )->string();
        $trade_status = Input::post( 'trade_status', '' )->required( '状态状态不能为空' )->string();
        $business_scene = Input::post( 'business_scene', '' )->string();     //是否扫码支付

        if ( Filter::getStatus() === false ) {
            //throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
            Log::getInstance( 'mobile_order_payment_error' )->write( 'notify_参数失败_:' . Filter::getFailMessage() . '|' . var_export( $_POST, true ) );
            die( 'fail' );
        }
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify( $this->alipay_config );
        $verify_result = $alipayNotify->verifyNotify();

        if ( $verify_result ) {//验证成功
            $order_model = new service_order_Payment_www();
            $order_model->setOrder_id( $order_id );
            $orderInfo = $order_model->getOrderInfoById();

            if ( !$orderInfo || $orderInfo->pay_status == service_Order_base::pay_status_success ) {//订单不存在 /订单状态不等于待支付的  已经支付的
                Log::getInstance( 'mobile_order_payment_error' )->write( 'notify_(订单不存在 或 订单已经支付的):' . var_export( $_POST, true ) );
                die( 'success' );
            }
            $orderInfo instanceof entity_OrderInfo_base;

            $pay_type = 0;
            if ( $business_scene == 'qrpay' ) {
                $pay_type = 1;
            }
            //记录订单支付日志        
            $entity_PayLog_base = new entity_PayLog_base();
            $entity_PayLog_base->uid = $orderInfo->uid;
            $entity_PayLog_base->order_id = $orderInfo->order_id;
            $entity_PayLog_base->trade_no = $trade_no;
            $entity_PayLog_base->trade_vendor = service_Order_base::trade_vendor_alipay;
            $entity_PayLog_base->trade_fee = $total_fee;
            $entity_PayLog_base->pay_status = -1;
            $entity_PayLog_base->pay_time = time();
            $entity_PayLog_base->pay_type = $pay_type;
            $entity_PayLog_base->buyer_id = $buyer_id;
            $entity_PayLog_base->buyer_email = $buyer_email;
            $entity_PayLog_base->pay_class = service_Order_base::pay_class_web;

            if ( $trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' ) {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //该种交易状态只在两种情况下出现
                //1、开通了普通即时到账，买家付款成功后。
                //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");                                    
                if ( $orderInfo->order_amount == $total_fee ) {
                    $order_model->setTrade_vendor( service_Order_base::trade_vendor_alipay );
                    $order_model->setTrade_no( $trade_no );
                    $order_model->setEntity_OrderInfo( $orderInfo );
                    if ( $order_model->orderPaySuccess() ) {
                        $entity_PayLog_base->order_note = '支付成功';
                        $entity_PayLog_base->pay_status = service_Order_base::pay_status_success;
                    } else {
                        $entity_PayLog_base->order_note = '支付成功 本站订单状态修改失败';
                        Log::getInstance( 'mobile_order_payment_error' )->write( 'notify_(支付成功 本站订单状态修改失败):' . var_export( $_POST, true ) );
                    }
                } else {
                    $entity_PayLog_base->order_note = '支付成功但是支付金额错误 设为支付失败';
                }
            } else {
                $entity_PayLog_base->order_note = '支付失败';
            }
            echo "success";  //请不要修改或删除
        } else {//验证失败
            //验证失败
            Log::getInstance( 'mobile_order_payment_error' )->write( 'notify_(notify_data的notify参数为空):' . var_export( $_POST, true ) );
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }

        if ( !empty( $entity_PayLog_base->order_note ) ) {
            $entity_PayLog_base->order_note .= '-异步';
            $order_model->insertPayLog( $entity_PayLog_base );
        }
    }

    /**
     * 页面跳转同步通知文件
     * ************************页面功能说明*************************
     * 该页面可在本机电脑测试
     * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
     * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn

     * TRADE_FINISHED(表示交易已经成功结束，并不能再对该交易做后续操作);
     * TRADE_SUCCESS(表示交易已经成功结束，可以对该交易做后续操作，如：分润、退款等);
     */
    public function returnurl()
    {
        /**
          echo '<pre>';
          print_r($_GET);
          print_r($_POST);
          Log::getInstance( 'mobile_order_payment_error' )->write( var_export( $_GET, true ) );
          Log::getInstance( 'mobile_order_payment_error' )->write( var_export( $_POST, true ) );
          die;
         * 
         */
        // $check_model = Tmac::model('check', APP_USER_NAME);
        // $check_model->require_login();  
        $url = INDEX_URL . 'order/fail';
        if ( empty( $_GET[ 'sign' ] ) || empty( $_GET[ 'trade_status' ] ) || empty( $_GET[ 'out_trade_no' ] ) || empty( $_GET[ 'trade_no' ] ) ) {
            parent::headerRedirect( $url . '?error=参数不正确' );
        }
        unset( $_GET[ 'TMAC_ACTION' ] );
        unset( $_GET[ 'TMAC_CONTROLLER' ] );
        unset( $_GET[ 'TMAC_CONTROLLER_FILE' ] );
        //计算得出通知验证结果                
        $alipayNotify = new AlipayNotify( $this->alipay_config );
        $verify_result = $alipayNotify->verifyReturn();

        //商户订单号
        $order_id = Input::get( 'out_trade_no', 0 )->int();

        //支付宝交易号
        $trade_no = $_GET[ 'trade_no' ];
        $trade_status = $_GET[ 'trade_status' ];

        if ( $verify_result ) {//验证成功
            if ( $trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' ) {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                $order_model = new service_order_Payment_www();
                $order_model->setOrder_id( $order_id );
                $orderInfo = $order_model->getOrderInfoById();

                $member_mall_model = new service_MemberMall_mall();
                $member_mall_info = $member_mall_model->getMemberMallInfoByUid( $orderInfo->item_uid );

                if ( $member_mall_info ) {
                    $mall_url = 'http://'.$member_mall_info->mall_domain.'/';
                } else {
                    $mall_url = INDEX_URL;
                }

                $url = $mall_url . 'order/fail';
                if ( !$orderInfo ) {
                    $url.='?error=订单不存在';
                    parent::headerRedirect( $url );
                }
                $url = $mall_url . 'order/success?sn=' . $orderInfo->order_sn;
                parent::headerRedirect( $url );
            }
            $url.='?error=支付失败';
            parent::headerRedirect( $url );
        }
        $url.='?error=验证失败';
        parent::headerRedirect( $url );
    }

    /**
     * 即时到帐接口接入页
     * *************************注意*************************
     * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
     * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
     * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
     * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
     * 如果不想使用扩展功能请把扩展功能参数赋空值。
     */
    public function refund()
    {
        //请与贵网站订单系统中的唯一订单号匹配
        $order_refund_id = Input::get( 'order_refund_id', 0 )->required( '订单商品号不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容            
        }
        $order_model = new service_order_Refund_www();
        $order_model->setOrder_refund_id( $order_refund_id );
        $order_model->setUid( 37 );
        $order_model->setService_status( service_order_Service_base::service_status_success );
        $order_model->setRefund_status( service_order_Service_base::refund_status_seller_agree );
        $order_model->setReturn_status( service_order_Service_base::return_status_default );
        $order_model->setTrade_vendor( service_Order_base::trade_vendor_alipay );
        $total_feee = 0.01;
        $order_model->setTotal_fee( $total_feee );
        $orderInfo = $order_model->checkOrderRefundPurview();
        if ( $orderInfo == false ) {
            die( $order_model->getErrorMessage() );
        }

        //商户退款单号        
        $batch_no = date( 'YmdHis' ) . $order_refund_id;
        //必填，格式：当天日期[8位]+序列号[3至24位]，如：201008010000001
        //退款笔数
        $batch_num = 1;
        //必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
        /**
         * 原付款支付宝交易号^退款总金额^退款理由
         */
        $detail_data = $orderInfo->trade_no . '^' . $total_feee . '^' . '取消订单退款';
        //必填，具体格式请参见接口技术文档
        //构造要请求的参数数组
        $parameter = array(
            "service" => "refund_fastpay_by_platform_nopwd",
            "partner" => $this->alipay_config[ 'partner' ],
            "seller_email" => $this->alipay_config[ 'seller_email' ],
            "notify_url" => $this->alipay_config[ 'refund_notify_url' ] . '/' . $order_refund_id, //http://dev.www.090.cn/pay/alipay.autoRefund/123
            "refund_date" => date( "Y-m-d H:i:s" ),
            "batch_no" => $batch_no,
            "batch_num" => $batch_num,
            "detail_data" => $detail_data,
            "_input_charset" => strtolower( $this->alipay_config[ 'input_charset' ] )
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit( $this->alipay_config );
        $html_text = $alipaySubmit->buildRequestHttp( $parameter );
        //解析XML
        //注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
        $doc = new DOMDocument();
        $doc->loadXML( $html_text );

        //请在这里加上商户的业务逻辑程序代码
        //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
        //解析XML
        $is_success = '';
        if ( !empty( $doc->getElementsByTagName( "alipay" )->item( 0 )->nodeValue ) ) {
            $is_success = trim( $doc->getElementsByTagName( "alipay" )->item( 0 )->nodeValue );
        }
        if ( $is_success == 'T' ) {
            echo 'success';
            return true;
        } else {
            echo 'false';
            return false;
        }
    }

    /**
     * 执行退款和退货退款
     */
    public function autoRefund()
    {
//        Log::getInstance( 'mobile_order_payment_alipay_refund_error' )->write( 'notify:' . var_export( $_POST, true ) . var_export( $GET, true ) );
        $order_refund_id = Input::get( 'order_refund_id', 0 )->required( '订单商品号不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            //throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
            die( 'fail' );
        }

        $get = $_GET;
        unset( $_GET[ 'TMAC_ACTION' ] );
        unset( $_GET[ 'TMAC_CONTROLLER' ] );
        unset( $_GET[ 'TMAC_CONTROLLER_FILE' ] );
        unset( $_GET[ 'order_goods_id' ] );
        if ( empty( $_POST[ 'sign' ] ) ) {
            //todo delete
            die( 'fail' );
        }
        $alipayNotify = new AlipayNotify( $this->alipay_config );
        $verify_result = $alipayNotify->verifyNotify();
        //$str ='3015254852^8.0^房客取消,退款#2012102502967140^0.2^房客取消,退款';        
        if ( $verify_result ) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //获取批次号
            $batch_no = Input::post( 'batch_no', '' )->string();

            //获取批量退款数据中转账成功的笔数
            $success_num = Input::post( 'success_num', '' )->string();
            //获取批量退款数据中的详细信息
            $result_details = Input::post( 'result_details', '' )->string();
            $details = preg_split( "/[\^]/", $result_details );

            $trade_no = $details[ 0 ];
            $total_fee = $details[ 1 ];

            //logResult($tradeNo.'***');
            if ( strpos( $details[ 2 ], 'SUCCESS' ) !== false ) {
                echo "success";  //请不要修改或删除
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
                $model = new service_order_Refund_base();
                $model->setTrade_no( $trade_no );
                $model->setTrade_vendor( service_Order_base::trade_vendor_alipay );
                $model->setBatch_no( $batch_no );
                $model->setOrder_refund_id( $order_refund_id );
                $model->setTotal_fee( $total_fee );
                $model->setRefund_id( '' );
                $res = $model->executeOrderRefund();
                if ( $res == false ) {
                    Log::getInstance( 'mobile_order_payment_alipay_refund_error' )->write( 'notify_更新本地退款数据失败:' . $model->getErrorMessage() . var_export( $_POST, true ) . var_export( $get, true ) );
                }
                //根据原始第三方订单号 修改订单状态 记录日志等            
                $log = 'success--退款批次号：';
                $log .= $batch_no . "---" . $result_details . "---" . '支付宝订单号:' . $trade_no . "--- order_refund_id:" . $order_refund_id . "\r\n";
                Log::getInstance( 'mobile_order_payment_alipay_refund' )->write( $log );
            } else {
                echo "fail";
                Log::getInstance( 'mobile_order_payment_alipay_refund_error' )->write( 'refund_notify_(退款支付宝返回失败):' . var_export( $_POST, true ) . var_export( $get, true ) );
            }
        } else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            $log = "退款认证失败" . "<br/>";
            Log::getInstance( 'mobile_order_payment_alipay_refund_error' )->write( 'refund_notify_(支付宝异步验证失败):' . $log . var_export( $_POST, true ) . var_export( $get, true ) );
        }
    }

}
