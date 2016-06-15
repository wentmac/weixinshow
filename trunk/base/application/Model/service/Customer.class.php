<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Customer.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Customer_base extends service_Model_base
{

    protected $uid;
    protected $customer_id;
    protected $pagesize;
    protected $image_size;
    protected $errorMessage;
    protected $memberInfo;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setCustomer_id( $customer_id )
    {
        $this->customer_id = $customer_id;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取所有资讯
     * return article_class,pages
     */
    public function getCustomerList()
    {
        $dao = dao_factory_base::getCustomerDao();
        $where = 'item_uid=' . $this->uid;
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

        $dao->setField( '*' );

        $dao->setOrderby( 'customer_id DESC' );
        $dao->setLimit( $limit );
        $rs = array();
        if ( $count > 0 ) {
            $rs = $dao->getListByWhere();
            //遍历通过class_id取class_name
            foreach ( $rs AS $v ) {
                $default_avatar_url = STATIC_URL . 'common/avatar.png';
                $v->member_image_id = empty( $v->member_image_id ) ? $default_avatar_url : $this->getImage( $v->member_image_id, $this->image_size, 'avatar' );
                $this->handleFreeSupplierOrderShow( $v );
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
            'retcode' => 'customer_list',
            'retmsg' => $retmsg,
            'reqdata' => $rs,
        );
        return $return;
    }

    /**
     * $model->setUid( $this->memberInfo->uid );
     * $model->setCustomer_id( $customer_id );
     * $model->setImage_size( $image_size );
     * $model->getCustomerInfo();
     */
    public function getCustomerInfo()
    {
        $dao = dao_factory_base::getCustomerDao();
        $dao->setField( 'customer_id,customer_uid,item_uid,realname,full_address,mobile,weixin_id,transaction_count,transaction_amount' );
        $dao->setPk( $this->customer_id );
        $customer_info = $dao->getInfoByPk();
        if ( !$customer_info ) {
            $this->errorMessage = '客户不存在';
            return false;
        }
        if ( $customer_info->item_uid <> $this->uid ) {
            $this->errorMessage = '没有权限';
            return false;
        }

        $this->handleFreeSupplierOrderShow( $customer_info );
        return $customer_info;
    }

    /**
     * 取卖家的与客户的订单列表
     * $this->where;
     * $this->getBuyerOrderList();
     */
    public function getSellerCustomerOrderList( $item_uid, $customer_uid )
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $where = "uid={$customer_uid} AND is_delete=0 AND item_uid={$item_uid}";
        $order_info_dao->setWhere( $where );
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
            $order_info_dao->setField( 'order_id,order_sn,order_status,order_amount,shipping_fee,consignee,create_time,item_uid,shop_name,order_goods_detail' );
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
                unset( $value->order_goods_detail );
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
            'retcode' => 'customer_order_list',
            'retmsg' => $retmsg,
            'reqdata' => $order_info_array,
        );
        return $return;
    }

    /**
     * 处理免费供应商的订单显示
     * @param type $order_info
     */
    protected function handleFreeSupplierOrderShow( $customer_info )
    {        
        if ( $this->memberInfo->member_type == service_Member_base::member_type_supplier && $this->memberInfo->member_class == service_Member_base::member_class_supplier_free ) {
            if ( isset( $customer_info->mobile ) ) {
                //免费供应商，不能看到手机号全部
                $customer_info->mobile = substr_replace( $customer_info->mobile, '****', 3, 4 );
            }

            if ( isset( $customer_info->full_address ) ) {
                $address_length = mb_strlen( $customer_info->full_address, 'utf8' );
                $customer_info->full_address = str_repeat( '*', $address_length ) . '(请联系银品惠客服4008-456-090查看该订单详情)';
            }
        } else if ( $this->memberInfo->member_type == service_Member_base::member_type_seller ) {
            if ( isset( $customer_info->mobile ) ) {
                //免费供应商，不能看到手机号全部
                $customer_info->mobile = substr_replace( $customer_info->mobile, '****', 3, 4 );
            }
        }
        return $customer_info;
    }

}
