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

        $this->alipay_config = Tmac::config( 'alipay.alipay_config', APP_MOBILE_NAME );
        require_once Tmac::findFile( 'payment/alipay/alipay_notify', APP_MOBILE_NAME );
        require_once Tmac::findFile( 'payment/alipay/alipay_submit', APP_MOBILE_NAME );
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

        $order_model = new service_order_Payment_mobile();
        $orderInfo = $order_model->getOrderInfoBySN( $order_sn );
        if ( !$orderInfo ) {
            die( '没有此订单' );
        }
        $orderInfo instanceof entity_OrderInfo_base;
        if ( $orderInfo->order_status <> service_Order_base::order_status_buyer_order_create ) {
            die( '操作流程错误' );
        }
        $order_id = $orderInfo->order_id;
        //获取支付金额
        $total_fee = $orderInfo->order_amount;
        $subject = $order_model->getOrderSubject();
        $out_trade_no = $order_id;
        $req_id = $orderInfo->order_sn; //用于关联请求与响应，防止 请求重播。 支付宝限制来自同一个 partner 的请求号必须唯 一。 
        //付款前判断库存
        $check_stock = $order_model->checkStockBeforePayment();
        if ( $check_stock == false ) {
            die( $order_model->getErrorMessage() );
        }
        // if ($use == 1) {
        //     $couponPrice = $this->order_action_model->setOrderInfo($orderInfo)->setCouponOfKLZ();
        //     if ($couponPrice) {
        //         $total_fee = $orderInfo->goods_amount - $couponPrice;
        //     }
        // }        
        //请求业务参数详细
        $req_data = '<direct_trade_create_req>'
                . '<notify_url>' . $this->alipay_config[ 'notify_url' ] . '</notify_url>'
                . '<call_back_url>' . $this->alipay_config[ 'return_url' ] . '</call_back_url>'
                . '<seller_account_name>' . trim( $this->alipay_config[ 'seller_email' ] ) . '</seller_account_name>'
                . '<out_trade_no>' . $out_trade_no . '</out_trade_no>'//商户网站 唯一订单 号 
                . '<subject>' . $subject . '</subject>'
                . '<total_fee>' . $total_fee . '</total_fee>'
                . '<merchant_url>' . $this->alipay_config[ 'merchant_url' ] . '</merchant_url>'//操作 中断返回地址
                . '</direct_trade_create_req>';
        //必填        
        //构造要请求的参数数组，无需改动
        $para_token = array(
            "service" => "alipay.wap.trade.create.direct",
            "partner" => trim( $this->alipay_config[ 'partner' ] ),
            "sec_id" => trim( $this->alipay_config[ 'sign_type' ] ),
            "format" => $this->alipay_config[ 'format' ],
            "v" => $this->alipay_config[ 'v' ],
            "req_id" => $req_id,
            "req_data" => $req_data,
            "_input_charset" => trim( strtolower( $this->alipay_config[ 'input_charset' ] ) )
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit( $this->alipay_config );
        $html_text = $alipaySubmit->buildRequestHttp( $para_token );

        //URLDECODE返回的信息
        $html_text = urldecode( $html_text );

        //解析远程模拟提交后返回的信息
        $para_html_text = $alipaySubmit->parseResponse( $html_text );

        //获取request_token
        $request_token = $para_html_text[ 'request_token' ];

        /*         * ************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute************************* */

        //业务详细
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        //必填
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "alipay.wap.auth.authAndExecute",
            "partner" => trim( $this->alipay_config[ 'partner' ] ),
            "sec_id" => trim( $this->alipay_config[ 'sign_type' ] ),
            "format" => $this->alipay_config[ 'format' ],
            "v" => $this->alipay_config[ 'v' ],
            "req_id" => $req_id,
            "req_data" => $req_data,
            "_input_charset" => trim( strtolower( $this->alipay_config[ 'input_charset' ] ) )
        );

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
        if ( empty( $_POST[ 'sign' ] ) || empty( $_POST[ 'notify_data' ] ) || empty( $_POST[ 'sec_id' ] ) ) {
            die( 'fail' );
        }
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify( $this->alipay_config );
        $verify_result = $alipayNotify->verifyNotify();

        if ( $verify_result ) {//验证成功
            $doc = new DOMDocument();
            if ( $this->alipay_config[ 'sign_type' ] == 'MD5' ) {
                $doc->loadXML( $_POST[ 'notify_data' ] );
            }

            if ( $this->alipay_config[ 'sign_type' ] == '0001' ) {
                $doc->loadXML( $alipayNotify->decrypt( $_POST[ 'notify_data' ] ) );
            }

            if ( !empty( $doc->getElementsByTagName( "notify" )->item( 0 )->nodeValue ) ) {
                //商户订单号
                $order_id = $doc->getElementsByTagName( "out_trade_no" )->item( 0 )->nodeValue;
                //支付宝交易号
                $trade_no = $doc->getElementsByTagName( "trade_no" )->item( 0 )->nodeValue;
                //交易状态
                $trade_status = $doc->getElementsByTagName( "trade_status" )->item( 0 )->nodeValue;
                //支付价格
                $total_fee = $doc->getElementsByTagName( "total_fee" )->item( 0 )->nodeValue;
                //买家支付宝用 户号 
                $buyer_email = $doc->getElementsByTagName( "buyer_email" )->item( 0 )->nodeValue;
                //买家买家支付宝账号
                $buyer_id = $doc->getElementsByTagName( "buyer_id" )->item( 0 )->nodeValue;

                $order_model = new service_order_Payment_mobile();
                $order_model->setOrder_id( $order_id );
                $orderInfo = $order_model->getOrderInfoById();

                if ( !$orderInfo || $orderInfo->pay_status == service_Order_base::pay_status_success ) {//订单不存在 /订单状态不等于待支付的  已经支付的
                    Log::getInstance( 'mobile_order_payment_error' )->write( 'notify_(订单不存在 或 订单已经支付的):' . var_export( $_POST, true ) );
                    die( 'success' );
                }
                $orderInfo instanceof entity_OrderInfo_base;

                $pay_type = 0;
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
                $entity_PayLog_base->pay_class = service_Order_base::pay_class_wap;

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
            } else {
                Log::getInstance( 'mobile_order_payment_error' )->write( 'notify_(支付宝异步验证失败):' . var_export( $_POST, true ) );
                echo 'fail';
            }
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
        $url = MOBILE_URL . 'order/fail';
        if ( empty( $_GET[ 'sign' ] ) || empty( $_GET[ 'result' ] ) || empty( $_GET[ 'out_trade_no' ] ) || empty( $_GET[ 'trade_no' ] ) ) {
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

        //交易状态
        $result = $_GET[ 'result' ];
        if ( $verify_result && $result == 'success' ) {//验证成功
            $order_model = new service_order_Payment_mobile();
            $order_model->setOrder_id( $order_id );
            $orderInfo = $order_model->getOrderInfoById();
            if ( !$orderInfo ) {
                $url.='?error=订单不存在';
                parent::headerRedirect( $url );
            }
            $url = MOBILE_URL . 'order/success?sn=' . $orderInfo->order_sn;
            parent::headerRedirect( $url );
        } else {
            $url.='?error=验证失败';
            parent::headerRedirect( $url );
        }
    }

    /**
     * 支付取消时显示的页面
     */
    public function merchant()
    {
        echo '取消支付';
    }

}
