<?php

/**
 * WEB 商品列表 业务模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_ItemList_base extends Model
{

    const sort_time = 1;  //上架时间
    const sort_sales_volume_desc = 2;  //销量从高到低
    const sort_sales_volume_asc = 3;  //销量从低到高    
    const sort_stock_desc = 4;  //库存从高到低
    const sort_stock_asc = 5;  //库存从低到高
    const sort_price_desc = 6;  //价格从低到高
    const sort_price_asc = 7;  //价格从低到高

    protected $errorMessage;
    protected $uid;
    protected $url;
    protected $item_cat_id;
    protected $query_string;
    protected $sort;
    protected $status; //1：is_delete=1|0:is_delete=2
    protected $self; //yes：自营｜no:供销

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setUrl( $url )
    {
        $this->url = $url;
    }

    function setitem_cat_id( $item_cat_id )
    {
        $this->item_cat_id = $item_cat_id;
    }

    function setQuery_string( $query_string )
    {
        $this->query_string = $query_string;
    }

    function setSort( $sort )
    {
        $this->sort = $sort;
    }

    function setStatus( $status )
    {
        $this->status = $status;
    }

    function setSelf( $self )
    {
        $this->self = $self;
    }

    /**
     * 获取所有资讯
     * return article_class,pages
     */
    public function getItemList()
    {

        if ( empty( $this->url ) ) {
            $url = PHP_SELF . '?m=seller/goods';
        } else {
            $url = $this->url;
        }
        $dao = dao_factory_base::getItemDao();
        $where = 'uid=' . $this->uid;
        if ( !empty( $this->item_cat_id ) ) {
            $url .= "&item_cat_id={$this->item_cat_id}";
            $where = $dao->getItemListWhereByCid( $where, $this->item_cat_id );
        }
        if ( !empty( $this->query_string ) ) {
            $url .= "&search_keyword={$this->query_string}";
            $where .= " AND item_name LIKE '%{$this->query_string}%'";
        }

        if ( !empty( $this->status ) ) {
            $url .= "&status={$this->status}";
            $is_delete = self::getIsDeleteByStatus( $this->status );
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
        $pages->setPrepage( 10 );
        $limit = $pages->getSqlLimit();

        $dao->setField( 'item_id,goods_id,item_cat_id,item_name,goods_image_id,item_price,item_stock,sales_volume,commission_fee,item_modify_time,supplier_offline' );

        switch ( $this->sort )
        {
            case self::sort_time:
            default:
                $sort = 'item_id DESC';
                break;
            case self::sort_sales_volume_desc:
                $sort = 'sales_volume DESC';
                break;
            case self::sort_sales_volume_asc:
                $sort = 'sales_volume ASC';
                break;
            case self::sort_stock_desc:
                $sort = 'item_stock DESC';
                break;
            case self::sort_stock_asc:
                $sort = 'item_stock ASC';
                break;
            case self::sort_price_desc:
                $sort = 'item_price DESC';
                break;
            case self::sort_price_asc:
                $sort = 'item_price ASC';
                break;
        }
        $dao->setOrderby( $sort );
        $dao->setLimit( $limit );

        $rs = $dao->getListByWhere();


        $image_size = '110';
        $item_cat_id = '';
        $goods_id_string = '';
        //遍历通过class_id取class_name
        if ( is_array( $rs ) ) {
            foreach ( $rs AS $k => $v ) {
                $item_cat_id.= empty( $v->item_cat_id ) ? '' : ',' . $v->item_cat_id;
                $goods_id_string .= ',' . $v->goods_id;
                $rs[ $k ]->item_modify_time = self::getFormatDate( $v->item_modify_time );
                $rs[ $k ]->goods_image_id = THUMB_URL . 'goods_' . $image_size . '/' . $v->goods_image_id . '.jpg';
                $rs[ $k ]->item_cat_id = empty( $v->item_cat_id ) ? '' : explode( ',', $v->item_cat_id );
            }
        }
        $goods_category_array = array(
            0 => ''
        );
        if ( $item_cat_id ) {
            $item_cat_id = substr( $item_cat_id, 1 );
            $item_category_dao = dao_factory_base::getItemCategoryDao();
            $item_category_dao->setField( 'item_cat_id,cat_name' );
            $where = $item_category_dao->getWhereInStatement( 'item_cat_id', $item_cat_id );
            $item_category_dao->setWhere( $where );
            $goods_category_res = $item_category_dao->getListByWhere();
            if ( $goods_category_res ) {
                foreach ( $goods_category_res as $goods_category_object ) {
                    $goods_category_array[ $goods_category_object->item_cat_id ] = $goods_category_object->cat_name;
                }
            }
            $goods_id_string = substr( $goods_id_string, 1 );
        }

        //把文章的当前page写到cookies里
        //HttpResponse::setCookie( 'article_page_' . $channelid, $pages->getNowPage() );
        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无图片!";
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

    /**
     * 取列表中所有的item_id的sku
     * @param type $goods_id_string
     */
    protected function _getGoodsSkuArray( $goods_id_string )
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setField( 'goods_id,goods_sku_json,price,stock,sales_volume' );
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
        }
        return $result_array;
    }

    /**
     * 状态转换
     * @param type $status
     * @return type
     */
    public static function getIsDeleteByStatus( $status )
    {
        switch ( $status )
        {
            case 'on':
            default:
                $is_delete = 0;
                break;
            case 'del':
            case 'delete':
                $is_delete = 1;
                break;
            case 'off':
                $is_delete = 2;
                break;
        }
        return $is_delete;
    }

}
