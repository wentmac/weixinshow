<?php

/**
 * 后台文章栏目模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Category.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_help_Category_base extends service_Model_base
{

    /**
     * 帮助分类
     * 聚店App
     */
    const cat_type_app = 0;

    /**
     * 帮助分类
     * 聚店商城
     */
    const cat_type_mall = 1;
    
    private $top_cat_pid;
    private $son_tree_list;
    protected $category_list;

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
        $dao = dao_factory_base::getHelpCategoryDao();
        $dao->setOrderby( 'cat_sort DESC, help_cat_id ASC' );
        $dao->setWhere( 'is_delete=0' );
        return $dao->getListByWhere();
    }

    /**
     * 插入数据
     * @param entity_HelpCategory_base $entity_HelpCategory_base
     * @return type
     */
    public function createCategory( entity_HelpCategory_base $entity_HelpCategory_base )
    {
        $dao = dao_factory_base::getHelpCategoryDao();
        return $dao->insert( $entity_HelpCategory_base );
    }

    /**
     * 根据主键更新
     * @param entity_HelpCategory_base $entity_HelpCategory_base
     * @return type
     */
    public function modifyCategoryByPk( entity_HelpCategory_base $entity_HelpCategory_base )
    {
        $dao = dao_factory_base::getHelpCategoryDao();
        $dao->setPk( $entity_HelpCategory_base->help_cat_id );
        return $dao->updateByPk( $entity_HelpCategory_base );
    }

    /**
     * 获取一个资讯栏目信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getCategoryInfo( $help_cat_id, $field = '*' )
    {
        $dao = dao_factory_base::getHelpCategoryDao();
        $dao->setField( $field );
        $dao->setPk( $help_cat_id );
        return $dao->getInfoByPk();
    }

    /**
     * 取所有的子ID
     * @global type $this->son_tree_list
     * @param type $parent
     * @return type 
     */
    public function getSonTreeList( $parent )
    {
        $catetory_array = $this->getCategoryArray();
        $this->son_tree_list = '';
        $this->son_tree_list = $this->son_fenlei( $catetory_array, $parent );

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
    protected function son_fenlei( $arr, $parent )
    {
        $num = count( $arr );
        for ( $i = 0; $i < $num; $i++ ) {//循环该层            
            if ( $arr[ $i ]->cat_pid == $parent ) {  //层中符合父级id的元素输出                                 
                $arr_array = $arr[ $i ];
                $this->son_tree_list .= ',' . $arr_array->help_cat_id;
                $this->son_fenlei( $arr, $arr[ $i ]->help_cat_id );  //递归执行寻找当前元素的子元素，这些子元素<ul><li>当前元素<ul><li>子元素</li></ul></li></ul>                
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
        $catetory_array = $this->getCategoryArray();
        $this->getTopCatPid( $catetory_array, $cat_pid );

        return $this->top_cat_pid;
    }

    private function getTopCatPid( $catetory_array, $cat_pid )
    {
        foreach ( $catetory_array as $catinfo ) {
            if ( $catinfo->help_cat_id == $cat_pid ) {
                $this->top_cat_pid = $catinfo->help_cat_id;
                empty( $catinfo->cat_pid ) || $this->getTopCatPid( $catetory_array, $catinfo->help_cat_id );
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
        $dao = dao_factory_base::getHelpCategoryDao();
        $dao->setField( 'help_cat_id,cat_pid,cat_name' );
        $where = $dao->getWhereInStatement( 'help_cat_id', $cat_id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $category_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $category_array[ $value->cat_pid ][ $value->help_cat_id ] = $value;
            }
        }
        return $category_array;
    }

}
