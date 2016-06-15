<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_Account_base extends service_Model_base
{

    const MAX_VERIFY_CODE_FAILD_COUNT = 10; //允许的最大错误次数
    const MAX_FAILD_COUNT = 25; //允许的最大错误次数
    const FAILD_ALLOW_TIME = 60; //错误的冻结时间（分钟）       
    const MAX_SMS_CODE_FAILED_COUNT = 3; //短信验证码最大失败次数
    const MAX_SMS_CODE_SEND_COUNT = 3; //短信验证码每个IP每天最大发送次数
    const MAX_LOGIN_PASSWORD_FAILED_COUNT = 3; //密码输入错误超过3次就需要验证图片验证码
    const SMS_SEND_DAY_LIMIT_COUNT = 5; //手机一天的发送次数
    const SMS_SEND_LAST_TIME_LIMIT = 60; //上一次发送时间间隔
    const member_type_seller = 1; //普通用户+卖家
    const member_type_supplier = 2; //用户类型 供应商    
    const sms_type_register = 1; //注册时短信验证
    const sms_type_forget_password = 2; //找回密码时的短信    
    const sms_type_bind_verify = 3; //绑定第三方手机验证
    const sms_type_bind_bankcard = 4; //绑定银行卡号码
    const sms_type_message = 5; //消息提醒类型的短信
    const sms_type_bind_mobile = 6; //账号绑定手机号
    const sms_type_effective_time = 5; //找回密码时的短信有效果时间，单位分钟

    /**
     * 用户账户注册来源
     * 自家网站
     */
    const register_source_self = 0;

    /**
     * 用户账户注册来源
     * 微信
     */
    const register_source_wechat = 1;

    /**
     * 用户账户注册来源
     * QQ
     */
    const register_source_qq = 2;

    /**
     * 用户账户注册来源
     * 微博
     */
    const register_source_weibo = 3;

    /**
     * 用户账户注册来源
     * APP
     */
    const register_source_app = 4;

    protected static $DENY_USER_ARRAY = array( '869' ); //特例，不允许登录的用户ID
    protected $needVerifyCode = false; //下一次请求时是否需要传verify_code
    protected $need_verify_code = true; //本次发送是否需要验证verify_code
    protected $isApi = false; //api 
    protected $shop_name;
    protected $errorMessage;

    function getNeedVerifyCode()
    {
        return $this->needVerifyCode;
    }

    function setNeed_verify_code( $need_verify_code )
    {
        $this->need_verify_code = $need_verify_code;
    }

    function setIsApi( $isApi )
    {
        $this->isApi = $isApi;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function getShop_name()
    {
        return $this->shop_name;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 说明：处理登录来源页面
     */
    public function getReferer()
    {
        $referer_url = Input::get( 'purl', '' )->String();

//        if ($referer_url == '') {
//			$referer_url = htmlspecialchars( Input::cookie ( 'Referer' )->String ());
//		}

        if ( $referer_url == 'ajax' ) {
            $referer_url = INDEX_URL . 'manage.php?m=bill.home';
        }

        if ( $referer_url == '' ) {
            $referer_url = isset( $_SERVER [ 'HTTP_REFERER' ] ) ? filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING ) : '';
        }

        $referer_url = urldecode( stripslashes( $referer_url ) );
        $referer_url = htmlspecialchars( $referer_url );

        $host = parse_url( $referer_url );

        if ( isset( $host [ 'host' ] ) && substr( $host [ 'host' ], - 6 ) != '090.cn' ) {
            $referer_url = '';
        }

        $referer_url = ($referer_url == '' || strpos( $referer_url, 'login' )) ? INDEX_URL . 'manage.php?m=bill.home' : $referer_url;

        return $referer_url;
    }

    /**
     * 说明：根据用户名称，返回用户信息     
     * @param string $username
     */
    public function getMemberInfoByUserName( $username )
    {
        $dao = dao_factory_base::getMemberDao();
        $field = 'uid,username,password,mobile,last_login_time,salt,member_image_id,login_fail_count,member_type';
        $dao->setField( $field );
        if ( !preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $username ) ) {
            $where = "username= '{$username}'";
        } else {
            $where = "mobile='{$username}'";
        }
        $dao->setWhere( $where );
        $result = $dao->getInfoByWhere();
        return $result;
    }

    public function getMemberInfoById( $uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $uid );
        return $dao->getInfoByPk();
    }

    /**
     * 说明：验证成功后，用户登录完成相关操作
     * @author zhangwentao
     */
    public function updateMemberCookieCheck( $member_info, $expire )
    {
        $expire_time = 86400 * $expire;
        $member_info instanceof entity_Member_base;
        //$password = md5_16(md5($password).$salt);
        $token = md5( md5( $member_info->password ) . $member_info->salt );

        //更新数据库成功,保存session
        setcookie( 'uid', $member_info->uid, $this->now + $expire_time, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        setcookie( 'token', $token, $this->now + $expire_time, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        setcookie( 'username', $member_info->username, $this->now + $expire_time, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        setcookie( 'mobile', $member_info->mobile, $this->now + $expire_time, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );

        $model = new service_order_Cart_base();
        $model->setUid( $member_info->uid );
        $model->cleanRepeat();
    }

    /**
     * 检查图片验证码正确性
     * @param type $verify_code
     * @return boolean
     */
    protected function checkVerifyCode( $verify_code )
    {
        //判断是否需要验证码、条件<每个IP每天只能发 self::MAX_SMS_CODE_SEND_COUNT 次
        $sms_send_count = self::getSmsSendCountEveryDayByIP();
        if ( $sms_send_count <= self::MAX_SMS_CODE_SEND_COUNT ) {
            if ( $sms_send_count == self::MAX_SMS_CODE_SEND_COUNT ) {
                $this->needVerifyCode = true;
            }
            return true;
        }
        $this->needVerifyCode = true;
        if ( empty( $_SESSION[ 'verify_code' ] ) ) {
            $this->errorMessage = '验证码不存在';
            return false;
        }
        $c_code = isset( $_SESSION [ 'verify_code' ] ) ? $_SESSION [ 'verify_code' ] : '';
        $verify_code = strtolower( $verify_code );

        if ( md5( $verify_code ) <> $c_code || empty( $verify_code ) || empty( $c_code ) ) {
            $this->errorMessage = '验证码不正确';
            return false;
        }
        return true;
    }

    /**
     * 每个IP今天的发送次数
     * @return type
     */
    private function getSmsSendCountEveryDayByIP()
    {
        $today_start = strtotime( date( 'Y-m-d' ) );
        $today_end = $today_start + 86400;
        $ip = Functions::get_client_ip();
        $dao = dao_factory_base::getSmsLogDao();
        $dao->setWhere( "sms_ip='{$ip}' AND sms_time>={$today_start} AND sms_time<={$today_end}" );
        return $dao->getCountByWhere();
    }

    /**
     * 给卖家在登录后检测是否需要
     */
    public function getShopNameStatusByUid( $uid )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $uid );
        $dao->setField( 'shop_name' );
        $member_setting_info = $dao->getInfoByPk();
        if ( empty( $member_setting_info->shop_name ) ) {
            return false;
        } else if ( preg_match( '/(聚店_20[\d+]{15,20})/', $member_setting_info->shop_name ) ) {
            return false;
        } else {
            $this->shop_name = $member_setting_info->shop_name;
            return true;
        }
    }

    /**
     * 处理注册和登录后用户信息返回
     * @param type $memberInfo
     */
    public function handleLoginReturn( $memberInfo )
    {
        $memberInfo instanceof entity_Member_base;
        $token = md5( md5( $memberInfo->password ) . $memberInfo->salt );
        $array = array(
            'uid' => $memberInfo->uid,
            'token' => $token,
            'username' => $memberInfo->username,
            'mobile' => $memberInfo->mobile,
            'member_avatar_url' => $this->getImage( $memberInfo->member_image_id, '110', 'avatar' )
        );
        return $array;
    }

}
