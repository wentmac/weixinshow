<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_List_manage extends service_order_List_base
{

    private $export_start_page;
    private $export_end_page;
    private $address_id;
    private $start_date;
    private $end_date;
    private $query_id_type;
    private $query_id;
    private $order_info_dao;

    function setExport_start_page( $export_start_page )
    {
        $this->export_start_page = $export_start_page;
    }

    function setExport_end_page( $export_end_page )
    {
        $this->export_end_page = $export_end_page;
    }

    function setAddress_id( $address_id )
    {
        $this->address_id = $address_id;
    }

    function setStart_date( $start_date )
    {
        $this->start_date = strtotime( $start_date );
    }

    function setEnd_date( $end_date )
    {
        $this->end_date = strtotime( $end_date );
    }

    function setQuery_id_type( $query_id_type )
    {
        $this->query_id_type = $query_id_type;
    }

    function setQuery_id( $query_id )
    {
        $this->query_id = $query_id;
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
        if ( empty( $this->member_type ) || $this->member_type == service_Member_base::member_type_seller || $this->member_type == service_Member_base::member_type_mall ) {
            $where = "item_uid={$this->uid}";
        } else if ( $this->member_type == service_Member_base::member_type_supplier ) {
            $where = "goods_uid={$this->uid}";
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
        if ( !empty( $this->start_date ) && !empty( $this->end_date ) ) {
            $array = array( $this->start_date, $this->end_date );
            $this->start_date = min( $array );
            $this->end_date = max( $array );
        }
        if ( !empty( $this->start_date ) ) {
            $where .= ' AND create_time>=' . $this->start_date;
        }
        if ( !empty( $this->end_date ) ) {
            $where .= ' AND create_time<=' . $this->end_date;
        }
        if ( !empty( $this->query_id ) ) {
            switch ( $this->query_id_type )
            {
                case 'goods':
                default :
                    $where .= $this->order_info_dao->getOrderListWhereByOrderId( $this->query_id );
                    break;

                case 'member':
                    $where .= " AND uid={$this->query_id}";
                    break;
            }
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
        if ( !empty( $this->address_id ) ) {
            $where .= " AND address_id={$this->address_id}";
        }
        $where .= " AND is_delete=0";
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

      $rs = $order_model->getSellerOrderList();
     */
    public function getSellerOrderList()
    {
        $order_info_dao = $this->order_info_dao = dao_factory_base::getOrderInfoDao();

        $where = $this->getOrderWhere();

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
            $order_info_dao->setField( 'order_id,order_sn,order_status,refund_status,order_amount,consignee,commission_fee,shipping_fee,item_uid,shop_name,order_goods_detail,refund_status,have_return_service,order_refund_id,supplier_mobile,goods_uid,create_time,demo_order,coupon_code,coupon_money,uid,address_id,full_address,mobile' );
            $order_info_dao->setOrderby( 'order_id DESC' );
            $res = $order_info_dao->getListByWhere();

            $order_config_array = Tmac::config( 'order.seller.order_status', APP_BASE_NAME );                        
            foreach ( $res as $value ) {
                $value->order_goods_array = @unserialize( $value->order_goods_detail );
                foreach ( $value->order_goods_array as $order_goods ) {
                    $order_goods->goods_image_url = $this->getImage( $order_goods->goods_image_id, '110', 'goods' );
                    unset( $order_goods->goods_image_id );
                }

                $value->order_status_text = $order_config_array[ $value->order_status ];
                $value->order_item_count = count( $value->order_goods_array );
                $value->supplier_status = ($this->uid == $value->goods_uid) ? true : false;
                $value->create_time = date( 'Y-m-d H:i:s', $value->create_time );
                $order_refund_info = $this->getOrderRefundInfo( $value );
                $value->money = $order_refund_info->money;
                $value->refund_service_status_text = $order_refund_info->refund_service_status_text;
                //parent::handleFreeSupplierOrderShow( $value );
                unset( $value->order_goods_detail );
                unset( $value->goods_uid );
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
            'retcode' => 'seller_order_list',
            'retmsg' => $retmsg,
            'reqdata' => $order_info_array,
        );
        return $return;
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

    /**
     *
      取卖家的订单列表
      $order_model->setUid( $this->memberInfo->uid );
      $order_model->setQuery_string( $query_string );
      $order_model->setOrder_status( $order_status );
      $order_model->setMember_type( $this->memberInfo->member_type );
      $order_model->setMemberInfo( $this->memberInfo );
      $order_model->setExport_start_page( $export_start_page );
      $order_model->setExport_end_page( $export_end_page );atus);

      $rs = $order_model->exportSellerOrderList();
     */
    public function exportSellerOrderList()
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();

        $where = $this->getOrderWhere();

        $order_info_dao->setWhere( $where );
        $count = $order_info_dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }

        $pagesize = 10;
        $limit = ($this->export_start_page - 1) * $pagesize . ',' . $pagesize * $this->export_end_page;

        $order_info_array = array();
        if ( $count > 0 ) {
            $order_info_dao->setLimit( $limit );
            //$order_info_dao->setField( 'order_id,order_sn,order_status,refund_status,order_amount,consignee,commission_fee,shipping_fee,item_uid,shop_name,order_goods_detail,refund_status,have_return_service,order_refund_id,supplier_mobile,goods_uid,create_time,demo_order,coupon_code,coupon_money,uid' );
            $order_info_dao->setOrderby( 'order_id DESC' );
            $res = $order_info_dao->getListByWhere();

            $order_config_array = Tmac::config( 'order.seller.order_status', APP_BASE_NAME );
            foreach ( $res as $value ) {
                $value->order_goods_array = @unserialize( $value->order_goods_detail );
                foreach ( $value->order_goods_array as $order_goods ) {
                    $order_goods->goods_image_url = $this->getImage( $order_goods->goods_image_id, '110', 'goods' );
                    unset( $order_goods->goods_image_id );
                }

                $value->order_status_text = $order_config_array[ $value->order_status ];
                $value->order_item_count = count( $value->order_goods_array );
                $value->supplier_status = ($this->uid == $value->goods_uid) ? true : false;
                $value->create_time = date( 'Y-m-d H:i:s', $value->create_time );
                $order_refund_info = $this->getOrderRefundInfo( $value );
                $value->money = $order_refund_info->money;
                $value->refund_service_status_text = $order_refund_info->refund_service_status_text;
                unset( $value->order_goods_detail );
                unset( $value->goods_uid );
                $order_info_array[] = $value;
            }
            //echo '<Pre>';
            //print_r( $order_info_array );

            include Tmac::findFile( 'PHPExcel', APP_ADMIN_NAME );
            $objPHPExcel = new PHPExcel();

            $title = "订单状态{$this->order_status}|关键字:{$this->query_string}|{$this->export_start_page}页开始导出{$this->export_end_page}页" . date( 'Y-m-d H:i:s' );
            // Set document properties        
            $objPHPExcel->getProperties()->setCreator( "Maarten Balliauw" )
                    ->setLastModifiedBy( "zhangwentao" )
                    ->setTitle( $title );
            //设置当前的sheet   
            $objPHPExcel->setActiveSheetIndex( 0 );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 4, 1, $title );
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth( 10 );
            $xls_header = array( '产品', '会员UID', '收货人', '电话', '地址', '备注', '订单编号', '数量', '邮费', '总价', '状态', '下单时间' );
            for ( $i = 0; $i < count( $xls_header ); $i++ ) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( $i, 2, $xls_header[ $i ] );
            }
            $row = 3;
            foreach ( $res as $value ) {
                $order_goods_info = '';
                foreach ( $value->order_goods_array as $order_goods ) {
                    $outer_code = isset( $order_goods->outer_code ) ? $order_goods->outer_code : '';
                    $order_goods_info .= "{$order_goods->item_name}({$order_goods->goods_id})";
                    $order_goods_info .="\n";
                    $order_goods_info .="商品编码：{$outer_code}";
                    $order_goods_info .="\n";
                    $order_goods_info .="规格：{$order_goods->goods_sku_name}";
                    $order_goods_info .="\n";
                    $order_goods_info .="单价：￥{$order_goods->item_price}";
                    $order_goods_info .="\n";
                    $order_goods_info .="数量：x{$order_goods->item_number}";
                    $order_goods_info .="\n一一一一一一一一一一一一一一一\n";
                }
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 0, $row, $order_goods_info );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 1, $row, $value->uid );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 2, $row, $value->consignee );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 3, $row, $value->mobile );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 4, $row, $value->full_address );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 5, $row, $value->order_note );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 6, $row, ' ' . $value->order_sn );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 7, $row, $value->order_item_count );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 8, $row, $value->shipping_fee );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 9, $row, $value->order_amount );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 10, $row, $value->order_status_text );
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 11, $row, $value->create_time );
                $row++;
            }
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 10, $row, '总数：' );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 11, $row, $count );

            $objPHPExcel->getActiveSheet()->getStyle( 'A3:A' . $count )->getAlignment()->setWrapText( TRUE );
            $objPHPExcel->getActiveSheet()->getStyle( 'E' . $count )->getAlignment()->setWrapText( TRUE );
            //E 列为文本
            $objPHPExcel->getActiveSheet()->getStyle( 'G' )->getNumberFormat()
                    ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
            // Redirect output to a client’s web browser (Excel2007)
            header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
            header( 'Content-Disposition: attachment;filename=' . $title . '.xlsx' );
            header( 'Cache-Control: max-age=0' );
// If you're serving to IE 9, then the following may be needed
            header( 'Cache-Control: max-age=1' );

// If you're serving to IE over SSL, then the following may be needed
            header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
            header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
            header( 'Pragma: public' ); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );
            $objWriter->save( 'php://output' );
        }
    }

}
