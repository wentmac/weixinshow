<?php

/**
 * 后台 提现 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: settle.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class settleAction extends service_Controller_admin
{

    private $check_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
        $this->check_model = $this->M( 'Check' );
        $this->check_model->checkLogin();
        $this->check_model->CheckPurview( 'tb_admin,tb_finance' );
    }

    /**
     * 管理商品管理主页
     */
    public function index()
    {
        $status = Input::get( 'status', '' )->string();
        $query_string = Input::get( 'query_string', '' )->string();
        $do = Input::get( 'do', '' )->string();

        /**
         * $this->uid;
         * $this->is_supplier;
         * $this->goods_source;
         * $this->goods_cat_id;
         * $this->query_string;
         * $this->sort;
         * $this->getGoodsList();
         */
        $model = new service_settle_List_admin();
        $model->setStatus( $status );
        $model->setQuery_string( $query_string );

        $rs = $model->getSettleList();

        $settle_status_show_array = Tmac::config( 'bill.bill.settle_status_text', APP_BASE_NAME );
        $settle_status_option = Utility::Option( $settle_status_show_array, $status );

        //取友情操作类型radiobutton数组
        $article_do_ary = Tmac::config( 'article.do_index' );
        $article_do_ary_option = Utility::Option( $article_do_ary, $do );


        $array[ 'settle_status_option' ] = $settle_status_option;
        $array[ 'article_do_ary_option' ] = $article_do_ary_option;
        $array[ 'query_string' ] = $query_string;

        $this->assign( $array );
//        echo '<pre>';
//        print_r( $array );
//        print_r( $rs );
//        die;
        $this->assign( $rs );
        $this->V( 'settle/index' );
    }

    public function member_bill_list()
    {
        $uid = Input::get( 'uid', 0 )->int();
        $status = Input::get( 'status', '' )->string();
        $pagesize = Input::get( 'pagesize', 20 )->int();
        $do = Input::get( 'do', '' )->string();

        $order_model = new service_bill_List_admin();
        $order_model->setUid( $uid );
        $order_model->setPagesize( $pagesize );
        $order_model->setStatus( $status );

        $order_model->getBillWhere();
        $rs = $order_model->getBillList();

        $member_model = new service_Member_base();
        $member_info = $member_model->getMemberInfoByUid( $uid );
        $member_setting_info = $member_model->getMemberSettingInfoByUid( $uid );



        $bill_type_array = Tmac::config( 'bill.bill.bill_type', APP_BASE_NAME );
        $bill_type_option = Utility::Option( $bill_type_array, $status );
        //取友情操作类型radiobutton数组
        $article_do_ary = Tmac::config( 'article.do_index' );
        $article_do_ary_option = Utility::Option( $article_do_ary, $do );

        $array[ 'bill_type_option' ] = $bill_type_option;
        $array[ 'article_do_ary_option' ] = $article_do_ary_option;
        $array[ 'member_info' ] = $member_info;
        $array[ 'member_setting_info' ] = $member_setting_info;
        $array[ 'uid' ] = $uid;
        $this->assign( $array );
//        echo '<pre>';
//        print_r( $array );
//        print_r( $rs );
//        die;
        $this->assign( $rs );
        $this->V( 'settle/member_bill_list' );
    }

    /**
     * 新增/修改栏目页面
     */
    public function detail()
    {
        $settle_id = Input::get( 'id', 0 )->required( 'ID不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }


        $model = new service_settle_Save_admin();
        $model->setSettle_id( $settle_id );
        $entity_Settle_base = $model->getSettleInfo();
        if ( empty( $entity_Settle_base ) ) {
            $this->redirect( '不存在' );
        }

        $model->handleSettleInfo( $entity_Settle_base );

        $error = '';
        //判断是否
        try {
            $model->checkSettleSaveBottonPurview( $entity_Settle_base );
        } catch (TmacClassException $exc) {
            $error = $exc->getMessage();
        }



        $array[ 'settle_id' ] = $settle_id;

        $array[ 'editinfo' ] = $entity_Settle_base;
        $array[ 'error' ] = $error;
        $array[ 'current_money' ] = $model->getCurrent_money();


//        echo '<pre>';
//        print_r( $array );
//        echo '<pre>';
//        die;
        $this->assign( $array );
        $this->V( 'settle/detail' );
    }

    /**
     * 新增/修改栏目页面　保存　
     */
    public function save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 3 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }

        $settle_id = Input::post( 'settle_id', 0 )->required( '请选择ID！' )->int();
        $settle_status = Input::post( 'settle_status', 0 )->required( '请选择操作！' )->int();
        $settle_note = Input::post( 'settle_note', '' )->string();
        $settle_bank_id = Input::post( 'settle_bank_id', 0 )->string();
        $settle_bank_cardnum = Input::post( 'settle_bank_cardnum', '' )->string();
        $settle_bank_account = Input::post( 'settle_bank_account', '' )->string();
        $settle_image_id = Input::post( 'settle_image_id', '' )->imageId();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        /**
        if ( $settle_status == service_settle_List_base::settle_status_success && (empty( $settle_bank_cardnum ) || empty( $settle_bank_account )) ) {
            $this->redirect( '打款的账号不能为空' );
        }*/

        $model = new service_settle_Save_admin();
        $model->setSettle_id( $settle_id );
        $entity_Settle = $model->getSettleInfo();
        if ( empty( $entity_Settle ) ) {
            $this->redirect( '不存在' );
        }
        //判断是否
        try {
            $model->checkSettleSaveBottonPurview( $entity_Settle );
        } catch (TmacClassException $exc) {
            $error = $exc->getMessage();
            $this->redirect( $error );
        }


        $entity_Settle_base = new entity_Settle_base();
        $entity_Settle_base->settle_id = $settle_id;
        $entity_Settle_base->settle_status = $settle_status;
        $entity_Settle_base->settle_note = $settle_note;
        $entity_Settle_base->settle_bank_id = $settle_bank_id;
        $entity_Settle_base->settle_bank_cardnum = $settle_bank_cardnum;
        $entity_Settle_base->settle_bank_account = $settle_bank_account;
        $entity_Settle_base->admin_username = $_SESSION[ 'admin' ];
        $entity_Settle_base->member_bill_id = $entity_Settle->member_bill_id;
        $entity_Settle_base->uid = $entity_Settle->uid;
        $entity_Settle_base->money = $entity_Settle->money;
        $entity_Settle_base->settle_image_id = $settle_image_id;
        $entity_Settle_base->settle_execute_time = $this->now;        

        if ( $settle_status == service_settle_List_base::settle_status_success || $settle_status == service_settle_List_base::settle_status_fail ) {
            $res = $model->settleSave( $entity_Settle_base );
        } else if ( $settle_status == service_settle_List_base::settle_status_verify ) {
            $res = $model->settleVerify( $settle_id );
        }
        if ( $res ) {
            $this->redirect( '提现操作成功' );
        } else {
            $this->redirect( $model->getErrorMessage() );
        }
    }

}
