<?php

/**
 * WEB 商品列表 业务模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_GoodsList_manage extends service_goods_List_base
{

    private $self;

    function setSelf( $self )
    {
        $this->self = $self;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取所有资讯
     * $this->url;
     * $this->goods_cat_id;
     * $this->query_string;
     * $this->status;
     * $this->uid;
     * $this->sort;
     * $this->getGoodsList();
     * return article_class,pages
     */
    public function getGoodsList()
    {
        if ( empty( $this->url ) ) {
            $url = PHP_SELF . '?m=goods';
        } else {
            $url = $this->url;
        }
        $dao = dao_factory_base::getGoodsDao();
        $where = 'uid=' . $this->uid;
        if ( !empty( $this->goods_cat_id ) ) {
            $url .= "&goods_cat_id={$this->goods_cat_id}";
            $where .= $dao->getGoodsListWhereByCid( $this->goods_cat_id, $this->just_this_goods_cat_id );
        }
        if ( !empty( $this->goods_type ) ) {
            $url .= "&goods_type={$this->goods_type}";
            $where .= " AND goods_type={$this->goods_type}";
        }
        if ( !empty( $this->query_string ) ) {
            $url .= "&search_keyword={$this->query_string}";
            $where .= " AND goods_name LIKE '%{$this->query_string}%'";
        }
        if ( !empty( $this->status ) ) {
            $url .= "&status={$this->status}";
            $is_delete = service_goods_ItemList_base::getIsDeleteByStatus( $this->status );
            $where .= " AND is_delete={$is_delete}";
        } else {
            $where .= " AND is_delete=0";
        }
        $url .= '&page=';
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $url );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();
        $dao->setLimit( $limit );
        $dao->setField( 'goods_id,goods_name,goods_stock,goods_price,goods_image_id,sales_volume,commission_fee,goods_cat_id,goods_modify_time,goods_time,uid,goods_agent' );
        switch ( $this->sort )
        {
            case self::sort_time:
            default:
                $sort = 'goods_id DESC';
                break;
            case self::sort_sales_volume_desc:
                $sort = 'sales_volume DESC';
                break;
            case self::sort_sales_volume_asc:
                $sort = 'sales_volume ASC';
                break;
            case self::sort_stock_desc:
                $sort = 'goods_stock DESC';
                break;
            case self::sort_stock_asc:
                $sort = 'goods_stock ASC';
                break;
            case self::sort_price_desc:
                $sort = 'goods_price DESC';
                break;
            case self::sort_price_asc:
                $sort = 'goods_price ASC';
                break;
        }
        $dao->setOrderby( $sort );
        $goods_id_array = $goods_category_array = $res = array();
        if ( $count > 0 ) {
            $res = $dao->getListByWhere();
        }
        $goods_cat_id = $goods_id_string = '';

        if ( $count > 0 ) {
            foreach ( $res as $value ) {
                $goods_cat_id .= empty( $value->goods_cat_id ) ? '' : ',' . $value->goods_cat_id;

                $value->goods_image_id = $this->getImage( $value->goods_image_id, $this->image_size, 'goods' );
                $value->goods_modify_time = self::getFormatDate( $value->goods_modify_time );
                $value->goods_cat_id = empty( $value->goods_cat_id ) ? '' : explode( ',', $value->goods_cat_id );

                $goods_id_array[] = $value->goods_id;
            }
            if ( $goods_cat_id ) {
                $goods_category_array = self::getGoodsCategoryString( $goods_cat_id );
            }
            $goods_id_string = implode( ',', $goods_id_array );
        }
        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无数据!";
        }

        $result = array(
            'rs' => $res,
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg,
            'goods_category_array' => $goods_category_array,
            'goods_sku_array' => $this->_getGoodsSkuArray( $goods_id_string )
        );
        return $result;
    }

    /**
     * 取商品列表中商品分类的数组
     * @param type $goods_cat_id
     */
    protected function getGoodsCategoryString( $goods_cat_id )
    {
        $goods_cat_ids = substr( $goods_cat_id, 1 );

        $goods_cat_id_array = explode( ',', $goods_cat_ids );

        $goods_cat_id_array = array_unique( $goods_cat_id_array );
        $goods_cat_ids = implode( ',', $goods_cat_id_array );
        $goods_category_dao = dao_factory_base::getGoodsCategoryDao();
        $goods_category_dao->setField( 'goods_cat_id,cat_name' );
        $where = $goods_category_dao->getWhereInStatement( 'goods_cat_id', $goods_cat_ids );
        $goods_category_dao->setWhere( $where );
        $goods_category_res = $goods_category_dao->getListByWhere();
        $goods_category_array = array();
        if ( $goods_category_res ) {
            foreach ( $goods_category_res as $goods_category_object ) {
                $goods_category_array[ $goods_category_object->goods_cat_id ] = $goods_category_object->cat_name;
            }
        }
        return $goods_category_array;
    }

    protected function getFormatDate( $time )
    {
        $year = date( 'Y' );
        $year_this = date( 'Y', $time );

        if ( $year <> $year_this ) {
            return date( 'Y年n月j日', $time );
        } else {
            return date( 'n月j日', $time );
        }
    }

}
