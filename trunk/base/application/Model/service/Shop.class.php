<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Shop_base extends service_Model_base
{

    const fixed_pagesize = 10;
    protected $uid;
    protected $errorMessage;
    protected $item_cat_id;
    protected $item_name;
    protected $recommend;
    protected $pagesize;
    protected $image_size = '110';
    protected $signboard_image_size = '640x330';
    protected $member_level;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setItem_cat_id( $item_cat_id )
    {
        $this->item_cat_id = $item_cat_id;
    }

    function setItem_name( $item_name )
    {
        $this->item_name = $item_name;
    }

    function setRecommend( $recommend )
    {
        $this->recommend = $recommend;
    }

    function setPagesize( $pagesize )
    {
        $this->pagesize = $pagesize;
    }

    function setImage_size( $image_size )
    {
        $this->image_size = $image_size;
    }

    function setSignboard_image_size( $signboard_image_size )
    {
        $this->signboard_image_size = $signboard_image_size;
    }

    function setMember_level( $member_level )
    {
        $this->member_level = $member_level;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 更新店铺设置
     * $this->uid;
     * $this->modifyShopInfo($entity_MemberSetting_base);
     * @param entity_MemberSetting_base $entity_MemberSetting_base
     * @return type
     */
    public function modifyShopInfo( entity_MemberSetting_base $entity_MemberSetting_base )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->uid );
        return $dao->updateByPk( $entity_MemberSetting_base );
    }

    /**
     * 取商品设置默认数据
     * @return type
     */
    public function getShopInfo()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->uid );
        $dao->setField( 'member_type,shop_name,shop_intro,shop_image_id,shop_signboard_image_id,shop_template_id,shop_address,stock_setting,weixin_id,goods_show_type,payment_type,refund_type,is_guarantee_transaction,collect_count,collect_count_variable' );
        $shop_info = $dao->getInfoByPk();
        if ( $shop_info ) {

            $member_level = Tmac::config( 'member.member.level', APP_BASE_NAME );

            $member_dao = dao_factory_base::getMemberDao();
            $member_dao->setPk( $this->uid );
            $member_dao->setField( 'mobile,member_type,member_class' );
            $member_info = $member_dao->getInfoByPk();

            $shop_info->shop_level = isset( $member_level[ $member_info->member_type ][ $member_info->member_class ] ) ? $member_level[ $member_info->member_type ][ $member_info->member_class ] : '';
            if ( empty( $shop_info->shop_image_id ) ) {
                $shop_info->shop_image_url = STATIC_URL . APP_MOBILE_NAME . '/default/image/vshop-shop-logo-default.jpg?v=1';
            } else {
                $shop_info->shop_image_url = $this->getImage( $shop_info->shop_image_id, $this->image_size, 'shop' );
            }
            if ( empty( $shop_info->shop_signboard_image_id ) ) {
                $shop_info->shop_signboard_image_url = STATIC_URL . APP_MOBILE_NAME . '/default/image/shop_signboard_image.png';
            } else {
                $shop_info->shop_signboard_image_url = $this->getImage( $shop_info->shop_signboard_image_id, $this->signboard_image_size, 'shop' );
            }
            $shop_info->url = MOBILE_URL . 'shop/' . $this->uid;
            $shop_info->mobile = $member_info->mobile;
            $shop_info->collect_count = $shop_info->collect_count + $shop_info->collect_count_variable;
        }

        return $shop_info;
    }

    /**
     * 取商品设置默认数据
     * @return type
     */
    public function getShopMoney()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->uid );
        $dao->setField( 'current_money,history_money' );
        $shop_info = $dao->getInfoByPk();
        return $shop_info;
    }

    /**
     * 取商品设置默认数据
     * @return type
     */
    public function getShopHome()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->uid );
        $dao->setField( 'shop_name,shop_image_id,history_money' );
        $shop_info = $dao->getInfoByPk();
        if ( $shop_info ) {
            if ( empty( $shop_info->shop_image_id ) ) {
                $shop_info->shop_image_url = STATIC_URL . APP_MOBILE_NAME . '/default/v1/images/vshop-shop-logo-default.jpg?v=1';
            } else {
                $shop_info->shop_image_url = $this->getImage( $shop_info->shop_image_id, $this->image_size, 'shop' );
            }
        }
        return $shop_info;
    }

    /**
     * 取店铺的所有分类
     * @return type
     */
    public function getCategoryArray()
    {
        /**
          $dao = dao_factory_base::getItemCategoryDao();
          $dao->setField( 'item_cat_id,cat_name,item_count' );
          $dao->setWhere( "uid={$this->uid} AND is_delete=0" );
          $res = $dao->getListByWhere();
         */
        $dao = dao_factory_base::getGoodsCategoryDao();
        $dao->setField( 'goods_cat_id,cat_name,goods_count,cat_pid' );
        $dao->setWhere( "is_cloud_product=1 AND is_delete=0" );
        $dao->setOrderby( 'cat_sort DESC' );
        $res = $dao->getListByWhere();
        $result = $category = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $category[ $value->cat_pid ][] = $value;
            }
            foreach ( $res as $value ) {
                $value->cat_son = isset( $category[ $value->goods_cat_id ] ) ? $category[ $value->goods_cat_id ] : array();
                if ( $value->cat_pid == 0 ) {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * 取所有店铺的所有商品
     * $this->uid;
     * $this->item_cat_id;
     * $this->item_name;
     * $this->pagesize;
     * $this->getItmeArray();
     */
    public function getItemArray()
    {
        $dao = dao_factory_base::getItemDao();
        $where = 'uid=' . $this->uid;
        if ( !empty( $this->item_cat_id ) ) {
            $where = $dao->getItemListWhereByCid( $where, $this->item_cat_id );
        }
        if ( !empty( $this->item_name ) ) {
            $where.=" AND item_name like '%{$this->item_name}%'";
        }
        /**
          if ( !empty( $this->recommend ) ) {
          $where.=" AND recommend=1";
          } */
        $where .= " AND is_delete=0";

        $dao->setWhere( $where );
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
            $dao->setOrderby( 'item_sort DESC,item_id DESC' );
            $dao->setLimit( $limit );
            $dao->setField( 'item_id,item_name,item_price,goods_image_id,sales_volume,recommend,goods_id,goods_type' );
            $res = $dao->getListByWhere();

            $goods_model = new service_Goods_base();
            foreach ( $res as $value ) {
                $price = $goods_model->getGoodsPromotePrice( $value->item_price, $value->goods_type, $this->member_level );
                $value->item_price = $price[ 'price' ];
                $value->price_source = $price[ 'price_source' ];
                $value->goods_image_url = $this->getImage( $value->goods_image_id, '300', 'goods' );
            }
        }
        $retHeader = array(
            'totalput' => $count,
            'totalpg' => intval( ceil( $count / self::fixed_pagesize ) ),
            'pagesize' => $this->pagesize,
            'page' => $pages->getNowPage()
        );
        $return = array(
            'retHeader' => $retHeader,
            'retcode' => 'shop_item_list',
            'retmsg' => $retmsg,
            'reqdata' => $res,
        );
        return $return;
    }

    public function getItemCategoryInfoById( $id )
    {
        $dao = dao_factory_base::getItemCategoryDao();
        $dao->setPk( $id );
        return $dao->getInfoByPk();
    }

}
