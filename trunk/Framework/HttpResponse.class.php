<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: HttpResponse.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class HttpResponse
{

    /**
     * 设置一个cookie
     * 
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return boolean
     */
    public static function setCookie($key, $value, $expire = 0)
    {
        global $TmacConfig;
        if ($TmacConfig['Common']['cookiecrypt']) {
            $key = $TmacConfig['Common']['cookiepre'] . md5($TmacConfig['Common']['cookiepre'] . $key);
            $value = base64_encode(serialize($value));
        } else {
            $key = $TmacConfig['Common']['cookiepre'] . $key;
        }
        return setcookie($key, $value, $expire, ROOT);
    }

    /**
     * 获取一个cookie
     * 
     * @param string $key
     * @return mixed
     */
    public static function getCookie($key)
    {
        global $TmacConfig;
        if ($TmacConfig['Common']['cookiecrypt']) {
            $key = $TmacConfig['Common']['cookiepre'] . md5($TmacConfig['Common']['cookiepre'] . $key);
            return isset($_COOKIE[$key]) ? unserialize(base64_decode($_COOKIE[$key])) : null;
        } else {
            $key = $TmacConfig['Common']['cookiepre'] . $key;
            return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        }
    }

    /**
     * 删除一个cookie
     *
     * @global array $TmacConfig
     * @param string $key
     * @return bool
     */
    public static function delCookie($key)
    {
        global $TmacConfig;
        if ($TmacConfig['Common']['cookiecrypt']) {
            $key = $TmacConfig['Common']['cookiepre'] . md5($TmacConfig['Common']['cookiepre'] . $key);
        } else {
            $key = $TmacConfig['Common']['cookiepre'] . $key;
        }
        return setcookie($key, '', -1, ROOT);
    }

    /**
     * 发送一个header信息
     * 
     * @param string $key
     * @param string $value
     */
    public static function addHeader($key, $value)
    {
        if (!headers_sent()) {
            $key = ucfirst(strtolower($key));
            header($key . ': ' . $value);
        } else {
            throw new TmacException('Header already sent.');
        }
    }

}