<?php

/**
 * 提现API
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class settleAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    public function index()
    {

        $status = Input::get( 'status', 'all' )->string();
        $array[ 'status' ] = $status;
        $this->assign( $array );

//		echo '<pre>';
//      print_r( $array_type );
        $this->V( 'member/settle' );
    }

    /**
     * 申请新的提现
     */
    public function create()
    {
        $money = Input::post( 'money', 0 )->required( '请输入要提现的金额' )->float();
        //$account_type = Input::post( 'account_type', 0 )->required( '请选择提现账户类型' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        /**
          $account_type_array = array( service_settle_Save_base::default_account_type_bank, service_settle_Save_base::default_account_type_alipay );
          if ( !in_array( $account_type, $account_type_array ) ) {
          throw new ApiException( '提现账户类型不正确' );
          }
         */
        //判断最小的提现额度
        //判断是否足够提现的金额
        //创建
        /**
         * $this->uid;
         * $this->money;
         * $this->account_type;
         * $this->createSettle();
         */
        $model = new service_settle_Save_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setMoney( $money );
        $model->setMemberInfo( $this->memberInfo );
        try {
            $model->createSettle();
            $this->apiReturn();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }
    }

    /**
     * 查看提现记录列表
     */
    public function get_list()
    {
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $status = Input::get( 'status', 'all' )->string();

        $model = new service_settle_List_mobile();
        $model->setPagesize( $pagesize );
        $model->setUid( $this->memberInfo->uid );
        $model->setStatus( $status );

        $rs = $model->getSellerSettleList();
        $this->apiReturn( $rs );
    }

}
