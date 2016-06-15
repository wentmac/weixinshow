<?php

/**
 * 参数接收过滤
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Input.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.kuailezu.com；
 */
class Filter
{

    protected static $field; //要过滤的参数
    protected static $errorMessage; //单个的过滤失败信息
    protected static $success = true; //Filter过滤参数的状态
    protected static $requiredField = true; //Filter 当前field必选
    protected static $failMessage; //最早的一次的失败信息

    /**
     * Filter实例
     *
     * @var object
     * @static
     */
    protected static $instance = null;

    /**
     * 取得Filter实例
     *
     * @return object
     * @access public
     * @static
     */
    public static function getInstance()
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new Filter;
        }
        return self::$instance;
    }

    /**
     * 取Filter类中的错误信息
     * @return type 
     */
    public static function getErrorMessage()
    {
        return self::$errorMessage;
    }

    /**
     * 取Filter类中的最早的错误信息
     * @return type 
     */
    public static function getFailMessage()
    {
        return self::$failMessage;
    }

    /**
     * 取Filter类中的成功状态
     * --------------------
     *   if (Filter::getStatus() === false) {
     *       echo Filter::getFailMessage();
     *   }
     * --------------------
     * @return type 
     */
    public static function getStatus()
    {
        return self::$success;
    }

    /**
     * 设置验证出错消息
     * @author zhangwentao
     * @param type $message 
     */
    protected function setErrorMessage( $message )
    {
        self::$errorMessage = $message;
        if ( self::$success === true ) {
            self::$success = false;
            self::$failMessage = $message;
        }
    }

    /**
     * 设置字段值
     * @author zhangwentao
     * @param type $message 
     */
    public function setField( $value )
    {
        self::$field = $value;
        if ( empty( $value ) ) {
            self::$requiredField = $value;
        } else {
            self::$requiredField = true;
        }
    }

    /**
     * 是否必选字段方法     
     * @param type $fieldDescription
     * @return type 
     */
    public function required( $fieldDescription = '' )
    {
        if ( !empty( $fieldDescription ) && empty( self::$field ) ) {//如果有不能为空限制
            self::setErrorMessage( $fieldDescription );
            self::$requiredField = false;
        }
        return self::$instance;
    }

    /**
     * 取int形式的参数
     * $room_id = Input::get('room_id')->required('房屋ID不能为空')->int();
     * @author zhangwentao     
     * @return type 
     */
    public function int()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        return intval( self::$field );
    }

    /**
     * 取长int形式的参数
     * $room_id = Input::get('room_id')->required('房屋ID不能为空')->bigint();
     * @author zhangwentao     
     * @return type 
     */
    public function bigint()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^(\d+)$/', self::$field ) ) {
            return intval( self::$field );
        }
        return self::$field;
    }

    /**
     * 取float形式的参数
     * $room_id = Input::get('baidu_lat')->required('百度经纬度不能为空')->float();     
     * @author zhangwentao     
     * @return type 
     */
    public function float()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        return floatval( self::$field );
    }

    /**
     * 过滤掉参数中的html标签
     * $room_id = Input::get('room_title')->required('房屋标题不能为空')->string();   
     * @author zhangwentao     
     * @return type 
     */
    public function string()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        $this->sqlInjectionFilter();
        return htmlspecialchars( self::$field, ENT_QUOTES );
    }

    /**
     * 过滤掉GET/POST参数中的sql注入
     * $room_id = Input::get('room_title')->required('房屋标题不能为空')->string();   
     * @author zhangwentao     
     * @return type 
     */
    public function sql()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        $this->sqlInjectionFilter();
        return self::$field;
    }

    /**
     * 处理字符串，以便可以正常进行搜索      
     * $search = Input::get('room_title')->required('房屋标题不能为空')->forSearch();
     * @access public           
     * @return string
     */
    public function forSearch()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        $this->sqlInjectionFilter();
        return str_replace( array( '%', '_' ), array( '\%', '\_' ), self::$field );
    }

    /**
     * 验证邮箱是否正确
     * $email = Input::get('email')->required('请输入邮箱地址')->email();
     * @author zhangwentao
     * @return type 
     */
    public function email()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^\s*([A-Za-z0-9_-]+(\.\w+)*@([\w-]+\.)+\w{2,3})\s*$/', self::$field ) ) {
            self::setErrorMessage( '邮箱格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 验证用户用户名是否正确     
     * $username = Input::get('email')->required('用户名不能为空')->username();
     * @return type 
     */
    public function username()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        $username_len = mb_strwidth( self::$field, 'UTF8' );
        //验证用户所填写的信息是否正确
        if ( $username_len < 4 ) {
            self::setErrorMessage( '请输入4个字母或2个汉字以上的用户名' );
            return false;
        } elseif ( $username_len > 20 ) {
            self::setErrorMessage( '用户名不能超过20个字母或10个汉字' );
            return false;
        } elseif ( !preg_match( '/^[\x{4e00}-\x{9fa5}\w-]+$/u', self::$field ) ) {
            self::setErrorMessage( '用户名只能使用字母数字下划线' );
            return false;
        } elseif ( preg_match( '/^1([3]|[5]|[8])[0-9]{9}$/', self::$field ) ) {
            self::setErrorMessage( '用户名请使用非手机号码的格式' );
            return false;
        }
        return self::$field;
    }

    /**
     * 密码验证方法
     * $password = Input::get('password')->required('用户名不能为空')->password();
     * @param type $password
     * @return type 
     */
    public function password()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        $password_len = strlen( self::$field );
        $this->sqlInjectionFilter();
        //验证密码是否为空
        if ( empty( self::$field ) ) {
            self::setErrorMessage( '密码不能为空' );
            return false;
        } elseif ( preg_match( '/\s/', self::$field ) ) {
            self::setErrorMessage( '密码请勿使用空格' );
            return false;
        } elseif ( $password_len < 6 ) {
            self::setErrorMessage( '密码太短了，最少6位。' );
            return false;
        } elseif ( $password_len > 16 ) {
            self::setErrorMessage( '密码太长了，最多16位。' );
            return false;
        }
        return self::$field;
    }

    /**
     * 验证手机号码是否正确     
     * $tel = Input::get('tel')->required('请输入手机号码')->tel();
     * @return type 
     */
    public function tel()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', self::$field ) ) {
            self::setErrorMessage( '手机格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 时间格式的验证
     * @return type 
     */
    public function date()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( "#\d{4}(-)?\d{1,2}(-)?\d{1,2}#", self::$field ) ) {
            self::setErrorMessage( '日期格式不正确' );
            return false;
        }
        return self::$field;
        //return zhuna_input_Transverter('date', self::$field);
    }

    /**
     * 过滤拼音 
     * @param type $pinyin     
     * @return type 
     */
    public function pinyin()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^([\w+]{1,250})$/', self::$field ) ) {
            self::setErrorMessage( '参数格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * IP地址格式验证
     * @return boolean
     */
    public function ip()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$//', self::$field ) ) {
            self::setErrorMessage( 'IP地址格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 过滤cityid          
     * @return type 
     */
    public function cityid()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        return self::city( '城市' );
    }

    /**
     * 过滤pid          
     * @return type 
     */
    public function pid()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        return self::city( '省级' );
    }

    /**
     * 过滤aid          
     * @return type 
     */
    public function aid()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        return self::city( '行政区' );
    }

    /**
     * 过滤sid          
     * @return type 
     */
    public function sid()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^([\d+]{6})$/', self::$field ) ) {
            self::setErrorMessage( '商圈格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 手机验证码格式
     * @return type 
     */
    public function smsCode()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^[0-9]{6}/', self::$field ) ) {
            $this->setErrorMessage( '验证码格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 取1,2,3,4,5 int被字符串分割
     * @return type 
     */
    public function intString()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^([\d]+,)*?([\d]+)$/', self::$field ) ) {
            $this->setErrorMessage( '参数格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 取1,2,3,-4,5 int被字符串分割
     * @return type 
     */
    public function intNegativeString()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^(-?[\d]+,)*?(-?[\d]+)$/', self::$field ) ) {
            $this->setErrorMessage( '参数格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 取图片尺寸格式
     * @return type 
     */
    public function imageSize()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^(\d+|\d+x\d+)$/', self::$field ) ) {
            $this->setErrorMessage( '图片尺寸格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 取图片ID格式
     * @return type 
     */
    public function imageId()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }        
        if ( !preg_match( '/^([a-z0-9]{2}\/[a-z0-9]{2}\/[a-z0-9]{12})$/', self::$field ) ) {
            $this->setErrorMessage( '图片ID参数格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * 过滤字符串数组  传的参数要用都(,)号把每个值分开
     * @return boolean
     */
    public function stringList()
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }

        $tmp = array();
        if ( empty( self::$field ) ) {
            return false;
        } else {
            empty( self::$field ) || self::$field = preg_replace( '/[^0-9,a-zA-Z-|^_\x80-\xff ]+/i', '', trim( self::$field, ',' ) );
            if ( empty( self::$field ) ) {
                return false;
            } elseif ( strpos( self::$field, ',' ) !== false ) {
                $tmp = explode( ',', self::$field );
                foreach ( $tmp as $k => &$v ) {
                    if ( empty( $v ) ) {
                        unset( $tmp [ $k ] );
                    }
                }
            } else {
                $tmp = array( self::$field );
            }
        }
        return $tmp;
    }

    /**
     * 过滤 pid aid cityid
     * @return type 
     */
    private function city( $name = '城市' )
    {
        if ( self::$requiredField !== true ) {
            return self::$requiredField;
        }
        if ( !preg_match( '/^([\d+]{4})$/', self::$field ) ) {
            self::setErrorMessage( $name . '格式不正确' );
            return false;
        }
        return self::$field;
    }

    /**
     * sql注入语句过滤
     * @return boolean
     */
    private function sqlInjectionFilter()
    {
        $_pattern = array( '/waitfor%20delay/', '/waitfor delay/', '/select/i', '/\(/', '/\)/', '/insert/i', '/update/i', '/delete/i', '/\'/', '/\//', '/\.\.\//', '/\.\//', '/union/i', '/into/i', '/load_file/i', '/outfile/i', '/\'/', '/&#039;/', '/--/', '/%27/', '/%22/', '/%5c/i' );
        $_replacement = array( '', '', '\select', '（', '）', '\insert', '\update', '\delete', '\'', '\/', '\.\.\/', '\.\/', '\union', '\into', '\load_file', '\outfile', '‘', '‘', '——', '\\\'', '\"', '\\\\\\' );
        self::$field = preg_replace( $_pattern, $_replacement, self::$field );
        return true;
    }

}

class Input
{

    /**
     * 取get的参数
     * @param type $field
     * @param type $defaultValue
     * @return type 
     */
    public static function get( $field, $defaultValue = null )
    {
        $returnValue = '';
        if ( !empty( $_GET[ $field ] ) ) {
            $returnValue = $_GET[ $field ];
        } else if ( isset( $defaultValue ) ) {
            $returnValue = $defaultValue;
        }
        $filterModel = Filter::getInstance();
        $filterModel->setField( $returnValue );
        return $filterModel;
    }

    /**
     * 取post的参数
     * @param type $field
     * @param type $defaultValue
     * @return type 
     */
    public static function post( $field, $defaultValue = null )
    {
        $returnValue = '';
        if ( !empty( $_POST[ $field ] ) ) {
            $returnValue = $_POST[ $field ];
        } else if ( isset( $defaultValue ) ) {
            $returnValue = $defaultValue;
        }
        $filterModel = Filter::getInstance();
        $filterModel->setField( $returnValue );
        return $filterModel;
    }

    /**
     * 取cookie值
     * @param type $field
     * @param type $defaultValue
     * @return type 
     */
    public static function cookie( $field, $defaultValue = null )
    {
        $returnValue = '';
        if ( !empty( $_COOKIE[ $field ] ) ) {
            $returnValue = $_COOKIE[ $field ];
        } else if ( !empty( $defaultValue ) ) {
            $returnValue = $defaultValue;
        }
        $filterModel = Filter::getInstance();
        $filterModel->setField( $returnValue );
        return $filterModel;
    }

    /**
     * 直接赋值操作
     * $checkIn = Input::set($checkIn)->required('不能为空')->date();
     * if($checkIn===false)
     *   die(Filter::getErrorMessage());
     * @param type $value
     * @param type $defaultValue
     * @return type 
     */
    public static function set( $value, $defaultValue = null )
    {
        $returnValue = '';
        if ( !empty( $value ) ) {
            $returnValue = $value;
        } else if ( isset( $defaultValue ) ) {
            $returnValue = $defaultValue;
        }
        $filterModel = Filter::getInstance();
        $filterModel->setField( $returnValue );
        return $filterModel;
    }

}
