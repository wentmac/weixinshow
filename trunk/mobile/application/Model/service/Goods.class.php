<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Goods.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Goods_mobile extends service_Goods_base
{

    const goods_fixed_pagesize = 10;
    
    private $goods_cat_id;
    private $query;
    private $pagesize;

    function setGoods_cat_id( $goods_cat_id )
    {
        $this->goods_cat_id = $goods_cat_id;
    }

    function setQuery( $query )
    {
        $this->query = $query;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取列表中所有的item_id的sku
     * @param type $goods_id_string
     */
    public function getGoodsSkuArray()
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setField( 'goods_sku_id,goods_sku_json,goods_sku,price,stock,sales_volume' );
        $where = 'goods_id=' . $this->goods_id . ' AND is_delete=0';
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        $result_array = array();
        if ( $res ) {
            foreach ( $res as $goods_sku_object ) {
                $goods_sku_json_array = unserialize( $goods_sku_object->goods_sku_json );
                $sku_name = '';
                foreach ( $goods_sku_json_array AS $sku_name_object ) {
                    $sku_name .= '[' . $sku_name_object[ 'spec_value_name' ] . ']';
                }
                $goods_sku_object->sku_name = $sku_name;
                unset( $goods_sku_object->goods_sku_json );

                $price = $this->getGoodsPromotePrice( $goods_sku_object->price, $this->goodsInfo->goods_type, $this->member_level );
                $goods_sku_object->price = $price[ 'price' ];
                $goods_sku_object->price_source = $price[ 'price_source' ];
                $result_array[ $goods_sku_object->goods_sku_id ] = $goods_sku_object;
            }
        }
        return $result_array;
    }

    public function getGoodsInfo( $field = '*' )
    {
        $goods_info = parent::getGoodsInfoById( $field );
        if ( $goods_info ) {
            $goods_info->goods_cat_id = empty( $goods_info->goods_cat_id ) ? '' : explode( ',', $goods_info->goods_cat_id );
            $goods_image_array = array();
            $goods_image_ids = json_decode( $goods_info->goods_image_ids );
            if ( $goods_image_ids ) {
                foreach ( $goods_image_ids AS $goods_image_id ) {
                    $goods_image_array[] = $this->getImage( $goods_image_id, '50', 'goods' );
                }
            }
            $goods_info->goods_image_array = $goods_image_array;
            $price = $this->getGoodsPromotePrice( $goods_info->goods_price, $goods_info->goods_type, $this->member_level );
            $goods_info->goods_price = $price[ 'price' ];
            $goods_info->price_source = $price[ 'price_source' ];
            /**
              $goods_price_model = new service_goods_Price_base();
              $goods_price_model->setUid( $this->uid );
              $goods_price_model->getHandleGoodsPrice( $goods_info );
             */
        }
        $this->goodsInfo = $goods_info;
        return $goods_info;
    }

    public function getItemId( $goodsInfo )
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setField( 'item_id' );
        $where = "uid={$goodsInfo->uid} AND goods_id={$goodsInfo->goods_id} AND is_self=1";
        $dao->setWhere( $where );
        return $dao->getInfoByWhere();
    }

    /**
     * 取所有店铺的所有商品
     * $this->uid;
     * $this->goods_cat_id;
     * $this->query;
     * $this->pagesize;
     * $this->member_level;
     * $this->getGoodsList();
     */
    public function getGoodsList()
    {
        $dao = dao_factory_base::getGoodsDao();
        $where = 'uid=' . $this->uid;
        if ( !empty( $this->goods_cat_id ) ) {
            $where .= $dao->getGoodsListWhereByCid( $this->goods_cat_id );
        }
        if ( !empty( $this->query ) ) {
            $where.=" AND goods_name like '%{$this->query}%'";
        }
        /**
          if ( !empty( $this->recommend ) ) {
          $where.=" AND recommend=1";
          } */
        $where .= " AND is_delete=0";

        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $res = array();
        if ( $count > 0 ) {
            $dao->setOrderby( 'goods_sort DESC,goods_id DESC' );
            $dao->setLimit( $limit );
            $dao->setField( 'goods_id,goods_name,goods_price,goods_image_id,sales_volume,goods_type' );
            $res = $dao->getListByWhere();
            
            foreach ( $res as $value ) {
                $price = $this->getGoodsPromotePrice( $value->goods_price, $value->goods_type, $this->member_level );
                $value->goods_price = $price[ 'price' ];
                $value->price_source = $price[ 'price_source' ];
                $value->goods_image_url = $this->getImage( $value->goods_image_id, '300', 'goods' );
            }
        }
        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / self::goods_fixed_pagesize ) ),
            'pagesize' => $this->pagesize,
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'shop_goods_list',
            'retmsg' => $retmsg,
            'reqdata' => $res,
        );
        return $return;
    }

}
