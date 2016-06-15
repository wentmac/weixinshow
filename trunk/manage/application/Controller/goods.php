<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: goods.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class goodsAction extends service_Controller_manage
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();

        if ( $this->memberInfo->member_type <> service_Member_base::member_type_supplier && $this->memberInfo->member_type <> service_Member_base::member_type_mall ) {
            die( '供应商专属哟~' );
        }
    }

    public function index()
    {
        $just_this_goods_cat_id = Input::get( 'just_this_goods_cat_id', 2 )->int(); //如果是1,查询下级所有商品。如果是2，仅查询当前分类下的商品
        $goods_cat_id = Input::get( 'goods_cat_id', 0 )->int();
        $sort = Input::get( 'sort', 1 )->int();
        $query_string = Input::get( 'search_keyword', '' )->string();
        $status = Input::get( 'status', '' )->string();
        $self = Input::get( 'self', '' )->string();
        $goods_type = Input::get( 'goods_type', 0 )->int();

        $goods_model = new service_goods_GoodsList_manage();

        $goods_model->setUid( $this->memberInfo->uid );
        $goods_model->setMemberInfo( $this->memberInfo );
        $goods_model->setSort( $sort );
        $goods_model->setGoods_cat_id( $goods_cat_id );
        $goods_model->setQuery_string( $query_string );
        $goods_model->setStatus( $status );
        $goods_model->setPagesize( 20 );
        $goods_model->setJust_this_goods_cat_id( $just_this_goods_cat_id - 1 );
        $goods_model->setImage_size( 110 );
        $goods_model->setSelf( $self );
        $goods_model->setUrl( PHP_SELF . '?m=goods' );
        $goods_model->setGoods_type( $goods_type );
        $rs = $goods_model->getGoodsList();
        //获取商品分类        
        $category_model = new service_GoodsCategory_admin();
        $category_list_option = $category_model->getCategoryTreeList( 0, $goods_cat_id );

        $goods_cat_name = '';
        if ( !empty( $goods_cat_id ) ) {
            $category_info = $category_model->getCategoryInfo( $goods_cat_id, 'cat_name' );
            $goods_cat_name = $category_info->cat_name;
        }


        $goods_type_array = Tmac::config( 'goods.goods.goods_type', APP_BASE_NAME );
        $goods_type_option = Utility::Option( $goods_type_array, $goods_type );

        $array[ 'status' ] = $status;
        $array[ 'query_string' ] = $query_string;
        $array[ 'sort' ] = $sort;
        $array[ '_goods_cat_id' ] = $goods_cat_id;
        $array[ 'goods_cat_name' ] = $goods_cat_name;
        $array[ 'just_this_goods_cat_id' ] = $just_this_goods_cat_id;
        $array[ 'category_list_option' ] = $category_list_option;
        $array[ 'goods_type_option' ] = $goods_type_option;

        $this->assign( $array );
        $this->assign( $rs );

//        echo '<pre>';
//        print_r($array);
//        print_r($rs);
////        echo '<pre>';
//     die;
        $this->V( 'goods_index_xiaobai' );
    }

    public function add()
    {

        $goods_id = Input::get( 'id', 0 )->int();
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->goods_image_id_array = array();
        $entity_Goods_base->goods_image_array = array();
        $entity_Goods_base->goods_cat_id = 0;

        $goods_model = new service_goods_Manage_manage();
        //$goods_id = 29;
        $goods_model->setGoods_id( $goods_id );

        $goods_sku_array = array();

        if ( $goods_id > 0 ) {
            $this->is_maller || $this->checkPurview( $goods_id );
            $entity_Goods_base = $goods_model->getGoodsInfo();
            if ( $entity_Goods_base == false ) {
                die( '商品不存在' );
            }
            $goods_sku_array = $goods_model->getGoodsSkuArray();
        }
        $spec_tree = $goods_model->getUserSpecArray( $this->memberInfo->uid );
        $global_param = array(
            'spec_tree' => $spec_tree
        );

        $global_value = $goods_model->getGoodsSpecArray();

        //获取商品分类        
        $category_model = new service_GoodsCategory_admin();
        $category_model->setIs_cloud_product( service_GoodsCategory_base::is_cloud_product_yes );
        $category_list_option = $category_model->getCategoryTreeList( 0, $entity_Goods_base->goods_cat_id );

        //商品来源
        $goods_source_array = Tmac::config( 'goods.goods.goods_source', APP_BASE_NAME );
        $goods_source_option = Utility::Option( $goods_source_array, $entity_Goods_base->goods_source );

        //商品类型
        $goods_type_array = Tmac::config( 'goods.goods.goods_type', APP_BASE_NAME );
        $goods_type_option = Utility::Option( $goods_type_array, $entity_Goods_base->goods_type );

        //会员商品级别
        $goods_member_level_array = Tmac::config( 'goods.goods.goods_member_level', APP_BASE_NAME );
        $goods_member_level_option = Utility::Option( $goods_member_level_array, $entity_Goods_base->goods_member_level );

        //是否能加积分活动
        $is_integral_array = Tmac::config( 'goods.goods.is_integral', APP_BASE_NAME );
        $is_integral_option = Utility::Option( $is_integral_array, $entity_Goods_base->is_integral );

        $array[ 'global_param' ] = json_encode( $global_param, true );
        $array[ 'global_value' ] = json_encode( $global_value[ 'result' ], true );
        $array[ 'goods_spec_array' ] = $global_value[ 'result_object' ];
        $array[ 'goods_id' ] = $goods_id;
        $array[ 'uid' ] = $this->memberInfo->uid;
        $array[ 'category_list_option' ] = $category_list_option;
        $array[ 'goods_source_option' ] = $goods_source_option;
        $array[ 'goods_type_option' ] = $goods_type_option;
        $array[ 'goods_member_level_option' ] = $goods_member_level_option;
        $array[ 'is_integral_option' ] = $is_integral_option;
        $array[ 'editinfo' ] = $entity_Goods_base;
        $array[ 'goods_priview' ] = $goods_model->getGoods_priview();
        $array[ 'image_array' ] = json_encode( $entity_Goods_base->goods_image_id_array, true );
        $array[ 'goods_image_array' ] = json_encode( $entity_Goods_base->goods_image_array, true );
        $array[ 'goods_sku_array' ] = json_encode( $goods_sku_array, true );
//        echo '<pre>';
//        print_r( $array );
//        echo '<pre>';
//        die;
        $this->assign( $array );
        $this->V( 'goods_add_xiaobai' );
    }

    public function save()
    {

        if ( empty( $_POST ) || count( $_POST ) < 1 ) {
            $this->redirect( '插入数据失败' );
            exit;
        }

        /**
          echo '<Pre>';
          print_r( $_POST );
          echo '<Pre>';
          die;
         */
        //初始化变量
        $goods_id = Input::post( 'goods_id', 0 )->int();
        $goods_cat_id = Input::post( 'goods_cat_id', 0 )->int();
        $goods_name = Input::post( 'goods_name', '' )->required( '商品名称不能为空' )->string();
        $image_array = Input::post( 'image_array', '' )->sql();
        $goods_desc = trim( Input::post( 'goods_desc', '' )->sql() );
        $item_cat_id_array = array();
        /**
         * 和supplier（供应商）的不同是 原来的 $goods_sku_stock的key值是‘5-7-8’||‘4’这样的sku串。
         * 而seller（分销商）现在的key值 如果是‘4’或‘5’这样的 说明是修改，如果是‘白色L’这样的说明是新增的
         */
        $goods_sku_stock_array = empty( $_POST[ 'goods_sku_stock' ] ) ? '' : $_POST[ 'goods_sku_stock' ];
        /**
         * 和supplier（供应商）的不同是 原来的 $goods_spec_array的key值是‘4’||‘5’这样的int。
         * 而seller（分销商）现在的array中的值 如果是‘4’或‘5’这样的 说明是修改，如果是‘白色L’这样的说明是新增的
         */
        $goods_spec_array = empty( $_POST[ 'goods_spec_array' ] ) ? '' : $_POST[ 'goods_spec_array' ];
        $goods_price = Input::post( 'goods_price', 0 )->float();
        $goods_stock = Input::post( 'goods_stock', 0 )->int();
        $outer_code = Input::post( 'outer_code', 0 )->string();
        $commission_fee = Input::post( 'commission_fee', 0 )->float();
        $commission_fee_rank = Input::post( 'commission_fee_rank', 0 )->float();
        $promote_start_date = Input::post( 'promote_start_date', '' )->string();
        $promote_end_date = Input::post( 'promote_end_date', '' )->string();
        $promote_price = Input::post( 'promote_price', 0 )->float(); //推广价展示的原价
        $commission_type = Input::post( 'commission_type', 0 )->int(); //佣金类型（0：固定金额｜1：总价比例）
        $commission_scale = Input::post( 'commission_scale', 0 )->float(); //佣金比例
        $shipping_fee = Input::post( 'shipping_fee', 0 )->float(); //运费
        $goods_source = Input::post( 'goods_source', 0 )->int(); //运费
        $goods_source_id = Input::post( 'goods_source_id', 0 )->int(); //运费
        $goods_image_id = Input::post( 'goods_image_id', 0 )->imageId(); //主图
        $goods_type = Input::post( 'goods_type', 0 )->int(); //商品类型
        $goods_member_level = Input::post( 'goods_member_level', 0 )->int(); //会员商品级别
        $goods_sort = Input::post( 'goods_sort', 0 )->int();
        $is_integral = Input::post( 'is_integral', 0 )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        //判断必填
        if ( count( $image_array ) == 0 ) {
            throw new ApiException( '亲，总得上传一张照片吧~~！' );
        }
        //单品商品 和 商品规格sku 二选一
        if ( (empty( $goods_price ) || empty( $goods_stock )) && empty( $goods_sku_stock_array ) ) {
            throw new ApiException( '亲，商品价格，规格 呢？' );
        }
        if ( !empty( $goods_sku_stock_array ) ) {
            $goods_price = 0;
            $goods_stock = 0;
            $outer_code = '';
            $this->_checkGoodsSku( $goods_sku_stock_array, $goods_spec_array );
        }
        /**
         * todo 要保存 涉及到的表
         * goods
         * goods_category_map
         * goods_sku
         * goods_spec
         * goods_image
         */
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->goods_name = trim( $goods_name );
        $entity_Goods_base->goods_desc = $goods_desc;
        $entity_Goods_base->goods_keywords = $goods_desc;
        $entity_Goods_base->goods_price = $goods_price;
        $entity_Goods_base->goods_stock = $goods_stock;
        $entity_Goods_base->outer_code = $outer_code;
        $entity_Goods_base->uid = $this->memberInfo->uid;
        $entity_Goods_base->goods_modify_time = $this->now;
        $entity_Goods_base->goods_cat_id = $goods_cat_id;
        $entity_Goods_base->commission_fee = $commission_fee;
        $entity_Goods_base->commission_fee_rank = $commission_fee_rank;
        $entity_Goods_base->promote_start_date = strtotime( $promote_start_date );
        $entity_Goods_base->promote_end_date = strtotime( $promote_end_date );
        $entity_Goods_base->promote_price = $promote_price;
        $entity_Goods_base->commission_type = $commission_type;
        $entity_Goods_base->commission_scale = $commission_scale;
        $entity_Goods_base->shipping_fee = $shipping_fee;
        $entity_Goods_base->goods_type = $goods_type;
        $entity_Goods_base->goods_member_level = $goods_member_level;
        $entity_Goods_base->goods_sort = $goods_sort;
        $entity_Goods_base->is_integral = $is_integral;
        if ( $this->is_maller ) {//聚店商城用户的自营产品
            $entity_Goods_base->is_supplier = service_Goods_base::is_supplier_no;
        } else {
            $entity_Goods_base->is_supplier = service_Goods_base::is_supplier_yes;
        }
        if ( isset( $_POST[ 'goods_source' ] ) && isset( $_POST[ 'goods_source_id' ] ) && $this->memberInfo->uid == 46 ) {
            $entity_Goods_base->goods_source = $goods_source;
            $entity_Goods_base->goods_source_id = $goods_source_id;
        }

        if ( $image_array ) {
            $entity_Goods_base->goods_image_id = empty( $goods_image_id ) ? $image_array[ 0 ] : $goods_image_id;
            $entity_Goods_base->goods_image_ids = json_encode( $image_array );
        }

        $entity_Item_base = new entity_Item_base();
        $entity_Item_base->item_cat_id = is_array( $item_cat_id_array ) ? implode( ',', $item_cat_id_array ) : $item_cat_id_array;
        $entity_Item_base->item_name = $entity_Goods_base->goods_name;
        $entity_Item_base->item_stock = $entity_Goods_base->goods_stock;
        $entity_Item_base->item_price = $entity_Goods_base->goods_price;
        $entity_Item_base->goods_image_id = $entity_Goods_base->goods_image_id;
        $entity_Item_base->item_sort = $entity_Goods_base->goods_sort;
        $entity_Item_base->item_modify_time = $entity_Goods_base->goods_modify_time;
        $entity_Item_base->uid = $this->memberInfo->uid;
        $entity_Item_base->commission_fee = $commission_fee;
        $entity_Item_base->is_delete = 0;

        $goods_save_model = new service_goods_GoodsSave_manage();
        $goods_save_model->setItem_cat_id_array( $item_cat_id_array );
        $goods_save_model->setGoods_image_array( $image_array );
        $goods_save_model->setGoods_sku_stock_array( $goods_sku_stock_array );
        $goods_save_model->setGoods_spec_array( $goods_spec_array );
        $goods_save_model->setUid( $this->memberInfo->uid );
        $goods_save_model->setGoods_id( $goods_id );
        $goods_cat_id_array = explode( ',', $goods_cat_id );
        $goods_save_model->setGoods_cat_id_array( $goods_cat_id_array );

        try {
            if ( $goods_id > 0 ) {
                //update data     
                ////权限检测
                $this->checkPurview( $goods_id );
                $goods_save_model->setGoods_id( $goods_id );
                $entity_Goods_base->goods_modify_time = $this->now;
                $rs = $goods_save_model->modifySupplierGoods( $entity_Goods_base );
                $message = '修改商品失败';
            } else {
                //insert data
                $rs = $goods_save_model->createGoods( $entity_Item_base, $entity_Goods_base );
                $message = '插入商品失败！,请联系技术支持检查原因！';
            }
        } catch (TmacClassException $e) {
            throw new ApiException( $e->getMessage() );
        }

        if ( $rs ) {
            $this->apiReturn( array() );
        } else {
            $error = $goods_save_model->getErrorMessage();
            if ( !empty( $error ) ) {
                $message = $error;
            }
            //Log::getInstance( 'api_post_goods_log' )->write( $message . '|' . var_export( $_GET, true ) . var_export( $_POST, true ) );
            throw new ApiException( $message );
        }
    }

    public function check_goods_name_repeat()
    {
        $goods_id = Input::post( 'goods_id', 0 )->int();
        $goods_name = Input::post( 'goods_name', '' )->required( '商品名称不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $goods_save_model = new service_goods_GoodsSave_manage();

        if ( empty( $goods_id ) ) {
            $repeat_item_id = $goods_save_model->checkGoodsRepeat( $this->memberInfo->uid, $goods_name );
        } else {
            $repeat_item_id = $goods_save_model->checkModifyGoodsRepeat( $this->memberInfo->uid, $goods_id, $goods_name );
        }
        if ( $repeat_item_id ) {
            $errorMessage = '商品名:"' . $goods_name . '"已经存在了重复';
            throw new ApiException( $errorMessage );
        }
        $this->apiReturn( array() );
    }

    public function get_attr_groups()
    {
        $goods_type_id = Input::get( 'goods_type_id', 0 )->required( '商品分类属性名称不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $model = new service_Attribute_admin();
        $attr_groups_array = $model->get_attr_groups( $goods_type_id );
        if ( !empty( $attr_groups_array[ 0 ] ) ) {
            $attr_groups_option = Utility::OptionObject( $attr_groups_array, 0 );
        } else {
            $attr_groups_option = '';
        }
        $this->apiReturn( $attr_groups_option );
    }

    public function add_spec_value()
    {
        $spec_id = Input::post( 'spec_id', 0 )->required( '商品规格不能为空' )->int();
        $spec_value_name = Input::post( 'spec_value_name', '' )->required( '商品规格值' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $spec_model = new service_Spec_manage();
        $spec_model->setUid( $this->memberInfo->uid );
        $res = $spec_model->createSpecValue( $spec_id, $spec_value_name );
        if ( $res ) {
            $this->apiReturn( $res );
        } else {
            throw new ApiException( $spec_model->getErrorMessage() );
        }
    }

    public function add_spec()
    {
        $spec_name = Input::post( 'spec_name', '' )->required( '商品规格不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $spec_model = new service_Spec_manage();
        $spec_model->setUid( $this->memberInfo->uid );
        $res = $spec_model->createSpec( $spec_name );
        if ( $res ) {
            $this->apiReturn( $res );
        } else {
            throw new ApiException( $spec_model->getErrorMessage() );
        }
    }

    public function temp_save_spec()
    {
        $spec_name = $_GET[ 'spec_name' ];
        $spec_value_array = empty( $_GET[ 'spec_value_array' ] ) ? '' : $_GET[ 'spec_value_array' ];
        $array = array();
        if ( $spec_value_array ) {
            foreach ( $spec_value_array as $value ) {
                $array[] = $value[ 'text' ];
            }
        }

        $spec_model = new service_Spec_manage();
        $rs = $spec_model->batchSave( $uid = 1, $spec_name, $array );
        $this->apiReturn( $rs );
    }

    /**
     * 校验goods_sku的数量和格式
     */
    private function _checkGoodsSku( $goods_sku_stock_array, $goods_spec_array )
    {
        $sku_count = 0;
        $i = 0;
        foreach ( $goods_spec_array AS $spec_value_array ) {
            if ( $i == 0 ) {
                $sku_count = count( $spec_value_array );
            } else {
                $sku_count = $sku_count * count( $spec_value_array );
            }
            $i++;
        }

        if ( $sku_count <> count( $goods_sku_stock_array ) ) {
            throw new ApiException( '商品规格数量不正确哟~' );
        }

        foreach ( $goods_sku_stock_array AS $goods_sku => $goods_sku_object ) {
            $goods_sku_array = explode( '-', $goods_sku );
            if ( count( $goods_sku_array ) <> $i ) {
                throw new ApiException( '不能使用"-"符号' );
            }
            if ( empty( $goods_sku_object[ 'sku_price' ] ) ) {
                throw new ApiException( '价格不能为空' );
            }
            //验证有两位小数的正实数：^[0-9]+(.[0-9]{2})?$
            if ( !preg_match( '/^(\d+)+(.[0-9]{1,2})?$/', $goods_sku_object[ 'sku_price' ] ) ) {
                throw new ApiException( '价格格式不正确' );
            }
            if ( empty( $goods_sku_object[ 'sku_stock' ] ) ) {
                throw new ApiException( '库存不能为空' );
            }
            if ( !preg_match( '/^(\d+)$/', $goods_sku_object[ 'sku_stock' ] ) ) {
                throw new ApiException( '库存格式不正确' );
            }
        }
    }

    /**
     * 图片删除
     * @throws ApiException
     */
    public function deleteGoodsImage()
    {
        $goods_id = Input::get( 'goods_id', 0 )->required( '商品ID不能为空' )->int();
        $goods_image_id = Input::get( 'goods_image_id', 0 )->required( '商品图片ID不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        //校验 用户对goods_id的权限，以及goods_image_id对goods_id的权限
        $goods_model = new service_goods_Manage_manage();
        $goods_model->setGoods_id( $goods_id );
        $goods_model->setUid( $this->memberInfo->uid );

        $checkImage = $goods_model->checkGoodsImagePurview( $goods_image_id );
        if ( $checkImage == false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $deleteImage = $goods_model->deleteGoodsImageById( $checkImage->id );
        if ( $deleteImage ) {
            $this->apiReturn( array( 'goods_image_id' => stripslashes( $goods_image_id ) ) );
        } else {
            throw new ApiException( '删除失败' );
        }
    }

    /**
     * 批量操作
     */
    public function batch()
    {
        $status = Input::post( 'status', '' )->required( '操作动作不能为空' )->string(); //del|on|off
        $goods_id = Input::post( 'id', 0 )->required( '要操作的ID不能为空' )->intString();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        //权限检测
        $this->checkPurview( $goods_id );

        $goods_model = new service_goods_Manage_manage();
        $goods_model->setGoods_id( $goods_id );
        $goods_model->setStatus( $status );
        $rs = $goods_model->batchGoodsStatus();

        // TODO DEL该分类下的所有资讯
        if ( $rs ) {
            $this->apiReturn();
        } else {
            throw new ApiException( '操作失败，请重试！' );
        }
    }

    /**
     * 批量操作
     */
    public function batch_category()
    {
        $goods_cat_id = Input::post( 'goods_cat_id', 0 )->int();
        $goods_id = Input::post( 'goods_id', 0 )->required( '要操作的ID不能为空' )->intString();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        //权限检测
        $this->checkPurview( $goods_id );

        $model = new service_goods_Manage_admin();
        $rs = $model->saveGoodsCategoryMap( $goods_id, $goods_cat_id, $category_type == 'edit' );

        // TODO DEL该分类下的所有资讯
        if ( $rs ) {
            $this->apiReturn();
        } else {
            throw new ApiException( '操作失败，请重试！' );
        }
    }

    /**
     * 取分类ID的子分类
     */
    public function get_goods_category_array()
    {
        if ( !isset( $_GET[ 'goods_cat_id' ] ) ) {
            throw new ApiException( '请选择分类' );
        }
        $goods_cat_id = (int) $_GET[ 'goods_cat_id' ];

        $model = new service_goods_Manage_manage();
        $res = $model->getGoodsCategoryByID( $goods_cat_id );
        $this->apiReturn( $res );
    }

    /**
     * 判断用户对分类的权限
     * @param type $id_string “1”或者“1,2,3,4”
     */
    private function checkPurview( $id_string, $throw_type = 'api' )
    {
        $goods_model = new service_goods_Manage_manage();
        $goods_model->setUid( $this->memberInfo->uid );

        $check_result = $goods_model->checkGoodsIdStringPurview( $id_string );
        if ( $check_result == false ) {
            if ( $throw_type == 'api' ) {
                throw new ApiException( $goods_model->getErrorMessage() );
            } else {
                $this->redirect( $goods_model->getErrorMessage() );
            }
            return false;
        }
        return $check_result;
    }

    /**
     * 自定义调整商品价格
     */
    public function custom_price()
    {
        $price_type = Input::post( 'price_type', '' )->required( '调价类型不能为空' )->string(); //del|on|off
        $price_class = Input::post( 'price_class', '' )->required( '调价方法不能为空' )->string(); //del|on|off
        $price_value = Input::post( 'price_value', '' )->required( '调价值不能为空' )->float(); //del|on|off
        $goods_id = Input::post( 'id', 0 )->required( '要操作的ID不能为空' )->intString();
        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        //判断权限
        if ( $this->is_maller == false ) {
            throw new ApiException( '没有对应的权限' );
        }
        $price_type_array = array( 'plus', 'less' );
        $price_class_array = array( 'fixed', 'percent' );
        //判断合法性
        if ( !in_array( $price_type, $price_type_array ) ) {
            throw new ApiException( '操作选择不存在' );
        }
        if ( !in_array( $price_class, $price_class_array ) ) {
            throw new ApiException( '操作选择不存在' );
        }

        if ( $price_type == 'plus' ) {
            $price_type_ = service_goods_Price_base::price_type_plus;
        } else {
            $price_type_ = service_goods_Price_base::price_type_less;
        }
        if ( $price_class == 'fixed' ) {
            $price_class_ = service_goods_Price_base::price_class_fixed;
        } else {
            $price_class_ = service_goods_Price_base::price_class_percent;
        }

        $model = new service_goods_Price_manage();
        $model->setGoods_id( $goods_id );
        $model->setPrice_type( $price_type_ );
        $model->setPrice_class( $price_class_ );
        $model->setPrice_value( $price_value );
        $model->setUid( $this->memberInfo->uid );

        $res = $model->saveGoodsCustomPrice();

        if ( $res == false ) {
            throw new ApiException( $model->getErrorMessage() );
        }
        $this->apiReturn();
    }

}
