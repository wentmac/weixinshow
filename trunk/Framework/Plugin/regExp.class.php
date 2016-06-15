<?php

/**
 * 表单验证类 
 * ============================================================================
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: regExp.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class regExp
{

    //去除字符串空格
    static function strTrim($str)
    {
        return preg_replace("/\s/", "", $str);
    }

    /**
     * 验证用户名
     * @param $str string 用户名
     * @param $type string 字符串类型：纯英文、英文数字、允许的符号(|-_字母数字)
     * @param $len string 用户名长度
     * @return bool true/false
     */
    static function userName($str, $type, $len)
    {
        $str = self::strTrim($str);
        if ($len < strlen($str)) {
            return false;
        } else {
            switch ($type)
            {
                case "EN"://纯英文
                    return preg_match("/^[a-zA-Z]+$/", $str) ? true : false;
                    break;
                case "ENNUM"://英文数字
                    return preg_match("/^[a-zA-Z0-9]+$/", $str) ? true : false;
                    break;
                case "ALL": //允许的符号(|-_字母数字)
                    return preg_match("/^[\|\-\_a-zA-Z0-9]+$/", $str) ? true : false;
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * 验证密码长度
     * @param $str string 密码字符串
     * @param $min num 最小长度
     * @param $max num 最大长度
     * @return bool true/false
     */
    static function passWord($min, $max, $str)
    {
        $str = self::strTrim($str);
        return (strlen($str) >= $min && strlen($str) <= $max) ? true : false;
    }

    /**
     * 验证Email
     * @param $str string 邮箱
     * @return bool true/false
     */
    static function Email($str)
    {
        $str = self::strTrim($str);
        return preg_match("/^([a-z0-9_]|\-|\.)+@(([a-z0-9_]|\-)+\.){1,2}[a-z]{2,4}$/i", $str) ? true : false;
    }

    /**
     * 验证身份证(中国)
     * @param $str string 身份证号码
     * @return bool true/false
     */
    static function idCard($str)
    {
        $str = self::strTrim($str);
        return preg_match("/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i", $str) ? true : false;
    }

    /**
     * 验证座机电话
     * @param $str string 座机电话
     * @param $type 座机类型，分国内(CHN)和国际(INT)
     * @return bool true/false
     */
    static function Phone($type, $str)
    {
        $str = self::strTrim($str);
        switch ($type)
        {
            case "CHN":
                return preg_match("/^([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/", $str) ? true : false;
                break;
            case "INT":
                return preg_match("/^[0-9]{4}-([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/", $str) ? true : false;
                break;
            default:
                break;
        }
    }

    // 验证手机号码
    static function check_mobile($str)
    {
        $str = self::strTrim($str);
        return (preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $str)) ? true : false;
    }

    /**
     * 验证邮编
     * @param $str string 邮编
     * @return bool true/false
     */
    static function Zipcode($str)
    {
        $str = self::strTrim($str);
        return preg_match("/^[1-9]\d{5}$/", $str) ? true : false;
    }

    /**
     * 验证URL
     * @param $str string 邮编
     * @return bool true/false
     */
    static function Url($str)
    {
        $str = self::strTrim($str);
        return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/", $str) ? true : false;
    }

}

