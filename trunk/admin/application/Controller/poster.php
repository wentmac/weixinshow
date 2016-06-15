<?php

/**
 * 后台文章模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: poster.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class posterAction extends service_Controller_admin
{

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );

        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin' );
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        $model = new service_Poster_admin();
        //TODO  取出所有广告
        $rs = $model->getPosterList();

        $this->assign( $rs );
        $this->V( 'poster' );
    }

    /**
     * 新增/修改文章页面
     */
    public function add()
    {
        $poster_id = Input::get( 'poster_id', 0 )->int();
        $editinfo = new entity_Poster_base();
        if ( $poster_id > 0 ) {
            $model = new service_Poster_admin();
            $editinfo = $model->getPosterInfo( $poster_id );
            if ( empty( $editinfo ) ) {
                $this->redirect( '广告不存在' );
            }
            $img = $model->getCfgBody( 'img', $editinfo->poster_imgurls );
            $imgid_array = $img[ 5 ];
            $imgurl_array = $model->getImageUrlArray( $img[ 5 ] );
            $self_field_array = $img[ 4 ];
            $sort_array = $img[ 3 ];
            $thumburl_array = $img[ 2 ];
            $thumbtitle_array = $img[ 1 ];
        } else {
            $imgid_array = array();
            $imgurl_array = array();
            $thumbtitle_array = array();
            $thumburl_array = array();
            $sort_array = array();
            $self_field_array = array();
        }
        //取广告类型option数组
        $poster_type_radio_ary = Tmac::config( 'poster.poster.poster_type', APP_ADMIN_NAME );
        $poster_type_radio_option = Utility::RadioButton( $poster_type_radio_ary, 'type_radio', $editinfo->poster_type_radio, 'onclick="typechange(this.value);"' );

        //取广告状态option数组
        $poster_state_radio_ary = Tmac::config( 'poster.poster.poster_state', APP_ADMIN_NAME );
        $poster_state_radio_option = Utility::RadioButton( $poster_state_radio_ary, 'state_radio', $editinfo->poster_state_radio, 'onclick="statechange(this.value);"' );


        //初始化一下    默认state_radio
        $editinfo->poster_starttime = !empty( $editinfo->poster_starttime ) ? date( 'Y-m-d', $editinfo->poster_starttime ) : '';
        $editinfo->poster_endtime = !empty( $editinfo->poster_endtime ) ? date( 'Y-m-d', $editinfo->poster_endtime ) : '';

        $this->assign( 'poster_type_radio_option', $poster_type_radio_option );
        $this->assign( 'poster_state_radio_option', $poster_state_radio_option );
        $this->assign( 'editinfo', $editinfo );
        $this->assign( 'imgid_array', $imgid_array );
        $this->assign( 'imgurl_array', $imgurl_array );
        $this->assign( 'thumbtitle_array', $thumbtitle_array );
        $this->assign( 'thumburl_array', $thumburl_array );
        $this->assign( 'sort_array', $sort_array );
        $this->assign( 'self_field_array', $self_field_array );
        //TODO　载入资讯类别添加表单
        $this->V( 'poster' );
    }

    /**
     * 广告管理 insert update => save()
     */
    public function save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 1 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }

        /* 初始化变量 */
        $poster_id = Input::post( 'poster_id', 0 )->int();
        $poster_title = Input::post( 'poster_title', '' )->required( '广告标题不能为空' )->string();
        $poster_name = Input::post( 'poster_name', '' )->required( '广告位置不能为空' )->string();
        $poster_link = Input::post( 'poster_link', '' )->string();
        $poster_width = Input::post( 'poster_width', 0 )->int();
        $poster_height = Input::post( 'poster_height', 0 )->int();
        $poster_type_radio = Input::post( 'type_radio', 0 )->int();
        $poster_starttime = Input::post( 'poster_starttime', 0 )->int();
        $poster_endtime = Input::post( 'poster_endtime', 0 )->int();
        $poster_state_radio = Input::post( 'state_radio', 0 )->int();

        $imgurl = Input::post( 'thumb', '' )->sql();
        $thumbdes = Input::post( 'thumbdes', '' )->sql();
        $thumburl = Input::post( 'thumburl', '' )->sql();
        $sort_array = Input::post( 'sort', '' )->sql();
        $self_field_array = Input::post( 'self_field', '' )->sql();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }


        if ( $poster_state_radio == service_Poster_admin::poster_state_radio_general && (empty( $poster_starttime ) || empty( $poster_endtime )) ) {
            $this->redirect( '广告开始时间结束时间不能为空' );
        }

        if ( $poster_type_radio == service_Poster_admin::poster_type_radio_image && (empty( $imgurl ) || empty( $thumbdes ) || empty( $thumburl )) ) {
            $this->redirect( '广告图文不能为空' );
        }
        $imgurls = '';
        if ( !empty( $imgurl ) ) {
            if ( is_array( $imgurl ) ) {
                foreach ( $imgurl AS $k => $v ) {
                    $thumbtitle = $thumbdes[ $k ];
                    $thumburls = $thumburl[ $k ];
                    $sort = $sort_array[ $k ];
                    $self_field = $self_field_array[ $k ];
                    $imgurls.="{img key=\"$thumbtitle\" url=\"$thumburls\" sort=\"$sort\" self_field=\"$self_field\"}$v{/img}";
                }
            }
        }
        
        // TODO goon to verify
        $entity_Poster_base = new entity_Poster_base();
        $entity_Poster_base->poster_title = $poster_title;
        $entity_Poster_base->poster_name = $poster_name;
        $entity_Poster_base->poster_link = $poster_link;
        $entity_Poster_base->poster_width = $poster_width;
        $entity_Poster_base->poster_height = $poster_height;
        $entity_Poster_base->poster_type_radio = $poster_type_radio;
        $entity_Poster_base->poster_starttime = strtotime( $poster_starttime );
        $entity_Poster_base->poster_endtime = strtotime( $poster_endtime );
        $entity_Poster_base->poster_state_radio = $poster_state_radio;
        $entity_Poster_base->poster_imgurls = $imgurls;

        $model = new service_Poster_admin();
        if ( $poster_id > 0 ) {
            $entity_Poster_base->poster_id = $poster_id;
            $rs = $model->modifyPoster( $entity_Poster_base );
            if ( $rs ) {
                $this->redirect( '修改广告内容成功' );
            } else {
                $this->redirect( '修改广告内容失败' );
            }
        } else {
            $entity_Poster_base->poster_time = $this->now;
            //insert save article_class
            $rs = $model->createPoster( $entity_Poster_base );
            if ( $rs ) {
                $this->redirect( '添加广告成功' );
            } else {
                $this->redirect( '添加广告失败' );
            }
        }
    }

    /**
     * 批量操作
     */
    public function action_do()
    {
        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'id', 0 )->int();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();

        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            $this->redirect( '请选择要操作的...' );
            exit;
        }

        if ( $do == 'del' || $act == 'del' ) {
            $model = new service_Poster_admin();
            $rs = $model->deletePosterId( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->redirect( '删除成功' );
            } else {
                $this->redirect( '删除失败，请重试！' );
            }
        }
    }

}
