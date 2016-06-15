<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Base.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class Base
{

    /**
     * 模板对象
     * @var Template
     */
    protected $tpl;

    /**
     * 当前时间
     * @var int
     */
    protected $now;

    /**
     * 本次请求是否为POST
     * @var bool
     */
    protected $isPost = false;
    protected $tVar = array(); // 模板输出变量

    /**
     * 构造函数 初始化
     * @access public
     */

    public function __construct()
    {
        $this->isPost = (isset( $_SERVER[ 'REQUEST_METHOD' ] ) && $_SERVER[ 'REQUEST_METHOD' ] === 'POST');
        $this->now = time();
    }

    /**
     * 连接数据库
     * @param string $database   数据库名
     * @access public
     * @final
     */
    public final function connect( $database = null )
    {
        $database || $database = $GLOBALS[ 'TmacConfig' ][ 'Common' ][ 'Database' ];
        return DatabaseDriver::getInstance( $database );
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name
     * @param mixed $value
     */
    public function assign( $name, $value = '' )
    {
        if ( is_array( $name ) ) {
            $this->tVar = array_merge( $this->tVar, $name );
        } elseif ( is_object( $name ) ) {
            foreach ( $name as $key => $val )
                $this->tVar[ $key ] = $val;
        } else {
            $this->tVar[ $name ] = $value;
        }
    }

    /**
     * 取模板變量值
     * @param type $name
     * @return type 
     */
    public final function getValue( $name )
    {
        if ( isset( $this->tVar[ $name ] ) )
            return $this->tVar[ $name ];
        else
            return false;
    }

    /**
     * 显示前台模板
     * @param string $tpl  模板文件名 为空时是 CONTROLLER_ACTION
     * @access public
     * @return void
     */
    public final function V( $tpl = null )
    {
        //设置模板中的全局变量|前台模板目录
        $array = array(
            'BASE' => BASE . 'common/',
            'BASE_V' => BASE_V . $GLOBALS[ 'TmacConfig' ][ 'Template' ][ 'template_dir' ] . '/',
            'BASE_COMMON_V' => BASE_V . 'common/'
        );
        $this->assign( $array );
        $tpl = $GLOBALS[ 'TmacConfig' ][ 'Template' ][ 'template_dir' ] . DIRECTORY_SEPARATOR . $tpl;
        Tmac::view( $tpl, $this->tVar );
    }

    /**
     * 显示原生前台模板
     * 主要是用来配置$this->assign();变量赋值使
     * @param string $tpl  模板文件名 为空时是 CONTROLLER_ACTION
     * @access public
     * @return void
     */
    public final function VIEW( $view )
    {
        //设置模板中的全局变量|前台模板目录
        $array = array(
            'BASE' => BASE . 'common/',
            'BASE_V' => BASE_V . $GLOBALS[ 'TmacConfig' ][ 'Template' ][ 'template_dir' ] . '/',
            'BASE_COMMON_V' => BASE_V . 'common/'
        );
        $this->assign( $array );
        Tmac::display( $view, $this->tVar );
    }

    /**
     * 获取Model对象
     * @param string $model
     * @param string $app_name 项目名（如果需要跨项目调用）
     * @param string $ext
     * @return object
     */
    public final function M( $model, $app_name = APP_NAME, $ext = '.class.php' )
    {
        return Tmac::model( $model, $app_name, $ext );
    }

    /**
     * 输出一个Debug变量信息
     * @global array $TmacConfig
     * @param string $key
     * @param mixed $value
     */
    public final function D( $key, $value )
    {
        global $TmacConfig;
        if ( $TmacConfig[ 'Common' ][ 'debug' ] ) {
            $debug = Debug::getInstance();
            $debug->setVar( $key, $value );
        }
    }

    /**
     * 载入插件
     * @param string $plugin   插件名 插件类名必须与文件名一致 "."作为目录分隔符
     * @param string $app_name 项目名（如果需要跨项目调用）
     * @param array $param     插件参数
     * @param string $ext      插件后缀名
     * @return object
     * @access public
     */
    public final function P( $plugin, $app_name = null, $param = array(), $ext = '.class.php' )
    {
        return Tmac::plugin( $plugin, $app_name, $param, $ext );
    }

    /**
     * 转义数据 htmlspecialchars效果
     * @param mixed $data 要转义的数据
     * @return mixed
     */
    public final function H( $data )
    {
        return is_array( $data ) ? array_map( array( __CLASS__, 'H' ), $data ) : htmlspecialchars( $data, ENT_QUOTES );
    }

    /**
     * 反转义数据 htmlspecialchars_decode() 函数把一些预定义的 HTML 实体转换为字符。
     * @param mixed $data 要转义的数据
     * @return mixed
     */
    public final function HD( $data )
    {
        return is_array( $data ) ? array_map( array( __CLASS__, 'HD' ), $data ) : htmlspecialchars_decode( $data, ENT_QUOTES );
    }

    /**
     * JS跳转 不受header限制
     * @param string $url      跳转到的URL
     * @param string $errStr   alert提示信息
     */
    public final function alert( $url = 'BACK', $errStr = 'NOERR', $parent = false )
    {
        $first = '<script type="text/javascript">';
        $center = '';
        $last = '</script>';
        ($errStr == 'NOERR') ? ($center = '') : ($center = 'alert("' . $errStr . '");');
        ($url == 'BACK') ? ($center .= 'window.history.go(-1);') : ($center .= ( $parent == true ? 'parent.' : '') . 'location.href="' . $url . '";');
        echo $first, $center, $last;
        exit();
    }

    /**
     * 页面跳转 不受header限制
     * @param string $msg      跳转时提示的文字
     * @param string $url      跳转到的URL（默认是返回上一级）
     * @param string $time     跳转时等待时间
     */
    public final function redirect( $msg, $url = 'javascript:history.go(-1);', $time = 3, $success = false )
    {
        $array[ 'msg' ] = $msg;
        $array[ 'url' ] = $url;
        $array[ 'time' ] = $time;
        $this->assign( $array );
        if ( $success ) {
            $this->V( 'redirect_success' );
        } else {
            $this->V( 'redirect' );
        }
        exit();
    }

    /**
     * 页面跳转 header
     * @param string $msg      跳转时提示的文字
     * @param string $url      跳转到的URL（默认是返回上一级）
     * @param string $time     跳转时等待时间
     */
    public final function headerRedirect( $url )
    {
        header( "Location: " . $url );
        exit();
    }

    /**
     * 取get post参数
     * @param <type> $key
     * @param <mixed> $default_value = NULL
     * @return <type>
     */
    public final function getParam( $key, $default_value = NULL )
    {
        if ( isset( $_GET[ $key ] ) ) {
            return $_GET[ $key ];
        } elseif ( isset( $_POST[ $key ] ) ) {
            return $_POST[ $key ];
        } else {
            if ( $default_value >= 0 ) {
                return $default_value;
            } else {
                return '';
            }
        }
    }

}
