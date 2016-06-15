<?php

/**
 * 接口 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Controller.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
abstract class service_Controller_www extends service_Controller_base
{

    protected $memberMallInfo;

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
        $agent_uid_cookie = Input::cookie( 'agent_uid', service_Member_base::yph_uid )->int();
        //memberMallInfo->uid = !empty($_COOKIE['uid']) ? $_COOKIE['uid'] : system_uid;
        $loginUrl = MOBILE_URL . 'account/login?referer=' . urlencode( MOBILE_URL . substr( $_SERVER[ 'REQUEST_URI' ], 1 ) );
        $this->setLoginUrl( $loginUrl );

        $referer = isset( $_SERVER [ 'HTTP_REFERER' ] ) ? filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING ) : MOBILE_URL;                
        $this->assign( 'referer_url', $referer );
    }

    /**
     * 检查接口签名     
     */
    protected function checkSign()
    {
        $appkey = Input::get( 'appkey', 0 )->int();
        $sign = Input::get( 'sign', '' )->string();
        $timestamp = Input::get( 'timestamp', 0 )->int();
        $method = 'WebView.authorize';
        //$method = $_GET[ 'TMAC_CONTROLLER_FILE' ] . '.' . $_GET[ 'TMAC_ACTION' ];

        if ( empty( $appkey ) || empty( $sign ) || empty( $timestamp ) ) {
            $this->checkLogin();
            return true;
        }

        $time = $this->now;
        $timestampEarly = $time - 7200;
        $timestampLast = $time + 7200;
        if ( $timestamp < $timestampEarly || $timestamp > $timestampLast ) {
            die( '接口认证过期|请检查您的手机日期时间设置是否准确？当前服务器时间：' . date( 'Y-m-d H:i:s' ) );
        }
        $check_model = Tmac::model( 'Check', APP_API_NAME );
        $check_model instanceof service_Check_api;
        $checkSign = $check_model->generateSign( $method, $timestamp, $appkey, $sign );
        if ( $checkSign <> $sign ) {
            die( '接口签名认证失败' );
        }

        //验证oauth过来的用户登录认证是否合法
        $this->checkOauthLogin();
        //种下登录认证功能的cookie
        $login_model = new service_account_Login_base();
        //更新cookie
        $expire = 1; //1天过期时间
        $login_model->updateMemberCookieCheck( $this->memberInfo, $expire );
        return true;
    }

    /**
     * 检测是否登录 api的url登录
     */
    protected function checkOauthLogin()
    {
        $uid = Input::get( 'uid', 0 )->required( '用户UID不能为空' )->int();
        $token = Input::get( 'token', '' )->required( '用户验证密钥不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $checkEffective = parent::checkMemberStatus( $uid );
        if ( $checkEffective === false ) {
            throw new ApiException( $this->getErrorMessage() );
        }
        if ( PRODUCTION_MODE == false ) {
            return true;
        }
        if ( $token <> md5( md5( $this->memberInfo->password ) . $this->memberInfo->salt ) ) {
            throw new ApiException( '认证失败，请先登录', -2 );
        }
    }

    public function no( $title = '' )
    {
        $array[ 'title' ] = $title;
        $this->assign( $array );
        $this->V( '404' );
        exit();
    }

}
