<?php

/**
 * 后台文章栏目模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zwt007 $  <zwttmac@qq.com>
 * $Id: category.class.php 99 2012-02-16 15:49:31Z zwt007 $
 * http://www.t-mac.org；
 */
class service_Category_base extends service_Model_base
{
    private $top_cat_pid;

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取分类列表
     * @global type $category_list
     * @param type $parent
     * @param type $url
     * @param type $username
     * @param type $indexurl
     * @return type 
     */
    public function getCategoryList( $parent )
    {

        $catetory_array = $this->getCategoryArray();
        global $category_list;
        $category_list = $this->fenlei( $catetory_array, $parent );
        return $category_list;
    }

    /**
     * 取出所有Category数组
     * @param type $uid
     * @return type 
     */
    public function getCategoryArray()
    {
        $dao = dao_factory_base::getCategoryDao();
        $dao->setField( 'cat_id,cat_pid,cat_name,channeltype,category_nicename,category_content' );
        $dao->setOrderby( 'cat_order DESC, cat_id ASC' );
        return $dao->getListByWhere();
    }

    /**
     * 插入数据
     * @param entity_Category_base $entity_Category_base
     * @return type
     */
    public function createCategory( entity_Category_base $entity_Category_base )
    {
        $dao = dao_factory_base::getCategoryDao();
        return $dao->insert( $entity_Category_base );
    }

    /**
     * 根据主键更新
     * @param entity_Category_base $entity_Category_base
     * @return type
     */
    public function modifyCategoryByPk( entity_Category_base $entity_Category_base )
    {
        $dao = dao_factory_base::getCategoryDao();
        $dao->setPk( $entity_Category_base->cat_id );
        return $dao->updateByPk( $entity_Category_base );
    }

    /**
     * 获取一个资讯栏目信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getCategoryInfo( $class_id, $field = '*' )
    {
        $dao = dao_factory_base::getCategoryDao();
        $dao->setField( $field );
        $dao->setPk( $class_id );
        return $dao->getInfoByPk();
    }

    /**
     * 取所有的子ID
     * @global type $son_tree_list
     * @param type $parent
     * @return type 
     */
    public function getSonTreeList( $parent )
    {        
        $catetory_array = $this->getCategoryArray();
        global $son_tree_list;        
        $son_tree_list = '';
        $son_tree_list = $this->son_fenlei( $catetory_array, $parent );

        if ( empty( $son_tree_list ) ) {
            $cat_id_rs = $parent;
        } else {
            $cat_id_rs = $son_tree_list . ',' . $parent;
            $cat_id_rs = substr( $cat_id_rs, 1 );
        }
        $cat_id_array = explode( ',', $cat_id_rs );
        $cat_id_arr = array_unique( $cat_id_array );

        $cat_id_rs = implode( ',', $cat_id_arr );
        return $cat_id_rs;
    }

    /**
     * 取所有的子ID
     * @global type $son_tree_list
     * @param type $arr
     * @param type $parent
     * @return type 
     */
    protected function son_fenlei( $arr, $parent )
    {        
        global $son_tree_list; //定义全局变量 返回值        
        $num = count( $arr );

        for ( $i = 0; $i < $num; $i++ ) {//循环该层
            if ( $arr[ $i ]->cat_pid == $parent ) {  //层中符合父级id的元素输出                                 
                $arr_array = $arr[ $i ];
                $son_tree_list .= ',' . $arr_array->cat_id;
                $this->son_fenlei( $arr, $arr[ $i ]->cat_id );  //递归执行寻找当前元素的子元素，这些子元素<ul><li>当前元素<ul><li>子元素</li></ul></li></ul>                
            }
        }

        return $son_tree_list;
    }

    /**
     * 取一个分类ID的最父级分类ID
     * @param type $cat_id
     */
    public function getTopCatPidByCatId( $cat_pid )
    {
        $catetory_array = $this->getCategoryArray();
        $this->getTopCatPid( $catetory_array, $cat_pid );
        
        return $this->top_cat_pid;
    }

    private function getTopCatPid( $catetory_array, $cat_pid )
    {
        foreach ( $catetory_array as $catinfo ) {
            if ( $catinfo->cat_id == $cat_pid ) {
                $this->top_cat_pid = $catinfo->cat_id;
                empty( $catinfo->cat_pid ) || $this->getTopCatPid( $catetory_array, $catinfo->cat_id );
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
        $dao = dao_factory_base::getCategoryDao();
        $dao->setField( 'cat_id,cat_pid,cat_name,category_nicename' );
        $where = $dao->getWhereInStatement( 'cat_id', $cat_id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $category_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $category_array[ $value->cat_pid ][ $value->cat_id ] = $value;
            }
        }
        return $category_array;
    }

}
