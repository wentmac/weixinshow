<?php

$config[ 'bill' ][ 'bill_type' ] = array(
    'all' => '全部账单',
    'in' => '进账单',
    'out' => '出账单',
    'waiting_confirm' => '待确认账单',
    'income_business' => '自营收入账单',
    'income_wholesale' => '代销收入账单',
    'income_receivable' => '直接收款账单',
    'expense_withdrawals_ing' => '提现进行中',
    'expense_withdrawals_success' => '已经提现'
);

$config[ 'bill' ][ 'settle_status' ] = array(
    0 => '申请提现/等待审核',
    3 => '审核成功/等待打款',
    1 => '提现成功',
    2 => '提现失败'    
);

$config[ 'bill' ][ 'settle_status_show' ] = array(
    'untreated' => 0,
    'success' => 1,    
    'fail' => 2,
    'verify' => 3
);

$config[ 'bill' ][ 'settle_status_text' ] = array(
    'untreated' => '申请提现/等待审核',
    'verify' => '审核成功/等待打款',
    'success' => '提现成功',
    'fail' => '提现失败'
);


$config[ 'bill' ][ 'account_type' ] = array(
    1 => '银行卡',
    2 => '支付宝'
);

$config[ 'bill' ][ 'bill_type_class' ] = array(
    0 => '',
    1 => '[自营收入]',
    2 => '[代销佣金]',
    3 => '[直接收款]',
    4 => '[系统佣金]'
);
$config[ 'bill' ][ 'bill_expend_type' ] = array(
    0 => '',
    1 => '[提现]',
    2 => '[退款]',
    3 => '[佣金退款]'
);

