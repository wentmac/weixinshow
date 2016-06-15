<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_oauth_WeixinTransfers_base extends service_Model_base
{

    protected $code;
    protected $state;
    protected $redirect_uri;
    protected $login_status;
    protected $errorMessage;
    protected $oauth_array;
    protected $scope = 'snsapi_userinfo';
    protected $memberInfo;

    function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
    }

    function setRedirect_uri( $redirect_uri )
    {
        $this->redirect_uri = $redirect_uri;
    }

    function setCode( $code )
    {
        $this->code = $code;
    }

    function setState( $state )
    {
        $this->state = $state;
    }

    function setScope( $scope )
    {
        $this->scope = $scope;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
        $this->oauth_array = Tmac::config( 'oauth.oauth.weixin_transfers', APP_WWW_NAME );
    }

    /**
     * 返回第三方Oauth授权的URL
     * @return string
     */
    public function getAuthorizeUrl()
    {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $scope = $this->scope;
        //location header
        $parameter = array(
            'appid' => $this->oauth_array[ 'appid' ],
            'redirect_uri' => $this->redirect_uri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => md5( $this->redirect_uri )
        );
        $url .= http_build_query( $parameter ) . '#wechat_redirect';
        return $url;
    }

    private function getAccessToken()
    {
        $parameter = array(
            'appid' => $this->oauth_array[ 'appid' ],
            'secret' => $this->oauth_array[ 'appsecret' ],
            'code' => $this->code,
            'grant_type' => 'authorization_code'
        );
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query( $parameter );
        $access_token_json = Functions::curl_file_get_contents( $url, 30, $ssl = true );
        $access_token_result = json_decode( $access_token_json );
        if ( !$access_token_result ) {
            throw new TmacClassException( '用户授权出错' );
        }
        if ( !empty( $access_token_result->errcode ) ) {
            throw new TmacClassException( $access_token_result->errcode . '|' . $access_token_result->errmsg );
        }
        return $access_token_result;
    }

    /**
     * 更新用户绑定的openid
     * @return type
     */
    public function handle()
    {
        $access_token_result = $this->getAccessToken();
        $userinfo = $this->getUserInfo( $access_token_result );
        return $this->openidBind( $userinfo );
    }

    /**
     * 微信授权注册
     */
    public function openidBind( $userinfo )
    {
        $weixin_member_info = $userinfo;
        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->openid = $weixin_member_info->openid;
        $entity_MemberSetting_base->nickname = $weixin_member_info->nickname;
        $entity_MemberSetting_base->avatar_imgurl = $weixin_member_info->headimgurl;
        $entity_MemberSetting_base->settle_status = service_Member_base::settle_status_success;

        $dao = dao_factory_base::getMemberSettingDao();
        $dao->getDb()->startTrans();

        $dao->setWhere( "uid={$this->memberInfo->uid}" );
        $dao->updateByWhere( $entity_MemberSetting_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
        //注册的时候判断有没有推荐人,如果有写上
    }

    /**
     * 取第三方用户信息
     * @param type $entity_MemberOauth_base
     * @return boolean
     */
    public function getUserInfo( $access_token_result )
    {
        $parameter = array(
            'access_token' => $access_token_result->access_token,
            'openid' => $access_token_result->openid,
            'lang' => 'zh_CN'
        );
        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query( $parameter );
        $userinfo_json = Functions::curl_file_get_contents( $url, 30, $ssl = true );
        $userinfo_result = json_decode( $userinfo_json );
        if ( !$userinfo_result ) {
            throw new TmacClassException( '取用户数据失败' );
        }
        if ( !empty( $userinfo_result->errcode ) ) {
            throw new TmacClassException( $userinfo_result->errcode . '|' . $userinfo_result->errmsg );
        }
        return $userinfo_result;
    }

}
