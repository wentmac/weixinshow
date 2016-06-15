<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_payment_Alipay_base extends service_Payment_base implements service_payment_Interface_base
{

    protected $alipay_config;

    public function __construct()
    {
        parent::__construct();
        $this->alipay_config = Tmac::config( 'alipay.alipay_config', APP_WWW_NAME );
        require_once Tmac::findFile( 'payment/alipay/alipay_submit', APP_WWW_NAME );
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
        //商户退款单号        
        $batch_no = date( 'YmdHis' ) . 'id' . $this->order_refund_id;
        //必填，格式：当天日期[8位]+序列号[3至24位]，如：201008010000001
        //退款笔数
        $batch_num = 1;
        //必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
        /**
         * 原付款支付宝交易号^退款总金额^退款理由
         */
        $detail_data = $this->orderInfo->trade_no . '^' . $this->money . '^' . '取消订单退款';
        //必填，具体格式请参见接口技术文档
        //构造要请求的参数数组
        $parameter = array(
            "service" => "refund_fastpay_by_platform_nopwd",
            "partner" => $this->alipay_config[ 'partner' ],
            "seller_email" => $this->alipay_config[ 'seller_email' ],
            "notify_url" => $this->alipay_config[ 'refund_notify_url' ] . '/' . $this->order_refund_id, //http://dev.www.090.cn/pay/alipay.autoRefund/123
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
            return true;
        } else {
            return false;
        }
    }

    /**
     * 演示订单的退款执行
     */
    private function demoOrderRefund()
    {
        $order_refund_id = $this->order_refund_id;
        $trade_no = $this->orderInfo->trade_no;
        //商户退款单号        
        $batch_no = date( 'YmdHis' ) . 'id' . $this->order_refund_id;
        //必填，格式：当天日期[8位]+序列号[3至24位]，如：201008010000001
        //退款笔数
        $batch_num = 1;
        //必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
        /**
         * 原付款支付宝交易号^退款总金额^退款理由
         */
        $detail_data = $this->orderInfo->trade_no . '^' . $this->money . '^' . '取消订单退款';


        $model = new service_order_Refund_base();
        $model->setTrade_no( $trade_no );
        $model->setTrade_vendor( service_Order_base::trade_vendor_alipay );
        $model->setBatch_no( $batch_no );
        $model->setOrder_refund_id( $order_refund_id );
        $model->setTotal_fee( $this->money );
        $model->setRefund_id( '' );
        $res = $model->executeOrderRefund();
        if ( $res == false ) {
            Log::getInstance( 'mobile_order_payment_alipay_refund_error' )->write( 'notify_更新本地退款数据失败:' . $model->getErrorMessage() );
            return FALSE;
        }
        //根据原始第三方订单号 修改订单状态 记录日志等            
        $log = 'success--退款批次号：';
        $log .= $batch_no . "---" . $detail_data . "---" . '支付宝订单号:' . $trade_no . "--- order_refund_id:" . $order_refund_id . "\r\n";
        Log::getInstance( 'mobile_order_payment_alipay_refund' )->write( $log );
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
