<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: order.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class orderAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 会员中心
     * 用户全部订单
     */
    public function index()
    {
        $status = Input::get( 'status', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $order_status = '';
        if ( !empty( $status ) ) {
            $order_status = 'order_status_buyer_' . $status;
        }

        $order_list_model = new service_order_List_mobile();
        $order_list_model->setOrder_status( $order_status );
        $order_list_model->setUid( $this->memberInfo->uid );
        $where = $order_list_model->getOrderListWhere();

        $order_list_model->setWhere( $where );
        $order_list_model->setPagesize( $pagesize );
        $res = $order_list_model->getBuyerOrderList();

        $array[ 'orderList' ] = $res;
        $array[ 'status' ] = $status;

//      $array[ 'orderList_json' ] = json_encode($res,true);

        $this->assign( $array );
//        echo '<pre>';
//        print_r( $array );
//        echo '</pre>';
//
//        $this->apiReturn( $array );
//        exit;
        $this->V( 'member/order_list' );
    }

    /**
     * 会员中心
     * 用户全部订单
     */
    public function get_list()
    {
        $status = Input::get( 'status', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $order_status = '';
        if ( !empty( $status ) ) {
            $order_status = 'order_status_buyer_' . $status;
        }

        $order_list_model = new service_order_List_mobile();
        $order_list_model->setOrder_status( $order_status );
        $order_list_model->setUid( $this->memberInfo->uid );
        $where = $order_list_model->getOrderListWhere();

        $order_list_model->setWhere( $where );
        $order_list_model->setPagesize( $pagesize );
        $res = $order_list_model->getBuyerOrderList();

        $this->apiReturn( $res );
    }

    public function detail()
    {
        $order_sn = Input::get( 'sn', '' )->bigint();
        $order_id = Input::get( 'id', '' )->int();

        if ( empty( $order_id ) && empty( $order_sn ) ) {
            $this->redirect( '订单不能为空' );
        }
        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }
        $model = new service_order_BuyerHandle_mobile();
        $model->setOrder_sn( $order_sn );
        $model->setOrder_id( $order_id );
        $model->setUid( $this->memberInfo->uid );

        try {
            $model->checkViewPriview();
        } catch (TmacClassException $exc) {
            $this->redirect( $exc->getMessage() );
        }


        $model->setImage_size( '110' );
        $order_info = $model->getOrderInfoDetail();
//      $this->apiReturn( $order_info );
//      die;
//		echo '<pre>';
//		var_dump($order_info);
        $array[ 'order_info' ] = $order_info;
        $this->assign( $array );
//			echo '<pre>';
//		print_r($order_info);
        $this->V( 'member/order_detail' );
    }

    /**
     * 申请退款页面
     */
    public function refund()
    {
        $order_sn = Input::get( 'sn', '' )->required( '订单号不能为空' )->bigint();
        $order_goods_id = Input::get( 'order_goods_id', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }
        $error_message = '';
        if ( empty( $order_sn ) && empty( $order_goods_id ) ) {
            $error_message = '订单号,订单商品ID不能为空';
        }

        $model = new service_order_RefundSave_mobile();
        //检测权限                
        $model->setOrder_sn( $order_sn );
        $model->setOrder_goods_id( $order_goods_id );
        $model->setUid( $this->memberInfo->uid );
        $model->setMoney( 0.01 );
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            $error_message = $exc->getMessage();
        }

        //TODO 取订单商品，订单金额，订单编号，交易时间
        $order_refund_array = Tmac::config( 'order.order_refund', APP_BASE_NAME );
        $refund_service_status_option = Utility::OptionObject( $order_refund_array[ 'refund_service_status' ] );
        $refund_service_reason_option = Utility::OptionObject( $order_refund_array[ 'refund_service_reason' ] );

        $array[ 'refund_service_status_option' ] = $refund_service_status_option;
        $array[ 'refund_service_reason_option' ] = $refund_service_reason_option;
        $array[ 'order_sn' ] = $order_sn;
        $array[ 'order_goods_id' ] = $order_goods_id;
        $array[ 'error_message' ] = $error_message;

        $this->assign( $array );
//		 echo '<pre>';
//      print_r( $array );
        $this->V( 'member/order_refund' );
    }

    /**
     * 申请退款保存
     */
    public function refund_save()
    {
        $order_sn = Input::post( 'sn', '' )->required( '订单号不能为空' )->bigint();
        $order_goods_id = Input::post( 'order_goods_id', 0 )->int();
        $refund_service_status = Input::post( 'refund_service_status', 0 )->required( '申请售后处理方式不能为空' )->int();
        $refund_service_reason = Input::post( 'refund_service_reason', 0 )->required( '售后退款原因不能为空' )->int();
        $money = Input::post( 'money', 0 )->required( '申请退款金额不能为空' )->float();
        $refund_note = Input::post( 'refund_note', '' )->string();
        $refund_images = json_decode( stripslashes( Input::post( 'refund_images', '' )->sql() ), true );
        //判断必填
        if ( !empty( $refund_images ) ) {
            foreach ( $refund_images as $image_id ) {
                Input::set( $image_id, '' )->required( '图片格式不正确' )->imageId();
            }
        }

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        if ( empty( $order_sn ) && empty( $order_goods_id ) ) {
            throw new ApiException( '订单号,订单商品ID不能为空' );
        }
        if ( $refund_service_status <> 1 && $refund_service_status <> 2 ) {
            throw new ApiException( '状态不对' );
        }
        if ( empty( $order_goods_id ) && $refund_service_status == 1 ) {
            throw new ApiException( '还没确认收到货呢，不能申请退款退货' );
        }
        $model = new service_order_RefundSave_mobile();
        //检测权限                
        $model->setOrder_sn( $order_sn );
        $model->setOrder_goods_id( $order_goods_id );
        $model->setUid( $this->memberInfo->uid );
        $model->setMoney( $money );
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $entity_OrderRefund_base = new entity_OrderRefund_base();
        $entity_OrderRefund_base->order_goods_id = $order_goods_id;
        $entity_OrderRefund_base->refund_service_status = $refund_service_status;
        $entity_OrderRefund_base->refund_service_reason = $refund_service_reason;
        $entity_OrderRefund_base->money = $money;
        $entity_OrderRefund_base->uid = $this->memberInfo->uid;
        $entity_OrderRefund_base->refund_time = $this->now;
        $entity_OrderRefund_base->refund_note = $refund_note;
        if ( $refund_images ) {
            $entity_OrderRefund_base->refund_images = json_encode( array_unique( $refund_images ) );
        }

        try {
            $res = $model->createOrderRefund( $entity_OrderRefund_base );
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }


        if ( $res ) {
            $this->apiReturn( $res );
        } else {
            throw new ApiException( '创建退款失败' );
        }
    }

    public function _temp_update_order_goods_detail()
    {
        $model = new service_order_List_mobile();
        $model->updateOrderGoodsDetail();
    }

    /**
     * 取消订单
     */
    public function cancel()
    {
        $order_sn = Input::post( 'sn', '' )->required( '订单号不能为空' )->bigint();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_BuyerHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_sn( $order_sn );
        try {
            $res = $model->cancelOrderInfo();
            if ( $res ) {
                $array = array(
                    'order_status_text' => $model->getOrder_status_text()
                );
                $this->apiReturn( $array );
            } else {
                throw new ApiException( '取消订单失败，请联系客服MM' );
            }
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }
    }

    /**
     * 确认收货
     */
    public function confirm()
    {
        $order_sn = Input::post( 'sn', '' )->required( '订单号不能为空' )->bigint();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_BuyerHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_sn( $order_sn );
        try {
            $res = $model->confirmOrderInfo();
            if ( $res ) {
                $array = array(
                    'order_status_text' => $model->getOrder_status_text()
                );
                $this->apiReturn( $array );
            } else {
                throw new ApiException( '订单确认收货失败，请联系客服MM' );
            }
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }
    }

    /**
     * 延长确认收货
     */
    public function extend_confirm()
    {
        $order_sn = Input::post( 'sn', '' )->required( '订单号不能为空' )->bigint();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_BuyerHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_sn( $order_sn );
        try {
            $res = $model->extendConfirmOrderInfo();
            if ( $res ) {
                $array = array(
                    'order_status_text' => $model->getOrder_status_text()
                );
                $this->apiReturn( $array );
            } else {
                throw new ApiException( '延长确认收货失败，请联系客服MM' );
            }
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }
    }

    /**
     * 评论页面
     */
    public function comment()
    {
        $order_sn = Input::get( 'sn', '' )->required( '订单号不能为空' )->bigint();
        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }
        //检测用户对order_info的权限
        $model = new service_order_BuyerHandle_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_sn( $order_sn );
        try {
            $orderInfo = $model->checkBuyerPriview();
            if ( $orderInfo == false ) {
                $this->redirect( '取消订单失败，请联系客服MM' );
            }
        } catch (TmacClassException $exc) {
            $this->redirect( $exc->getMessage() );
        }
        //取出没有评论的订单商品
        $comment_model = new service_order_Comment_mobile();
        $comment_model->setUid( $this->memberInfo->uid );
        $comment_model->setOrder_id( $orderInfo->order_id );
        $res = $comment_model->getOrderGoodsUnCommentArray();

        $array[ 'order_goods_array' ] = $res;
        $array[ 'shop_name' ] = $orderInfo->shop_name;
        $array[ 'order_sn' ] = $orderInfo->order_sn;
