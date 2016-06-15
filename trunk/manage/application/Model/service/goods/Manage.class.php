<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Manage_manage extends service_goods_Manage_base
{
    protected $status;

    function setStatus( $status )
    {
        $this->status = $status;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取商品Goods的分类上下级
     * @param type $goods_cat_id
     */
    public function getGoodsCategoryByID( $goods_cat_id )
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setField('goods_cat_id,cat_name');
        $where = "cat_pid={$goods_cat_id} AND is_cloud_product=1 AND is_delete=0";
        $dao->setWhere($where);
        $res = $dao->getListByWhere();
        return $res;
    }

}
