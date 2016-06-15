<?php

/**
 * 得到到卖家店铺的宣传二维码
 * 
 */
class service_utils_WeixinToken_base extends service_Model_base
{

    private $appid;
    private $secret;
    private $errorMessage;
    private $expired_time;

    function setAppid( $appid )
    {
        $this->appid = $appid;
    }

    function setSecret( $secret )
    {
        $this->secret = $secret;
    }

    function setExpired_time( $expired_time )
    {
        $this->expired_time = $expired_time;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
        $this->expired_time = 7200;
    }

    /**
     * 取access_token值
     */
    public function getAccessToken()
    {
        $token_array = Tmac::getCache( 'weixin_access_token_' . $this->appid, array( $this, 'getAccessTokenFromWeixin' ), array(), $this->expired_time );
        if ( empty( $token_array[ 'access_token' ] ) ) {
            $this->expired_time = 0;
            return $this->getAccessToken();
        }
        return $token_array[ 'access_token' ];
    }

    /**
     * 取access_token值
     */
    public function getAccessTokenFromWeixin()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->secret;
        $token_json = Functions::curl_file_get_contents( $url, 30, true );
        $token_array = json_decode( $token_json, true );
        if ( array_key_exists( 'errcode', $token_array ) ) {
            throw new TmacClassException( $token_array[ 'errmsg' ] );
        } else {
            return $token_array;
        }
    }

}
