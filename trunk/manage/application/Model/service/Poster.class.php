<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Poster_manage extends service_Poster_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function customPosterSave( entity_Poster_base $entity_Poster_base )
    {
        $dao = dao_factory_base::getPosterDao();
        $where = "uid={$entity_Poster_base->uid} AND poster_name='{$entity_Poster_base->poster_name}'";
        $dao->setWhere( $where );
        $custom_poster_info = $dao->getInfoByWhere();
        if ( $custom_poster_info ) {//判断是否存在
            //存在更新特定字段
            $custom_poster_info = $this->getPosterCustomInfo( $entity_Poster_base->poster_name );
            $custom_poster_info instanceof entity_Poster_base;
            $entity_Poster_base->poster_title = $custom_poster_info->poster_title;
            $entity_Poster_base->poster_link = $custom_poster_info->poster_link;
            $entity_Poster_base->poster_width = $custom_poster_info->poster_width;
            $entity_Poster_base->poster_height = $custom_poster_info->poster_height;
            $entity_Poster_base->poster_type_radio = $custom_poster_info->poster_type_radio;
            $entity_Poster_base->poster_starttime = $custom_poster_info->poster_starttime;
            $entity_Poster_base->poster_endtime = $custom_poster_info->poster_endtime;
            $entity_Poster_base->poster_state_radio = $custom_poster_info->poster_state_radio;
            $entity_Poster_base->poster_time = $this->now;
            $entity_Poster_base->is_delete = 0;
            $res = $dao->updateByWhere( $entity_Poster_base );
        } else {
            $custom_poster_info = $this->getPosterCustomInfo( $entity_Poster_base->poster_name );
            $custom_poster_info instanceof entity_Poster_base;
            $entity_Poster_base->poster_title = $custom_poster_info->poster_title;
            $entity_Poster_base->poster_link = $custom_poster_info->poster_link;
            $entity_Poster_base->poster_width = $custom_poster_info->poster_width;
            $entity_Poster_base->poster_height = $custom_poster_info->poster_height;
            $entity_Poster_base->poster_type_radio = $custom_poster_info->poster_type_radio;
            $entity_Poster_base->poster_starttime = $custom_poster_info->poster_starttime;
            $entity_Poster_base->poster_endtime = $custom_poster_info->poster_endtime;
            $entity_Poster_base->poster_state_radio = $custom_poster_info->poster_state_radio;
            $entity_Poster_base->poster_time = $this->now;
            $entity_Poster_base->is_delete = 0;
            $res = $dao->insert( $entity_Poster_base );
        }
        return $res;
    }

    private function getPosterSystemInfo( $poster_name )
    {
        $dao = dao_factory_base::getPosterDao();
        $where = "uid=0 AND poster_name='{$poster_name}'";
        $dao->setWhere( $where );
        $res = $dao->getInfoByWhere();
        return $res;
    }

    public function getPosterCustomInfo( $poster_name )
    {
        $dao = dao_factory_base::getPosterDao();
        $where = "uid={$this->mall_uid} AND poster_name='{$poster_name}' AND is_delete=0";
        $dao->setWhere( $where );
        $res = $dao->getInfoByWhere();
        return $res;
    }

    /**
     * 取买家的订单列表
     * $this->where;
     * $this->getBuyerOrderList();
     */
    public function getPosterCustomList()
    {

        $dao = dao_factory_base::getPosterDao();

        $where = "uid={$this->mall_uid} AND is_delete=0";
        $dao->setWhere( $where );
        $dao->setField( 'poster_id,poster_title,poster_name' );
        $res = $dao->getListByWhere();

        $return_array = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $return_array[ $value->poster_name ] = $value;
            }
        }
        return $return_array;
    }

}
