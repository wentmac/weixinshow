<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_account_Register_base extends service_Account_base
{

    protected $mobile;
    protected $username;
    protected $realname;
    protected $sms_captcha;
    protected $password;
    protected $sms_type;
    protected $verify_code;
    protected $register_source = 0;
    protected $need_password = true;
    protected $agent_mobile;

    function setMobile( $mobile )
    {
        $this->mobile = $mobile;
    }

    function setUsername( $username )
    {
        $this->username = $username;
    }

    function setRealname( $realname )
    {
        $this->realname = $realname;
    }

    function setSms_captcha( $sms_captcha )
    {
        $this->sms_captcha = $sms_captcha;
    }

    function setPassword( $password )
    {
        $this->password = $password;
    }

    function setSms_type( $sms_type )
    {
        $this->sms_type = $sms_type;
    }

    function setVerify_code( $verify_code )
    {
        $this->verify_code = $verify_code;
    }

    function setRegister_source( $register_source )
    {
        $this->register_source = $register_source;
    }

    /**
     * 是否需要密码 如果不需要密码是系统内自动注册
     * @param type $no_password
     */
    function setNeed_password( $need_password )
    {
        $this->need_password = $need_password;
    }

    function setAgent_mobile( $agent_mobile )
    {
        $this->agent_mobile = $agent_mobile;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测mobile是否已经存在
     * @return boolean
     */
    public function checkMobileRepeat()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,password,username,realname,member_type' );
        $dao->setWhere( "mobile = '{$this->mobile}'" );

        $res = $dao->getInfoByWhere();
        if ( $res && count( $res ) > 0 ) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * 检测username是否已经存在
     * @return boolean
     */
    public function checkUsernameRepeat()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,password,username,member_type' );
        $dao->setWhere( "username = '{$this->username}'" );

        $res = $dao->getInfoByWhere();
        if ( $res && count( $res ) > 0 ) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * 说明：检查手机验证码是否正确
     * @param unknown_type $mobile
     * @param unknown_type $code
     * @param unknown_type $type
     */
    public function checkSmsCode()
    {
        //检查短信发送记录中有没有1小时内的发送记录
        $today_start = $this->now - (self::sms_type_effective_time * 60);
        $today_end = $this->now;
        $dao = dao_factory_base::getSmsLogDao();

        $dao->getDb()->startTrans();

        $dao->setWhere( "sms_type={$this->sms_type} AND sms_mobile='{$this->mobile}' AND sms_time>={$today_start} AND sms_time<={$today_end}" );
        $dao->setOrderby( 'sms_id DESC' );
        $dao->setField( 'sms_id,sms_code,fail_count' );
        $sms_code_info = $dao->getInfoByWhere();
        if ( empty( $sms_code_info ) ) {
            $this->errorMessage = '没有找到匹配的的短信验证码';
            return false;
        }

        if ( $this->sms_captcha <> $sms_code_info->sms_code ) {
            //更新失败次数
            $dao->setPk( $sms_code_info->sms_id );
            $entity_SmsLog_base = new entity_SmsLog_base();
            $entity_SmsLog_base->fail_count = new TmacDbExpr( 'fail_count+1' );
            $dao->updateByPk( $entity_SmsLog_base );
            $this->errorMessage = '短信验证码不正确';
            return false;
        }
        if ( $sms_code_info->fail_count > self::MAX_SMS_CODE_FAILED_COUNT ) {
            $this->errorMessage = '本次验证码失败次数已达' . self::MAX_SMS_CODE_FAILED_COUNT . '次，请重新获取新的验证码';
            return false;
        }

        //更新成功状态
        if ( !empty( $this->password ) ) {
            $dao->setPk( $sms_code_info->sms_id );
            $entity_SmsLog_base = new entity_SmsLog_base();
            $entity_SmsLog_base->fail_count = self::MAX_SMS_CODE_FAILED_COUNT + 1;
            $dao->updateByPk( $entity_SmsLog_base );
        }

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 说明:写入新用户
     * @author zhangwentao
     * @param object $entity_member_parameter
     */
    public function createMember()
    {
        // 手机号重复校验
        $checkMobiel = $this->checkMobileRepeat();
        if ( $checkMobiel == true ) {
            $this->errorMessage = '您手机号' . $this->mobile . '已经注册过，请直接登录';
            return false;
        }
        // 用户名重复校验
        /**
          $checkUsername = $this->checkUsernameRepeat();
          if ( $checkUsername == true ) {
          $this->errorMessage = '用户名"' . $this->username . '"重复';
          return false;
          }
         * 
         */
        /**
          //检验图片验证码
          $verify_code_result = parent::checkVerifyCode( $this->verify_code );
          if ( $verify_code_result == false ) {
          return false;
          }
         * 
         */
        // 短信验证码校验
        if ( $this->need_password == true ) {
            $this->sms_type = self::sms_type_register;
            $checkSmsCode = $this->checkSmsCode();
            if ( $checkSmsCode == false ) {
                //errorMessage已经抛出
                return false;
            }
        }
        /**
          $url = isset( $_SERVER [ 'HTTP_REFERER' ] ) ? filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING ) : '';
          if ( $url != "" ) {
          if ( strpos( $url, INDEX_URL ) !== 0 || strpos( $url, MOBILE_URL ) !== 0 ) {
          $this->errorMessage = '非法注册来源';
          return false;
          }
          }
         * 
         */
        $agent_uid = 0;
        if ( !empty( $this->agent_mobile ) ) {
            $agent_uid = $this->getUidByMobile( $this->agent_mobile );
        }
        // 开始存储事务
        // member表
        //开始注册用户        
        $entity_member = new entity_Member_base ();
        $entity_member->username = $this->username;
        $entity_member->realname = $this->realname;
        //MD5(pass+salt)
        $entity_member->password = md5( $this->password . $this->sms_captcha ); //hash( hash( password ) + salt )
        $entity_member->mobile = $this->mobile;
        $entity_member->email = '';
        $entity_member->member_type = service_Member_base::member_type_buyer;
        $entity_member->member_class = 0;
        $entity_member->member_image_id = '';
        $entity_member->reg_time = $this->now;
        $entity_member->salt = $this->sms_captcha;
        $entity_member->last_login_time = $this->now;
        $entity_member->last_login_ip = Functions::get_client_ip();
        $entity_member->login_fail_count = 0;
        $entity_member->agent_uid = $agent_uid;

        $dao = dao_factory_base::getMemberDao();
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

        if ( $uid ) {
            //写登录cookie
            $entity_member->uid = $uid;
            $this->isApi || parent::updateMemberCookieCheck( $entity_member, 1 );
            return $entity_member;
        } else {
            $this->errorMessage = '注册失败，请重试或联系管理员';
            return false;
        }
    }

    /**
     * 取短信发送次数
     * @return type
     */
    private function getSmsSendCountByMobile( $sms_type )
    {
        $today_start = strtotime( date( 'Y-m-d' ) );
        $today_end = $today_start + 86400;
        $dao = dao_factory_base::getSmsLogDao();
        $dao->setWhere( "sms_type={$sms_type} AND sms_mobile='{$this->mobile}' AND sms_time>={$today_start} AND sms_time<={$today_end}" );
        return $dao->getCountByWhere();
    }

    /**
     * 判断上一次短信发送时间是否合法
     * @return type
     */
    private function checkSmsLastTime()
    {
        $dao = dao_factory_base::getSmsLogDao();
        $dao->setWhere( "sms_type={$this->sms_type} AND sms_mobile='{$this->mobile}'" );
        $dao->setField( 'sms_time' );
        $dao->setLimit( 1 );
        $dao->setOrderby( 'sms_id DESC' );

        $sms_info = $dao->getInfoByWhere();
        if ( empty( $sms_info ) ) {
            return true;
        }
        $sms_time = $sms_info->sms_time;
        $interval_time = $this->now - $sms_time;
        $send_interval_time = parent::SMS_SEND_LAST_TIME_LIMIT - $interval_time;
        if ( $interval_time < parent::SMS_SEND_LAST_TIME_LIMIT ) {
            throw new TmacClassException( '你的手机号' . $this->mobile . '等' . $send_interval_time . '秒后才能重新获取验证码' );
        }
        return true;
    }

    /**
     * 执行验证码发送的
     * @return type
     * @throws TmacClassException
     */
    public function sendVerifyCode()
    {
        //检验图片验证码
        if ( $this->need_verify_code ) {//默认需要
            $verify_code_result = parent::checkVerifyCode( $this->verify_code );
            if ( $verify_code_result == false ) {
                return false;
            }
        }
        //检查上一次发送时间是不是间隔1分钟
        $this->checkSmsLastTime();
        //验证该手机今天的发送次数
        $sms_send_count = $this->getSmsSendCountByMobile( $this->sms_type );
        if ( $sms_send_count >= self::SMS_SEND_DAY_LIMIT_COUNT ) {
            throw new TmacClassException( '你的手机号' . $this->mobile . '今天验证码已经发送超过' . self::SMS_SEND_DAY_LIMIT_COUNT . '次了' );
        }
        $sms_code = rand( '100000', '999999' );
        switch ( $this->sms_type )
        {
            case self::sms_type_register:
            default:
                $message = '您的短信验证码是：' . $sms_code;
                break;

            case self::sms_type_forget_password:
                $message = '您的找回密码的短信验证码是：' . $sms_code;
                break;
        }

        $sms_model = new service_utils_SmsApiChuanglan_base();
        $sms_model->setSms_type( $this->sms_type );
        $sms_model->setSms_code( $sms_code );
        $sms_model->setMobile( $this->mobile );
        $sms_model->setMessage( $message );
        $sms_res = $sms_model->sendSMS();

        if ( $sms_res == false ) {
            throw new TmacClassException( $sms_model->getErrorMessage() );
        }
        return true;
    }

    /**
     * 发送短信的API
     * @param type $mobile
     * @param type $message
     * @return type
     */
    private function sendSms( $mobile, $message )
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, SMS_API );

        curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, FALSE );

        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, TRUE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $ch, CURLOPT_SSLVERSION, 3 );

        curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $ch, CURLOPT_USERPWD, 'api:key-' . SMS_API_KEY );


        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, array( 'mobile' => $mobile, 'message' => $message ) );

        $res = curl_exec( $ch );
        curl_close( $ch );
        return $res;
    }

    public function resetPassword()
    {
        /**
          //检验图片验证码
          $verify_code_result = parent::checkVerifyCode( $this->verify_code );
          if ( $verify_code_result == false ) {
          return false;
          }
         * 
         */
        //检验短信验证码        
        $checkSmsCode = $this->checkSmsCode();
        if ( $checkSmsCode == false ) {
            //errorMessage已经抛出
            return false;
        }
        //修改密码
        $entity_member = new entity_Member_base();
        $entity_member->password = md5( $this->password . $this->sms_captcha );
        $entity_member->salt = $this->sms_captcha;

        $dao = dao_factory_base::getMemberDao();
        $dao->setWhere( "mobile='{$this->mobile}'" );
        $res = $dao->updateByWhere( $entity_member );
        if ( $res ) {
            $this->isApi || parent::updateMemberCookieCheck( $entity_member, 1 );
        }
        return $res;
    }

    /**
     * 说明：根据用户名称，返回用户信息     
     * @param string $username
     */
    private function getUidByMobile( $mobile )
    {
        $dao = dao_factory_base::getMemberDao();
        $field = 'uid';
        $where = "mobile='{$mobile}'";
        $dao->setField( $field );
        $dao->setWhere( $where );
        $result = $dao->getInfoByWhere();
        if ( $result == false ) {
            $agent_uid = 0;
        } else {
            $agent_uid = $result->uid;
        }
        return $agent_uid;
    }

    /**
     * 说明：根据用户名称，返回用户信息     
     * @param string $username
     */
    public function getUidByUid( $uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $field = 'uid,sum_integral';
        $dao->setField( $field );
        $dao->setPk( $uid );
        $result = $dao->getInfoByPk();
        $return = array(
            'agent_uid' => 0,
            'sum_integral' => 0
        );
        if ( $result == false ) {
            $return[ 'agent_uid' ] = 0;
        } else {
            $return[ 'agent_uid' ] = $result->uid;
            $return[ 'sum_integral' ] = $result->sum_integral;
        }
        return $return;
    }

    /**
     * 通过远程图片url传
     */
    public function getMemberImageIdFromURL( $image_url )
    {
        $imageMd5 = md5( $image_url );
        $postField = array(
            'key' => md5( 'upfile_kuailezu_api_001@' . $imageMd5 ),
            'imageResource' => $image_url,
            'imageType' => 'avatar',
            'imageMd5' => $imageMd5,
            'size' => '110',
            'upType' => 1
        );

        $error = '';
        $re = Functions::curl_post_contents( IMAGE_URL . 'upapi/upfile.php', $postField );
        if ( $re ) {
            $result = json_decode( $re, true );
            if ( $result[ 'success' ] ) {
                $imageId = $result[ 'data' ][ 'imageId' ];
                return $imageId;
            } else {
                $error = $result[ 'message' ];
            }
        } else {
            $error = '照片上传到图片服务器失败，请重试或联系网站客服';
        }
        $this->errorMessage = $error;
        return false;
    }

}
