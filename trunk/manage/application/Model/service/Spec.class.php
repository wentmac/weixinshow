<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Spec_manage extends Model
{

    const member_type_student_type = 1;  //学生用户类

    private $errorMessage;
    private $uid;

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    public function batchSave( $uid, $spec_name, $spec_value_array )
    {
        //先通过学期返回所有的班级
        $spec_dao = dao_factory_base::getSpecDao();
        $spec_dao->setWhere( "spec_name='{$spec_name}'" );
        $spec_dao->setField( 'spec_id' );
        $spec_info = $spec_dao->getInfoByWhere();
        if ( $spec_info == false ) {
            $entity_Spec_base = new entity_Spec_base();
            $entity_Spec_base->spec_name = $spec_name;
            $entity_Spec_base->spec_sort = 0;
            $spec_id = $spec_dao->insert( $entity_Spec_base );
        } else {
            $spec_id = $spec_info->spec_id;
        }

        $spec_map_dao = dao_factory_base::getSpecMapDao();
        $spec_map_dao->setWhere( "uid={$uid} AND spec_id={$spec_id}" );
        $spec_map_dao->setField( 'spec_map_id' );
        $spec_map_info = $spec_map_dao->getInfoByWhere();
        if ( $spec_map_info == false ) {
            $entity_SpecMap_base = new entity_SpecMap_base();
            $entity_SpecMap_base->spec_id = $spec_id;
            $entity_SpecMap_base->spec_name = $spec_name;
            $entity_SpecMap_base->spec_sort = 0;
            $entity_SpecMap_base->uid = $uid;
            $spec_map_dao->insert( $entity_SpecMap_base );
        }

        if ( empty( $spec_value_array ) ) {
            return true;
        }
        $spec_value_dao = dao_factory_base::getSpecValueDao();
        $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();
        foreach ( $spec_value_array AS $spec_value_name ) {
            $spec_value_id = $this->_getSpecValueId( $spec_value_dao, $spec_id, $spec_value_name );
            //检查map中是否存在
            $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();
            $spec_value_map_dao->setField( 'spec_value_map_id' );
            $spec_value_map_dao->setWhere( "uid={$uid} AND spec_id={$spec_id} AND spec_value_id={$spec_value_id}" );
            $spec_value_map_info = $spec_value_map_dao->getInfoByWhere();
            if ( $spec_value_map_info == false ) {
                $entity_SpecValueMap_base = new entity_SpecValueMap_base();
                $entity_SpecValueMap_base->spec_value_id = $spec_value_id;
                $entity_SpecValueMap_base->spec_value_name = $spec_value_name;
                $entity_SpecValueMap_base->spec_id = $spec_id;
                $entity_SpecValueMap_base->spec_value_sort = 0;
                $entity_SpecValueMap_base->uid = $uid;
                $spec_value_map_dao->insert( $entity_SpecValueMap_base );
            }
        }
        return true;
    }

    private function _getSpecValueId( $dao, $spec_id, $spec_value_name )
    {
        $dao instanceof dao_impl_SpecMap_base;
        $dao->setWhere( "spec_id={$spec_id} AND spec_value_name='{$spec_value_name}'" );
        $dao->setField( 'spec_value_id' );
        $spec_value_info = $dao->getInfoByWhere();
        if ( $spec_value_info == false ) {
            $entity_SpecValue_base = new entity_SpecValue_base();
            $entity_SpecValue_base->spec_value_name = $spec_value_name;
            $entity_SpecValue_base->spec_id = $spec_id;
            $entity_SpecValue_base->spec_value_sort = 0;
            $spec_value_id = $dao->insert( $entity_SpecValue_base );
        } else {
            $spec_value_id = $spec_value_info->spec_value_id;
        }
        return $spec_value_id;
    }

    /**
     * 用户新增规格值
     */
    public function createSpecValue( $spec_id, $spec_value_name )
    {
        $spec_value_name = trim( $spec_value_name );
        $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();

        $where = "uid={$this->uid} AND spec_id={$spec_id} AND spec_value_name='{$spec_value_name}'";
        $spec_value_map_dao->setWhere( $where );
        $spec_value_map_dao->setField( 'spec_value_id,spec_value_name,spec_id' );
        $spec_value_info = $spec_value_map_dao->getInfoByWhere();
        //检测wsw_spec_value_map是否存在
        if ( $spec_value_info == false ) {
            //如果存在就返回id,不存在就创建
            //判断spec_id是否存在
            $spec_map_dao = dao_factory_base::getSpecMapDao();
            $where = "uid={$this->uid} AND spec_id={$spec_id}";
            $spec_map_dao->setWhere( $where );
            $spec_map_info = $spec_map_dao->getInfoByWhere();
            if ( !$spec_map_info ) {
                $spec_map_id = $this->_createSpecMapBySpecId( $spec_id );
                if ( $spec_map_id == false ) {
                    $this->errorMessage = '商品规格' . $spec_id . '不存在';
                    return false;
                }
            }

            $spec_value_id = $this->_createSpecValueMap( $spec_id, $spec_value_name );
            $entity_SpecValueMap_base = new entity_SpecValueMap_base();
            $entity_SpecValueMap_base->spec_id = $spec_id;
            $entity_SpecValueMap_base->spec_value_id = $spec_value_id;
            $entity_SpecValueMap_base->spec_value_name = $spec_value_name;
            $spec_value_info = $entity_SpecValueMap_base;
        }

        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        return $spec_value_info;
    }

    /**
     * 用户新增规格
     */
    public function createSpec( $spec_name )
    {
        $spec_name = trim( $spec_name );
        $spec_map_dao = dao_factory_base::getSpecMapDao();

        $spec_map_dao->getDb()->startTrans();

        $where = "uid={$this->uid} AND spec_name='{$spec_name}'";
        $spec_map_dao->setWhere( $where );
        $spec_map_dao->setField( 'spec_id,spec_name' );
        $spec_info = $spec_map_dao->getInfoByWhere();
        //检测wsw_spec_map是否存在
        if ( $spec_info == false ) {
            //如果存在就返回id,不存在就创建
            $spec_id = $this->_createSpecMap( $spec_name );
            $entity_SpecMap_base = new entity_SpecMap_base();
            $entity_SpecMap_base->spec_id = $spec_id;
            $entity_SpecMap_base->spec_name = $spec_name;
            $spec_info = $entity_SpecMap_base;
        }

        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $spec_map_dao->getDb()->isSuccess() ) {
            $spec_map_dao->getDb()->commit();
            return $spec_info;
        } else {
            $spec_map_dao->getDb()->rollback();
            return false;
        }
    }

    private function _createSpecMap( $spec_name )
    {
        $spec_dao = dao_factory_base::getSpecDao();
        $where = "spec_name='{$spec_name}'";
        $spec_dao->setWhere( $where );
        $spec_dao->setField( 'spec_id' );
        $spec_info = $spec_dao->getInfoByWhere();
        if ( $spec_info ) {
            $spec_id = $spec_info->spec_id;
        } else {
            $entity_Spec_base = new entity_Spec_base();
            $entity_Spec_base->spec_name = $spec_name;
            $entity_Spec_base->spec_sort = 0;
            $spec_id = $spec_dao->insert( $entity_Spec_base );
        }
        $spec_map_dao = dao_factory_base::getSpecMapDao();
        $entity_SpecMap_base = new entity_SpecMap_base();
        $entity_SpecMap_base->spec_id = $spec_id;
        $entity_SpecMap_base->spec_name = $spec_name;
        $entity_SpecMap_base->spec_sort = 0;
        $entity_SpecMap_base->uid = $this->uid;

        $spec_map_id = $spec_map_dao->insert( $entity_SpecMap_base );
        if ( $spec_map_id ) {
            return $spec_id;
        }
        return false;
    }

    /**
     * 如果spec_map表中没有用户(uid和spec_id），则从spec表中查出来。然后写入spec_map表中
     * @param type $spec_id
     * @return boolean
     */
    private function _createSpecMapBySpecId( $spec_id )
    {
        $spec_dao = dao_factory_base::getSpecDao();
        $spec_dao->setPk( $spec_id );
        $spec_dao->setField( 'spec_id,spec_name' );
        $spec_info = $spec_dao->getInfoByPk();
        if ( $spec_info == false ) {
            $this->errorMessage = '商品规格ID：' . $spec_id . '不存在';
            return false;
        }

        $spec_map_dao = dao_factory_base::getSpecMapDao();
        $entity_SpecMap_base = new entity_SpecMap_base();
        $entity_SpecMap_base->spec_id = $spec_id;
        $entity_SpecMap_base->spec_name = $spec_info->spec_name;
        $entity_SpecMap_base->spec_sort = 0;
        $entity_SpecMap_base->uid = $this->uid;

        return $spec_map_dao->insert( $entity_SpecMap_base );
    }

    private function _createSpecValueMap( $spec_id, $spec_value_name )
    {
        $spec_value_dao = dao_factory_base::getSpecValueDao();
        $where = "spec_id={$spec_id} AND spec_value_name='{$spec_value_name}'";
        $spec_value_dao->setWhere( $where );
        $spec_value_dao->setField( 'spec_value_id' );
        $spec_value_info = $spec_value_dao->getInfoByWhere();
        if ( $spec_value_info ) {
            $spec_value_id = $spec_value_info->spec_value_id;
        } else {
            $entity_SpecValue_base = new entity_SpecValue_base();
            $entity_SpecValue_base->spec_value_name = $spec_value_name;
            $entity_SpecValue_base->spec_id = $spec_id;
            $entity_SpecValue_base->spec_value_sort = 0;
            $spec_value_id = $spec_value_dao->insert( $entity_SpecValue_base );
        }
        $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();
        $entity_SpecValueMap_base = new entity_SpecValueMap_base();
        $entity_SpecValueMap_base->spec_value_id = $spec_value_id;
        $entity_SpecValueMap_base->spec_value_name = $spec_value_name;
        $entity_SpecValueMap_base->spec_id = $spec_id;
        $entity_SpecValueMap_base->spec_value_sort = 0;
        $entity_SpecValueMap_base->uid = $this->uid;

        $spec_value_map_id = $spec_value_map_dao->insert( $entity_SpecValueMap_base );
        if ( $spec_value_map_id ) {
            return $spec_value_id;
        }
        return false;
    }

}
