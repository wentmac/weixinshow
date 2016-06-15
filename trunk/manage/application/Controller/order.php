<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: order.php 367 2016-06-14 08:09:22Z zhangwentao $
 * http://www.t-mac.org；
 */
class orderAction extends service_Controller_manage
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 取总数
     */
    public function get_order_status_count()
    {
        $order_model = new service_order_Home_manage();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setMember_type( $this->memberInfo->member_type );
        $res = $order_model->getSellerOrderCountArray();
        $this->apiReturn( $res );
    }

    /**
     * 取订单列表
     */
    public function index()
    {
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $searchParameter[ 'status' ] = $status;
        $searchParameter[ 'pagesize' ] = $pagesize;
        $searchParameter[ 'query' ] = $query_string;

        $array[ 'searchParameter' ] = json_encode( $searchParameter );
        $this->assign( $array );
        $this->V( 'order/list' );
    }

    /**
     * 取订单列表
     */
    public function get_list()
    {
        //（待付款：waiting_payment｜待发货：wating_seller_delivery｜已发货：wating_receiving｜已完成：complete｜已关闭：close｜退款中：refund）
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $image_size = Input::get( 'image_size', 110 )->imageSize();
        $address_id = Input::get( 'address_id', 0 )->int();
        $start_date = Input::get( 'start_date', '' )->date();
        $end_date = Input::get( 'end_date', '' )->date();
        $query_id = Input::get( 'query_id', 0 )->int();
        $query_id_type = Input::get( 'query_id_type', '' )->string();

        $order_status = '';
        if ( !empty( $status ) ) {
            $order_status = 'order_status_buyer_' . $status;
        }

        $order_model = new service_order_List_manage();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setQuery_string( $query_string );
        $order_model->setPagesize( $pagesize );
        $order_model->setImage_size( $image_size );
        $order_model->setOrder_status( $order_status );
        $order_model->setMember_type( $this->memberInfo->member_type );
        $order_model->setMemberInfo( $this->memberInfo );
        $order_model->setAddress_id( $address_id );
        $order_model->setStart_date( $start_date );
        $order_model->setEnd_date( $end_date );
        $order_model->setQuery_id_type( $query_id_type );
        $order_model->setQuery_id( $query_id );

        $rs = $order_model->getSellerOrderList();
        $this->apiReturn( $rs );
    }

    /**
     * 导出订单列表
     */
    public function export_order_list()
    {
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query', '' )->string();
        $export_start_page = Input::get( 'export_start_page', 1 )->int();
        $export_end_page = Input::get( 'export_end_page', 10 )->imageSize();

        $order_status = '';
        if ( !empty( $status ) ) {
            $order_status = 'order_status_buyer_' . $status;
        }

        $order_model = new service_order_List_manage();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setQuery_string( $query_string );
        $order_model->setOrder_status( $order_status );
        $order_model->setMember_type( $this->memberInfo->member_type );
        $order_model->setMemberInfo( $this->memberInfo );
        $order_model->setExport_start_page( $export_start_page );
        $order_model->setExport_end_page( $export_end_page );

        set_time_limit( 0 );
        $order_model->exportSellerOrderList();
        exit;
    }

    /**
     * 取快递列表
     */
    public function get_express()
    {
        $order_model = new service_order_List_manage();
        $res = $order_model->getExpressArray();
        $this->apiReturn( $res );
    }

    /**
     * 发货操作
     */
    public function delivery()
    {
        $order_id_string = Input::post( 'order_id', 0 )->required( '订单号不能为空' )->intString();
        $express_id = Input::post( 'express_id', 0 )->int();
        $express_name = Input::post( 'express_name', 0 )->string();
        $express_no = Input::post( 'express_no', 0 )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        if ( empty( $express_id ) && empty( $express_name ) ) {
            throw new ApiException( '快递名称不能为空' );
        }


        if ( $express_id <> -1 && empty( $express_no ) ) {
            throw new ApiException( '快递单号不能为空' );
        }


        $model = new service_order_SellerHandle_manage();
        $model->setOrder_id_string( $order_id_string );
        $model->setExpress_id( $express_id );
        $model->setExpress_name( $express_name );
        $model->setExpress_no( $express_no );
        $model->setUid( $this->memberInfo->uid );
        $res = $model->sellerDelivery();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'order_status_text' => $model->getOrder_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 发货操作
     */
    public function note_save()
    {
        $order_id = Input::post( 'order_id', 0 )->required( '订单号不能为空' )->int();
        $order_note = Input::post( 'order_note', 0 )->required( '订单备注不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_SellerHandle_manage();
        $model->setOrder_id( $order_id );

        $checkPurview = $model->checkPurviewByItemUid( $this->memberInfo->uid );
        if ( $checkPurview === false ) {
            throw new ApiException( $model->getErrorMessage() );
        }

        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->order_note = $order_note;

        $order_info_res = $model->updateOrderInfo( $entity_OrderInfo_base );
        $this->apiReturn( $order_info_res );
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $order_id = Input::get( 'order_id', 0 )->required( '订单号不能为空' )->int();
        $image_size = Input::get( 'image_size', 110 )->imageSize();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_order_SellerHandle_manage();
        $model->setOrder_id( $order_id );

        $checkPurview = $model->checkPurviewByItemUid( $this->memberInfo->uid );
        if ( $checkPurview === false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $model->setUid( $this->memberInfo->uid );
        $model->setImage_size( $image_size );
        $model->setMemberInfo( $this->memberInfo );
        $order_info = $model->getOrderInfoDetail();

        $array[ 'order_info' ] = $order_info;
        $this->assign( $array );
//		echo '<pre>';
//		print_r($array);
//		die;
        $this->V( 'order/detail' );
    }

    /**
     * 取物流跟踪
     * @throws ApiException
     */
    public function get_express_info()
    {
        $express_id = Input::get( 'express_id', 0 )->required( '快递公司名称不能为空' )->int();
        $express_no = Input::get( 'express_no', '' )->required( '快递单号不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_Express_base();
        $model->setExpress_id( $express_id );
        $model->setExpress_no( $express_no );
        $express_info = $model->getExpressInfo();
        if ( !$express_info ) {
            throw new ApiException( '快递公司不存在' );
        }

        $express_detail = $model->getExpressDetail();
        $array[ 'express_detail' ] = $express_detail;
        $this->apiReturn( $array );
    }

    /**
     * 取退款的列表总数
     */
    public function get_refund_count()
    {
        $order_model = new service_order_Home_manage();
        $order_model->setUid( $this->memberInfo->uid );
        $res = $order_model->getSellerOrderRefundCountArray();
        $this->apiReturn( $res );
    }

    /**
     * 售后维权订单列表
     */
    public function refund()
    {
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $searchParameter[ 'status' ] = $status;
        $searchParameter[ 'pagesize' ] = $pagesize;
        $searchParameter[ 'query' ] = $query_string;
        $array[ 'searchParameter' ] = json_encode( $searchParameter );
        $rs = $searchParameter;
        $this->assign( $array );
        $this->assign( $rs );

//		echo '<pre>';
//		print_r($rs);
        $this->V( 'order/refund_list' );
    }

    /**
     * 取售后维权订单列表
     */
    public function get_refund_list()
    {
        //（待卖家处理：seller_confirm｜待买家处理：buyer_confirm｜银品惠客服介入：customer_confirm｜同意退款：complete｜撤销维权：close）
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $image_size = Input::get( 'image_size', 110 )->imageSize();

        $order_model = new service_order_RefundList_manage();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setQuery_string( $query_string );
        $order_model->setPagesize( $pagesize );
        $order_model->setImage_size( $image_size );
        $order_model->setOrder_status( $status );

        $rs = $order_model->getSellerOrderRefundList();
        $this->apiReturn( $rs );
    }

    /**
     * 查看退款详情
     */
    public function refund_detail()
    {
        $order_refund_id = Input::get( 'order_refund_id', 0 )->required( '订单号不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        //检测权限
        $model = new service_order_RefundDetail_manage();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );
        $model->setIdentity( 'seller' );
        try {
            //$model->checkPurviewBySeller();
            $model->getOrderRefundInfo();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $array[ 'order_refund_info' ] = $model->handleOrderRefundInfo();
        $array[ 'order_service_list' ] = $model->getOrderServiceArray();

        $this->assign( $array );
//				echo '<pre>';
//				print_r($array);
//				die;
        $this->V( 'order/refund_detail' );
    }

    /**
     * 同意退款
     */
    public function refund_yes()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '退款详细不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_SellerRefundHandle_manage();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );
        //权限检测
        try {
            $model->checkPurviewBySeller();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $res = $model->refundAgree();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'refund_status_text' => $model->getRefund_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 拒绝退款
     */
    public function refund_no()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '退款详细不能为空' )->int();
        $reason = Input::post( 'reason', '' )->string();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_SellerRefundHandle_manage();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );

        //权限检测
        try {
            $model->checkPurviewBySeller();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $res = $model->refundDisagree( $reason );
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'refund_status_text' => $model->getRefund_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 收到买家的退货
     */
    public function receipt_yes()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '退款详细不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_order_SellerRefundHandle_manage();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );

        //权限检测
        try {
            $model->checkPurviewBySeller();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $res = $model->receiptYes();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'refund_status_text' => $model->getRefund_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 没有收到买家的退货
     */
    public function receipt_no()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '退款详细不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_order_SellerRefundHandle_manage();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );

        //权限检测
        try {
            $model->checkPurviewBySeller();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $res = $model->receiptNo();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'refund_status_text' => $model->getRefund_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 未发货的商品列表
     */
    public function un_shipped()
    {
        $pagesize = Input::get( 'pagesize', 20 )->int();

        $searchParameter[ 'pagesize' ] = $pagesize;

        $array[ 'searchParameter' ] = json_encode( $searchParameter );
        $this->assign( $array );
        $this->V( 'order/un_shipped_list' );
    }

    /**
     * 取未发货的商品列表数据
     */
    public function get_un_shipped()
    {
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $order_model = new service_order_UnShippedList_manage();
        $order_model->setPagesize( $pagesize );
        $rs = $order_model->getUnShippedOrderList();
        
        $this->apiReturn( $rs );
    }

}
