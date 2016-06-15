<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Save_admin extends service_goods_Save_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 供应商的商品修改
     * $this->goods_id;
     * $this->modifySupplierGoods( entity_Goods_base $entity_Goods_base );
     * @param entity_Item_base $entity_Item_base
     * @param entity_Goods_base $entity_Goods_base
     * @return boolean
     */
    public function modifyAdminGoods( entity_Goods_base $entity_Goods_base )
    {
        $goods_dao = dao_factory_base::getGoodsDao();
        $item_dao = dao_factory_base::getItemDao();


        $goods_dao->getDb()->startTrans();

        $entity_Goods_base->brand_name = $this->getBrandNameById( $entity_Goods_base->brand_id );
        $goods_dao->setPk( $this->goods_id );
        $goods_dao->updateByPk( $entity_Goods_base );

        $entity_Item_base = new entity_Item_base();
        $entity_Item_base->commission_seller_different = $entity_Goods_base->commission_seller_different;
        $entity_Item_base->commission_different_object = $entity_Goods_base->commission_different_object;        
        $entity_Item_base->brand_id = $entity_Goods_base->brand_id;
        $entity_Item_base->brand_name = $entity_Goods_base->brand_name;
        $where = 'goods_id=' . $this->goods_id;
        $item_dao->setWhere( $where );
        $item_dao->updateByWhere( $entity_Item_base );

        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $goods_dao->getDb()->isSuccess() ) {
            $goods_dao->getDb()->commit();
            return true;
        } else {
            $goods_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 更新用户的所有商品到云端商品库中
     * @param type $uid
     * @return type
     */
    public function updateGoodsSupplier( $uid )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setWhere( "uid={$uid}" );
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->is_supplier = service_Goods_base::is_supplier_yes;

        return $dao->updateByWhere( $entity_Goods_base );
    }

}
