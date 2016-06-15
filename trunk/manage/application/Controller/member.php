<?php

/**
 * 后台 文章栏目模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: member.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class memberAction extends service_Controller_manage
{

    private $tmp_model;
    private $check_model;

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 店铺基本设置
     */
    public function basic()
    {
        $model = new service_MemberSetting_manage();
        $model->setUid( $this->memberInfo->uid );
        $member_setting_info = $model->getMemberSettingBasic();

        $array[ 'editinfo' ] = $member_setting_info;

//        echo '<pre>';
//        print_r( $array );
//        echo "</pre>";
        //die;
        $this->assign( $array );
        $this->V( 'member_basic' );
    }

    /**
     * 店铺基本设置 保存
     */
    public function basic_save()
    {
        //shop_name,shop_intro,shop_image_id
        $shop_name = Input::post( 'shop_name', '' )->required( '请输入店铺名称' )->string();
        $shop_intro = Input::post( 'shop_intro', '' )->required( '请输入店铺简介' )->string();
        $shop_image_id = Input::post( 'shop_image_id', '' )->required( '请上传店铺LOGO' )->imageId();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->shop_name = $shop_name;
        $entity_MemberSetting_base->shop_intro = $shop_intro;
        $entity_MemberSetting_base->shop_image_id = $shop_image_id;

        $model = new service_MemberSetting_manage();
        $model->setUid( $this->memberInfo->uid );
        $res = $model->modifyMemberSetting( $entity_MemberSetting_base );
        if ( $res ) {
            $this->apiReturn( array() );
        } else {
            throw new ApiException( '保存出错，请联系客服!' );
        }
    }

    /**
     * 身份验证
     */
    public function idcard()
    {
        $model = new service_MemberSetting_manage();
        $model->setUid( $this->memberInfo->uid );
        $member_setting_info = $model->getMemberSettingIdcard();

        $member_setting_info->realname = $this->memberInfo->realname;
        $array[ 'editinfo' ] = $member_setting_info;

        //echo '<pre>';
        //print_r( $array );
        //echo "</pre>";
        //die;
        $this->assign( $array );
        $this->V( "member_idcard" );
    }

    /**
     * 身份验证保存
     */
    public function idcard_save()
    {
        //idcard,idcard_positive_image_id,idcard_negative_image_id,idcard_verify
        $idcard = Input::post( 'idcard', '' )->required( '请输入身份证号' )->bigint();
        $realname = Input::post( 'realname', '' )->required( '请输入真实姓名' )->string();
        $idcard_positive_image_id = Input::post( 'idcard_positive_image_id', '' )->required( '请上传身份证正面' )->imageId();
        $idcard_negative_image_id = Input::post( 'idcard_negative_image_id', '' )->required( '请上传身份证反面' )->imageId();
        $idcard_image_id = Input::post( 'idcard_image_id', '' )->required( '请上传身份证反面' )->imageId();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $idcard_check = $this->checkIdCard( $idcard );
        if ( $idcard_check == false ) {
            throw new ApiException( '身份证号格式不对' ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->idcard = $idcard;
        $entity_MemberSetting_base->idcard_positive_image_id = $idcard_positive_image_id;
        $entity_MemberSetting_base->idcard_negative_image_id = $idcard_negative_image_id;
        $entity_MemberSetting_base->idcard_image_id = $idcard_image_id;

        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->realname = $realname;

        $model = new service_MemberSetting_manage();
        $model->setUid( $this->memberInfo->uid );
        $res = $model->modifyMemberSetting( $entity_MemberSetting_base, $entity_Member_base );
        $res = $model->modifyMember( $entity_Member_base );
        if ( $res ) {
            $this->apiReturn( array() );
        } else {
            throw new ApiException( '保存出错，请联系客服' );
        }
    }

    /**
     * 支付设置
     */
    public function payment()
    {
        $model = new service_MemberSetting_manage();
        $model->setUid( $this->memberInfo->uid );
        $member_setting_info = $model->getMemberSettingPayment();

        $bank_id_array = Tmac::config('member.member_setting.bank_id');
        $bank_id_option = Utility::Option($bank_id_array, $member_setting_info->bank_id);
        $array[ 'bank_id_option' ] = $bank_id_option;
        $array[ 'editinfo' ] = $member_setting_info;

        //echo '<pre>';
        //print_r( $array );
        //echo '</pre>';
        //die;
        $this->assign( $array );
        $this->V( 'member_payment' );
    }

    /**
     * 支付设置保存
     */
    public function payment_save()
    {
        //bank_id,bank_pid,bank_cityid,bank_name,bank_cardnum,bank_account
        $bank_id = Input::post( 'bank_id', '' )->required( '请选择银行' )->string();
        $bank_pid = Input::post( 'bank_pid', 0 )->required( '请选择开户银行省份' )->int();
        $bank_cityid = Input::post( 'bank_cityid', 0 )->required( '请选择开户银行城市' )->int();
        $bank_cardnum = Input::post( 'bank_cardnum', '' )->required( '请输入开户账号' )->bigint();
        $bank_account = Input::post( 'bank_account', '' )->required( '请输入开户姓名' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $bank_id_array = Tmac::config('member.member_setting.bank_id');
        $bank_name = $bank_id_array[$bank_id];
        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->bank_id = $bank_id;
        $entity_MemberSetting_base->bank_pid = $bank_pid;
        $entity_MemberSetting_base->bank_cityid = $bank_cityid;
        $entity_MemberSetting_base->bank_name = $bank_name;
        $entity_MemberSetting_base->bank_cardnum = $bank_cardnum;
        $entity_MemberSetting_base->bank_account = $bank_account;

        $model = new service_MemberSetting_manage();
        $model->setUid( $this->memberInfo->uid );
        $res = $model->modifyMemberSetting( $entity_MemberSetting_base );
        if ( $res ) {
            $this->apiReturn( array() );
        } else {
            throw new ApiException( '保存出错，请联系客服' );
        }
    }

    private function checkIdCard( $idcard )
    {

        // 只能是18位  
        if ( strlen( $idcard ) != 18 ) {
            return false;
        }

        // 取出本体码  
        $idcard_base = substr( $idcard, 0, 17 );

        // 取出校验码  
        $verify_code = substr( $idcard, 17, 1 );

        // 加权因子  
        $factor = array( 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 );

        // 校验码对应值  
        $verify_code_list = array( '1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2' );

        // 根据前17位计算校验码  
        $total = 0;
        for ( $i = 0; $i < 17; $i++ ) {
            $total += substr( $idcard_base, $i, 1 ) * $factor[ $i ];
        }

        // 取模  
        $mod = $total % 11;

        // 比较校验码  
        if ( $verify_code == $verify_code_list[ $mod ] ) {
            return true;
        } else {
            return false;
        }
    }

}
