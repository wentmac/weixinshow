<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: List.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_settle_List_admin extends service_settle_List_base
{

    protected $url;    
    private $query_string;

    function setQuery_string( $query_string )
    {
        $this->query_string = $query_string;
    }

    function setUrl( $url )
    {
        $this->url = $url;
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
    public function getSettleList()
    {
        if ( empty( $this->url ) ) {
            $url = PHP_SELF . '?m=settle.index';
        } else {
            $url = $this->url;
        }


        $dao = dao_factory_base::getSettleDao();
        $settle_status_show_array = Tmac::config( 'bill.bill.settle_status_show', APP_BASE_NAME );        
        $where = '1=1';
        if ( !empty( $this->status ) ) {            
            $where .= ' AND settle_status=' . $settle_status_show_array[ $this->status ];                        
            $url .= '&status=' . $this->status;
        }
        //解析$query_string
        if ( !empty( $this->query_string ) ) {
            if ( preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $this->query_string ) ) {
                $where .= " AND mobile='{$this->query_string}'";
            } elseif ( preg_match( '/^[0-9]{1,8}$/', $this->query_string ) ) {
                $where .= " AND uid='{$this->query_string}'";
            } else {//订单号                
                $where .= " AND realname LIKE '%{$this->query_string}%'";
            }            
            $url .= 'query_string=' . $this->query_string;
        }
        $url .= '&page=';
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $url );
        $pages->setPrepage( 10 );
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
                if ( !empty( $value->bank_id ) && isset($bank_id_array[ $value->bank_id ])) {
                    $value->bank_name = $bank_id_array[ $value->bank_id ];
                } else {
                    $value->bank_name = '';
                }
                $value->settle_apply_time = date( 'Y-m-d H:i:s', $value->settle_apply_time );
                $value->settle_execute_time = empty( $value->settle_execute_time ) ? '' : date( 'Y-m-d H:i:s', $value->settle_execute_time );
                $value->settle_status = $settle_status_array[ $value->settle_status ];
                //$value->account_type_text = $account_type_array[ $value->account_type ];
            }
        }
        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无商品!";
        }

        $result = array(
            'rs' => $res,
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg
        );
        return $result;
    }

}
