<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_account_Login_base extends service_Account_base
{

    protected $username;
    protected $password;
    protected $expries;
    protected $failedCount = 0;

    function setUsername( $username )
    {
        $this->username = $username;
    }

    function setPassword( $password )
    {
        $this->password = $password;
    }

    function setExpries( $expries )
    {
        $this->expries = $expries;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 说明：用户退出,清除对应的COOKIE
     * @author zhuqiang by time 2014-07-09
     */
    public function loginOut()
    {
        setcookie( 'uid', '', $this->now - 3600, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        setcookie( 'token', '', $this->now - 3600, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        setcookie( 'username', '', $this->now - 3600, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );        
        setcookie( 'mobile', '', $this->now - 3600, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );        
        return true;
    }

    /**
     * 说明：检查用户登录信息，返回是否登录成功或错误信息
     * @author zhuqiang by time 2014-07-09
     * @param array $login_info
     */
    public function checkLoginInfo()
    {
        //todo 检测验证码        
        /**
          $verify_code_result = parent::checkVerifyCode( $this->verify_code );
          if ( $verify_code_result == false ) {
          return false;
          }
         * 
         */
        $dao = dao_factory_base::getMemberDao();

        $member_info = $this->getMemberInfoByUserName( $this->username );

        if ( empty( $member_info ) ) {
            $this->errorMessage = '亲，帐号或密码错误哦!';
            return false;
        }

        if ( $this->_checkDenyUser( $member_info ) === false ) {
            $this->errorMessage = '亲，您的账户被禁用，请联系客服';
            return FALSE;
        }

        $checkLoginFailedResult = $this->checkLoginFailedCount( $member_info );
        if ( $checkLoginFailedResult == false ) {
            return false;
        }

        //token $password = md5_16(md5($password).$salt);
        //$password = MD5(pass+salt)
        $check_password = md5( $this->password . $member_info->salt );

        if ( $member_info->password <> $check_password ) {
            self::updateLoginFailed( $member_info->uid );
            $this->errorMessage = "亲，帐号或密码错误了";
            return false;
        }
        $_SESSION[ 'verify_code_error_count' ] = 0;

        if ( $this->expries == 1 ) {
            $expire_day = 30;
        } else {
            $expire_day = 1;
        }

        //更新登录成功后member表的数据
        $this->modifyMemberLoginInfo( $member_info->uid );
        //更新cookie
        $this->isApi || $this->updateMemberCookieCheck( $member_info, $expire_day );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return $member_info;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 通过验证码登录||在控制器中已经判断过验证码的真实性了
     * $this->username;
     * $this->expries;
     * $this->checkLoginInfoBySMSCaptcha();
     */
    public function checkLoginInfoBySMSCaptcha()
    {
        $dao = dao_factory_base::getMemberDao();

        $member_info = $this->getMemberInfoByUserName( $this->username );

        if ( empty( $member_info ) ) {
            $this->errorMessage = '亲，帐号或密码错误哦!';
            return false;
        }

        if ( $this->_checkDenyUser( $member_info ) === false ) {
            $this->errorMessage = '亲，您的账户被禁用，请联系客服';
            return FALSE;
        }

        $checkLoginFailedResult = $this->checkLoginFailedCount( $member_info );
        if ( $checkLoginFailedResult == false ) {
            return false;
        }

        if ( $this->expries == 1 ) {
            $expire_day = 30;
        } else {
            $expire_day = 1;
        }

        //更新登录成功后member表的数据
        $this->modifyMemberLoginInfo( $member_info->uid );
        //更新cookie
        $this->updateMemberCookieCheck( $member_info, $expire_day );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return $member_info;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 说明：检查是否是禁止登录的用户
     * @author zhuqiang by time 2014-07-09
     * @param object $member_info
     */
    public static function _checkDenyUser( $member_info )
    {
        $flag = true;
        $member_info instanceof entity_Member_base;

        if ( in_array( $member_info->uid, parent::$DENY_USER_ARRAY ) ) {
            $flag = FALSE;
        }

        return $flag;
    }

    /**
     * 更新用户登录失败次数及失败时间
     * @return type 
     */
    private function updateLoginFailed( $uid )
    {
        if ( isset( $_SESSION[ 'verify_code_error_count' ] ) ) {
            $_SESSION[ 'verify_code_error_count' ] ++;
        } else {
            $_SESSION[ 'verify_code_error_count' ] = 0;
        }
        $dao = dao_factory_base::getMemberDao();
        $entity_Member = new entity_Member_base();
        $entity_Member->last_login_time = $this->now;
        $entity_Member->login_fail_count = new TmacDbExpr( 'login_fail_count+1' );

        $dao->setPk( $uid );
        return $dao->updateByPk( $entity_Member );
    }

    /**
     * 检测登录次数是否过多
     * @param type $username
     * @return type 
     */
    private function checkLoginFailedCount( $memberInfo )
    {
        $memberInfo instanceof entity_Member_base;
        (int) $this->failedCount = self::MAX_FAILD_COUNT - $memberInfo->login_fail_count + 1;

        if ( $memberInfo->login_fail_count <= self::MAX_FAILD_COUNT ) {
            return true;
        }
        $lastLoginTime = $this->now - $memberInfo->last_login_time;
        if ( $lastLoginTime > self::FAILD_ALLOW_TIME * 60 ) {
            return true;
        }
        self::updateLoginFailed( $memberInfo->uid );
        $this->errorMessage = '密码错误错误次数过多,冻结' . self::FAILD_ALLOW_TIME . '分钟！';
        return false;
    }

    /**
     * 说明：登录成功后，更新用户登录信息
     * @author zhuqiang by time 2014-07-09
     * @param object $member_info
     */
    public function modifyMemberLoginInfo( $uid )
    {
        $update_member_info = new entity_Member_base ();

        $update_member_info->last_login_ip = Functions::get_client_ip();
        $update_member_info->last_login_time = $this->now;
        $update_member_info->login_fail_count = 0;

        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $uid );
        $rs = $dao->updateByPk( $update_member_info );
        return $rs;
    }

}
