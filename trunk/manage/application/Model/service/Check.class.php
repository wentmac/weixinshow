<?php

/**
 * API 用户验证 模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Check.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Check_www extends Model
{

    private $errorMessage;

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function setErrorMessage( $errorMessage )
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function checkStudent( $uid )
    {
        return $this->checkMemberType($uid, service_Member_www::member_type_student_type);
    }

    public function checkMemberType( $uid, $member_type )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setWhere( "uid={$uid}" );
        $memberInfo = $dao->getInfoByWhere();
        if ( $memberInfo == false ) {
            $this->setErrorMessage( '会员不存在' );
            return false;
        }
        if ( $memberInfo->member_type != $member_type ) {
            $this->setErrorMessage( '用户非学生' );
            return false;
        }
        return $memberInfo;
    }

}
