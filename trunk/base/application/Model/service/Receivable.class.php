<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Receivable.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Receivable_base extends service_Model_base
{

    protected $uid;
    protected $receivable_id;
    protected $receivable_name;
    protected $receivable_money;
    protected $receivableInfo;
    protected $errorMessage;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setReceivable_id( $receivable_id )
    {
        $this->receivable_id = $receivable_id;
    }

    function setReceivable_name( $receivable_name )
    {
        $this->receivable_name = $receivable_name;
    }

    function setReceivable_money( $receivable_money )
    {
        $this->receivable_money = $receivable_money;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测新增的是否存在
     * @return boolean
     */
    public function checkReceivableExist()
    {
        $dao = dao_factory_base::getReceivableDao();
        $dao->setField( 'receivable_id,is_delete' );
        $dao->setWhere( "uid={$this->uid} AND receivable_name='{$this->receivable_name}' AND receivable_money='{$this->receivable_money}'" );
        $dao->setLimit( 1 );
        $receivable_info = $dao->getInfoByWhere();
        if ( $receivable_info ) {
            $this->receivableInfo = $receivable_info;
            return $receivable_info;
        }
        return false;
    }

    /**
     * 检测用户对 $item_ids 的权限
     * @param type $id_string
     * @return boolean
     */
    public function checkPurview( $id_string )
    {
        $dao = dao_factory_base::getReceivableDao();
        $dao->setField( 'receivable_id,uid,is_delete' );
        $where = $dao->getWhereInStatement( 'receivable_id', $id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        if ( $res ) {
            foreach ( $res AS $receivable ) {
                if ( $receivable->uid <> $this->uid ) {
                    $this->errorMessage = "您对ID:{$receivable->receivable_id} 没有权限";
                    return false;
                }
            }
            return true;
        }
        $this->errorMessage = "没有对应的权限";
        return false;
    }

    /**
     * 保存新增的
     */
    public function saveReceivalbe( $receivable_id )
    {
        $dao = dao_factory_base::getReceivableDao();

        $entity_Receivable_base = new entity_Receivable_base();
        $entity_Receivable_base->uid = $this->uid;
        $entity_Receivable_base->receivable_name = $this->receivable_name;
        $entity_Receivable_base->receivable_money = $this->receivable_money;
        $entity_Receivable_base->receivable_time = $this->now;
        $entity_Receivable_base->is_delete = 0;
        if ( empty( $receivable_id ) ) {//新增
            return $dao->insert( $entity_Receivable_base );
        } else {//修改
            $dao->setPk( $receivable_id );
            $res = $dao->updateByPk( $entity_Receivable_base );
            if ( $res ) {
                return $receivable_id;
            }
            return false;
        }
    }

    public function deleteById( $id )
    {
        $dao = dao_factory_base::getReceivableDao();

        $dao->getDb()->startTrans();
        $entity_Receivable_base = new entity_Receivable_base();
        $entity_Receivable_base->is_delete = 1;

        $where = $dao->getWhereInStatement( 'receivable_id', $id );
        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_Receivable_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 取所有收款项目
     */
    public function getReceivableArray()
    {
        $dao = dao_factory_base::getReceivableDao();
        $dao->setField( 'receivable_id,receivable_name,receivable_money,receivable_time' );
        $dao->setWhere( "uid={$this->uid} AND is_delete=0" );
        $dao->setOrderby( 'receivable_id DESC' );
        $res = $dao->getListByWhere();
        if ( $res ) {
            foreach ( $res as $value ) {
                $value->receivable_time = date( 'Y-m-d', $value->receivable_time );
            }
        }
        return $res;
    }

    /**
     * 取所有收款项目
     */
    public function getReceivableInfo( $receivable_id )
    {
        $dao = dao_factory_base::getReceivableDao();
        $dao->setField( 'receivable_id,uid,receivable_name,receivable_money,receivable_time' );
        $dao->setPk( $receivable_id );
        $res = $dao->getInfoByPk();
        if ( $res ) {
            $res->receivable_url = MOBILE_URL . 'receivable/' . $receivable_id . '.html';
            $res->receivable_time = date( 'Y-m-d H:i:s', $res->receivable_time );
        }
        return $res;
    }

}
