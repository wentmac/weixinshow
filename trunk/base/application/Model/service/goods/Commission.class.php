<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Commission_base extends service_Model_base
{

    protected $uid;
    protected $member_mall_commission;
    protected $errorMessage;
    protected $goods_category_model;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 取出计算后的佣金
     * $this->setUid($uid);     
     * $goodsArray中必须有goods_id,goods_price|price,commission_fee|goods_cat_id
     * $this->getHandleGoodsPrice($goodsArray);     
     */
    public function getHandleCommission( $goodsArray )
    {
        if ( empty( $goodsArray ) ) {
            return $goodsArray;
        }
        //判断是否有特殊佣金设置
        $member_mall_commission = $this->member_mall_commission = $this->getMemberMallCommissionInfo();
        if ( $member_mall_commission == FALSE ) {
            return $goodsArray;
        }
        if ( $member_mall_commission->commission_type == service_MemberMall_base::commission_type_none ) {
            return $goodsArray;
        }

        //因为要验证商品是否海淘商品
        //if ( $this->member_mall_commission->commission_type == service_MemberMall_base::commission_type_category ) {
        $this->goods_category_model = $model = new service_GoodsCategory_base();
        $this->goods_category_model->setIs_cloud_product( service_GoodsCategory_base::is_cloud_product_yes );
        //}
        if ( is_array( $goodsArray ) ) {
            foreach ( $goodsArray as $goodsInfo ) {
                $this->getGoodsCommission( $goodsInfo );
            }
        } else {
            $goodsArray = $this->getGoodsCommission( $goodsArray );
        }
        return $goodsArray;
    }

    /**
     * 取一条商品的特殊价
     * @param type $goodsInfo
     * @return type
     */
    private function getGoodsCommission( $goodsInfo )
    {
        //只有小店牛牛的才生效
        if ( $goodsInfo->uid <> service_Member_base::yph_uid ) {
            return $goodsInfo;
        }
        //分类筛选
        $goods_cat_id_array = array();
        if ( !is_array( $goodsInfo->goods_cat_id ) ) {
            $goodsInfo->goods_cat_id = explode( ',', $goodsInfo->goods_cat_id );
        }

        foreach ( $goodsInfo->goods_cat_id as $goods_cat_id ) {
            $goods_cat_id_array[] = $this->goods_category_model->getTopCatPidByCatId( $goods_cat_id );
        }

        //判断是否顶级分类
        if ( $this->member_mall_commission->commission_type == service_MemberMall_base::commission_type_category ) {
            $commission_cat_id_array = explode( ',', $this->member_mall_commission->commission_cat_id );
            //取两个分类数组的交集，判断是否满足条件
            $result = array_intersect( $goods_cat_id_array, $commission_cat_id_array );
            if ( empty( $result ) ) {
                return $goodsInfo;
            }
        }
        if ( in_array( service_Haitao_mall::global_cat_id, $goods_cat_id_array ) ) {
            return $goodsInfo;
        }

        //分类筛选结束
        $price_value = $this->getCommissionValue( $goodsInfo->goods_price );
        $goodsInfo->commission_fee_source = $goodsInfo->commission_fee;
        //覆盖佣金操作覆盖 
        if ( isset( $goodsInfo->commission_fee ) ) {
            $goodsInfo->commission_fee = $price_value;
        }
        return $goodsInfo;
    }

    /**
     * 取一组sku商品的特殊佣金
     * @param type $goodsInfo
     * @return type
     */
    public function getHandleSkuCommission( $goodsArray )
    {
        if ( empty( $goodsArray ) ) {
            return $goodsArray;
        }
        //判断是否有特殊佣金设置
        $member_mall_commission = $this->member_mall_commission = $this->getMemberMallCommissionInfo();
        if ( $member_mall_commission == FALSE ) {
            return $goodsArray;
        }
        if ( $member_mall_commission->commission_type == service_MemberMall_base::commission_type_none ) {
            return $goodsArray;
        }

        //if ( $this->member_mall_commission->commission_type == service_MemberMall_base::commission_type_category ) {
        $this->goods_category_model = $model = new service_GoodsCategory_base();
        $this->goods_category_model->setIs_cloud_product( service_GoodsCategory_base::is_cloud_product_yes );
        //}

        $goods_id_array = array();
        foreach ( $goodsArray as $goods_id => $goods_sku_array ) {
            $goods_id_array[] = $goods_id;
        }
        $goods_id_string = implode( ',', $goods_id_array );
        $goods_array = $this->getGoodsArray( $goods_id_string );
        foreach ( $goodsArray as $goods_id => $goods_sku_array ) {
            $goods_info = $goods_array[ $goods_id ];
            foreach ( $goods_sku_array as $goods_sku ) {
                $goods_sku->goods_price = $goods_sku->price;
                $goods_sku->goods_cat_id = $goods_info->goods_cat_id;
                $goods_sku->uid = $goods_info->uid;

                $this->getGoodsCommission( $goods_sku );
            }
        }
        return $goodsArray;
    }

    /**
     * 取一条sku商品的特殊佣金
     * @param type $goodsInfo
     * @return type
     */
    public function getHandleSkuDetailCommission( $goods_info )
    {
        if ( empty( $goods_info ) ) {
            return $goods_info;
        }
        if ( empty( $goods_info->goods_price ) ) {
            $goods_info->goods_price = $goods_info->price;
        }
        //判断是否有特殊佣金设置
        $member_mall_commission = $this->member_mall_commission = $this->getMemberMallCommissionInfo();
        if ( $member_mall_commission == FALSE ) {
            return $goods_info;
        }
        if ( $member_mall_commission->commission_type == service_MemberMall_base::commission_type_none ) {
            return $goods_info;
        }

        //if ( $this->member_mall_commission->commission_type == service_MemberMall_base::commission_type_category ) {
        $this->goods_category_model = $model = new service_GoodsCategory_base();
        $this->goods_category_model->setIs_cloud_product( service_GoodsCategory_base::is_cloud_product_yes );
        //}

        $goods_id_string = $goods_info->goods_id;
        $goods_array = $this->getGoodsArray( $goods_id_string );
        $goods_detail_info = $goods_array[ $goods_info->goods_id ]; //包含goods的uid和goods_cat_id的        

        $goods_info->goods_cat_id = $goods_detail_info->goods_cat_id;
        $this->getGoodsCommission( $goods_info );

        return $goods_info;
    }

    /**
     * 通过UID取member
     * @param type $uid
     * @return type
     */
    private function getMemberMallCommissionInfo()
    {
        $dao = dao_factory_base::getMemberMallCommissionDao();
        $where = "uid={$this->uid}";
        $dao->setWhere( $where );
        $dao->setLimit( 1 );
        $dao->setOrderby( 'member_mall_commission_id DESC' );
        $res = $dao->getListByWhere();
        if ( $res ) {
            return $res[ 0 ];
        } else {
            return false;
        }
    }

    /**
     * 取出 固定的佣金或佣金的百分比
     * @param type $goods_price
     * @return type
     */
    private function getCommissionValue( $goods_price )
    {
        return round( $goods_price * round( $this->member_mall_commission->price_value / 100, 2 ), 2 );
        /**
        if ( $this->member_mall_commission->price_class == service_MemberMall_base::price_class_fixed ) {
            //固定价格
            return $this->member_mall_commission->price_value;
        } else if ( $this->member_mall_commission->price_class == service_MemberMall_base::price_class_percent ) {
            return round( $goods_price * round( $this->member_mall_commission->price_value / 100, 2 ), 2 );
        }**/
    }

    private function getGoodsArray( $goods_id_string )
    {

        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id,uid,goods_cat_id' );
        $where = $dao->getWhereInStatement( 'goods_id', $goods_id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $goods_array_map = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $goods_array_map[ $value->goods_id ] = $value;
            }
        }
        return $goods_array_map;
    }

}
