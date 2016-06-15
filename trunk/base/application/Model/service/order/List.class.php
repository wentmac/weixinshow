<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_List_base extends service_Order_base
{

    protected $where;
    protected $query_string;
    protected $image_size;
    protected $pagesize;
    protected $order_status;
    protected $uid;
    protected $member_type;

    function setWhere( $where )
    {
        $this->where = $where;
    }

    function setQuery_string( $query_string )
    {
        $this->query_string = $query_string;
    }

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    function setOrder_status( $order_status )
    {
        $this->order_status = $order_status;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setMember_type( $member_type )
    {
        $this->member_type = $member_type;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取买家的订单列表
     * $this->where;
     * $this->getBuyerOrderList();
     */
    public function getBuyerOrderList()
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();

        $order_info_dao->setWhere( $this->where );
        $count = $order_info_dao->getCountByWhere();

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
            $order_info_dao->setLimit( $limit );
            $order_info_dao->setOrderby( 'order_id DESC' );
            $order_info_dao->setField( 'order_id,order_sn,order_status,shipping_status,pay_status,refund_status,order_amount,create_time,confirm_time,shipping_time,confirm_deadline_time,item_uid,shop_name,order_goods_detail,order_type,have_return_service,comment_status' );
            $res = $order_info_dao->getListByWhere();

            $order_config_array = Tmac::config( 'order.seller.order_status', APP_BASE_NAME );
            foreach ( $res as $value ) {
                $value->order_goods_array = unserialize( $value->order_goods_detail );
                foreach ( $value->order_goods_array as $order_goods ) {
                    $order_goods->goods_image_url = $this->getImage( $order_goods->goods_image_id, '110', 'goods' );
                    unset( $order_goods->goods_image_id );
                }

                $value->create_time = date( 'Y-m-d H:i:s', $value->create_time );
                $value->order_status_text = $order_config_array[ $value->order_status ];
                if ( $value->order_status == parent::order_status_complete && $value->have_return_service == 0 && ($this->now - $value->confirm_time) < parent::return_service_max_day * 86400 ) {
                    $value->return_service_status = true; //可以申请售后,这里还是有一些 不太完整，比如一个订单有两个商品时，只要申请了一个售后，其他的商品就不能在列表页面中申请了
                } else {
                    $value->return_service_status = false;
                }
                if ( $value->confirm_deadline_time > $this->now ) {
                    //true:可以延长收货 false:不能延长收货
                    $value->extend_confirm_deadline_time_status = ($value->confirm_deadline_time - $value->shipping_time) > (8 * 86400) ? false : true;
                    //离自动确认收货还剩的秒数
                    $value->confirm_deadline_time = $value->confirm_deadline_time - $this->now;
                } else {
                    $value->confirm_deadline_time = 0;
                    $value->extend_confirm_deadline_time_status = false;
                }

                unset( $value->order_goods_detail, $value->confirm_time, $value->shipping_time );
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
            'retcode' => 'buyer_order_list',
            'retmsg' => $retmsg,
            'reqdata' => $order_info_array,
        );
        return $return;
    }

    /**
     * todo delete
     */
    public function updateOrderGoodsDetail()
    {
        $dao = dao_factory_base::getOrderInfoDao();
        $res = $dao->getListByWhere();
        foreach ( $res as $value ) {
            $entity_OrderInfo_base = new entity_OrderInfo_base();
            $entity_OrderInfo_base->order_goods_detail = $this->getOrderGoodsDetail( $value->order_id );
            $dao->setPk( $value->order_id );
            $dao->updateByPk( $entity_OrderInfo_base );
        }
    }

    /**
     * todo delete
     * @param type $order_id
     * @return type
     */
    private function getOrderGoodsDetail( $order_id )
    {
        $dao = dao_factory_base::getOrderGoodsDao();
        $dao->setWhere( 'order_id=' . $order_id );
        $dao->setField( 'item_id,item_name,item_number,item_price,goods_image_id,goods_sku_name' );
        $res = $dao->getListByWhere();

        $order_goods_detail_array = array();
        foreach ( $res AS $order_goods_info ) {
            $order_goods_detail_array[] = $order_goods_info;
        }
        return serialize( $order_goods_detail_array );
    }

    /**
     * 取退款金额和退款类型 给列表页面中用
     */
    protected function getOrderRefundInfo( $order_info )
    {
        $order_refund_info = new stdClass();
        $order_refund_info->money = 0;
        $order_refund_info->refund_service_status_text = '';
        if ( $order_info->have_return_service == 0 && $order_info->refund_status == service_order_Service_base::refund_status_buyer_return && $order_info->supplier_status == true ) {
            $order_refund_array = Tmac::config( 'order.order_refund', APP_BASE_NAME );
            $dao = dao_factory_base::getOrderRefundDao();
            $dao->setPk( $order_info->order_refund_id );
            $dao->setField( 'money,refund_service_status' );
            $order_refund_info = $dao->getInfoByPk();
            if ( $order_refund_info ) {
                $order_refund_info->refund_service_status_text = $order_refund_array[ 'refund_service_status' ][ $order_refund_info->refund_service_status ];
            }
        }
        return $order_refund_info;
    }

    /**
     * 取快递公司名称
     * 数组
     */
    public function getExpressArray()
    {
        $dao = dao_factory_base::getExpressDao();
        $dao->setField( 'express_id,express_name' );
        $dao->setOrderby( 'express_id ASC' );
        $res = $dao->getListByWhere();
        return $res;
    }

}
