<?php

/**
 *
 * @Authors k-feng (wfylife@163.com)
 * @DateTime    2014-08-13 20:16:20
 * @version $1.0$
 */
class service_goods_Brand_admin extends service_Model_base
{

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
    }

    //插入数据
    public function createBrand( entity_brand_base $entity_brand_base )
    {
        $dao = dao_factory_base::getBrandDao();
        return $dao->insert( $entity_brand_base );
    }

    //更新数据
    public function modifyBrand( entity_brand_base $entity_brand_base )
    {
        $dao = dao_factory_base::getBrandDao();
        $dao->setPK( $entity_brand_base->brand_id );
        return $dao->updateByPk( $entity_brand_base );
    }

    //取单条信息
    public function getBrandInfo( $id )
    {
        $dao = dao_factory_base::getBrandDao();
        $dao->setPk( $id );
        return $dao->getInfoByPk();
    }

    //获取列表
    public function getBrandList( entity_parameter_brand_base $entity_parameter_brand_base )
    {
        $where = " is_delete=0 ";
        if ( $entity_parameter_brand_base->getUrl() == null ) {
            $url = PHP_SELF . '?m=goods/brand';
        } else {
            $url = $entity_parameter_brand_base->getUrl();
        }
        if ( !empty( $entity_parameter_brand_base->goods_cat_id ) ) {
            $url .= "&goods_cat_id={$entity_parameter_brand_base->goods_cat_id}";
            $where .= " AND goods_cat_id={$entity_parameter_brand_base->goods_cat_id}";
        }
        if ( $entity_parameter_brand_base->getQuery() != null ) {
            $url .= "&search_keyword={$entity_parameter_brand_base->getQuery()}";
            $where .= " AND brand_name like '%{$entity_parameter_brand_base->getQuery()}%'";
        }

        $url .= '&page=';
        $dao = dao_factory_base::getBrandDao();
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $url );
        $pages->setPrepage( $entity_parameter_brand_base->getPageSize() );
        $limit = $pages->getSqlLimit();
        $dao->setField( 'brand_id,brand_name,site_url,sort_order,is_delete,goods_cat_id' );
        $dao->setOrderby( 'brand_id DESC' );
        $dao->setLimit( $limit );
        $rs = $dao->getListByWhere();
        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = '暂无品牌!';
        }
        if ( $rs ) {
            $goods_category_map = $this->getGoodsCategoryMapArray();
            foreach ( $rs as $value ) {
                $value->cat_name = empty( $goods_category_map[ $value->goods_cat_id ] ) ? '' : $goods_category_map[ $value->goods_cat_id ];
            }
        }
        $result = array( 'rs' => $rs, 'pageCurrent' => $pages->getNowPage(), 'page' => $pages->show(), 'ErrorMsg' => $ErrorMsg );
        return $result;
    }

    //删除记录
    public function deleteByBrandId( $id )
    {
        $dao = dao_factory_base::getBrandDao();

        $dao->getDb()->startTrans();
        $entity_brand_base = new entity_Brand_base();
        $entity_brand_base->is_delete = 1;
        if ( strpos( $id, ',' ) === false ) {
            $dao->setPk( $id );
            $dao->updateByPk( $entity_brand_base );
//            $dao->deleteByPk();
        } else {
            $dao->setWhere( "brand_id IN({$id})" );
            $dao->updateByWhere( $entity_brand_base );
//            $dao->deleteByWhere();
        }

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    public function getGoodsCategoryMapArray()
    {
        $goods_category_array = $this->getTopLevelGoodsCategoryArray();
        $goods_category_map = array();
        foreach ( $goods_category_array as $value ) {
            $goods_category_map[ $value->goods_cat_id ] = $value->cat_name;
        }
        return $goods_category_map;
    }

    public function getTopLevelGoodsCategoryArray()
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setOrderby( 'cat_sort DESC, goods_cat_id ASC' );
        $where = 'is_delete=0';
        $where .= ' AND is_cloud_product=' . service_GoodsCategory_base::is_cloud_product_yes;
        $where .= ' AND cat_pid=0';
        $dao->setWhere( $where );
        $dao->setField( 'goods_cat_id,cat_name' );
        $res = $dao->getListByWhere();
        return $res;
    }

}
