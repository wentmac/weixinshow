<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: MemberAccount.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_MemberAccount_www extends service_Member_base
{

    private $username;
    private $password;
    private $remember;

    const LOGIN_FAILED_COUNT = 5; //8次试错    
    const LOGIN_FAILED_TIME = 15; //默认15分钟    

    public function __construct ()
    {
        parent::__construct ();
    }

    public function setUsername ( $username )
    {
        $this->username = $username;
    }

    public function setPassword ( $password )
    {
        $this->password = $password;
    }

    public function setRemember ( $remember )
    {
        $this->remember = $remember;
    }

    public function handleLogin ()
    {
        //检查用户是否存是否存在
        if ( preg_match ( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $this->username ) ) {
            $where = "mobile='{$this->username}'";
        } else {
            $where = "username='{$this->username}'";
        }
        $dao = dao_factory_base::getMemberDao ();
        $dao->setWhere ( $where );
        $memberInfo = $dao->getInfoByWhere ();

        if ( $memberInfo === false ) {
            throw new TmacClassException ( '用户不存在' );
        }
        $memberLoginFailedCountCheck = $this->checkLoginFailedCount ( $memberInfo );
        if ( $memberLoginFailedCountCheck == false ) {
            throw new TmacClassException ( '密码错误错误次数过多,冻结' . self::LOGIN_FAILED_TIME . '分钟！' );
        }
        
        //判断密码是否正确
        if ( $memberInfo->password == md5 ( $this->password ) ) {
            $token = md5 ( md5 ( $memberInfo->password ) . $memberInfo->salt );
            if ( !empty ( $memberInfo->realname ) ) {
                $username = $memberInfo->realname;
            } else {
                $username = $memberInfo->username;
            }

            $expires_time = 7200;
            if ( !empty ( $this->remember ) ) {
                $expires_time = 86400 * 7;
            }
            setcookie ( "username", urlencode ( $username ), $this->now + $expires_time, '/', $GLOBALS[ 'TmacConfig' ][ 'Cookie' ][ 'domain' ] );
            setcookie ( "token", $token, $this->now + $expires_time, '/', $GLOBALS[ 'TmacConfig' ][ 'Cookie' ][ 'domain' ] );
            setcookie ( "uid", $memberInfo->uid, $this->now + $expires_time, '/', $GLOBALS[ 'TmacConfig' ][ 'Cookie' ][ 'domain' ] );
            return true;
        }
        //返回密码串                
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->last_login_time = $this->now;
        $entity_Member_base->last_login_ip = Functions::get_client_ip ();
        $entity_Member_base->login_fail_count = new TmacDbExpr ( 'login_fail_count+1' );
        $dao->setPk ( $memberInfo->uid );
        $dao->updateByPk ( $entity_Member_base );

        return false;
    }

    /**
     * 检测登录次数是否过多
     * @param type $username
     * @return type 
     */
    private function checkLoginFailedCount ( $memberInfo )
    {
        ( int ) $failedCount = self::LOGIN_FAILED_COUNT - $memberInfo->login_fail_count + 1;
        if ( $memberInfo->login_fail_count <= self::LOGIN_FAILED_COUNT ) {
            return true;
        }
        $lastLoginTime = $this->now - $memberInfo->last_login_time;
        if ( $lastLoginTime > self::LOGIN_FAILED_TIME * 60 ) {
            return true;
        }
        return false;
    }

}
