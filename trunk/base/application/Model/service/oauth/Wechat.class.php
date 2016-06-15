<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_oauth_Wechat_base extends service_Oauth_base implements service_oauth_Interface_base
{

    protected $oauth_array;

    public function __construct()
    {
        parent::__construct();
        $this->oauth_array = Tmac::config( 'oauth.oauth.wechat_open_app', APP_WWW_NAME );
    }

    /**
     * 返回第三方Oauth授权的URL
     * @return string
     */
    public function getAuthorizeUrl()
    {
        switch ( $this->display )
        {
            case service_Oauth_base::display_web:
                $this->oauth_array = Tmac::config( 'oauth.oauth.wechat_open_web', APP_WWW_NAME );
                $scope = 'snsapi_login';
                $url = 'https://open.weixin.qq.com/connect/qrconnect?';
                $redirect_uri = MOBILE_URL . 'oauth/wechat?display=web';
                break;



            case service_Oauth_base::display_mobile:
                $this->oauth_array = Tmac::config( 'oauth.oauth.wechat_open_app', APP_WWW_NAME );
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
                $scope = 'snsapi_userinfo';
                $redirect_uri = MOBILE_URL . 'oauth/wechat';
                break;

            case service_Oauth_base::display_weixin:
            default:
                $this->oauth_array = Tmac::config( 'oauth.oauth.wechat', APP_WWW_NAME );
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
                $scope = 'snsapi_userinfo';
                $redirect_uri = MOBILE_URL . 'oauth/wechat?display=weixin';
                break;
        }
        //location header
        $parameter = array(
            'appid' => $this->oauth_array[ 'appid' ],
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => session_id()
        );
        $url .= http_build_query( $parameter ) . '#wechat_redirect';
        return $url;
    }

    /**
     * 根据code取access_token
     * @param type $code
     * $this->code;     
     * $this->getAccessToken();
     */
    public function handle()
    {
        if ( parent::checkState() === false ) {
            return false;
        }
        switch ( $this->display )
        {
            case service_Oauth_base::display_web:
                $this->oauth_array = Tmac::config( 'oauth.oauth.wechat_open_web', APP_WWW_NAME );
                break;


            case service_Oauth_base::display_mobile:
                $this->oauth_array = Tmac::config( 'oauth.oauth.wechat_open_app', APP_WWW_NAME );
                break;
            case service_Oauth_base::display_weixin:
            default:
                $this->oauth_array = Tmac::config( 'oauth.oauth.wechat', APP_WWW_NAME );
                break;
        }
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
            $this->errorMessage = '用户授权出错';
            return false;
        }
        if ( !empty( $access_token_result->errcode ) ) {
            $this->errorMessage = $access_token_result->errcode . '|' . $access_token_result->errmsg;
            return false;
        }
        $userinfo = $this->getUserInfo( $access_token_result );
        if ( $userinfo == false ) {
            $nickname = $avator_imgurl = '';
        } else {
            $nickname = addslashes( $userinfo->nickname );
            $avator_imgurl = $userinfo->headimgurl;
        }
        $member_oauth_info = $this->checkMemberOauthExist( $access_token_result );
        if ( $member_oauth_info == false ) {
            $entity_MemberOauth_base = new entity_MemberOauth_base();
            $entity_MemberOauth_base->uid = 0;
            $entity_MemberOauth_base->oauth_type = service_Oauth_base::oauth_type_wechat;
            $entity_MemberOauth_base->openid = $access_token_result->openid;
            $entity_MemberOauth_base->unionid = isset( $access_token_result->unionid ) ? $access_token_result->unionid : '';
            $entity_MemberOauth_base->access_token = $access_token_result->access_token;
            $entity_MemberOauth_base->expires_in = $this->now + $access_token_result->expires_in;
            $entity_MemberOauth_base->refresh_token = $access_token_result->refresh_token;
            $entity_MemberOauth_base->nickname = $nickname;
            $entity_MemberOauth_base->avatar_imgurl = $avator_imgurl;
            $entity_MemberOauth_base->oauth_time = $this->now;
            $oauth_id = $result = parent::insertMemberOauth( $entity_MemberOauth_base );
            $entity_MemberOauth_base->id = $oauth_id;
        } else {//已经存在用户在微信平台上的关联，更新一下access_token 授权数据
            $entity_MemberOauth_base = new entity_MemberOauth_base();
            $entity_MemberOauth_base->id = $member_oauth_info->id;
            $entity_MemberOauth_base->access_token = $access_token_result->access_token;
            $entity_MemberOauth_base->expires_in = $this->now + $access_token_result->expires_in;
            $entity_MemberOauth_base->refresh_token = $access_token_result->refresh_token;
            $entity_MemberOauth_base->nickname = $nickname;
            $entity_MemberOauth_base->avatar_imgurl = $avator_imgurl;
            $entity_MemberOauth_base->oauth_time = $this->now;
            $result = parent::updateMemberOauthById( $entity_MemberOauth_base );
            $entity_MemberOauth_base->openid = $access_token_result->openid;
            if ( !empty( $member_oauth_info->uid ) ) {
                //对这上用户进行登录操作
                $login_model = new service_account_Login_base();
                $member_info = $login_model->getMemberInfoById( $member_oauth_info->uid );
                $expire = 1;

                //更新登录成功后member表的数据
                $login_model->modifyMemberLoginInfo( $member_oauth_info->uid );
                //更新cookie
                $login_model->updateMemberCookieCheck( $member_info, $expire );
                $this->login_status = true;
            }
        }
        //把oauth数据种在cookie中
        parent::setOauthCookie( $entity_MemberOauth_base );
        return $entity_MemberOauth_base;
    }

    private function checkMemberOauthExist( $access_token_result )
    {
        $dao = dao_factory_base::getMemberOauthDao();
        //有多个应用 使用了open.weixin.qq.com的时候 做了多应用绑定的时候.需要用unionid来确认多应用间.比如app和web登录中的unionid. 如果只有一个appid,就只需要使用openid
        $where = 'oauth_type=' . service_Oauth_base::oauth_type_wechat . " AND openid='{$access_token_result->openid}'";
        $dao->setWhere( $where );
        $res = $dao->getInfoByWhere();
        return $res;
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
            $this->errorMessage = '取用户数据失败';
            return false;
        }
        if ( !empty( $userinfo_result->errcode ) ) {
            $this->errorMessage = $userinfo_result->errcode . '|' . $userinfo_result->errmsg;
            return false;
        }
        return $userinfo_result;
    }

}
