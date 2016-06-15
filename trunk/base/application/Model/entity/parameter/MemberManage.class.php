<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: MemberManage.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of search
 *
 * @author Tracy McGrady
 */
class entity_parameter_MemberManage_base
{

    private $q;
    private $identity_id;
    private $class_map_id;
    private $subject_school_id;
    private $member_type;

    public function getQ()
    {
        return $this->q;
    }

    public function getIdentity_id()
    {
        return $this->identity_id;
    }

    public function getClass_map_id()
    {
        return $this->class_map_id;
    }

    public function getSubject_school_id()
    {
        return $this->subject_school_id;
    }

    public function setQ( $q )
    {
        $this->q = $q;
        return $this;
    }

    public function setIdentity_id( $identity_id )
    {
        $this->identity_id = $identity_id;
        return $this;
    }

    public function setClass_map_id( $class_map_id )
    {
        $this->class_map_id = $class_map_id;
        return $this;
    }

    public function setSubject_school_id( $subject_school_id )
    {
        $this->subject_school_id = $subject_school_id;
        return $this;
    }

    public function getMember_type()
    {
        return $this->member_type;
    }

    public function setMember_type( $member_type )
    {
        $this->member_type = $member_type;
        return $this;
    }

}
