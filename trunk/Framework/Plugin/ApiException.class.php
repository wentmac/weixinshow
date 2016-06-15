<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: ApiException.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class ApiException extends Exception
{

    /**
     * 异常类型
     * @var string
     * @access private
     */
    private $type;

    /**
     * 构造器
     *
     * @param string $message
     * @param int $code
     * @access public
     */
    public function __construct( $message = 'Unknown Error', $code = -1, $callback = null )
    {
        parent::__construct( $message, $code );
        $this->type = get_class( $this );
        $debug = $this->getCode(); //是否是debug
        if ( $this->type == 'ApiException' ) {
            try {
                //放出接口出错时的返回值
                $return = array(
                    'status' => $this->getCode(),
                    'success' => false,
                    'message' => $this->getMessage(),
                );

                if ( !empty( $callback ) ) {
                    header( "Content-type: application/json; charset=utf-8" );
                    echo $callback . "(" . json_encode( $return, JSON_UNESCAPED_UNICODE ) . ")";
                    exit( 1 );
                }
                if ( $debug == 1 ) {
                    header( "Content-type: text/html; charset=utf-8" );
                    echo '<pre>';
                    print_r( $return );
                    echo '</pre>';
                } else {
                    header( "Content-type: application/json; charset=utf-8" );
                    echo json_encode( $return, JSON_UNESCAPED_UNICODE );
                }
                exit( 1 );
            } catch (Exception $e) {
                echo $this->getMessage();
            }
        } else {
            die( $this->getMessage() );
        }
    }

}

?>
