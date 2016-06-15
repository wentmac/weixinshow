<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Manage_admin extends service_goods_Manage_base
{

    /**
     * 商品中分类批量
     * 商品分类叠加
     */
    const batch_category_action_add = 'add';

    /**
     * 商品中分类批量
     * 商品分类重置为一个分类
     */
    const batch_category_action_modify = 'modify';

    /**
     * 商品中分类批量
     * 从分类中删除
     */
    const batch_category_action_delete = 'del';

    public function __construct()
    {
        parent::__construct();
    }

    public function getGoodsAllFieldSkuArray()
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setWhere( "goods_id={$this->goods_id} AND is_delete=0" );
        $dao->setField( 'goods_sku_id,goods_sku,price,stock,outer_code,goods_sku_json,sales_volume' );
        $res = $dao->getListByWhere();
        $result_array = array();
        if ( $res ) {
            foreach ( $res AS $goods_sku_obj ) {
                $goods_sku_obj->goods_sku_json = unserialize( $goods_sku_obj->goods_sku_json );
                $result_array[ $goods_sku_obj->goods_sku_id ] = $goods_sku_obj;
            }
        }
        return $result_array;
    }

    /**
     * 只取goods的全部Sku_id
     * @return type
     */
    public function getGoodsSkuIDArray( $goods_id )
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setWhere( "goods_id={$goods_id} AND is_delete=0" );
        $dao->setField( 'goods_sku_id' );
        $res = $dao->getListByWhere();
        return $res;
    }

    public function getGoodsInfo( $image_size = '80' )
    {
        $goods_info = parent::getGoodsInfo( $image_size );
        if ( $goods_info ) {
            $is_supplier_array = Tmac::config( 'goods.goods.is_supplier', APP_BASE_NAME );
            $goods_source_array = Tmac::config( 'goods.goods.goods_source', APP_BASE_NAME );
            $goods_info->goods_time = date( 'Y-m-d H:i:s', $goods_info->goods_time );
            $goods_info->is_supplier = $is_supplier_array[ $goods_info->is_supplier ];
            $goods_info->goods_source = $goods_source_array[ $goods_info->goods_source ];
        }
        return $goods_info;
    }

    public function batchModifyGoods( $id, entity_Goods_base $entity_Goods_base )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->getDb()->startTrans();

        $where = $dao->getWhereInStatement( 'goods_id', $id );
        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_Goods_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    private function deleteGoodsCategoryMap( $goods_id, $goods_cat_id )
    {
        $goods_category_map_dao = dao_factory_base::getGoodsCategoryMapDao();
        $goods_category_map_dao->getDb()->startTrans();
        //然后再把已经存在的delete=0
        $goods_id_array = explode( ',', $goods_id );
        foreach ( $goods_id_array AS $goods_id ) {
            $where = $goods_category_map_dao->getWhereInStatement( 'goods_id', $goods_id );
            $where .= ' AND goods_cat_id=' . $goods_cat_id;
            $goods_category_map_dao->setWhere( $where );

            //先更新所有的为delete=1            
            $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
            $entity_GoodsCategoryMap_base->is_delete = 1;

            $goods_category_map_dao->updateByWhere( $entity_GoodsCategoryMap_base );
            $this->updateGoodsCatId( $goods_id, $goods_cat_id, self::batch_category_action_delete );
        }

        $this->updateCategoryGoodsCount( $goods_cat_id );
        if ( $goods_category_map_dao->getDb()->isSuccess() ) {
            $goods_category_map_dao->getDb()->commit();
            return true;
        } else {
            $goods_category_map_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 保存 商品分类映射
     */
    public function saveGoodsCategoryMap( $goods_id, $goods_cat_id, $category_type )
    {
        if ( $category_type == self::batch_category_action_delete ) {
            return $this->deleteGoodsCategoryMap( $goods_id, $goods_cat_id );
        }
        $goods_category_map_dao = dao_factory_base::getGoodsCategoryMapDao();
        $goods_category_map_dao->getDb()->startTrans();

        //然后再把已经存在的delete=0
        $goods_id_array = explode( ',', $goods_id );
        foreach ( $goods_id_array AS $goods_id ) {
            $where = $goods_category_map_dao->getWhereInStatement( 'goods_id', $goods_id );
            $goods_category_map_dao->setWhere( $where );
            if ( $category_type != self::batch_category_action_add ) {//商品分类重置为一个        
                //先更新所有的为delete=1            
                $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
                $entity_GoodsCategoryMap_base->is_delete = 1;

                $goods_category_map_dao->updateByWhere( $entity_GoodsCategoryMap_base );
            }


            //取出所有的goods_spec_array
            $goods_category_map_dao->setField( 'goods_cat_map_id,goods_id,goods_cat_id,is_delete' );
            $res = $goods_category_map_dao->getListByWhere();

            $goods_category_update_array = array();
            if ( $res ) {
                foreach ( $res AS $goods_category_object ) {
                    $goods_category_update_array[ $goods_category_object->goods_cat_id ] = $goods_category_object->goods_cat_map_id;
                }
            }
            if ( !empty( $goods_category_update_array[ $goods_cat_id ] ) ) {
                //update 状态                    
                $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
                $entity_GoodsCategoryMap_base->is_delete = 0;

                $goods_category_map_dao->setPk( $goods_category_update_array[ $goods_cat_id ] );
                $goods_category_map_dao->updateByPk( $entity_GoodsCategoryMap_base );
                $this->updateGoodsCatId( $goods_id, $goods_cat_id, $category_type );
                continue;
            }
            $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
            $entity_GoodsCategoryMap_base->goods_id = $goods_id;
            $entity_GoodsCategoryMap_base->goods_cat_id = $goods_cat_id;
            $entity_GoodsCategoryMap_base->is_delete = 0;

            $goods_category_map_dao->insert( $entity_GoodsCategoryMap_base );

            $this->updateGoodsCatId( $goods_id, $goods_cat_id, $category_type );
        }
        $this->updateCategoryGoodsCount( $goods_cat_id );
        if ( $goods_category_map_dao->getDb()->isSuccess() ) {
            $goods_category_map_dao->getDb()->commit();
            return true;
        } else {
            $goods_category_map_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 更新分类的商品总数
     * @param type $item_cat_id
     */
    private function updateGoodsCatId( $goods_id, $goods_cat_id, $category_type )
    {
        $goods_dao = dao_factory_base::getGoodsDao();
        $goods_dao->setPk( $goods_id );

        $entity_Goods_base = new entity_Goods_base();
        if ( $category_type == self::batch_category_action_add ) {//商品分类叠加
            $goods_dao->setField( 'goods_cat_id' );
            $goods_info = $goods_dao->getInfoByPk();
            $source_goods_cat_id = $goods_info->goods_cat_id;
            if ( empty( $source_goods_cat_id ) ) {
                $entity_Goods_base->goods_cat_id = $goods_cat_id;
            } else {
                $goods_cat_id_array = explode( ',', $source_goods_cat_id );
                if ( in_array( $goods_cat_id, $goods_cat_id_array ) ) {
                    return true;
                } else {
                    $entity_Goods_base->goods_cat_id = $source_goods_cat_id . ',' . $goods_cat_id;
                }
            }
        } else if ( $category_type == self::batch_category_action_delete ) {//商品删除分类
            $goods_dao->setField( 'goods_cat_id' );
            $goods_info = $goods_dao->getInfoByPk();
            $source_goods_cat_id = $goods_info->goods_cat_id;
            if ( empty( $source_goods_cat_id ) ) {
                return true;
            } else {
                $goods_cat_id_array = explode( ',', $source_goods_cat_id );
                if ( in_array( $goods_cat_id, $goods_cat_id_array ) ) {
                    $new_goods_cat_id_array = array();
                    foreach ( $goods_cat_id_array as $value ) {
                        if ( $value == $goods_cat_id ) {
                            continue;
                        }
                        $new_goods_cat_id_array[] = $value;
                    }
                    $entity_Goods_base->goods_cat_id = implode( ',', $new_goods_cat_id_array );
                } else {
                    return true;
                }
            }
        } else {//商品分类重置为一个
            $entity_Goods_base->goods_cat_id = $goods_cat_id;
        }
        return $goods_dao->updateByPk( $entity_Goods_base );
    }

    /**
     * 更新分类的商品总数
     * @param type $item_cat_id
     */
    private function updateCategoryGoodsCount( $goods_cat_id )
    {
        $goods_category_dao = dao_factory_base::getGoodsCategoryDao();
        $goods_category_map_dao = dao_factory_base::getGoodsCategoryMapDao();

        $where = "goods_cat_id={$goods_cat_id} AND is_delete=0";
        $goods_category_map_dao->setWhere( $where );
        $goods_count = $goods_category_map_dao->getCountByWhere();

        $entity_GoodsCategory_base = new entity_GoodsCategory_base();
        $entity_GoodsCategory_base->goods_count = $goods_count;
        $goods_category_dao->setPk( $goods_cat_id );
        return $goods_category_dao->updateByPk( $entity_GoodsCategory_base );
    }

    public function batchUpdateGoodsBrandId( $brand_id )
    {
        $brand_dao = dao_factory_base::getBrandDao();
        $brand_dao->setField( 'brand_id,brand_name' );
        $brand_dao->setPk( $brand_id );
        $brand_info = $brand_dao->getInfoByPk();
        if ( empty( $brand_info ) ) {
            $this->errorMessage = '品牌不存在';
            return false;
        }
        $goods_dao = dao_factory_base::getGoodsDao();
        $item_dao = dao_factory_base::getItemDao();

        $goods_dao->getDb()->startTrans();


        $where = $goods_dao->getWhereInStatement( 'goods_id', $this->goods_id );
        $goods_dao->setWhere( $where );

        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->brand_id = $brand_id;
        $entity_Goods_base->brand_name = $brand_info->brand_name;
        $goods_dao->updateByWhere( $entity_Goods_base );

        $entity_Item_base = new entity_Item_base();
        $entity_Item_base->brand_id = $brand_id;
        $entity_Item_base->brand_name = $brand_info->brand_name;

        $item_dao->setWhere( $where );
        $item_dao->updateByWhere( $entity_Item_base );

        if ( $goods_dao->getDb()->isSuccess() ) {
            $goods_dao->getDb()->commit();
            return true;
        } else {
            $goods_dao->getDb()->rollback();
            return false;
        }
    }

    public function getGoodsPriceInfoByGoodsId( $goods_agent )
    {
        $goods_agent = json_decode( $goods_agent, true );
        if ( empty( $goods_agent ) ) {
            $member_config_array = Tmac::config( 'member.member.member_class', APP_BASE_NAME );
            $member_agent_array = $member_config_array[ service_Member_base::member_type_mall ];
            $result = array();
            foreach ( $member_agent_array as $key => $value ) {
                if ( $key == 0 ) {
                    continue;
                }
                $value_res = array();
                $value_res[ 'member_agent' ] = $key;
                $value_res[ 'price' ] = 0;
                $value_res[ 'sku_array' ] = array();
                $result[] = $value_res;
            }
            $goods_agent = $result;
        }
        return $this->getGoodsAgentMap( $goods_agent );        
    }

    private function getGoodsAgentMap( $goods_agent )
    {
        $res = $agent = array();
        foreach ( $goods_agent as $value ) {
            $agent = $value;
            $sku_res = array();
            foreach ( $value[ 'sku_array' ] as $sku ) {
                $sku_res[ $sku[ 'sku_id' ] ] = $sku;
            }
            $agent[ 'sku_array' ] = $sku_res;
            $res [ $value[ 'member_agent' ] ] = $agent;
        }

        return $res;
    }

}
