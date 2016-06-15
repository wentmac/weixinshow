<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_ItemCategory_base extends Model
{

    /**
     * 删除分类并删除分类下所有产品
     */
    const delete_type_delete_all_goods = 1;

    /**
     * 删除分类移除分类下的商品分类
     */
    const delete_type_remove_item_cat_id = 2;

    protected $uid;
    protected $errorMessage;
    protected $goods_number_rows;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function getGoods_number_rows()
    {
        return $this->goods_number_rows;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getItemCategoryList( $uid )
    {
        $dao = dao_factory_base::getItemCategoryDao();
        $dao->setWhere( "uid={$uid} AND is_delete=0" );
        $dao->setField( 'item_cat_id,cat_name,cat_keywords,cat_description,cat_sort,item_count' );
        $res = $dao->getListByWhere();
        return $res;
    }

    public function getCategoryInfo( $cat_id, $field = '*' )
    {
        $dao = dao_factory_base::getItemCategoryDao();
        $dao->setPk( $cat_id );
        $dao->setField( $field );
        $result = $dao->getInfoByPk();
        return $result;
    }

    /**
     * 取商品分类的全部商品ID
     * @param type $cat_id
     * @return type
     */
    public function getItemIdStringOfCategoryByCatid( $cat_id )
    {
        $dao = dao_factory_base::getItemCategoryMapDao();
        $dao->setField( 'item_id' );
        $where = "item_cat_id={$cat_id} AND is_delete=0";
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        $result_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $result_array[] = $value->item_id;
            }
        }
        return $result_array;
    }

    /**
     * 插入数据
     * @param entity_ItemCategory_base $entity_ItemCategory_base
     * @return type
     */
    public function createCategory( entity_ItemCategory_base $entity_ItemCategory_base )
    {
        $dao = dao_factory_base::getItemCategoryDao();
        //判断是不是存在
        $dao->setField( 'item_cat_id' );
        $where = "uid={$entity_ItemCategory_base->uid} AND cat_name='{$entity_ItemCategory_base->cat_name}'";
        $dao->setWhere( $where );
        $item_category_info = $dao->getInfoByWhere();
        if ( $item_category_info ) {//存在就更新
            $entity_ItemCategory_base = new entity_ItemCategory_base();
            $entity_ItemCategory_base->is_delete = 0;
            $dao->setPk( $item_category_info->item_cat_id );
            return $dao->updateByPk( $entity_ItemCategory_base );
        } else {//不存在就插入
            return $dao->insert( $entity_ItemCategory_base );
        }
    }

    /**
     * 根据主键更新
     * @param entity_ItemCategory_base $entity_ItemCategory_base
     * @return type
     */
    public function modifyCategoryByPk( entity_ItemCategory_base $entity_ItemCategory_base )
    {
        $dao = dao_factory_base::getItemCategoryDao();
        $dao->setPk( $entity_ItemCategory_base->item_cat_id );
        return $dao->updateByPk( $entity_ItemCategory_base );
    }

    /**
     * del
     * @param int $class_id
     */
    public function deleteItemCategoryById( $id, $delete_type )
    {
        if ( empty( $id ) ) {
            return true;
        }
        $dao = dao_factory_base::getItemCategoryDao();
        $item_dao = dao_factory_base::getItemDao();
        $item_category_map_dao = dao_factory_base::getItemCategoryMapDao();

        $dao->getDb()->startTrans();
        $entity_ItemCategory_base = new entity_ItemCategory_base();
        $entity_ItemCategory_base->is_delete = 1;
        $where = $dao->getWhereInStatement( 'item_cat_id', $id );

        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_ItemCategory_base );

        $goods_number_rows = 0;
        if ( $delete_type == self::delete_type_delete_all_goods ) {
            //删除分类下的所有的商品
            $where = "FIND_IN_SET  ('{$id}', item_cat_id)>0";
            $entity_Item_base = new entity_Item_base();
            $entity_Item_base->is_delete = 1;
            $item_dao->setWhere( $where );
            $item_dao->updateByWhere( $entity_Item_base );
            $goods_number_rows = $item_dao->getDb()->getNumRows();
        } else if ( $delete_type == self::delete_type_remove_item_cat_id ) {
            //删除分类移除分类下的商品分类
            $where = "FIND_IN_SET  ('{$id}', item_cat_id)>0 AND is_delete=0";
            $item_dao->setWhere( $where );
            $item_dao->setField( 'item_id,item_cat_id' );
            $res = $item_dao->getListByWhere();
            $i = 0;
            if ( $res ) {
                foreach ( $res AS $goods_object ) {
                    $entity_Item_base = new entity_Item_base();
                    $entity_Item_base->item_cat_id = $this->removeItemCatId( $id, $goods_object->item_cat_id );
                    $item_dao->setPk( $goods_object->item_id );
                    $item_dao->updateByPk( $entity_Item_base );
                    $i++;
                }
            }
            $goods_number_rows = $i;
        }
        $entity_ItemCategoryMap_base = new entity_ItemCategoryMap_base();
        $entity_ItemCategoryMap_base->is_delete = 1;
        $where = "item_cat_id={$id}";
        $item_category_map_dao->setWhere( $where );
        $item_category_map_dao->updateByWhere( $entity_ItemCategoryMap_base );
        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $this->goods_number_rows = $goods_number_rows;
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    public function removeItemCatId( $cat_id, $cat_id_string )
    {
        $item_cat_id_array = explode( ',', $cat_id_string );
        $new_item_cat_id_array = array();
        foreach ( $item_cat_id_array as $value ) {
            if ( $cat_id <> $value ) {
                $new_item_cat_id_array[] = $value;
            }
        }
        return implode( ',', $new_item_cat_id_array );
    }

    /**
     * 检测用户对 $item_cat_ids 的权限
     * @param type $id_string
     * @return boolean
     */
    public function checkPurview( $id_string )
    {
        $dao = dao_factory_base::getItemCategoryDao();
        $dao->setField( 'item_cat_id,uid' );
        $where = $dao->getWhereInStatement( 'item_cat_id', $id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        if ( $res ) {
            foreach ( $res AS $goods_catetory ) {
                if ( $goods_catetory->uid <> $this->uid ) {
                    $this->errorMessage = "您对分类ID:{$goods_catetory->item_cat_id} 没有权限";
                    return false;
                }
            }
            return true;
        }
        $this->errorMessage = "没有对应的权限";
        return false;
    }

    /**
     * 批量保存
     * @param type $param_array
     */
    public function batchSaveGoodsCategory( $param_array )
    {
        $dao = dao_factory_base::getItemCategoryDao();

        //先更新所有的为delete=1
        $where = "uid={$this->uid}";
        $entity_ItemCategory_base = new entity_ItemCategory_base();
        $entity_ItemCategory_base->is_delete = 1;

        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_ItemCategory_base );
        //取出所有的goods_spec_array                    
        $dao->setField( 'item_cat_id,cat_name,is_delete' );
        $res = $dao->getListByWhere();

        $goods_cat_update_array = array();

        if ( $res ) {
            foreach ( $res AS $goods_cat_object ) {
                $goods_cat_update_array[ $goods_cat_object->cat_name ] = $goods_cat_object->item_cat_id;
            }
        }
        //然后再把已经存在的delete=0
        foreach ( $param_array->modify AS $goods_cat_object ) {
            //update 状态                    
            $entity_ItemCategory_base = new entity_ItemCategory_base();

            $entity_ItemCategory_base->cat_name = $goods_cat_object->cat_name;
            $entity_ItemCategory_base->cat_sort = $goods_cat_object->sort_num;
            $entity_ItemCategory_base->is_delete = 0;

            $dao->setPk( $goods_cat_object->item_cat_id );
            $dao->updateByPk( $entity_ItemCategory_base );
        }
        foreach ( $param_array->create AS $goods_cat_object ) {
            if ( !empty( $goods_cat_update_array[ $goods_cat_object->cat_name ] ) ) {
                //update 状态                    
                $entity_ItemCategory_base = new entity_ItemCategory_base();

                $entity_ItemCategory_base->cat_name = $goods_cat_object->cat_name;
                $entity_ItemCategory_base->cat_sort = $goods_cat_object->sort_num;
                $entity_ItemCategory_base->is_delete = 0;

                $dao->setPk( $goods_cat_update_array[ $goods_cat_object->cat_name ] );
                $dao->updateByPk( $entity_ItemCategory_base );
                continue;
            }
            //插入不存在的
            $entity_ItemCategory_base = new entity_ItemCategory_base();
            $entity_ItemCategory_base->cat_name = $goods_cat_object->cat_name;
            $entity_ItemCategory_base->cat_sort = $goods_cat_object->sort_num;
            $entity_ItemCategory_base->uid = $this->uid;
            $entity_ItemCategory_base->is_delete = 0;

            $dao->insert( $entity_ItemCategory_base );
        }
        return true;
    }

}
