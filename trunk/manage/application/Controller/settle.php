<?php

/**
 * 后台 提现 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: settle.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class settleAction extends service_Controller_manage
{

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 我要提现申请页面
     */
    public function apply()
    {
        $model = new service_settle_Save_manage();
        $model->setUid( $this->memberInfo->uid );
        $member_info = $model->getDefaultAccountType();

        $account_bank_array = array();
        if ( !empty( $member_info[ 'account_type' ] ) ) {            
            $account_bank_array = $model->getMemberAccountBankArray();
        }

        $array[ 'member_info' ] = $member_info;
        $array[ 'account_bank_array' ] = $account_bank_array;

        $this->assign( $array );
//        echo '<pre>';
//        print_r( $array );        
//        die;        
        $this->V( 'settle_apply' );
    }

    /**
     * 新增/修改栏目页面
     */
    public function account()
    {
        $model = new service_settle_Save_manage();
        $model->setUid( $this->memberInfo->uid );
        $member_info = $model->getDefaultAccountType();
        $account_bank_array = $model->getMemberAccountBankArray();


        $bank_id_array = Tmac::config( 'member.member_setting.bank_id', APP_BASE_NAME );
        $bank_id_option = Utility::Option( $bank_id_array, $account_bank_array[ 1 ][ 'bank_id' ] );

        $array[ 'member_info' ] = $member_info;
        $array[ 'account_bank_array' ] = $account_bank_array;
        $array[ 'bank_id_option' ] = $bank_id_option;
//        echo '<pre>';
//        print_r( $array );
//        echo '<pre>';
//        die;
        $this->assign( $array );
        $this->V( 'settle_account' );
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
        $settle_bank_id = Input::post( 'settle_bank_id', 0 )->required( '请选择打款平台' )->string();
        $settle_bank_cardnum = Input::post( 'settle_bank_cardnum', '' )->string();
        $settle_bank_account = Input::post( 'settle_bank_account', '' )->string();
        $settle_image_id = Input::post( 'settle_image_id', '' )->imageId();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }


        if ( $settle_status == service_settle_List_base::settle_status_success && (empty( $settle_bank_cardnum ) || empty( $settle_bank_account )) ) {
            $this->redirect( '打款的账号不能为空' );
        }

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

    /**
     * 发送验证码
     * Tested
     * @throws ApiException
     */
    public function send_verify_code()
    {
        //throw new ApiException( '您已经注册会员了',-2 );  //测试
        $model = new service_account_Register_manage();

        try {
            $model->setMobile( $this->memberInfo->mobile );
            $model->setSms_type( service_account_Register_mobile::sms_type_bind_bankcard );
            $model->setNeed_verify_code( false );
            $res = $model->sendVerifyCode();
            if ( $res ) {
                $this->apiReturn( $res ); //ajax成功
            } else {
                throw new ApiException( $model->getErrorMessage() );
            }
        } catch (TmacClassException $e) {
            throw new ApiException( $e->getMessage() );
        }
    }

    public function bank_card_save()
    {
        $account_type = Input::post( 'account_type', 0 )->required( '请选择提现账户类型' )->int();
        $sms_captcha = Input::post( 'sms_captcha', '' )->required( '请输入短信校验码' )->smsCode();
        $alipay_account = Input::post( 'alipay_account', '' )->string();
        $alipay_username = Input::post( 'alipay_username', '' )->string();
        $bank_id = Input::post( 'bank_id', '' )->string();
        $bank_cardnum = Input::post( 'bank_cardnum', '' )->bigint();
        $bank_account = Input::post( 'bank_account', '' )->string();


        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $account_type_array = array( service_settle_Save_base::default_account_type_bank, service_settle_Save_base::default_account_type_alipay );
        if ( !in_array( $account_type, $account_type_array ) ) {
            throw new ApiException( '提现账户类型不正确' );
        }

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->default_account_type = $account_type;
        switch ( $account_type )
        {
            case service_settle_Save_base::default_account_type_bank:
                if ( empty( $bank_id ) || empty( $bank_cardnum ) || empty( $bank_account ) ) {
                    throw new ApiException( '请填写完整银行卡信息' );
                }
                $bank_id_array = Tmac::config( 'member.member_setting.bank_id', APP_MANAGE_NAME );
                $bank_name = $bank_id_array[ $bank_id ];

                if ( empty( $bank_name ) ) {
                    throw new ApiException( '银行不正确' );
                }
                $entity_MemberSetting_base->bank_id = $bank_id;
                $entity_MemberSetting_base->bank_name = $bank_name;
                $entity_MemberSetting_base->bank_cardnum = $bank_cardnum;
                $entity_MemberSetting_base->bank_account = $bank_account;
                break;

            case service_settle_Save_base::default_account_type_alipay:
                if ( empty( $alipay_account ) || empty( $alipay_username ) ) {
                    throw new ApiException( '请填写完整支付宝信息' );
                }
                $entity_MemberSetting_base->alipay_account = $alipay_account;
                $entity_MemberSetting_base->alipay_username = $alipay_username;
                break;
        }
        //检测验证码是否正确
        $check_model = new service_account_Register_manage();

        $check_model->setMobile( $this->memberInfo->mobile );
        $check_model->setSms_type( service_account_Register_mobile::sms_type_bind_bankcard );
        $check_model->setSms_captcha( $sms_captcha );
        $checkInfo = $check_model->checkSmsCode();
        if ( $checkInfo == false ) {
            throw new ApiException( $check_model->getErrorMessage() );
        }
        //保存银行卡信息
        $model = new service_MemberSetting_manage();
        $model->setUid( $this->memberInfo->uid );
        $res = $model->modifyMemberSetting( $entity_MemberSetting_base );
        if ( $res ) {
            $this->apiReturn( array() );
        } else {
            throw new ApiException( '保存出错，请联系客服' );
        }
    }

    /**
     * 申请新的提现
     */
    public function create()
    {
        $money = Input::post( 'money', 0 )->required( '请输入要提现的金额' )->float();
        $account_type = Input::post( 'account_type', 0 )->required( '请选择提现账户类型' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $account_type_array = array( service_settle_Save_base::default_account_type_bank, service_settle_Save_base::default_account_type_alipay );
        if ( !in_array( $account_type, $account_type_array ) ) {
            throw new ApiException( '提现账户类型不正确' );
        }
        //判断最小的提现额度
        //判断是否足够提现的金额
        //创建
        /**
         * $this->uid;
         * $this->money;
         * $this->account_type;
         * $this->createSettle();
         */
        $model = new service_settle_Save_manage();
        $model->setUid( $this->memberInfo->uid );
        $model->setMobile( $this->memberInfo->mobile );
        $model->setMoney( $money );
        $model->setAccount_type( $account_type );
        $model->setMemberInfo( $this->memberInfo );
        try {
            $model->createSettle();
            $this->apiReturn();
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage() );
        }
    }

}
