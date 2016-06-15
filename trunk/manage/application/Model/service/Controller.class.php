<?php

/**
 * WEB后台 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Controller.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
abstract class service_Controller_manage extends service_Controller_base
{

    protected $is_maller;

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
        $configcache = Tmac::config( 'configcache.config', APP_WWW_NAME, '.inc.php' );
        $this->assign( 'config', $configcache );
    }

    /**
     * 检测是否登录
     */
    protected function checkLogin()
    {
        $uid = Input::cookie( 'uid', 0 )->required( '用户UID不能为空' )->int();
        $token = Input::cookie( 'token', '' )->required( '用户验证密钥不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            $this->redirect( '请你登录系统', MOBILE_URL . PHP_SELF . '?m=account.login' );
        }
        $checkEffective = parent::checkMemberStatus( $uid );
        if ( $checkEffective === false ) {
            $this->redirect( self::getErrorMessage() );
        }
        if ( $token <> md5( md5( $this->memberInfo->password ) . $this->memberInfo->salt ) ) {
            $this->redirect( '认证失败，请先登录', MOBILE_URL . PHP_SELF . '?m=account.login' );
        }
        if ( $this->memberInfo->member_type == service_Member_base::member_type_buyer ) {
            $this->redirect( '分销商/供应商的用户身份才能登录系统，请联系银品惠客服' );
        }
        $this->setManageVariable();
        //超级管理员账户,日后注册掉
        $this->handleSuperManager( $uid );
        return true;
    }

    /**
     * manage控制台设置变量
     */
    protected function setManageVariable()
    {
        $is_supplier = $this->is_maller = false;
        if ( $this->memberInfo->member_type == service_Member_base::member_type_supplier ) {
            $is_supplier = true;
        }
        if ( $this->memberInfo->member_type == service_Member_base::member_type_mall ) {
            $this->is_maller = true;
        }
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->memberInfo->uid );
        $memberSettingInfo = $dao->getInfoByPk();

        $member_class_array = Tmac::config( 'member.member.member_class', APP_BASE_NAME );

        $this->assign( 'is_supplier', $is_supplier );
        $this->assign( 'is_maller', $this->is_maller );
        $this->assign( 'memberInfo', $this->memberInfo );
        $this->assign( 'memberSettingInfo', $memberSettingInfo );
        $this->assign( 'member_type_class_text', $member_class_array[ $this->memberInfo->member_type ][ $this->memberInfo->member_class ] );
    }

    /**
     * 处理超级管理员用户
     * @param type $uid
     */
    protected function handleSuperManager( $uid )
    {
        $other_uid = Input::get( 'other_uid', 0 )->int();
        $other_uid_cookie = Input::cookie( 'other_uid', 0 )->int();

        if ( empty( $other_uid ) && empty( $other_uid_cookie ) ) {
            return true;
        }
        if ( $uid <> 46 ) {
            return true;
        }

        if ( empty( $other_uid ) ) {
            $other_uid = $other_uid_cookie;
        } else {
            $expire_time = 1000;
            setcookie( 'other_uid', $other_uid, $this->now + $expire_time, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        }
        $checkEffective = parent::checkMemberStatus( $other_uid );
        if ( $checkEffective === false ) {
            $this->redirect( self::getErrorMessage() );
        }
        $this->setManageVariable();
        return true;
    }

    public function no( $title = '' )
    {
        $array[ 'title' ] = $title;
        $this->assign( $array );
        $this->V( '404' );
        exit();
    }

}
