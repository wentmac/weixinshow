<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: UnShippedList.class.php 367 2016-06-14 08:09:22Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_UnShippedList_manage extends service_order_List_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
      取卖家的订单列表
      $order_model->setUid( $this->memberInfo->uid );
      $order_model->setQuery_string( $query_string );
      $order_model->setPagesize( $pagesize );
      $order_model->setImage_size( $image_size );
      $order_model->setOrder_status($order_status);

      $rs = $order_model->getSellerOrderList();
     */
    public function getUnShippedOrderList()
    {
        
        $order_goods_dao = $this->order_goods_dao = dao_factory_base::getOrderGoodsDao();
                        
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_info_dao->setField('order_id');
        $order_info_dao->setWhere('is_delete=0 AND order_status=' . service_Order_base::order_status_buyer_payment);
        $where_sql = $order_info_dao->getSqlByWhere();
        $where = "order_id IN({$where_sql})";
        $order_goods_dao->setWhere( $where );        
        $order_goods_dao->setCount_field('DISTINCT goods_id,goods_sku_id');
        $count = $order_goods_dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $order_info_array = array();
        if ( $count > 0 ) {
            $order_goods_dao->setLimit( $limit );
            $order_goods_dao->setField( 'order_id,goods_id,item_name,goods_sku_id,goods_sku_name,goods_type,SUM(item_number) AS item_count' );
            $order_goods_dao->setGroupby('goods_id,goods_sku_id');
            $order_goods_dao->setOrderby( 'order_id DESC' );
            $res = $order_goods_dao->getListByWhere();
            $goods_type_array = Tmac::config('goods.goods.goods_type',APP_BASE_NAME);
            foreach ( $res as $value ) {
                $value->goods_type = $goods_type_array[$value->goods_type];
                $order_info_array[] = $value;
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
            'retcode' => 'un_shipped_order_list',
            'retmsg' => $retmsg,
            'reqdata' => $order_info_array,
        );
        return $return;
    }

}
