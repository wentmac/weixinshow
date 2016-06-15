<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Save_manage extends service_goods_Save_base
{

    protected $item_id_string;
    protected $item_goods_map_array;

    function setItem_id_string( $item_id_string )
    {
        $this->item_id_string = $item_id_string;
    }

    function setItem_goods_map_array( $item_goods_map_array )
    {
        $this->item_goods_map_array = $item_goods_map_array;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 批量更新多个商品的多个分类保存
     * $this->item_id;
     * $this->item_cat_id_array;
     * $this->item_goods_map_array;
     * $this->batchModifyItemCategory();
     */
    public function batchModifyItemCategory()
    {
        $dao = dao_factory_base::getItemDao();
        $dao->getDb()->startTrans();

        $item_id_array = explode( ',', $this->item_id_string );

        $entity_Item_base = new entity_Item_base();
        foreach ( $item_id_array as $item_id ) {
            $entity_Item_base->item_cat_id = implode( ',', $this->item_cat_id_array );
            $dao->setPk( $item_id );
            $dao->updateByPk( $entity_Item_base );

            $this->item_id = $item_id;
            $goods_id = $this->item_goods_map_array[ $item_id ]->goods_id;
            parent::_saveItemCategoryMap( $item_id, $goods_id );
        }

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

}
