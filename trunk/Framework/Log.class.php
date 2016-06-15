<?php

/**
 * Log::getInstance('create_order')->write('i am the log'); 
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Log.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class Log
{

    protected $path;
    protected $conversionPattern;
    protected $append;
    protected $level;
    protected $errorMessage;

    const FOPEN_WRITE_CREATE = 'ab'; //写入方式打开，将文件指针指向文件末尾。如果文件不存在则尝试创建之。 append=true
    const FOPEN_WRITE_CREATE_DESTRUCTIVE = 'wb'; //写入方式打开，将文件指针指向文件头并将文件大小截为零。如果文件不存在则尝试创建之。 

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Filter实例
     *
     * @var object
     * @static
     */
    protected static $instance = array();

    /**
     * 取得Filter实例
     *
     * @return object
     * @access public
     * @static
     */
    public static function getInstance( $name )
    {
        if ( !isset( self::$instance[ $name ] ) ) {
            self::$instance[ $name ] = new Log( $name );
        }
        return self::$instance[ $name ];
    }

    /**
     * 构造函数
     * @param type $log_name
     */
    public function __construct( $log_name )
    {
        $logConfigArray = Tmac::config( 'log.log.' . $log_name, APP_BASE_NAME );
        preg_match_all( "/(?:\[)(.*)(?:\])/i", $logConfigArray[ 'File' ], $out );
        if ( !empty( $out[ 0 ][ 0 ] ) ) {
            $logConfigArray[ 'File' ] = str_replace( $out[ 0 ][ 0 ], date( $out[ 1 ][ 0 ] ), $logConfigArray[ 'File' ] );
        }
        preg_match_all( "/(?:\[)(.*)(?:\])/i", $logConfigArray[ 'ConversionPattern' ], $out );
        if ( !empty( $out[ 0 ][ 0 ] ) ) {
            $logConfigArray[ 'ConversionPattern' ] = str_replace( $out[ 0 ][ 0 ], date( $out[ 1 ][ 0 ] ), $logConfigArray[ 'ConversionPattern' ] );
        }
        $this->path = $logConfigArray[ 'File' ];
        $this->conversionPattern = $logConfigArray[ 'ConversionPattern' ];
        $this->append = $logConfigArray[ 'Append' ];
        $this->level = '';
        $this->checkFolder( dirname( $logConfigArray[ 'File' ] ) );
    }

    /**
     * 设置level级别
     * @param type $level
     * @return \Log
     */
    public function setLevel( $level )
    {
        $this->level = $level;
        return $this;
    }

    /**
     * 写日志
     * @param type $level
     * @param type $message
     * @return boolean
     */
    public function write( $message )
    {
        $head = '';
        if ( $this->append ) {
            $file_mode = self::FOPEN_WRITE_CREATE;
        } else {
            $file_mode = self::FOPEN_WRITE_CREATE_DESTRUCTIVE;
        }
        if ( !$fp = fopen( $this->path, $file_mode ) ) {
            return FALSE;
        }

        $head .= '[' . $this->conversionPattern . '] ' . $this->level . ' --> ';

        flock( $fp, LOCK_EX ); //要取得独占锁定（写入的程序），
        fwrite( $fp, "\n" . $head );
        fwrite( $fp, $message );
        flock( $fp, LOCK_UN ); //要释放锁定（无论共享或独占），
        fclose( $fp );

        chmod( $this->path, 0666 );

        return TRUE;
    }

    /**
     * 创建目录 递归创建多级目录
     * @param type $filedir
     * @return boolean
     */
    private function checkFolder( $filedir )
    {
        if ( !file_exists( $filedir ) ) {
            if ( !mkdir( $filedir, 0777, true ) ) {
                $this->errorMessage = '指定的路径权限不足 =>' . $filedir;
                return false;
            }
        }
        return true;
    }

}
