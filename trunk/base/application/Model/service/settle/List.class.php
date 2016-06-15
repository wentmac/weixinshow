<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_settle_List_base extends service_Model_base
{

    /**
     * 提现状态
     * 申请-等待审核
     */
    const settle_status_untreated = 0;

    /**
     * 提现状态
     * 同意
     */
    const settle_status_success = 1;

    /**
     * 提现状态
     * 不同意
     */
    const settle_status_fail = 2;

    /**
     * 提现状态
     * 审核成功-等待打款
     */
    const settle_status_verify = 3;

    protected $errorMessage;
    protected $pagesize;
    protected $uid;
    protected $status;

    function getErrorMessage()
    {
        return $this->errorMessage;
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
     * 取买家的订单列表
     * $this->where;
     * $this->getBuyerOrderList();
     */
    public function getSellerSettleList()
    {
        $dao = dao_factory_base::getSettleDao();

        $where = 'uid=' . $this->uid;
        switch ( $this->status )
        {
            case 'verify':

                $where .= ' AND settle_status=' . service_settle_List_base::settle_status_verify;
                break;
            case 'success':

                $where .= ' AND settle_status=' . service_settle_List_base::settle_status_success;
                break;
            case 'fail':

                $where .= ' AND settle_status=' . service_settle_List_base::settle_status_fail;
                break;

            default:
            case 'all':
                break;
        }
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

        $res = array();
        if ( $count > 0 ) {
            $dao->setLimit( $limit );
            $dao->setOrderby( 'settle_id DESC' );
            $res = $dao->getListByWhere();

            $bank_id_array = Tmac::config( 'member.member_setting.bank_id', APP_MANAGE_NAME );
            $settle_status_array = Tmac::config( 'bill.bill.settle_status', APP_BASE_NAME );
            $account_type_array = Tmac::config( 'bill.bill.account_type', APP_BASE_NAME );
            foreach ( $res as $value ) {
                if ( !empty( $value->bank_id ) ) {
                    $value->bank_name = $bank_id_array[ $value->bank_id ];
                } else {
                    $value->bank_name = '';
                }
                $value->settle_apply_time = date( 'Y-m-d H:i:s', $value->settle_apply_time );
                $value->settle_execute_time = empty( $value->settle_execute_time ) ? '' : date( 'Y-m-d H:i:s', $value->settle_execute_time );
                $value->settle_status = $settle_status_array[ $value->settle_status ];
                //$value->account_type_text = $account_type_array[ $value->account_type ];
                unset( $value->admin_uid );
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
            'retcode' => 'settle_list',
            'retmsg' => $retmsg,
            'reqdata' => $res,
        );
        return $return;
    }

}
