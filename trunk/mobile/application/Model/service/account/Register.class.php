<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_account_Register_mobile extends service_account_Register_base
{

    private $openid;
    private $eventKey;
    private $oauth_array;
    private $access_token;

    function setOpenid( $openid )
    {
        $this->openid = $openid;
    }

    function setEventKey( $eventKey )
    {
        $this->eventKey = $eventKey;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 更新access_token之类 数据
     * @param type $uid
     */
    public function updateMemberOauthAvatar( $uid )
    {
        $member_oauth_dao = dao_factory_base::getMemberOauthDao();
        $where = "uid={$uid} AND oauth_type=" . service_Oauth_base::oauth_type_wechat;
        $member_oauth_dao->setWhere( $where );
        $member_oauth_info = $member_oauth_dao->getInfoByWhere();
        if ( empty( $member_oauth_info ) ) {
            return true;
        }
        $this->openid = $member_oauth_info->openid;
        $weixin_member_info = $this->getMemberInfoFromWeixin();

        $member_image_id = $this->getMemberImageIdFromURL( $weixin_member_info->headimgurl );
        if ( $member_image_id == false ) {
            $member_image_id = '';
        }
        $nickname = $weixin_member_info->nickname;

        $dao = dao_factory_base::getMemberDao();
        $dao->getDb()->startTrans();

        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->member_image_id = $member_image_id;
        if ( !empty( $nickname ) ) {
            $entity_Member_base->nickname = $nickname;
        }
        $dao->setPk( $uid );
        $dao->updateByPk( $entity_Member_base );

        //member_oauth表        
        $entity_MemberOauth_base = new entity_MemberOauth_base();
        $entity_MemberOauth_base->avatar_imgurl = $weixin_member_info->headimgurl;
        $member_oauth_dao->updateByWhere( $entity_MemberOauth_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return $weixin_member_info->headimgurl;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 公众号关注时注册
     * $this->openid;
     * $this->eventKey;
     * $this->mpRegister();
     */
    public function mpRegister()
    {
        //判断是否已经注册过
        $register_status = $this->isOpenidRegister();
        if ( $register_status == true ) {
            return true;
        }
        //如果没有就执行注册              
        //取用户信息
        $weixin_member_info = $this->getMemberInfoFromWeixin();

        $agent_uid = 0;
        $sum_integral = 0; //历史总积分
        if ( !empty( $this->eventKey ) && substr( $this->eventKey, 0, 8 ) === 'qrscene_' ) {
            $agent_id = str_replace( 'qrscene_', '', $this->eventKey );
            $agent_info = $this->getUidByUid( $agent_id );
            $agent_uid = $agent_info[ 'agent_uid' ];
            $sum_integral = $agent_info[ 'sum_integral' ]; //历史总积分
        }
        $member_image_id = $this->getMemberImageIdFromURL( $weixin_member_info->headimgurl );
        if ( $member_image_id == false ) {
            $member_image_id = '';
        }
        // 开始存储事务
        // member表
        //开始注册用户        
        $entity_member = new entity_Member_base ();
        //MD5(pass+salt)
        $entity_member->nickname = empty( $weixin_member_info->nickname ) ? '银品惠_' . date( 'YmdHis' ) . rand( 10000, 99999 ) : $weixin_member_info->nickname;
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
        $entity_member->available_integral = service_Member_base::agent_register_integral_value;
        $entity_member->sum_integral = $entity_member->available_integral;
        $entity_member->agent_integral = service_Member_base::agent_integral_value; //东家应得1分

        $dao = dao_factory_base::getMemberDao();
        $dao->getDb()->startTrans();

        $uid = $dao->insert( $entity_member );

        //更新东家的积分
        $this->updateAgentIntegral( $agent_uid, $sum_integral );

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
        $entity_MemberOauth_base->access_token = '';
        $entity_MemberOauth_base->expires_in = 0;
        $entity_MemberOauth_base->refresh_token = '';
        $entity_MemberOauth_base->nickname = $weixin_member_info->nickname;
        $entity_MemberOauth_base->avatar_imgurl = $weixin_member_info->headimgurl;
        $entity_MemberOauth_base->oauth_time = $this->now;
        $member_oauth_dao = dao_factory_base::getMemberOauthDao();
        $member_oauth_dao->insert( $entity_MemberOauth_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $entity_member->uid = $uid;
            parent::updateMemberCookieCheck( $entity_member, 1 );
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
        //注册的时候判断有没有推荐人,如果有写上
    }

    /**
     * 更新东家的积分
     * @param type $sum_integral
     */
    private function updateAgentIntegral( $agent_uid, $sum_integral )
    {
        $check_today_integral = $this->checkAgentTodayIntegral( $agent_uid );
        if ( $check_today_integral == false ) {
            return FALSE;
        }
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->sum_integral = new TmacDbExpr( 'sum_integral+' . service_Member_base::agent_integral_value );
        //东家的积分还没超标
        if ( $sum_integral < service_Member_base::agent_integral_max_value ) {
            $entity_Member_base->available_integral = new TmacDbExpr( 'available_integral+' . service_Member_base::agent_integral_value );
        }
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $agent_uid );
        return $dao->updateByPk( $entity_Member_base );
    }

    /**
     * 检测今天有没有超过日限制积分
     * @param type $uid
     */
    private function checkAgentTodayIntegral( $agent_uid )
    {
        $today_start = strtotime( date( 'Y-m-d' ) );        
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'SUM(agent_integral) AS today_integral' );
        $where = "agent_uid={$agent_uid} AND reg_time>=$today_start";
        $dao->setWhere( $where );
        $memberInfo = $dao->getInfoByWhere();
        if ( $memberInfo->today_integral >= service_Member_base::agent_integral_day_max_value ) {
            return false;
        }
        return true;
    }

    private function getMemberInfoFromWeixin( $update = false )
    {

        $this->oauth_array = Tmac::config( 'oauth.oauth.wechat', APP_WWW_NAME );
        $weixin_token_model = new service_utils_WeixinToken_base();
        $weixin_token_model->setAppid( $this->oauth_array[ 'appid' ] );
        $weixin_token_model->setSecret( $this->oauth_array[ 'appsecret' ] );
        if ( $update ) {
            $weixin_token_model->setExpired_time( 0 );
        }
        try {
            $this->access_token = $weixin_token_model->getAccessToken();
        } catch (TmacClassException $exc) {
            throw new TmacClassException( $exc->getMessage() );
        }

        $userinfo = $this->getUserInfo();
        return $userinfo;
    }

    /**
     * 取第三方用户信息
     * @param type $entity_MemberOauth_base
     * @return boolean
     */
    private function getUserInfo()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->access_token . '&openid=' . $this->openid . '&lang=zh_CN';
        $userinfo_json = Functions::curl_file_get_contents( $url, 30, $ssl = true );
        $userinfo_result = json_decode( $userinfo_json );
        if ( !empty( $userinfo_result->errcode ) ) {
            $access_token_error_array = array( '40001', '40014', '41001', '42001' );
            if ( in_array( $userinfo_result->errcode, $access_token_error_array ) ) {
                return $this->getMemberInfoFromWeixin( true );
            }
            throw new TmacClassException( $userinfo_result->errcode . '|' . $userinfo_result->errmsg );
        }
        if ( !$userinfo_result ) {
            throw new TmacClassException( '取用户数据失败' );
        }

        return $userinfo_result;
    }

    private function isOpenidRegister()
    {
        $dao = dao_factory_base::getMemberOauthDao();
        $where = "oauth_type=" . service_Oauth_base::oauth_type_wechat . " AND openid='{$this->openid}'";
        $dao->setWhere( $where );
        $member_oauth_info = $dao->getInfoByWhere();
        if ( !$member_oauth_info ) {
            return false;
        }
        //如果注册过 种下验证cookie
        $member_dao = dao_factory_base::getMemberDao();
        $member_dao->setPk( $member_oauth_info->uid );
        $member_info = $member_dao->getInfoByPk();
        parent::updateMemberCookieCheck( $member_info, 1 );
        return TRUE;
    }

}
