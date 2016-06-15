<?php

/**
 * 后台 文章栏目模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: index.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class indexAction extends service_Controller_admin
{

    private $check_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
        $this->check_model = $this->M( 'Check' );
        $this->check_model->checkLogin();
        $this->check_model->CheckPurview( 'tb_admin,tb_editer' );
    }

    /**
     * 管理商品管理主页
     */
    public function index()
    {
        $goods_cat_id = Input::get( 'goods_cat_id', 0 )->int();
        $sort = Input::get( 'sort', 1 )->int();
        $uid = Input::get( 'uid', 0 )->int();
        $is_supplier = Input::get( 'is_supplier', 0 )->int();
        $goods_source = Input::get( 'goods_source', 0 )->int();
        $query_string = Input::get( 'query_string', '' )->string();
        $just_this_goods_cat_id = Input::get( 'just_this_goods_cat_id', 0 )->int();
        $do = Input::get( 'do', '' )->string();
        $pagesize = Input::get( 'pagesize', 20 )->int();
        $brand_id = Input::get( 'brand_id', 0 )->int();
        $goods_source_id = Input::get( 'goods_source_id', 0 )->int();

        /**
         * $this->uid;
         * $this->is_supplier;
         * $this->goods_source;
         * $this->goods_cat_id;
         * $this->query_string;
         * $this->sort;
         * $this->getGoodsList();
         */
        $goods_model = new service_goods_List_admin();
        $goods_model->setUid( $uid );
        $goods_model->setGoods_cat_id( $goods_cat_id );
        $goods_model->setSort( $sort );
        $goods_model->setQuery_string( $query_string );
        $goods_model->setGoods_source( $goods_source );
        $goods_model->setIs_supplier( $is_supplier );
        $goods_model->setJust_this_goods_cat_id( $just_this_goods_cat_id );
        $goods_model->setPagesize( $pagesize );
        $goods_model->setBrand_id( $brand_id );
        $goods_model->setGoods_source_id( $goods_source_id );
        $rs = $goods_model->getGoodsList();

        $is_supplier_array = Tmac::config( 'goods.goods.is_supplier_show', APP_BASE_NAME );
        $is_supplier_option = Utility::Option( $is_supplier_array, $is_supplier );

        $goods_source_array = Tmac::config( 'goods.goods.goods_source_show', APP_BASE_NAME );
        $goods_source_option = Utility::Option( $goods_source_array, $goods_source );

        //商品能批量上架到的店铺
        $market_shop_array = Tmac::config( 'goods.goods.market_shop', APP_BASE_NAME );
        $market_shop_option = Utility::Option( $market_shop_array, 0 );

        $sort_array = Tmac::config( 'goods.goods.sort', APP_BASE_NAME );
        $sort_option = Utility::Option( $sort_array, $sort );

        //取友情操作类型radiobutton数组
        $article_do_ary = Tmac::config( 'article.do' );
        $article_do_ary_option = Utility::Option( $article_do_ary, $do );

        $category_model = new service_GoodsCategory_admin();
        $category_tree_list = $category_model->getCategoryTreeList( 0, $goods_cat_id );

        //取商品品牌
        $brand_array = service_goods_Manage_base::getBrandArray();
        $brand_option = Utility::OptionObject( $brand_array, $brand_id, 'brand_id,brand_name' );
        $brand_batch_option = Utility::OptionObject( $brand_array, 0, 'brand_id,brand_name' );

        $array[ 'is_supplier_option' ] = $is_supplier_option;
        $array[ 'goods_source_option' ] = $goods_source_option;
        $array[ 'article_do_ary_option' ] = $article_do_ary_option;
        $array[ 'sort_option' ] = $sort_option;
        $array[ 'market_shop_option' ] = $market_shop_option;
        $array[ 'goods_cat_id' ] = $goods_cat_id;
        $array[ 'uid' ] = $uid;
        $array[ 'query_string' ] = $query_string;
        $array[ 'goods_source_id' ] = $goods_source_id;
        $array[ 'category_tree_list' ] = $category_tree_list;
        $array[ 'just_this_goods_cat_id' ] = $just_this_goods_cat_id;
        $array[ 'pagesize' ] = $pagesize;
        $array[ 'brand_option' ] = $brand_option;
        $array[ 'brand_batch_option' ] = $brand_batch_option;

        $this->assign( $array );
//        echo '<pre>';
//        print_r( $array );
//        print_r( $rs );
//        die;
        $this->assign( $rs );

        $this->V( 'goods/index' );
    }

    /**
     * 新增/修改栏目页面
     */
    public function detail()
    {
        $array1 = array(
            0 => array(
                'member_agent' => 1,
                'price' => 80,
                'sku_array' => array(
                    0 => array(
                        'sku_id' => 10213,
                        'price' => 81
                    ),
                    1 => array(
                        'sku_id' => 10214,
                        'price' => 82
                    ),
                    2 => array(
                        'sku_id' => 10213,
                        'price' => 83
                    )
                )
            ),
            1 => array(
                'member_agent' => 2,
                'price' => 81,
                'sku_array' => array(
                    0 => array(
                        'sku_id' => 10213,
                        'price' => 81
                    ),
                    1 => array(
                        'sku_id' => 10214,
                        'price' => 82
                    ),
                    2 => array(
                        'sku_id' => 10213,
                        'price' => 83
                    )
                )
            ),
            2 => array(
                'member_agent' => 1,
                'price' => 82,
                'sku_array' => array(
                    0 => array(
                        'sku_id' => 10213,
                        'price' => 81
                    ),
                    1 => array(
                        'sku_id' => 10214,
                        'price' => 82
                    ),
                    2 => array(
                        'sku_id' => 10213,
                        'price' => 83
                    )
                )
            ),
        );

        //echo json_encode($array1);
        //die;
        $goods_id = Input::get( 'id', 0 )->int();
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->goods_image_id_array = array();
        $entity_Goods_base->goods_image_array = array();
        $entity_Goods_base->goods_cat_id = '';
        $entity_Goods_base->uid = 0;
        $entity_Goods_base->commission_seller_different = 0;

        $goods_model = new service_goods_Manage_admin();
        //$goods_id = 29;
        $goods_model->setGoods_id( $goods_id );

        $goods_sku_array = array();

        if ( $goods_id > 0 ) {
            $entity_Goods_base = $goods_model->getGoodsInfo();
            $goods_sku_array = $goods_model->getGoodsAllFieldSkuArray();
        }

        $goods_cat_id_array = array();
        if ( !empty( $entity_Goods_base->goods_cat_id ) ) {
            $goods_cat_id_array = explode( ',', $entity_Goods_base->goods_cat_id );
        }
        //获取商品分类
        $category_array = $goods_model->getGoodsCategoryMap( $entity_Goods_base->goods_cat_id );

        //取商品品牌
        $brand_array = service_goods_Manage_base::getBrandArray();
        $brand_option = Utility::OptionObject( $brand_array, $entity_Goods_base->brand_id, 'brand_id,brand_name' );

        $array[ 'goods_id' ] = $goods_id;
        $array[ 'goods_cat_id_array' ] = $goods_cat_id_array;
        $array[ 'category_array' ] = $category_array;
        $array[ 'editinfo' ] = $entity_Goods_base;
        $array[ 'image_array' ] = json_encode( $entity_Goods_base->goods_image_id_array, true );
        $array[ 'goods_image_array' ] = $entity_Goods_base->goods_image_array;
        $array[ 'goods_sku_array' ] = $goods_sku_array;
        $array[ 'brand_option' ] = $brand_option;

        //$this->apiReturn($array);
        //die;
        $this->assign( $array );
        $this->V( 'goods/detail' );
    }

    /**
     * 新增/修改栏目页面　保存　
     */
    public function save()
    {
        $this->check_model->CheckPurview( 'tb_admin' );
        if ( empty( $_POST ) || count( $_POST ) < 3 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }

        $goods_cat_id = Input::post( 'goods_cat_id', 0 )->int();
        $cat_pid = Input::post( 'cat_pid', 0 )->int();
        //修改的时候父级栏目不能为自己
        if ( ($goods_cat_id > 0) && ($cat_pid == $goods_cat_id) ) {
            $this->redirect( '所属栏目父级不能为自己!' );
            exit;
        }

        $cat_name = Input::post( 'cat_name', '' )->required( '请填写标题！' )->string();
        $cat_keywords = Input::post( 'cat_keywords', '' )->string();
        $cat_description = Input::post( 'cat_description', '' )->string();
        $cat_sort = Input::post( 'cat_sort', 0 )->int();
        $is_cloud_product = Input::post( 'is_cloud_product', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $entity_GoodsCategory_base = new entity_GoodsCategory_base();
        $entity_GoodsCategory_base->cat_pid = $cat_pid;
        $entity_GoodsCategory_base->cat_name = $cat_name;
        $entity_GoodsCategory_base->cat_keywords = $cat_keywords;
        $entity_GoodsCategory_base->cat_description = $cat_description;
        $entity_GoodsCategory_base->cat_sort = $cat_sort;
        $entity_GoodsCategory_base->is_cloud_product = $is_cloud_product;


        $check_cat_info = $this->tmp_model->checkCategoryName( $cat_name, $cat_pid );
        if ( $check_cat_info && $check_cat_info->is_delete == 0 && $check_cat_info->goods_cat_id <> $goods_cat_id ) {
            $this->redirect( '分类"' . $cat_name . '"已经存在' );
            exit;
        }
        if ( $check_cat_info && $check_cat_info->is_delete == 1 ) {
            $entity_GoodsCategory_base->goods_cat_id = $check_cat_info->goods_cat_id;
            $entity_GoodsCategory_base->is_delete = 0;
            $rs = $this->tmp_model->modifyCategoryByPk( $entity_GoodsCategory_base );
            if ( $rs ) {
                $this->redirect( '修改新分类成功', PHP_SELF . '?m=goods/category' );
            } else {
                $this->redirect( '修改新分类失败' );
            }
        }
        if ( $goods_cat_id > 0 ) {
            //update save article_class            
            $entity_GoodsCategory_base->goods_cat_id = $goods_cat_id;
            $rs = $this->tmp_model->modifyCategoryByPk( $entity_GoodsCategory_base );
            if ( $rs ) {
                $this->redirect( '修改新分类成功', PHP_SELF . '?m=goods/category' );
            } else {
                $this->redirect( '修改新分类失败' );
            }
        } else {
            //insert save article_class
            $rs = $this->tmp_model->createCategory( $entity_GoodsCategory_base );
            if ( $rs ) {
                $this->redirect( '添加新分类成功', PHP_SELF . '?m=goods/category' );
            } else {
                $this->redirect( '添加新分类失败' );
            }
        }
    }

    /**
     * 管理员修改商品的的小部分配置
     * $commission_seller_different;
     * $commission_seller_free;
     * $commission_seller_copper;
     * $commission_seller_sliver;
     * $commission_seller_gold;
     */
    public function modify()
    {
        $this->check_model->CheckPurview( 'tb_admin' );
        $goods_id = Input::post( 'goods_id', 0 )->required( '商品ID不能为空' )->int();
        //分销商的佣金不同（0:所有的分销商佣金相同|1:分销商佣金按级别不同区分）
        $commission_seller_different = Input::post( 'commission_seller_different', 0 )->int();
        $commission_seller_free = Input::post( 'commission_seller_free', 0 )->int();
        $commission_seller_vip = Input::post( 'commission_seller_vip', 0 )->int();
        $commission_seller_svip = Input::post( 'commission_seller_svip', 0 )->int();
        $goods_sort = Input::post( 'goods_sort', 0 )->int();
        $goods_brief = Input::post( 'goods_brief', 0 )->string();
        $brand_id = Input::post( 'brand_id', 0 )->int();
        $goods_country_id = Input::post( 'goods_country_id', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        //取不同代理级别 对应不同的成本价设置
        $goods_agent = $this->getGoodsAgent( $goods_id );
        $commission_seller_different_array = array( 0, 1 );
        if ( !in_array( $commission_seller_different, $commission_seller_different_array ) ) {
            $this->redirect( '参数不正确' );
        }
        if ( !empty( $commission_seller_different ) ) {
            if ( empty( $commission_seller_free ) && empty( $commission_seller_vip ) && empty( $commission_seller_svip ) ) {
                $this->redirect( '佣金比例不能为空' );
            }
            $commission_seller_free = $commission_seller_free > 100 ? 100 : $commission_seller_free;
            $commission_seller_vip = $commission_seller_vip > 100 ? 100 : $commission_seller_vip;
            $commission_seller_svip = $commission_seller_svip > 100 ? 100 : $commission_seller_svip;
        }

        $commission_different_object_array = array(
            'commission_seller_free' => $commission_seller_free,
            'commission_seller_vip' => $commission_seller_vip,
            'commission_seller_svip' => $commission_seller_svip
        );
        $entity_Goods_base = new entity_Goods_base();
        $entity_Goods_base->commission_seller_different = $commission_seller_different;
        $entity_Goods_base->commission_different_object = serialize( $commission_different_object_array );
        $entity_Goods_base->goods_sort = $goods_sort;
        $entity_Goods_base->goods_brief = $goods_brief;
        $entity_Goods_base->brand_id = $brand_id;
        $entity_Goods_base->goods_agent = $goods_agent;
        $entity_Goods_base->goods_country_id = $goods_country_id;

        $model = new service_goods_Save_admin();
        $model->setGoods_id( $goods_id );
        $res = $model->modifyAdminGoods( $entity_Goods_base );

        if ( $res ) {
            $this->redirect( '修改成功' );
        } else {
            $this->redirect( '修改失败' );
        }
    }

    /**
     * 批量操作
     */
    public function action_do()
    {
        $this->check_model->CheckPurview( 'tb_admin,tb_editer' );

        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'id', 0 )->int();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();
        $goods_cat_id = Input::post( 'batch_goods_cat_id', 0 )->int();
        $category_type = Input::post( 'category_type', 'add' )->string();
        $market_shop = Input::post( 'market_shop', 0 )->int();
        $brand_id = Input::post( 'brand_id', 0 )->int();

        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            $this->redirect( '请选择要操作的...' );
        }
        if ( (empty( $goods_cat_id ) && empty( $do )) && empty( $act ) ) {
            $this->redirect( '请选择要操作的...' );
        }

        if ( $do == 'del' || $act == 'del' ) {
            $goods_model = new service_goods_Manage_manage();
            $rs = $goods_model->deleteGoodsById( $id );
        } else if ( $do == 'up_supplier' ) {//上架云端商品库
            $model = new service_goods_Manage_admin();
            $entity_Goods_base = new entity_Goods_base();
            $entity_Goods_base->is_supplier = 1;
            $rs = $model->batchModifyGoods( $id, $entity_Goods_base );
        } else if ( $do == 'down_supplier' ) {//上架云端商品库
            $model = new service_goods_Manage_admin();
            $entity_Goods_base = new entity_Goods_base();
            $entity_Goods_base->is_supplier = 0;
            $rs = $model->batchModifyGoods( $id, $entity_Goods_base );
        } else if ( $do == 'market_goods_create' ) {//上架指定店铺
            if ( empty( $market_shop ) ) {
                $this->redirect( '要上架的店铺不能为空' );
            }
            $market_shop_array = Tmac::config( 'goods.goods.market_shop', APP_BASE_NAME );
            if ( array_key_exists( $market_shop, $market_shop_array ) == false ) {
                $this->redirect( '要上架的店铺不合法' );
            }
            //执行批量上架
            $model = new service_goods_AgentSave_admin();
            $model->setUid( $market_shop );
            $model->setGoods_id( $id );
            $rs = $model->batchGoodsAgentSave();
        } else if ( $do == 'market_goods_brand' ) {//批量添加商品品牌
            if ( empty( $brand_id ) ) {
                $this->redirect( '要添加的商品品牌不能为空' );
                exit();
            }
            //执行批量上架
            $model = new service_goods_Manage_admin();
            $model->setGoods_id( $id );
            $rs = $model->batchUpdateGoodsBrandId( $brand_id );
        } else if ( !empty( $goods_cat_id ) ) {
            $model = new service_goods_Manage_admin();
            $rs = $model->saveGoodsCategoryMap( $id, $goods_cat_id, $category_type );
        }
        // TODO DEL该分类下的所有资讯
        if ( $rs ) {
            $this->redirect( '操作成功' );
            //$this->apiReturn( array( '删除课件成功' ) );
        } else {
            $this->redirect( '操作失败' . $model->getErrorMessage() );
            //throw new ApiException( '删除课件失败，请重试！' );
        }
    }

    private function getGoodsAgent( $goods_id )
    {
        $goods_manage = new service_goods_Manage_admin();
        $goods_sku_array = $goods_manage->getGoodsSkuIDArray( $goods_id );

        $member_config_array = Tmac::config( 'member.member.member_class', APP_BASE_NAME );
        $member_agent_array = $member_config_array[ service_Member_base::member_type_mall ];

        $goods_agent = array();
        $goods_agent_status = FALSE;
        foreach ( $member_agent_array as $member_agent_id => $member_agent_name ) {
            if ( $member_agent_id == 0 ) {
                continue;
            }
            $goods_agent_detail = array();
            $goods_agent_detail[ 'member_agent' ] = $member_agent_id;
            $goods_field = 'member_agent_' . $member_agent_id;
            $goods_agent_detail[ 'price' ] = Input::post( $goods_field, 0 )->int();
            if ( !empty( $goods_agent_detail[ 'price' ] ) ) {
                $goods_agent_status = true;
            }
            if ( empty( $goods_sku_array ) ) {
                $goods_agent_detail[ 'sku_array' ] = array();
            } else {
                foreach ( $goods_sku_array as $goods_sku ) {
                    $goods_agent_sku_detail = array();
                    $goods_sku_field = 'member_agent_' . $member_agent_id . '_' . $goods_sku->goods_sku_id;
                    $goods_agent_sku_detail[ 'sku_id' ] = $goods_sku->goods_sku_id;
                    $goods_agent_sku_detail[ 'price' ] = Input::post( $goods_sku_field, 0 )->int();
                    $goods_agent_detail[ 'sku_array' ][] = $goods_agent_sku_detail;
                    if ( !empty( $goods_agent_sku_detail[ 'price' ] ) ) {
                        $goods_agent_status = true;
                    }
                }
            }
            $goods_agent[] = $goods_agent_detail;
        }
        if ( $goods_agent_status === false ) {
            $goods_agent = array();
            return false;
        }
        return json_encode( $goods_agent );
    }

}
