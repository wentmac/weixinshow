<?php

/**
 * 后台文章栏目模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: GoodsCategory.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_GoodsCategory_base extends service_Model_base
{

    /**
     * 商品分类 类型
     * 临时分类
     */
    const is_cloud_product_temp = 0;

    /**
     * 商品分类 类型
     * 云端商品库分类
     */
    const is_cloud_product_yes = 1;

    /**
     * 商品分类 类型
     * 精选分类
     */
    const is_cloud_product_recommend = 2;

    /**
     * 商品分类 类型
     * 推荐/专题分类
     */
    const is_cloud_product_topic = 3;

    private $top_cat_pid;
    private $son_tree_list;
    protected $category_list;
    protected $is_cloud_product = false;
    protected $category_array;

    function setIs_cloud_product( $is_cloud_product )
    {
        $this->is_cloud_product = $is_cloud_product;
    }

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取出所有Category数组
     * @param type $uid
     * @return type 
     */
    public function getCategoryArray()
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setOrderby( 'cat_sort DESC, goods_cat_id ASC' );
        $where = 'is_delete=0';
        if ( $this->is_cloud_product !== false ) {
            $where .= ' AND is_cloud_product=' . $this->is_cloud_product;
        }
        $dao->setWhere( $where );
        return $dao->getListByWhere();
    }

    /**
     * 插入数据
     * @param entity_GoodsCategory_base $entity_GoodsCategory_base
     * @return type
     */
    public function createCategory( entity_GoodsCategory_base $entity_GoodsCategory_base )
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        return $dao->insert( $entity_GoodsCategory_base );
    }

    /**
     * 根据主键更新
     * @param entity_GoodsCategory_base $entity_GoodsCategory_base
     * @return type
     */
    public function modifyCategoryByPk( entity_GoodsCategory_base $entity_GoodsCategory_base )
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setPk( $entity_GoodsCategory_base->goods_cat_id );
        return $dao->updateByPk( $entity_GoodsCategory_base );
    }

    /**
     * 获取一个资讯栏目信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getCategoryInfo( $goods_cat_id, $field = '*' )
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setField( $field );
        $dao->setPk( $goods_cat_id );
        return $dao->getInfoByPk();
    }

    /**
     * 取所有的子ID
     * @global type $this->son_tree_list
     * @param type $parent
     * @return type 
     */
    public function getSonTreeList( $parent, $is_cloud_product = 0 )
    {
        $catetory_array = $this->getCategoryArray();
        $this->son_tree_list = '';
        $this->son_tree_list = $this->son_fenlei( $catetory_array, $parent, $is_cloud_product );

        if ( empty( $this->son_tree_list ) ) {
            $cat_id_rs = $parent;
        } else {
            $cat_id_rs = $this->son_tree_list . ',' . $parent;
            $cat_id_rs = substr( $cat_id_rs, 1 );
        }
        $cat_id_array = explode( ',', $cat_id_rs );
        $cat_id_arr = array_unique( $cat_id_array );

        $cat_id_rs = implode( ',', $cat_id_arr );
        return $cat_id_rs;
    }

    /**
     * 取所有的子ID
     * @global type $this->son_tree_list
     * @param type $arr
     * @param type $parent
     * @return type 
     */
    protected function son_fenlei( $arr, $parent, $is_cloud_product )
    {
        $num = count( $arr );
        for ( $i = 0; $i < $num; $i++ ) {//循环该层
            if ( $is_cloud_product <> $arr[ $i ]->is_cloud_product ) {
                continue;
            }
            if ( $arr[ $i ]->cat_pid == $parent ) {  //层中符合父级id的元素输出                                 
                $arr_array = $arr[ $i ];
                $this->son_tree_list .= ',' . $arr_array->goods_cat_id;
                $this->son_fenlei( $arr, $arr[ $i ]->goods_cat_id, $is_cloud_product );  //递归执行寻找当前元素的子元素，这些子元素<ul><li>当前元素<ul><li>子元素</li></ul></li></ul>                
            }
        }

        return $this->son_tree_list;
    }

    /**
     * 取一个分类ID的最父级分类ID
     * @param type $cat_id
     */
    public function getTopCatPidByCatId( $cat_pid )
    {
        if ( empty( $this->category_array ) ) {
            $catetory_array = $this->getCategoryArray();
            $this->category_array = $catetory_array;
        } else {
            $catetory_array = $this->category_array;
        }

        $this->getTopCatPid( $catetory_array, $cat_pid );

        return $this->top_cat_pid;
    }

    private function getTopCatPid( $catetory_array, $cat_pid )
    {
        foreach ( $catetory_array as $catinfo ) {
            if ( $catinfo->goods_cat_id == $cat_pid ) {
                $this->top_cat_pid = $catinfo->goods_cat_id;
                empty( $catinfo->cat_pid ) || $this->getTopCatPid( $catetory_array, $catinfo->cat_pid );                
            }
        }
        return $this->top_cat_pid;
    }

    /**
     * 取数据
     * @param type $cat_id_string
     * @return type
     */
    public function getCategoryArrayByCatIdString( $cat_id_string )
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setField( 'goods_cat_id,cat_pid,cat_name' );
        $where = $dao->getWhereInStatement( 'cat_id', $cat_id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $category_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $category_array[ $value->cat_pid ][ $value->goods_cat_id ] = $value;
            }
        }
        return $category_array;
    }

    /**
     * 取顶级分类数组
     */
    public function getCategoryTopArray()
    {
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setField( 'goods_cat_id,cat_name' );
        $where = "cat_pid=0 AND is_cloud_product=" . self::is_cloud_product_yes . " AND is_delete=0";
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        return $res;
    }

}
