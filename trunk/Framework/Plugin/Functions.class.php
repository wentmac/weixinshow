<?php

/**
 * 函数库
 * ============================================================================
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Functions.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class Functions
{

    /**
     * 获取客户端IP地址
     * @return <type>
     */
    public static function get_client_ip()
    {
        $ip = $_SERVER[ 'REMOTE_ADDR' ];
        if ( getenv( 'HTTP_CLIENT_IP' ) && strcasecmp( getenv( 'HTTP_CLIENT_IP' ), 'unknown' ) ) {
            $ip = getenv( 'HTTP_CLIENT_IP' );
        } elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) && strcasecmp( getenv( 'HTTP_X_FORWARDED_FOR' ), 'unknown' ) ) {
            $ip = getenv( 'HTTP_X_FORWARDED_FOR' );
        } elseif ( getenv( 'REMOTE_ADDR' ) && strcasecmp( getenv( 'REMOTE_ADDR' ), 'unknown' ) ) {
            $ip = getenv( 'REMOTE_ADDR' );
        } elseif ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) && $_SERVER[ 'REMOTE_ADDR' ] && strcasecmp( $_SERVER[ 'REMOTE_ADDR' ], 'unknown' ) ) {
            $ip = $_SERVER[ 'REMOTE_ADDR' ];
        }
        return preg_match( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [ 0 ] : '';
    }

    /**
      +----------------------------------------------------------
     * 字符串截取，支持中文和其他编码
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $str 需要转换的字符串
     * @param string $length 截取长度
     * @param string $start 开始位置
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public static function msubstr( $str, $length, $start = 0, $charset = "utf-8", $suffix = true )
    {
        if ( function_exists( "mb_substr" ) )
            return mb_substr( $str, $start, $length, $charset );
        elseif ( function_exists( 'iconv_substr' ) ) {
            return iconv_substr( $str, $start, $length, $charset );
        }
        $re[ 'utf-8' ] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re[ 'gb2312' ] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re[ 'gbk' ] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re[ 'big5' ] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all( $re[ $charset ], $str, $match );
        $slice = join( "", array_slice( $match[ 0 ], $start, $length ) );
        if ( $suffix )
            return $slice . "…";
        return $slice;
    }

    /**
     * 截取中文字符串
     * Utf-8、gb2312都支持的汉字截取函数
     * cut_str(字符串, 截取长度, 开始长度, 编码);
     * 编码默认为 utf-8
     * 开始长度默认为 0
     */
    public static function cut_str( $string, $sublen, $start = 0, $code = 'UTF-8' )
    {
        if ( $code == 'UTF-8' ) {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all( $pa, $string, $t_string );

            if ( count( $t_string[ 0 ] ) - $start > $sublen )
                return join( '', array_slice( $t_string[ 0 ], $start, $sublen ) ) . "...";
            return join( '', array_slice( $t_string[ 0 ], $start, $sublen ) );
        }
        else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen( $string );
            $tmpstr = '';

            for ( $i = 0; $i < $strlen; $i++ ) {
                if ( $i >= $start && $i < ($start + $sublen) ) {
                    if ( ord( substr( $string, $i, 1 ) ) > 129 ) {
                        $tmpstr.= substr( $string, $i, 2 );
                    } else {
                        $tmpstr.= substr( $string, $i, 1 );
                    }
                }
                if ( ord( substr( $string, $i, 1 ) ) > 129 )
                    $i++;
            }
            if ( strlen( $tmpstr ) < $strlen )
                $tmpstr.= "...";
            return $tmpstr;
        }
    }

    //GB转UTF-8编码
    public static function gb2utf8( $gbstr )
    {
        if ( function_exists( 'iconv' ) ) {
            return iconv( 'gbk', 'utf-8//ignore', $gbstr );
        }
        global $CODETABLE;
        if ( trim( $gbstr ) == "" ) {
            return $gbstr;
        }
        if ( empty( $CODETABLE ) ) {
            $filename = DEDEINC . "/data/gb2312-utf8.dat";
            $fp = fopen( $filename, "r" );
            while ( $l = fgets( $fp, 15 ) ) {
                $CODETABLE[ hexdec( substr( $l, 0, 6 ) ) ] = substr( $l, 7, 6 );
            }
            fclose( $fp );
        }
        $ret = "";
        $utf8 = "";
        while ( $gbstr != '' ) {
            if ( ord( substr( $gbstr, 0, 1 ) ) > 0x80 ) {
                $thisW = substr( $gbstr, 0, 2 );
                $gbstr = substr( $gbstr, 2, strlen( $gbstr ) );
                $utf8 = "";
                @$utf8 = u2utf8( hexdec( $CODETABLE[ hexdec( bin2hex( $thisW ) ) - 0x8080 ] ) );
                if ( $utf8 != "" ) {
                    for ( $i = 0; $i < strlen( $utf8 ); $i += 3 )
                        $ret .= chr( substr( $utf8, $i, 3 ) );
                }
            } else {
                $ret .= substr( $gbstr, 0, 1 );
                $gbstr = substr( $gbstr, 1, strlen( $gbstr ) );
            }
        }
        return $ret;
    }

    static function GetHttps( $url, $charset = "utf-8" )
    {
        if ( extension_loaded( 'curl' ) ) {
            $file_contents = self::curl_file_get_contents( $url );
        } else {
            $file_contents = @file_get_contents( $url );
        }
        if ( $charset == "utf-8" ) {
            return $file_contents;
        } elseif ( $charset == "gb2312" ) {
            $file_contents = iconv( "gb2312", "UTF-8", $file_contents );
            return $file_contents;
        }
    }

    /**
     * curl取文件
     * @param type $url
     * @param type $timeout
     * @param type $ssl
     * @return type 
     */
    public static function curl_file_get_contents( $url, $timeout = 5, $ssl = false )
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        if ( $ssl )
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        if ( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) )
            curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $r = curl_exec( $ch );
        curl_close( $ch );
        return $r;
    }

    /**
     * curl POST文件
     * @param type $url
     * @param type $postField
     * @param type $timeout
     * @return type 
     */
    public static function curl_post_contents( $url, $postField, $timeout = 30, $ssl = false )
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postField );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        if ( $ssl ) {
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        }
        if ( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) {
            curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
        }
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $r = curl_exec( $ch );
        curl_close( $ch );
        return $r;
    }

    // 循环创建目录创建目录 递归创建多级目录
    public static function CreateFolder( $dir, $mode = 0777 )
    {
        if ( is_dir( $dir ) || @mkdir( $dir, $mode ) )
            return true;
        if ( !self::CreateFolder( dirname( $dir ), $mode ) )
            return false;
        return @mkdir( $dir, $mode );
    }

    /**
     * 删除非空文件夹
     * @param $dir;
     * return
     */
    public static function deldir( $dir )
    {
        $dh = opendir( $dir );
        while ( ($file = readdir( $dh )) !== false ) {
            if ( $file != '.' && $file != '..' && $file != '.svn' ) {
                is_dir( $dir . DIRECTORY_SEPARATOR . $file ) ?
                                self::delDir( $dir . DIRECTORY_SEPARATOR . $file ) :
                                unlink( $dir . DIRECTORY_SEPARATOR . $file );
            }
        }
        if ( readdir( $dh ) == false ) {
            closedir( $dh );
            if ( rmdir( $dir ) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 取16位md5
     * @param type $str
     * @return type 
     */
    public static function md5_16bit( $str )
    {
        return substr( md5( $str ), 8, 16 );
    }

    /**
     * 二维数组排序
     * @param type $multi_array
     * @param type $sort_key
     * @param type $sort
     * @return type 
     */
    public static function array_sort( $multi_array, $sort_key, $sort = SORT_ASC )
    {
        $key_array = array();
        if ( is_array( $multi_array ) ) {
            foreach ( $multi_array AS $k => $v ) {
                $key_array[ $k ] = $v[ $sort_key ];
            }
        } else {
            return false;
        }
        array_multisort( $key_array, $sort, $multi_array );
        return $multi_array;
    }

    public static function array_object_sort( $multi_array, $sort_key, $sort = SORT_ASC )
    {
        $key_array = array();
        if ( is_array( $multi_array ) ) {
            foreach ( $multi_array AS $k => $v ) {
                $key_array[ $k ] = $v->$sort_key;
            }
        } else {
            return false;
        }
        array_multisort( $key_array, $sort, $multi_array );
        return $multi_array;
    }

    /**
     * 两个时间戳获取 分钟小时
     * @param type $start_time
     * @param type $end_time
     * @return boolean 
     */
    public static function fome_time( $start_time, $end_time )
    {

        $time = $end_time >= $start_time ? $end_time - $start_time : $start_time - $end_time;
        if ( $time < 60 ) {
            return $time . '秒钟';
        } else if ( $time < 3600 ) {

            return (int) ($time / 60) . '分钟';
        } else if ( $time > 3600 && $time < 86400 ) {

            $minute = (int) (($time % 3600) / 60);
            return (int) ($time / 3600) . '小时' . $minute . '分钟';
        } else {
            $hour = (int) (($time % 86400) / 3600);
            $minute = (int) ((($time % 86400) % 3600) / 60);
            return (int) ($time / 86400) . '天' . $hour . '小时' . $minute . '分钟';
        }
    }

}
