<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_MemberManage_www extends service_Member_base
{

    private $uid;
    private $purviewArray = '';
    private $class_map_id_array;
    private $subject_school_id_array;
    private $memberInfo;
    private $errorMessage;

    public function __construct()
    {
        parent::__construct();
    }

    public function setUid( $uid )
    {
        $this->uid = $uid;
        return $this;
    }

    public function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
        return $this;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 取用户所有权限
     * @return type
     */
    private function getPurviewArray()
    {
        if ( !empty( $this->purviewArray ) ) {
            return $this->purviewArray;
        }
        if ( !empty( $this->memberInfo ) && $this->memberInfo->member_type == service_Member_www::member_type_admin_type ) {
            return $this->purviewArray = $this->getAllPurviewArray();
        } else {
            $dao = dao_factory_base::getPurviewDao();
            $dao->setWhere( 'uid=' . $this->uid . ' AND is_delete=0' );
            return $this->purviewArray = $dao->getListByWhere();
        }
    }

    /**
     * 当用户是学校管理员时取出学校所有的班级和科目
     */
    private function getAllPurviewArray()
    {
        $class_map_dao = dao_factory_base::getClassMapDao();
        //取学校所有班级 
        $class_map_dao->setField( 'class_map_id,grade_name,class_name' );
        $where = "school_id={$this->memberInfo->school_id} AND term_id={$this->memberInfo->term_id} AND is_delete=0";
        $class_map_dao->setWhere( $where );
        $res = $class_map_dao->getListByWhere();
        //取学校所有的科目
        $subject_school_dao = dao_factory_base::getSubjectSchoolDao();
        $subject_school_dao->setField( 'subject_school_id,subject_name' );
        $where = "school_id={$this->memberInfo->school_id}";
        $subject_school_dao->setWhere( $where );
        $subject_school_res = $subject_school_dao->getListByWhere();
        //设置一个身份为学校管理员
        $result = array();
        foreach ( $res as $key => $value ) {
            foreach ( $subject_school_res as $vv ) {
                $rs = new StdClass;
                $rs->uid = $this->memberInfo->uid;
                $rs->class_map_id = $value->class_map_id;
                $rs->class_map_name = $value->grade_name . $value->class_name;
                $rs->subject_school_id = $vv->subject_school_id;
                $rs->subject_name = $vv->subject_name;
                $rs->identity_id = 7;
                $rs->identity_name = '学校管理员';
                $result[] = $rs;
            }
        }
        return ( Object ) $result;
    }

    /**
     * 取教师的列表
     * @param entity_parameter_MemberManage_base $entity_parameter_MemberManage_base
     * @return type
     */
    public function getTeacherListByPurview( entity_parameter_MemberManage_base $entity_parameter_MemberManage_base )
    {
        $dao = dao_factory_base::getMemberDao();
        $entity_parameter_MemberManage_base->setMember_type( service_Member_www::member_type_teacher_type );
        $res = $dao->getMemberByPurview( $entity_parameter_MemberManage_base );

        foreach ( $res as $key => $value ) {
            $this->memberInfo = '';
            $this->purviewArray = '';
            $purview = $this->getMemberPurview( $value->uid );
            $res[ $key ]->purview = $this->getPurviewTextFromObject( $purview );
        }
        return $res;
    }

    /**
     * 把权限对象转换成字符串
     * @param type $purview_object
     * @return string
     */
    private function getPurviewTextFromObject( $purview_object )
    {
        $return = array();

        foreach ( $purview_object AS $k => $v ) {
            $subject_text = '';
            $subject_count = empty( $v[ 'subject' ] ) ? 0 : count( $v[ 'subject' ] );
            if ( empty( $v[ 'subject' ] ) ) {
                continue;
            }
            foreach ( $v[ 'subject' ] AS $kk => $vv ) {
                $class_count = empty( $vv[ 'class' ] ) ? 0 : count( $vv[ 'class' ] );
                if ( $subject_count == 1 && $class_count <= 3 ) {
                    foreach ( $vv[ 'class' ] AS $kkk => $vvv ) {
                        $subject_text.= $vvv[ 'class_map_name' ] . ' ';
                    }
                } else {
                    $subject_text .= "{$class_count}个班级 {$subject_count}个科目";
                }

                $subject_text .= ' ' . $vv[ 'subject_name' ] . ' <br>';
            }

            $subject_text = substr( $subject_text, 0, -1 );
            $return[ $v[ 'identity_id' ] ] = $v[ 'identity_name' ] . '<br>' . $subject_text;
        }
        return $return;
    }

    /**
     * 取学生的列表
     * @param entity_parameter_MemberManage_base $entity_parameter_MemberManage_base
     * @return type
     */
    public function getStudentListByPurview( entity_parameter_MemberManage_base $entity_parameter_MemberManage_base )
    {
        $dao = dao_factory_base::getMemberDao();
        $entity_parameter_MemberManage_base->setMember_type( service_Member_www::member_type_student_type );

        $where = 'member_type= ' . service_Member_www::member_type_student_type;
        if ( $entity_parameter_MemberManage_base->getClass_map_id() != null ) {
            $where .= $dao->getWhereInStatement( ' AND class_map_id', $entity_parameter_MemberManage_base->getClass_map_id() );
        }
        if ( $entity_parameter_MemberManage_base->getQ() != null ) {
            $where .= " AND realname LIKE '%{$entity_parameter_MemberManage_base->getQ()}%' ";
        }

        $dao->setField( 'uid,username,realname,mobile,student_id,student_short_id,member_type,member_class,member_status,class_map_id,school_id' );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $student_identity_array = Tmac::config( 'member.member.student_identity_id', APP_WWW_NAME );
        $result = array();
        if ( $res ) {
            foreach ( $res as $key => $value ) {
                if ( !empty( $value->member_class ) ) {
                    $value->purview = $student_identity_array[ $value->member_class ];
                } else {
                    $value->purview = '';
                }
                $result[ $value->class_map_id ][] = $value;
            }
        }


        $class_map_id = '';
        foreach ( $result AS $key => $value ) {
            $class_map_id .= ',' . $key;
        }
        $class_map_id = substr( $class_map_id, 1 );
        $subject_array = $this->getSubjectByClassMapId( $class_map_id );
        $class_map_array = $this->getClassMapNameById( $class_map_id );
        $return = array();
        foreach ( $result AS $key => $value ) {
            $return[ $key ][ 'result' ] = $value;
            $return[ $key ][ 'subject' ] = empty( $subject_array[ $key ] ) ? '' : $subject_array[ $key ];
            $return[ $key ][ 'class_map_name' ] = empty( $class_map_array[ $key ][ 0 ] ) ? '' : $class_map_array[ $key ][ 0 ];
        }
        return $return;
    }

    private function getSubjectByClassMapId( $class_map_id )
    {
        $dao = dao_factory_base::getSubjectDao();
        $dao->setWhere( 'class_map_id IN(' . $class_map_id . ') AND is_delete=0' );
        $dao->setField( 'class_map_id,subject_school_id,subject_name' );
        $res = $dao->getListByWhere();
        $return = array();
        if ( $res ) {
            foreach ( $res as $key => $value ) {
                $return[ $value->class_map_id ][] = $value;
            }
        }
        return $return;
    }

    /**
     * 通过class_map_id串来取班级名称
     * @param type $class_map_id
     */
    private function getClassMapNameById( $class_map_id )
    {
        $dao = dao_factory_base::getClassMapDao();
        $sql = $dao->getWhereInStatement( 'class_map_id', $class_map_id );
        $dao->setWhere( $sql );
        $res = $dao->getListByWhere();
        $return = array();
        if ( $res ) {
            foreach ( $res AS $k => $v ) {
                $return[ $v->class_map_id ][] = $v->grade_name . $v->class_name;
            }
        }
        return $return;
    }

    /**
     * 取用户的所有身份，以及身份下的班级，科目的关系
     * @param type $uid
     * @return type
     */
    public function getMemberPurview( $uid )
    {
        $this->uid = $uid;
        $res = $this->getPurviewArray();
        $return = array();
        foreach ( $res AS $k => $v ) {
            $return[ $v->identity_id ][ 'identity_id' ] = $v->identity_id;
            $return[ $v->identity_id ][ 'identity_name' ] = $v->identity_name;
            if ( $v->identity_id == service_member_www::identity_head_principal ) {
                continue;
            }
            $return[ $v->identity_id ][ 'class_map' ][ $v->class_map_id ][ 'class_map_id' ] = $v->class_map_id;
            $return[ $v->identity_id ][ 'class_map' ][ $v->class_map_id ][ 'class_map_name' ] = $v->class_map_name;
            $return[ $v->identity_id ][ 'class_map' ][ $v->class_map_id ][ 'subject' ][ $v->subject_school_id ] = array(
                'subject_school_id' => $v->subject_school_id,
                'subject_name' => $v->subject_name
            );
            $return[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'subject_school_id' ] = $v->subject_school_id;
            $return[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'subject_name' ] = $v->subject_name;
            $return[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'class' ][ $v->class_map_id ] = array(
                'class_map_id' => $v->class_map_id,
                'class_map_name' => $v->class_map_name
            );
        }
        return $return;
    }

    /**
     * 挑选出用户顺位最高的身份   这个身份的所有班级/科目范围
     * @param type $uid
     * @return type
     */
    public function getHighLevelPurview()
    {
        $purview_array = $this->getMemberPurview( $this->uid );
        krsort( $purview_array );
        foreach ( $purview_array AS $value ) {
            return $value;
        }
    }

    /**
     * 取用户顺位最高的身份 班级/科目的intString格式的ID
     * @param type $uid
     * @return type
     */
    public function getHighLevelPurviewIntString()
    {
        $identity_array = $this->getHighLevelPurview();
        $subject_school_id_array = $subject_class_array = $class_map_id_array = array();
        foreach ( $identity_array[ 'subject' ] as $value ) {
            $subject_count = count( $value[ 'class' ] );
            $subject_school_id_array[ $subject_count ] = $value;
        }
        krsort( $subject_school_id_array );
        foreach ( $subject_school_id_array AS $value ) {
            $subject_class_array = $value;
            break;
        }
        $class_name = '';
        foreach ( $subject_class_array[ 'class' ] as $value ) {
            $class_map_id_array[] = $value[ 'class_map_id' ];
            $class_name .= ',' . $value[ 'class_map_name' ];
        }
        $class_count = count( $subject_class_array[ 'class' ] );
        if ( $class_count > 3 ) {
            $subject_class_string = $subject_class_array[ 'subject_name' ] . ':(' . $class_count . '个班)';
        } else {
            $subject_class_string = $subject_class_array[ 'subject_name' ] . ':(' . substr( $class_name, 1 ) . ')';
        }
        return array(
            'class_map_id' => implode( ',', $class_map_id_array ),
            'subject_school_id' => $subject_class_array[ 'subject_school_id' ],
            'subject_class_string' => $subject_class_string
        );
    }

    /**
     * 设置学生课代表或信息委员身份
     * @param entity_Purview_base $entity_Purview_base
     * @return boolean
     */
    public function setStudentPurview( entity_Purview_base $entity_Purview_base )
    {
        $member_dao = dao_factory_base::getMemberDao();
        $purview_dao = dao_factory_base::getPurviewDao();

        $member_dao->getDb()->startTrans();
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->member_class = $entity_Purview_base->identity_id;
        $member_dao->setPk( $entity_Purview_base->uid );
        $member_dao->updateByPk( $entity_Member_base );

        $where = "uid={$entity_Purview_base->uid} AND class_map_id={$entity_Purview_base->class_map_id} AND subject_school_id={$entity_Purview_base->subject_school_id} AND identity_id={$entity_Purview_base->identity_id}";
        $purview_dao->setWhere( $where );
        $purview_result = $purview_dao->getInfoByWhere();
        if ( $purview_result == false ) {
            $class_map_dao = dao_factory_base::getClassMapDao();
            $class_map_dao->setPk( $entity_Purview_base->class_map_id );
            $class_map_info = $class_map_dao->getInfoByPk();

            $entity_Purview_base->class_map_name = $class_map_info->grade_name . $class_map_info->class_name;

            $subject_dao = dao_factory_base::getSubjectSchoolDao();
            $subject_dao->setPk( $entity_Purview_base->subject_school_id );
            $subject_info = $subject_dao->getInfoByPk();
            $entity_Purview_base->subject_name = $subject_info->subject_name;

            $identity_array = Tmac::config( 'member.member.student_identity_id', APP_WWW_NAME );
            $entity_Purview_base->identity_name = $identity_array[ $entity_Purview_base->identity_id ];

            $purview_dao->insert( $entity_Purview_base );
        }

        if ( $member_dao->getDb()->isSuccess() ) {
            $member_dao->getDb()->commit();
            return true;
        } else {
            $member_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 通过用户取用户权限的下的所有班级和科目
     * @param type $uid
     * @return type
     */
    public function getMemberClassAndSubjectByUid( $class_map_id_array = array(), $subject_school_id = 0 )
    {
        $res = $this->getPurviewArray();
        $return = array();
        foreach ( $res AS $k => $v ) {
            $return[ 'class_map' ][ $v->class_map_id ][ 'class_map_id' ] = $v->class_map_id;
            $return[ 'class_map' ][ $v->class_map_id ][ 'class_map_name' ] = $v->class_map_name;
            $selected = false;
            if ( !empty( $class_map_id_array ) && in_array( $v->class_map_id, $class_map_id_array ) ) {
                $selected = true;
            }
            $return[ 'class_map' ][ $v->class_map_id ][ 'selected' ] = $selected;
            $return[ 'subject' ][ $v->subject_school_id ][ 'subject_school_id' ] = $v->subject_school_id;
            $return[ 'subject' ][ $v->subject_school_id ][ 'subject_name' ] = $v->subject_name;
            $selected = false;
            if ( $subject_school_id == $v->subject_school_id ) {
                $selected = true;
            }
            $return[ 'subject' ][ $v->subject_school_id ][ 'selected' ] = $selected;
        }
        return $return;
    }

    /**
     * 通过用户取用户权限的下的所有班级和科目 交差结果 带seleceed状态
     * @param type $uid
     * @return boolean
     */
    public function getMemberClassAndSubjectCrossSelectArrayByUid( $class_map_id_array = array(), $subject_school_id = 0 )
    {
        $res = $this->getPurviewArray();
        $return = array();
        foreach ( $res AS $k => $v ) {
            $return[ 'class_map' ][ $v->class_map_id ][ 'class_map_id' ] = $v->class_map_id;
            $return[ 'class_map' ][ $v->class_map_id ][ 'class_map_name' ] = $v->class_map_name;
            $return[ 'class_map' ][ $v->class_map_id ][ 'subject' ][ $v->subject_school_id ] = array(
                'subject_school_id' => $v->subject_school_id,
                'subject_name' => $v->subject_name
            );

            $selected = false;
            if ( !empty( $class_map_id_array ) && in_array( $v->class_map_id, $class_map_id_array ) ) {
                $selected = true;
            }
            $return[ 'class_map' ][ $v->class_map_id ][ 'selected' ] = $selected;


            $return[ 'subject' ][ $v->subject_school_id ][ 'subject_school_id' ] = $v->subject_school_id;
            $return[ 'subject' ][ $v->subject_school_id ][ 'subject_name' ] = $v->subject_name;
            $return[ 'subject' ][ $v->subject_school_id ][ 'class' ][ $v->class_map_id ] = array(
                'class_map_id' => $v->class_map_id,
                'class_map_name' => $v->class_map_name
            );
            $selected = false;
            if ( $subject_school_id == $v->subject_school_id ) {
                $selected = true;
            }
            $return[ 'subject' ][ $v->subject_school_id ][ 'selected' ] = $selected;
        }
        return $return;
    }

    /**
     * 通过用户取用户权限的下的所有班级和科目 交差结果
     * @param type $uid
     * @return boolean
     */
    public function getMemberClassAndSubjectCrossArrayByUid( $uid )
    {
        $this->uid = $uid;
        $res = $this->getPurviewArray();
        $return = array();
        foreach ( $res AS $k => $v ) {
            $return[ 'class_map' ][ $v->class_map_id ][ 'class_map_id' ] = $v->class_map_id;
            $return[ 'class_map' ][ $v->class_map_id ][ 'class_map_name' ] = $v->class_map_name;
            $return[ 'class_map' ][ $v->class_map_id ][ 'subject' ][ $v->subject_school_id ] = array(
                'subject_school_id' => $v->subject_school_id,
                'subject_name' => $v->subject_name
            );
            $return[ 'subject' ][ $v->subject_school_id ][ 'subject_school_id' ] = $v->subject_school_id;
            $return[ 'subject' ][ $v->subject_school_id ][ 'subject_name' ] = $v->subject_name;
            $return[ 'subject' ][ $v->subject_school_id ][ 'class' ][ $v->class_map_id ] = array(
                'class_map_id' => $v->class_map_id,
                'class_map_name' => $v->class_map_name
            );
        }
        return $return;
    }

    /**
     * 通过用户取用户权限的下的所有班级和科目 的字符串 交差结果
     * @return type
     */
    public function getMemberClassAndSubjectCrossStringByUid()
    {
        $array = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );

        $class_map_id_array = array();
        $subject_school_id_array = array();
        foreach ( $array[ 'class_map' ] AS $class_map ) {
            $class_map_id_array[] = $class_map[ 'class_map_id' ];
        }
        foreach ( $array[ 'subject' ] AS $subject ) {
            $subject_school_id_array[] = $subject[ 'subject_school_id' ];
        }
        $return = array(
            'class_map_id' => implode( ',', $class_map_id_array ),
            'subject_school_id' => implode( ',', $subject_school_id_array )
        );
        return $return;
    }

    /**
     * 检测用户对班级和科目的权限,返回用户有权限的所有班级idString，所有科目idString
     * @param type $uid
     * @param type $class_map_id_array
     * @param type $subject_school_id
     */
    public function checkMemberPurviewOfClassAndSubject( $uid, $class_map_id, $subject_school_id = 0 )
    {
        $class_map_id_array = explode( ',', $class_map_id );
        $subject_school_id_array = explode( ',', $subject_school_id );
        $this->uid = $uid;
        $res = $this->getMemberClassAndSubjectByUid( $class_map_id_array, $subject_school_id );
        if ( empty( $res ) ) {
            return false;
        }
        $class_map_id_result = array();
        foreach ( $res[ 'class_map' ] as $key => $value ) {
            if ( in_array( $key, $class_map_id_array ) ) {
                $class_map_id_result[] = $key;
            }
        }

        $subject_id_result = array();
        foreach ( $res[ 'subject' ] as $key => $value ) {
            if ( in_array( $key, $subject_school_id_array ) ) {
                $subject_id_result[] = $key;
            }
        }

        if ( empty( $class_map_id_result ) ) {
            return false;
        }

        if ( empty( $subject_id_result ) && !empty( $subject_school_id ) ) {
            return false;
        }


        return array(
            'class_map_id' => implode( ',', $class_map_id_result ),
            'subject_school_id' => implode( ',', $subject_id_result )
        );
    }

    /**
     * 判断用户对$class_map_id的$subject_school_id有没有权限
     * 必须对$class_map_id和$subject_school_id有权限，严格的验证两个参数
     * @param type $uid
     * @param type $class_map_id
     * @param type $subject_school_id
     * @return boolean
     */
    public function checkMemberClassAndSubjectPurview( $class_map_id, $subject_school_id )
    {
        $res = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );
        if ( empty( $res[ 'class_map' ][ $class_map_id ][ 'subject' ][ $subject_school_id ] ) ) {
            $this->errorMessage = '您不拥有' . $this->getClassName( $class_map_id ) . '，' . $this->getSubjectName( $subject_school_id ) . '的权限';
            return false;
        }
        return true;
    }

    /**
     * 检测用户对班级$class_map_id和$exam_subject_id是否有权限
     * @param type $class_map_id
     * @param type $exam_subject_id
     * @return boolean
     */
    public function checkMemberClassAndExamSubjectCrossPurview( $class_map_id, $exam_subject_id )
    {
        $exam_subject_dao = dao_factory_base::getExamSubjectDao();
        $exam_subject_dao->setPk( $exam_subject_id );
        $exam_subject_dao->setField( 'subject_school_id' );
        $exam_subject_info = $exam_subject_dao->getInfoByPk();
        if ( $exam_subject_info == false ) {
            $this->errorMessage = '考试科目不存在';
            return false;
        }
        return self::checkMemberClassAndSubjectCrossPurview( $class_map_id, $exam_subject_info->subject_school_id );
    }

    /**
     * 判断用户对$class_map_id有没有权限     
     * @param type $uid
     * @param type $class_map_id     
     * @return boolean
     */
    public function checkMemberClassPurview( $class_map_id )
    {
        $res = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );
        $class_map_id_array = $this->class_map_id_array = explode( ',', $class_map_id );
        foreach ( $class_map_id_array AS $class_map_id ) {
            if ( empty( $res[ 'class_map' ][ $class_map_id ] ) ) {
                $this->errorMessage = '您不拥有' . $this->getClassName( $class_map_id ) . '的权限';
                return false;
            }
        }
        return true;
    }

    /**
     * 判断用户是否对 班级/科目 矩阵 权限的判断，如果对 班级X科目 中任何一个没有权限就返回False
     * @param type $uid
     * @param type $class_map_id
     * @param type $subject_school_id
     * @return boolean
     */
    public function checkMemberClassAndSubjectCrossPurview( $class_map_id, $subject_school_id )
    {
        $class_map_id_array = $this->class_map_id_array = explode( ',', $class_map_id );
        $subject_school_id_array = $this->subject_school_id_array = explode( ',', $subject_school_id );
        $res = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );
        foreach ( $class_map_id_array AS $class_map_id ) {
            if ( empty( $res[ 'class_map' ][ $class_map_id ] ) ) {
                $this->errorMessage = '您不拥有' . $this->getClassName( $class_map_id ) . '班的权限';
                return false;
            }
            foreach ( $subject_school_id_array as $subject_school_id ) {
                if ( empty( $res[ 'class_map' ][ $class_map_id ][ 'subject' ][ $subject_school_id ] ) ) {
                    $this->errorMessage = '您不拥有' . $res[ 'class_map' ][ $class_map_id ][ 'class_map_name' ] . '，' . $this->getSubjectName( $subject_school_id ) . '的权限';
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 检测用户对$class_map_id班级的权限（规则，只要是本年级的班级权限就行）$subject_school_id科目的权限（规则，如果不为空。则必须拥有此科目的权限.如果为空，则有任何科目权限都视为有科目权限）
     * 函数会判断当前正在进行的学期内的合法班级$class_map_id
     * @param type $class_map_id
     * @param type $subject_school_id
     * @return array('grade_id'=>$grade_id,'term_id'=>$term_id 年级ID，学期ID
     */
    public function checkMemberGradeAndSubjectPurview( $class_map_id, $subject_school_id = 0 )
    {
        $dao = dao_factory_base::getClassMapDao();
        $dao->setField( 'grade_id,term_id' );
        $where = $dao->getWhereInStatement( 'class_map_id', $class_map_id );
        $dao->setWhere( $where );
        $class_map_array = $dao->getListByWhere();
        if ( empty( $class_map_array ) ) {
            $this->errorMessage = '班级不存在';
            return false;
        }
        $grade_id_array = array();
        foreach ( $class_map_array AS $class_map ) {
            $grade_id_array[ $class_map->grade_id ] = $class_map->grade_id;
            $grade_id = $class_map->grade_id;
            $term_id = $class_map->term_id;
        }
        if ( count( $grade_id_array ) > 1 ) {
            $this->errorMessage = '不能跨年级选择班级';
            return false;
        }

        $dao->setWhere( "grade_id={$grade_id}" );
        $dao->setField( 'class_map_id' );
        $class_map_array = $dao->getListByWhere();
        $grade_status = false;
        $res = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );
        foreach ( $class_map_array AS $class_map ) {
            if ( !empty( $res[ 'class_map' ][ $class_map->class_map_id ] ) ) {//如果在本年级中找到用户身份中的班级权限就 可以确认对本年级有权限
                $grade_status = true;
                break;
            }
        }
        if ( $grade_status == false ) {
            $this->errorMessage = '没有任何班级权限';
            return false;
        }
        if ( empty( $subject_school_id ) ) {
            if ( count( $res[ 'subject' ] ) == 0 ) {
                $this->errorMessage = '没有任何科目权限';
                return false;
            }
        } else {
            if ( empty( $res[ 'subject' ][ $subject_school_id ] ) ) {//如果在本年级中找到用户身份中的班级权限就 可以确认对本年级有权限                                
                $subject_school_model = Tmac::model( 'SubjectSchool', APP_WWW_NAME );
                $subject_school_model instanceof service_SubjectSchool_www;
                $subject_school_info = $subject_school_model->getSubjectSchoolById( $subject_school_id );
                if ( $subject_school_info == false ) {
                    $subject_school_name = $subject_school_id;
                } else {
                    $subject_school_name = $subject_school_info->subject_name;
                }
                $this->errorMessage = '没有科目' . $subject_school_name . '的权限';
                return false;
            }
        }
        return array('grade_id' => $grade_id, 'term_id' => $term_id);
    }

    /**
     * 取用户身份下的所有默认的多个班级和一个科目的intString
     * @param type $uid
     * @return boolean
     */
    public function getMemberDefaultClassAndSubjectIntStringByUid()
    {
        $res = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );
        if ( empty( $res ) ) {
            $this->errorMessage = '您身份下没有班级权限';
            return false;
        }

        $subject_school_id_array = $subject_class_array = $class_map_id_array = array();
        foreach ( $res[ 'subject' ] as $value ) {
            $subject_count = count( $value[ 'class' ] );
            $subject_school_id_array[ $subject_count ] = $value;
        }
        krsort( $subject_school_id_array );
        foreach ( $subject_school_id_array AS $value ) {
            $subject_class_array = $value;
            break;
        }
        foreach ( $subject_class_array[ 'class' ] as $value ) {
            $class_map_id_array[] = $value[ 'class_map_id' ];
        }
        return array(
            'class_map_id' => implode( ',', $class_map_id_array ),
            'subject_school_id' => $subject_class_array[ 'subject_school_id' ]
        );
    }

    /**
     * 通过￥uid取用户所有的科目数组
     * @param type $uid
     * @return type
     */
    public function getMemberSubjectArrayByUid( $uid )
    {
        $dao = dao_factory_base::getPurviewDao();
        $dao->setWhere( 'uid=' . $uid . ' AND is_delete=0 GROUP BY subject_school_id ' );
        $dao->setField( 'class_map_id,class_map_name,subject_school_id,subject_name' );
        return $res = $dao->getListByWhere();
    }

    /**
     * 通过$class_map_id和subject_school_id取到 英语（高三：(1)班,(2)班,(3)班）数学（高三：(1)班,(2)班,(3)班）这种类型的 
     * @param type $class_map_id
     * @param type $subject_school_id
     */
    public function getSubjectGradeClassNameString()
    {
        $class_map_id_array = $this->class_map_id_array;
        $subject_school_id_array = $this->subject_school_id_array;
        $res = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );

        $class_name = '';
        $subject_class_string = '';
        foreach ( $class_map_id_array as $value ) {
            $class_name .= ',' . $res[ 'class_map' ][ $value ][ 'class_map_name' ];
        }
        $class_name_string = ':(' . substr( $class_name, 1 ) . ')';
        foreach ( $subject_school_id_array as $value ) {
            $subject_class_string .= $res[ 'subject' ][ $value ][ 'subject_name' ] . $class_name_string . ' ';
        }

        return $subject_class_string;
    }

    private function getClassName( $class_map_id )
    {
        $class_map_model = Tmac::model( 'ClassMap', APP_WWW_NAME );
        $class_map_model instanceof service_ClassMap_www;
        $class_map_info = $class_map_model->getClassMapById( $class_map_id );
        if ( $class_map_info == false ) {
            $class_map_name = $class_map_id;
        } else {
            $class_map_name = $class_map_info->grade_name . $class_map_info->class_name;
        }
        return $class_map_name;
    }

    private function getSubjectName( $subject_school_id )
    {
        $subject_school_model = Tmac::model( 'SubjectSchool', APP_WWW_NAME );
        $subject_school_model instanceof service_SubjectSchool_www;
        $subject_school_info = $subject_school_model->getSubjectSchoolById( $subject_school_id );
        if ( $subject_school_info == false ) {
            $subject_school_name = $subject_school_id;
        } else {
            $subject_school_name = $subject_school_info->subject_name;
        }
        return $subject_school_name;
    }

    /**
     * 取一个班的 所有科目对应老师的
     * @param type $class_map_id
     * @param type $subject_school_id
     */
    public function getSubjectUidArrayMap( $class_map_id )
    {
        $dao = dao_factory_base::getPurviewDao();
        $dao->setField( 'uid,subject_school_id,identity_id' );
        $identity_id = service_Member_www::identity_instructor;
        $where = "class_map_id={$class_map_id} AND identity_id={$identity_id} AND is_delete=0";
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        $result = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $result[ $value->subject_school_id ] = $value->uid;
            }
        }
        return $result;
    }

    /**
     * 通过class_map_array和subject_array返回 两个数组中都符合权限的
     * 主要是给api中使用
     * @param type $class_map_id_array
     * @param type $subject_school_id_array
     * @return type
     */
    public function getSubjectClassMapCrossArray( $class_map_id_array, $subject_school_id_array )
    {        
        $res = $this->getMemberClassAndSubjectCrossArrayByUid( $this->uid );

        foreach ( $class_map_id_array AS $key => $class_map_info ) {
            $class_map_id = $class_map_info[ 'id' ];
            if ( empty( $res[ 'class_map' ][ $class_map_id ] ) ) {
                unset( $class_map_id_array[ $key ] );
            }
            foreach ( $subject_school_id_array as $kk => $subject_school_info ) {
                $subject_school_id = $subject_school_info[ 'id' ];
                if ( empty( $res[ 'class_map' ][ $class_map_id ][ 'subject' ][ $subject_school_id ] ) ) {
                    unset( $subject_school_id_array[ $kk ] );
                }
            }
        }

        $return = array(
            'class_map' => $class_map_id_array,
            'subject'=>$subject_school_id_array
        );
        return $return;
    }

}
