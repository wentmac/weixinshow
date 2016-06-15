<?php

/**
 * 用户登录注册页面
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class shopAction extends service_Controller_manage
{

    //定义初始化变量

    public function _init()
    {
        $this->checkLogin();
    }

    /**
     * 修改店铺设置
     */
    public function modify()
    {
        $shop_name = Input::post( 'shop_name', '' )->string(); //微店名称        
        $weixin_id = Input::post( 'weixin_id', '' )->string(); //微信号
        $shop_intro = Input::post( 'shop_intro', '' )->string(); //微店公告
        $shop_template_id = Input::post( 'shop_template_id', '' )->string(); //微店封面
        $shop_address = Input::post( 'shop_address', '' )->string(); //微店实体店地址        
        $shop_image_id = Input::post( 'shop_image_id', '' )->imageId(); //微店头像        
        $shop_signboard_image_id = Input::post( 'shop_signboard_image_id', '' )->imageId(); //微店招牌        
        $goods_show_type = Input::post( 'goods_show_type', 0 )->int(); //商品展示方式        
        $payment_type = Input::post( 'payment_type', 1 )->int(); //支付类型（1：货到付款）        
        $refund_type = Input::post( 'refund_type', 0 )->int(); //退货状态（1：7天退货）        
        $is_guarantee_transaction = Input::post( 'is_guarantee_transaction', 0 )->int(); //担保交易（1：7天退货）        
        $stock_setting = Input::post( 'stock_setting', 0 )->int(); //库存设置状态（1：拍下减库存｜2：付款减库存）

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->shop_name = $shop_name;
        $entity_MemberSetting_base->weixin_id = $weixin_id;
        $entity_MemberSetting_base->shop_intro = $shop_intro;
        $entity_MemberSetting_base->shop_template_id = $shop_template_id;
        $entity_MemberSetting_base->shop_address = $shop_address;
        $entity_MemberSetting_base->shop_image_id = $shop_image_id;
        $entity_MemberSetting_base->shop_signboard_image_id = $shop_signboard_image_id;
        $entity_MemberSetting_base->goods_show_type = $goods_show_type;
        $entity_MemberSetting_base->payment_type = $payment_type;
        $entity_MemberSetting_base->refund_type = 1; //$refund_type;
        $entity_MemberSetting_base->is_guarantee_transaction = $is_guarantee_transaction;
        $entity_MemberSetting_base->stock_setting = $stock_setting;

        $shop_model = new service_Shop_base();
        $shop_model->setUid( $this->memberInfo->uid );
        $res = $shop_model->modifyShopInfo( $entity_MemberSetting_base );
        if ( $res ) {
            $this->apiReturn( array() );
        } else {
            throw new ApiException( '店铺设置失败' );
        }
    }

    /**
     * 取店铺详情
     */
    public function detail()
    {
        $model = new service_Shop_base();
        $model->setUid( $this->memberInfo->uid );
        $shopInfo = $model->getShopInfo();
        $array[ 'shop_info' ] = $shopInfo;
        $this->assign( $array );
//		echo '<pre>';
//	    print_r( $array );
        $this->V( 'shop_detail' );
    }

    /**
     * 取店铺的二维码
     */
    public function get_qrcode_logo()
    {
        $model = new service_Shop_base();
        $model->setUid( $this->memberInfo->uid );
        $shopInfo = $model->getShopInfo();
        $qrcode_model = new service_utils_QRCode_base();
        $qrcode_model->setUid( $this->memberInfo->uid );
        $qrcode_model->setShop_qrcode_status( true );
        $qrcode_model->setQrcode_title( $shopInfo->shop_name );
        $qrcode_model->setQrcode_description( '长按识别图中二维码或直接扫码进入~' );
        $qrcode_model->setUrl( MOBILE_URL );
        $qrcode_model->getShopQRCodeWithBackImage();
    }

}
