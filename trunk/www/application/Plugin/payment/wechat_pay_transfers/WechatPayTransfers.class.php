<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: WechatPayTransfers.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of WechatTransfers
 *
 * @author zhang
 */
class WechatPayTransfers
{

    const appid = 'wxb35508a58bb6f951';
    const mchid = '1325392401';
    const key = '427661d531ae4a10f573444eaeac6afe';
    const appsecret = '468f7695fc16bb0104c8ed107d2d1f84';
    const apiclient_cert = '/var/www/yph/release_1.0/www/application/Plugin/payment/wechat_pay_transfers/apiclient_cert.pem';
    const apiclient_key = '/var/www/yph/release_1.0/www/application/Plugin/payment/wechat_pay_transfers/apiclient_key.pem';

    private $timeout = 30;
    private $partner_trade_no;
    private $openid;
    private $check_name;
    private $amount;
    private $desc;

    /**
     * 商户订单号，需保持唯一性
     * @param type $partner_trade_no
     */
    function setPartner_trade_no( $partner_trade_no )
    {
        $this->partner_trade_no = $partner_trade_no;
    }

    function setOpenid( $openid )
    {
        $this->openid = $openid;
    }

    /**
     * NO_CHECK：不校验真实姓名 
     * FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账） 
     * OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
     * @param type $check_name
     */
    function setCheck_name( $check_name )
    {
        $this->check_name = $check_name;
    }

    /**
     * 总计 企业付款金额，单位为分
     * @param type $amount
     */
    function setAmount( $amount )
    {
        $this->amount = $amount;
    }

    /**
     * 企业付款操作说明信息。必填。
     * @param type $desc
     */
    function setDesc( $desc )
    {
        $this->desc = $desc;
    }

    public function __construct()
    {
        $this->check_name = 'NO_CHECK';
    }

    private function _enterprisePay()
    {
        require_once Tmac::findFile( 'payment/wechat_pay_transfers/lib/WechatPayTransfers', APP_WWW_NAME, '.php' );
        $wxpay_transfers_model = new WechatPayTransfers();

        $params = array(
            'partner_trade_no' => $number, //商户订单号，需保持唯一性
            'openid' => $openid,
            'check_name' => 'NO_CHECK', //NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账） OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
            'amount' => $amount, // 总计 企业付款金额，单位为分
            'desc' => $desc, //企业付款操作说明信息。必填。
        );

        try {
            $result = $wxpay_transfers_model->payToUser( $params );
        } catch (TmacClassException $exc) {
            die( $exc->getMessage() );
        }


        return $result;
    }

    /**
     * 企业向个人付款
     */
    public function payToUser()
    {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        $params = array();
        $params[ "partner_trade_no" ] = $this->partner_trade_no;
        $params[ "openid" ] = $this->openid;
        $params[ "check_name" ] = $this->check_name;
        $params[ "amount" ] = $this->amount;
        $params[ "desc" ] = $this->desc;
        //检测必填参数
        if ( $params[ "partner_trade_no" ] == null ) {   //
            throw new TmacClassException( "退款申请接口中，缺少必填参数partner_trade_no！" . "<br>" );
        } elseif ( $params[ "openid" ] == null ) {
            throw new TmacClassException( "退款申请接口中，缺少必填参数openid！" . "<br>" );
        } elseif ( $params[ "check_name" ] == null ) {             //NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
            throw new TmacClassException( "退款申请接口中，缺少必填参数check_name！" . "<br>" );
        } elseif ( ($params[ "check_name" ] == 'FORCE_CHECK' or $params[ "check_name" ] == 'OPTION_CHECK') && ($params[ "re_user_name" ] == null) ) {  //收款用户真实姓名。
            throw new TmacClassException( "退款申请接口中，缺少必填参数re_user_name！" . "<br>" );
        } elseif ( $params[ "amount" ] == null ) {
            throw new TmacClassException( "退款申请接口中，缺少必填参数amount！" . "<br>" );
        } elseif ( $params[ "desc" ] == null ) {
            throw new TmacClassException( "退款申请接口中，缺少必填参数desc！" . "<br>" );
        }

        $params[ "mch_appid" ] = self::appid; //公众账号ID
        $params[ "mchid" ] = self::mchid; //商户号
        $params[ "nonce_str" ] = $this->getNonceStr(); //随机字符串
        $params[ 'spbill_create_ip' ] = Functions::get_client_ip();
        $params[ "sign" ] = $this->getSign( $params ); //签名
        $xml = $this->arrayToXml( $params );

        return $this->postXmlSSLCurl( $xml, $url, false, self::apiclient_cert, self::apiclient_key );
    }

    /**
     * 
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    private function getNonceStr( $length = 32 )
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ( $i = 0; $i < $length; $i++ ) {
            $str .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
        }
        return $str;
    }

    private function getSign( $param_array )
    {
        $str = '';
        //对param_array中的参数名称进行升序排序
        ksort( $param_array );
        //按照如下格式转换数组为string格式
        foreach ( $param_array as $k => $v ) {
            $str .= "&$k=$v";
        }
        $str.= '&key=' . self::key;
        $str = substr( $str, 1 );
        //生成MD5为最终的数据签名                
        return strtoupper( md5( $str ) );
    }

    private function arrayToXml( $arr )
    {
        $xml = "<xml>";
        foreach ( $arr as $key => $val ) {
            if ( is_numeric( $val ) ) {
                $xml.="<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml.="<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     *     作用：使用证书，以post方式提交xml到对应的接口url
     */
    private function postXmlSSLCurl( $xml, $url, $second, $cert, $key )
    {
        $ch = curl_init();
        //超时时间
        curl_setopt( $ch, CURLOPT_TIMEOUT, $second ? $second : $this->timeout  );

        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        //设置header
        curl_setopt( $ch, CURLOPT_HEADER, FALSE );
        //要求结果为字符串且输出到屏幕上
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt( $ch, CURLOPT_SSLCERTTYPE, 'PEM' );
        curl_setopt( $ch, CURLOPT_SSLCERT, $cert );
        //默认格式为PEM，可以注释
        curl_setopt( $ch, CURLOPT_SSLKEYTYPE, 'PEM' );
        curl_setopt( $ch, CURLOPT_SSLKEY, $key );
        //post提交方式
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
        $data = curl_exec( $ch );

        //返回结果
        if ( $data ) {
            curl_close( $ch );
            return $this->xmlToArray( $data );
        } else {
            $error = curl_errno( $ch );
            curl_close( $ch );
            throw new TmacClassException( "curl出错，错误码:$error" . "<br>" );
        }
    }

    private function xmlToArray( $data )
    {
        return json_decode( json_encode( simplexml_load_string( $data, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );
    }

}
