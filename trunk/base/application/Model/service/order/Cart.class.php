<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Cart.class.php 363 2016-06-10 16:47:07Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Cart_base extends service_Order_base
{

    const free_shipping_amount = 1000;

    protected $item_num;
    protected $goods_sku_id;
    protected $goods_stock_array;
    protected $goods_uid;
    protected $item_uid;
    protected $cart_array;
    protected $address_id;
    protected $session_id;
    protected $available_integral;

    /**
     * 商城专用，商城主uid
     * @var type 
     */
    protected $mall_uid;

    function setMall_uid( $mall_uid )
    {
        $this->mall_uid = $mall_uid;
    }

    function setItem_num( $item_num )
    {
        $this->item_num = $item_num;
    }

    function setGoods_sku_id( $goods_sku_id )
    {
        $this->goods_sku_id = $goods_sku_id;
    }

    function getGoods_stock_array()
    {
        return $this->goods_stock_array;
    }

    function setGoods_uid( $goods_uid )
    {
        $this->goods_uid = $goods_uid;
    }

    function setItem_uid( $item_uid )
    {
        $this->item_uid = $item_uid;
    }

    function setCart_array( $cart_array )
    {
        $this->cart_array = $cart_array;
    }

    function setAddress_id( $address_id )
    {
        $this->address_id = $address_id;
    }

    function setAvailable_integral( $available_integral )
    {
        $this->available_integral = $available_integral;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * order专用详情
     */
    private function getGoodsInfoById( $goods_id, $field = '*' )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( $field );
        $dao->setPk( $goods_id );
        return $dao->getInfoByPk();
    }

    /**
     * 取默认的收货地址
     * $this->uid;
     * $this->address_id;
     * $this->getDefaultAddress();
     */
    public function getDefaultAddress()
    {
        $dao = dao_factory_base::getMemberAddressDao();
        $dao->setField( 'address_id,uid,consignee,mobile,full_address' );
        if ( empty( $this->address_id ) ) {
            //查询默认的收货地址
            $where = "uid={$this->uid} AND is_delete=0 AND is_default=1";
            $dao->setWhere( $where );
            $address_info = $dao->getInfoByWhere();
            if ( $address_info == false ) {
                $this->errorMessage = '还没有添加过收货地址，请先添加收货地址';
                return false;
            }
            return $address_info;
        } else {
            //查询对应的address_id
            $dao->setPk( $this->address_id );
            $address_info = $dao->getInfoByPk();
            if ( $address_info == false ) {
                $this->errorMessage = '收货地址不存在，请先添加收货地址';
                return false;
            }
            if ( $address_info->uid <> $this->uid ) {
                $this->errorMessage = '没有权限的收货地址';
                return false;
            }
            return $address_info;
        }
    }

    /**
     * 保存/新增 购物车
     * $this->item_id
     * $this->item_num
     * $this->goods_sku_id
     * $this->uid
     * $this->saveCart();
     */
    public function saveCart()
    {
        //做一些权限判断
        $goods_info = parent::getItemInfoById( 'item_id,goods_id,item_name,item_price,item_stock,goods_image_id,is_delete,uid,goods_uid,outer_code,commission_fee,commission_seller_different,commission_different_object,shipping_fee,goods_type,is_integral' );
        if ( $goods_info == false ) {
            $this->errorMessage = '商品项目不存在';
            return false;
        }
        if ( $goods_info->is_delete == 1 ) {
            $this->errorMessage = '商品项目已经下线';
            return false;
        }

        $goods_model = new service_Goods_mobile();
        $goods_model->setGoods_id( $goods_info->goods_id );
        $goods_model->getGoodsInfo();
        $goods_sku_array = $goods_model->getGoodsSkuArray();
        if ( count( $goods_sku_array ) > 0 && empty( $this->goods_sku_id ) ) {
            $this->errorMessage = '请先选择商品规格';
            return true;
        }

        //检查商品的sku信息
        if ( empty( $this->goods_sku_id ) ) {
            if ( empty( $goods_info->item_price ) || empty( $goods_info->item_stock ) ) {
                $this->errorMessage = '没有库存了';
                return false;
            }
            $goods_info->price = $goods_info->item_price;
            $goods_info->stock = $goods_info->item_stock;
            $goods_info->outer_code = $goods_info->outer_code;
            $goods_info->sku_name = '';
            $goods_info->goods_sku = '';
            $goods_info->goods_sku_json = '';
        } else {
            $goods_sku_info = parent::getGoodsSkuById( $this->goods_sku_id );
            if ( $goods_sku_info == false ) {
                $this->errorMessage = '商品规格不存在';
                return false;
            }
            if ( $goods_sku_info->goods_id <> $goods_info->goods_id ) {
                $this->errorMessage = '商品的规格不正确';
                return false;
            }
            $goods_info->price = $goods_sku_info->price;
            $goods_info->stock = $goods_sku_info->stock;
            $goods_info->outer_code = $goods_sku_info->outer_code;
            $goods_info->sku_name = $goods_sku_info->sku_name;
            $goods_info->goods_sku = $goods_sku_info->goods_sku;
            $goods_info->goods_sku_json = $goods_sku_info->goods_sku_json;
            $goods_info->commission_fee = $goods_sku_info->commission_fee;
        }

        if ( $goods_info->stock < $this->item_num ) {
            $this->errorMessage = '您选的商品数量已经超过商品库存了';
            return false;
        }
        $goods_info->goods_sku_id = $this->goods_sku_id;
        $goods_info->goods_image_url = $this->getImage( $goods_info->goods_image_id, '50', 'goods' );

        //取分销商 不同级别的不同 和 系统 总佣金的佣金比例        
        $commission_fee = $goods_info->commission_fee;

        $price = $goods_model->getGoodsPromotePrice( $goods_info->price, $goods_info->goods_type, $this->memberInfo->member_level );
        $goods_info->price = $price[ 'price' ];
        $goods_info->item_total_price = $price[ 'price_source' ];
        /**
         * 检测会员商品的购买权限
         */
        $goods_member_level = 0;
        $goodsInfo = $this->getGoodsInfoById( $goods_info->goods_id, 'goods_type,commission_fee_rank,goods_member_level' );
        if ( $goodsInfo->goods_type == service_Goods_base::goods_type_member ) {
            $checkGoodsMemberLevelPurview = $this->checkGoodsMemberPurview( $goodsInfo->goods_member_level );
            if ( $checkGoodsMemberLevelPurview == false ) {
                return false;
            }
            $item_number = 1;
            $goods_member_level = $goodsInfo->goods_member_level;
        } elseif ( $goodsInfo->goods_type == service_Goods_base::goods_type_sale ) {//特惠商品一次只让买一个
            /**
              $checkGoodsPurview = $this->checkGoodsPurview();
              if ( $checkGoodsPurview == false ) {
              return false;
              }
              $item_number = 1;
             * 
             */
            $item_number = $goods_info->stock > $this->item_num ? $this->item_num : $goods_info->stock;
        } else {
            $item_number = $goods_info->stock > $this->item_num ? $this->item_num : $goods_info->stock;
        }
        $commission_fee_rank = $goodsInfo->commission_fee_rank;

        $cart_dao = dao_factory_base::getCartDao();
        $session_id = $this->session_id = session_id();
        if ( empty( $this->uid ) ) {
            $where = "session_id='{$session_id}' AND item_id={$this->item_id} AND goods_sku_id={$this->goods_sku_id}";
        } else {
            $where = "uid='{$this->uid}' AND item_id={$this->item_id} AND goods_sku_id={$this->goods_sku_id}";
            //更新所有购物车中的session_id为uid
            $entity_Cart_base = new entity_Cart_base();
            $entity_Cart_base->uid = $this->uid;
            $cart_dao->setWhere( "session_id='{$session_id}'" );
            $cart_dao->updateByWhere( $entity_Cart_base );
        }
        $cart_dao->setField( 'cart_id' );
        $cart_dao->setWhere( $where );
        $cart_info = $cart_dao->getInfoByWhere();

        $entity_Cart_base = new entity_Cart_base();

        if ( $cart_info ) {
            //判断是否存在，存在就更新    
            $entity_Cart_base->uid = $this->uid;
            $entity_Cart_base->item_price = $goods_info->price;
            $entity_Cart_base->item_total_price = $goods_info->item_total_price;
            if ( $goodsInfo->goods_type == service_Goods_base::goods_type_member ) {
                $entity_Cart_base->item_number = $item_number;
            } else {
                $entity_Cart_base->item_number = new TmacDbExpr( 'item_number+' . $item_number );
            }
            $entity_Cart_base->commission_fee = $commission_fee;
            $entity_Cart_base->commission_fee_rank = $commission_fee_rank;
            $entity_Cart_base->goods_member_level = $goods_member_level;
            $cart_dao->setPk( $cart_info->cart_id );
            $res = $cart_dao->updateByPk( $entity_Cart_base );
            if ( $res ) {
                return $cart_info->cart_id;
            }
        } else {
            $member_setting_dao = dao_factory_base::getMemberSettingDao();
            $member_setting_dao->setPk( $goods_info->uid );
            $member_setting_dao->setField( 'shop_name' );
            $member_setting_info = $member_setting_dao->getInfoByPk();
            $shop_name = $member_setting_info->shop_name;
            //不存在就新插入
            $entity_Cart_base->uid = $this->uid;
            $entity_Cart_base->session_id = session_id();
            $entity_Cart_base->item_id = $goods_info->item_id;
            $entity_Cart_base->goods_id = $goods_info->goods_id;
            $entity_Cart_base->goods_name = $goods_info->item_name;
            $entity_Cart_base->goods_image_id = $goods_info->goods_image_id;
            $entity_Cart_base->item_total_price = $goods_info->item_total_price;
            $entity_Cart_base->item_price = $goods_info->price;
            $entity_Cart_base->item_number = $item_number;
            $entity_Cart_base->outer_code = $goods_info->outer_code;
            $entity_Cart_base->goods_sku_id = $this->goods_sku_id;
            $entity_Cart_base->goods_sku_name = parent::getSkuNameFromSkuJson( $goods_info->goods_sku_json );
            $entity_Cart_base->goods_sku_json = $goods_info->goods_sku_json;
            $entity_Cart_base->goods_uid = $goods_info->goods_uid;
            $entity_Cart_base->item_uid = empty( $this->item_uid ) ? $goods_info->uid : $this->item_uid;
            $entity_Cart_base->shop_name = $shop_name;
            $entity_Cart_base->commission_fee = $commission_fee;
            $entity_Cart_base->commission_fee_rank = $commission_fee_rank;
            $entity_Cart_base->shipping_fee = $goods_info->shipping_fee;
            $entity_Cart_base->goods_member_level = $goods_member_level;
            $entity_Cart_base->goods_type = $goods_info->goods_type;
            $entity_Cart_base->is_integral = $goods_info->is_integral;
            $res = $cart_dao->insert( $entity_Cart_base );
        }

        return $res;
    }

    /**
     * 检测会员商品的购买权限
     * 
     * 每一级的会员商品每个账号可以买多次，直推和排位佣金继续分配就行了
     * 会员商品不能跳着买。
     * 必须一级一级的买
     * 按顺序买不能跳着买
     * 购买会员商品,判断当前有没有未处理完的会员商品退款
     * $goods_member_level
     */
    private function checkGoodsMemberPurview( $goods_member_level )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->uid );
        $this->memberInfo = $dao->getInfoByPk();

        $next_member_level = $this->memberInfo->member_level == service_Member_base::member_level_9 ? $this->memberInfo->member_level : $this->memberInfo->member_level + 1;
        if ( $goods_member_level > $next_member_level ) {
            $this->errorMessage = '您目前只能购买LV' . $next_member_level . '级别或以下的会员哟';
            return false;
        }
        //判断购物车中有没有未付款的会员商品
        $cart_dao = dao_factory_base::getCartDao();
        $session_id = $this->session_id;
        if ( empty( $this->uid ) ) {
            $where = "session_id='{$session_id}' AND goods_member_level>=" . service_Member_base::member_level_1;
        } else {
            $where = "uid={$this->uid} AND goods_member_level>=" . service_Member_base::member_level_1;
            //更新所有购物车中的session_id为uid
            $entity_Cart_base = new entity_Cart_base();
            $entity_Cart_base->uid = $this->uid;
            $cart_dao->setWhere( "session_id='{$session_id}'" );
            $cart_dao->updateByWhere( $entity_Cart_base );
        }
        $cart_dao->setField( 'cart_id' );
        $cart_dao->setWhere( $where );
        $cart_info = $cart_dao->getInfoByWhere();
        if ( $cart_info ) {
            $this->errorMessage = '您的购物车中已经有一个会员商品了,请先结算或取消后再购买新的会员商品哟';
            return false;
        }
        return true;
    }

    /**
     * 检测特惠商品的购买权限
     * 
     * 特惠商品只能一次买一件
     * $goods_member_level
     */
    private function checkGoodsPurview()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->uid );
        $this->memberInfo = $dao->getInfoByPk();

        //判断购物车中有没有未付款的会员商品
        $cart_dao = dao_factory_base::getCartDao();
        $session_id = $this->session_id;
        if ( empty( $this->uid ) ) {
            $where = "session_id='{$session_id}'";
        } else {
            $where = "uid={$this->uid}";
            //更新所有购物车中的session_id为uid
            $entity_Cart_base = new entity_Cart_base();
            $entity_Cart_base->uid = $this->uid;
            $cart_dao->setWhere( "session_id='{$session_id}'" );
            $cart_dao->updateByWhere( $entity_Cart_base );
        }
        $cart_dao->setField( 'cart_id,goods_id,goods_type' );
        $cart_dao->setWhere( $where );
        $cart_array = $cart_dao->getListByWhere();
        if ( $cart_array ) {
            $goods_type_member_count = 0;
            $goods_type_sale_count = 0;
            $goods_type_other = 0;
            foreach ( $cart_array as $cart_info ) {
                if ( $cart_info->goods_type == service_Goods_base::goods_type_member ) {
                    $goods_type_member_count++;
                } else if ( $cart_info->goods_type == service_Goods_base::goods_type_sale ) {
                    $goods_type_sale_count++;
                } else {
                    $goods_type_other++;
                }
            }
            if ( $goods_type_sale_count > 0 ) {
                $this->errorMessage = '您的购物车中已经有一个特惠商品了,请先结算或取消后再购买新的特惠商品哟';
                return false;
            }
        }
        return true;
    }

    /**
     * 取分销商 和 系统 的佣金
     * 不同的分销商佣金比例不同
     * 
     * @param type $item_uid
     * @param type $commission_fee
     * @param type $commission_seller_different
     * @param type $commission_different_object
     * @param type $goods_price
     */
    public function getCommissionArray( $item_uid, $commission_fee, $commission_seller_different, $commission_different_object, $goods_price )
    {
        $commission_fee_array = array(
            'commission_fee' => $commission_fee,
            'commission_system_fee' => 0
        );
        //查询$item_uid的会员级别
        $member_dao = dao_factory_base::getMemberDao();
        $member_dao->setField( 'member_type,member_class,promotion_type' );
        $member_dao->setPk( $item_uid );
        $member_info = $member_dao->getInfoByPk();

        if ( $member_info->member_type <> service_Member_base::member_type_seller ) {
            //Log::getInstance( 'post_error' )->write( $item_uid.'|'.var_export( $member_info, true ) );
            return $commission_fee_array;
        }
        //检测$goods_id中有没有设置 分销商的佣金不同（0:所有的分销商佣金相同|1:分销商佣金按级别不同区分）        
        if ( $commission_seller_different == 0 ) {
            return $this->handleCommissionPromotion( $goods_price, $member_info, $commission_fee_array );
        }

        $member_class_array = Tmac::config( 'member.member.commission_seller', APP_BASE_NAME );
        $member_class = isset( $member_class_array[ $member_info->member_class ] ) ? $member_class_array[ $member_info->member_class ] : 'commission_seller_free';
        $commission_different_array = unserialize( $commission_different_object );
        //分销商的佣金占 总佣金的百分比
        $item_uid_commission_scale = $commission_different_array[ $member_class ] / 100;
        //系统的佣金 占 总佣金的百分比
        $system_commission_scale = 1 - $item_uid_commission_scale;

        $commission_fee_array[ 'commission_fee' ] = round( $commission_fee * $item_uid_commission_scale, 2 );
        $commission_fee_array[ 'commission_system_fee' ] = round( $commission_fee * $system_commission_scale, 2 );

        return $this->handleCommissionPromotion( $goods_price, $member_info, $commission_fee_array );
    }

    /**
     * 处理 用户佣金促销
     * @param type $goods_price
     * @param type $member_info
     * @param type $commission_fee_array
     * @return type
     */
    private function handleCommissionPromotion( $goods_price, $member_info, $commission_fee_array )
    {
        if ( $member_info->promotion_type == service_Member_base::promotion_type_seller ) {
            $commission_fee_ = $commission_fee_array[ 'commission_fee' ] * 2;
            //$commission_system_fee = $commission_fee_array[ 'commission_system_fee' ] * 2;
            //最高佣金 商品总价的70%;
            $max_commission_fee = round( $goods_price * service_Order_base::max_commission_rate, 2 );

            $commission_fee_array[ 'commission_fee' ] = $commission_fee_ > $max_commission_fee ? $max_commission_fee : $commission_fee_;
            //$commission_fee_array[ 'commission_system_fee' ] = $commission_system_fee > $max_commission_fee ? $max_commission_fee : $commission_system_fee;
        }
        return $commission_fee_array;
    }

    /**
     * 更新用户的未登录前的购物数据
     * $this->uid;
     * $this->updateCartSessionIdToUid();
     * @return type
     */
    public function updateCartSessionIdTOUid()
    {
        $cart_dao = dao_factory_base::getCartDao();
        $session_id = session_id();
        $entity_Cart_base = new entity_Cart_base();
        $entity_Cart_base->uid = $this->uid;
        $cart_dao->setWhere( "session_id='{$session_id}'" );
        return $cart_dao->updateByWhere( $entity_Cart_base );
    }

    /**
     * 取购物车里的商品数据
     * $this->uid;
     * $this->getCartList();
     */
    public function getCartList()
    {
        $dao = dao_factory_base::getCartDao();
        if ( empty( $this->uid ) ) {
            $session_id = session_id();
            $where = "session_id='{$session_id}'";
        } else {
            $where = "uid={$this->uid}";
        }
        $dao->setWhere( $where );
        $dao->setOrderby( 'cart_id DESC' );
        $res = $dao->getListByWhere();
        //$use_integral = false; //有没有积分商品
        //$integral_value = 0; //可抵扣的积分
        $result_array = array( 'cart_array' => array(), 'cart_amount' => array(), 'use_integral' => array(), 'integral_value' => array() );
        $sku_id_array = array( 'goods_sku_id' => array(), 'goods_id' => array() );
        $shop_name_array = array();

        $return_array = array();
        if ( $res ) {
            foreach ( $res as $cart_info ) {
                $cart_info->goods_image_id = $this->getImage( $cart_info->goods_image_id, '50', 'goods' );
                //店铺名称放在统一数组中
                $shop_name_array[ $cart_info->goods_uid ][ $cart_info->item_uid ] = $cart_info->shop_name;
                unset( $cart_info->shop_name );

                unset( $cart_info->goods_sku_json );
                unset( $cart_info->session_id );
                $result_array[ 'cart_array' ][ $cart_info->goods_uid ][ $cart_info->item_uid ][] = $cart_info;
                if ( isset( $result_array[ 'cart_amount' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] ) ) {
                    $result_array[ 'cart_amount' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] += $cart_info->item_price * $cart_info->item_number;
                } else {
                    $result_array[ 'cart_amount' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] = $cart_info->item_price * $cart_info->item_number;
                }

                //运费计算，同一店铺的商品取运费最高的
                $result_array[ 'shipping_fee' ][ $cart_info->goods_uid ][ $cart_info->item_uid ][] = $cart_info->shipping_fee;
                if ( $cart_info->shipping_fee == 0 ) {
                    $result_array[ 'free_shipping_fee' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] = true;
                }
                if ( $cart_info->goods_type == service_Goods_base::goods_type_sale ) {
                    $result_array[ 'shipping_fee_goods_sale' ][ $cart_info->goods_uid ][ $cart_info->item_uid ][] = $cart_info->shipping_fee;
                }
                //判断是否参加积分活动
                if ( $cart_info->is_integral == service_Goods_base::is_integral_yes ) {
                    $result_array[ 'use_integral' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] = true;
                    if ( isset( $result_array[ 'integral_value' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] ) ) {
                        $result_array[ 'integral_value' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] += $cart_info->item_number * $cart_info->item_price;
                    } else {
                        $result_array[ 'integral_value' ][ $cart_info->goods_uid ][ $cart_info->item_uid ] = $cart_info->item_number * $cart_info->item_price;
                    }
                }
                if ( empty( $cart_info->goods_sku_id ) ) {
                    $sku_id_array[ 'goods_id' ][] = $cart_info->goods_id;
                } else {
                    $sku_id_array[ 'goods_sku_id' ][] = $cart_info->goods_sku_id;
                }
            }
            $this->goods_stock_array = $this->getGoodsStockArray( $sku_id_array );

            /**
             * 运费处理运费 这块实现购物车里面有一个包邮 的 其它的就 包邮吧
             * 
             */
            //重组一份友好的数据结构给购物车页面
            foreach ( $result_array[ 'cart_array' ] AS $goods_uid => $cart_item_array ) {
                foreach ( $cart_item_array as $item_uid => $cart_array ) {
                    $cart_res = array();
                    $integral_value = $result_array[ 'integral_value' ][ $goods_uid ][ $item_uid ];
                    $use_available_integral = $this->available_integral >= $integral_value ? $integral_value : $this->available_integral;

                    $cart_res[ 'goods_uid' ] = $goods_uid;
                    $cart_res[ 'item_uid' ] = $item_uid;
                    $cart_res[ 'shop_name' ] = $shop_name_array[ $goods_uid ][ $item_uid ];
                    $cart_res[ 'shipping_fee' ] = empty( $result_array[ 'shipping_fee_goods_sale' ][ $goods_uid ][ $item_uid ] ) ? max( $result_array[ 'shipping_fee' ][ $goods_uid ][ $item_uid ] ) : max( $result_array[ 'shipping_fee_goods_sale' ][ $goods_uid ][ $item_uid ] );
                    if ( isset( $result_array[ 'free_shipping_fee' ][ $goods_uid ][ $item_uid ] ) && $result_array[ 'free_shipping_fee' ][ $goods_uid ][ $item_uid ] === TRUE ) {
                        $cart_res[ 'shipping_fee' ] = 0;
                    }
                    $use_integral = $result_array[ 'use_integral' ][ $goods_uid ][ $item_uid ];
                    $total_price = $result_array[ 'cart_amount' ][ $goods_uid ][ $item_uid ];
                    $shipping_fee = $cart_res[ 'shipping_fee' ];
                    if ( $use_integral ) {
                        $total_amount = ($total_price - $use_available_integral) + $shipping_fee;
                    } else {
                        $total_amount = $total_price + $shipping_fee;
                    }
                    $cart_res[ 'item_amount' ] = $total_price; //$result_array[ 'cart_amount' ][ $goods_uid ][ $item_uid ];
                    $cart_res[ 'all_amount' ] = $total_amount; //$cart_res[ 'item_amount' ] + $cart_res[ 'shipping_fee' ];
                    $cart_res[ 'item_list' ] = $cart_array;
                    
                    $cart_res['order_payable_amount'] = $total_price + $shipping_fee;//应付总价
                    $cart_res[ 'use_integral' ] = $use_integral;//能不能使用积分 
                    $cart_res[ 'use_available_integral' ] = $use_available_integral;//能使用的积分 
                    $return_array[] = $cart_res;
                }
            }
        }
        return $return_array;
    }

    private function getGoodsStockArray( $sku_id_array )
    {
        $goods_sku_dao = dao_factory_base::getGoodsSkuDao();
        $goods_dao = dao_factory_base::getGoodsDao();

        $goods_stock_array = array();
        if ( !empty( $sku_id_array[ 'goods_sku_id' ] ) ) {
            $goods_sku_id_string = implode( ',', $sku_id_array[ 'goods_sku_id' ] );
            $where = $goods_sku_dao->getWhereInStatement( 'goods_sku_id', $goods_sku_id_string );
            $goods_sku_dao->setWhere( $where );
            $goods_sku_dao->setField( 'goods_sku_id,goods_id,stock' );
            $res = $goods_sku_dao->getListByWhere();
            foreach ( $res as $value ) {
                $goods_stock_array[ 'goods_sku_id' ][ $value->goods_sku_id ] = $value->stock;
            }
        }
        if ( !empty( $sku_id_array[ 'goods_id' ] ) ) {
            $goods_id_string = implode( ',', $sku_id_array[ 'goods_id' ] );
            $where = $goods_dao->getWhereInStatement( 'goods_id', $goods_id_string );
            $goods_dao->setWhere( $where );
            $goods_dao->setField( 'goods_id,goods_stock' );
            $res = $goods_dao->getListByWhere();
            foreach ( $res as $value ) {
                $goods_stock_array[ 'goods_id' ][ $value->goods_id ] = $value->goods_stock;
            }
        }
        return $goods_stock_array;
    }

    public function updateCartSessionId( $uid )
    {
        $dao = dao_factory_base::getCartDao();
        $session_id = session_id();
        $dao->setWhere( "session_id={$session_id}" );
        $entity_Cart_base = new entity_Cart_base();
        $entity_Cart_base->uid = $uid;
        return $dao->updateByWhere( $entity_Cart_base );
    }

    /**
     * 检测购物车的订单确认的 最终购物车中的订单 及 数量的格式和权限
     * $this->item_uid;
     * $this->cart_array;
     * $this->updateCart();
     */
    public function updateCart()
    {
        if ( empty( $this->cart_array ) ) {
            return true;
        }
        $cart_i = 0;
        $first_cart_id = 0;
        $new_cart_array = array();
        foreach ( $this->cart_array as $cart_id => $goods_num ) {
            $cart_id = intval( $cart_id );
            $goods_num = intval( $goods_num );
            $new_cart_array [ $cart_id ] = empty( $goods_num ) ? 1 : $goods_num;
            $cart_i === 0 && $first_cart_id = $cart_id;
            $cart_i++;
        }
        if ( empty( $this->item_uid ) ) {
            $item_uid = $this->getCartDefaultItemUid();
        } else {
            $item_uid = $this->item_uid;
        }


        $dao = dao_factory_base::getCartDao();
        $where = "cart_id={$first_cart_id} AND uid={$this->uid} AND item_uid={$item_uid}";
        $dao->setWhere( $where );
        $dao->setField( 'goods_uid' );
        $cart_info = $dao->getInfoByWhere();
        if ( $cart_info == false ) {
            throw new TmacClassException( '购物车数据不合法~~' );
        }
        $goods_uid = $cart_info->goods_uid;

        $where = "uid={$this->uid} AND item_uid={$item_uid}";
        $dao->setWhere( $where );
        $dao->setField( 'cart_id,item_id,goods_id,goods_sku_id,item_number,goods_uid,goods_member_level' );
        $res = $dao->getListByWhere();
        if ( $res ) {
            $cart_id_array = array();
            $cart_array = array();
            $sku_id_array = array( 'goods_sku_id' => array(), 'goods_id' => array() );

            foreach ( $res as $cart_info ) {
                //预防不同供应商的商品下在一个订单中了
                if ( $goods_uid <> $cart_info->goods_uid ) {
                    continue;
                }

                if ( !empty( $new_cart_array[ $cart_info->cart_id ] ) ) {
                    $cart_id_array[] = $cart_info->cart_id;
                    $cart_array[] = $cart_info;
                    if ( empty( $cart_info->goods_sku_id ) ) {
                        $sku_id_array[ 'goods_id' ][] = $cart_info->goods_id;
                    } else {
                        $sku_id_array[ 'goods_sku_id' ][] = $cart_info->goods_sku_id;
                    }
                }
            }
            if ( empty( $cart_array ) ) {
                throw new TmacClassException( '购物车数据不能为空哟~~' );
            }
            //购物车中的最新库存数据
            $goods_stock_array = $this->getGoodsStockArray( $sku_id_array );
            foreach ( $cart_array as $cart_info ) {
                if ( empty( $cart_info->goods_sku_id ) ) {
                    $stock = $goods_stock_array[ 'goods_id' ][ $cart_info->goods_id ];
                } else {
                    $stock = $goods_stock_array[ 'goods_sku_id' ][ $cart_info->goods_sku_id ];
                }
                $item_number = $cart_info->item_number;
                if ( $new_cart_array[ $cart_info->cart_id ] <> $item_number ) {//购物车中的商品数量有变化了，求更新                    
                    $new_item_number = $new_cart_array[ $cart_info->cart_id ];
                    $new_item_number = $new_item_number > $stock ? $stock : $new_item_number; //如果大于库存则不合法   
                    if ( $cart_info->goods_member_level > 0 ) {
                        continue;
                    }
                    $this->updateCartGoodsNumberById( $cart_info->cart_id, $new_item_number );
                }
            }
            $cart_id_string = implode( ',', $cart_id_array );
            self::updateCartConfirmShow( $cart_id_string );
            return $cart_id_string;
        } else {
            throw new TmacClassException( '购物车为空' );
        }
    }

    /**
     * 更新显示的
     * @param type $cart_id_string
     */
    private function updateCartConfirmShow( $cart_id_string )
    {

        $entity_Cart_base = new entity_Cart_base();
        $entity_Cart_base->confirm_show = 0;

        $dao = dao_factory_base::getCartDao();
        $dao->setWhere( "uid={$this->uid}" );
        $dao->updateByWhere( $entity_Cart_base );

        $entity_Cart_base->confirm_show = 1;
        $where = $dao->getWhereInStatement( 'cart_id', $cart_id_string );
        $dao->setWhere( $where );
        $dao->updateByWhere( $entity_Cart_base );
        return true;
    }

    /**
     * 更新购物车的商品数量
     * @param type $cart_id
     * @param type $item_number
     * @return type
     */
    private function updateCartGoodsNumberById( $cart_id, $item_number )
    {
        $dao = dao_factory_base::getCartDao();

        $entity_Cart_base = new entity_Cart_base();
        $entity_Cart_base->item_number = $item_number;
        $entity_Cart_base->confirm_show = 1;

        $dao->setPk( $cart_id );
        return $dao->updateByPk( $entity_Cart_base );
    }

    private function getCartDefaultItemUid()
    {
        $dao = dao_factory_base::getCartDao();
        $dao->setField( 'item_uid' );
        $where = "uid={$this->uid}";
        $dao->setWhere( $where );
        $dao->setOrderby( 'cart_id DESC' );
        $dao->setLimit( 1 );
        $res = $dao->getListByWhere();
        if ( empty( $res ) ) {
            throw new TmacClassException( '购物车不能为空' );
        }
        return $res[ 0 ]->item_uid;
    }

    public function getCartListByIdString( $cart_id_string )
    {
        $dao = dao_factory_base::getCartDao();
        $dao->setField( 'cart_id,item_id,goods_id,goods_name,goods_image_id,item_price,item_number,goods_sku_id,goods_sku_name,item_uid,goods_uid,shop_name,shipping_fee,goods_type,is_integral' );
        if ( empty( $cart_id_string ) ) {
            if ( empty( $this->item_uid ) ) {
                $item_uid = $this->getCartDefaultItemUid();
            } else {
                $item_uid = $this->item_uid;
            }
            $where = "uid={$this->uid} AND item_uid={$item_uid} AND goods_uid={$this->goods_uid} AND confirm_show=1";
        } else {
            $where = $dao->getWhereInStatement( 'cart_id', $cart_id_string );
        }

        $dao->setWhere( $where );
        $dao->setOrderby( 'cart_id DESC' );
        $res = $dao->getListByWhere();
        $total_price = 0;
        $use_integral = false; //有没有积分商品
        $integral_value = 0; //可抵扣的积分
        $shipping_fee_array = $shipping_fee_goods_sale_array = array();
        $free_shipping_status = false; //邮费 有包邮按包邮算 没有包邮按最高算 之前邮费全是按最低的算的 现在邮费全是按最高的算的
        if ( $res ) {
            foreach ( $res as $key => $value ) {
                $res[ $key ]->goods_image_id = $this->getImage( $value->goods_image_id, '50', 'goods' );
                $total_price += $value->item_number * $value->item_price;
                $shipping_fee_array[] = $value->shipping_fee;
                if ( $value->goods_type == service_Goods_base::goods_type_sale ) {
                    $shipping_fee_goods_sale_array[] = $value->shipping_fee;
                }
                if ( $value->shipping_fee == 0 ) {
                    $free_shipping_status = true; //包邮
                }
                if ( $value->is_integral == service_Goods_base::is_integral_yes ) {
                    $use_integral = true;
                    $integral_value += $value->item_number * $value->item_price;
                }
            }
        }
        if ( $shipping_fee_array ) {
            $shipping_fee_max = max( $shipping_fee_array );
        } else {
            $shipping_fee_max = 0;
        }

        $use_available_integral = $this->available_integral >= $integral_value ? $integral_value : $this->available_integral;
        /**
          if ( $this->goods_uid == service_Member_base::yph_uid ) {
          $shipping_fee = $total_price > self::free_shipping_amount ? 0 : $shipping_fee_max;
          } else {
          $shipping_fee = $shipping_fee_max;
          } */
        $shipping_fee = empty( $shipping_fee_goods_sale_array ) ? max( $shipping_fee_array ) : max( $shipping_fee_goods_sale_array );

        if ( $free_shipping_status ) {//包邮邮费
            $shipping_fee = 0;
        }
        if ( $use_integral ) {
            $total_amount = ($total_price - $use_available_integral) + $shipping_fee;
        } else {
            $total_amount = $total_price + $shipping_fee;
        }
        $array = array(
            'cart_list' => $res,
            'shipping_fee' => $shipping_fee,
            'total_price' => $total_price,
            'order_payable_amount' => $total_price + $shipping_fee,
            'total_amount' => $total_amount,
            'use_integral' => $use_integral, //能不能使用积分 
            'use_available_integral' => $use_available_integral//能使用的积分 
        );
        return $array;
    }

    /**
     * 删除购物车
     * $this->uid;
     * $this->deleteCartByIdString($cart_id_string);
     * @param type $cart_id_string
     */
    public function deleteCartByIdString( $cart_id_string )
    {
        $dao = dao_factory_base::getCartDao();
        $dao->setField( 'cart_id,uid,session_id' );
        if ( empty( $this->uid ) ) {
            $session_id = session_id();
            $where = "session_id='{$session_id}'";
        } else {
            $where = "uid='{$this->uid}'";
        }
        $where .= ' AND ' . $dao->getWhereInStatement( 'cart_id', $cart_id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();
        if ( $res ) {
            $new_cart_id_array = array();
            foreach ( $res as $cart_info ) {
                $new_cart_id_array[] = $cart_info->cart_id;
            }
            $new_cart_id_string = implode( ',', $new_cart_id_array );
            $where = $dao->getWhereInStatement( 'cart_id', $new_cart_id_string );
            $dao->setWhere( $where );
            return $dao->deleteByWhere();
        }
        return true;
    }

    /**
     * $this->uid;
     * $this->getCartCount();
     */
    public function getCartCount()
    {
        $dao = dao_factory_base::getCartDao();
        if ( empty( $this->uid ) ) {
            $session_id = session_id();
            $where = "session_id='{$session_id}'";
        } else {
            $where = "uid={$this->uid}";
        }
        $dao->setWhere( $where );
        return $dao->getCountByWhere();
    }

    /**
     * 用户登录成功后，清理一下购物车中的重复商品
     * $this->uid;
     * $this->cleanRepeat();
     */
    public function cleanRepeat()
    {
        $dao = dao_factory_base::getCartDao();
        $where = "uid={$this->uid}";
        $dao->setWhere( $where );
        $dao->setField( 'cart_id,item_id,goods_id,goods_sku_id,item_number,goods_uid' );
        $res = $dao->getListByWhere();
        if ( $res ) {
            $result_array = array();
            $update_array = array();
            $delete_array = array();
            foreach ( $res as $value ) {
                $result_array[ $value->item_id ][ $value->goods_sku_id ][] = $value;
            }


            if ( $result_array ) {
                foreach ( $result_array as $member_cart_array ) {
                    foreach ( $member_cart_array as $value ) {
                        $cart_count = count( $value );
                        if ( $cart_count == 1 ) {
                            continue;
                        }

                        foreach ( $value as $key => $delete ) {
                            if ( $key == 0 ) {
                                $update_array[] = array(
                                    'cart_id' => $delete->cart_id,
                                    'count' => $cart_count
                                );
                                continue;
                            }
                            $delete_array[] = $delete->cart_id;
                        }
                    }
                }
            }

            if ( !empty( $update_array ) ) {
                foreach ( $update_array as $update ) {
                    $entity_Cart_base = new entity_Cart_base();
                    $entity_Cart_base->item_number = $update[ 'count' ];
                    $dao->setPk( $update[ 'cart_id' ] );
                    $dao->updateByPk( $entity_Cart_base );
                }
            }
            if ( !empty( $delete_array ) ) {
                $delete_stirng = implode( ',', $delete_array );
                $where = $dao->getWhereInStatement( 'cart_id', $delete_stirng );
                $dao->setWhere( $where );
                $dao->deleteByWhere();
            }
        }
        return true;
    }

    /**
     * 取买家的微信号
     */
    public function getMemberWeixinId()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $where = "uid={$this->uid}";
        $dao->setWhere( $where );
        $dao->setField( 'weixin_id' );
        $member_setting_info = $dao->getInfoByWhere();
        $weixin_id = '';
        if ( $member_setting_info ) {
            $weixin_id = $member_setting_info->weixin_id;
        }
        return $weixin_id;
    }

}
