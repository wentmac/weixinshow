<?php

class brandAction extends service_Controller_admin
{

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );

        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer' );
    }

    public function index()
    {

        $search_keyword = Input::get( 'search_keyword', '' )->string();
        $goods_cat_id = Input::get( 'goods_cat_id', 0 )->int();

        $entity_parameter_brand_base = new entity_parameter_Brand_base();
        $entity_parameter_brand_base->setQuery( $search_keyword );
        $entity_parameter_brand_base->setPagesize( 20 );
        $entity_parameter_brand_base->goods_cat_id = $goods_cat_id;
        $model = new service_goods_Brand_admin();
        $rs = $model->getBrandList( $entity_parameter_brand_base );

        $list_do_ary = Tmac::config( 'article.do' );
        $list_do_ary_option = Utility::Option( $list_do_ary );
        $array[ 'search_keyword' ] = $search_keyword;
        $array[ 'list_do_ary_option' ] = $list_do_ary_option;

        $this->assign( $array );
        $this->assign( $rs );
        $this->V( 'goods/brand' );
    }

    public function add()
    {
        $brand_id = Input::get( 'bid', 0 )->int();
        $link_image = '';
        $entity_brand_base = new entity_Brand_base();
        $model = new service_goods_Brand_admin();
        $model instanceof service_Brand_admin;
        if ( $brand_id > 0 ) {
            $entity_brand_base = $model->getBrandInfo( $brand_id );
            $link_image = $this->getImage( $entity_brand_base->brand_logo, 'brand', '200x150' );
        }
        $entity_brand_base->photo_url = $link_image;
        $is_delete_array = Tmac::config( 'brand.is_delete' );
        $is_delete_radio = Utility::RadioButton( $is_delete_array, 'is_delete', $entity_brand_base->is_delete );
        $recommend_index_array = Tmac::config( 'brand.recommend_index' );
        $recommend_index_option = Utility::Option( $recommend_index_array, 'recommend_index', $entity_brand_base->recommend_index );
        $recommend_category_array = Tmac::config( 'brand.recommend_category' );
        $recommend_category_option = Utility::Option( $recommend_category_array, 'recommend_category', $entity_brand_base->recommend_category );

        $goods_category_array = $model->getTopLevelGoodsCategoryArray();
        $goods_category_option = Utility::OptionObject( $goods_category_array, $entity_brand_base->goods_cat_id, 'goods_cat_id,cat_name' );

        $this->assign( 'is_delete_radio', $is_delete_radio );
        $this->assign( 'goods_category_option', $goods_category_option );
        $this->assign( 'recommend_index_option', $recommend_index_option );
        $this->assign( 'recommend_category_option', $recommend_category_option );
        $this->assign( 'editinfo', $entity_brand_base );
        $this->V( 'goods/brand' );
    }

    public function save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 1 ) {
            $this->redirect( '插入数据失败' );
            exit;
        }
        //初始化变量
        $brand_id = Input::post( 'brand_id', 0 )->int();
        $brand_name = Input::post( 'brand_name', '' )->required( '品牌名称不能为空!' )->string();
        $brand_logo = Input::post( 'brand_logo', '' )->string();
        $brand_desc = Input::post( 'brand_desc', '' )->string();
        $site_url = Input::post( 'site_url', '' )->string();
        $sort_order = Input::post( 'sort_order', 1 )->int();
        $goods_cat_id = Input::post( 'goods_cat_id', 0 )->int();
        $is_delete = Input::post( 'is_delete', 0 )->int();
        $recommend_index = Input::post( 'recommend_index', 0 )->int();
        $recommend_category = Input::post( 'recommend_category', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $entity_brand_base = new entity_Brand_base();
        $entity_brand_base->brand_id = $brand_id;
        $entity_brand_base->brand_name = $brand_name;
        $entity_brand_base->brand_logo = $brand_logo;
        $entity_brand_base->brand_desc = $brand_desc;
        $entity_brand_base->site_url = $site_url;
        $entity_brand_base->sort_order = $sort_order;
        $entity_brand_base->goods_cat_id = $goods_cat_id;
        $entity_brand_base->recommend_category = $recommend_category;
        $entity_brand_base->recommend_index = $recommend_index;
        $entity_brand_base->is_delete = $is_delete;

        $url = PHP_SELF . '?m=goods/' . $_GET[ 'TMAC_CONTROLLER' ];

        $model = new service_goods_Brand_admin();
        if ( $brand_id > 0 ) {
            //update data
            $entity_brand_base->brand_id = $brand_id;
            $rs = $model->modifyBrand( $entity_brand_base );
            if ( $rs ) {
                $this->redirect( '修改品牌成功!', $url );
            } else {
                $this->redirect( '修改品牌失败！' );
            }
        } else {
            //insert data
            $brand_id = $model->createBrand( $entity_brand_base );
            if ( $brand_id ) {
                $this->redirect( '插入品牌成功！', $url );
            } else {
                $this->redirect( '插入品牌失败！,请联系技术支持检查原因！' );
            }
        }
    }

    public function operate()
    {
        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'bid', 0 )->int();

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
            $model = new service_goods_Brand_admin();
            $rs = $model->deleteByBrandId( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->redirect( '删除成功', INDEX_URL . PHP_SELF . '?m=goods/brand' );
            } else {
                die;
                $this->redirect( '删除失败，请重试！' );
            }
        }
    }

}
