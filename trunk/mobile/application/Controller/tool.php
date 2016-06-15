<?php

/**
 * mobile 购物车 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: tool.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class toolAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取店铺的二维码
     */
    public function get_shop_qrcode()
    {
        $uid = Input::get( 'uid', 0 )->int();
        $qrcode_model = new service_utils_QRCode_base();
        $qrcode_model->setUid( $uid );
        $qrcode_model->setUrl( MOBILE_URL );
        $qrcode_model->getShopQRCode();
    }

    /**
     * 取收款的二维码
     */
    public function get_receivable_qrcode()
    {
        $receivable_id = Input::get( 'receivable_id', 0 )->required( '收款ID不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $qrcode_model = new service_utils_QRCode_base();
        $qrcode_model->setUrl( MOBILE_URL );
        $qrcode_model->getReceivableQRCode( $receivable_id );
    }

    /**
     * 取商品二维码
     */
    public function get_item_qrcode()
    {
        $id = Input::get( 'id', 0 )->required( '要操作的商品ID不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $qrcode_model = new service_utils_QRCode_base();
        $qrcode_model->setUrl( MOBILE_URL );
        $qrcode_model->getItemQRCode( $id );
    }

    /**
     * 统计下载次数
     */
    public function download_statistic()
    {
        $union = Input::get( 'union', '' )->string();
        $model = new service_DownloadStatistic_base();
        switch ( $union )
        {
            default:
                $model->setUnion_id( 0 );
                break;
            case 'zhihuitui':
                $model->setUnion_id( service_DownloadStatistic_base::union_zhihuitui );
                break;
            case 'bd':
                $model->setUnion_id( service_DownloadStatistic_base::union_bd );
        }
        $res = $model->createDownloadStatistic();
        $this->apiReturn( $res );
    }

}
