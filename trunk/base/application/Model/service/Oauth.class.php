<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_Oauth_base extends service_Model_base
{

    /**
     * 第三方oauth类型
     * 微信
     */
    const oauth_type_wechat = 1;

    /**
     * 第三方oauth类型
     * 微博
     */
    const oauth_type_weibo = 2;

    /**
     * 第三方oauth类型
     * QQ
     */
    const oauth_type_qq = 3;

    /**
     * 授权显示PC版网站
     * web
     */
    const display_web = 'web';

    /**
     * 授权显示H5版网站
     * mobile
     */
    const display_mobile = 'mobile';

    /**
     * 授权显示微信公众号授权版网站
     * weixin
     */
    const display_weixin = 'weixin';

    protected $errorMessage;
    protected $code;
    protected $state;
    protected $oauth_referer;
    protected $login_status;
    protected $display = 'mobile';
    protected $domain;
    protected $isApi;
    protected $oauth_id;

    function setOauth_id( $oauth_id )
    {
        $this->oauth_id = $oauth_id;
    }

    function setIsApi( $isApi )
    {
        $this->isApi = $isApi;
    }

    function setCode( $code )
    {
        $this->code = $code;
    }

    function setState( $state )
    {
        $this->state = $state;
    }

    function getOauth_referer()
    {
        if ( empty( $this->oauth_referer ) ) {
            $this->oauth_referer = Input::cookie( 'oauth_referer', '' )->string();
        }
        return stripslashes( $this->oauth_referer );
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function getLogin_status()
    {
        return $this->login_status;
    }

    function setDisplay( $display )
    {
        $this->display = $display;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 工厂模式
     * @param type $source
     * @return \class_name
     * demo
     * ========================
     * $weiboModel = service_Oauth_base::factory('Weibo');
     * $weiboModel->getRoomStatus();
     */
    public static function factory( $source )
    {
        $class_name = "service_oauth_{$source}_base";
        return new $class_name;
    }

    /**
     * 工厂模式
     * @param type $source
     * @return \class_name
     * demo
     * ========================
     * $weiboModel = service_Oauth_base::factory('Weibo');
     * $weiboModel->getRoomStatus();
     */
    public static function apiFactory( $source )
    {
        $class_name = "service_oauth_{$source}_api";
        return new $class_name;
    }

    public function insertMemberOauth( entity_MemberOauth_base $entity_MemberOauth_base )
    {
        $dao = dao_factory_base::getMemberOauthDao();
        return $dao->insert( $entity_MemberOauth_base );
    }

    public function updateMemberOauthById( entity_MemberOauth_base $entity_MemberOauth_base )
    {
        $dao = dao_factory_base::getMemberOauthDao();
        $dao->setPk( $entity_MemberOauth_base->id );
        return $dao->updateByPk( $entity_MemberOauth_base );
    }

    /**
     * oauth授权页面登录，记录来源页面
     */
    public function setRefererCookie( $referer )
    {
        $this->oauth_referer = $referer;
        $expire = 3600 * 2; //2个小时
        setcookie( 'oauth_referer', $referer, $this->now + $expire, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        return $referer;
    }

    /**
     * 把oauth数据种在cookie中
     */
    public function setOauthCookie( entity_MemberOauth_base $entity_MemberOauth_base )
    {
        $oauth_token = md5( $entity_MemberOauth_base->id . $entity_MemberOauth_base->openid . $entity_MemberOauth_base->access_token . $entity_MemberOauth_base->oauth_time );
        $expire = 3600 * 2; //2个小时
        setcookie( 'oauth_id', $entity_MemberOauth_base->id, $this->now + $expire, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        setcookie( 'oauth_token', $oauth_token, $this->now + $expire, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
    }

    /**
     * oauth授权页面登录，记录来源页面
     */
    public function setDomainCookie( $domain )
    {        
        $expire = 1800; //30分钟
        setcookie( 'domain', $domain, $this->now + $expire, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        return $domain;
    }
    /**
     * 绑定用户到member oauth中
     * @param type $memberInfo
     */
    public function bindMemberOauth( $memberInfo )
    {
        //检验oauth
        if ( empty( $this->isApi ) ) {
            $oauth_info = $this->checkOauthFromCookie();
            if ( $oauth_info === false ) {
                return false;
            }
        } else {
            $dao = dao_factory_base::getMemberOauthDao();
            $dao->setPk( $this->oauth_id );
            $oauth_info = $dao->getInfoByPk();
            if ( $oauth_info == false ) {
                $this->errorMessage = '授权信息不合法';
                return false;
            }
        }
        $entity_MemberOauth_base = new entity_MemberOauth_base();
        $entity_MemberOauth_base->uid = $memberInfo->uid;
        $entity_MemberOauth_base->id = $oauth_info->id;
        return $this->updateMemberOauthById( $entity_MemberOauth_base );
    }

    private function checkOauthFromCookie()
    {
        $oauth_id = Input::cookie( 'oauth_id' )->int();
        $oauth_token = Input::cookie( 'oauth_token' )->string();
        if ( empty( $oauth_id ) || empty( $oauth_token ) ) {
            $this->errorMessage = '授权信息不存在';
            return false;
        }
        $dao = dao_factory_base::getMemberOauthDao();
        $dao->setPk( $oauth_id );
        $oauth_info = $dao->getInfoByPk();
        if ( $oauth_info == false ) {
            $this->errorMessage = '授权信息不合法';
            return false;
        }
        $oauth_info instanceof entity_MemberOauth_base;
        $member_oauth_token = md5( $oauth_info->id . $oauth_info->openid . $oauth_info->access_token . $oauth_info->oauth_time );
        if ( $member_oauth_token <> $oauth_token ) {
            $this->errorMessage = '不要hack :-)';
            return false;
        }
        return $oauth_info;
    }

    /**
     * client端的状态值。
     * 用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。
     * 请务必严格按照流程检查用户与state参数状态的绑定。
     */
    protected function checkState()
    {
        if ( $this->state <> session_id() ) {
            $this->errorMessage = 'status值不正确';
            return false;
        }
        return true;
    }

}
