<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Goods_base extends service_Model_base
{

    /**
     * item状态
     * 上架
     */
    const is_delete_no = 0;

    /**
     * item状态
     * 删除
     */
    const is_delete_yes = 1;

    /**
     * item状态
     * 下架
     */
    const is_delete_shelves = 2;

    /**
     * 是否供应商发布的云端商品库
     * 不是
     */
    const is_supplier_no = 0;

    /**
     * 是否供应商发布的云端商品库
     * 是
     */
    const is_supplier_yes = 1;

    /**
     * 佣金类型
     * 固定金额
     * @var type 
     */
    const commission_type_fixed = 0;

    /**
     * 佣金类型
     * 总价的比例
     * @var type 
     */
    const commission_type_scale = 1;

    /**
     * 商品来源
     * 聚店
     * @var type 
     */
    const goods_source_090 = 0;

    /**
     * 商品来源 
     * 京东
     * @var type 
     */
    const goods_source_jd = 1;

    /**
     * 商品来源 
     * 淘宝
     * @var type 
     */
    const goods_source_tb = 2;

    /**
     * 商品类型
     * 普通商品
     * 会员购买打折
     * @var type 
     */
    const goods_type_normal = 1;

    /**
     * 商品类型
     * 会员商品
     * @var type 
     */
    const goods_type_member = 2;

    /**
     * 商品类型
     * 特惠商品
     * @var type 
     */
    const goods_type_sale = 3;

    /**
     * 商品类型
     * 会员专卖
     * @var type 
     */
    const goods_type_member_monopoly = 4;

    /**
     * 商品类型
     * 商城商品
     * @var type 
     */
    const goods_type_mall = 5;

    /**
     * 商品是否支持积分抵售价
     * 支持
     */
    const is_integral_yes = 1;

    /**
     * 商品是否支持积分抵售价
     * 不支持
     */
    const is_integral_n = 0;

    protected $errorMessage;
    protected $goods_id;
    protected $item_id;
    protected $uid;
    private $goods_priview;
    protected $member_level;
    protected $goodsInfo;

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setItem_id( $item_id )
    {
        $this->item_id = $item_id;
    }

    function setGoods_id( $goods_id )
    {
        $this->goods_id = $goods_id;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function getGoods_priview()
    {
        return $this->goods_priview;
    }

    function setMember_level( $member_level )
    {
        $this->member_level = $member_level;
    }

    /**
     * 取商品的规格
     * @return type
     */
    public function getGoodsSpecArray()
    {
        if ( empty( $this->goods_id ) ) {
            return array( 'result' => new stdClass(), 'result_object' => new stdClass() );
        }
        $dao = dao_factory_base::getGoodsSpecDao();
        $where = "goods_id={$this->goods_id} AND is_delete=0";
        $dao->setWhere( $where );
        $dao->setField( 'spec_id,spec_value_id,spec_name,spec_value_name' );
        $dao->setOrderby( 'goods_spec_id DESC' );
        $res = $dao->getListByWhere();
        $result = new stdClass();
        $result_object = new stdClass();
        if ( $res ) {
            $result = array();
            $result_object = array();
            foreach ( $res as $spec ) {
                $result[ $spec->spec_id ][] = $spec->spec_value_id;
                $result_object[ $spec->spec_id ][] = $spec;
            }
        }
        return array( 'result' => $result, 'result_object' => $result_object );
    }

    public function getItemCategoryArray( $item_cat_id, $uid )
    {
        $dao = dao_factory_base::getItemCategoryDao();
        $dao->setField( 'item_cat_id,cat_name,item_count' );
        $where = "uid={$uid} AND is_delete=0";
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $result_array = array();
        if ( $res ) {
            $item_cat_id_array = empty( $item_cat_id ) ? array() : explode( ',', $item_cat_id );
            foreach ( $res AS $category ) {
                if ( in_array( $category->item_cat_id, $item_cat_id_array ) ) {
                    $category->checked = true;
                } else {
                    $category->checked = false;
                }
                $result_array[] = $category;
            }
        }
        return $result_array;
    }

    public function getItemInfoById( $field = '*' )
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setPk( $this->item_id );
        $dao->setField( $field );
        $res = $dao->getInfoByPk();
        if ( $res ) {
            if ( $res->goods_uid == 46 || $res->goods_uid == 2506 ) {
                srand( $res->item_id );
                $sales_volume = rand( 10, 1000 );
                $res->sales_volume = $res->sales_volume + $sales_volume;
            }
            $res->collect_count = $res->collect_count + $res->collect_count_variable;
            $res->item_desc = htmlspecialchars_decode( $this->getGoodsDescById( $res->goods_id ) );
        }
        return $res;
    }

    /**
     * 通过goods_id取goods_desc
     * @param type $goods_id
     */
    private function getGoodsDescById( $goods_id )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_desc' );
        $dao->setPk( $goods_id );
        $res = $dao->getInfoByPk();
        //替换手机号 如果影响性能的话就放在存储环节
        //$res->goods_desc = preg_replace( '/(1\d{2})\d{4}(\d{4})/', '${1}*****${2}', $res->goods_desc );
        return $res->goods_desc;
    }

    public function getGoodsImageArray( $goods_id )
    {
        $dao = dao_factory_base::getGoodsImageDao();
        $dao->setWhere( "goods_id={$goods_id} AND is_delete=0" );
        $dao->setOrderby( "goods_image_sort DESC,id DESC" );
        $dao->setField( 'goods_image_id' );
        $res = $dao->getListByWhere();
        if ( $res ) {
            foreach ( $res as $key => $value ) {
                $value->goods_image_id = $this->getImage( $value->goods_image_id, '640', 'goods' );
            }
        }
        return $res;
    }

    public function getGoodsInfoById( $field = '*' )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setPk( $this->goods_id );
        //$dao->setField( $field );
        $res = $dao->getInfoByPk();
        if ( $res ) {
            if ( $res->uid == 46 && !empty( $res->goods_id ) ) {
                srand( $res->goods_id );
                $sales_volume = rand( 10, 1000 );
                $collect_count = rand( 100, 2000 );
                $res->sales_volume = $res->sales_volume + $sales_volume;
                $res->collect_count = $collect_count;
            }
            if ( !empty( $res->goods_desc ) ) {
                $res->goods_desc = htmlspecialchars_decode( $res->goods_desc );
            }
            unset( $res->commission_seller_different, $res->commission_different_object );
        }
        return $res;
    }

    public function getGoodsInfo( $image_size = '80' )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setPk( $this->goods_id );

        $info = $dao->getInfoByPk();
        if ( $info ) {
            $goods_image_array = $goods_image_id_array = array();
            $goods_image_ids = json_decode( $info->goods_image_ids );
            if ( $goods_image_ids ) {
                foreach ( $goods_image_ids AS $goods_image_id ) {
                    $goods_image_array[] = THUMB_URL . 'goods_' . $image_size . '/' . $goods_image_id . '.jpg';
                    $goods_image_id_array[] = $goods_image_id;
                }
            }
            $info->goods_image_array = $goods_image_array;
            $info->goods_image_id_array = $goods_image_id_array;
            $info->goods_image_url = $this->getImage( $info->goods_image_id, $image_size, 'goods' );
            $info->goods_desc = htmlspecialchars_decode( $info->goods_desc );
        }
        return $info;
    }

    public function getItemInfo( $image_size = '80' )
    {
        $dao = dao_factory_base::getItemDao();
        $dao->setPk( $this->item_id );

        $status_array = Tmac::config( 'system.system.status', APP_BASE_NAME );
        $info = $dao->getInfoByPk();
        if ( $info ) {
            $this->goods_id = $info->goods_id;
            $info instanceof entity_Item_base;
            if ( $info->is_delete == 1 ) {
                return false;
            }
            $goods_info = $this->getGoodsInfo( $image_size );
            $goods_info instanceof entity_Goods_base;
            $goods_info->item_cat_id = $info->item_cat_id;
            $goods_info->shipping_fee = $info->shipping_fee;
            $goods_info->url = MOBILE_URL . 'item/' . $this->item_id . '.html';
            unset( $goods_info->promote_price );
            unset( $goods_info->promote_start_date );
            unset( $goods_info->promote_end_date );
            unset( $goods_info->goods_image_ids );
            unset( $goods_info->is_on_sale );
            unset( $goods_info->is_delete );
            if ( $goods_info->uid == $info->uid ) {
                //是供应商自己发的
                $goods_info->item_cat_id = $info->item_cat_id;
                $goods_info->recommend = $info->recommend;
                $goods_info->status = $status_array[ $info->is_delete ];
                $this->goods_priview = true;
                return $goods_info;
            } else {
                $this->goods_priview = false;
                //分销的
                $goods_info->goods_name = $info->item_name;
                $goods_info->goods_price = $info->item_price;
                $goods_info->goods_image_id = $info->goods_image_id;
                $goods_info->item_cat_id = $info->item_cat_id;
                $goods_info->recommend = $info->recommend;
                $goods_info->status = $status_array[ $info->is_delete ];
                $goods_info->goods_image_url = $this->getImage( $info->goods_image_id, $image_size, 'goods' );
                return $goods_info;
            }
        }
        return false;
    }

    /**
     * 取会员商品的普通商品折扣
     * @param type $price
     * @param type $member_level
     * @return type
     */
    public function getGoodsPromotePrice( $price, $goods_type, $member_level )
    {
        $return = array(
            'price' => $price,
            'price_source' => $price
        );
        //普通商品类型和商城商品类型的打折
        if ( $goods_type <> service_Goods_base::goods_type_normal && $goods_type <> service_Goods_base::goods_type_mall ) {
            return $return;
        }
        if ( $member_level == 0 || empty( $member_level ) ) {
            return $return;
        }
        if ( $goods_type == service_Goods_base::goods_type_normal ) {
            $goods_offer_rate_array = Tmac::config( 'goods.goods.goods_offer_rate', APP_BASE_NAME );
        } elseif ( $goods_type == service_Goods_base::goods_type_mall ) {
            $goods_offer_rate_array = Tmac::config( 'goods.goods.goods_mall_offer_rate', APP_BASE_NAME );
        }

        $rate = $goods_offer_rate_array[ $member_level ] / 10;
        $return[ 'price' ] = round( $price * $rate, 2 );
        return $return;
    }

}
