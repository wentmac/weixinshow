<?php

/**
 * WEB 检测member表中的字段是否已经存在
 * 主要是 username mobile email student_id student_short_id 这几个字段
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_MemberCheck_www extends service_Member_base
{

    private $dao;
    private $errorMessage;

    public function __construct()
    {
        parent::__construct();
        $this->dao = dao_factory_base::getMemberDao();
    }

    public function setErrorMessage( $errorMessage )
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * 检测用户用户名是否存在（在不为空的情况下）
     * @param type $username
     * @return boolean
     */
    public function checkUsernameIsExist( $username )
    {
        if ( empty( $username ) ) {
            return true;
        }
        $this->dao->setField( 'uid' );
        $this->dao->setWhere( "username='{$username}'" );
        $this->dao->setLimit( 1 );
        $res = $this->dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

    /**
     * 检测用户手机号是否存在（在不为空的情况下）
     * @param type $mobile
     * @return boolean
     */
    public function checkMobielIsExist( $mobile )
    {
        if ( empty( $mobile ) ) {
            return true;
        }
        $this->dao->setField( 'uid' );
        $this->dao->setWhere( "mobile='{$mobile}'" );
        $this->dao->setLimit( 1 );
        $res = $this->dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

    /**
     * 检测用户邮箱是否存在（在不为空的情况下）
     * @param type $email
     * @return boolean
     */
    public function checkEmailIsExist( $email )
    {
        if ( empty( $email ) ) {
            return true;
        }
        $this->dao->setField( 'uid' );
        $this->dao->setWhere( "email='{$email}'" );
        $this->dao->setLimit( 1 );
        $res = $this->dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

    /**
     * 检测学生的长学号是否重复
     * @param type $school_id
     * @param type $class_map_id
     * @param type $student_id
     */
    public function checkStudentIdIsExist( $school_id, $class_map_id, $student_id )
    {
        $this->dao->setField( 'uid' );
        $this->dao->setWhere( "school_id={$school_id} AND member_type=" . service_Member_www::member_type_student_type . " AND student_id={$student_id}" );
        $this->dao->setLimit( 1 );
        $res = $this->dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

    /**
     * 检测学生短学号是否存在
     * @param type $school_id
     * @param type $class_map_id
     * @param type $student_short_id
     * @return boolean
     */
    public function checkStudentShortIdIsExist( $school_id, $class_map_id, $student_short_id )
    {
        $this->dao->setField( 'uid' );
        $this->dao->setWhere( "school_id={$school_id} AND member_type=" . service_Member_www::member_type_student_type . " AND class_map_id={$class_map_id} AND student_short_id={$student_short_id}" );
        $this->dao->setLimit( 1 );
        $res = $this->dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

    /**
     * 检测老师长学号是否存在
     * @param type $school_id
     * @param type $student_id
     * @return boolean
     */
    public function checkTeacherStudentIdIsExist( $school_id, $student_id )
    {
        $this->dao->setField( 'uid' );
        $this->dao->setWhere( "school_id={$school_id} AND member_type=" . service_Member_www::member_type_teacher_type . " AND student_id={$student_id}" );
        $this->dao->setLimit( 1 );
        $res = $this->dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

}
