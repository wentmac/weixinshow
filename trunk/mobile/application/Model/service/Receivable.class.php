<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Receivable.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Receivable_mobile extends service_Receivable_base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 取商品设置默认数据
     * @return type
     */
    public function getShopLogo( $uid )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $uid );
        $dao->setField( 'shop_image_id' );
        $shop_info = $dao->getInfoByPk();
        $shop_image_url = STATIC_URL . APP_MOBILE_NAME . '/default/v1/images/vshop-shop-logo-default.jpg?v=1';
        if ( $shop_info && !empty( $shop_info->shop_image_id ) ) {
            $shop_image_url = $this->getImage( $shop_info->shop_image_id, '200', 'shop' );
        }
        return $shop_image_url;
    }

}
