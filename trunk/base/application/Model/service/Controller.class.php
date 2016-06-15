<?php

/**
 * 接口 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Controller.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
abstract class service_Controller_base extends Action
{

    protected $errorMessage;
    protected $memberInfo;
    protected $loginUrl;

    function setLoginUrl( $loginUrl )
    {
        $this->loginUrl = $loginUrl;
    }

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
        $this->loginUrl = INDEX_URL . 'index.php?m=account.login';
    }

    /**
     * 取account_base类中的错误信息
     * @return type 
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 设置验证出错消息
     * @author zhangwentao
     * @param type $message 
     */
    protected function setErrorMessage( $message )
    {
        $this->errorMessage = $message;
    }

    /**
     * 检测是否登录
     */
    protected function checkLogin()
    {
        $uid = Input::cookie( 'uid', 0 )->required( '用户UID不能为空' )->int();
        $token = Input::cookie( 'token', '' )->required( '用户验证密钥不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            $this->headerRedirect( $this->loginUrl );
            exit();
        }
        $checkEffective = self::checkMemberStatus( $uid );
        if ( $checkEffective === false ) {
            $this->redirect( self::getErrorMessage() );
            exit();
        }
        if ( $token <> md5( md5( $this->memberInfo->password ) . $this->memberInfo->salt ) ) {
            $this->headerRedirect( $this->loginUrl );
            exit();
        }
        return true;
    }

    /**
     * 返回登录状态
     */
    protected function checkLoginStatus()
    {
        $uid = Input::cookie( 'uid', 0 )->required( '用户UID不能为空' )->int();
        $token = Input::cookie( 'token', '' )->required( '用户验证密钥不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            return false;
        }        
        $checkEffective = self::checkMemberStatus( $uid );
        if ( $checkEffective === false ) {
            return false;
        }
        if ( $token <> md5( md5( $this->memberInfo->password ) . $this->memberInfo->salt ) ) {
            return false;
        }
        return true;
    }

    /**
     * 检查用户状态
     * @return type 
     */
    protected function checkMemberStatus( $uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $uid );
        $result = $dao->getInfoByPk();
        if ( !$result ) {
            self::setErrorMessage( '用户不存在' );
            return false;
        }
        $this->memberInfo = $result;
        return true;
    }

    /**
     * Api 返回值函数
     * @param type $data
     * @param type $debug
     * @param type $format 
     */
    protected function apiReturn( $data = array(), $debug = 0, $format = 'json', $callback = null )
    {
        $return = array(
            'status' => 0,
            'success' => true,
            'data' => $data
        );
        if ( $debug == 1 ) {
            header( "Content-type: text/html; charset=utf-8" );
            echo '<pre>';
            print_r( $return );
            echo '</pre>';
        } else {
            if ( $format == 'json' ) {
                header( "Content-type: application/json; charset=utf-8" );
                echo json_encode( $return, JSON_UNESCAPED_UNICODE );
            } else if ( $format == 'jsonp' ) {
                header( "Content-type: application/json; charset=utf-8" );
                echo $callback . "(" . json_encode( $return, JSON_UNESCAPED_UNICODE ) . ")";
            } else if ( $format == 'xml' ) {
                header( 'Content-Type: text/xml; charset=utf-8' );
                $xml = array2xml::createXML( $callback, $data );
                echo $xml->saveXML();
            }
        }
    }

    /**
     * 接口请求接口时返回
     * @param type $res 
     */
    protected function apiPostReturn( $res )
    {
        header( "Content-type: application/json; charset=utf-8" );
        echo $res;
        exit();
    }

    /**
     * 取用户背景图片
     * @param type $imageId
     * @param type $size
     * @return type 
     */
    protected function getImage( $imageId, $size, $type = 'article' )
    {
        if ( empty( $imageId ) ) {
            return '';
        }
        if ( empty( $size ) ) {
            return IMAGE_URL . 'article/' . $imageId . '.jpg';
        }
        return THUMB_URL . $type . '_' . $size . '/' . $imageId . '.jpg';
    }

    /**
     * 定义404页面
     */
    protected function nofound()
    {
        header( 'HTTP/1.0 404 Not Found' );
        $this->V( '404' );
        exit;
    }

}
