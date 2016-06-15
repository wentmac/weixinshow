<?php

/**
 * 账单 
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class billAction extends service_Controller_mobile
{

    //定义初始化变量

    public function _init()
    {
        $this->checkLogin();
    }

    /**
     * 取账单统计
     */
    public function home()
    {
        //取出{累计收入｜账户余额}
        //{待确认｜提现中｜已提现｜自营收入｜代销收入｜直接收款}
        $model = new service_Shop_manage();
        $model->setUid( $this->memberInfo->uid );
        $shopInfo = $model->getShopMoney();


        $bill_model = new service_bill_List_manage();
        $bill_model->setUid( $this->memberInfo->uid );


        $array = array();
        //累计收入
        $array[ 'history_money' ] = $shopInfo->history_money;
        //账户余额
        $array[ 'current_money' ] = $shopInfo->current_money;
        //待确认
        $bill_model->setStatus( 'waiting_confirm' );
        $bill_model->getBillWhere();
        $array[ 'waiting_confirm' ] = $bill_model->getBillSum();
        //提现中
        $bill_model->setStatus( 'expense_withdrawals_ing' );
        $bill_model->getBillWhere();
        $array[ 'expense_withdrawals_ing' ] = $bill_model->getBillSum();
        //已提现
        $bill_model->setStatus( 'expense_withdrawals_success' );
        $bill_model->getBillWhere();
        $array[ 'expense_withdrawals_success' ] = $bill_model->getBillSum();
        //所有收入
        $bill_model->setStatus( 'in' );
        $bill_model->getBillWhere();


        $member_model = new service_Member_base();
        $member_setting_info = $member_model->getMemberSettingInfoByUid( $this->memberInfo->uid );
        if ( empty( $member_setting_info->openid ) ) {
            $this->authorize();
        }

        $array[ 'in' ] = $bill_model->getBillSum();
        $array[ 'member_setting_info' ] = $member_setting_info;
        $this->assign( $array );

        //echo '<pre>';
        //print_r( $array );die;
        $this->V( 'member/bill_home' );
    }

    /**
     * 取全部账单类型
     */
    public function index()
    {
        $status = Input::get( 'status', 'all' )->string();
        $array[ 'status' ] = $status;
        $this->assign( $array );

        $this->V( 'member/bill_list' );
    }

    /**
     * 取全部账单列表
     */
    public function get_bill_list()
    {
        $status = Input::get( 'status', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $order_model = new service_bill_List_manage();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setPagesize( $pagesize );
        $order_model->setStatus( $status );

        $order_model->getBillWhere();
        $rs = $order_model->getBillList();
        $this->apiReturn( $rs );
    }

    public function authorize()
    {
        $weixin_model = new service_oauth_WeixinTransfers_base();
        $weixin_model->setRedirect_uri( MOBILE_URL . 'oauth/weixin_transfers' );
        parent::headerRedirect( $weixin_model->getAuthorizeUrl() );
    }

    public function update_avatar()
    {
        $register_model = new service_account_Register_mobile();
        $res = $register_model->updateMemberOauthAvatar( $this->memberInfo->uid );
        if ( $res ) {
            $this->apiReturn($res);
        } else {
            throw new ApiException( $register_model->getErrorMessage() );
        }
    }

}
