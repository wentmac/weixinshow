<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_bill_List_admin extends service_bill_List_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取买家的订单列表
     * $this->where;
     * $this->getBuyerOrderList();
     */
    public function getBillList()
    {

        if ( empty( $this->url ) ) {
            $url = PHP_SELF . '?m=settle.member_bill_list';
        } else {
            $url = $this->url;
        }

        $url .= '&uid=' . $this->uid . '&status=' . $this->status;
        $url .= '&page=';
        $dao = dao_factory_base::getMemberBillDao();

        $dao->setWhere( $this->where );
        $count = $dao->getCountByWhere();

        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $url );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $order_info_array = array();
        if ( $count > 0 ) {
            $dao->setLimit( $limit );
            $dao->setField( 'member_bill_id,order_id,money,bill_type,bill_type_class,bill_expend_type,bill_note,bill_time,is_execute,order_complete,order_finish,bill_uid,bill_realname,bill_image_id' );
            $dao->setOrderby( 'member_bill_id DESC' );
            $res = $dao->getListByWhere();
            $bill_type_class_array = Tmac::config( 'bill.bill.bill_type_class', APP_BASE_NAME );
            $bill_expend_type_array = Tmac::config( 'bill.bill.bill_expend_type', APP_BASE_NAME );
            foreach ( $res as $value ) {
                if ( !empty( $value->bill_image_id ) ) {
                    $value->bill_image_id = $this->getImage( $value->bill_image_id, '80', 'goods' );
                }
                if ( $value->bill_type == service_Member_base::bill_type_expend && $value->bill_expend_type == service_Member_base::bill_expend_type_withdrawals ) {
                    $value->bill_image_id = STATIC_URL . 'common/icon_tixian.png';
                }
                $value->bill_time = date( 'Y-m-d H:i:s', $value->bill_time );
                $value->bill_status = parent::getBillStatus( $value );
                $bill_type_text = $bill_type_class_array[ $value->bill_type_class ] . $bill_expend_type_array[ $value->bill_expend_type ];
                $value->bill_note = $bill_type_text . $value->bill_note;
                //$value->order_status_text = $order_config_array[ $value->order_status ];
                unset( $value->bill_type, $value->bill_type_class, $value->bill_expend_type, $value->is_execute, $value->order_complete );
                $order_info_array[] = $value;
            }
        }
        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无账单!";
        }

        $result = array(
            'rs' => $order_info_array,
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg
        );
        return $result;
    }

}
