<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Address.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_Address_mobile extends service_Member_base
{

    private $address_id;

    function setAddress_id( $address_id )
    {
        $this->address_id = $address_id;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测修改/删除 地址的权限
     * $this->uid;
     * $this->address_id;
     * $this->checkPurview();
     */
    public function checkPurview()
    {
        $member_address_info = $this->getMemberAddressInfoById( 'uid' );
        if ( $member_address_info == false ) {
            $this->errorMessage = '收货地址不存在';
            return false;
        }
        if ( $member_address_info->uid <> $this->uid ) {
            $this->errorMessage = '没有此收货地址的权限';
            return false;
        }
        return true;
    }

    public function getMemberAddressInfoById( $field = '*' )
    {
        $dao = dao_factory_base::getMemberAddressDao();
        $dao->setPk( $this->address_id );
        $dao->setField( $field );
        return $dao->getInfoByPk();
    }

    public function createMemberAddress( entity_MemberAddress_base $entity_MemberAddress_base )
    {
        $dao = dao_factory_base::getMemberAddressDao();
        //判断是否已经存在
        $where = "uid={$entity_MemberAddress_base->uid} AND province={$entity_MemberAddress_base->province} AND city={$entity_MemberAddress_base->city} AND district={$entity_MemberAddress_base->district} AND address='{$entity_MemberAddress_base->address}'";
        $dao->setField( 'address_id,is_delete' );
        $dao->setWhere( $where );
        $member_address_info = $dao->getInfoByWhere();
        if ( $member_address_info ) {
            if ( $member_address_info->is_delete == 1 ) {
                $entity_MemberAddress_base->is_delete = 0;
                $entity_MemberAddress_base->address_id = $member_address_info->address_id;
                return $this->modifyMemberAddress( $entity_MemberAddress_base );
            }
            return $member_address_info->address_id;
        }
        $this->getFullAddress( $entity_MemberAddress_base );

        //判断有没有默认地址
        if ( $this->checkMemberAddressHasDefault( $entity_MemberAddress_base->uid ) == false ) {
            $entity_MemberAddress_base->is_default = 1;
        }
        return $dao->insert( $entity_MemberAddress_base );
    }

    /**
     * 检测 用户有没有默认地址
     */
    private function checkMemberAddressHasDefault( $uid )
    {
        $dao = dao_factory_base::getMemberAddressDao();
        //查询默认的收货地址
        $where = "uid={$uid} AND is_delete=0 AND is_default=1";
        $dao->setWhere( $where );
        return $address_info = $dao->getInfoByWhere();
    }

    public function modifyMemberAddress( entity_MemberAddress_base $entity_MemberAddress_base )
    {
        $this->getFullAddress( $entity_MemberAddress_base );
        $dao = dao_factory_base::getMemberAddressDao();
        $dao->setPk( $entity_MemberAddress_base->address_id );
        return $dao->updateByPk( $entity_MemberAddress_base );
    }

    /**
     * 取全路径地址
     * @param entity_MemberAddress_base $entity_MemberAddress_base
     */
    public function getFullAddress( entity_MemberAddress_base $entity_MemberAddress_base )
    {
        $entity_MemberAddress_base->full_address = $this->getRegionNameById( $entity_MemberAddress_base->province )
                . $this->getRegionNameById( $entity_MemberAddress_base->city )
                . $this->getRegionNameById( $entity_MemberAddress_base->district )
                . $entity_MemberAddress_base->address;
        return $entity_MemberAddress_base;
    }

    private function getRegionNameById( $id )
    {
        $dao = dao_factory_base::getRegionDao();
        $dao->setPk( $id );
        $dao->setField( 'region_name' );
        $res = $dao->getInfoByPk();
        if ( $res ) {
            return $res->region_name;
        }
        return '';
    }

    /**
     * del
     * @param int $class_id
     */
    public function deleteByMemberAddressById( $id )
    {
        $dao = dao_factory_base::getMemberAddressDao();

        $dao->getDb()->startTrans();
        $entity_MemberAddress_base = new entity_MemberAddress_base();
        $entity_MemberAddress_base->is_delete = 1;

        $where = $dao->getWhereInStatement( 'address_id', $id );
        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_MemberAddress_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 更新默认收货地址
     * $this->uid;
     * $this->address_id;
     * $this->updateMemberAddressDefault();
     */
    public function updateMemberAddressDefault()
    {
        $dao = dao_factory_base::getMemberAddressDao();
        $dao->setWhere( "uid={$this->uid}" );
        $entity_MemberAddress_base = new entity_MemberAddress_base();
        $entity_MemberAddress_base->is_default = 0;
        $dao->updateByWhere( $entity_MemberAddress_base );

        $entity_MemberAddress_base->is_default = 1;
        $dao->setPk( $this->address_id );
        return $dao->updateByPk( $entity_MemberAddress_base );
    }

    public function getMemberAddressList()
    {
        $dao = dao_factory_base::getMemberAddressDao();
        $where = "uid = {$this->uid} AND is_delete = 0";
        $dao->setWhere( $where );
        $dao->setOrderby( 'is_default DESC,address_id DESC' );
        $dao->setField( 'address_id,consignee,mobile,is_default,full_address' );
        $res = $dao->getListByWhere();
        return $res;
    }

}
