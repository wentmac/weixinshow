<?php

/**
 * 后台文章模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Article.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_help_Article_base extends service_Model_base
{

    /**
     * 090聚店App
     */
    const cat_type_app = 0;

    /**
     * 090聚店商城
     */
    const cat_type_mall = 1;
    
    protected $url;
    protected $help_cat_id;
    protected $query_string;
    protected $help_recommend;

    function setUrl( $url )
    {
        $this->url = $url;
    }

    function setHelp_cat_id( $help_cat_id )
    {
        $this->help_cat_id = $help_cat_id;
    }

    function setQuery_string( $query_string )
    {
        $this->query_string = $query_string;
    }

    function setHelp_recommend( $help_recommend )
    {
        $this->help_recommend = $help_recommend;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取一个资讯栏目信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getHelpArticleInfo( $id )
    {
        $dao = dao_factory_base::getHelpArticleDao();
        $dao->setPk( $id );
        return $dao->getInfoByPk();
    }

}
