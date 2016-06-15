<?php

/**
 * 接口 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Model.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
abstract class service_Model_base extends Model
{
    //protected $common_api_model;

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * 取用户背景图片
     * @param type $imageId
     * @param type $size
     * @return type 
     */
    protected function getImage( $imageId, $size, $type = 'article' )
    {
        if ( empty( $imageId ) ) {
            return '';
        }
        if ( empty( $size ) ) {
            return IMAGE_URL . 'article/' . $imageId . '.jpg';
        }
        return THUMB_URL . $type . '_' . $size . '/' . $imageId . '.jpg';
    }

    /**
     * 判断传过来的字段是否有效
     * @param type $field
     * @param type $key_name_array
     * @return type 
     */
    protected function checkField( $field, $key_name_array )
    {
        $field_array = explode( ',', $field );
        if ( is_array( $field_array ) ) {
            $field_string = '';
            foreach ( $field_array AS $k => $v ) {
                if ( in_array( $v, $key_name_array ) ) {
                    $field_string .= ',' . $v;
                }
            }
            $field_string = substr( $field_string, 1 );
            return $field_string;
        }
        return '';
    }

    /**
     * 获取MySQL分页SQL的LIMIT语句
     * @param type $total
     * @param type $perpage
     * @param type $currentPage
     * @return type 
     */
    protected function getSqlLimit( $total, $perpage, $currentPage )
    {
        $pages = ceil( $total / $perpage );
        //如果当前currentPage 大于 总pages的话就显示最后一页
        $currentPage > $pages && $currentPage = $pages;
        return ($currentPage - 1) * $perpage . ',' . $perpage;
    }

}
