<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: refund.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class refundAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 会员中心
     * 用户全部售后退款
     * 表：wsw_order_refund<订单商品维权售后申请表>数据结构说明 [保密资料]
     */
    public function index()
    {
        //（待卖家处理：seller_confirm｜待买家处理：buyer_confirm｜银品惠客服介入：customer_confirm｜同意退款：complete｜撤销维权：close）
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $image_size = Input::get( 'image_size', 110 )->imageSize();

        $order_model = new service_order_RefundList_mobile();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setQuery_string( $query_string );
        $order_model->setPagesize( $pagesize );
        $order_model->setImage_size( $image_size );
        $order_model->setOrder_status( $status );

        $rs = $order_model->getBuyerOrderRefundList();
//      $this->apiReturn( $rs );
//      exit;
        $array[ 'status' ] = $status;
        $this->assign( $array );
        $this->assign( $rs );
//		echo '<pre>';
//		 print_r( $rs );

        $this->V( 'member/refund_list' );
    }

    /**
     * 会员中心
     * 用户全部售后退款
     * 表：wsw_order_refund<订单商品维权售后申请表>数据结构说明 [保密资料]
     */
    public function get_list()
    {
        //（待卖家处理：seller_confirm｜待买家处理：buyer_confirm｜银品惠客服介入：customer_confirm｜同意退款：complete｜撤销维权：close）
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $image_size = Input::get( 'image_size', 110 )->imageSize();

        $order_model = new service_order_RefundList_mobile();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setQuery_string( $query_string );
        $order_model->setPagesize( $pagesize );
        $order_model->setImage_size( $image_size );
        $order_model->setOrder_status( $status );

        $rs = $order_model->getBuyerOrderRefundList();
        $this->apiReturn( $rs );
    }

    /**
     * 退货页面
     */
    public function returned()
    {
        $order_refund_id = Input::get( 'order_refund_id', 0 )->required( '请填写订单售后ID' )->int();
        $model = new service_order_BuyerRefundHandle_mobile();

        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );

        //检测当前退货人的权限
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            $this->redirect( $exc->getMessage() );
        }
        $this->assign( 'order_refund_id', $order_refund_id );
        $this->V( 'member/returned' );
    }

    /**
     * 退货保存
     */
    public function returned_save()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '请填写订单售后ID' )->int();
        $express_id = Input::post( 'express_id', 0 )->int();
        $express_name = Input::post( 'express_name', 0 )->string();
        $express_no = Input::post( 'express_no', 0 )->string();
        $is_modify = Input::post( 'is_modify', 0 )->int(); //如果是1代表 是重新修改退货信息

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        if ( empty( $express_id ) && empty( $express_name ) ) {
            throw new ApiException( '快递名称不能为空' );
        }

        if ( $express_id <> -1 && empty( $express_no ) ) {
            throw new ApiException( '快递单号不能为空' );
        }

        $model = new service_order_BuyerRefundHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );
        $model->setIs_modify( $is_modify );
        $model->setExpress_id( $express_id );
        $model->setExpress_name( $express_name );
        $model->setExpress_no( $express_no );
        //检测当前退货人的权限
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $res = $model->executeReturn();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $this->apiReturn();
    }

    /**
     * 取快递列表
     */
    public function get_express()
    {
        $order_model = new service_order_List_mobile();
        $res = $order_model->getExpressArray();
        $this->apiReturn( $res );
    }

    /**
     * 买家取消/关闭退款
     */
    public function returned_cancel()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '请填写订单售后ID' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_BuyerRefundHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );
        //检测当前退货人的权限
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $res = $model->executeCancel();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'refund_status_text' => $model->getRefund_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 买家 申请客服介入
     * 退款的介入
     */
    public function intervene_refund()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '请填写订单售后ID' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_order_BuyerRefundHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );
        //检测当前退货人的权限
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $model->refundOrderIntervene();
        $res = $model->createOrderIntervene();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'refund_status_text' => $model->getRefund_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 买家 申请客服介入
     */
    public function intervene_return()
    {
        $order_refund_id = Input::post( 'order_refund_id', 0 )->required( '请填写订单售后ID' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_order_BuyerRefundHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );
        //检测当前退货人的权限
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $model->returnOrderIntervene();
        $res = $model->createOrderIntervene();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $array = array(
            'refund_status_text' => $model->getRefund_status_text()
        );
        $this->apiReturn( $array );
    }

    /**
     * 查看退款详情
     */
    public function detail()
    {
        $order_refund_id = Input::get( 'order_refund_id', 0 )->required( '订单号不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        
        //检测权限
        $model = new service_order_RefundDetail_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_refund_id( $order_refund_id );
        $model->setIdentity( 'buyer' );
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $array[ 'order_refund_info' ] = $model->handleOrderRefundInfo();
        $array[ 'order_service_list' ] = $model->getOrderServiceArray();

//      $this->apiReturn( $array );
//      die;

        $this->assign( $array );
//		
//		echo '<pre>';
//      print_r( $array);
        $this->V( 'member/refund_detail' );
    }

    /**
     * 取退款的列表总数
     */
    public function get_refund_count()
    {
        $model = new service_member_Home_mobile();
        $model->setUid( $this->memberInfo->uid );
        $res = $model->getBuyerOrderRefundCountArray();
        $this->apiReturn( $res );
    }

}
