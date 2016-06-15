<?php
//Log::getInstance( 'api_post_goods_log' )->write( $errorMessage . var_export( $rs, true ) . '|' . var_export( $_GET, true ) . var_export( $_POST, true ) );

$config[ 'log' ][ 'sms_error' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'sms_error' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
$config[ 'log' ][ 'post_error' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'post_error' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
$config[ 'log' ][ 'mobile_order_payment_error' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'mobile_order_payment_error' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
$config[ 'log' ][ 'mobile_order_payment_alipay_refund' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'mobile_order_payment_alipay_refund' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
$config[ 'log' ][ 'mobile_order_payment_alipay_refund_error' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'mobile_order_payment_alipay_refund_error' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//微信支付错误日志
$config[ 'log' ][ 'mobile_order_payment_wechatpay_error' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'mobile_order_payment_wechatpay_error' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//微信支付退款日志
$config[ 'log' ][ 'mobile_order_payment_wechatpay_refund' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'mobile_order_payment_wechatpay_refund' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
$config[ 'log' ][ 'mobile_order_payment_wechatpay_refund_error' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'mobile_order_payment_wechatpay_refund_error' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//微信支付企业付款
$config[ 'log' ][ 'mobile_order_payment_wechatpay_transfers_error' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'mobile_order_payment_wechatpay_transfers_error' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//API的接口调试
$config[ 'log' ][ 'api_post_field_log' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'api_post_field_log' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//商品图片入库图片采集的
$config[ 'log' ][ 'api_post_goods_images_log' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'api_post_goods_images_log' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//商品图片入库图片采集的
$config[ 'log' ][ 'api_post_goods_log' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'api_post_goods_log' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);

//定时跑日志的
$config[ 'log' ][ 'crontab' ] = array(
    'File' => TMAC_BASE_PATH . APP_CRONTAB_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'crontab' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);

//定时跑日志的
$config[ 'log' ][ 'crontab_update_current_money' ] = array(
    'File' => TMAC_BASE_PATH . APP_CRONTAB_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'update_current_money' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//定时跑日志的
$config[ 'log' ][ 'crontab_auto_order_confirm' ] = array(
    'File' => TMAC_BASE_PATH . APP_CRONTAB_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'auto_order_confirm' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//定时跑日志的
$config[ 'log' ][ 'crontab_update_receivable_current_money' ] = array(
    'File' => TMAC_BASE_PATH . APP_CRONTAB_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'crontab_update_receivable_current_money' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//PushMessage的异常错误日志
$config[ 'log' ][ 'push_message' ] = array(
    'File' => TMAC_BASE_PATH . APP_BASE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'push_message' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);
//service_member_Tree_base getNextParent 错误日志
$config[ 'log' ][ 'tree_get_next_parent' ] = array(
    'File' => TMAC_BASE_PATH . APP_MOBILE_NAME . DIRECTORY_SEPARATOR . VARROOT . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR. 'tree_get_next_parent' . DIRECTORY_SEPARATOR . 'log-[Y-m-d].log',
    'Append' => true,
    'ConversionPattern' => '[Y-m-d H:i:s]'
);