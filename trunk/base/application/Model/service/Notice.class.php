<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Notice_base extends service_Model_base
{

    protected $notice_id;
    protected $pagesize;
    protected $errorMessage;

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setNotice_id( $notice_id )
    {
        $this->notice_id = $notice_id;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getNoticeInfoById( $id )
    {
        $dao = dao_factory_base::getNoticeDao();
        $dao->setField( 'notice_id,notice_title,notice_content,notice_time' );
        $dao->setPk( $id );
        $notice_info = $dao->getInfoByPk();
        if ( $notice_info ) {
            $notice_info->notice_time = date( 'Y-m-d H:i:s', $notice_info->notice_time );
        }
        return $notice_info;
    }

    /**
     * 取所有店铺的所有商品
     * $this->page;
     * $this->pagesize;
     * $this->getNoticeArray();
     */
    public function getNoticeArray()
    {
        $dao = dao_factory_base::getNoticeDao();
        $where = 'is_delete=0';
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $res = array();
        if ( $count > 0 ) {
            $dao->setLimit( $limit );
            $dao->setField( 'notice_id,notice_title,notice_image_id,notice_time' );
            $res = $dao->getListByWhere();

            foreach ( $res as $value ) {
                $value->notice_time = date( 'Y-m-d H:i:s', $value->notice_time );
                if ( empty( $value->notice_image_id ) ) {
                    $value->notice_image_url = STATIC_URL . 'common/notice_demo.png';
                } else {
                    $value->notice_image_url = $this->getImage( $value->notice_image_id, '550x260', 'notice' );
                }
                $value->notice_url = MOBILE_URL . 'notice/' . $value->notice_id . '.html';
                unset( $value->notice_image_id );
            }
        }
        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / $this->pagesize ) ),
            'pagesize' => $this->pagesize,
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'notice_list',
            'retmsg' => $retmsg,
            'reqdata' => $res,
        );
        return $return;
    }

}
