<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_oauth_Weixin_base extends service_Model_base
{

    protected $code;
    protected $state;
    protected $redirect_uri;
    protected $login_status;
    protected $errorMessage;
    protected $oauth_array;
    protected $scope = 'snsapi_base';

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
        $this->oauth_array = Tmac::config( 'oauth.oauth.wechat', APP_WWW_NAME );
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
     * 根据code取access_token
     * @param type $code
     * $this->code;     
     * $this->getAccessToken();
     */
    public function handle()
    {
        $access_token_result = $this->getAccessToken();
        $openid = $access_token_result->openid;
        //判断openid在平台是否注册过
        $member_oauth_info = $this->checkOpenidRegister( $openid );

        $login_model = new service_account_Login_mobile();
        if ( $member_oauth_info ) {
            //直接登录验证成功
            $login_model->setUid( $member_oauth_info->uid );
            $member_info = $login_model->doLoginInfo();
            return true;
        } else {
            //调用snsapi_userinfo进行注册
            return false;
        }
    }

    public function handleRegister()
    {
        $access_token_result = $this->getAccessToken();
        $userinfo = $this->getUserInfo( $access_token_result );
        return $this->weixinRegister( $access_token_result, $userinfo );
    }

    /**
     * 微信授权注册
     */
    public function weixinRegister( $access_token_result, $userinfo )
    {
        //判断是否已经注册过
        $register_status = $this->checkOpenidRegister( $userinfo->openid );
        if ( $register_status == true ) {
            return true;
        }
        $agent_uid = 0;

        $weixin_member_info = $userinfo;
        // 开始存储事务
        // member表
        $register_model = new service_account_Register_base();
        $member_image_id = $register_model->getMemberImageIdFromURL( $weixin_member_info->headimgurl );
        if ( $member_image_id == false ) {
            $member_image_id = '';
        }
        //开始注册用户        
        $entity_member = new entity_Member_base ();
        //MD5(pass+salt)
        $entity_member->nickname = $weixin_member_info->nickname;
        $entity_member->password = md5( rand( 10000000, 990000000 ) );
        $entity_member->mobile = '';
        $entity_member->email = '';
        $entity_member->member_type = service_Member_base::member_type_buyer;
        $entity_member->member_class = 0;
        $entity_member->member_image_id = $member_image_id;
        $entity_member->reg_time = $this->now;
        $entity_member->salt = rand( 100000, 999999 );
        $entity_member->last_login_time = $this->now;
        $entity_member->last_login_ip = Functions::get_client_ip();
        $entity_member->login_fail_count = 0;
        $entity_member->agent_uid = $agent_uid;
        $entity_member->register_source = service_Account_base::register_source_wechat;
        $entity_member->sex = $weixin_member_info->sex;
        $address_info = array(
            'country' => $weixin_member_info->country,
            'province' => $weixin_member_info->province,
            'city' => $weixin_member_info->city
        );
        $entity_member->address_info = serialize( $address_info );

        $dao = dao_factory_base::getMemberDao();
        $dao->getDb()->startTrans();

        $uid = $dao->insert( $entity_member );

        $spec_map_dao = dao_factory_base::getSpecMapDao();
        $spec_map_dao->createMemberSpecMap( $uid );

        //会员设置表插入记录
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->uid = $uid;
        $entity_MemberSetting_base->shop_name = '银品惠_' . date( 'YmdHis' ) . rand( 10000, 99999 );
        $entity_MemberSetting_base->member_type = service_Member_base::member_type_buyer;
        $entity_MemberSetting_base->reg_time = $this->now;
        $member_setting_dao->insert( $entity_MemberSetting_base );

        //member_oauth表
        $entity_MemberOauth_base = new entity_MemberOauth_base();
        $entity_MemberOauth_base->uid = $uid;
        $entity_MemberOauth_base->oauth_type = service_Oauth_base::oauth_type_wechat;
        $entity_MemberOauth_base->openid = $weixin_member_info->openid;
        $entity_MemberOauth_base->unionid = isset( $weixin_member_info->unionid ) ? $weixin_member_info->unionid : '';
        $entity_MemberOauth_base->access_token = $access_token_result->access_token;
        $entity_MemberOauth_base->expires_in = $access_token_result->expires_in;
        $entity_MemberOauth_base->refresh_token = $access_token_result->refresh_token;
        $entity_MemberOauth_base->nickname = $weixin_member_info->nickname;
        $entity_MemberOauth_base->avatar_imgurl = $weixin_member_info->headimgurl;
        $entity_MemberOauth_base->oauth_time = $this->now;
        $member_oauth_dao = dao_factory_base::getMemberOauthDao();
        $member_oauth_dao->insert( $entity_MemberOauth_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $entity_member->uid = $uid;
            $login_model = new service_account_Login_base();
            $expire = 1;
            //更新cookie
            $login_model->updateMemberCookieCheck( $entity_member, $expire );
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
        //注册的时候判断有没有推荐人,如果有写上
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
            throw new TmacClassException( '取用户数据失败' );
        }
        if ( !empty( $userinfo_result->errcode ) ) {
            throw new TmacClassException( $userinfo_result->errcode . '|' . $userinfo_result->errmsg );
        }
        return $userinfo_result;
    }

    /**
     * 检测openid在平台中是否注册过
     * @param type $openid
     */
    private function checkOpenidRegister( $openid )
    {
        $dao = dao_factory_base::getMemberOauthDao();
        $dao->setField( 'uid' );
        $where = 'oauth_type=' . service_Oauth_base::oauth_type_wechat . " AND openid='{$openid}'";
        $dao->setWhere( $where );
        $member_oauth_info = $dao->getInfoByWhere();
        return $member_oauth_info;
    }

}
