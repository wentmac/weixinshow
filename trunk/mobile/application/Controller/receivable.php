<?php

/**
 * mobile 购物车 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: receivable.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class receivableAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $receivable_id = Input::get( 'id', 0 )->required( '收款项目不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            die( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_Receivable_mobile();
        $info = $model->getReceivableInfo( $receivable_id );
        if ( !$info ) {
            die( '收款项目不存在' );
        }

        //登录状态（收货地址）
        $login_status = $this->checkLoginStatus();
        if ( $login_status ) {
            //调用收货地址
            $member_info = array(
                'uid' => $this->memberInfo->uid,
                'realname' => $this->memberInfo->realname,
                'mobile' => $this->memberInfo->mobile
            );
        } else {
            //告诉用户登录
            $member_info = array(
                'uid' => 0,
                'realname' => '',
                'mobile' => Input::cookie( 'mobile', '' )->tel()
            );
        }


        $array[ 'member_info' ] = $member_info;
        $array[ 'member_info_json' ] = json_encode( $member_info );
        $array[ 'shop_logo_url' ] = $model->getShopLogo( $info->uid );
        $array[ 'receivable_info' ] = $info;
        $array[ 'receivable_info_json' ] = json_encode( $info );
//          echo '<pre>';
//          print_r( $array );
//          echo "</pre>";
//        die;        
        $this->assign( $array );
        $this->V( 'receivable' );
    }

    /**
     * 保存订单
     */
    public function order_save()
    {
        $receivable_id = Input::post( 'id', 0 )->required( '请选择收款项目' )->int();
        $realname = Input::post( 'realname', 0 )->string();
        $mobile = Input::post( 'mobile', 0 )->tel();
        $postscript = Input::post( 'postscript', 0 )->string();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }
        $login_status = $this->checkLoginStatus();
        $uid = 0;
        if ( $login_status ) {
            $uid = $this->memberInfo->uid;
            $realname = $this->memberInfo->realname;
            $mobile = $this->memberInfo->mobile;
        } else {
            if ( empty( $realname ) || empty( $mobile ) ) {
                throw new ApiException( '请填写姓名或手机' ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容    
            }
        }
        //判断是否存在
        $model = new service_Receivable_mobile();
        $receivable_info = $model->getReceivableInfo( $receivable_id );
        if ( !$receivable_info ) {
            throw new ApiException( '收款项目不存在' );
        }

        $receivable_order_save_model = new service_order_ReceivableSave_mobile();
        $receivable_order_save_model->setUid( $uid );
        $receivable_order_save_model->setRealname( $realname );
        $receivable_order_save_model->setMobile( $mobile );
        $receivable_order_save_model->setMemberInfo( $this->memberInfo );
        $receivable_order_save_model->setPostscript( $postscript );
        $receivable_order_save_model->setReceivable_id( $receivable_id );
        $receivable_order_save_model->setReceivable_info( $receivable_info );

        $order_sn = $receivable_order_save_model->createOrder();
        if ( $order_sn == false ) {
            throw new ApiException( $receivable_order_save_model->getErrorMessage() );
        }
        $this->apiReturn( $order_sn );
    }

}
