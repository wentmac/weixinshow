<?php

/**
 * WEB 分销代理上架
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_OperationLog_base extends service_Model_base
{

    protected $errorMessage;

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * $this->goods_id;
     * $this->uid;
     * $this->goodsAgentSave();
     */
    public function createGoodsOperationLog( entity_GoodsOperationLog_base $entity_GoodsOperationLog_base )
    {
        $dao = dao_factory_base::getGoodsOperationLogDao();
        return $dao->insert( $entity_GoodsOperationLog_base );
    }

}
