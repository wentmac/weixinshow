<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_List_admin extends service_order_List_base
{

    private $member;
    private $order_list_type;
    private $url;
    private $demo_order;
    private $domain;

    function setOrder_list_type( $order_list_type )
    {
        $this->order_list_type = $order_list_type;
    }

    function setDemo_order( $demo_order )
    {
        $this->demo_order = $demo_order;
    }

    function setDomain( $domain )
    {
        $this->domain = $domain;
    }

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
        $memberInfo = $this->getMemberInfo();
        $this->url = PHP_SELF . '?m=order';
        if ( $memberInfo !== false ) {
            if ( $this->order_list_type == service_Order_base::order_list_type_buyer ) {
                $where = "uid={$this->uid}";
            } else if ( empty( $this->member->member_type ) || $this->member->member_type == service_Member_base::member_type_seller || $this->member->member_type == service_Member_base::member_type_mall ) {
                $where = "item_uid={$this->uid}";
            } else if ( $this->member->member_type == service_Member_base::member_type_supplier ) {
                $where = "goods_uid={$this->uid}";
            }
            $this->url.= "&uid={$this->uid}";
        } else {
            $where = '1=1';
        }

        switch ( $this->order_status )
        {
            /**
             * 买家订单状态
             * 待付款
             */
            case 'order_status_buyer_waiting_payment':
                $where .= " AND order_status=" . service_Order_base::order_status_buyer_order_create;
                break;
            /**
             * 买家订单状态
             * 待发货
             * 买家等待卖家发货
             */
            case 'order_status_buyer_wating_seller_delivery':
                $where .= " AND order_status=" . service_Order_base::order_status_buyer_payment;
                break;
            /**
             * 买家订单状态
             * 待收货
             * 买家等待收货
             */
            case 'order_status_buyer_wating_receiving':
                $where .= " AND order_status=" . service_Order_base::order_status_seller_delivery;
                break;
            /**
             * 买家订单状态
             * 已经完成|买家已收货|待评价
             * 订单已完成
             */
            case 'order_status_buyer_wating_comment':
                $where .= " AND order_status=" . service_Order_base::order_status_complete
                        . " AND is_delete=0"
                        . " AND comment_status=0";
                break;
            /**
             * 买家订单状态
             * 已经完成|买家已收货
             * 订单已完成
             */
            case 'order_status_buyer_complete':
                $where .= " AND order_status=" . service_Order_base::order_status_complete;
                break;
            /**
             * 买家订单状态
             * 交易关闭
             * 订单无效关闭
             */
            case 'order_status_buyer_close':
                $where .= " AND order_status=" . service_Order_base::order_status_close;
                break;
            /**
             * 订单状态
             * 订单退款中
             */
            case 'order_status_buyer_refund':
                $where .= " AND refund_status=" . service_order_Service_base::refund_status_buyer_return;
                break;

            default :
                break;
        }
        $this->url.= "&status=" . str_replace( 'order_status_', '', $this->order_status );
        //解析$query_string
        if ( !empty( $this->query_string ) ) {
            if ( preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $this->query_string ) ) {
                $where .= " AND mobile='{$this->query_string}'";
            } elseif ( preg_match( '/^20[0-9]{15,20}$/u', $this->query_string ) ) {
                $where .= " AND order_sn='{$this->query_string}'";
            } else {//订单号                
                $where .= " AND consignee LIKE '%{$this->query_string}%'";
            }
            $this->url.= "&query_string={$this->query_string}";
        }
        //解析$demo_order
        if ( !empty( $this->demo_order ) ) {
            $demo_order = $this->demo_order - 1;
            $where .= " AND demo_order={$demo_order}";
            $this->url.= "&demo_order={$this->demo_order}";
        }
        $this->url.= '&page=';
        $where .= " AND is_delete=0";
        return $where;
    }

    /**
     *
      后台取所有的订单列表
      $order_model->setUid( $this->member->uid );
      $order_model->setQuery_string( $query_string );
      $order_model->setPagesize( $pagesize );
      $order_model->setImage_size( $image_size );
      $order_model->setOrder_status($order_status);

      $rs = $order_model->getSellerOrderList();
     */
    public function getOrderList()
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();

        $where = $this->getOrderWhere();

        $order_info_dao->setWhere( $where );
        $count = $order_info_dao->getCountByWhere();

        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $this->url );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $order_info_array = $member_mall_array = array();
        if ( $count > 0 ) {
            $order_info_dao->setLimit( $limit );
            $order_info_dao->setField( 'order_id,order_sn,order_status,refund_status,order_amount,uid,mobile,commission_fee,consignee,shipping_fee,item_uid,item_mobile,shop_name,order_goods_detail,refund_status,have_return_service,order_refund_id,supplier_mobile,goods_uid,create_time,pay_time,confirm_time,demo_order,goods_uid,coupon_money,coupon_code' );
            $order_info_dao->setOrderby( 'order_id DESC' );
            $res = $order_info_dao->getListByWhere();

            $order_config_array = Tmac::config( 'order.seller.order_status', APP_BASE_NAME );
            $demo_order_config_array = Tmac::config( 'order.system.demo_order', APP_BASE_NAME );

            $item_uid_array = array();
            foreach ( $res as $value ) {
                $value->order_goods_array = unserialize( $value->order_goods_detail );
                foreach ( $value->order_goods_array as $order_goods ) {
                    $order_goods->goods_image_url = $this->getImage( $order_goods->goods_image_id, '110', 'goods' );
                    unset( $order_goods->goods_image_id );
                }
                $value->order_status_text = $order_config_array[ $value->order_status ];
                $value->order_item_count = count( $value->order_goods_array );
                $value->supplier_status = ($this->uid == $value->goods_uid) ? true : false;
                $order_refund_info = $this->getOrderRefundInfo( $value );
                $value->money = $order_refund_info->money;
                $value->refund_service_status_text = $order_refund_info->refund_service_status_text;
                $value->create_time = date( 'Y-m-d H:i:s', $value->create_time );
                $value->pay_time = empty( $value->pay_time ) ? '' : date( 'Y-m-d H:i:s', $value->pay_time );
                $value->demo_order = $demo_order_config_array[ $value->demo_order ];
                if ( !empty( $value->coupon_code ) ) {
                    $order_amount = $value->order_amount + $value->coupon_money;
                    $value->order_amount = "总:{$order_amount}|代:{$value->coupon_money}";
                }
                unset( $value->order_goods_detail );
                //parent::handleFreeSupplierOrderShow( $value );
                $order_info_array[] = $value;
                $item_uid_array[] = $value->item_uid;
                $item_uid_string = implode( ',', $item_uid_array );
            }            
        }


        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "没有数据";
        }

        $result = array(
            'rs' => $order_info_array,            
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg
        );
        return $result;
    }

    /**
     * 取用户信息
     * @return boolean
     */
    private function getMemberInfo()
    {
        if ( empty( $this->uid ) ) {
            return false;
        }
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->uid );
        $memberInfo = $dao->getInfoByPk();
        if ( !$memberInfo ) {
            return false;
        }
        $this->member = $memberInfo;
        return $this->member;
    }

}
