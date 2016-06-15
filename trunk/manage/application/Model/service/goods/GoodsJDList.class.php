<?php

/**
 * WEB 商品列表 业务模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_GoodsJDList_manage extends service_Model_base
{

    protected $errorMessage;
    private $is_delete;
    private $pagesize;

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setIs_delete( $is_delete )
    {
        $this->is_delete = $is_delete;
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
     * 获取所有资讯
     * return article_class,pages
     */
    public function getGoodsList()
    {
        $dao = dao_factory_base::getGoodsDao();
        $where = 'goods_source=1';

        if ( !empty( $this->is_delete ) ) {
            $is_delete = $this->is_delete - 1;
            $where.=' AND is_delete=' . $is_delete;
        } else {
            $where.=' AND is_delete!=1';
        }

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

        $dao->setField( 'goods_id,goods_name,goods_source_id,goods_price,goods_stock,promote_start_date,promote_end_date,commission_type,commission_scale,is_delete' );
        $dao->setOrderby( 'goods_id ASC' );
        $dao->setLimit( $limit );
        $result_array = array();
        if ( $count > 0 ) {
            $result_array = $dao->getListByWhere();
            foreach ( $result_array AS $v ) {
                $v->goods_sku_array = $this->getGoodsSku( $v->goods_id );
            }
        }


        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / $this->pagesize ) ),
            'pagesize' => $this->pagesize,
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'goods_list',
            'retmsg' => $retmsg,
            'reqdata' => $result_array,
        );
        return $return;
    }

    private function getGoodsSku( $goods_id )
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setWhere( 'goods_id=' . $goods_id );
        $dao->setField( 'goods_sku_id,goods_id,goods_sku,goods_sku_json,price,stock,commission_fee,is_delete' );
        $res = $dao->getListByWhere();
        if ( $res ) {
            foreach ( $res as $goods_sku_object ) {
                $goods_sku_json_array = unserialize( $goods_sku_object->goods_sku_json );
                $sku_name = '';
                foreach ( $goods_sku_json_array AS $sku_name_object ) {
                    $sku_name .= '[' . $sku_name_object[ 'spec_value_name' ] . ']';
                }
                $goods_sku_object->sku_name = $sku_name;
            }
        }
        return $res;
    }

}
