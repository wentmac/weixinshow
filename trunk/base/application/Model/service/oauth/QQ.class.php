<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_oauth_QQ_base extends service_Oauth_base implements service_oauth_Interface_base
{

    /**
     * PC网站： https://graph.qq.com/oauth2.0/authorize
     * WAP网站： https://graph.z.qq.com/moc2/authorize
     * @var type 
     */
    protected $api_url = 'https://graph.qq.com/oauth2.0/';
    protected $oauth_array;

    function setApi_url( $api_url )
    {
        $this->api_url = $api_url;
    }

    public function __construct()
    {
        parent::__construct();
        $this->oauth_array = Tmac::config( 'oauth.oauth.qq', APP_WWW_NAME );
    }

    /**
     * 返回第三方Oauth授权的URL
     * @return string
     */
    public function getAuthorizeUrl()
    {
//location header
        $parameter = array(
            'client_id' => $this->oauth_array[ 'appid' ],
            'redirect_uri' => MOBILE_URL . 'oauth/qq?display=' . $this->display,
            'scope' => 'get_user_info,get_simple_userinfo,add_t',
            'state' => session_id(),
            'response_type' => 'code'
        );
        switch ( $this->display )
        {
            case service_Oauth_base::display_web:
                break;

            default:
            case service_Oauth_base::display_mobile:
                $parameter[ 'display' ] = 'mobile';
                break;
        }
        $url = $this->api_url . 'authorize?' . http_build_query( $parameter );
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
            'client_id' => $this->oauth_array[ 'appid' ],
            'client_secret' => $this->oauth_array[ 'appkey' ],
            'code' => $this->code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => MOBILE_URL . 'oauth/qq?display=' . $this->display
        );
        $url = $this->api_url . 'token?' . http_build_query( $parameter );
        $access_token_json = Functions::curl_file_get_contents( $url, 30, $ssl = true );
        $response = $access_token_json;
        if ( strpos( $response, "callback" ) !== false ) {
            $lpos = strpos( $response, "(" );
            $rpos = strrpos( $response, ")" );
            $response = substr( $response, $lpos + 1, $rpos - $lpos - 1 );
            $msg = json_decode( $response );
            if ( isset( $msg->error ) ) {
                $this->errorMessage = $msg->error . '|' . $msg->error_description;
                return false;
            }
        }

        $params = array();
        parse_str( $response, $params );

        $openid = $this->getOpenid( $params );
        if ( $openid == false ) {
            return false;
        }

        $userinfo = $this->getUserInfo( $params, $openid );
        if ( $userinfo == false ) {
            return false;
        } else {
            $nickname = addslashes( $userinfo->nickname );
            $avator_imgurl = $userinfo->figureurl_qq_2;
        }
        $member_oauth_info = $this->checkMemberOauthExist( $openid );
        if ( $member_oauth_info == false ) {
            $entity_MemberOauth_base = new entity_MemberOauth_base();
            $entity_MemberOauth_base->uid = 0;
            $entity_MemberOauth_base->oauth_type = service_Oauth_base::oauth_type_qq;
            $entity_MemberOauth_base->openid = $openid;
            $entity_MemberOauth_base->unionid = '';
            $entity_MemberOauth_base->access_token = $params[ 'access_token' ];
            $entity_MemberOauth_base->expires_in = $this->now + $params[ 'expires_in' ];
            $entity_MemberOauth_base->refresh_token = $params[ 'refresh_token' ];
            $entity_MemberOauth_base->nickname = $nickname;
            $entity_MemberOauth_base->avatar_imgurl = $avator_imgurl;
            $entity_MemberOauth_base->oauth_time = $this->now;
            $oauth_id = $result = parent::insertMemberOauth( $entity_MemberOauth_base );
            $entity_MemberOauth_base->id = $oauth_id;
        } else {//已经存在用户在微信平台上的关联，更新一下access_token 授权数据
            $entity_MemberOauth_base = new entity_MemberOauth_base();
            $entity_MemberOauth_base->id = $member_oauth_info->id;
            $entity_MemberOauth_base->access_token = $params[ 'access_token' ];
            $entity_MemberOauth_base->expires_in = $this->now + $params[ 'expires_in' ];
            $entity_MemberOauth_base->refresh_token = $params[ 'refresh_token' ];
            $entity_MemberOauth_base->nickname = $nickname;
            $entity_MemberOauth_base->avatar_imgurl = $avator_imgurl;
            $entity_MemberOauth_base->oauth_time = $this->now;
            $result = parent::updateMemberOauthById( $entity_MemberOauth_base );
            $entity_MemberOauth_base->openid = $openid;
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

    private function checkMemberOauthExist( $openid )
    {
        $dao = dao_factory_base::getMemberOauthDao();
        $where = 'oauth_type=' . service_Oauth_base::oauth_type_qq . " AND openid='{$openid}'";
        $dao->setWhere( $where );
        $res = $dao->getInfoByWhere();
        return $res;
    }

    /**
     * 取第三方用户信息
     * @param type $entity_MemberOauth_base
     * @return boolean
     */
    public function getUserInfo( $access_token_result, $openid )
    {

        $url = "https://graph.qq.com/user/get_user_info?"
                . "access_token=" . $access_token_result[ 'access_token' ]
                . "&oauth_consumer_key=" . $this->oauth_array[ 'appid' ]
                . "&openid=" . $openid
                . "&format=json";

        $info = Functions::curl_file_get_contents( $url, 30, $ssl = true );
        $userinfo_result = json_decode( $info );
        if ( !$userinfo_result ) {
            $this->errorMessage = '取用户数据失败';
            return false;
        }
        if ( $userinfo_result->ret <> 0 ) {
            $this->errorMessage = $userinfo_result->msg;
            return false;
        }
        return $userinfo_result;
    }

    protected function getOpenid( $access_token_result )
    {
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
                . $access_token_result[ 'access_token' ];

        $str = file_get_contents( $graph_url );
        if ( strpos( $str, "callback" ) !== false ) {
            $lpos = strpos( $str, "(" );
            $rpos = strrpos( $str, ")" );
            $str = substr( $str, $lpos + 1, $rpos - $lpos - 1 );
        }

        $user = json_decode( $str );
        if ( isset( $user->error ) ) {
            $this->errorMessage = $user->error . '|' . $user->error_description;
            return false;
        }

        return $user->openid;
    }

}
