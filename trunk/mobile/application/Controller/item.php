<?php

/**
 * mobile 购物车 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: item.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class itemAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $item_id = Input::get( 'id', 0 )->required( '商品不能为空' )->int();
        $agent_id = Input::get( 'agent', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        if ( !empty( $agent_id ) ) {
            $expire_time = 86400 * 7;
            setcookie( 'agent_id', $agent_id, $this->now + $expire_time, '/', $GLOBALS [ 'TmacConfig' ] [ 'Cookie' ] [ 'domain' ] );
        }
        $model = new service_Goods_mobile();
        $model->setItem_id( $item_id );

        $item_info = $model->getItemInfoById();

        if ( !$item_info ) {
            self::no( '商品不存在！' );
        } else if ( $item_info->is_delete == 1 ) {
            self::no( '商品已经删除' );        
        } else if ( $item_info->is_delete == 2 ) {
            self::no( '商品已经下架' );
        }
        $item_info->goods_image_id = $this->getImage( $item_info->goods_image_id, '640x0', 'goods' );
        $item_info->item_desc = preg_replace( '/(width|height)="(\d+)"/', '', $item_info->item_desc );
        $item_info->item_desc = str_replace( 'width: 750px;', '', $item_info->item_desc );

        $model->setGoods_id( $item_info->goods_id );
        $goods_spec_array = $model->getGoodsSpecArray();
        $goods_sku_array = $model->getGoodsSkuArray();

        $goods_image_array = $model->getGoodsImageArray( $item_info->goods_id );

        $shop_model = new service_Shop_base();
        $shop_model->setUid( $item_info->uid );
        $shopInfo = $shop_model->getShopInfo();

        if ( $shopInfo->member_type == service_Member_base::member_type_supplier ) {//供应商的商品
            $item_photo_show = false;
        } else {
            $item_photo_show = true;//显示首图
        }

        //商品规格
        $array[ 'goods_spec_array' ] = json_encode( $goods_spec_array[ 'result_object' ], true );
        //商品规格对应的sku 价格/库存
        $array[ 'goods_sku_array' ] = json_encode( $goods_sku_array, true );
        $array[ 'item_info' ] = $item_info;
        $array[ 'goods_image_array' ] = $goods_image_array;
        $array[ 'shop_info' ] = $shopInfo;
        $array[ 'item_photo_show' ] = $item_photo_show;
//		
//           echo '<pre>';
//           print_r( $array );
//         echo "</pre>";
//        die;

        $this->assign( $array );
        $this->V( 'item' );
    }


}
