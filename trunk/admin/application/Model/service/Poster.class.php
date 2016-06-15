<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Poster_admin extends service_Poster_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取买家的订单列表
     * $this->where;
     * $this->getBuyerOrderList();
     */
    public function getPosterList()
    {
        if ( empty( $this->url ) ) {
            $url = PHP_SELF . '?m=poster.index';
        } else {
            $url = $this->url;
        }


        $dao = dao_factory_base::getPosterDao();
        $url .= '&page=';
        $where = 'is_delete=0';
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $url );
        $pages->setPrepage( 50 );
        $limit = $pages->getSqlLimit();

        $res = array();
        if ( $count > 0 ) {
            $dao->setLimit( $limit );
            $dao->setOrderby( 'poster_id DESC' );
            $res = $dao->getListByWhere();

            $poster_type_array = Tmac::config( 'poster.poster.poster_type', APP_ADMIN_NAME );
            $poster_state_array = Tmac::config( 'poster.poster.poster_state', APP_ADMIN_NAME );
            foreach ( $res as $value ) {

                $value->poster_starttime = date( 'Y-m-d H:i:s', $value->poster_starttime );
                $value->poster_endtime = date( 'Y-m-d H:i:s', $value->poster_endtime );
                $value->poster_time = date( 'Y-m-d H:i:s', $value->poster_time );

                $value->poster_type_radio_text = isset( $poster_type_array[ $value->poster_type_radio ] ) ? $poster_type_array[ $value->poster_type_radio ] : '';
                $value->poster_state_radio_text = isset( $poster_state_array[ $value->poster_state_radio ] ) ? $poster_state_array[ $value->poster_state_radio ] : '';
            }
        }
        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无广告!";
        }

        $result = array(
            'rs' => $res,
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg
        );
        return $result;
    }


}
