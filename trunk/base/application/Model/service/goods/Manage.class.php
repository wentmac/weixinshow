<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Manage_base extends service_Goods_base
{

    /**
     * 检测item_id权限时 返回有权限的item_id当key值。对应item_info是value值      
     * @var type 
     */
    protected $item_array;

    function setItem_array( $item_array )
    {
        $this->item_array = $item_array;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @param type $uid
     * @return type
     */
    public function getUserSpecArray( $uid )
    {
        $spec_map_dao = dao_factory_base::getSpecMapDao();

        $where = "uid={$uid}";

        $spec_map_dao->setWhere( $where );
        $spec_map_dao->setField( 'spec_id,spec_name' );
        $spec_map_array = $spec_map_dao->getListByWhere();

        $spec_field = 'spec_id,spec_value_id,spec_value_name';
        if ( $uid == service_Member_base::yph_uid ) {
            $where = 'goods_id=' . $this->goods_id;
            $goods_spec_dao = dao_factory_base::getGoodsSpecDao();
            $goods_spec_dao->setWhere( $where );
            $goods_spec_dao->setField( $spec_field );
            $spec_value_map_array = $goods_spec_dao->getListByWhere();
        } else {            
            $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();
            $spec_value_map_dao->setWhere( $where );
            $spec_value_map_dao->setField( $spec_field );
            $spec_value_map_array = $spec_value_map_dao->getListByWhere();
        }

        $spec_array = array();
        if ( empty( $spec_map_array ) ) {
            //TODO insert default spec
        } else {
            $spec_value_array = array();
            foreach ( $spec_value_map_array AS $spec_value_map_object ) {
                $spec_value_array[ $spec_value_map_object->spec_id ][] = $spec_value_map_object;
            }
            foreach ( $spec_map_array as $spec_map_object ) {
                $spec_map_object->value_list = empty( $spec_value_array[ $spec_map_object->spec_id ] ) ? '' : $spec_value_array[ $spec_map_object->spec_id ];
                $spec_array[] = $spec_map_object;
            }
        }
        return $spec_array;
    }

    /**
     * 
     * @param type $uid
     * @return type
     */
    public function getUserSpecObjectArray( $uid )
    {
        $spec_map_dao = dao_factory_base::getSpecMapDao();
        $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();

        $where = "uid={$uid}";

        $spec_map_dao->setWhere( $where );
        $spec_map_dao->setField( 'spec_id,spec_name' );
        $spec_map_array = $spec_map_dao->getListByWhere();

        $spec_value_map_dao->setWhere( $where );
        $spec_value_map_dao->setField( 'spec_id,spec_value_id,spec_value_name' );
        $spec_value_map_array = $spec_value_map_dao->getListByWhere();

        $spec_array = array();
        if ( empty( $spec_map_array ) ) {
            //TODO insert default spec
        } else {
            $spec_value_array = array();
            foreach ( $spec_value_map_array AS $spec_value_map_object ) {
                $spec_value_array[ $spec_value_map_object->spec_id ][ $spec_value_map_object->spec_value_id ] = $spec_value_map_object;
            }
            foreach ( $spec_map_array as $spec_map_object ) {
                $spec_map_object->value_list = empty( $spec_value_array[ $spec_map_object->spec_id ] ) ? '' : $spec_value_array[ $spec_map_object->spec_id ];
                $spec_array[ $spec_map_object->spec_id ] = $spec_map_object;
            }
        }
        return $spec_array;
    }

    /**
     * 
     * uid=46时太占内存了废掉
     * @param type $uid
     * @return type
     */
    public function getUserSpecValueArrayDelete( $uid )
    {
        $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();

        $where = "uid={$uid}";

        $spec_value_map_dao->setWhere( $where );
        $spec_value_map_dao->setField( 'spec_value_id,spec_id,spec_value_name' );
        $spec_value_map_array = $spec_value_map_dao->getListByWhere();

        $spec_value_id_array = $spec_value_name_array = array();
        if ( !empty( $spec_value_map_array ) ) {
            foreach ( $spec_value_map_array AS $spec_value_map_object ) {
                $spec_value_id_array[ $spec_value_map_object->spec_value_id ] = strtoupper( $spec_value_map_object->spec_value_name );
                $spec_value_name = strtoupper( $spec_value_map_object->spec_value_name );
                $spec_value_name_array[ $spec_value_name ][ $spec_value_map_object->spec_id ] = $spec_value_map_object->spec_value_id;
            }
        }
        return array(
            'key_spec_value_id' => $spec_value_id_array,
            'key_spec_value_name' => $spec_value_name_array,
        );
    }

    /**
     * 
     * @param type $uid
     * @return type
     */
    public function getUserSpecValueArray( $uid, $goods_spec_array )
    {
        $spec_value_name_array = $spec_value_id_array = array();
        foreach ( $goods_spec_array as $goods_spec ) {
            foreach ( $goods_spec as $spec_value_name ) {
                if ( is_numeric( $spec_value_name ) === false ) {
                    $spec_value_name_array[] = $spec_value_name;
                } else {
                    $spec_value_id_array[] = $spec_value_name;
                }
            }
        }
        $spec_value_name_string = "'" . implode( '\',\'', $spec_value_name_array ) . "'";
        $spec_value_id_string = implode( ',', $spec_value_id_array );
        $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();


        $where = "uid={$uid}";
        if ( $spec_value_id_array && $spec_value_name_array ) {
            $where .= ' AND (' . $spec_value_map_dao->getWhereInStatement( 'spec_value_name', $spec_value_name_string );
            $where .= ' OR ' . $spec_value_map_dao->getWhereInStatement( 'spec_value_id', $spec_value_id_string ) . ')';
        } else if ( $spec_value_name_array ) {
            $where .= ' AND ' . $spec_value_map_dao->getWhereInStatement( 'spec_value_name', $spec_value_name_string );
        } else if ( $spec_value_id_array ) {
            $where .= ' AND ' . $spec_value_map_dao->getWhereInStatement( 'spec_value_id', $spec_value_id_string );
        }
        $spec_value_map_dao->setWhere( $where );
        $spec_value_map_dao->setField( 'spec_value_id,spec_id,spec_value_name' );
        $spec_value_map_array = $spec_value_map_dao->getListByWhere();

        $spec_value_id_array = $spec_value_name_array = array();
        if ( !empty( $spec_value_map_array ) ) {
            foreach ( $spec_value_map_array AS $spec_value_map_object ) {
                $spec_value_id_array[ $spec_value_map_object->spec_value_id ] = strtoupper( $spec_value_map_object->spec_value_name );
                $spec_value_name = strtoupper( $spec_value_map_object->spec_value_name );
                $spec_value_name_array[ $spec_value_name ][ $spec_value_map_object->spec_id ] = $spec_value_map_object->spec_value_id;
            }
        }
        return array(
            'key_spec_value_id' => $spec_value_id_array,
            'key_spec_value_name' => $spec_value_name_array,
        );
    }

    /**
     * 检测用户对 $item_ids 的权限
     * @param type $id_string
     * @return boolean
     */
    public function checkPurview( $id_string )
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setField( 'item_id,goods_id,uid,goods_uid' );
        $where = $dao->getWhereInStatement( 'item_id', $id_string );
        //$where .= " AND is_delete=0";
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        $result_array = array();
        if ( $res ) {
            foreach ( $res AS $item ) {
                if ( $item->uid <> $this->uid ) {
                    $this->errorMessage = "您对ID:{$item->item_id} 没有权限";
                    return false;
                }
                $result_array[ $item->item_id ] = $item;
            }
            return $result_array;
        }
        $this->errorMessage = "没有对应的权限";
        return false;
    }

    /**
     * 检测用户对 $item_ids 的权限
     * @param type $id_string
     * @return boolean
     */
    public function checkGoodsIdStringPurview( $id_string )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id,uid' );
        $where = $dao->getWhereInStatement( 'goods_id', $id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        if ( $res ) {
            foreach ( $res AS $goods ) {
                if ( $goods->uid <> $this->uid ) {
                    $this->errorMessage = "您对ID:{$goods->goods_id} 没有权限";
                    return false;
                }
            }
            return true;
        }
        $this->errorMessage = "没有对应的权限";
        return false;
    }

    /**
     * 检测图片ID（$goods_image_id）的删除权限
     * @param type $goods_image_id
     * @return boolean
     */
    public function checkGoodsImagePurviewByItemId( $goods_image_id )
    {
        $item_info = parent::getItemInfoById( 'goods_id,uid' );
        $item_info instanceof entity_Item_base;
        if ( $item_info == false ) {
            $this->errorMessage = '商品项目找不到 ：－）';
            return false;
        }
        $this->goods_id = $item_info->goods_id;
        $goods_info = parent::getGoodsInfoById( 'uid' );
        if ( $goods_info == false ) {
            $this->errorMessage = '商品找不到 ：－）';
            return false;
        }
        if ( $goods_info->uid <> $item_info->uid ) {
            $this->errorMessage = '只能删除自己的商品图片，不能删除分销的商品图片 ：－）';
            return false;
        }

        $dao = dao_factory_base::getGoodsImageDao();

        $where = "goods_id={$this->goods_id} AND goods_image_id='{$goods_image_id}'";
        $dao->setField( 'id,uid' );
        $dao->setWhere( $where );
        $goods_image_info = $dao->getInfoByWhere();
        if ( !$goods_image_info ) {
            $this->errorMessage = '图片不存在';
            return FALSE;
        }
        if ( $goods_image_info->uid <> $this->uid ) {
            $this->errorMessage = '没有图片的删除权限哟 ：－）';
            return false;
        }
        return $goods_image_info;
    }

    /**
     * 检测图片ID（$goods_image_id）的删除权限
     * @param type $goods_image_id
     * @return boolean
     * 
     * $this->uid;
     * $this->goods_id;
     * $this->checkGoodsImagePurview( $goods_image_id );
     */
    public function checkGoodsImagePurview( $goods_image_id )
    {
        $goods_info = parent::getGoodsInfoById( 'uid' );
        if ( $goods_info == false ) {
            $this->errorMessage = '商品找不到 ：－）';
            return false;
        }
        if ( $goods_info->uid <> $this->uid ) {
            $this->errorMessage = '只能删除自己的商品图片，不能删除分销的商品图片 ：－）';
            return false;
        }

        $dao = dao_factory_base::getGoodsImageDao();

        $where = "goods_id={$this->goods_id} AND goods_image_id='{$goods_image_id}'";
        $dao->setField( 'id,uid' );
        $dao->setWhere( $where );
        $goods_image_info = $dao->getInfoByWhere();
        if ( !$goods_image_info ) {
            $this->errorMessage = '图片不存在';
            return FALSE;
        }
        if ( $goods_image_info->uid <> $this->uid ) {
            $this->errorMessage = '没有图片的删除权限哟 ：－）';
            return false;
        }
        return $goods_image_info;
    }

    public function deleteGoodsImageById( $id )
    {
        $dao = dao_factory_base::getGoodsImageDao();
        $dao->setPk( $id );
        $entity_GoodsImage_base = new entity_GoodsImage_base();
        $entity_GoodsImage_base->is_delete = 1;
        $res = $dao->updateByPk( $entity_GoodsImage_base );
        if ( $res ) {
            $this->syncGoodsImageIds();
            return true;
        } else {
            return false;
        }
    }

    private function syncGoodsImageIds()
    {
        $goods_image_dao = dao_factory_base::getGoodsImageDao();
        $goods_image_dao->setField( 'goods_image_id' );
        $where = "goods_id={$this->goods_id} AND is_delete=0";
        $goods_image_dao->setWhere( $where );

        $goods_image_array = $goods_image_dao->getListByWhere();
        $goods_image_ary = array();

        foreach ( $goods_image_array as $goods_image_object ) {
            $goods_image_ary[] = $goods_image_object->goods_image_id;
        }
        $dao = dao_factory_base::getGoodsDao();
        $dao->setPk( $this->goods_id );
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->goods_image_id = empty( $goods_image_ary ) ? '' : $goods_image_ary[ 0 ];
        $entity_Goods_base->goods_image_ids = json_encode( $goods_image_ary );

        return $dao->updateByPk( $entity_Goods_base );
    }

    public function getGoodsSkuArray()
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setWhere( "goods_id={$this->goods_id} AND is_delete=0" );
        $dao->setField( 'goods_sku,price,stock,outer_code,sales_volume' );
        $res = $dao->getListByWhere();
        $result_array = array();
        if ( $res ) {
            foreach ( $res AS $goods_sku_obj ) {
                $result_array[ $goods_sku_obj->goods_sku ] = $goods_sku_obj;
            }
        }
        if ( empty( $result_array ) ) {
            return new stdClass();
        }
        return $result_array;
    }

    /**
     * 删除item表
     * @param type $id
     * @return boolean
     */
    public function deleteItemById( $id )
    {
        $goods_dao = dao_factory_base::getGoodsDao();
        $dao = dao_factory_base::getItemDao();

        $dao->getDb()->startTrans();
        $entity_Item_base = new entity_Item_base();
        $entity_Item_base->is_delete = 1;

        $where = $dao->getWhereInStatement( 'item_id', $id );
        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_Item_base );

        $this->_updateItemCategoryMap( $id );

        //更新商品的 分销人数总数
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->seller_count = new TmacDbExpr( 'seller_count-1' );

        $goods_id_string = $this->getGoodsIdStringByItemId( $id );
        $goods_dao->updateGoodsSellerCount( $goods_id_string );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 
     * @param type $item_id_string
     */
    private function getGoodsIdStringByItemId( $item_id_string )
    {
        $dao = dao_factory_base::getItemDao();
        $where = $dao->getWhereInStatement( 'item_id', $item_id_string );
        $dao->setWhere( $where );
        $dao->setField( 'goods_id' );
        $item_array = $dao->getListByWhere();
        $goods_id_array = array();
        foreach ( $item_array as $value ) {
            $goods_id_array[] = $value->goods_id;
        }
        return implode( ',', $goods_id_array );
    }

    /**
     * 给manage用
     * 快速修改商品 上下线状态以及删除     
     * $this->goods_id;     
     * $this->status;
     * $this->batchGoodsStatus();
     * 
     */
    public function batchGoodsStatus()
    {
        //分类不为空 修改分类           
        $goods_dao = dao_factory_base::getGoodsDao();
        $item_dao = dao_factory_base::getItemDao();

        $goods_itemlist_model = new service_goods_ItemList_base();
        $is_delete = $goods_itemlist_model->getIsDeleteByStatus( $this->status );

        if ( $is_delete == service_Goods_base::is_delete_yes ) {
            return $this->deleteGoodsById( $this->goods_id );
        }

        $goods_dao->getDb()->startTrans();

        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->is_delete = $is_delete;
        $goods_where = $goods_dao->getWhereInStatement( 'goods_id', $this->goods_id );
        $goods_dao->setWhere( $goods_where );
        $goods_dao->updateByWhere( $entity_Goods_base );


        $entity_Item_base = new entity_Item_base();
        $entity_Item_base->is_delete = $is_delete;
        $where = $item_dao->getWhereInStatement( 'goods_id', $this->goods_id );
        if ( $is_delete == service_Goods_base::is_delete_no ) {//上线
            $entity_Item_base->supplier_offline = 0;
            $where .= " AND is_delete=" . service_Goods_base::is_delete_shelves; //只要原来是强制下线的才能强制上线
        } else if ( $is_delete == service_Goods_base::is_delete_shelves ) {//下线
            $entity_Item_base->supplier_offline = 1; //item表中 标识出 是 供应商强制下线
        }
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

    /**
     * 供应商商品删除
     * @param type $id
     * @return boolean
     */
    public function deleteGoodsById( $id )
    {
        $goods_dao = dao_factory_base::getGoodsDao();
        $dao = dao_factory_base::getItemDao();

        $dao->getDb()->startTrans();


        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->is_delete = 1;
        $where = $dao->getWhereInStatement( 'goods_id', $id );
        $goods_dao->setWhere( $where );
        $goods_dao->updateByWhere( $entity_Goods_base );

        //供应商，如果是自己的商品同时删除goods表的中商品。并且下架在item表中的所在分销商品
        $entity_Item_base = new entity_Item_base();
        $entity_Item_base->is_delete = service_Goods_base::is_delete_shelves;

        $goods_delete_where = $dao->getWhereInStatement( 'goods_id', $id );
        $dao->setWhere( $goods_delete_where );
        $dao->updateByWhere( $entity_Item_base );

        $this->_updateGoodsCategoryMap( $id );
        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 删除item时调用的，把对应的item category map一起删除了
     * 调用的时候要包上事务
     */
    public function _updateItemCategoryMap( $item_id )
    {
        $item_category_map_dao = dao_factory_base::getItemCategoryMapDao();

        //先更新所有的为delete=1
        $where = $item_category_map_dao->getWhereInStatement( 'item_id', $item_id );
        $entity_ItemCategoryMap_base = new entity_ItemCategoryMap_base();
        $entity_ItemCategoryMap_base->is_delete = 1;

        $item_category_map_dao->setWhere( $where );
        return $item_category_map_dao->updateByWhere( $entity_ItemCategoryMap_base );
    }

    /**
     * 保存 商品分类映射
     * 调用的时候要包上事务
     */
    protected function _updateGoodsCategoryMap( $goods_id )
    {
        $goods_category_map_dao = dao_factory_base::getGoodsCategoryMapDao();

        //goods_category表中的goods减量一根据where条件
        $goods_category_map_dao->updateGoodsCategoryGoodsCount( $goods_id );

        //先更新所有的为delete=1
        $where = $goods_category_map_dao->getWhereInStatement( 'goods_id', $goods_id );
        $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
        $entity_GoodsCategoryMap_base->is_delete = 1;

        $goods_category_map_dao->setWhere( $where );
        $goods_category_map_dao->updateByWhere( $entity_GoodsCategoryMap_base );
        return true;
    }

    /**
     * 通$goods_cat_id取 goods_category的map映射
     * @param type $goods_cat_id
     * @return type
     */
    public function getGoodsCategoryMap( $goods_cat_id )
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $where = $dao->getWhereInStatement( 'goods_cat_id', $goods_cat_id );
        $dao->setWhere( $where );
        $dao->setField( 'goods_cat_id,cat_name' );
        $res = $dao->getListByWhere();
        $result_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $result_array[ $value->goods_cat_id ] = $value->cat_name;
            }
        }
        return $result_array;
    }

    static public function getBrandArray()
    {
        $dao = dao_factory_base::getBrandDao();
        $dao->setField( 'brand_id,brand_name' );
        $dao->setWhere( 'is_delete=0' );
        $dao->setOrderby( 'sort_order DESC' );
        return $dao->getListByWhere();
    }

}
