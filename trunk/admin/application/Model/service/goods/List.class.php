<?php

/**
 * WEB 商品列表 业务模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_List_admin extends service_goods_List_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取所有商品列表
     * $this->uid;
     * $this->is_supplier;
     * $this->goods_source;
     * $this->goods_cat_id;
     * $this->query_string;
     * $this->sort;     
     */
    public function getGoodsList()
    {

        if ( empty( $this->url ) ) {
            $url = PHP_SELF . '?m=goods/index';
        } else {
            $url = $this->url;
        }
        $dao = dao_factory_base::getGoodsDao();
        $where = '1=1';
        if ( !empty( $this->uid ) ) {
            $where .= ' AND uid=' . $this->uid;
            $url .= "&uid={$this->uid}";
        }
        if ( !empty( $this->is_supplier ) ) {
            if ( $this->is_supplier == 3 ) {
                $where .= " AND is_supplier=1 AND uid<>46";
            } else {
                $is_supplier = $this->is_supplier - 1;
                $where .= ' AND is_supplier=' . $is_supplier;
            }
            $url .= "&is_supplier={$this->is_supplier}";
        }
        if ( !empty( $this->goods_source ) ) {
            $goods_source = $this->goods_source - 1;
            $where .= ' AND goods_source=' . $goods_source;
            $url .= "&goods_source={$this->goods_source}";
        }
        if ( !empty( $this->goods_source_id ) ) {            
            $where .= ' AND goods_source_id=' . $this->goods_source_id;
            $url .= "&goods_source_id={$this->goods_source_id}";
        }
        if ( !empty( $this->goods_cat_id ) ) {
            $url .= "&goods_cat_id={$this->goods_cat_id}";
            $where .= $dao->getGoodsListWhereByCid( $this->goods_cat_id, $this->just_this_goods_cat_id );
        }
        if ( !empty( $this->just_this_goods_cat_id ) ) {
            $url .= "&just_this_goods_cat_id={$this->just_this_goods_cat_id}";
        }

        if ( !empty( $this->brand_id ) ) {
            $url .= "&brand_id={$this->brand_id}";
            $where .= " AND brand_id={$this->brand_id}";
        }
        if ( !empty( $this->query_string ) ) {
            $url .= "&query_string={$this->query_string}";
            $where .= " AND goods_name LIKE '%{$this->query_string}%'";
        }
        $url .= '&sort=' . $this->sort . '&pagesize=' . $this->pagesize . '&page=';
        $where .= " AND is_delete=0";
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        $rs = array();


        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $url );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $dao->setField( 'goods_id,goods_cat_id,goods_name,goods_image_id,goods_price,commission_fee,goods_stock,sales_volume,goods_time,uid,goods_source,goods_source_id,is_supplier,goods_sort,brand_id,brand_name' );

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
                $sort = 'goods_price DESC';
                break;
            case self::sort_stock_asc:
                $sort = 'goods_stock ASC';
                break;
            case self::sort_goods_sort_desc:
                $sort = 'goods_sort DESC';
                break;
        }
        if ( $count > 0 ) {
            $dao->setOrderby( $sort );
            $dao->setLimit( $limit );

            $rs = $dao->getListByWhere();
        }
        $image_size = '110';
        $goods_cat_id = '';
        $goods_id_string = '';
        //遍历通过class_id取class_name
        if ( is_array( $rs ) ) {
            $is_supplier_array = Tmac::config( 'goods.goods.is_supplier', APP_BASE_NAME );
            $goods_source_array = Tmac::config( 'goods.goods.goods_source', APP_BASE_NAME );
            foreach ( $rs AS $v ) {
                $goods_cat_id.= empty( $v->goods_cat_id ) ? '' : ',' . $v->goods_cat_id;
                $goods_id_string .= ',' . $v->goods_id;
                $v->goods_time = date( 'Y-m-d H:i:s', $v->goods_time );
                $v->goods_image_id = THUMB_URL . 'goods_' . $image_size . '/' . $v->goods_image_id . '.jpg';
                $v->goods_cat_id = empty( $v->goods_cat_id ) ? '' : explode( ',', $v->goods_cat_id );
                $v->is_supplier = $is_supplier_array[ $v->is_supplier ];
                $v->goods_source = $goods_source_array[ $v->goods_source ];
            }
            $goods_id_string = substr( $goods_id_string, 1 );
        }
        $goods_category_array = $this->getGoodsCategoryMap( $goods_cat_id );        

        //把文章的当前page写到cookies里
        //HttpResponse::setCookie( 'article_page_' . $channelid, $pages->getNowPage() );
        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无商品!";
        }

        $result = array(
            'rs' => $rs,
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg,
            'goods_category_array' => $goods_category_array,
            'goods_sku_array' => $this->_getGoodsSkuArray( $goods_id_string )
        );
        return $result;
    }

}
