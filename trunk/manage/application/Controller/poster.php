<?php

/**
 * 后台文章模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: poster.php 343 2016-06-07 11:00:00Z zhangwentao $
 * http://www.t-mac.org；
 */
class posterAction extends service_Controller_manage
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        $model = new service_Poster_manage();
        $model->setMall_uid( $this->memberInfo->uid );

        //TODO  取出所有广告
        $poster_array = $model->getPosterCustomList();
        $poster_custom_array = Tmac::config( 'poster.poster_custom', APP_ADMIN_NAME );

        $array[ 'poster_array' ] = $poster_array;
        $array[ 'poster_custom_array' ] = $poster_custom_array;
//        $this->apiReturn($array);die;
        $this->assign( $array );
        $this->V( 'poster' );
    }

    /**
     * 新增/修改文章页面
     */
    public function add()
    {
        $poster_name = Input::get( 'poster_name', 0 )->string();
        $editinfo = new entity_Poster_base();

        $imgid_array = array();
        $imgurl_array = array();
        $thumbtitle_array = array();
        $thumburl_array = array();
        $sort_array = array();
        $self_field_array = array();

        if ( !empty( $poster_name ) ) {
            $model = new service_Poster_manage();
            $model->setMall_uid( $this->memberInfo->uid );
            $editinfo = $model->getPosterCustomInfo( $poster_name );
            if ( !empty( $editinfo ) ) {
                $img = $model->getCfgBody( 'img', $editinfo->poster_imgurls );
                $imgid_array = $img[ 5 ];
                $imgurl_array = $model->getImageUrlArray( $img[ 5 ] );
                $self_field_array = $img[ 4 ];
                $sort_array = $img[ 3 ];
                $thumburl_array = $img[ 2 ];
                $thumbtitle_array = $img[ 1 ];
            }
        }

        $poster_custom_array = Tmac::config( 'poster.poster_custom', APP_ADMIN_NAME );

        $this->assign( 'editinfo', $editinfo );
        $this->assign( 'poster_name', $poster_name );
        $this->assign( 'poster_custom_array', $poster_custom_array );
        $this->assign( 'imgid_array', $imgid_array );
        $this->assign( 'imgurl_array', $imgurl_array );
        $this->assign( 'thumbtitle_array', $thumbtitle_array );
        $this->assign( 'thumburl_array', $thumburl_array );
        $this->assign( 'sort_array', $sort_array );
        $this->assign( 'self_field_array', $self_field_array );

        //TODO　载入资讯类别添加表单
        $this->V( 'poster_detail' );
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
        $poster_name = Input::post( 'poster_name', '' )->required( '广告位置不能为空' )->string();

        $imgurl = Input::post( 'thumb', '' )->sql();
        $thumbdes = Input::post( 'thumbdes', '' )->sql();
        $thumburl = Input::post( 'thumburl', '' )->sql();
        $sort_array = Input::post( 'sort', '' )->sql();
        $self_field_array = Input::post( 'self_field', '' )->sql();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }
        $imgurls = '';
        if ( !empty( $imgurl ) ) {
            if ( is_array( $imgurl ) ) {
                foreach ( $imgurl AS $k => $v ) {
                    $thumbtitle = $thumbdes[ $k ];
                    $thumburls = $thumburl[ $k ];
                    $sort = $sort_array[ $k ];
                    $self_field = $self_field_array[ $k ];

                    if ( empty( $thumbtitle ) && empty( $thumburls ) ) {
                        continue;
                    }
                    $imgurls.="{img key=\"$thumbtitle\" url=\"$thumburls\" sort=\"$sort\" self_field=\"$self_field\"}$v{/img}";
                }
            }
        }
        if ( empty( $imgurls ) ) {
            $this->redirect( '请上传广告位图片哟' );
        }

        // TODO goon to verify
        $entity_Poster_base = new entity_Poster_base();
        $entity_Poster_base->poster_name = $poster_name;
        $entity_Poster_base->uid = $this->memberInfo->uid;
        $entity_Poster_base->poster_imgurls = $imgurls;
        
        $model = new service_Poster_manage();
        $rs = $model->customPosterSave( $entity_Poster_base );
        if ( $rs ) {
            parent::headerRedirect( PHP_SELF . '?m=poster' );
        } else {
            $this->redirect( '修改自定义广告位失败' );
        }
    }

    /**
     * 批量操作
     */
    public function batch()
    {
        $act = Input::post( 'action', '' )->string();
        $aid = Input::post( 'id', 0 )->string();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();

        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            throw new ApiException( '请选择要操作的...' );
        }

        if ( $do == 'del' || $act == 'del' ) {
            $model = new service_Poster_manage();
            $model->setMall_uid( $this->memberInfo->uid );
            $rs = $model->deletePosterId( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->apiReturn();
            } else {
                throw new ApiException( '删除失败，请重试！' );
            }
        }
    }

}
