<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_bill_List_base extends service_Model_base
{

    protected $where;
    protected $image_size;
    protected $pagesize;
    protected $uid;
    protected $status;

    function setWhere( $where )
    {
        $this->where = $where;
    }

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setStatus( $status )
    {
        $this->status = $status;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取账单列表的where语句
     * $this->order_status;
     * $this->uid;
     * $this->getOrderListWhere();
     */
    public function getBillWhere()
    {
        $where = "uid={$this->uid}";
        switch ( $this->status )
        {
            /**
             * 待确认
             * 待确认收货的：AND bill_type=" . service_Member_base::bill_type_income . ' AND order_complete=' . service_Member_base::order_complete_no;
             * +
             * 待结算的收入
             * waiting_settle
             * use index uid_finish
             */
            case 'waiting_confirm':
                $where .= ' AND bill_type=' . service_Member_base::bill_type_income
                        . ' AND order_finish=' . service_Member_base::order_finish_no;
                break;
            /**
             * 待结算收入
             * use index uid_finish
             */
            case 'waiting_settle':
                $where .= ' AND bill_type=' . service_Member_base::bill_type_income
                        . ' AND order_finish=' . service_Member_base::order_finish_no
                        . ' AND order_complete=' . service_Member_base::order_complete_yes;
                break;
            /**
             * 已结算收入          
             * use index uid_finish
             */
            case 'already_settle':
                $where .= ' AND bill_type=' . service_Member_base::bill_type_income
                        . ' AND order_finish=' . service_Member_base::order_finish_yes
                        . ' AND order_complete=' . service_Member_base::order_complete_yes;
                break;
            /**
             * 提现中
             * use index uid_execute
             */
            case 'expense_withdrawals_ing':
                $where .= ' AND bill_type=' . service_Member_base::bill_type_expend
                        . ' AND bill_expend_type=' . service_Member_base::bill_expend_type_withdrawals
                        . ' AND is_execute=0';
                break;
            /**
             * 已提现
             * use index uid_execute
             */
            case 'expense_withdrawals_success':
                $where .= ' AND bill_type=' . service_Member_base::bill_type_expend
                        . ' AND bill_expend_type=' . service_Member_base::bill_expend_type_withdrawals
                        . ' AND is_execute=1';
                break;

            /**
             * 自营收入
             * use index uid
             */
            case 'income_business':
                $where .= ' AND bill_type_class=' . service_Member_base::bill_type_class_business
                        . ' AND order_finish=' . service_Member_base::order_finish_yes;
                break;

            /**
             * 代销收入
             * use index uid
             */
            case 'income_wholesale':
                $where .= ' AND bill_type_class=' . service_Member_base::bill_type_class_wholesale
                        . ' AND order_finish=' . service_Member_base::order_finish_yes;
                break;

            /**
             * 直接收款
             * use index uid
             */
            case 'income_receivable':
                $where .= ' AND bill_type_class=' . service_Member_base::bill_type_class_receivable
                        . ' AND order_finish=' . service_Member_base::order_finish_yes;
                break;

            /**
             * 全部账单
             */
            case 'all':
            default :
                $where .= "";
                break;

            /**
             * 进账单
             * use index uid_execute
             */
            case 'in':
                $where .= " AND bill_type=" . service_Member_base::bill_type_income;
                break;

            /**
             * 出账单
             * use index uid_execute
             */
            case 'out':
                $where .= " AND bill_type=" . service_Member_base::bill_type_expend;
                break;
        }
        $this->where = $where;
        return $where;
    }

    /**
     * $this->where;
     * $this->getBillSum();
     * 取总数
     */
    public function getBillSum()
    {
        $dao = dao_factory_base::getMemberBillDao();
        $dao->setWhere( $this->where );
        $dao->setField( 'SUM(money) AS m' );
        $res = $dao->getInfoByWhere();
        if ( $res->m ) {
            return $res->m;
        }
        return 0;
    }

    /**
     * 取账单的当前状态
     * @param entity_MemberBill_base $member_bill
     */
    protected function getBillStatus( $member_bill )
    {
        $member_bill instanceof entity_MemberBill_base;
        $member_bill_status = '';
        if ( $member_bill->bill_type == service_Member_base::bill_type_income ) {//收入
            if ( $member_bill->order_complete == service_Member_base::order_complete_no ) {//未完成
                $member_bill_status = '等待确认收货';
            } else {
                if ( $member_bill->order_finish == service_Member_base::order_finish_no ) {
                    $member_bill_status = '未结算收入';
                } else {
                    $member_bill_status = '交易成功';
                }
            }
        } else if ( $member_bill->bill_type == service_Member_base::bill_type_expend ) {//支出
            $member_bill_status = '退款成功';
            if ( $member_bill->bill_expend_type == service_Member_base::bill_expend_type_withdrawals ) {//提现
                if ( $member_bill->is_execute == service_Member_base::is_execute_default ) {
                    $member_bill_status = '提现进行中';
                } else if ( $member_bill->is_execute == service_Member_base::is_execute_fail ) {
                    $member_bill_status = '提现被拒';
                } else if ( $member_bill->is_execute == service_Member_base::is_execute_success ) {
                    $member_bill_status = '提现成功';
                }
            }
        }
        return $member_bill_status;
    }

    /**
     * 取买家的订单列表
     * $this->where;
     * $this->getBuyerOrderList();
     */
    public function getBillList()
    {
        $dao = dao_factory_base::getMemberBillDao();

        $dao->setWhere( $this->where );
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
                $value->bill_status = self::getBillStatus( $value );
                $bill_type_text = $bill_type_class_array[ $value->bill_type_class ] . $bill_expend_type_array[ $value->bill_expend_type ];
                $value->bill_note = $bill_type_text . $value->bill_note;
                //$value->order_status_text = $order_config_array[ $value->order_status ];
                unset( $value->bill_type, $value->bill_type_class, $value->bill_expend_type, $value->is_execute, $value->order_complete );
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
            'retcode' => 'bill_list',
            'retmsg' => $retmsg,
            'reqdata' => $order_info_array,
        );
        return $return;
    }

}
