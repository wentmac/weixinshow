<?php

$config[ 'order_service' ][ 'identity_name' ] = array(
    'buyer' => '买家',
    'seller' => '卖家'
);

/**
 * 申请售后处理方式（ 1：退货退款｜ 2：仅退款 ） 
 */
$config[ 'order_refund' ][ 'refund_service_status' ] = array(
    1 => '退货退款',
    2 => '退款'
);

/**
 * 售后退款原因（ 0：默认状态｜ 1：买/买双方协商一致｜ 2：买错/多买/不想要｜ 3：商品质量问题｜ 4：未收到货品 ）
 */
$config[ 'order_refund' ][ 'refund_service_reason' ] = array(
    1 => '买/买双方协商一致',
    2 => '买错/多买/不想要',
    3 => '商品质量问题',
    4 => '未收到货品'
);

$config[ 'order_refund' ][ 'service_status' ] = array(
    0 => '',
    1 => '退款中:等待卖家处理',
    2 => '退款中:等待买家处理',
    3 => '退款中:客服介入处理',
    4 => '退款结束',
    5 => '退款取消',
);

$config[ 'seller' ][ 'order_status' ] = array(
    0 => '未确认',
    1 => '待付款',
    2 => '待发货',
    3 => '已发货',
    4 => '已关闭',
    5 => '已完成'
);
$config[ 'buyer' ][ 'order_status' ] = array(
    0 => '未确认',
    1 => '等待买家付款',
    2 => '等待卖家发货',
    3 => '卖家已经发货',
    4 => '订单无效/关闭',
    5 => '订单完成'
);

$config[ 'system' ][ 'order_status' ] = array(
    'buyer_waiting_payment' => '待付款',
    'buyer_wating_seller_delivery' => '待发货',
    'buyer_wating_receiving' => '已发货/待收货',
    'buyer_wating_comment' => '待评价',
    'buyer_complete' => '已完成',
    'buyer_close' => '已关闭',
    'buyer_refund' => '退款中'
);

$config[ 'system' ][ 'demo_order' ] = array(
    0 => '正常订单',
    1 => 'APP订单'
);

$config[ 'system' ][ 'demo_order_show' ] = array(
    0 => '全部订单',
    1 => '正常订单',
    2 => 'APP订单'
);

$config[ 'system' ][ 'order_refund_status' ] = array(
    'seller_confirm' => '待卖家处理',
    'buyer_confirm' => '待买家处理',
    'customer_confirm' => '待银品惠客服介入',
    'complete' => '同意退款',
    'close' => '撤销维权'
);

$config[ 'order' ][ 'order_type' ] = array(
    0 => '普通商品',
    1 => '会员商品',
    2 => '会员专卖商品'
);
