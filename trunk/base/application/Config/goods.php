<?php

$config[ 'goods' ][ 'goods_source' ] = array(
    0 => '聚店',
    1 => '京东',
    2 => '淘宝'
);
$config[ 'goods' ][ 'goods_source_show' ] = array(
    1 => '聚店',
    2 => '京东'
);
$config[ 'goods' ][ 'is_supplier' ] = array(
    0 => '普通',
    1 => '云端商品库'
);
$config[ 'goods' ][ 'is_supplier_show' ] = array(
    1 => '普通',
    2 => '云端商品库',
    3 => '供应商上传的云端商品库'
);
$config[ 'goods' ][ 'sort' ] = array(
    1 => '上架时间',
    2 => '销量从高到低',
    3 => '销量从低到高',
    4 => '库存从高到低',
    5 => '库存从低到高',
    8 => '商品排序'
);

$config[ 'goods' ][ 'commission_seller_different' ] = array(
    0 => '所有的分销商佣金相同',
    1 => '分销商佣金按级别不同区分'
);

$config[ 'goods' ][ 'market_shop' ] = array(
    63 => '天下客',
    558 => '农副产品专卖',
    640 => '蜗牛速跑',
    277 => '多客来'
);

$config[ 'goods' ][ 'goods_country_id' ] = array(
    1 => '泰国',
    2 => '美国',
    3 => '法国',
    4 => '德国',
    5 => '澳洲',
    6 => '韩国',
    7 => '荷兰',
    8 => '英国',
    9 => '日本',
    10 => '香港'
);

$config[ 'goods' ][ 'goods_type' ] = array(
    1 => '普通商品',
    2 => '会员商品',
    3 => '特惠商品',
    4 => '会员专卖',
    5 => '商城商品'
);

$config[ 'goods' ][ 'goods_type_order_type_map' ] = array(
    1 => 0,
    2 => 1,
    3 => 0,
    4 => 2,
    5 => 3
);

$config[ 'goods' ][ 'goods_member_level' ] = array(
    1 => 'lv1会员',
    2 => 'lv2会员',
    3 => 'lv3会员',
    4 => 'lv4会员',
    5 => 'lv5会员',
    6 => 'lv6会员',
    7 => 'lv7会员',
    8 => 'lv8会员',
    9 => 'lv9会员'
);
/**
 * 普通商品针对会员级别对应的优惠率
 */
$config[ 'goods' ][ 'goods_offer_rate' ] = array(
    1 => 3.5,
    2 => 3.45,
    3 => 3.4,
    4 => 3.35,
    5 => 3.3,
    6 => 3.25,
    7 => 3.2,
    8 => 3.15,
    9 => 3
);

/**
 * 商城商品针对会员级别对应的优惠率
 */
$config[ 'goods' ][ 'goods_mall_offer_rate' ] = array(
    1 => 7,
    2 => 6.9,
    3 => 6.8,
    4 => 6.7,
    5 => 6.6,
    6 => 6.5,
    7 => 6.4,
    8 => 6.3,
    9 => 6
);

//商品是否支持积分抵扣款。
$config[ 'goods' ][ 'is_integral' ] = array(
    0 => '不参加积分活动',
    1 => '积分活动'
);
