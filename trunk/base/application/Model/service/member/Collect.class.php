<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Collect.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_Collect_base extends service_Model_base
{

    protected $uid;
    protected $item_uid;
    protected $item_id;
    protected $pagesize;
    protected $errorMessage;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setItem_uid( $item_uid )
    {
        $this->item_uid = $item_uid;
    }

    function setItem_id( $item_id )
    {
        $this->item_id = $item_id;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 新建商品收藏
     * $this->uid;
     * $this->item_id;
     * $this->saveCollectItem();
     */
    public function saveCollectItem()
    {
        //判断item的合法性
        $goods_dao = dao_factory_base::getGoodsDao();
        $item_dao = dao_factory_base::getItemDao();
        $item_dao->setPk( $this->item_id );
        $item_dao->setField( 'goods_id,item_name,goods_image_id' );
        $item_info = $item_dao->getInfoByPk();
        if ( !$item_info ) {
            $this->errorMessage = '要收藏的商品不存在哟~';
            return false;
        }
        //判断本次组合有没有收藏过  有就更新 没有就插入        
        $collect_item_dao = dao_factory_base::getCollectItemDao();
        $collect_item_info = $this->getCollectItemInfo();

        $item_dao->getDb()->startTrans();

        $entity_CollectItem_base = new entity_CollectItem_base();
        if ( !$collect_item_info ) {
            $entity_CollectItem_base->uid = $this->uid;
            $entity_CollectItem_base->goods_id = $item_info->goods_id;
            $entity_CollectItem_base->item_id = $this->item_id;
            $entity_CollectItem_base->collect_time = $this->now;
            $entity_CollectItem_base->is_delete = 0;
            $res = $collect_item_dao->insert( $entity_CollectItem_base );
        } else {
            $entity_CollectItem_base->is_delete = 0;
            $collect_item_dao->setPk( $collect_item_info->collect_item_id );
            $res = $collect_item_dao->updateByPk( $entity_CollectItem_base );
        }

        if ( empty( $collect_item_info ) || $collect_item_info->is_delete = 1 ) {
            $entity_Item_base = new entity_Item_base();
            $entity_Item_base->collect_count = new TmacDbExpr( 'collect_count+1' );
            $item_dao->updateByPk( $entity_Item_base );

            $entity_Goods_base = new entity_Goods_base();
            $entity_Goods_base->collect_count = new TmacDbExpr( 'collect_count+1' );
            $goods_dao->updateByPk( $entity_Goods_base );
        }

        if ( $item_dao->getDb()->isSuccess() ) {
            $item_dao->getDb()->commit();
            return true;
        } else {
            $item_dao->getDb()->rollback();
            $this->errorMessage = '收藏失败';
            return false;
        }
    }

    /**
     * 新建商品店铺
     * $this->uid;
     * $this->item_uid;
     * $this->saveCollectShop();
     */
    public function saveCollectShop()
    {
        //判断item的合法性
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $member_setting_dao->setPk( $this->item_uid );
        $member_setting_dao->setField( 'uid,shop_name,shop_image_id' );
        $member_setting_info = $member_setting_dao->getInfoByPk();
        if ( !$member_setting_info ) {
            $this->errorMessage = '要收藏的店铺不存在哟~';
            return false;
        }
        //判断本次组合有没有收藏过  有就更新 没有就插入
        $collect_shop_dao = dao_factory_base::getCollectShopDao();
        $collect_shop_info = $this->getCollectShopInfo();

        $member_setting_dao->getDb()->startTrans();

        $entity_CollectShop_base = new entity_CollectShop_base();
        if ( !$collect_shop_info ) {
            $entity_CollectShop_base->uid = $this->uid;
            $entity_CollectShop_base->item_uid = $this->item_uid;
            $entity_CollectShop_base->shop_name = $member_setting_info->shop_name;
            $entity_CollectShop_base->shop_image_id = $member_setting_info->shop_image_id;
            $entity_CollectShop_base->collect_time = $this->now;
            $entity_CollectShop_base->is_delete = 0;
            $res = $collect_shop_dao->insert( $entity_CollectShop_base );
        } else {
            $entity_CollectShop_base->is_delete = 0;
            $collect_shop_dao->setPk( $collect_shop_info->collect_shop_id );
            $res = $collect_shop_dao->updateByPk( $entity_CollectShop_base );
        }

        if ( empty( $collect_shop_info ) || $collect_shop_info->is_delete == 1 ) {
            $entity_MemberSetting_base = new entity_MemberSetting_base();
            $entity_MemberSetting_base->collect_count = new TmacDbExpr( 'collect_count+1' );
            $member_setting_dao->updateByPk( $entity_MemberSetting_base );
        }

        if ( $member_setting_dao->getDb()->isSuccess() ) {
            $member_setting_dao->getDb()->commit();
            return true;
        } else {
            $member_setting_dao->getDb()->rollback();
            $this->errorMessage = '收藏失败';
            return false;
        }
    }

    private function getCollectItemInfo()
    {
        $collect_item_dao = dao_factory_base::getCollectItemDao();
        $collect_item_dao->setField( 'collect_item_id,item_id,is_delete' );
        $collect_item_dao->setWhere( "uid={$this->uid} AND item_id={$this->item_id}" );
        $collect_item_info = $collect_item_dao->getInfoByWhere();
        return $collect_item_info;
    }

    private function getCollectShopInfo()
    {
        $collect_shop_dao = dao_factory_base::getCollectShopDao();
        $collect_shop_dao->setField( 'collect_shop_id,item_uid,is_delete' );
        $collect_shop_dao->setWhere( "uid={$this->uid} AND item_uid={$this->item_uid}" );
        $collect_shop_info = $collect_shop_dao->getInfoByWhere();
        return $collect_shop_info;
    }

    /**
     * 删除商品收藏
     * $this->uid;
     * $this->item_id;
     * $this->deleteCollectItem();
     */
    public function deleteCollectItem()
    {
        //判断本次组合有没有收藏过  有就更新 没有就插入        
        $collect_item_dao = dao_factory_base::getCollectItemDao();
        $collect_item_info = $this->getCollectItemInfo();
        if ( !$collect_item_info ) {
            return true;
        }
        $collect_item_dao->setPk( $collect_item_info->collect_item_id );

        $item_dao = dao_factory_base::getItemDao();
        $item_dao->getDb()->startTrans();

        //更新商品收藏的删除状态
        $entity_CollectItem_base = new entity_CollectItem_base();
        $entity_CollectItem_base->is_delete = 1;
        $collect_item_dao->updateByPk( $entity_CollectItem_base );

        //更新商品的被收藏总数
        $entity_Item_base = new entity_Item_base();
        $entity_Item_base->collect_count = new TmacDbExpr( 'collect_count-1' );
        $item_dao->setPk( $collect_item_info->item_id );
        $item_dao->updateByPk( $entity_Item_base );

        if ( $item_dao->getDb()->isSuccess() ) {
            $item_dao->getDb()->commit();
            return true;
        } else {
            $item_dao->getDb()->rollback();
            $this->errorMessage = '删除收藏商品失败';
            return false;
        }
    }

    /**
     * 删除商品店铺
     * $this->uid;
     * $this->item_uid;
     * $this->deleteCollectShop();
     */
    public function deleteCollectShop()
    {
        //判断本次组合有没有收藏过  有就更新 没有就插入
        $collect_shop_dao = dao_factory_base::getCollectShopDao();
        $collect_shop_info = $this->getCollectShopInfo();
        if ( !$collect_shop_info ) {
            return true;
        }

        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $member_setting_dao->getDb()->startTrans();

        //更新店铺收藏的删除状态
        $entity_CollectShop_base = new entity_CollectShop_base();
        $entity_CollectShop_base->is_delete = 1;
        $collect_shop_dao->setPk( $collect_shop_info->collect_shop_id );
        $collect_shop_dao->updateByPk( $entity_CollectShop_base );

        //更新店铺的被收藏总数
        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->collect_count = new TmacDbExpr( 'collect_count-1' );
        $member_setting_dao->setPk( $collect_shop_info->item_uid );
        $member_setting_dao->updateByPk( $entity_MemberSetting_base );

        if ( $member_setting_dao->getDb()->isSuccess() ) {
            $member_setting_dao->getDb()->commit();
            return true;
        } else {
            $member_setting_dao->getDb()->rollback();
            $this->errorMessage = '删除收藏店铺失败';
            return false;
        }
    }

    /**
     * $this->uid;
     * $this->getCollectItemList();     
     */
    public function getCollectItemList()
    {
        $dao = dao_factory_base::getCollectItemDao();
        $dao->setWhere( "uid={$this->uid} AND is_delete=0" );

        $count = $dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();

        $res = array();
        if ( $count > 0 ) {
            $dao->setOrderby( 'collect_item_id DESC' );
            $dao->setLimit( $limit );
            $res = $dao->getCollectItemArray( $this->uid );
            foreach ( $res as $value ) {
                $value->collect_time = date( 'Y-m-d H:i:s', $value->collect_time );
                $value->goods_image_url = $this->getImage( $value->goods_image_id, '300', 'goods' );
                unset( $value->goods_image_id );
            }
        }

        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / $this->pagesize ) ),
            'pagesize' => $this->pagesize,
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'collect_item_list',
            'retmsg' => $retmsg,
            'reqdata' => $res,
        );
        return $return;
    }

    /**
     * $this->uid;
     * $this->getCollectShopList();     
     */
    public function getCollectShopList()
    {
        $dao = dao_factory_base::getCollectShopDao();
        $dao->setWhere( "uid={$this->uid} AND is_delete=0" );

        $count = $dao->getCountByWhere();

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setPrepage( $this->pagesize );
        $limit = $pages->getSqlLimit();
        $res = array();
        if ( $count > 0 ) {
            $dao->setOrderby( 'collect_shop_id DESC' );
            $dao->setLimit( $limit );
            $res = $dao->getListByWhere();
            foreach ( $res as $value ) {
                $value->collect_time = date( 'Y-m-d H:i:s', $value->collect_time );
                $value->shop_image_url = $this->getImage( $value->shop_image_id, '110', 'shop' );
                unset( $value->shop_image_id );
            }
        }

        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / $this->pagesize ) ),
            'pagesize' => $this->pagesize,
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'collect_shop_list',
            'retmsg' => $retmsg,
            'reqdata' => $res,
        );
        return $return;
    }

    /**
     * 检测用户商品是否收藏过
     * @param type $uid
     * @param type $item_id
     */
    public function checkCollectItemExist( $uid, $item_id )
    {
        $dao = dao_factory_base::getCollectItemDao();
        $dao->setField( 'collect_item_id' );
        $dao->setWhere( "uid={$uid} AND item_id={$item_id} AND is_delete=0" );
        $res = $dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

    /**
     * 检测用户店铺是否收藏过
     * @param type $uid
     * @param type $item_uid
     */
    public function checkCollectShopExist( $uid, $item_uid )
    {
        $dao = dao_factory_base::getCollectShopDao();
        $dao->setField( 'collect_shop_id' );
        $dao->setWhere( "uid={$uid} AND item_uid={$item_uid} AND is_delete=0" );
        $res = $dao->getInfoByWhere();
        if ( $res ) {
            return true;
        }
        return false;
    }

}