//      echo '<pre>';
//      print_r( $array );
//      die;
        $this->assign( $array );
        $this->V( 'member/order_comment' );
    }

    /**
     * 评论页面 保存
     */
    public function comment_save()
    {
//        $param = '{"1":{"rank":2,"content":"产品很棒，用了后胸变大了不少哟"},"2":{"rank":5,"content":"老板的产品给力，用了后时间变长了"}}';
//        $param_array = json_decode( $param );

        if ( empty( $_POST ) ) {
            throw new ApiException( 'don\'t be evil' );
        }
        $order_sn = Input::post( 'sn', '' )->required( '订单号不能为空' )->bigint();
        $param = Input::post( 'param', 0 )->required( '评价的参数不能为空' )->sql();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $param_array = $this->checkBatchSave( $param );
        //检测用户对order_info的权限
        $buyer_handle_model = new service_order_BuyerHandle_mobile();
        $buyer_handle_model->setUid( $this->memberInfo->uid );
        $buyer_handle_model->setOrder_sn( $order_sn );
        try {
            $orderInfo = $buyer_handle_model->checkBuyerPriview();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $model = new service_order_Comment_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setOrder_id( $orderInfo->order_id );
        $username = empty( $this->memberInfo->username ) ? $this->memberInfo->mobile : $this->memberInfo->username;
        $model->setUsername( $username );
        $model->setOrderInfo( $orderInfo );
        try {
            $res = $model->createGoodsComment( $param_array );
            if ( $res == false ) {
                throw new ApiException( '评论失败，请联系客服MM' );
            }
            $this->apiReturn( $res );
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }
    }

    /**
     * 检测格式
     * @param type $param_array
     */
    private function checkBatchSave( $param )
    {
        $param_array = json_decode( stripslashes( $param ) );
        if ( empty( $param_array ) ) {
            throw new ApiException( '评论数据不能为空' );
        }

        $result_array = array();
        foreach ( $param_array as $order_goods_id => $value ) {
            $order_goods_id = Input::set( $order_goods_id, 0 )->int();
            if ( empty( $value->rank ) && empty( $value->content ) ) {
                throw new ApiException( '评价内容不能为空' );
            }
            $value->rank = Input::set( $value->rank, 0 )->int();
            $value->content = Input::set( $value->content, '' )->string();
            $result_array[ $order_goods_id ] = $value;
        }

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        return $result_array;
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

}
