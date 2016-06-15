<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Save_base extends Model
{

    const goods_price_need_shipping_fee = 80;

    protected $errorMessage;
    protected $goods_image_array;
    protected $item_cat_id_array;
    protected $goods_cat_id_array;
    protected $goods_sku_stock_array;
    protected $goods_spec_array;
    protected $goods_spec_value_spec_id_map; //每个商品规格值对应的 商品规格 ID 映射
    protected $goods_id;
    protected $item_id;
    protected $uid;
    protected $spec_value_map_array;
    protected $commission_fee;
    protected $commission_type;
    protected $commission_scale;

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setGoods_image_array( $goods_image_array )
    {
        $this->goods_image_array = $goods_image_array;
    }

    function setItem_cat_id_array( $item_cat_id_array )
    {
        $this->item_cat_id_array = $item_cat_id_array;
    }

    function setGoods_cat_id_array( $goods_cat_id_array )
    {
        $this->goods_cat_id_array = $goods_cat_id_array;
    }

    function setGoods_sku_stock_array( $goods_sku_stock_array )
    {
        $this->goods_sku_stock_array = $goods_sku_stock_array;
    }

    function setGoods_spec_array( $goods_spec_array )
    {
        $this->goods_spec_array = $goods_spec_array;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setGoods_id( $goods_id )
    {
        $this->goods_id = $goods_id;
    }

    function setItem_id( $item_id )
    {
        $this->item_id = $item_id;
    }

    /**
     * todo 要保存 涉及到的表
     * goods
     * goods_category_map
     * goods_sku
     * goods_spec
     * goods_image
     */
    public function createGoods( entity_Item_base $entity_Item_base, entity_Goods_base $entity_Goods_base )
    {
        $item_dao = dao_factory_base::getItemDao();
        $goods_dao = dao_factory_base::getGoodsDao();


        $repeat_item_id = $this->checkGoodsRepeat( $entity_Goods_base->uid, $entity_Goods_base->goods_name );
        if ( $repeat_item_id ) {
            $this->errorMessage = '商品名:"' . $entity_Goods_base->goods_name . '"已经存在了重复';
            return FALSE;
        }

        //检测goods_source重复
        $goods_source_repeat = $this->checkGoodsSourceRepeat( $entity_Goods_base->goods_source, $entity_Goods_base->goods_source_id );
        if ( $goods_source_repeat === false ) {
            $this->errorMessage = '重复';
            return false;
        }
        $item_dao->getDb()->startTrans();
        $entity_Goods_base->goods_time = $this->now;
        if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
            $sku_price_stock = $this->getGoodsSkuMinPriceAndStock();
            //如果有商品规格，goods表中的价格是sku中最小的
            $entity_Goods_base->goods_price = $sku_price_stock[ 'price' ];
            $entity_Goods_base->goods_stock = $sku_price_stock[ 'stock' ];

            if ( $entity_Goods_base->goods_price <= self::goods_price_need_shipping_fee && empty( $entity_Goods_base->shipping_fee ) && $entity_Goods_base->goods_source == service_Goods_base::goods_source_jd ) {
                $entity_Goods_base->shipping_fee = 10;
            }
            $entity_Item_base->item_price = $entity_Goods_base->goods_price;
            $entity_Item_base->item_stock = $entity_Goods_base->goods_stock;
        }
        //处理原价/实际销价
        $this->handelPromotePriceDifference( $entity_Goods_base );
        //处理佣金
        $this->handleGoodsCommissionFee( $entity_Goods_base );
        //goods表保存
        $goods_id = $goods_dao->insert( $entity_Goods_base );
        $entity_Item_base->goods_uid = $entity_Goods_base->uid;
        $entity_Item_base->goods_id = $goods_id;
        $entity_Item_base->commission_fee = $entity_Goods_base->commission_fee;
        $entity_Item_base->shipping_fee = $entity_Goods_base->shipping_fee;
        $entity_Item_base->goods_type = $entity_Goods_base->goods_type;
        $entity_Item_base->is_integral = $entity_Goods_base->is_integral;

        if ( $entity_Item_base->uid == $entity_Item_base->goods_uid ) {
            $entity_Item_base->is_self = 1;
        } else {
            $entity_Item_base->is_self = 0;
        }
        $item_id = $item_dao->insert( $entity_Item_base );
        //goods_image 商品图片表
        $this->_saveGoodsImage( $goods_id );
        if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
            //goods_spec 商品规格表
            $this->_saveGoodsSpec( $goods_id );
            //goods_sku 商品sku表
            $this->_saveGoodsSku( $goods_id );
        }
        //item_category_map表
        $this->_saveItemCategoryMap( $item_id, $goods_id );
        //goods_category_map表
        $this->_saveGoodsCategoryMap( $goods_id );

        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $item_dao->getDb()->isSuccess() ) {
            $item_dao->getDb()->commit();
            return $goods_id;
        } else {
            $item_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 处理商品的佣金
     */
    protected function handleGoodsCommissionFee( entity_Goods_base $entity_Goods_base )
    {
        $this->commission_fee = $entity_Goods_base->commission_fee;
        $this->commission_type = $entity_Goods_base->commission_type;
        $this->commission_scale = $entity_Goods_base->commission_scale > 1 ? 1 : round( $entity_Goods_base->commission_scale, 2 );
        if ( !empty( $entity_Goods_base->commission_type ) && empty( $entity_Goods_base->commission_scale ) ) {
            return true;
        }
        if ( $entity_Goods_base->commission_type == service_Goods_base::commission_type_scale ) {
            $entity_Goods_base->commission_fee = round( $entity_Goods_base->goods_price * $entity_Goods_base->commission_scale, 1 );
        }
        if ( $entity_Goods_base->commission_fee >= $entity_Goods_base->goods_price ) {
            $entity_Goods_base->commission_fee = $entity_Goods_base->goods_price;
        }
        //处理goods_desc内容过滤手机号和超链接 这个在显示的页面替换
        //$entity_Goods_base->goods_desc = preg_replace( '/(1\d{2})\d{4}(\d{4})/', '${1}*****${2}', $entity_Goods_base->goods_desc );
        $entity_Goods_base->goods_keywords = $entity_Goods_base->goods_desc;
        //处理品牌
        if ( !empty( $entity_Goods_base->brand_id ) ) {
            $entity_Goods_base->brand_name = $this->getBrandNameById( $entity_Goods_base->brand_id );
        }
        return $entity_Goods_base;
    }

    /**
     * 检测商品是否存在
     * @param type $uid
     * @param type $goods_name
     * @return boolean
     */
    protected function checkGoodsSourceRepeat( $goods_source, $goods_source_id, $goods_id = 0 )
    {
        if ( empty( $goods_source ) || empty( $goods_source_id ) ) {
            return true;
        }
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id' );
        $dao->setWhere( "goods_source_id={$goods_source_id} AND goods_source={$goods_source}" );
        $goodsInfo = $dao->getInfoByWhere();
        if ( $goodsInfo && $goodsInfo->goods_id <> $goods_id ) {
            return FALSE;
        }
        return true;
    }

    /**
     * 检测商品是否存在
     * 创建商品
     * @param type $uid
     * @param type $goods_name
     * @return boolean
     */
    public function checkGoodsRepeat( $uid, $goods_name )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id' );
        $dao->setWhere( "uid={$uid} AND goods_name='{$goods_name}' AND is_delete=0" );
        $goodsInfo = $dao->getInfoByWhere();
        if ( $goodsInfo ) {
            $item_dao = dao_factory_base::getItemDao();
            $item_dao->setField( 'item_id' );
            $item_dao->setWhere( "uid={$uid} AND goods_id={$goodsInfo->goods_id}" );
            $item_info = $item_dao->getInfoByWhere();
            return $item_info->item_id;
        }
        return false;
    }

    /**
     * 检测商品是否存在
     * 创建商品
     * @param type $uid
     * @param type $goods_name
     * @return boolean
     */
    public function checkModifyGoodsRepeat( $uid, $goods_id, $goods_name )
    {
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id' );
        $dao->setWhere( "uid={$uid} AND goods_name='{$goods_name}' AND is_delete=0" );
        $goodsInfo = $dao->getInfoByWhere();
        if ( $goodsInfo && $goodsInfo->goods_id <> $goods_id ) {
            return true;
        }
        return false;
    }

    public function modifyGoods( entity_Item_base $entity_Item_base, entity_Goods_base $entity_Goods_base )
    {
        $item_dao = dao_factory_base::getItemDao();
        $goods_dao = dao_factory_base::getGoodsDao();

        //get item info 和 goods info 判断是不是本人发的如果是的话开放修改
        $item_dao->setPk( $this->item_id );
        $item_dao->setField( 'goods_id,uid' );
        $item_info = $item_dao->getInfoByPk();

        $goods_dao->setPk( $item_info->goods_id );
        $goods_dao->setField( 'uid' );
        $goods_info = $goods_dao->getInfoByPk();

        $this->goods_id = $item_info->goods_id;

        if ( $goods_info->uid == $item_info->uid ) { //修改自己发布的
            $goods_priview = true;
            if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
                //如果有商品规格，goods表中的价格是sku中最小的                
                $sku_price_stock = $this->getGoodsSkuMinPriceAndStock();
                //如果有商品规格，goods表中的价格是sku中最小的
                $entity_Goods_base->goods_price = $sku_price_stock[ 'price' ];
                $entity_Goods_base->goods_stock = $sku_price_stock[ 'stock' ];
            }
        } else { //分销的商品，下面没有修改goods,goods_image,goods_spec,goods_sku的权限
            $goods_priview = false;
        }
        //处理原价/实际销价
        $this->handelPromotePriceDifference( $entity_Goods_base );
        //处理佣金
        $this->handleGoodsCommissionFee( $entity_Goods_base );
        $item_dao->getDb()->startTrans();

        $entity_Item_base->goods_id = $this->goods_id;
        $item_dao->updateByPk( $entity_Item_base );

        if ( $goods_priview ) {//如果是自己发布的商品
            $goods_dao->setPk( $this->goods_id );
            $goods_dao->updateByPk( $entity_Goods_base );
            //更新item中分销的价格
            $entity_Item = new entity_Item_base();
            $entity_Item->item_price = $entity_Goods_base->goods_price;
            $entity_Item->item_stock = $entity_Goods_base->goods_stock;
            $entity_Item->outer_code = $entity_Goods_base->outer_code;
            $entity_Item->shipping_fee = $entity_Goods_base->shipping_fee;
            $entity_Item->commission_fee = $entity_Goods_base->commission_fee;
            $item_dao->setWhere( "goods_id={$this->goods_id}" );
            $item_dao->updateByWhere( $entity_Item );

            //goods_image 商品图片表
            $this->_saveGoodsImage( $this->goods_id );
            if ( !empty( $this->goods_spec_array ) && !empty( $this->goods_sku_stock_array ) ) {
                //goods_spec 商品规格表
                $this->_saveGoodsSpec( $this->goods_id );
                //goods_sku 商品sku表
                $this->_saveGoodsSku( $this->goods_id );
            }
        }
        //没有商品规格的时候 更新购物车中的商品价格
        if ( empty( $this->goods_spec_array ) && empty( $this->goods_sku_stock_array ) ) {
            $cart_dao = dao_factory_base::getCartDao();

            $entity_Cart_base = new entity_Cart_base();
            $entity_Cart_base->item_price = $entity_Goods_base->goods_price;
            $entity_Cart_base->outer_code = $entity_Goods_base->outer_code;
            $where = "goods_id={$this->goods_id}";
            $cart_dao->setWhere( $where );
            $cart_dao->updateByWhere( $entity_Cart_base );
        }

        //item_category_map表
        $this->_saveItemCategoryMap( $this->item_id, $this->goods_id );
        //goods_category_map表
        $this->_saveGoodsCategoryMap( $this->goods_id );

        /**
         * if ( $spec_value_map_dao->getDb()->isSuccess() && $spec_value_map_dao->getDb()->getNumRows() > 0 ) {
         */
        if ( $item_dao->getDb()->isSuccess() ) {
            $item_dao->getDb()->commit();
            return $this->item_id;
        } else {
            $item_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 保存goods_image
     */
    public function _saveGoodsImage( $goods_id )
    {
        //先取出goods_id所有的，然后编历把存在的，且删除状态为1的改状态
        $this->goods_image_array = array_unique( $this->goods_image_array );
        if ( empty( $this->goods_image_array ) ) {
            return true;
        }
        $goods_image_dao = dao_factory_base::getGoodsImageDao();
        if ( empty( $this->goods_id ) ) {
            foreach ( $this->goods_image_array AS $goods_image_id ) {
                $entity_GoodsImage_base = new entity_GoodsImage_base();
                $entity_GoodsImage_base->uid = $this->uid;
                $entity_GoodsImage_base->goods_id = $goods_id;
                $entity_GoodsImage_base->goods_image_id = $goods_image_id;
                $entity_GoodsImage_base->goods_photo_time = $this->now;
                $entity_GoodsImage_base->is_delete = 0;
                $entity_GoodsImage_base->goods_image_sort = 0;
                $goods_image_dao->insert( $entity_GoodsImage_base );
            }
        } else {
            $goods_image_dao->setField( 'id,goods_image_id,is_delete' );
            $goods_image_dao->setWhere( "goods_id={$this->goods_id}" );
            $res = $goods_image_dao->getListByWhere();

            $goods_image_delete_array = $goods_image_update_array = array();
            if ( $res ) {
                foreach ( $res as $goods_image_object ) {
                    if ( $goods_image_object->is_delete == 0 ) {
                        //查找到并删除$goods_image_array中的
                        $goods_image_delete_array[] = $goods_image_object->goods_image_id;
                    } else {
                        //这里面是 已经存在的。修改删除状态就行了
                        $goods_image_update_array[ $goods_image_object->goods_image_id ] = $goods_image_object->id;
                    }
                }
            }
            foreach ( $this->goods_image_array AS $goods_image_id ) {
                $goods_image_id = stripslashes( $goods_image_id );
                if ( in_array( $goods_image_id, $goods_image_delete_array ) ) {
                    continue;
                }
                if ( !empty( $goods_image_update_array[ $goods_image_id ] ) ) {
                    //update 状态                    
                    $entity_GoodsImage_base = new entity_GoodsImage_base();
                    $entity_GoodsImage_base->is_delete = 0;

                    $goods_image_dao->setPk( $goods_image_update_array[ $goods_image_id ] );
                    $goods_image_dao->updateByPk( $entity_GoodsImage_base );
                    continue;
                }

                $entity_GoodsImage_base = new entity_GoodsImage_base();
                $entity_GoodsImage_base->uid = $this->uid;
                $entity_GoodsImage_base->goods_id = $this->goods_id;
                $entity_GoodsImage_base->goods_image_id = $goods_image_id;
                $entity_GoodsImage_base->goods_photo_time = $this->now;
                $entity_GoodsImage_base->is_delete = 0;
                $entity_GoodsImage_base->goods_image_sort = 0;
                $goods_image_dao->insert( $entity_GoodsImage_base );
            }
        }
        return true;
    }

    /**
     * 保存 商品sku
     */
    public function _saveGoodsSku( $goods_id )
    {
        $goods_sku_dao = dao_factory_base::getGoodsSkuDao();
        $goods_model = new service_goods_Manage_manage();
        $this->spec_value_map_array = $goods_model->getUserSpecValueArray( $this->uid, $this->goods_spec_array );
        if ( empty( $this->goods_id ) ) {
            foreach ( $this->goods_sku_stock_array AS $goods_sku => $goods_sku_object ) {
                if ( !isset( $goods_sku ) || empty( $goods_sku ) ) {
                    continue;
                }
                $goods_sku_obj = $this->_getGoodsSkuJsonBySku( $goods_sku );
                if ( $goods_sku_obj == false ) {
                    continue;
                }
                $entity_GoodsSku_base = new entity_GoodsSku_base();
                $entity_GoodsSku_base->goods_id = $goods_id;

                $entity_GoodsSku_base->goods_sku = $goods_sku_obj[ 'goods_sku' ];
                $entity_GoodsSku_base->goods_sku_json = $goods_sku_obj[ 'goods_sku_json' ];

                $entity_GoodsSku_base->price = $goods_sku_object[ 'sku_price' ];
                $entity_GoodsSku_base->stock = $goods_sku_object[ 'sku_stock' ];
                $entity_GoodsSku_base->outer_code = $goods_sku_object[ 'sku_code' ];
                $sku_commission_fee = empty( $goods_sku_object[ 'sku_commission_fee' ] ) ? 0 : $goods_sku_object[ 'sku_commission_fee' ];
                $entity_GoodsSku_base->commission_fee = $this->getGoodsSkuCommissionFee( $goods_sku_object[ 'sku_price' ], $sku_commission_fee );
                $entity_GoodsSku_base->create_time = $this->now;
                $entity_GoodsSku_base->modify_time = $this->now;
                $entity_GoodsSku_base->is_delete = 0;
                $goods_sku_dao->insert( $entity_GoodsSku_base );
            }
        } else {
            //先更新所有的为delete=1
            $where = "goods_id={$this->goods_id}";
            $entity_GoodsSku_base = new entity_GoodsSku_base();
            $entity_GoodsSku_base->is_delete = 1;

            $goods_sku_dao->setWhere( $where );
            $goods_sku_dao->updateByWhere( $entity_GoodsSku_base );
            //取出所有的goods_spec_array
            $goods_sku_dao->setField( 'goods_sku_id,goods_id,goods_sku,is_delete' );
            $res = $goods_sku_dao->getListByWhere();

            $goods_sku_update_array = array();

            if ( $res ) {
                foreach ( $res AS $goods_sku_object ) {
                    $goods_sku_update_array[ $goods_sku_object->goods_sku ] = $goods_sku_object->goods_sku_id;
                }
            }
            //然后再把已经存在的delete=0
            foreach ( $this->goods_sku_stock_array AS $goods_sku => $goods_sku_object ) {
                if ( !isset( $goods_sku ) || empty( $goods_sku ) ) {
                    continue;
                }
                $goods_sku_obj = $this->_getGoodsSkuJsonBySku( $goods_sku );
                if ( $goods_sku_obj == false ) {
                    continue;
                }
                $goods_sku = $goods_sku_obj[ 'goods_sku' ];

                if ( !empty( $goods_sku_update_array[ $goods_sku ] ) ) {
                    //update 状态                    
                    $entity_GoodsSku_base = new entity_GoodsSku_base();

                    $entity_GoodsSku_base->price = $goods_sku_object[ 'sku_price' ];
                    $entity_GoodsSku_base->stock = $goods_sku_object[ 'sku_stock' ];
                    $entity_GoodsSku_base->outer_code = $goods_sku_object[ 'sku_code' ];
                    $sku_commission_fee = empty( $goods_sku_object[ 'sku_commission_fee' ] ) ? 0 : $goods_sku_object[ 'sku_commission_fee' ];
                    $entity_GoodsSku_base->commission_fee = $this->getGoodsSkuCommissionFee( $goods_sku_object[ 'sku_price' ], $sku_commission_fee );
                    $entity_GoodsSku_base->goods_sku = $goods_sku_obj[ 'goods_sku' ];
                    $entity_GoodsSku_base->goods_sku_json = $goods_sku_obj[ 'goods_sku_json' ];
                    $entity_GoodsSku_base->modify_time = $this->now;
                    $entity_GoodsSku_base->is_delete = 0;

                    $goods_sku_dao->setPk( $goods_sku_update_array[ $goods_sku ] );
                    $goods_sku_dao->updateByPk( $entity_GoodsSku_base );
                    //更新购物车中的价格
                    self::updateCartPriceByGoodsSkuId( $goods_sku_update_array[ $goods_sku ], $goods_sku_object[ 'sku_price' ] );
                    continue;
                }
                //插入不存在的                
                $entity_GoodsSku_base = new entity_GoodsSku_base();
                $entity_GoodsSku_base->goods_id = $this->goods_id;

                $entity_GoodsSku_base->goods_sku = $goods_sku_obj[ 'goods_sku' ];
                $entity_GoodsSku_base->goods_sku_json = $goods_sku_obj[ 'goods_sku_json' ];

                $entity_GoodsSku_base->price = $goods_sku_object[ 'sku_price' ];
                $entity_GoodsSku_base->stock = $goods_sku_object[ 'sku_stock' ];
                $entity_GoodsSku_base->outer_code = $goods_sku_object[ 'sku_code' ];
                $sku_commission_fee = empty( $goods_sku_object[ 'sku_commission_fee' ] ) ? 0 : $goods_sku_object[ 'sku_commission_fee' ];
                $entity_GoodsSku_base->commission_fee = $this->getGoodsSkuCommissionFee( $goods_sku_object[ 'sku_price' ], $sku_commission_fee );
                $entity_GoodsSku_base->create_time = $this->now;
                $entity_GoodsSku_base->modify_time = $this->now;
                $entity_GoodsSku_base->is_delete = 0;
                $goods_sku_dao->insert( $entity_GoodsSku_base );
            }
        }
    }

    /**
     * 取sku的佣金
     * @param type $price
     */
    private function getGoodsSkuCommissionFee( $price, $sku_commission_fee = 0 )
    {
        if ( !empty( $sku_commission_fee ) ) {
            return ( $sku_commission_fee >= $price ) ? $price : $sku_commission_fee;
        }
        if ( empty( $this->commission_type ) ) {//固定金额的佣金
            return $this->commission_fee;
        }
        if ( !empty( $this->commission_type ) && empty( $this->commission_scale ) ) {//固定金额的佣金
            return $this->commission_fee;
        }
        //佣金比例
        $commission_fee = round( $price * $this->commission_scale, 1 );
        if ( $commission_fee >= $price ) {
            $commission_fee = $price;
        }
        return $commission_fee;
    }

    /**
     * 更新购物车中商品sku的价格
     * @param type $goods_id
     * @param type $goods_sku_id
     */
    private function updateCartPriceByGoodsSkuId( $goods_sku_id, $price )
    {
        $cart_dao = dao_factory_base::getCartDao();

        $entity_Cart_base = new entity_Cart_base();
        $entity_Cart_base->item_price = $price;
        $where = "goods_id={$this->goods_id} AND goods_sku_id={$goods_sku_id}";
        $cart_dao->setWhere( $where );
        return $cart_dao->updateByWhere( $entity_Cart_base );
    }

    private function _getGoodsSkuJsonBySku( $goods_sku )
    {
        $spec_value_map_array = $this->spec_value_map_array;
        $goods_sku_repeat_array = explode( '-', $goods_sku );
        $goods_sku_array = array_filter( $goods_sku_repeat_array );

        $goods_sku_arr = $spec_value_id_array = array();
        if ( $goods_sku_array ) {
            foreach ( $goods_sku_array AS $spec_value_id ) {
                //如果新增加的商品规格值，转换成 商品规格ID，系统会自动排重
                if ( is_numeric( $spec_value_id ) == false ) {
                    $spec_value_name = strtoupper( $spec_value_id );
                    $spec_value_name_spec_id = $this->goods_spec_value_spec_id_map[ $spec_value_name ];
                    if ( empty( $spec_value_map_array[ 'key_spec_value_name' ][ $spec_value_name ][ $spec_value_name_spec_id ] ) ) {
                        continue;
                    }
                    $spec_value_id = $spec_value_map_array[ 'key_spec_value_name' ][ $spec_value_name ][ $spec_value_name_spec_id ];
                } else {
                    if ( empty( $spec_value_map_array[ 'key_spec_value_id' ][ $spec_value_id ] ) ) {
                        continue;
                    }
                    $spec_value_name = $spec_value_map_array[ 'key_spec_value_id' ][ $spec_value_id ];
                }
                $goods_sku_arr[] = array( 'spec_value_id' => $spec_value_id, 'spec_value_name' => $spec_value_name );
                $spec_value_id_array[] = $spec_value_id;
            }
            //goods_sku中的spec_value_id做一个小到大排序
            sort( $spec_value_id_array );
        }
        if ( empty( $spec_value_id_array ) ) {
            return false;
        }
        $goods_sku_string = implode( '-', $spec_value_id_array );
        return array(
            'goods_sku' => $goods_sku_string,
            'goods_sku_json' => serialize( $goods_sku_arr )
        );
    }

    /**
     * 经销商在发布商品的规格时 取spec_value_id,会自动排重    
     * @param service_Spec_manage $spec_model
     * @param type $spec_id
     * @param type $spec_value_name
     * @return boolean
     */
    private function _getGoodsSpecValueIdBySpecValueName( $spec_model, $spec_id, $spec_value_name )
    {
        $spec_model instanceof service_Spec_manage;

        $spec_model->setUid( $this->uid );
        $res = $spec_model->createSpecValue( $spec_id, $spec_value_name );
        if ( $res ) {
            return $res;
        } else {
            throw new TmacClassException( $spec_model->getErrorMessage() );
        }
    }

    private function _getSpecValueIdBySpecValueName()
    {
        $spec_value_map_dao = dao_factory_base::getSpecValueMapDao();
        $where = "uid={$this->uid} AND spec_id={$spec_id} AND spec_value_name='{$spec_value_name}'";
        $spec_value_map_dao->setWhere( $where );
        $spec_value_map_dao->setField( 'spec_value_id,spec_value_name,spec_id' );
        $spec_value_info = $spec_value_map_dao->getInfoByWhere();
        //检测wsw_spec_value_map是否存在
        if ( $spec_value_info == false ) {
            
        }
    }

    /**
     * 保存 商品规格属性值
     */
    public function _saveGoodsSpec( $goods_id )
    {
        $goods_spec_dao = dao_factory_base::getGoodsSpecDao();

        $goods_model = new service_goods_Manage_manage();
        $spec_tree = $goods_model->getUserSpecObjectArray( $this->uid );

        $spec_model = new service_Spec_manage();
        if ( empty( $this->goods_id ) ) {
            foreach ( $this->goods_spec_array AS $spec_id => $spec_value_array ) {
                foreach ( $spec_value_array AS $spec_value_id ) {
                    if ( empty( $spec_value_id ) ) {
                        continue;
                    }
                    //如果新增加的商品规格值，转换成 商品规格ID，系统会自动排重
                    if ( is_numeric( $spec_value_id ) == false ) {
                        $spec_value_id = $this->filterSeparator( $spec_value_id );
                        $spec_value_info = self::_getGoodsSpecValueIdBySpecValueName( $spec_model, $spec_id, $spec_value_id );
                        $spec_value_id = $spec_value_info->spec_value_id;
                        $spec_value_name = $spec_value_info->spec_value_name;
                        //$spec_tree = $goods_model->getUserSpecObjectArray( $this->uid );
                    } else {
                        if ( empty( $spec_tree[ $spec_id ]->value_list[ $spec_value_id ] ) ) {
                            continue;
                        }
                        $spec_value_name = $spec_tree[ $spec_id ]->value_list[ $spec_value_id ]->spec_value_name;
                    }
                    $spec_value_name = strtoupper( $spec_value_name );
                    $this->goods_spec_value_spec_id_map[ $spec_value_name ] = $spec_id;
                    $entity_GoodsSpec_base = new entity_GoodsSpec_base();
                    $entity_GoodsSpec_base->goods_id = $goods_id;
                    $entity_GoodsSpec_base->spec_id = $spec_id;
                    $entity_GoodsSpec_base->spec_name = $spec_tree[ $spec_id ]->spec_name;
                    $entity_GoodsSpec_base->spec_value_id = $spec_value_id;
                    $entity_GoodsSpec_base->spec_value_name = $spec_value_name;
                    $entity_GoodsSpec_base->is_delete = 0;
                    $goods_spec_dao->insert( $entity_GoodsSpec_base );
                }
            }
        } else {
            //先更新所有的为delete=1
            $where = "goods_id={$this->goods_id}";
            $entity_GoodsSpec_base = new entity_GoodsSpec_base();
            $entity_GoodsSpec_base->is_delete = 1;

            $goods_spec_dao->setWhere( $where );
            $goods_spec_dao->updateByWhere( $entity_GoodsSpec_base );
            //取出所有的goods_spec_array
            $goods_spec_dao->setField( 'goods_spec_id,goods_id,spec_id,spec_value_id,is_delete' );
            $res = $goods_spec_dao->getListByWhere();

            $goods_spec_update_array = array();

            if ( $res ) {
                foreach ( $res AS $goods_spec_object ) {
                    $goods_spec_update_array[ $goods_spec_object->spec_value_id ] = $goods_spec_object->goods_spec_id;
                }
            }
            //然后再把已经存在的delete=0
            foreach ( $this->goods_spec_array AS $spec_id => $spec_value_array ) {
                foreach ( $spec_value_array AS $spec_value_id ) {
                    if ( empty( $spec_value_id ) ) {
                        continue;
                    }
                    //如果新增加的商品规格值，转换成 商品规格ID，系统会自动排重
                    if ( is_numeric( $spec_value_id ) == false ) {
                        $spec_value_id = $this->filterSeparator( $spec_value_id );
                        $spec_value_info = self::_getGoodsSpecValueIdBySpecValueName( $spec_model, $spec_id, $spec_value_id );
                        $spec_value_id = $spec_value_info->spec_value_id;
                        $spec_value_name = $spec_value_info->spec_value_name;
                        //$spec_tree = $goods_model->getUserSpecObjectArray( $this->uid );
                    } else {
                        if ( empty( $spec_tree[ $spec_id ]->value_list[ $spec_value_id ] ) ) {
                            continue;
                        }
                        $spec_value_name = $spec_tree[ $spec_id ]->value_list[ $spec_value_id ]->spec_value_name;
                    }

                    $spec_value_name = strtoupper( $spec_value_name );
                    if ( !empty( $goods_spec_update_array[ $spec_value_id ] ) ) {
                        //update 状态                    
                        $entity_GoodsSpec_base = new entity_GoodsSpec_base();
                        $entity_GoodsSpec_base->is_delete = 0;

                        $goods_spec_dao->setPk( $goods_spec_update_array[ $spec_value_id ] );
                        $goods_spec_dao->updateByPk( $entity_GoodsSpec_base );
                        $this->goods_spec_value_spec_id_map[ $spec_value_name ] = $spec_id;
                        continue;
                    }
                    $this->goods_spec_value_spec_id_map[ $spec_value_name ] = $spec_id;
                    //插入不存在的
                    $entity_GoodsSpec_base = new entity_GoodsSpec_base();
                    $entity_GoodsSpec_base->goods_id = $this->goods_id;
                    $entity_GoodsSpec_base->spec_id = $spec_id;
                    $entity_GoodsSpec_base->spec_name = $spec_tree[ $spec_id ]->spec_name;
                    $entity_GoodsSpec_base->spec_value_id = $spec_value_id;
                    $entity_GoodsSpec_base->spec_value_name = $spec_value_name;
                    $entity_GoodsSpec_base->is_delete = 0;
                    $goods_spec_dao->insert( $entity_GoodsSpec_base );
                }
            }
        }
        return true;
    }

    /**
     * 保存 商品分类映射
     */
    public function _saveItemCategoryMap( $item_id, $goods_id )
    {
        if ( empty( $this->item_cat_id_array ) ) {
            return true;
        }
        $item_category_map_dao = dao_factory_base::getItemCategoryMapDao();

        if ( empty( $this->item_id ) ) {
            foreach ( $this->item_cat_id_array AS $item_cat_id ) {
                $entity_ItemCategoryMap_base = new entity_ItemCategoryMap_base();
                $entity_ItemCategoryMap_base->item_id = $item_id;
                $entity_ItemCategoryMap_base->goods_id = $goods_id;
                $entity_ItemCategoryMap_base->item_cat_id = $item_cat_id;
                $entity_ItemCategoryMap_base->is_delete = 0;

                $item_category_map_dao->insert( $entity_ItemCategoryMap_base );
                //更新分类商品uko
                $this->updateCategoryItemCount( $item_cat_id );
            }
        } else {
            //先更新所有的为delete=1
            $where = "item_id={$this->item_id}";
            $entity_ItemCategoryMap_base = new entity_ItemCategoryMap_base();
            $entity_ItemCategoryMap_base->is_delete = 1;

            $item_category_map_dao->setWhere( $where );
            $item_category_map_dao->updateByWhere( $entity_ItemCategoryMap_base );
            //取出所有的goods_spec_array
            $item_category_map_dao->setField( 'item_cat_map_id,item_id,goods_id,item_cat_id,is_delete' );
            $res = $item_category_map_dao->getListByWhere();

            $goods_category_update_array = array();
            if ( $res ) {
                foreach ( $res AS $goods_category_object ) {
                    $goods_category_update_array[ $goods_category_object->item_cat_id ] = $goods_category_object->item_cat_map_id;
                }
            }
            //然后再把已经存在的delete=0

            foreach ( $this->item_cat_id_array AS $item_cat_id ) {
                if ( !empty( $goods_category_update_array[ $item_cat_id ] ) ) {
                    //update 状态                    
                    $entity_ItemCategoryMap_base = new entity_ItemCategoryMap_base();
                    $entity_ItemCategoryMap_base->is_delete = 0;

                    $item_category_map_dao->setPk( $goods_category_update_array[ $item_cat_id ] );
                    $item_category_map_dao->updateByPk( $entity_ItemCategoryMap_base );
                    //更新分类商品uko
                    $this->updateCategoryItemCount( $item_cat_id );
                    continue;
                }
                $entity_ItemCategoryMap_base = new entity_ItemCategoryMap_base();
                $entity_ItemCategoryMap_base->item_id = $this->item_id;
                $entity_ItemCategoryMap_base->goods_id = $goods_id;
                $entity_ItemCategoryMap_base->item_cat_id = $item_cat_id;
                $entity_ItemCategoryMap_base->is_delete = 0;

                $item_category_map_dao->insert( $entity_ItemCategoryMap_base );
                //更新分类商品uko
                $this->updateCategoryItemCount( $item_cat_id );
            }
        }
        return true;
    }

    /**
     * 保存 商品分类映射
     */
    public function _saveGoodsCategoryMap( $goods_id )
    {
        if ( empty( $this->goods_cat_id_array ) || count( $this->goods_cat_id_array ) == 0 ) {
            return true;
        }
        $goods_category_map_dao = dao_factory_base::getGoodsCategoryMapDao();

        if ( empty( $this->goods_id ) ) {
            foreach ( $this->goods_cat_id_array AS $goods_cat_id ) {
                $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
                $entity_GoodsCategoryMap_base->goods_id = $goods_id;
                $entity_GoodsCategoryMap_base->goods_cat_id = $goods_cat_id;
                $entity_GoodsCategoryMap_base->is_delete = 0;

                $goods_category_map_dao->insert( $entity_GoodsCategoryMap_base );
                //更新分类商品uko
                $this->updateCategoryGoodsCount( $goods_cat_id );
            }
        } else {
            //先更新所有的为delete=1
            $where = "goods_id={$this->goods_id}";
            $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
            $entity_GoodsCategoryMap_base->is_delete = 1;

            $goods_category_map_dao->setWhere( $where );
            $goods_category_map_dao->updateByWhere( $entity_GoodsCategoryMap_base );
            //取出所有的goods_spec_array
            $goods_category_map_dao->setField( 'goods_cat_map_id,goods_id,goods_cat_id,is_delete' );
            $res = $goods_category_map_dao->getListByWhere();

            $goods_category_update_array = array();
            if ( $res ) {
                foreach ( $res AS $goods_category_object ) {
                    $goods_category_update_array[ $goods_category_object->goods_cat_id ] = $goods_category_object->goods_cat_map_id;
                }
            }
            //然后再把已经存在的delete=0

            foreach ( $this->goods_cat_id_array AS $goods_cat_id ) {
                if ( !empty( $goods_category_update_array[ $goods_cat_id ] ) ) {
                    //update 状态                    
                    $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
                    $entity_GoodsCategoryMap_base->is_delete = 0;

                    $goods_category_map_dao->setPk( $goods_category_update_array[ $goods_cat_id ] );
                    $goods_category_map_dao->updateByPk( $entity_GoodsCategoryMap_base );
                    //更新分类商品uko
                    $this->updateCategoryGoodsCount( $goods_cat_id );
                    continue;
                }
                $entity_GoodsCategoryMap_base = new entity_GoodsCategoryMap_base();
                $entity_GoodsCategoryMap_base->goods_id = $goods_id;
                $entity_GoodsCategoryMap_base->goods_cat_id = $goods_cat_id;
                $entity_GoodsCategoryMap_base->is_delete = 0;

                $goods_category_map_dao->insert( $entity_GoodsCategoryMap_base );
                //更新分类商品uko
                $this->updateCategoryGoodsCount( $goods_cat_id );
            }
        }
        return true;
    }

    /**
     * 取sku中最便宜的价钱
     */
    public function getGoodsSkuMinPriceAndStock()
    {
        $price_array = array();
        $stock = 0;
        foreach ( $this->goods_sku_stock_array AS $goods_sku_object ) {
            $price_array[] = $goods_sku_object[ 'sku_price' ];
            $stock+= $goods_sku_object[ 'sku_stock' ];
        }
        $array = array(
            'price' => min( $price_array ),
            'stock' => $stock
        );
        return $array;
    }

    /**
     * 更新分类的商品总数
     * @param type $item_cat_id
     */
    private function updateCategoryItemCount( $item_cat_id )
    {
        $item_category_dao = dao_factory_base::getItemCategoryDao();
        $item_category_map_dao = dao_factory_base::getItemCategoryMapDao();

        $where = "item_cat_id={$item_cat_id} AND is_delete=0";
        $item_category_map_dao->setWhere( $where );
        $item_count = $item_category_map_dao->getCountByWhere();

        $entity_ItemCategory_base = new entity_ItemCategory_base();
        $entity_ItemCategory_base->item_count = $item_count;
        $item_category_dao->setPk( $item_cat_id );
        return $item_category_dao->updateByPk( $entity_ItemCategory_base );
    }

    /**
     * 更新分类的商品总数
     * @param type $item_cat_id
     */
    private function updateCategoryGoodsCount( $goods_cat_id )
    {
        $goods_category_dao = dao_factory_base::getGoodsCategoryDao();
        $goods_category_map_dao = dao_factory_base::getGoodsCategoryMapDao();

        $where = "goods_cat_id={$goods_cat_id} AND is_delete=0";
        $goods_category_map_dao->setWhere( $where );
        $goods_count = $goods_category_map_dao->getCountByWhere();

        $entity_GoodsCategory_base = new entity_GoodsCategory_base();
        $entity_GoodsCategory_base->goods_count = $goods_count;
        $goods_category_dao->setPk( $goods_cat_id );
        return $goods_category_dao->updateByPk( $entity_GoodsCategory_base );
    }

    /**
     * 过滤spec_value中的-
     * @param type $value
     */
    private function filterSeparator( $value )
    {
        return str_replace( '-', '－', strtoupper( $value ) );
    }

    /**
     * todo delete
     */
    public function tempUpdateGoodsSku()
    {
        $dao = dao_factory_base::getGoodsSkuDao();
        $dao->setField( 'goods_sku_id,goods_sku' );
        //$dao->setWhere( 'goods_sku_id=10113' );
        $res = $dao->getListByWhere();
        foreach ( $res as $value ) {
            $goods_sku_array = explode( '-', $value->goods_sku );
            sort( $goods_sku_array );
            $goods_sku_array = array_filter( $goods_sku_array );
            $goods_sku_string = implode( '-', $goods_sku_array );

            $entity_GoodsSku_base = new entity_GoodsSku_base();
            $entity_GoodsSku_base->goods_sku = $goods_sku_string;
            $dao->setPk( $value->goods_sku_id );
            $dao->updateByPk( $entity_GoodsSku_base );
            echo $value->goods_sku_id . "\r\n";
        }
    }

    protected function getBrandNameById( $brand_id )
    {
        $dao = dao_factory_base::getBrandDao();
        $dao->setField( 'brand_name' );
        $dao->setPk( $brand_id );
        $brand_info = $dao->getInfoByPk();
        if ( $brand_info ) {
            return $brand_info->brand_name;
        }
        return '';
    }

    /**
     * 处理原价/实际销价
     * @param entity_Goods_base $entity_Goods_base
     * @return \entity_Goods_base
     */
    protected function handelPromotePriceDifference( entity_Goods_base $entity_Goods_base )
    {
        if ( empty( $entity_Goods_base->promote_price ) ) {
            $entity_Goods_base->promote_price = $entity_Goods_base->goods_price;
            $entity_Goods_base->price_difference = 0;
        } else if ( $entity_Goods_base->promote_price <= $entity_Goods_base->goods_price ) {
            $entity_Goods_base->promote_price = $entity_Goods_base->goods_price;
            $entity_Goods_base->price_difference = 0;
        } else {
            $entity_Goods_base->price_difference = ceil( $entity_Goods_base->promote_price - $entity_Goods_base->goods_price );
        }
        return $entity_Goods_base;
    }

}
