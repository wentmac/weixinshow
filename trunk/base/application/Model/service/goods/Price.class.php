<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Price_base extends service_Model_base
{

    /**
     * 价格调整类型
     * 涨价
     */
    const price_type_plus = 1;

    /**
     * 价格调整类型
     * 减价
     */
    const price_type_less = 2;

    /**
     * 价格调整方法
     * 固定价格
     */
    const price_class_fixed = 1;

    /**
     * 价格调整方法
     * 百分比
     */
    const price_class_percent = 2;

    protected $uid;
    protected $goods_id;
    protected $errorMessage;
    private $goods_price_map;
    private $goods_price_array;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setGoods_id( $goods_id )
    {
        $this->goods_id = $goods_id;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 取出计算后的售价     
     * $this->setUid($uid);     
     * $goodsArray中必须有goods_id,goods_price|price,commission_fee
     * $this->getHandleGoodsPrice($goodsArray);     
     */
    public function getHandleGoodsPrice( $goodsArray )
    {
        if ( empty( $goodsArray ) ) {
            return $goodsArray;
        }
        if ( is_array( $goodsArray ) ) {
            $goods_id_array = array();
            foreach ( $goodsArray as $goodsInfo ) {
                $goods_id_array[] = $goodsInfo->goods_id;
            }
            $goods_id_string = implode( ',', $goods_id_array );
        } else {
            $goods_id_string = $goodsArray->goods_id;
        }
        $goods_price_map = $this->getGoodsPriceArray( $goods_id_string );
        if ( empty( $goods_price_map ) ) {
            return $goodsArray;
        }
        if ( is_array( $goodsArray ) ) {
            foreach ( $goodsArray as $goodsInfo ) {
                $this->getGoodsPrice( $goodsInfo );
            }
        } else {
            $goodsArray = $this->getGoodsPrice( $goodsArray );
        }
        return $goodsArray;
    }

    /**
     * 取出计算后的售价     
     * $this->setUid($uid);     
     * $goodsArray中必须有goods_sku_id,goods_id,goods_price|price,commission_fee
     * $this->getHandleGoodsSkuPrice($goodsArray);     
     */
    public function getHandleGoodsSkuPrice( $goodsArray )
    {
        if ( empty( $goodsArray ) ) {
            return $goodsArray;
        }
        $goods_id_array = array();
        foreach ( $goodsArray as $goods_id => $goods_sku_array ) {
            $goods_id_array[] = $goods_id;
        }
        $goods_id_string = implode( ',', $goods_id_array );
        $goods_price_map = $this->getGoodsPriceArray( $goods_id_string );

        if ( empty( $goods_price_map ) ) {
            return $goodsArray;
        }
        foreach ( $goodsArray as $goods_id => $goods_sku_array ) {
            foreach ( $goods_sku_array as $goods_sku ) {
                $this->getGoodsPrice( $goods_sku );
            }
        }
        return $goodsArray;
    }

    /**
     * 取出计算后的售价     
     * $this->setUid($uid);     
     * $goodsArray中必须有goods_sku_id,goods_id,goods_price|price,commission_fee
     * $this->getHandleGoodsSkuPrice($goodsArray);     
     */
    public function getHandleGoodsDetailSkuPrice( $goods_id, $goodsArray )
    {
        if ( empty( $goodsArray ) ) {
            return $goodsArray;
        }
        $goods_id_string = $goods_id;
        $goods_price_map = $this->getGoodsPriceArray( $goods_id_string );

        if ( empty( $goods_price_map ) ) {
            return $goodsArray;
        }
        foreach ( $goodsArray as $goods_sku ) {
            $this->getGoodsPrice( $goods_sku );
        }
        return $goodsArray;
    }

    /**
     * 取一条商品的特殊价
     * @param type $goodsInfo
     * @return type
     */
    private function getGoodsPrice( $goodsInfo )
    {
        //处理一下后台特殊佣金设置
        if ( empty( $this->goods_price_map[ $goodsInfo->goods_id ] ) ) {
            return $goodsInfo;
        }
        $goods_price_info = $this->goods_price_map[ $goodsInfo->goods_id ];
        $goods_price_info instanceof entity_GoodsPrice_base;

        if ( isset( $goodsInfo->price ) ) {
            $price_value = $this->getPriceValue( $goodsInfo->price, $goods_price_info );
            $goodsInfo->price_source = $goodsInfo->price;
        }
        if ( isset( $goodsInfo->goods_price ) ) {
            $price_value = $this->getPriceValue( $goodsInfo->goods_price, $goods_price_info );
            $goodsInfo->goods_price_source = $goodsInfo->goods_price;
        }

        if ( $goods_price_info->price_type == service_goods_Price_base::price_type_plus ) {
            //涨价操作
            if ( isset( $goodsInfo->goods_price ) ) {
                $goodsInfo->goods_price = $goodsInfo->goods_price + $price_value;
            } else if ( isset( $goodsInfo->price ) ) {
                $goodsInfo->price = $goodsInfo->price + $price_value;
            }
            //涨的价格加在佣金中
            $goodsInfo->commission_fee = $goodsInfo->commission_fee + $price_value;
        } else if ( $goods_price_info->price_type == service_goods_Price_base::price_type_less ) {
            //减价操作。判断佣金
            if ( $price_value > $goodsInfo->commission_fee ) {
                return $goodsInfo;
            }
            if ( isset( $goodsInfo->goods_price ) ) {
                $goodsInfo->goods_price = $goodsInfo->goods_price - $price_value;
            } else if ( isset( $goodsInfo->price ) ) {
                $goodsInfo->price = $goodsInfo->price - $price_value;
            }
            //减的价格从佣金中扣除
            $goodsInfo->commission_fee = $goodsInfo->commission_fee - $price_value;
        }
        return $goodsInfo;
    }

    private function getPriceValue( $price, $goods_price_info )
    {
        if ( $goods_price_info->price_class == service_goods_Price_base::price_class_fixed ) {
            //固定价格
            return $goods_price_info->price;
        } else if ( $goods_price_info->price_class == service_goods_Price_base::price_class_percent ) {
            return round( $price * round( $goods_price_info->price / 100, 2 ), 2 );
        }
    }

    private function getGoodsPriceArray( $goods_id_string )
    {
        if ( empty( $this->goods_price_array ) ) {
            $dao = dao_factory_base::getGoodsPriceDao();
            $dao->setField( 'goods_id,price_type,price_class,price' );
            $where = "uid={$this->uid} AND " . $dao->getWhereInStatement( 'goods_id', $goods_id_string );
            $dao->setWhere( $where );
            $res = $this->goods_price_array = $dao->getListByWhere();
        } else {
            $res = $this->goods_price_array;
        }
        $goods_price_map = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $goods_price_map[ $value->goods_id ] = $value;
            }
        }
        $this->goods_price_map = $goods_price_map;
        return $this->goods_price_map;
    }

}
