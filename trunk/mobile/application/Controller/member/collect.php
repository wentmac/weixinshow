<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: collect.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class collectAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 会员中心
     * 用户全部售后退款
     * 表：wsw_order_refund<订单商品维权售后申请表>数据结构说明 [保密资料]
     */
    public function index()
    {
        $type = Input::get( 'type', 'goods' )->string();

        $this->assign( 'type', $type );
        $this->V( 'member/collect' );
    }

    public function item()
    {
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $model = new service_member_Collect_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setPagesize( $pagesize );
        $res = $model->getCollectItemList();
        $this->apiReturn( $res );
        exit;
    }

    /**
     * 收藏商品
     * @throws ApiException
     */
    public function item_save()
    {
        $item_id = Input::post( 'item_id', 0 )->required( '要收藏的商品ID不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $collect_model = new service_member_Collect_mobile();
        $collect_model->setUid( $this->memberInfo->uid );
        $collect_model->setItem_id( $item_id );
        $res = $collect_model->saveCollectItem();
        if ( $res == false ) {
            throw new ApiException( $collect_model->getErrorMessage() );
        }
        $this->apiReturn( $res );
    }

    /**
     * 删除收藏商品
     * @throws ApiException
     */
    public function item_delete()
    {
        $item_id = Input::post( 'item_id', 0 )->required( '要删除的收藏的商品ID不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $collect_model = new service_member_Collect_mobile();
        $collect_model->setUid( $this->memberInfo->uid );
        $collect_model->setItem_id( $item_id );
        $res = $collect_model->deleteCollectItem();
        if ( $res == false ) {
            throw new ApiException( $collect_model->getErrorMessage() );
        }
        $this->apiReturn( $res );
    }

    /**
     * get $page
     * get $pagesize
     */
    public function shop()
    {
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $model = new service_member_Collect_mobile();
        $model->setUid( $this->memberInfo->uid );
        $model->setPagesize( $pagesize );
        $res = $model->getCollectShopList();


        $this->apiReturn( $res );
        exit;
    }

    /**
     * 收藏店铺
     * @throws ApiException
     */
    public function shop_save()
    {
        $item_uid = Input::post( 'item_uid', 0 )->required( '要收藏的店铺不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $collect_model = new service_member_Collect_mobile();
        $collect_model->setUid( $this->memberInfo->uid );
        $collect_model->setItem_uid( $item_uid );
        $res = $collect_model->saveCollectShop();
        if ( $res == false ) {
            throw new ApiException( $collect_model->getErrorMessage() );
        }
        $this->apiReturn( $res );
    }

    /**
     * 删除商品收藏
     * @throws ApiException
     */
    public function shop_delete()
    {
        $item_uid = Input::post( 'item_uid', 0 )->required( '要删除收藏的店铺不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $collect_model = new service_member_Collect_mobile();
        $collect_model->setUid( $this->memberInfo->uid );
        $collect_model->setItem_uid( $item_uid );
        $res = $collect_model->deleteCollectShop();
        if ( $res == false ) {
            throw new ApiException( $collect_model->getErrorMessage() );
        }
        $this->apiReturn( $res );
    }

    /**
     * 检测商品收藏状态
     */
    public function check_item_collect()
    {
        $item_id = Input::get( 'item_id', 0 )->required( '要检测的商品ID不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_member_Collect_mobile();
        $check_res = $model->checkCollectItemExist( $this->memberInfo->uid, $item_id );
        $this->apiReturn( $check_res );
    }

    /**
     * 检测店铺收藏状态
     */
    public function check_shop_collect()
    {
        $item_uid = Input::get( 'item_uid', 0 )->required( '要检测的店铺不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_member_Collect_mobile();
        $check_res = $model->checkCollectShopExist( $this->memberInfo->uid, $item_uid );
        $this->apiReturn( $check_res );
    }

}
