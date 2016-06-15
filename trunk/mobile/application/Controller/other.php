<?php

/**
 * mobile 购物车 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: other.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class itemAction extends service_Controller_mobile
{

    public function __construct()
    {
        
    }

    public function index()
    {
        $item_id = Input::get( 'id', 0 )->required( '商品项目不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_Goods_mobile();
        $model->setItem_id( $item_id );

        $item_info = $model->getItemInfoById();

        if ( !$item_info ) {
            $this->redirect( '商品不存在！' );
        }
        $item_info->goods_image_id = $this->getImage($item_info->goods_image_id, '640x0', 'goods');

        $model->setGoods_id( $item_info->goods_id );
        $goods_spec_array = $model->getGoodsSpecArray();
        $goods_sku_array = $model->getGoodsSkuArray();
        
        $goods_image_array = $model->getGoodsImageArray($item_info->goods_id);

        //商品规格
        $array[ 'goods_spec_array' ] = json_encode( $goods_spec_array[ 'result_object' ], true );
        //商品规格对应的sku 价格/库存
        $array[ 'goods_sku_array' ] = json_encode( $goods_sku_array, true );
        $array[ 'item_info' ] = $item_info;
        $array[ 'goods_image_array' ] = $goods_image_array;


        //echo '<pre>';
        //print_r( $array );
        //echo "</pre>";
        //die;

        $this->assign( $array );
        $this->V( 'item' );

    }

}
