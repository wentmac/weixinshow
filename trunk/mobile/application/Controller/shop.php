<?php

/**
 * mobile 购物车 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: shop.php 352 2016-06-07 16:52:27Z zhangwentao $
 * http://www.t-mac.org；
 */
class shopAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 店铺首页
     */
    public function index()
    {

        $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
        $page = Input::get( 'p', 1 )->int();
        $y = Input::get( 'y', 0 )->float();

        if ( Filter::getStatus() === false ) {
            die( Filter::getFailMessage() );
            //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_Shop_mobile();
        $model->setUid( $shop_id );
        $shopInfo = $model->getShopInfo();

        //        $this->apiReturn( $shopInfo );
        //        exit;
        //调用首页轮播广告
        $poster_model = new service_Poster_base();
        $poster_model->setMall_uid( service_Member_base::yph_uid );
        $shop_index_banner = $poster_model->getPosterDetail( 'shop_index_banner', '640x330' );
        $shop_index_button = $poster_model->getPosterDetail( 'shop_index_button', '40' );
        $shop_index_image = $poster_model->getPosterDetail( 'shop_index_image', '320x110' );
        $shop_index_category = $poster_model->getPosterDetail( 'shop_index_category', '106x68' );

        $shopInfo->shop_id = $shop_id;
        $array[ 'shop_info' ] = $shopInfo;
        $array[ 'shop_info_json' ] = json_encode( $shopInfo, true );
        $array[ 'pagesize' ] = service_Shop_mobile::fixed_pagesize * $page;
        $array[ 'p' ] = $page;
        $array[ 'y' ] = $y;
        $array[ 'shop_index_banner' ] = $shop_index_banner;
        $array[ 'shop_index_button' ] = $shop_index_button;
        $array[ 'shop_index_image' ] = $shop_index_image;
        $array[ 'shop_index_category' ] = $shop_index_category;

        $this->assign( $array );
//		         echo '<pre>';
//		         print_r($array);
//		         echo '</pre>';
//                 die;
        $this->V( 'shop_index' );
    }

    /**
     * 店铺首页
     */
    public function index_v1()
    {

        $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
        $page = Input::get( 'p', 1 )->int();
        $y = Input::get( 'y', 0 )->float();

        if ( Filter::getStatus() === false ) {
            die( Filter::getFailMessage() );
            //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_Shop_mobile();
        $model->setUid( $shop_id );
        $shopInfo = $model->getShopInfo();

        //        $this->apiReturn( $shopInfo );
        //        exit;
        //调用首页轮播广告
        $poster_model = new service_Poster_base();
        $poster_model->setMall_uid( service_Member_base::yph_uid );
        $shop_index_banner = $poster_model->getPosterDetail( 'shop_index_banner', '640x330' );
        $shop_index_button = $poster_model->getPosterDetail( 'shop_index_button', '40' );
        $shop_index_image = $poster_model->getPosterDetail( 'shop_index_image', '320x110' );
        $shop_index_category = $poster_model->getPosterDetail( 'shop_index_category', '106x68' );

        $shopInfo->shop_id = $shop_id;
        $array[ 'shop_info' ] = $shopInfo;
        $array[ 'shop_info_json' ] = json_encode( $shopInfo, true );
        $array[ 'pagesize' ] = service_Shop_mobile::fixed_pagesize * $page;
        $array[ 'p' ] = $page;
        $array[ 'y' ] = $y;
        $array[ 'shop_index_banner' ] = $shop_index_banner;
        $array[ 'shop_index_button' ] = $shop_index_button;
        $array[ 'shop_index_image' ] = $shop_index_image;
        $array[ 'shop_index_category' ] = $shop_index_category;

        $this->assign( $array );
//		         echo '<pre>';
//		         print_r($array);
//		         echo '</pre>';
//                 die;
        $this->V( 'shop_index_v1' );
    }

    public function get_category_list()
    {
        /**
          $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
          if ( Filter::getStatus() === false ) {
          throw new ApiException( Filter::getFailMessage() );
          }
         * 
         */
        $model = new service_Shop_mobile();
        //$model->setUid( $shop_id );
        $res = $model->getCategoryArray();
        $this->apiReturn( $res );
    }

    public function get_item_list()
    {
        $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
        $item_cat_id = Input::get( 'item_cat_id', 0 )->int();
        $item_name = Input::get( 'query', 0 )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $recommend = Input::get( 'recommend', 0 )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $login_status = $this->checkLoginStatus();
        if ( $login_status == true ) {
            $member_level = $this->memberInfo->member_level;
        } else {
            $member_level = 0;
        }

        $model = new service_Shop_mobile();
        $model->setUid( $shop_id );
        $model->setItem_cat_id( $item_cat_id );
        $model->setItem_name( $item_name );
        $model->setPagesize( $pagesize );
        $model->setRecommend( $recommend );
        $model->setMember_level( $member_level );
        $res = $model->getItemArray();
        $this->apiReturn( $res );
    }

    /**
     * 店铺商品列表页
     */
    public function goodslist()
    {
        $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
        $goods_cat_id = Input::get( 'goods_cat_id', 0 )->int();
        $query = Input::get( 'query', '' )->string();
        $page = Input::get( 'p', 1 )->int();
        $y = Input::get( 'y', 0 )->float();

        if ( Filter::getStatus() === false ) {
            die( Filter::getFailMessage() );
            //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }
        $model = new service_Shop_mobile();
        $model->setUid( $shop_id );
        $shopInfo = $model->getShopInfo();
        $shopInfo->shop_id = $shop_id;

        if ( !empty( $goods_cat_id ) ) {
            $goods_category_model = new service_GoodsCategory_mobile();
            $goods_category_info = $goods_category_model->getCategoryInfo( $goods_cat_id );
        } else {
            $goods_category_info = new entity_GoodsCategory_base();
            $goods_category_info->cat_name = $query;
            $goods_category_info->goods_count = 0;
        }

        $array[ 'shop_info' ] = $shopInfo;
        $array[ 'shop_info_json' ] = json_encode( $shopInfo, true );
        $array[ 'goods_category_info' ] = $goods_category_info;
        $array[ 'query' ] = $query;
        $array[ 'pagesize' ] = service_Shop_mobile::fixed_pagesize * $page;
        $array[ 'p' ] = $page;
        $array[ 'y' ] = $y;

        $this->assign( $array );
        $this->V( 'shop_goodslist' );
    }

    public function get_goods_list()
    {
        $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
        $goods_cat_id = Input::get( 'goods_cat_id', 0 )->int();
        $query = Input::get( 'query', 0 )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $login_status = $this->checkLoginStatus();
        if ( $login_status == true ) {
            $member_level = $this->memberInfo->member_level;
        } else {
            $member_level = 0;
        }

        $model = new service_Goods_mobile();
        $model->setUid( $shop_id );
        $model->setGoods_cat_id( $goods_cat_id );
        $model->setQuery( $query );
        $model->setPagesize( $pagesize );
        $model->setMember_level( $member_level );
        $res = $model->getGoodsList();
        $this->apiReturn( $res );
    }

    /**
     * 店铺商品列表页
     */
    public function itemlist()
    {
        $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
        $item_cat_id = Input::get( 'item_cat_id', 0 )->required( '分类ID不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            die( Filter::getFailMessage() );
            //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }
        $model = new service_Shop_mobile();
        $model->setUid( $shop_id );
        $shopInfo = $model->getShopInfo();
        $shopInfo->shop_id = $shop_id;

        $array[ 'shop_info' ] = $shopInfo;
        $array[ 'shop_info_json' ] = json_encode( $shopInfo, true );
        $array[ 'item_category_info' ] = $model->getItemCategoryInfoById( $item_cat_id );

        $this->assign( $array );
        $this->V( 'shop_itemlist' );
    }

    /**
     * 商品搜索页
     */
    public function search()
    {

        $shop_id = Input::get( 'id', 0 )->required( '店铺不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            die( Filter::getFailMessage() );
            //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_Shop_mobile();
        $model->setUid( $shop_id );
        $shopInfo = $model->getShopInfo();

        //        $this->apiReturn( $shopInfo );
        //        exit;

        $shopInfo->shop_id = $shop_id;
        $array[ 'shop_info' ] = $shopInfo;
        $array[ 'shop_info_json' ] = json_encode( $shopInfo, true );
//		echo '<pre>';
//		print_r($array);
//		echo "</pre>";
        $this->assign( $array );
        $this->V( 'shop_search' );
    }

}
