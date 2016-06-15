<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_MemberSetting_manage extends service_Model_base
{

    private $uid;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取详情
     * @param type $uid
     * @return type
     */
    public function getMemberSettingInfoByUid( $field = '*' )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->uid );
        $dao->setField( $field );
        $res = $dao->getInfoByPk();
        if ( $res ) {
            //格式化数据
            if ( isset( $res->shop_image_id ) ) {
                $res->shop_image_url = parent::getImage( $res->shop_image_id, '200x150', 'shop' );
            }
            if ( isset( $res->idcard_positive_image_id ) ) {
                $res->idcard_positive_image_url = parent::getImage( $res->idcard_positive_image_id, '200x150', 'idcard' );
            }
            if ( isset( $res->idcard_negative_image_id ) ) {
                $res->idcard_negative_image_url = parent::getImage( $res->idcard_negative_image_id, '200x150', 'idcard' );
            }
            if ( isset( $res->idcard_image_id ) ) {
                $res->idcard_image_url = parent::getImage( $res->idcard_image_id, '200x150', 'idcard' );
            }
        }
        return $res;
    }

    /**
     * 取 基本
     * @return type
     */
    public function getMemberSettingBasic()
    {
        $field = 'shop_name,shop_intro,shop_image_id';
        return $this->getMemberSettingInfoByUid( $field );
    }

    /**
     * 取 基本
     * @return type
     */
    public function getMemberSettingIdcard()
    {
        $field = 'idcard,idcard_positive_image_id,idcard_negative_image_id,idcard_image_id,idcard_verify';
        return $this->getMemberSettingInfoByUid( $field );
    }

    /**
     * 取 基本
     * @return type
     */
    public function getMemberSettingPayment()
    {
        $field = 'bank_id,bank_pid,bank_cityid,bank_name,bank_cardnum,bank_account';
        return $this->getMemberSettingInfoByUid( $field );
    }

    /**
     * 更新用户设置
     * @param entity_MemberSetting_base $entity_MemberSetting_base
     * @return type
     */
    public function modifyMemberSetting( entity_MemberSetting_base $entity_MemberSetting_base )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->uid );
        return $dao->updateByPk( $entity_MemberSetting_base );
    }

    /**
     * 更新用户设置
     * @param entity_MemberSetting_base $entity_Member_base
     * @return type
     */
    public function modifyMember( entity_Member_base $entity_Member_base )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->uid );
        return $dao->updateByPk( $entity_Member_base );
    }

}
