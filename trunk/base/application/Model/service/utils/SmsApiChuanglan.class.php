<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: SmsApiChuanglan.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of SmsApiChuanglan
 *
 * @author wentao
 */
class service_utils_SmsApiChuanglan_base extends service_Model_base
{

    const username = 'yinpin';
    const password = 'Tch147258fu';

    private $mobile;
    private $message;
    private $sms_type;
    private $sms_code;
    private $errorMessage;

    function setMobile( $mobile )
    {
        $this->mobile = $mobile;
    }

    function setMessage( $message )
    {
        $this->message = $message;
    }

    function setSms_type( $sms_type )
    {
        $this->sms_type = $sms_type;
    }

    function setSms_code( $sms_code )
    {
        $this->sms_code = $sms_code;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * $this->mobile;
     * $this->message;
     * $this->sms_type;
     * $this->sms_code;//可选
     * $this->sendSMS();
     * @return boolean
     * @throws TmacClassException
     */
    public function sendSMS()
    {
        if ( empty( $this->mobile ) ) {
            $this->errorMessage = '接收短信的手机号不能为空';
            return false;
        }
        if ( empty( $this->message ) ) {
            $this->errorMessage = '短信内容不能为空';
            return false;
        }
        if ( empty( $this->sms_type ) ) {
            $this->errorMessage = '短信内容不能为空';
            return false;
        }
        $sms_send_res = $this->send();
        if ( $sms_send_res[ 1 ] == 0 ) {
            //写到sms_log表中
            $entity_SmsLog_base = new entity_SmsLog_base();
            $entity_SmsLog_base->sms_type = $this->sms_type;
            $entity_SmsLog_base->sms_code = $this->sms_code;
            $entity_SmsLog_base->sms_mobile = $this->mobile;
            $entity_SmsLog_base->sms_content = $this->message;
            $entity_SmsLog_base->sms_time = $this->now;
            $entity_SmsLog_base->sms_ip = Functions::get_client_ip();
            $entity_SmsLog_base->result_code = $sms_send_res[ 0 ];

            $dao = dao_factory_base::getSmsLogDao();
            $res = $dao->insert( $entity_SmsLog_base );
            if ( $res ) {
                return true;
            } else {
                Log::getInstance( 'sms_error' )->write( var_export( $entity_SmsLog_base, true ) );
                return false;
            }
        } else {
            //写错误log
            Log::getInstance( 'sms_error' )->write( var_export( $sms_send_res, true ) );
            $this->errorMessage = '短信服务失败，请联系管理员';
            return false;
        }
    }

    /**
     * 蓝创的短信发送
     * @param type $mobile
     * @param type $message
     * @return type
     */
    public function send()
    {
        $post_data = array();
        $post_data[ 'account' ] = iconv( 'utf-8', 'GB2312', self::username );
        $post_data[ 'pswd' ] = iconv( 'utf-8', 'GB2312', self::password );
        $post_data[ 'mobile' ] = $this->mobile;
        $post_data[ 'msg' ] = mb_convert_encoding( $this->message, 'UTF-8', 'auto' );
        $url = 'http://222.73.117.156/msg/HttpBatchSendSM?';
        $o = "";
        foreach ( $post_data as $k => $v ) {
            $o.= "$k=" . urlencode( $v ) . "&";
        }
        $post_data = substr( $o, 0, -1 );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $result = curl_exec( $ch );
        curl_close( $ch );
        $result = preg_split( "/[,\r\n]/", $result );
        return $result;
    }

}
