<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Member_www extends service_Member_base
{

    const member_type_student_type = 1;  //学生用户类
    const member_type_parent_type = 2;   //家长用户类型
    const member_type_teacher_type = 3;   //老师用户类型
    const member_type_admin_type = 4;    //管理员用户类型
    const member_class_father_class = 1;  //父亲子分类ID
    const member_class_mother_class = 2;  //母亲子分类ID
    /**
     * 任课教师
     */
    const identity_instructor = 1;

    /**
     * 班主任
     */
    const identity_head_teacher = 2;

    /**
     * 课程组组长
     */
    const identity_team_leader = 3;

    /**
     * 年级组长
     */
    const identity_senior_leader = 4;

    /**
     * 教务主任
     */
    const identity_head_dean = 5;

    /**
     * 校长
     */
    const identity_head_principal = 6;

    /**
     * 课代表
     */
    const student_identity_representative = 1;

    /**
     * 信息委员
     */
    const student_identity_information = 2;

    private $errorMessage;

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getStudentAndParentsInfo( $term_id, $query = '' )
    {
        //先通过学期返回所有的班级
        $classmap_model = Tmac::model( 'ClassMap' );
        $classmap_model instanceof service_ClassMap_www;
        $class_map_info = $classmap_model->getClassMapByTermid( $term_id );
        if ( empty( $class_map_info ) ) {
            return false;
        }
        $class_map_ids = '';

        foreach ( $class_map_info as $v ) {
            $class_map_ids .= $v->class_map_id . ",";
        }
        $class_map_ids = substr( $class_map_ids, 0, -1 );
        $student_list = $this->getStudentByclassmapids( $class_map_ids, $query );
        if ( empty( $student_list ) ) {
            return false;
        }

        $parent_list = $this->getParentsInfoByStudent( $student_list );
        $new_student_list = $this->handleNewStudnetCard($student_list,$parent_list);
        
//        $new_student_list = array_merge( $student_list, $parent_list );
//        return $this->handleStudentInfo( $new_student_list );
        return $new_student_list;
    }
    
    public function handleNewStudnetCard($student_list,$parent_list){
        $father_str = '';
        $mother_str = '';
        foreach($parent_list as $k=>$v){
            if($v->member_class == 2){
                $mother_str .= $v->link_uid.",".$v->link_uid_two.",".$v->link_uid_three.",";
            }
            if($v->member_class == 2){
                $father_str .= $v->link_uid.",".$v->link_uid_two.",".$v->link_uid_three.",";
            }
        }
        
        $mother_array = explode(",", $mother_str);
        $mother_array = array_unique($mother_array);
        $father_array = explode(",", $father_str);
        $father_array = array_unique($father_array);
        
        $student_array = array();
        $class_map_model = Tmac::model( 'ClassMap' );
        $class_map_model instanceof service_ClassMap_www;
        foreach ( $student_list as $k => $v ) {
            $student_array[ $k ][ 'uid' ] = $v->uid;
            $student_array[ $k ][ 'username' ] = $v->username;
            $student_array[ $k ][ 'realname' ] = ($v->realname == '') ? $v->username : $v->realname;
            $student_array[ $k ][ 'mobile' ] = $v->mobile;
            $student_array[ $k ][ 'student_id' ] = $v->student_id;
            $student_array[ $k ][ 'student_short_id' ] = $v->student_short_id;
            $student_array[ $k ][ 'member_type' ] = $v->member_type;
            $student_array[ $k ][ 'member_class' ] = $v->member_class;
            $student_array[ $k ][ 'member_status' ] = $v->member_status;
            $student_array[ $k ][ 'class_map_id' ] = $v->class_map_id;
            $student_array[ $k ][ 'class_name' ] = '';
            $student_array[ $k ][ 'h_mother' ] = 0;
            $student_array[ $k ][ 'h_father' ] = 1;
            if(  in_array( $v->uid, $mother_array )){
                $student_array[ $k ][ 'h_mother' ] = 1;
            }
            
            if(  in_array( $v->uid, $father_array )){
                $student_array[ $k ][ 'h_father' ] = 1;
            }
            if ( $v->class_map_id > 0 ) {
                $class_info = $class_map_model->getClassMapById( $v->class_map_id );
                $student_array[ $k ][ 'class_name' ] = $class_info->grade_name . $class_info->class_name;
            }
        }
        
        return $student_array;
    }

    /**
     * 说明： 处理学生信息
     * @param type $student_list
     * @return string
     */
    public function handleStudentInfo( $student_list )
    {
        $student_array = array();
        $class_map_model = Tmac::model( 'ClassMap' );
        $class_map_model instanceof service_ClassMap_www;
        foreach ( $student_list as $k => $v ) {
            $student_array[ $k ][ 'uid' ] = $v->uid;
            $student_array[ $k ][ 'username' ] = $v->username;
            $student_array[ $k ][ 'mobile' ] = $v->mobile;
            $student_array[ $k ][ 'student_id' ] = $v->student_id;
            $student_array[ $k ][ 'student_short_id' ] = $v->student_short_id;
            $student_array[ $k ][ 'member_type' ] = $v->member_type;
            $student_array[ $k ][ 'member_class' ] = $v->member_class;
            $student_array[ $k ][ 'member_status' ] = $v->member_status;
            $student_array[ $k ][ 'class_map_id' ] = $v->class_map_id;
            $student_array[ $k ][ 'class_name' ] = '';

            if ( $v->class_map_id > 0 ) {
                $class_info = $class_map_model->getClassMapById( $v->class_map_id );
                $student_array[ $k ][ 'class_name' ] = $class_info->grade_name . $class_info->class_name;
            }
        }

        return $student_array;
    }

    /**
     * 说明，返回所有学生信息
     * @param type $class_map_ids
     * @return type
     */
    public function getStudentByclassmapids( $class_map_ids, $query = '' )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );

        if ( $query != '' ) {
            $dao->setWhere( "class_map_id IN ({$class_map_ids}) AND  (username like '%{$query}%' or realname like '%{$query}%' or student_id like '%{$query}%' or student_short_id like '%{$query}%' )" );
        } else {
            $dao->setWhere( "class_map_id IN ({$class_map_ids})" );
        }

        return $dao->getListByWhere();
    }

    /**
     * 说明：根据学生ID返回学生父母帐号信息
     * @param type $student_list
     * @return type
     */
    public function getParentsInfoByStudent( $student_list )
    {
        $student_ids = '';
        foreach ( $student_list as $v ) {
            $student_ids .= $v->uid . ',';
        }
        $student_ids = substr( $student_ids, 0, -1 );
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );
        $dao->setWhere( "link_uid in ({$student_ids}) or link_uid_two in ({$student_ids}) or link_uid_three in ({$student_ids})" );
        return $dao->getListByWhere();
    }

    /**
     * 说明，返回所有老师列表
     * @return array
     */
    public function getTearchList( $school_id, $query )
    {
        $teacher_list = array();
        //所有老师信息
//        $all_teacher = $this->getMemberByMembertype( self::member_type_teacher_type );
        $all_teacher = $this->getTeacherListBySchoolid( $school_id, $query );
        if ( empty( $all_teacher ) ) {
            return $teacher_list;
        }

        $purview_model = Tmac::model( 'Purview', APP_WWW_NAME );
        $purview_model instanceof service_Purview_www;

        foreach ( $all_teacher as $k => $v ) {
            $purview = $purview_model->getPurviewByUid( $v->uid );
            $purview_list = $this->handleTeacherInfoForView( $v, $purview );
            $teacher_list[ $k ] = $purview_list;
        }
        return $teacher_list;
    }

    /**
     * 说明：根据用户类型，返回所有用户
     * @param type $member_type
     * @return type
     */
    public function getMemberByMembertype( $member_type, $school_id = 0 )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );
        if ( $school_id > 0 ) {
            $dao->setWhere( "member_type = {$member_type} AND school_id = {$school_id}" );
        } else {
            $dao->setWhere( "member_type = {$member_type}" );
        }


        $res = $dao->getListByWhere();
        return $res;
    }

    /**
     * 说明：根据条件返回相关用户信息
     * @param entity_Member_base $entity_member
     * @return type
     */
    public function getMemberListInfo( entity_Member_base $entity_member )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );
        $where_str = ' 1 = 1';
        //用户类型
        if ( $entity_member->member_type > 0 ) {
            $where_str .= " AND member_type = {$entity_member->member_type}";
        }
        //用户学校
        if ( $entity_member->school_id > 0 ) {
            $where_str .= " AND school_id = {$entity_member->school_id}";
        }

        $dao->setWhere( $where_str );
        $res = $dao->getListByWhere();
        return $res;
    }

    /**
     * 说明：处理要写入的数据
     * @param type $student_arr
     * @return \entity_Member_base
     */
    public function handleStudentData( $student_arr )
    {
        $data = array('student' => '', 'father' => '', 'mother' => '');
        $entity_student = new entity_Member_base();
        $entity_student->class_map_id = $student_arr[ 'class_map_id' ];
        $entity_student->member_type = self::member_type_student_type;
        $entity_student->username = service_utils_Function_base::guid();
        $entity_student->realname = $student_arr[ 'username' ];
        $entity_student->mobile = $student_arr[ 'mobile' ];
        $entity_student->student_id = $student_arr[ 'student_id' ];
        $entity_student->student_short_id = $student_arr[ 'student_short_id' ];
        $entity_student->school_id = $student_arr[ 'school_id' ];
        $entity_student->term_id = $student_arr[ 'term_id' ];
        $activation = md5( $entity_student->username );
        $entity_student->activation = substr( $activation, 0, 4 ) . '-' . substr( $activation, 4, 4 ) . '-' . substr( $activation, 8, 4 ) . '-' . substr( $activation, 12, 4 );


        $data[ 'student' ] = $entity_student;

        if ( $student_arr[ 'f_phone' ] > 0 ) {
            $entity_father = new entity_Member_base();
            $entity_father->username = service_utils_Function_base::guid();
            $entity_father->realname = $student_arr[ 'username' ] . '的父亲';
            $entity_father->mobile = $student_arr[ 'f_phone' ];
            $entity_father->term_id = $student_arr[ 'term_id' ];
            $entity_father->school_id = $student_arr[ 'school_id' ];
            $entity_father->member_type = self::member_type_parent_type;
            $entity_father->member_class = self::member_class_father_class;
            $activation = md5( $entity_father->username );
            $entity_father->activation = substr( $activation, 0, 4 ) . '-' . substr( $activation, 4, 4 ) . '-' . substr( $activation, 8, 4 ) . '-' . substr( $activation, 12, 4 );
            $data[ 'father' ] = $entity_father;
        }

        if ( $student_arr[ 'm_phone' ] > 0 ) {
            $entity_mother = new entity_Member_base();
            $entity_mother->username = service_utils_Function_base::guid();
            $entity_mother->realname = $student_arr[ 'username' ] . '的母亲';
            $entity_mother->mobile = $student_arr[ 'm_phone' ];
            $entity_mother->term_id = $student_arr[ 'term_id' ];
            $entity_mother->school_id = $student_arr[ 'school_id' ];
            $entity_mother->member_type = self::member_type_parent_type;
            $entity_mother->member_class = self::member_class_mother_class;
            $activation = md5( $entity_mother->username );
            $entity_mother->activation = substr( $activation, 0, 4 ) . '-' . substr( $activation, 4, 4 ) . '-' . substr( $activation, 8, 4 ) . '-' . substr( $activation, 12, 4 );
            $data[ 'mother' ] = $entity_mother;
        }

        return $data;
    }

    /**
     * 说明：根据手机号返回用户信息（可以用来判断用户是否已经存在）
     * @param type $mobile
     * @return type
     */
    public function getMemberByMobile( $mobile )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );
        $dao->setWhere( "mobile = '{$mobile}'" );
        $res = $dao->getInfoByWhere();
        return $res;
    }

    public function getMemberByUid( $uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );
        $dao->setPk( $uid );
        $res = $dao->getInfoByPk();
        return $res;
    }

    /**
     * 创建学生帐号，同时创建学生父母帐号
     * @param type $data
     * @return type
     * @throws ApiException
     */
    public function createStudentInfo( $data )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->getDb()->startTrans();

        $member_id = 0;
        if ( !empty( $data[ 'student' ] ) ) {
            $member_check_model = new service_MemberCheck_www();
            //判断学生的手机号是否存在
            $mobile_check = $member_check_model->checkMobielIsExist( $data[ 'student' ]->mobile );
            if ( $mobile_check == true ) {
                $this->errorMessage = '学生手机号：' . $data[ 'student' ]->mobile . '已经存在';
                $dao->getDb()->rollback();
                return FALSE;
            }
            //判断学生的长学号是否存在
            $student_id_check = $member_check_model->checkStudentIdIsExist( $data[ 'student' ]->school_id, $data[ 'student' ]->class_map_id, $data[ 'student' ]->student_id );
            if ( $student_id_check == true ) {
                $this->errorMessage = '学生长学号：' . $data[ 'student' ]->student_id . '已经存在';
                $dao->getDb()->rollback();
                return FALSE;
            }
            //判断学生的短学号是否存在
            $student_short_check = $member_check_model->checkStudentShortIdIsExist( $data[ 'student' ]->school_id, $data[ 'student' ]->class_map_id, $data[ 'student' ]->student_short_id );
            if ( $student_short_check == true ) {
                $this->errorMessage = '学生短学号：' . $data[ 'student' ]->student_short_id . '已经存在';
                $dao->getDb()->rollback();
                return FALSE;
            }
            
            $member_id = $this->createOneMemberInfo( $data[ 'student' ] );
        }

        if ( $member_id <= 0 ) {
            $this->errorMessage = '写入学生信息失败，请联系管理员';
        } else {
            $data[ 'student' ]->uid = $member_id;
        }

        //创建或更新父亲关联
        if ( !empty( $data[ 'father' ] ) ) {
            $data[ 'father' ] = $this->createParentInfo( $data[ 'father' ], $member_id );
            if ( $data[ 'father' ] == false ) {
                $this->errorMessage = '写入学生父亲信息失败(可能存在三个学生已经与此帐号关联)，请联系管理员';
            }
        }

        if ( !empty( $data[ 'mother' ] ) ) {
            $data[ 'mother' ] = $this->createParentInfo( $data[ 'mother' ], $member_id );
            if ( $data[ 'mother' ] == false ) {
                $this->errorMessage = '写入学生母亲信息失败(可能存在三个学生已经与此帐号关联)，请联系管理员';
            }
        }

        //写入学生ID成功后，更新班级学生总数 (class_map)
        $class_map_model = Tmac::model( "ClassMap" );
        $class_map_model instanceof service_ClassMap_www;
        $new_class_map_entity = new entity_ClassMap_base();
        $new_class_map_entity->student_count = new TmacDbExpr( 'student_count+1' );
        $new_class_map_entity->class_map_id = $data[ 'student' ]->class_map_id;
        $class_map_model->modifyClassMapByPK( $new_class_map_entity );

        //写入学生ID成功后，更新学期学生数量
        $term_model = Tmac::model( "Term" );
        $term_model instanceof service_Term_www;
        $new_term_entity = new entity_Term_base();
        $new_term_entity->term_student_count = new TmacDbExpr( 'term_student_count+1' );
        $new_term_entity->term_id = $data[ 'student' ]->term_id;
        $term_model->updateTermInfo( $new_term_entity );

        //写入学生ID成功后，更新学校学生信息
        $school_model = Tmac::model( "School" );
        $school_model instanceof service_School_www;
        $new_school_entity = new entity_School_base();
        $new_school_entity->student_count = new TmacDbExpr( 'student_count+1' );
        $new_school_entity->school_id = $data[ 'student' ]->school_id;
        $school_model->modifySchoolByPk( $new_school_entity );

        if ( $dao->getDb()->isSuccess() ) {
            $this->createEasemobMember( $member_id );
            $dao->getDb()->commit();
            return $data;
        } else {
            $dao->getDb()->rollback();
            return FALSE;
        }
    }

    /**
     * 说明：添加学生时，添加父母用户
     * @param type $entity_member
     * @param type $member_id
     * @return boolean
     */
    public function createParentInfo( $entity_member, $member_id )
    {
        $dao = dao_factory_base::getMemberDao();
        $member_info = $this->getMemberByMobile( $entity_member->mobile );

        if ( empty( $member_info ) ) {
            $entity_member->link_uid = $member_id;
            $new_member_id = $dao->insert( $entity_member );

            //写入家长ID成功后，更新学期家长数量
            $term_model = Tmac::model( "Term" );
            $term_model instanceof service_Term_www;
            $new_term_entity = new entity_Term_base();
            $new_term_entity->term_parents_count = new TmacDbExpr( 'term_parents_count+1' );
            $new_term_entity->term_id = $entity_member->term_id;
            $term_model->updateTermInfo( $new_term_entity );

            //写入家长ID成功后，更新学校家长数量
            $school_model = Tmac::model( "School" );
            $school_model instanceof service_School_www;
            $new_school_entity = new entity_School_base();
            $new_school_entity->parents_count = new TmacDbExpr( 'parents_count+1' );
            $new_school_entity->school_id = $entity_member->school_id;
            $school_model->modifySchoolByPk( $new_school_entity );

            $entity_member->uid = $new_member_id;
            $this->createEasemobMember( $new_member_id );
            return $entity_member;
        } else {
            $entity_father = new entity_Member_base();
            $entity_father->uid = $member_info->uid;
            if ( $member_info->link_uid <= 0 ) {
                $entity_father->link_uid = $member_id;
            } else if ( $member_info->link_uid_two <= 0 ) {
                $entity_father->link_uid_two = $member_id;
            } else if ( $member_info->link_uid_three <= 0 ) {
                $entity_father->link_uid_three = $member_id;
            } else {
                return false;
            }

            $num = $this->modifyOneMemberInfo( $entity_father );

            return ($num <= 0) ? false : $member_info;
        }
    }

    /**
     * 说明：创建一条用户信息
     * @param type $entity_member
     * @return type
     */
    public function createOneMemberInfo( $entity_member )
    {
        $dao = dao_factory_base::getMemberDao();
        $member_id = $dao->insert( $entity_member );

        return $member_id;
    }

    /**
     * 说明：更新一条用户信息
     * @param entity_Member_base $entity_member
     * @return type
     */
    public function modifyOneMemberInfo( entity_Member_base $entity_member )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $entity_member->uid );
        $dao->updateByPk( $entity_member );
        $num = $dao->getDb()->getNumRows();

        return $num;
    }

    /**
     * 说明：根据学期，年级，班级，返回学生帐号ID
     * @param type $class_map_id
     * @return type
     */
    public function getStudentIdByClassmapid( $class_map_id )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid' );
        $dao->setWhere( "class_map_id = {$class_map_id} AND member_type = 1" );

        $res = $dao->getListByWhere();
        return $res;
    }

    public function getStudentInfoByClassmapid( $class_map_id )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );
        $dao->setWhere( "class_map_id = {$class_map_id} AND member_type = 1" );

        $res = $dao->getListByWhere();
        return $res;
    }

    /**
     * 说明：根据学期，年级，班级，返回老师帐号ID
     * @param type $class_map_id
     * @return type
     */
    public function getTeacherIdByClassmapid( $class_map_id )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid' );
        $dao->setWhere( "class_map_id = {$class_map_id} AND member_type = 1" );

        $res = $dao->getListByWhere();
        return $res;
    }

    /**
     * 说明，返回该学期的年级，班级信息
     * @param type $term_id
     * @param type $school_id
     * @return type
     * @throws ApiException
     */
    public function getAllTermInfoForTearch( $term_id, $school_id = 0 )
    {
        $classmap_model = Tmac::model( 'ClassMap' );
        $classmap_model instanceof service_ClassMap_www;

        $classmap_info = $classmap_model->getClassMapByTermidANdSchoolid( $term_id, $school_id );

        if ( empty( $classmap_info ) ) {
            return false;
        }

        $class_map_info = $classmap_model->handleClassMapInfo( $classmap_info );

        return $class_map_info;
    }

    //老师部分
    /**
     * 说明：添加老师，添加身份，添加权限范围
     * @param type $tearch_info
     * @return type
     * @throws ApiException
     */
    public function handleTeacherInfo( $tearch_info )
    {
        $entity_teacher = new entity_Member_base();
        $entity_teacher->username = service_utils_Function_base::guid();
        $entity_teacher->realname = $tearch_info[ 'username' ];
        $entity_teacher->mobile = $tearch_info[ 'mobile' ];
        $entity_teacher->student_id = $tearch_info[ 'student_id' ];
        $entity_teacher->school_id = $tearch_info[ 'school_id' ];
        $entity_teacher->term_id = $tearch_info[ 'term_id' ];
        $activation = md5( $entity_teacher->username );
        $entity_teacher->activation = substr( $activation, 0, 4 ) . '-' . substr( $activation, 4, 4 ) . '-' . substr( $activation, 8, 4 ) . '-' . substr( $activation, 12, 4 );

        $dao = dao_factory_base::getMemberDao();
        $dao->getDb()->startTrans();

        $member_check_model = new service_MemberCheck_www();
        //判断老师的手机号是否存在
        $mobile_check = $member_check_model->checkMobielIsExist( $entity_teacher->mobile );
        if ( $mobile_check == false ) {
            $this->errorMessage = '手机号：' . $entity_teacher->mobile . '已经存在';
            $dao->getDb()->rollback();
            return FALSE;
        }
        //判断老师的长学号是否存在
        $student_id_check = $member_check_model->checkTeacherStudentIdIsExist( $entity_teacher->school_id, $entity_teacher->student_id );
        if ( $student_id_check == false ) {
            $this->errorMessage = '长学号：' . $entity_teacher->student_id . '已经存在';
            $dao->getDb()->rollback();
            return FALSE;
        }
        $teacher_id = $this->createOneTeacher( $entity_teacher );

        //写入老师ID成功后，更新学期老师数量
        $term_model = Tmac::model( "Term" );
        $term_model instanceof service_Term_www;
        $new_term_entity = new entity_Term_base();
        $new_term_entity->term_teacher_count = new TmacDbExpr( 'term_teacher_count+1' );
        $new_term_entity->term_id = $entity_teacher->term_id;
        $term_model->updateTermInfo( $new_term_entity );

        //写入老师ID成功后，更新学校老师数量
        $school_model = Tmac::model( "School" );
        $school_model instanceof service_School_www;
        $new_school_entity = new entity_School_base();
        $new_school_entity->teacher_count = new TmacDbExpr( 'teacher_count+1' );
        $new_school_entity->school_id = $entity_teacher->school_id;
        $school_model->modifySchoolByPk( $new_school_entity );

        $entity_teacher->uid = $teacher_id;


        //写入老师身份表
        $identity_arr = $this->createTeacherIdentity( $tearch_info[ 'purview_str' ], $teacher_id );
        if ( empty( $identity_arr ) ) {
            $this->errorMessage = '写入老师身份出错，请联系管理员';
            $dao->getDb()->rollback();
            return FALSE;
        }

        //写入用户权限
        $purview_list = $this->createTeacherPurview( $teacher_id, $tearch_info[ 'purview_str' ] );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $this->createEasemobMember( $teacher_id );
            $teacher_data = $this->handleTeacherInfoForView( $entity_teacher, $purview_list );
            return $teacher_data;
        } else {
            $dao->getDb()->rollback();
            return FALSE;
        }
    }

    /**
     * 说明：处理要返回的老师信息
     * @param entity_Member_base $entity_teacher
     * @param type $purview_list
     */
    public function handleTeacherInfoForView( $entity_teacher, $purview_list )
    {

        $teacher_data = array();
        $purview_info = array();
        $teacher_data[ 'uid' ] = $entity_teacher->uid;
        $teacher_data[ 'member_status' ] = $entity_teacher->member_status;
        $teacher_data[ 'username' ] = $entity_teacher->username;
        $teacher_data[ 'mobile' ] = $entity_teacher->mobile;
        $teacher_data[ 'student_id' ] = $entity_teacher->student_id;
        $teacher_data[ 'purview' ] = $purview_info;

        if ( empty( $purview_list ) ) {
            return $teacher_data;
        }

        $class_map_model = Tmac::model( 'ClassMap' );
        $class_map_model instanceof service_ClassMap_www;
        $subject_model = Tmac::model( 'Subject' );
        $subject_model instanceof service_Subject_www;

        foreach ( $purview_list as $k => $v ) {
            $purview_info[ $v->identity_id ][ 'identity_id' ] = $v->identity_id;
            $purview_info[ $v->identity_id ][ 'identity_name' ] = $v->identity_name;
            if ( $v->identity_id == service_Member_www::identity_head_principal ) {
                continue;
            }

            if ( $v->identity_id != service_Member_www::identity_head_teacher ) {
                $purview_info[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'subject_school_id' ] = $v->subject_school_id;
                $purview_info[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'subject_short_name' ] = $v->subject_name;
                $purview_info[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'class' ][ $v->class_map_id ][ 'class_map_id' ] = $v->class_map_id;
                if ( $v->identity_id == service_Member_www::identity_senior_leader || $v->identity_id == service_Member_www::identity_team_leader ) {
                    $classmap_info = $class_map_model->getClassMapById( $v->class_map_id );
                    $purview_info[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'class' ][ $v->class_map_id ][ 'class_name' ] = $classmap_info->grade_name;
                } else {
                    $purview_info[ $v->identity_id ][ 'subject' ][ $v->subject_school_id ][ 'class' ][ $v->class_map_id ][ 'class_name' ] = $v->class_map_name;
                }
            } else {
                $purview_info[ $v->identity_id ][ 'subject' ][ $v->class_map_id ][ 'subject_school_id' ] = $v->class_map_id;
                $purview_info[ $v->identity_id ][ 'subject' ][ $v->class_map_id ][ 'subject_short_name' ] = $v->class_map_name;
            }
        }
        $teacher_data[ 'purview' ] = $purview_info;
        return $teacher_data;
    }

    /**
     * 说明：写入身份表
     * @param type $purview_str
     * @param type $uid
     * @return \entity_Identity_base
     * @throws ApiException
     */
    public function createTeacherIdentity( $purview_str, $uid )
    {
        $identity_info = $this->handlePurivewInfo( $purview_str );
        $identity_model = Tmac::model( 'Identity' );
        $identity_model instanceof service_Identity_www;

        $member_config = Tmac::config( 'member', APP_WWW_NAME );
        $identity_config = $member_config[ 'identity_id' ];

        $entity_identity = new entity_Identity_base();
        $identity_array = array();

        if ( $identity_info ) {
            foreach ( $identity_info as $v ) {
                $entity_identity->id = '';
                $entity_identity->identity_id = $v[ 'identity_id' ];
                $entity_identity->identity_name = $identity_config[ $v[ 'identity_id' ] ];
                $entity_identity->uid = $uid;

                $old_entity_identity = $identity_model->getIdentityByidentityidAndUid( $v[ 'identity_id' ], $uid );

                if ( !empty( $old_entity_identity ) ) {
                    $id = $old_entity_identity->id;
                } else {
                    $id = $identity_model->createOneIdentity( $entity_identity );
                }

                $entity_identity->id = $id;
                $identity_array[] = $entity_identity;
            }
        }
        return $identity_array;
    }

    /**
     * 说明：写入老师用户
     * @param entity_Member_base $entity_teacher
     * @return type
     */
    public function createOneTeacher( entity_Member_base $entity_teacher )
    {
        $dao = dao_factory_base::getMemberDao();
        $entity_teacher->member_type = self::member_type_teacher_type; //老师类型
        $teacher_id = $dao->insert( $entity_teacher );

        return $teacher_id;
    }

    /**
     * 说明：处理身份，权限信息
     * @param type $purivew_str
     * @return boolean
     */
    public function handlePurivewInfo( $purivew_str )
    {
        if ( $purivew_str == '' ) {
            return false;
        }

        $purivew_arr = explode( "$", $purivew_str );
        $p_arr = array();
        foreach ( $purivew_arr as $k => $v ) {
            if ( empty( $v ) ) {
                continue;
            }
            $p_info = explode( "ID", $v );
            $temp_arr[ 'identity_id' ] = $p_info[ 0 ];
            $temp_arr[ 'cmid' ] = $p_info[ 1 ];
            $temp_arr[ 'ssid' ] = $p_info[ 2 ];
            $p_arr[] = $temp_arr;
        }

        return $p_arr;
    }

    /**
     * 说明：写入权限范围表
     * @param type $uid
     * @param type $identity_arr    //暂时没有用到这个参数
     * @param type $purview_arr
     * @return boolean|\entity_Purview_base
     * @throws ApiException
     */
    public function createTeacherPurview( $uid, $purview_str )
    {
        $purview_arr = $this->handlePurivewInfo( $purview_str );
        $purview_data = $this->handleInsertPurviewData( $purview_arr );

        if ( empty( $purview_data ) ) {
            return false;
        }

        $member_config = Tmac::config( 'member' );
        $identity_config = $member_config[ 'identity_id' ];

        $entity_purview = new entity_Purview_base();
        $entity_purview->uid = $uid;

        $purview_model = Tmac::model( 'Purview' );
        $purview_model instanceof service_Purview_www;

        $purview_list = array();

        foreach ( $purview_data as $k => $p ) {
            foreach ( $p as $t => $v ) {
                if ( $k == 6 && $t > 0 ) {//校长身份，写入一条数据就可以了
                    break;
                }
                $entity_purview->purview_id = '';
                $entity_purview->identity_id = $k;
                $entity_purview->identity_name = $identity_config[ $k ];
                $entity_purview->class_map_name = $v[ 'class_map_name' ];
                $entity_purview->subject_school_id = $v[ 'subject_school_id' ];
                $entity_purview->class_map_id = $v[ 'class_map_id' ];
                $entity_purview->subject_name = $v[ 'subject_name' ];

                $old_entity_purview = $purview_model->getPurviewByUidAndIndentityidAndClassmapidAndSubjectschoolid( $uid, $k, $v[ 'class_map_id' ], $v[ 'subject_school_id' ] );
                $purview_id = 0;
                if ( !empty( $old_entity_purview ) ) {
                    $purview_id = $old_entity_purview->purview_id;
                } else {
                    $purview_id = $purview_model->createOnePurview( $entity_purview );
                }

                $entity_purview->purview_id = $purview_id;
                $purview_list[] = $entity_purview;
            }
        }
        return $purview_list;
    }

    /**
     * 处理要写入权限表中的数据
     * @param type $purview_arr
     * @return type
     */
    public function handleInsertPurviewData( $purview_arr )
    {
        $purview_info = array();
        foreach ( $purview_arr as $v ) {
            $purview_info[ $v[ 'identity_id' ] ][ 'cmid' ] = explode( ",", $v[ 'cmid' ] );
            $purview_info[ $v[ 'identity_id' ] ][ 'ssid' ] = explode( ",", $v[ 'ssid' ] );
        }

        $subject_model = Tmac::model( 'Subject' );
        $subject_model instanceof service_Subject_www;
        $class_map_model = Tmac::model( 'ClassMap' );
        $class_map_model instanceof service_ClassMap_www;
        $purview = array();
        foreach ( $purview_info as $k => $v ) {
            foreach ( $v[ 'cmid' ] as $cm ) {
                if ( $cm == '' ) {
                    continue;
                }
                $class_info = $class_map_model->getClassMapById( $cm );
                if ( empty( $class_info ) ) {
                    continue;
                }
                $class_name = $class_info->grade_name . $class_info->class_name;
                foreach ( $v[ 'ssid' ] as $ss ) {
                    if ( $ss == '' ) {
                        continue;
                    }

                    $subject_info = $subject_model->getSubjectByclassmapidAndSujectschoolid( $cm, $ss );
                    if ( empty( $subject_info ) ) {
                        continue;
                    }

                    $temp_arr[ 'class_map_id' ] = $cm;
                    $temp_arr[ 'class_map_name' ] = $class_name;
                    $temp_arr[ 'subject_school_id' ] = $ss;
                    $temp_arr[ 'subject_name' ] = $subject_info->subject_short_name;
                    $purview[ $k ][] = $temp_arr;
                }
            }
        }

        return $purview;
    }

    /**
     * 创建环信用户
     * @param type $username
     * @param type $password
     */
    public function createEasemobMember( $username, $password = null )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $username );
        $username = md5( $username );
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->uid_md5 = $username;
        $dao->updateByPk( $entity_Member_base );

        if ( empty( $password ) ) {
            $password = $username;
        }
        $easemob_model = $this->getEasemobModel();
        $options = array(
            'username' => $username,
            'password' => $password
        );
        $res = $easemob_model->accreditRegister( $options );
        if ( !empty( $res[ 'error' ] ) ) {
            $res = $easemob_model->accreditRegister( $options );
        }
        return $res;
    }

    /**
     * 创建环信用户
     * @param type $username
     * @param type $password
     */
    public function getEasemobMember( $username )
    {
        $username = md5( $username );

        $easemob_model = $this->getEasemobModel();

        //$res = $easemob_model->userDetails( $username );
        $ql = "select+*+where+from='" . $username . "'+or+to='" . $username . "'+order+by+timestamp+desc";
        $res = $easemob_model->chatRecord( $ql, $cursor = 0, $limit = 200 );
        return $res;
    }

    /**
     * 说明：按班级，返回班级学生信息
     * @param type $student_info
     * @return string
     */
    public function handleStudentGroupByClassmapid( $student_info )
    {
        $class_student = array();
        foreach ( $student_info as $k => $v ) {
            $class_student[ $v->class_map_id ][] = $v;
        }
        return $class_student;
    }

    /**
     * 说明：根据学校ID，返回学校所有老师(以后加状态判断)
     * @param type $school_id
     */
    public function getTeacherListBySchoolid( $school_id, $query = '' )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,realname,password,mobile,student_id,student_short_id,member_type,member_class,member_status,salt,class_map_id,link_uid,link_uid_two,link_uid_three,school_id' );
        $member_type = self::member_type_teacher_type;
        if ( $query != '' ) {
            $dao->setWhere( "member_type = {$member_type} AND school_id = {$school_id} AND (username like '%{$query}%' OR realname like '%{$query}%')" );
        } else {
            $dao->setWhere( "member_type = {$member_type} AND school_id = {$school_id}" );
        }

        $res = $dao->getListByWhere();

        return $res;
    }

    /**
     * 在创建学生时判断学校和学期ID
     * 暂时用不着，在创建学生和老师时直接使用 管理员的memberInfo->school_id和memberInfo->term_id
     * @param type $school_id
     * @param type $term_id
     * @return boolean
     */
    public function checkShcoolTermId( $school_id, $term_id )
    {
        $dao = dao_factory_base::getSchooolDao();
        $dao->setField( 'term_id' );
        $dao->setPk( $school_id );
        $school_info = $dao->getInfoByPk();
        if ( $school_info == false ) {
            $this->errorMessage = '学校不存在';
            return false;
        }
        if ( $school_info->term_id <> $term_id ) {
            $this->errorMessage = '学校和学期不符合';
            return false;
        }
        return true;
    }

}
