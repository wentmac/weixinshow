<?php

/**
 * WEB 分销代理上架
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_AgentSave_admin extends service_goods_AgentSave_base
{

    private $goods_array;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * $this->goods_id;
     * $this->uid;
     * $this->batchGoodsAgentSave();
     */
    public function batchGoodsAgentSave()
    {
        $dao = dao_factory_base::getItemDao();
        $goods_dao = dao_factory_base::getGoodsDao();
        //判断是否是自己的。自己的不能代理
        $goods_array = $this->checkGoodsPurview();
        if ( $goods_array == false ) {
            return false;
        }


        foreach ( $this->goods_array as $goodsInfo ) {
            $dao->getDb()->startTrans();
            $goodsInfo instanceof entity_Goods_base;

            $dao->setField( 'item_id,is_delete' );
            $dao->setWhere( "uid={$this->uid} AND goods_id={$goodsInfo->goods_id}" );
            $checkItemInfo = $dao->getInfoByWhere();
            if ( $checkItemInfo && $checkItemInfo->is_delete == 0 ) {
                continue;
            } else if ( $checkItemInfo && ($checkItemInfo->is_delete == 1 || $checkItemInfo->is_delete == 2) ) {//更新状态
                $entity_Item_base = new entity_Item_base();
                $entity_Item_base->is_delete = 0;
                $dao->setPk( $checkItemInfo->item_id );
                $rs = $dao->updateByPk( $entity_Item_base );
                if ( $rs && $dao->getDb()->isSuccess() ) {
                    $dao->getDb()->commit();
                    continue;
                }
                $this->errorMessage .= $goodsInfo->goods_id . ':重新上架失败，请联系客服MM';
                continue;
            }
            //item表中写入数据

            $entity_Item_base = new entity_Item_base();
            $entity_Item_base->goods_id = $goodsInfo->goods_id;
            $entity_Item_base->item_name = $goodsInfo->goods_name;                        
            $entity_Item_base->item_stock = $goodsInfo->goods_stock;
            $entity_Item_base->item_price = $goodsInfo->goods_price;
            $entity_Item_base->outer_code = $goodsInfo->outer_code;            
            $entity_Item_base->goods_image_id = $goodsInfo->goods_image_id;
            $entity_Item_base->item_sort = 0;
            $entity_Item_base->item_time = $this->now;
            $entity_Item_base->item_modify_time = $this->now;
            $entity_Item_base->comment_count = 0;
            $entity_Item_base->click_count = 0;
            //$entity_Item_base->sales_volume = $goodsInfo->sales_volume;
            $entity_Item_base->uid = $this->uid;
            $entity_Item_base->goods_uid = $goodsInfo->uid;
            $entity_Item_base->collect_count = 0;
            $entity_Item_base->shipping_fee = $goodsInfo->shipping_fee;
            $entity_Item_base->commission_fee = $goodsInfo->commission_fee;
            $dao->insert( $entity_Item_base );

            //更新商品的 分销人数总数
            $entity_Goods_base = new entity_Goods_base();
            $entity_Goods_base->seller_count = new TmacDbExpr( 'seller_count+1' );

            $goods_dao->setPk( $goodsInfo->goods_id );
            $goods_dao->updateByPk( $entity_Goods_base );

            if ( $dao->getDb()->isSuccess() ) {
                $dao->getDb()->commit();                
            } else {
                $dao->getDb()->rollback();
                $this->errorMessage .= $goodsInfo->goods_id . '上架失败，请联系客服MM';
            }
        }
        return true;
    }

    /**
     * 判断是否是自己的。自己的不能代理
     */
    private function checkGoodsPurview()
    {
        $dao = dao_factory_base::getGoodsDao();
        $where = $dao->getWhereInStatement( 'goods_id', $this->goods_id );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        if ( empty( $res ) ) {
            $this->errorMessage = '要上架的商品为空';
            return FALSE;
        }
        $goods_array = array();
        foreach ( $res as $goods_info ) {
            if ( !$goods_info ) {
                //throw new TmacClassException( '要上架的产品不存在哟' );
                continue;
            }
            if ( $goods_info->is_delete == 1 ) {
                //throw new TmacClassException( '要上架的产品已经删除了' );
                continue;
            }
            if ( $goods_info->uid == $this->uid ) {
                //throw new TmacClassException( '不能上架自己的产品哟' );
                continue;
            }
            $goods_array[] = $goods_info;
        }


        $this->goods_array = $goods_array;
        return true;
    }

}
