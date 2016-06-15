<?php

/**
 * 用户登录注册页面
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class accountAction extends service_Controller_mobile
{

    private $sms_type_array;

    //定义初始化变量

    public function _init()
    {
        Tmac::session();
        //加载并返回Model文件夹下的Model对象。	
        $this->sms_type_array = array( 1, 2, 3, 4, 5, 6 );
    }

    /**
     * 说明：登录页面
     * Tested
     */
    public function login()
    {
        $referer = stripslashes( Input::get( 'referer', MOBILE_URL )->string() );

        $model = new service_Oauth_base();

        $referer_url = $model->setRefererCookie( $referer );

        //如果己经登录，跳转回去
        if ( $this->checkLoginStatus() ) {
            parent::headerRedirect( $referer_url );
            exit;
        }
        $this->assign( 'referer_login', $referer_url );
        $this->V( 'account_login' );
    }

    /**
     * 说明:退出登录
     * Tested
     */
    public function loginout()
    {
        $model = new service_account_Login_www();
        $model->loginOut();
        $redirect = MOBILE_URL;
        $this->headerRedirect( $redirect );
    }

    /**
     * 说明:退出登录
     * Tested
     */
    public function login_out()
    {
        $callback = Input::get( 'callback', 'callback' )->string();

        $model = new service_account_Login_www();
        $model->loginOut();
        $this->apiReturn( array(), 0, 'jsonp', $callback ); //ajax成功
    }

    /**
     * 说明：异步登录
     * Tested
     */
    public function login_do()
    {
        $username = Input::post( 'username' )->required( '请输入正确的手机号码' )->tel();
        $password = Input::post( 'password' )->required( '密码不能为空' )->password();
        $expries = Input::post( 'expries', 0 )->int();
        $callback = Input::get( 'callback', 'callback' )->string();
        $order_cart = Input::post( 'cart', 0 )->int(); //当cart＝1时是在购物车中登录

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        //检查 verify_code 的好坏
        $model = new service_account_Login_mobile();
        $model->setUsername( $username );
        $model->setPassword( $password );
        $model->setExpries( $expries );

        $login_data = $model->checkLoginInfo();
        if ( $login_data == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }

        if ( $order_cart == 1 ) {
            $order_model = new service_order_Cart_mobile();
            $order_model->updateCartSessionId( $login_data->uid );
        }
        $this->apiReturn( $login_data->uid, 0, 'jsonp', $callback ); //ajax成功
        //$this->apiReturn( $login_data ); //ajax成功
    }

    /**
     * 说明：注册页面
     */
    public function register()
    {
        $model = new service_account_Register_www();
        $referer_url = $model->getReferer();
        //如果己经登录，不能进入注册页面
        if ( $this->checkLoginStatus() ) {
            self::headerRedirect( $referer_url );
            exit;
        }
        $this->assign( 'referer_login', $referer_url );
        $this->V( 'account_login' );
    }

    /**
     * 说明：创建新用户
     * Tested
     * @author  by time 2014-07-19
     */
    public function register_do()
    {
        $mobile = Input::post( 'mobile', '' )->required( '请输入正确的手机号' )->tel();
        $sms_captcha = Input::post( 'sms_captcha', '' )->required( '请输入短信校验码' )->smsCode();
        $password = Input::post( 'pwd', '' )->required( '密码不能为空' )->password();
        $username = Input::post( 'username', '' )->string();
        $callback = Input::get( 'callback', '' )->string();


        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_account_Register_mobile();

        $model->setUsername( $username );
        $model->setMobile( $mobile );
        $model->setSms_captcha( $sms_captcha );
        $model->setPassword( $password );

        //注册新用户
        $reg_info = $model->createMember();
        if ( $reg_info == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }
        $this->apiReturn( $reg_info, 0, 'jsonp', $callback ); //ajax成功
    }

    /**
     * 忘记密码的 post action
     * Tested
     */
    public function password()
    {
        $mobile = Input::post( 'mobile', '' )->required( '请输入正确的手机号' )->tel();
        $sms_captcha = Input::post( 'sms_captcha', '' )->required( '请输入短信校验码' )->smsCode();
        $password = Input::post( 'pwd', '' )->required( '密码不能为空' )->password();
        $callback = Input::get( 'callback', 'callback' )->string();


        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_account_Register_mobile();
        $model->setMobile( $mobile );
        $model->setSms_captcha( $sms_captcha );
        $model->setSms_type( service_account_Register_mobile::sms_type_forget_password );
        $model->setPassword( $password );

        $res = $model->resetPassword();
        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }
        $this->apiReturn( $res, 0, 'jsonp', $callback ); //ajax成功
        //
        //self::headerRedirect( $referer_url, 200 );        
    }

    /**
     * 说明：异步检查短信验证码是否正确
     * Tested
     * @author  by time 2014-07-20
     */
    public function check_sms_code()
    {
        $mobile = Input::post( 'mobile', '' )->required( '手机号码不能为空' )->tel();
        $sms_type = Input::post( 'sms_type', 1 )->int(); //短信分类（1：账户注册｜2：找回密码）
        $sms_captcha = Input::post( 'sms_captcha', '' )->required( '验证码不能为空' )->smsCode();
        $callback = Input::get( 'callback', 'callback' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }
        if ( !in_array( $sms_type, $this->sms_type_array ) ) {
            throw new ApiException( '短信验证码类型不正确', -1, $callback );
        }

        $model = new service_account_Register_mobile();

        $model->setMobile( $mobile );
        $model->setSms_type( $sms_type );
        $model->setSms_captcha( $sms_captcha );
        $checkInfo = $model->checkSmsCode();
        if ( $checkInfo == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }
        $this->apiReturn( $checkInfo, 0, 'jsonp', $callback ); //ajax成功
    }

    /**
     * 发送验证码     
     * Tested
     * @throws ApiException
     */
    public function send_verify_code()
    {
        $mobile = Input::post( 'mobile', '' )->required( '请输入要验证手机号!' )->tel();
        $sms_type = Input::post( 'sms_type', 1 )->int(); //短信分类（1：账户注册｜2：找回密码）
        $verify_code = Input::post( 'verify_code', '' )->string();
        $callback = Input::get( 'callback', 'callback' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback );
        }

        if ( !in_array( $sms_type, $this->sms_type_array ) ) {
            throw new ApiException( '短信验证码类型不正确', -1, $callback );
        }

        //throw new ApiException( '您已经注册会员了',-2 );  //测试

        $refere_url = isset( $_SERVER [ 'HTTP_REFERER' ] ) ? filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING ) : '';
        $_parse_refere = parse_url( $refere_url );
        $_refere_host = isset( $_parse_refere [ 'host' ] ) ? 'http://' . $_parse_refere [ 'host' ] . '/' : '';

        if ( empty( $refere_url ) || (($_refere_host <> INDEX_URL) && $_refere_host <> MOBILE_URL) ) {
            header( 'HTTP/1.1 403 Forbidden' );
            exit();
        }

        $model = new service_account_Register_mobile();
        $model->setMobile( $mobile );
        $model->setVerify_code( $verify_code );

        //检查用户是否存在
        if ( $sms_type == service_account_Register_mobile::sms_type_register && $model->checkMobileRepeat() ) {
            throw new ApiException( '您已经注册会员了', -2, $callback );
        }

        if ( $sms_type == service_account_Register_mobile::sms_type_forget_password && $model->checkMobileRepeat() == false ) {
            throw new ApiException( '您的还不是会员不能使用找回密码', -1, $callback );
        }

        if ( $sms_type == service_account_Register_mobile::sms_type_bind_verify && $model->checkMobileRepeat() == false ) {
            throw new ApiException( '您的手机号没有注册', -1, $callback );
        }

        if ( $sms_type == service_account_Register_mobile::sms_type_bind_mobile && $model->checkMobileRepeat() !== false ) {
            throw new ApiException( '您的手机号已经注册过注册', -1, $callback );
        }

        try {
            $model->setMobile( $mobile );
            $model->setSms_type( $sms_type );
            $model->setNeed_verify_code( false );
            $res = $model->sendVerifyCode();
            if ( $res ) {
                $this->apiReturn( $res, 0, 'jsonp', $callback ); //ajax成功
            } else {
                if ( $model->getNeedVerifyCode() ) {//需要图片验证码                   
                    $code = 0;
                } else {
                    $code = -1;
                }
                throw new ApiException( $model->getErrorMessage(), $code, $callback );
            }
        } catch (TmacClassException $e) {
            throw new ApiException( $e->getMessage(), -1, $callback );
        }
    }

    /**
     * 说明：生成图片验证码
     * Tested
     */
    public function verifyimg()
    {
        if ( DIRECTORY_SEPARATOR == '\\' ) {
            $image = imagecreatetruecolor( 58, 22 );
            $color_Background = imagecolorallocate( $image, 255, 255, 255 );
            imagefill( $image, 0, 0, $color_Background );
            $key = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
            $string = null;
            $char_X = 6;
            $char_Y = 0;
            for ( $i = 0; $i < 4; $i++ ) {
                $char_Y = mt_rand( 0, 5 );
                $char = $key[ mt_rand( 0, 9 ) ];
                $string .= $char;
                $color_Char = imagecolorallocate( $image, mt_rand( 0, 230 ), mt_rand( 0, 230 ), mt_rand( 0, 230 ) );
                imagechar( $image, 5, $char_X, $char_Y, $char, $color_Char );
                $char_X = $char_X + mt_rand( 8, 15 );
            }
            $line_X1 = 0;
            $line_Y1 = 0;
            $line_X2 = 0;
            $line_Y2 = 0;
            for ( $i = 0; $i < mt_rand( 0, 64 ); $i++ ) {
                $line_X1 = mt_rand( 0, 58 );
                $line_Y1 = mt_rand( 0, 22 );
                $line_X2 = mt_rand( 0, 58 );
                $line_Y2 = mt_rand( 0, 22 );
                $line_X1 = $line_X1;
                $line_Y1 = $line_Y1;
                $line_X2 = $line_X1 + mt_rand( 1, 8 );
                $line_Y2 = $line_Y1 + mt_rand( 1, 8 );
                $color_Line = imagecolorallocate( $image, mt_rand( 0, 230 ), mt_rand( 0, 230 ), mt_rand( 0, 230 ) );
                imageline( $image, $line_X1, $line_Y1, $line_X2, $line_Y2, $color_Line );
            }
            $key = 'verify_code';
            $_SESSION[ $key ] = md5( $string );
            header( 'Content-Type: image/jpeg' );
            imagepng( $image );
            imagedestroy( $image );
        } else {
            $key = 'verify_code';
            $captcha = new service_utils_Captcha_base ();
            $_SESSION [ $key ] = strtolower( $captcha::generate() );
        }
    }

    /**
     * 说明：异步检查用户手机号是否注册
     * Tested
     * @author  by time 2014-07-19
     */
    public function check_mobile_isreg()
    {
        $mobile = Input::get( 'mobile', '' )->required( '手机号码不能为空' )->tel();
        $callback = Input::get( 'callback', 'callback' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_account_Register_www();

        $model->setMobile( $mobile );
        $checkInfo = $model->checkMobileRepeat();
        if ( $checkInfo == true ) {
            throw new ApiException( '手机号码已经注册过，请直接登录', -1, $callback );
        }
        $this->apiReturn( array(), 0, 'jsonp', $callback ); //ajax成功
    }

    /**
     * 说明：异步检查用户名是否注册
     * Tested
     * @author  by time 2014-07-19
     */
    public function check_username_isreg()
    {
        $username = Input::get( 'username', '' )->required( '要检测的用户名不能为空' )->username();
        $callback = Input::get( 'callback', 'callback' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_account_Register_www();

        $model->setUsername( $username );
        $checkInfo = $model->checkUsernameRepeat();
        if ( $checkInfo == true ) {
            throw new ApiException( '用户名已经存在', -1, $callback );
        }
        $this->apiReturn( array(), 0, 'jsonp', $callback ); //ajax成功
    }

    /**
     * 第三方用户已经是会员
     * 用于直接登录    
     */
    public function bind_mobile()
    {

        //进行系统登录关联
        $mobile = Input::post( 'mobile' )->required( '请输入正确的手机号码' )->tel();
        $sms_captcha = Input::post( 'sms_captcha', '' )->required( '请输入短信校验码' )->smsCode();
        $callback = Input::get( 'callback', 'callback' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }
        if ( $this->checkLoginStatus() == false ) {
            throw new ApiException( '请先登录', -1, $callback ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }
        //检查 verify_code 的好坏
        //只要sms_captcha正确就判断是合法用户，
        $model = new service_account_Register_mobile();

        $model->setMobile( $mobile );
        $model->setSms_type( service_account_Register_mobile::sms_type_bind_mobile );
        $model->setSms_captcha( $sms_captcha );
        $checkInfo = $model->checkSmsCode();
        if ( $checkInfo == false ) {
            throw new ApiException( $model->getErrorMessage(), -1, $callback );
        }

        //验证手机号不存在
        //检测当前会员登录成功
        //执行绑定
        //在验证过验证码没问题后 执行登录时的系统操作
        $login_model = new service_account_Login_mobile();
        try {
            $login_model->bindMobile( $this->memberInfo, $mobile );
        } catch (TmacClassException $exc) {
            throw new ApiException( $exc->getMessage(), -1, $callback );
        }

        $this->apiReturn( array(), 0, 'jsonp', $callback ); //ajax成功
    }

}
