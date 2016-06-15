<?php

/**
 * WEB 商品列表 业务模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_List_base extends service_Model_base
{

    const sort_time = 1;  //上架时间
    const sort_sales_volume_desc = 2;  //销量从高到低
    const sort_sales_volume_asc = 3;  //销量从低到高    
    const sort_stock_desc = 4;  //库存从高到低
    const sort_stock_asc = 5;  //库存从低到高
    const sort_price_desc = 6;  //价格从高到低
    const sort_price_asc = 7;  //价格从低到高
    const sort_goods_sort_desc = 8;  //商品排序

    protected $query_string;
    protected $goods_cat_id;
    protected $is_supplier;
    protected $goods_source;
    protected $query;
    protected $pagesize;
    protected $image_size;
    protected $uid;
    protected $errorMessage;
    protected $sort;
    protected $just_this_goods_cat_id;
    protected $brand_id;
    protected $goods_source_id;
    protected $status; //1：is_delete=1|0:is_delete=2
    protected $url;
    protected $memberInfo;
    protected $goods_country_id;
    protected $goods_type;

    public function __construct()
    {
        parent::__construct();
    }

    function setGoods_source_id( $goods_source_id )
    {
        $this->goods_source_id = $goods_source_id;
    }

    function setBrand_id( $brand_id )
    {
        $this->brand_id = $brand_id;
    }

    function setQuery_string( $query_string )
    {
        $this->query_string = $query_string;
    }

    function setIs_supplier( $is_supplier )
    {
        $this->is_supplier = $is_supplier;
    }

    function setGoods_source( $goods_source )
    {
        $this->goods_source = $goods_source;
    }

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

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setSort( $sort )
    {
        $this->sort = $sort;
    }

    function setJust_this_goods_cat_id( $just_this_goods_cat_id )
    {
        $this->just_this_goods_cat_id = $just_this_goods_cat_id;
    }

    function setStatus( $status )
    {
        $this->status = $status;
    }

    function setUrl( $url )
    {
        $this->url = $url;
    }

    function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
    }

    function setGoods_country_id( $goods_country_id )
    {
        $this->goods_country_id = $goods_country_id;
    }

    function setGoods_type( $goods_type )
    {
        $this->goods_type = $goods_type;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getIsWholesaleByGoodsIdString( $goods_id_string )
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setField( 'item_id,goods_id' );
        $where = "uid={$this->uid} AND " . $dao->getWhereInStatement( 'goods_id', $goods_id_string ) . ' AND is_delete=0';
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $result_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $result_array[ $value->goods_id ] = $value->item_id;
            }
        }
        return $result_array;
    }

    /**
     * 取所有的云端产品库分类
     */
    public function getGoodsCategoryArray( $goods_cat_id = 0, $is_cloud_product = 1 )
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setField( 'goods_cat_id,cat_name,cat_pid' );
        $where = 'is_delete=0 AND is_cloud_product=' . $is_cloud_product;
        $dao->setWhere( $where );
        $dao->setOrderby( 'goods_cat_id ASC' );
        $res = $dao->getListByWhere();
        $category_pid_array = array();
        $result_array = array();
        foreach ( $res as $value ) {
            $value->category_icon = STATIC_URL . APP_MOBILE_NAME . '/default/v1/images/goods_category/' . $value->goods_cat_id . '.png';
            $category_pid_array[ $value->cat_pid ][] = $value;
        }
        if ( !empty( $goods_cat_id ) ) {
            $key = $goods_cat_id;
        } else {
            $key = 0;
        }
        $is_cloud_product == service_GoodsCategory_base::is_cloud_product_yes && $key = 0; //只显示一级二级
        if ( isset( $category_pid_array[ $key ] ) ) {
            foreach ( $category_pid_array[ $key ] as $value ) {

                if ( !empty( $category_pid_array[ $value->goods_cat_id ] ) ) {
                    $value->subclass = $category_pid_array[ $value->goods_cat_id ];
                }
                $result_array[] = $value;
            }
        }

        $new_activity_object = new stdClass();
        $new_activity_object->goods_cat_id = 1213;
        $new_activity_object->cat_name = '活动专区';
        $new_activity_object->cat_pid = 492;
        $new_activity_object->category_icon = STATIC_URL . APP_MOBILE_NAME . '/default/v1/images/goods_category/' . 1213 . '.png';

        $result_array[ 0 ]->subclass[] = $new_activity_object;

        return $result_array;
    }

    /**
     * 取列表中所有的item_id的sku
     * @param type $goods_id_string
     */
    protected function _getGoodsSkuArray( $goods_id_string )
    {
        if ( empty( $goods_id_string ) ) {
            return array();
        }
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setField( 'goods_sku_id,goods_id,goods_sku_json,price,stock,sales_volume,commission_fee' );
        $where = $dao->getWhereInStatement( 'goods_id', $goods_id_string ) . ' AND is_delete=0';
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
                $result_array[ $goods_sku_object->goods_id ][] = $goods_sku_object;
            }
            /**
              $goods_price_model = new service_goods_Price_base();
              $goods_price_model->setUid( $this->uid );
              $goods_price_model->getHandleGoodsSkuPrice( $result_array );

              //取商城 佣金特殊设置
              $goods_commission_model = new service_goods_Commission_base();
              $goods_commission_model->setUid( $this->uid );
              $goods_commission_model->getHandleSkuCommission( $result_array );

              //取不同的代理的成本价，反映在佣金上
              $goods_agent_model = new service_goods_Agent_base();
              $goods_agent_model->setUid( $this->uid );
              $goods_agent_model->getHandleSkuAgentPrice( $result_array );
             */
        }
        return $result_array;
    }

    /**
     * 取商品分类的key-value
     * @param type $goods_cat_id
     * @return boolean
     */
    protected function getGoodsCategoryMap( $goods_cat_id )
    {
        if ( empty( $goods_cat_id ) ) {
            return false;
        }
        $goods_category_array = array();
        $goods_cat_id = substr( $goods_cat_id, 1 );
        $goods_category_dao = dao_factory_base::getGoodsCategoryDao();
        $goods_category_dao->setField( 'goods_cat_id,cat_name' );
        $where = $goods_category_dao->getWhereInStatement( 'goods_cat_id', $goods_cat_id );
        $goods_category_dao->setWhere( $where );
        $goods_category_res = $goods_category_dao->getListByWhere();
        if ( $goods_category_res ) {
            foreach ( $goods_category_res as $goods_category_object ) {
                $goods_category_array[ $goods_category_object->goods_cat_id ] = $goods_category_object->cat_name;
            }
        }
        return $goods_category_array;
    }

}
