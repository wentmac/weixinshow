<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: factory.class.php 329 2016-06-01 11:18:04Z zhangwentao $
 */

/**
 * Description of DaoFactory
 *
 * @author Tracy McGrady
 */
final class dao_factory_base
{

    /**
     * 工厂类中数据库连接
     * @return type
     */
    public static function getDb()
    {
        $database = $GLOBALS[ 'TmacConfig' ][ 'Common' ][ 'Database' ];
//        $database = 'hotel_9tour_cn';
        return DatabaseDriver::getInstance( $database );
    }

    /**
     * 工厂类中数据库连接
     * @return type
     */
    public static function getLineSearchNoteDb()
    {
//        $database = $GLOBALS['TmacConfig']['Common']['Database'];
        $database = 'line_search_note';
        return DatabaseDriver::getInstance( $database );
    }

    /**
     * dao_factory_base::createDao('article',$this->db);
     * @param type $name
     * @param type $link_identifier
     * @throws TmacException
     */
    public static function createDao( $name )
    {
        $className = $className = 'dao_impl_' . $name . '_' . APP_BASE_NAME; //java版    Class clazz = Class.forName(className);            
        return new $className( self::getDb() );
    }

    public static function getArticleDao()
    {
        return new dao_impl_Article_base( self::getDb() );
    }

    public static function getAddArticleDao()
    {
        return new dao_impl_AddArticle_base( self::getDb() );
    }

    public static function getCategoryDao()
    {
        return new dao_impl_Category_base( self::getDb() );
    }

    public static function getUserDao()
    {
        return new dao_impl_User_base( self::getDb() );
    }

    public static function getUserLogDao()
    {
        return new dao_impl_UserLog_base( self::getDb() );
    }

    public static function getUserTypeDao()
    {
        return new dao_impl_UserType_base( self::getDb() );
    }

    public static function getSysconfigDao()
    {
        return new dao_impl_Sysconfig_base( self::getDb() );
    }

    public static function getMemberDao()
    {
        return new dao_impl_Member_base( self::getDb() );
    }

    public static function getMemberSettingDao()
    {
        return new dao_impl_MemberSetting_base( self::getDb() );
    }

    public static function getMemberBillDao()
    {
        return new dao_impl_MemberBill_base( self::getDb() );
    }

    public static function getMemberAddressDao()
    {
        return new dao_impl_MemberAddress_base( self::getDb() );
    }

    public static function getMemberOauthDao()
    {
        return new dao_impl_MemberOauth_base( self::getDb() );
    }

    public static function getSmsLogDao()
    {
        return new dao_impl_SmsLog_base( self::getDb() );
    }

    public static function getSpecDao()
    {
        return new dao_impl_Spec_base( self::getDb() );
    }

    public static function getSpecMapDao()
    {
        return new dao_impl_SpecMap_base( self::getDb() );
    }

    public static function getSpecValueDao()
    {
        return new dao_impl_SpecValue_base( self::getDb() );
    }

    public static function getSpecValueMapDao()
    {
        return new dao_impl_SpecValueMap_base( self::getDb() );
    }

    public static function getGoodsSpecDao()
    {
        return new dao_impl_GoodsSpec_base( self::getDb() );
    }

    public static function getGoodsSkuDao()
    {
        return new dao_impl_GoodsSku_base( self::getDb() );
    }

    public static function getGoodsImageDao()
    {
        return new dao_impl_GoodsImage_base( self::getDb() );
    }

    public static function getGoodsCommentDao()
    {
        return new dao_impl_GoodsComment_base( self::getDb() );
    }

    public static function getGoodsCategoryDao()
    {
        return new dao_impl_GoodsCategory_base( self::getDb() );
    }

    public static function getGoodsCategoryMapDao()
    {
        return new dao_impl_GoodsCategoryMap_base( self::getDb() );
    }

    public static function getGoodsPriceDao()
    {
        return new dao_impl_GoodsPrice_base( self::getDb() );
    }

    public static function getItemCategoryDao()
    {
        return new dao_impl_ItemCategory_base( self::getDb() );
    }

    public static function getItemCategoryMapDao()
    {
        return new dao_impl_ItemCategoryMap_base( self::getDb() );
    }

    public static function getGoodsAttributeDao()
    {
        return new dao_impl_GoodsAttribute_base( self::getDb() );
    }

    public static function getGoodsDao()
    {
        return new dao_impl_Goods_base( self::getDb() );
    }

    public static function getItemDao()
    {
        return new dao_impl_Item_base( self::getDb() );
    }

    public static function getRegionDao()
    {
        return new dao_impl_Region_base( self::getDb() );
    }

    public static function getCartDao()
    {
        return new dao_impl_Cart_base( self::getDb() );
    }

    public static function getOrderInfoDao()
    {
        return new dao_impl_OrderInfo_base( self::getDb() );
    }

    public static function getOrderGoodsDao()
    {
        return new dao_impl_OrderGoods_base( self::getDb() );
    }

    public static function getOrderActionDao()
    {
        return new dao_impl_OrderAction_base( self::getDb() );
    }

    public static function getOrderServiceDao()
    {
        return new dao_impl_OrderService_base( self::getDb() );
    }

    public static function getOrderRefundDao()
    {
        return new dao_impl_OrderRefund_base( self::getDb() );
    }

    public static function getPayLogDao()
    {
        return new dao_impl_PayLog_base( self::getDb() );
    }

    public static function getCollectShopDao()
    {
        return new dao_impl_CollectShop_base( self::getDb() );
    }

    public static function getCollectItemDao()
    {
        return new dao_impl_CollectItem_base( self::getDb() );
    }

    public static function getOauthDao()
    {
        return new dao_impl_Oauth_base( self::getDb() );
    }

    public static function getCustomerDao()
    {
        return new dao_impl_Customer_base( self::getDb() );
    }

    public static function getExpressDao()
    {
        return new dao_impl_Express_base( self::getDb() );
    }

    public static function getNoticeDao()
    {
        return new dao_impl_Notice_base( self::getDb() );
    }

    public static function getSettleDao()
    {
        return new dao_impl_Settle_base( self::getDb() );
    }

    public static function getDownloadStatisticDao()
    {
        return new dao_impl_DownloadStatistic_base( self::getDb() );
    }

    public static function getHelpCategoryDao()
    {
        return new dao_impl_HelpCategory_base( self::getDb() );
    }

    public static function getHelpArticleDao()
    {
        return new dao_impl_HelpArticle_base( self::getDb() );
    }

    public static function getPosterDao()
    {
        return new dao_impl_Poster_base( self::getDb() );
    }

    public static function getBrandDao()
    {
        return new dao_impl_Brand_base( self::getDb() );
    }

    public static function getCouponDao()
    {
        return new dao_impl_Coupon_base( self::getDb() );
    }

    public static function getMemberTempDao()
    {
        return new dao_impl_MemberTemp_base( self::getDb() );
    }

}
