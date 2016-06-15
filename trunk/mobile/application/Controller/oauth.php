<?php

/**
 * 用户登录注册页面
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class oauthAction extends service_Controller_mobile
{

    //定义初始化变量

    public function _init()
    {
        parent::__construct();
        Tmac::session();
    }

    /**
     * 第三方联合登录回调
     * 微博登录
     * TODO将来上线后做
     */
    public function weibo()
    {
        $code = Input::get( 'code', '' )->string();
        $state = Input::get( 'state', '' )->string();
        $display = Input::get( 'display', 'mobile' )->string();
        $domain = $this->handleDomainUrl( Input::get( 'd', '' )->string() );
        $model = service_Oauth_base::factory( 'Weibo' );
        $model instanceof service_oauth_Weibo_base;
        $model->setDomainCookie( $domain );
        $model->setDisplay( $display );
        if ( empty( $code ) ) {
            setcookie( 'weibo_display', $display, $this->now + 300, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
            parent::headerRedirect( $model->getAuthorizeUrl() );
        } else if ( empty( $code ) && !empty( $state ) ) {
            //用户不同意            
            parent::headerRedirect( $model->getOauth_referer() );
        }
        $model->setCode( $code );
        $model->setState( $state );
        $entity_MemberOauth_base = $model->handle();
        if ( $entity_MemberOauth_base == false ) {
            die( $model->getErrorMessage() );
        }

        if ( $model->getLogin_status() === true ) {//用户已经关联过系统的用户账号、直接跳回之前的页面
            $display = Input::cookie( 'weibo_display', '' )->string();
            $domain = stripslashes( Input::cookie( 'domain', '' )->string() );
            if ( $display == service_Oauth_base::display_web ) {
                echo '<script>document.domain=\'' . $GLOBALS[ 'TmacConfig' ][ 'Cookie' ][ 'domain' ] . '\';window.opener.location.href=\'' . $domain . '\';window.close();</script>';
                exit;
            } else {
                parent::headerRedirect( $model->getOauth_referer() );
            }
        }

        setcookie( 'register_source', service_Account_base::register_source_weibo, $this->now + 3600, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        //跳到绑定页面
        parent::headerRedirect( MOBILE_URL . 'oauth/bind?type=weibo&nickname=' . urlencode( $entity_MemberOauth_base->nickname ) . '&display=' . $display );
    }

    /**
     * 第三方联合登录回调
     * QQ登录
     * todo将来上线后做
     */
    public function qq()
    {
        $code = Input::get( 'code', '' )->string();
        $state = Input::get( 'state', '' )->string();
        $usercancel = Input::get( 'usercancel', '' )->string();
        $display = Input::get( 'display', 'mobile' )->string();
        $domain = $this->handleDomainUrl( Input::get( 'd', '' )->string() );
        $model = service_Oauth_base::factory( 'QQ' );
        $model instanceof service_oauth_QQ_base;
        $model->setDomainCookie( $domain );
        $model->setDisplay( $display );
        if ( empty( $code ) ) {
            parent::headerRedirect( $model->getAuthorizeUrl() );
        } else if ( !empty( $usercancel ) ) {
            //用户不同意            
            parent::headerRedirect( $model->getOauth_referer() );
        }

        $model->setCode( $code );
        $model->setState( $state );
        $entity_MemberOauth_base = $model->handle();
        if ( $entity_MemberOauth_base == false ) {
            die( $model->getErrorMessage() );
        }

        if ( $model->getLogin_status() === true ) {//用户已经关联过系统的用户账号、直接跳回之前的页面                                                
            $domain = stripslashes( Input::cookie( 'domain', '' )->string() );
            if ( $display == service_Oauth_base::display_web ) {
                echo '<script>document.domain=\'' . $GLOBALS[ 'TmacConfig' ][ 'Cookie' ][ 'domain' ] . '\';window.opener.location.href=\'' . $domain . '\';window.close();</script>';
                exit;
            } else {
                parent::headerRedirect( $model->getOauth_referer() );
            }
        }

        setcookie( 'register_source', service_Account_base::register_source_wechat, $this->now + 3600, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        //跳到绑定页面
        parent::headerRedirect( MOBILE_URL . 'oauth/bind?type=qq&nickname=' . urlencode( $entity_MemberOauth_base->nickname ) . '&display=' . $display );
    }

    /**
     * 第三方联合登录回调
     * 微信登录
     */
    public function wechat()
    {
        $code = Input::get( 'code', '' )->string();
        $state = Input::get( 'state', '' )->string();
        $display = Input::get( 'display', 'mobile' )->string();
        $domain = $this->handleDomainUrl( Input::get( 'd', '' )->string() );
        $model = service_Oauth_base::factory( 'Wechat' );
        $model instanceof service_oauth_Wechat_base;
        $model->setDomainCookie( $domain );
        $model->setDisplay( $display );
        if ( empty( $code ) ) {
            parent::headerRedirect( $model->getAuthorizeUrl() );
        } else if ( empty( $code ) && !empty( $state ) ) {
            //用户不同意            
            parent::headerRedirect( $model->getOauth_referer() );
        }
        $model->setCode( $code );
        $model->setState( $state );
        $entity_MemberOauth_base = $model->handle();
        if ( $entity_MemberOauth_base == false ) {
            die( $model->getErrorMessage() );
        }

        if ( $model->getLogin_status() === true ) {//用户已经关联过系统的用户账号、直接跳回之前的页面                                                
            $domain = stripslashes( Input::cookie( 'domain', '' )->string() );
            if ( $display == service_Oauth_base::display_web ) {
                echo '<script>document.domain=\'' . $GLOBALS[ 'TmacConfig' ][ 'Cookie' ][ 'domain' ] . '\';window.opener.location.href=\'' . $domain . '\';window.close();</script>';
                exit;
            } else {
                parent::headerRedirect( $model->getOauth_referer() );
            }
        }

        setcookie( 'register_source', service_Account_base::register_source_wechat, $this->now + 3600, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        //跳到绑定页面
        parent::headerRedirect( MOBILE_URL . 'oauth/bind?type=wechat&nickname=' . urlencode( $entity_MemberOauth_base->nickname ) . '&display=' . $display );
    }

    /**
     * 绑定系统 账户页面
     * 调用手机检测，
     * {
     * 如果已经是会员：执行oauth/login_do
     * 如果还不是会员：执行oauth/register_do
     * }
     */
    public function bind()
    {
        $type = Input::get( 'type', '' )->string();
        $nickname = Input::get( 'nickname', '' )->string();
        $display = Input::get( 'display', '' )->string();

        switch ( $type )
        {
            case 'weibo':
                $oauth_type = '微博';

                break;
            case 'qq':
                $oauth_type = 'QQ';
                break;

            default:
            case 'wechat':
                $oauth_type = '微信';
                break;
        }

        $array[ 'oauth_type' ] = $oauth_type;
        $array[ 'nickname' ] = $nickname;
        $array[ 'display' ] = $display;
        $array[ 'domain' ] = stripslashes( Input::cookie( 'domain', '' )->string() );
        $array[ 'oauth_referer_url' ] = Input::cookie( 'oauth_referer', MOBILE_URL )->sql();

        $this->assign( $array );
        $this->V( 'oauth_bind' );
    }

    /**
     * 第三方用户已经是会员
     * 用于直接登录    
     */
    public function bind_account()
    {
        //进行系统登录关联
        $mobile = Input::post( 'mobile' )->required( '请输入正确的手机号码' )->tel();
        $sms_captcha = Input::post( 'sms_captcha', '' )->required( '请输入短信校验码' )->smsCode();
        $expries = Input::post( 'expries', 1 )->int();
        $callback = Input::get( 'callback', 'callback' )->string();
        $order_cart = Input::post( 'cart', 1 )->int(); //当cart＝1时是在购物车中登录

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        //检查 verify_code 的好坏
        //只要sms_captcha正确就判断是合法用户，
        $model = new service_account_Register_mobile();

        $model->setMobile( $mobile );
        $model->setSms_type( service_account_Register_mobile::sms_type_bind_verify );
        $model->setSms_captcha( $sms_captcha );
        $checkInfo = $model->checkSmsCode();
        if ( $checkInfo == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }

        //在验证过验证码没问题后 执行登录时的系统操作
        $login_model = new service_account_Login_www();
        $login_model->setUsername( $mobile );
        $login_model->setExpries( $expries );
        $member_info = $login_model->checkLoginInfoBySMSCaptcha();
        if ( $member_info == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }

        //绑定用户到member_oauth中
        $oauth_model = service_Oauth_base::factory( 'Wechat' );
        $oauth_model instanceof service_oauth_Wechat_base;
        $oauth_bind_res = $oauth_model->bindMemberOauth( $member_info );
        if ( $oauth_bind_res == false ) {
            throw new ApiException( $oauth_model->getErrorMessage(), -1, $callback );
        }

        if ( $order_cart == 1 ) {
            $order_model = new service_order_Cart_mobile();
            $order_model->updateCartSessionId( $member_info->uid );
        }
        $this->apiReturn( $member_info->uid, 0, 'jsonp', $callback ); //ajax成功        
    }

    /**
     * 第三方用户还不是会员
     * 用于注册
     */
    public function bind_new_account()
    {
        //进行系统注册并关联
        $mobile = Input::post( 'mobile', '' )->required( '请输入正确的手机号' )->tel();
        $sms_captcha = Input::post( 'sms_captcha', '' )->required( '请输入短信校验码' )->smsCode();
        $password = Input::post( 'pwd', rand( 100000, 999999 ) )->password();
        $username = Input::post( 'username', '' )->string();
        $callback = Input::get( 'callback', '' )->string();


        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_account_Register_www();

        $model->setUsername( $username );
        $model->setMobile( $mobile );
        $model->setSms_captcha( $sms_captcha );
        $model->setPassword( $password );
        $model->setRegister_source( Input::cookie( 'register_source', 0 )->int() );

        //注册新用户
        $memberInfo = $model->createMember();
        if ( $memberInfo == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }
        //绑定用户到member_oauth中
        $oauth_model = service_Oauth_base::factory( 'Wechat' );
        $oauth_model instanceof service_oauth_Wechat_base;
        $oauth_bind_res = $oauth_model->bindMemberOauth( $memberInfo );
        if ( $oauth_bind_res == false ) {
            throw new ApiException( $oauth_model->getErrorMessage(), -1, $callback );
        }

        $this->apiReturn( $memberInfo, 0, 'jsonp', $callback ); //ajax成功        
    }

    private function handleDomainUrl( $domain )
    {
        if ( !empty( $domain ) ) {
            $domain .= 'member/home';
        } else {
            $domain = INDEX_URL . 'manage.php?m=bill.home';
        }
        return $domain;
    }

    private function setCookieDomain()
    {
        $domain = stripslashes( Input::cookie( 'domain', '' )->string() );
        $domain = str_replace( 'http://', '', $domain );
        $domain = substr( $domain, 0, -1 );
        $GLOBALS[ 'TmacConfig' ][ 'Cookie' ][ 'domain' ] = $domain;       //cookie���� $_SERVER['HTTP_HOST']
    }

    /**
     * 微信判断openid注册过没有
     * 如果注册过。就直接跳到之前的页面
     * 如果没有注册过，就跳到重新授权的页面,进行注册，oauth/weixin_login
     */
    public function weixin()
    {
        $code = Input::get( 'code', '' )->string();
        $state = Input::get( 'state', '' )->string();
        $model = new service_oauth_Weixin_base();
        if ( empty( $code ) && !empty( $state ) ) {
            //用户不同意            
            parent::headerRedirect( MOBILE_URL );
        }
        $model->setCode( $code );
        $model->setState( $state );
        try {
            $login_status = $model->handle();
        } catch (TmacClassException $exc) {
            die( $exc->getMessage() );
        }

        if ( $login_status == false ) {
            $model->setRedirect_uri( MOBILE_URL . 'oauth/weixin_login' );
            $model->setScope( 'snsapi_userinfo' );
            parent::headerRedirect( $model->getAuthorizeUrl() );
            exit();
        }
        $oauth_referer = Input::cookie( 'oauth_referer', $this->redirect_uri )->string();
        //跳到绑定页面
        parent::headerRedirect( $oauth_referer );
    }

    /**
     * 微信重新授权的注册页面
     */
    public function weixin_login()
    {
        $code = Input::get( 'code', '' )->string();
        $state = Input::get( 'state', '' )->string();
        $model = new service_oauth_Weixin_base();
        if ( empty( $code ) && !empty( $state ) ) {
            //用户不同意            
            parent::headerRedirect( MOBILE_URL );
        }
        $model->setCode( $code );
        $model->setState( $state );
        try {
            $model->handleRegister();
        } catch (TmacClassException $exc) {
            die( $exc->getMessage() );
        }
        $oauth_referer = Input::cookie( 'oauth_referer', $this->redirect_uri )->string();
        //跳到绑定页面
        parent::headerRedirect( $oauth_referer );
    }

    /**
     * 给企业付款的appid授权用的
     * 微信判断openid授权过没有
     * 如果授权过。就直接跳到之前的页面
     * 如果没有授权过，就跳到重新授权的页面,进行注册，oauth/weixin_transfers_login
     */
    public function weixin_transfers()
    {
        $this->checkLogin();
        $code = Input::get( 'code', '' )->string();
        $state = Input::get( 'state', '' )->string();
        $model = new service_oauth_WeixinTransfers_base();
        if ( empty( $code ) && !empty( $state ) ) {
            //用户不同意            
            parent::headerRedirect( MOBILE_URL . 'member/home' );
        }
        $model->setCode( $code );
        $model->setState( $state );
        $model->setMemberInfo( $this->memberInfo );
        try {
            $model->handle();
        } catch (TmacClassException $exc) {
            die( $exc->getMessage() );
        }
        $oauth_referer = MOBILE_URL . 'member/bill.home';
        //跳到绑定页面
        parent::headerRedirect( $oauth_referer );
    }

}
