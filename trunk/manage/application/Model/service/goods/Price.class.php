<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Price_manage extends service_goods_Price_base
{

    private $price_type;
    private $price_class;
    private $price_value;

    function setPrice_type( $price_type )
    {
        $this->price_type = $price_type;
    }

    function setPrice_class( $price_class )
    {
        $this->price_class = $price_class;
    }

    function setPrice_value( $price_value )
    {
        $this->price_value = $price_value;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 保存自定义商品售价调整
     */
    public function saveGoodsCustomPrice()
    {
        $goods_id_array = $this->getGoodsIdString();
        if ( empty( $goods_id_array ) ) {
            return true;
        }
        if ( count( $goods_id_array ) > 100 ) {
            $this->errorMessage = '一次操作太多了，服务器吃不消啊~~';
            return false;
        }
        $goods_price_dao = dao_factory_base::getGoodsPriceDao();
        foreach ( $goods_id_array as $goods_id ) {
            $goods_price_dao->getDb()->startTrans();
            $entity_GoodsPrice_base = new entity_GoodsPrice_base();
            $entity_GoodsPrice_base->goods_id = $goods_id;
            $entity_GoodsPrice_base->uid = $this->uid;
            $entity_GoodsPrice_base->price_type = $this->price_type;
            $entity_GoodsPrice_base->price_class = $this->price_class;
            $entity_GoodsPrice_base->price = $this->price_value;
            $entity_GoodsPrice_base->goods_price_time = $this->now;

            $where = "uid={$this->uid} AND goods_id={$goods_id}";
            $goods_price_dao->setWhere( $where );
            $goods_price_dao->setField( 'goods_price_id' );
            $goods_pirce = $goods_price_dao->getInfoByWhere();
            if ( $goods_pirce ) {
                $goods_price_dao->updateByWhere( $entity_GoodsPrice_base );
            } else {
                $goods_price_dao->insert( $entity_GoodsPrice_base );
            }

            /**
             * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
             */
            if ( $goods_price_dao->getDb()->isSuccess() ) {
                $goods_price_dao->getDb()->commit();
            } else {
                $goods_price_dao->getDb()->rollback();
            }
        }
        return true;
    }

    private function getGoodsIdString()
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id' );
        $where = $dao->getWhereInStatement( 'goods_id', $this->goods_id ) . ' AND is_delete=0';
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        $return_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $return_array[] = $value->goods_id;
            }
        }
        return $return_array;
    }

}
