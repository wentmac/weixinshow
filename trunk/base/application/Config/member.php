<?php

$config[ 'member' ][ 'level' ] = array(
    1 => array(
        0 => '',
        1 => 'vip',
        2 => 'svip'
    ),
    2 => array(
        0 => '',
        1 => 'copper',
        2 => 'silver',
        3 => 'gold'
    )
);
$config[ 'member' ][ 'member_type' ] = array(
    1 => '分销商',
    2 => '供应商',
    3 => '买家'    
);
$config[ 'member' ][ 'stock_setting' ] = array(
    1 => '拍下减库存',
    2 => '付款减库存'
);
$config[ 'member' ][ 'member_class' ] = array(
    1 => array(
        -1 => '全部',
        0 => '免费分销商',
        1 => 'vip',
        2 => 'svip'
    ),
    2 => array(
        -1 => '全部',
        0 => '免费供应商',
        1 => '铜牌',
        2 => '银牌',
        3 => '金牌',
    ),
    3 => array(
        0 => '买家'
    )
);
$config[ 'member' ][ 'commission_seller' ] = array(
    0 => 'commission_seller_free',
    1 => 'commission_seller_vip',
    2 => 'commission_seller_svip'
);


$config[ 'member' ][ 'idcard_verify' ] = array(
    -1 => '审核不通过',
    0 => '未审核',
    1 => '审核通过'
);
$config[ 'member' ][ 'export' ] = array(
    1 => '仅导出当前列表中的数据',
    2 => '导出查询出来的所有数据'
);
$config[ 'member' ][ 'locked_type' ] = array(
    0 => '正常未锁定',
    1 => '提现锁定'
);
$config[ 'member' ][ 'fee_type' ] = array(
    0 => '直接收款和自营收入扣',
    1 => '直接收款和自营收入不扣手续费'
);
$config[ 'member' ][ 'promotion_type' ] = array(
    0 => '无特殊推广',
    1 => '分销商的佣金促销推广'
);


$config[ 'member_setting' ][ 'bank_id' ] = array(
    'cmb' => '招商银行',
    'icbc' => '中国工商银行',
    'abc' => '中国农业银行',
    'ccb' => '中国建设银行',
    'boc' => '中国银行',
    'spdb' => '上海浦东发展银行',
    'bcom' => '交通银行',
    'cmbc' => '中国民生银行',
    'citic' => '中信银行',
    'psbc' => '中国邮政储蓄银行'
);
$config[ 'member_setting' ][ 'settle_bank_id' ] = array(
    'alipay' => '支付宝',
    'cmb' => '招商银行',
    'icbc' => '中国工商银行',
    'abc' => '中国农业银行',
    'ccb' => '中国建设银行',
    'boc' => '中国银行',
    'spdb' => '上海浦东发展银行',
    'bcom' => '交通银行',
    'cmbc' => '中国民生银行',
    'citic' => '中信银行',
    'psbc' => '中国邮政储蓄银行'
);
$config[ 'member' ][ 'sex' ] = array(
    '1' => '男',
    '2' => '女'    
);
$config[ 'member' ][ 'agent_lock' ] = array(
    '1' => '没有买过会员',
    '2' => '买过会员'    
);
