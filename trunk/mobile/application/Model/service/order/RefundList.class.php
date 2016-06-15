<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: RefundList.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_RefundList_mobile extends service_order_RefundList_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取订单列表的where语句
     * $this->order_status;
     * $this->uid;
     * $this->getOrderListWhere();
     */
    public function getOrderWhere()
    {
        $where = "uid={$this->uid}";
        switch ( $this->order_status )
        {
            /**
             * 待卖家处理             
             */
            case 'seller_confirm':
                $where .= " AND service_status=" . service_order_Service_base::service_status_waiting_seller_confirm;
                break;
            /**
             * 待买家处理
             */
            case 'buyer_confirm':
                $where .= " AND service_status=" . service_order_Service_base::service_status_waiting_buyer_confirm;
                break;
            /**
             * 银品惠客服介入
             */
            case 'customer_confirm':
                $where .= " AND service_status=" . service_order_Service_base::service_status_waiting_customer_confirm;
                break;
            /**
             * 同意退款
             */
            case 'complete':
                $where .= " AND service_status=" . service_order_Service_base::service_status_success;
                break;
            /**
             * 撤销维权
             */
            case 'close':
                $where .= " AND service_status=" . service_order_Service_base::service_status_close;
                break;


            default :
                break;
        }
        //解析$query_string
        if ( !empty( $this->query_string ) ) {
            if ( preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $this->query_string ) ) {
                $where .= " AND mobile='{$this->query_string}'";
            } elseif ( preg_match( '/^20[0-9]{15,20}$/u', $this->query_string ) ) {
                $where .= " AND order_sn='{$this->query_string}'";
            } else {//订单号                
                $where .= " AND consignee LIKE '%{$this->query_string}%'";
            }
        }
        //$where .= " AND is_delete=0";
        $this->where = $where;
        return $where;
    }

    /**
     *
      取卖家的订单列表
      $order_model->setUid( $this->memberInfo->uid );
      $order_model->setQuery_string( $query_string );
      $order_model->setPagesize( $pagesize );
      $order_model->setImage_size( $image_size );
      $order_model->setOrder_status($order_status);

      $rs = $order_model->getSellerOrderRefundList();
     */
    public function getBuyerOrderRefundList()
    {
        $order_refund_dao = dao_factory_base::getOrderRefundDao();

        $where = $this->getOrderWhere();

        $order_refund_dao->setWhere( $where );
        $count = $order_refund_dao->getCountByWhere();

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
            $order_refund_dao->setLimit( $limit );
            $order_refund_dao->setField( 'order_refund_id,order_id,order_sn,order_goods_id,order_goods_detail,item_uid,shop_name,refund_service_status,money,service_status,refund_status,return_status,consignee,goods_uid,supplier_mobile' );
            $order_refund_dao->setOrderby( 'order_refund_id DESC' );
            $res = $order_refund_dao->getListByWhere();

            foreach ( $res as $value ) {
                $value->order_goods_array = unserialize( $value->order_goods_detail );
                foreach ( $value->order_goods_array as $order_goods ) {
                    $order_goods->goods_image_url = $this->getImage( $order_goods->goods_image_id, '110', 'goods' );
                    unset( $order_goods->goods_image_id );
                }
                $service_status_map = self::getServiceStatusText( $value->service_status, $value->refund_status, $value->return_status );
                $value->status_text = $service_status_map[ 'buyer' ];
                $value->order_item_count = count( $value->order_goods_array );
                unset( $value->order_goods_detail, $value->goods_uid );
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
            'retcode' => 'buyer_order_refund_list',
            'retmsg' => $retmsg,
            'reqdata' => $order_info_array,
        );
        return $return;
    }

}
