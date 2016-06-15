<?php

/**
 * 说明：住哪新版，公用方法 
 * @author  zhuqiang by time 2014-07-13
 */
class service_utils_Function_base extends Model
{

    /**
     * 说明：创建随机数
     * @staticvar array $rand_array
     * @param type $length
     * @return string
     */
    public static function makePassword( $length = 6 )
    {
        static $rand_array = array(2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'w', 'x', 'y', 'z');
        $return_info = '';
        $count = count( $rand_array );
        for ( $i = 0; $i < $length; $i ++ ) {
            $return_info .= $rand_array [ rand( 0, $count - 1 ) ];
        }
        return $return_info;
    }

    public static function guid()
    {
        if ( function_exists( 'com_create_guid' ) ) {
            return substr( strtolower( com_create_guid() ), 1, - 1 );
        }
        mt_srand( ( double ) microtime() * 10000 );
        $charid = md5( uniqid( rand(), true ) );
        $hyphen = chr( 45 ); // "-"
        $uuid = substr( $charid, 0, 8 ) . $hyphen . substr( $charid, 8, 4 ) . $hyphen . substr( $charid, 12, 4 ) . $hyphen . substr( $charid, 16, 4 ) . $hyphen . substr( $charid, 20, 12 );
        return $uuid;
    }

}
