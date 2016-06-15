<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_GoodsSave_manage extends service_goods_Save_base
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
    public function modifySupplierGoods( entity_Goods_base $entity_Goods_base )
    {
        $item_dao = dao_factory_base::getItemDao();
        $goods_dao = dao_factory_base::getGoodsDao();

        
          $repeat_status = $this->checkModifyGoodsRepeat( $entity_Goods_base->uid, $this->goods_id, $entity_Goods_base->goods_name );
          if ( $repeat_status ) {
          $this->errorMessage = '商品名:"' . $entity_Goods_base->goods_name . '"已经存在了重复';
          return FALSE;
          }
         
        //检测goods_source重复
        $goods_source_repeat = $this->checkGoodsSourceRepeat( $entity_Goods_base->goods_source, $entity_Goods_base->goods_source_id, $this->goods_id );
        if ( $goods_source_repeat === false ) {
            $this->errorMessage = '重复';
            return false;
        }
        $item_dao->getDb()->startTrans();


        if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
            //如果有商品规格，goods表中的价格是sku中最小的
            $sku_price_stock = $this->getGoodsSkuMinPriceAndStock();
            //如果有商品规格，goods表中的价格是sku中最小的
            $entity_Goods_base->goods_price = $sku_price_stock[ 'price' ];
            $entity_Goods_base->goods_stock = $sku_price_stock[ 'stock' ];


            if ( $entity_Goods_base->goods_price <= self::goods_price_need_shipping_fee 
                    && empty( $entity_Goods_base->shipping_fee ) 
                    && $entity_Goods_base->goods_source == service_Goods_base::goods_source_jd ) {
                $entity_Goods_base->shipping_fee = 10;
            }
        }
        //处理原价/实际销价
        $this->handelPromotePriceDifference( $entity_Goods_base );
        //处理佣金
        $this->handleGoodsCommissionFee( $entity_Goods_base );
        $goods_dao->setPk( $this->goods_id );
        $goods_dao->updateByPk( $entity_Goods_base );
        //更新item中分销的价格
        $entity_Item = new entity_Item_base();
        $entity_Item->item_price = $entity_Goods_base->goods_price;
        $entity_Item->item_stock = $entity_Goods_base->goods_stock;
        $entity_Item->outer_code = $entity_Goods_base->outer_code;
        $entity_Item->shipping_fee = $entity_Goods_base->shipping_fee;
        $entity_Item->commission_fee = $entity_Goods_base->commission_fee;
        $entity_Item->item_name = $entity_Goods_base->goods_name;
        $entity_Item->goods_image_id = $entity_Goods_base->goods_image_id;        
        $entity_Item->item_sort = $entity_Goods_base->goods_sort;        
        $entity_Item->goods_type = $entity_Goods_base->goods_type;
        $entity_Item->is_integral = $entity_Goods_base->is_integral;
        $item_dao->setWhere( "goods_id={$this->goods_id}" );
        $item_dao->updateByWhere( $entity_Item );

        //goods_image 商品图片表
        $this->_saveGoodsImage( $this->goods_id );
        if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
            //goods_spec 商品规格表
            $this->_saveGoodsSpec( $this->goods_id );
            //goods_sku 商品sku表
            $this->_saveGoodsSku( $this->goods_id );
        }

        //没有商品规格的时候 更新购物车中的商品价格
        if ( empty( $this->goods_spec_array ) && empty( $this->goods_sku_stock_array ) ) {
            $cart_dao = dao_factory_base::getCartDao();

            $entity_Cart_base = new entity_Cart_base();
            $entity_Cart_base->item_price = $entity_Goods_base->goods_price;
            $entity_Cart_base->outer_code = $entity_Goods_base->outer_code;
            $where = "goods_id={$this->goods_id}";
            $cart_dao->setWhere( $where );
            $cart_dao->updateByWhere( $entity_Cart_base );
        }

        //goods_category_map表
        $this->_saveGoodsCategoryMap( $this->goods_id );

        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $item_dao->getDb()->isSuccess() ) {
            $item_dao->getDb()->commit();
            return $this->goods_id;
        } else {
            $item_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 供应商的商品规格SKU更新
     * $this->goods_id;
     * $this->modifySupplierGoods( entity_Goods_base $entity_Goods_base );     
     * @param entity_Goods_base $entity_Goods_base
     * @return boolean
     */
    public function modifySupplierGoodsSku( entity_Goods_base $entity_Goods_base )
    {
        $item_dao = dao_factory_base::getItemDao();
        $goods_dao = dao_factory_base::getGoodsDao();

        $item_dao->getDb()->startTrans();

        if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
            //如果有商品规格，goods表中的价格是sku中最小的                
            $sku_price_stock = $this->getGoodsSkuMinPriceAndStock();
            //如果有商品规格，goods表中的价格是sku中最小的
            $entity_Goods_base->goods_price = $sku_price_stock[ 'price' ];
            $entity_Goods_base->goods_stock = $sku_price_stock[ 'stock' ];
        }
        $this->handleGoodsCommissionFee( $entity_Goods_base );
        $goods_dao->setPk( $this->goods_id );
        $goods_dao->updateByPk( $entity_Goods_base );
        //更新item中分销的价格
        $entity_Item = new entity_Item_base();
        $entity_Item->item_price = $entity_Goods_base->goods_price;
        $entity_Item->item_stock = $entity_Goods_base->goods_stock;
        $entity_Item->outer_code = $entity_Goods_base->outer_code;
        $item_dao->setWhere( "goods_id={$this->goods_id}" );
        $item_dao->updateByWhere( $entity_Item );

        if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
            //goods_spec 商品规格表
            $this->_saveGoodsSpec( $this->goods_id );
            //goods_sku 商品sku表
            $this->_saveGoodsSku( $this->goods_id );
        }

        //没有商品规格的时候 更新购物车中的商品价格
        if ( empty( $this->goods_spec_array ) && empty( $this->goods_sku_stock_array ) ) {
            $cart_dao = dao_factory_base::getCartDao();

            $entity_Cart_base = new entity_Cart_base();
            $entity_Cart_base->item_price = $entity_Goods_base->goods_price;
            $entity_Cart_base->outer_code = $entity_Goods_base->outer_code;
            $where = "goods_id={$this->goods_id}";
            $cart_dao->setWhere( $where );
            $cart_dao->updateByWhere( $entity_Cart_base );
        }

        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $item_dao->getDb()->isSuccess() ) {
            $item_dao->getDb()->commit();
            return $this->goods_id;
        } else {
            $item_dao->getDb()->rollback();
            return false;
        }
    }

}
