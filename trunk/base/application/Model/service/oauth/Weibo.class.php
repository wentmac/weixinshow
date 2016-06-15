<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_oauth_Weibo_base extends service_Oauth_base implements service_oauth_Interface_base
{

    protected $oauth_array;

    public function __construct()
    {
        parent::__construct();
        $this->oauth_array = Tmac::config( 'oauth.oauth.weibo', APP_WWW_NAME );
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
                $display = 'default';
                $api_url = 'https://api.weibo.com/';
                break;

            default:
            case service_Oauth_base::display_mobile:
                $display = 'mobile';
                $api_url = 'https://open.weibo.cn/';
                break;
        }
        //location header
        $parameter = array(
            'client_id' => $this->oauth_array[ 'appkey' ],
            'redirect_uri' => MOBILE_URL . 'oauth/weibo',
            'Scope' => 'all',
            'state' => session_id(),
            'display' => $display
        );
        $url = $api_url . 'oauth2/authorize?' . http_build_query( $parameter );
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
        $parameter = array(
            'client_id' => $this->oauth_array[ 'appkey' ],
            'client_secret' => $this->oauth_array[ 'appsecret' ],
            'code' => $this->code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => MOBILE_URL . 'oauth/weibo'
        );
        $url = 'https://api.weibo.com/oauth2/access_token?' . http_build_query( $parameter );
        $access_token_json = Functions::curl_post_contents( $url, array(), 30, $ssl = true );
        $access_token_result = json_decode( $access_token_json );
        if ( !$access_token_result ) {
            $this->errorMessage = '用户授权出错';
            return false;
        }
        if ( !empty( $access_token_result->error ) ) {
            $this->errorMessage = $access_token_result->error . '|' . $access_token_result->error_code;
            return false;
        }
        $userinfo = $this->getUserInfo( $access_token_result );
        if ( $userinfo == false ) {
            $this->errorMessage = '获取微博用户信息失败';
            return false;
        } else {
            $nickname = addslashes( $userinfo->screen_name );
            $avator_imgurl = $userinfo->avatar_large;
        }
        $member_oauth_info = $this->checkMemberOauthExist( $userinfo->id );
        if ( $member_oauth_info == false ) {
            $entity_MemberOauth_base = new entity_MemberOauth_base();
            $entity_MemberOauth_base->uid = 0;
            $entity_MemberOauth_base->oauth_type = service_Oauth_base::oauth_type_weibo;
            $entity_MemberOauth_base->openid = $userinfo->id;
            $entity_MemberOauth_base->unionid = '';
            $entity_MemberOauth_base->access_token = $access_token_result->access_token;
            $entity_MemberOauth_base->expires_in = $this->now + $access_token_result->expires_in;
            $entity_MemberOauth_base->refresh_token = $access_token_result->access_token;
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
            $entity_MemberOauth_base->refresh_token = $access_token_result->access_token;
            $entity_MemberOauth_base->nickname = $nickname;
            $entity_MemberOauth_base->avatar_imgurl = $avator_imgurl;
            $entity_MemberOauth_base->oauth_time = $this->now;
            $result = parent::updateMemberOauthById( $entity_MemberOauth_base );
            $entity_MemberOauth_base->openid = $userinfo->id;
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

    private function checkMemberOauthExist( $uid )
    {
        $dao = dao_factory_base::getMemberOauthDao();
        $where = 'oauth_type=' . service_Oauth_base::oauth_type_weibo . " AND openid='{$uid}'";
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
            'source' => $this->oauth_array[ 'appkey' ],
            'access_token' => $access_token_result->access_token,
            'uid' => $access_token_result->uid
        );
        $url = 'https://api.weibo.com/2/users/show.json?' . http_build_query( $parameter );
        $userinfo_json = Functions::curl_file_get_contents( $url, 30, $ssl = true );
        $userinfo_result = json_decode( $userinfo_json );
        if ( !$userinfo_result ) {
            $this->errorMessage = '取用户数据失败';
            return false;
        }
        if ( !empty( $userinfo_result->error_code ) ) {
            $this->errorMessage = $userinfo_result->error_code . '|' . $userinfo_result->error;
            return false;
        }
        return $userinfo_result;
    }

}
