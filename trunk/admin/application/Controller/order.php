<?php

/**
 * 用户登录注册页面
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class orderAction extends service_Controller_admin
{

    private $check_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );

        $check_model = $this->check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer,tb_customer_manager' );
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
        //（待付款：waiting_payment｜待发货：wating_seller_delivery｜已发货：wating_receiving｜已完成：complete｜已关闭：close｜退款中：refund）
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query_string', '' )->string();
        $pagesize = Input::get( 'pagesize', 20 )->int();
        $image_size = Input::get( 'image_size', 110 )->imageSize();
        $uid = Input::get( 'uid', 0 )->int();
        $order_list_type = Input::get( 'order_list_type', 0 )->int();
        $demo_order = Input::get( 'demo_order', 0 )->int();        

        $order_status = '';
        if ( !empty( $status ) ) {
            $order_status = 'order_status_' . $status;
        }

        //用户UID
        $order_model = new service_order_List_admin();
        $order_model->setUid( $uid );
        $order_model->setQuery_string( $query_string );
        $order_model->setPagesize( $pagesize );
        $order_model->setImage_size( $image_size );
        $order_model->setOrder_status( $order_status );
        $order_model->setDemo_order( $demo_order );
        $order_model->setOrder_list_type( $order_list_type );        

        $rs = $order_model->getOrderList();

        //加载订单状态的数组
        $order_status_array = Tmac::config( 'order.system.order_status', APP_BASE_NAME );
        $order_status_option = Utility::Option( $order_status_array, $status );

        //加载订单状态的数组
        $demo_order_array = Tmac::config( 'order.system.demo_order_show', APP_BASE_NAME );
        $demo_order_option = Utility::Option( $demo_order_array, $demo_order );

        //取友情操作类型radiobutton数组
        $article_do_ary = Tmac::config( 'article.do_order' );
        $article_do_ary_option = Utility::Option( $article_do_ary, '' );
//        var_dump( $order_status_option );
//        echo '<pre>';
//        print_r( $rs );
//        die;
        $array[ 'uid' ] = $uid;
        $array[ 'status' ] = $status;
        $array[ 'query_string' ] = $query_string;
        $array[ 'order_status_option' ] = $order_status_option;
        $array[ 'demo_order_option' ] = $demo_order_option;
        $array[ 'article_do_ary_option' ] = $article_do_ary_option;        
        $this->assign( $array );
        $this->assign( $rs );
        $this->V( 'order/list' );
    }

    /**
     * 取快递列表
     */
    public function get_express()
    {
        $order_model = new service_order_List_admin();
        $res = $order_model->getExpressArray();
        $this->apiReturn( $res );
    }

    /**
     * 发货操作
     */
    public function delivery()
    {
        $order_id = Input::post( 'order_id', 0 )->required( '订单号不能为空' )->int();
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
        $model->setOrder_id( $order_id );
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
        $model = new service_order_SellerHandle_admin();
        $model->setOrder_id( $order_id );

        $model->setImage_size( $image_size );
        $order_info = $model->getOrderInfoDetail();
        //var_dump( $order_info );
        //die;
        $array[ 'order_info' ] = $order_info;
        $this->assign( $array );
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
     * 查看退款详情
     */
    public function get_refund_detail()
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

        $this->apiReturn( $array );
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
     * 确认收货
     */
    public function confirm()
    {
        $order_sn = Input::post( 'sn', '' )->required( '订单号不能为空' )->bigint();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_BuyerHandle_mobile();
        $model->setOrder_sn( $order_sn );
        $model->setAdminPurview( true );
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
        $model->setOrder_sn( $order_sn );
        $model->setAdminPurview( true );
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
     * 取消订单
     */
    public function cancel()
    {
        $order_sn = Input::post( 'sn', '' )->required( '订单号不能为空' )->bigint();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $model = new service_order_BuyerHandle_mobile();
        $model->setOrder_sn( $order_sn );
        $model->setAdminPurview( true );
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
     * 申请退款页面
     */
    public function refund()
    {
        $order_sn = Input::get( 'sn', '' )->required( '订单号不能为空' )->bigint();
        $order_goods_id = Input::get( 'order_goods_id', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        if ( empty( $order_sn ) && empty( $order_goods_id ) ) {
            throw new ApiException( '订单号,订单商品ID不能为空' );
        }

        $model = new service_order_RefundSave_mobile();
        //检测权限                
        $model->setOrder_sn( $order_sn );
        $model->setOrder_goods_id( $order_goods_id );
        $model->setMoney( 0.01 );
        $model->setAdminPurview( true );
        try {
            $model->checkPurviewByBuyer();
        } catch (TmacClassException $exc) {
            $this->redirect( $exc->getMessage() );
        }

        //TODO 取订单商品，订单金额，订单编号，交易时间
        $order_refund_array = Tmac::config( 'order.order_refund', APP_BASE_NAME );
        $refund_service_status_option = Utility::OptionObject( $order_refund_array[ 'refund_service_status' ] );
        $refund_service_reason_option = Utility::OptionObject( $order_refund_array[ 'refund_service_reason' ] );

        $array[ 'refund_service_status_option' ] = $refund_service_status_option;
        $array[ 'refund_service_reason_option' ] = $refund_service_reason_option;
        $array[ 'order_sn' ] = $order_sn;
        $array[ 'order_goods_id' ] = $order_goods_id;

        $this->assign( $array );
//		 echo '<pre>';
//      print_r( $array );
        $this->V( 'order/refund' );
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
        $model->setMoney( $money );
        $model->setAdminPurview( true );

        $orderInfo = $model->getOrderInfoBySN( $order_sn );
        if ( $orderInfo->demo_order == service_Order_base::demo_order_no ) {
            throw new ApiException( '正常用户的订单不能取消，只能取消APP订单的' );
        }
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
        $entity_OrderRefund_base->uid = $orderInfo->uid;
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

    /**
     * 售后维权订单列表
     */
    public function refund_list()
    {
        //（待卖家处理：seller_confirm｜待买家处理：buyer_confirm｜银品惠客服介入：customer_confirm｜同意退款：complete｜撤销维权：close）
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query_string', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $image_size = Input::get( 'image_size', 110 )->imageSize();
        $uid = Input::get( 'uid', 0 )->int();

        $order_model = new service_order_RefundList_admin();
        $order_model->setUid( $uid );
        $order_model->setQuery_string( $query_string );
        $order_model->setPagesize( $pagesize );
        $order_model->setImage_size( $image_size );
        $order_model->setOrder_status( $status );

        $rs = $order_model->getSellerOrderRefundList();


        //加载订单状态的数组
        $order_refund_status_array = Tmac::config( 'order.system.order_refund_status', APP_BASE_NAME );
        $order_refund_status_option = Utility::Option( $order_refund_status_array, $status );

        $searchParameter[ 'uid' ] = $uid;
        $searchParameter[ 'status' ] = $status;
        $searchParameter[ 'pagesize' ] = $pagesize;
        $searchParameter[ 'query_string' ] = $query_string;
        $searchParameter[ 'order_refund_status_option' ] = $order_refund_status_option;


        $this->assign( $searchParameter );
        $this->assign( $rs );

//		echo '<pre>';
//		print_r($rs);
//        die;
        $this->V( 'order/refund_list' );
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
     * 批量操作
     */
    public function action_do()
    {
        $this->check_model->CheckPurview( 'tb_admin' );

        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'id', 0 )->int();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();


        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            $this->redirect( '请选择要操作的...' );
        }
        if ( empty( $do ) && empty( $act ) ) {
            $this->redirect( '请选择要操作的...' );
        }

        if ( $do == 'refund' || $act == 'refund' ) {
            $model = new service_order_Refund_admin();
            $rs = $model->batchOrderRefund( $id );
        }
        // TODO DEL该分类下的所有资讯
        if ( $rs ) {
            $this->redirect( '操作成功' . $model->getErrorMessage() );
            //$this->apiReturn( array( '删除课件成功' ) );
        } else {
            $this->redirect( '操作失败' . $model->getErrorMessage() );
            //throw new ApiException( '删除课件失败，请重试！' );
        }
    }

}
