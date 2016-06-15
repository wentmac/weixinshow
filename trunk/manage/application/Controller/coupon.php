<?php

/**
 * 后台文章模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: coupon.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class couponAction extends service_Controller_manage
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();

        if ( $this->memberInfo->member_type <> service_Member_base::member_type_mall ) {
            die( '供应商专属哟~' );
        }
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {

        $model = new service_Coupon_manage();
        $model->setMemberInfo( $this->memberInfo );

        $coupon_money_created_total = $model->getCouponCreatedSum();

        $member_mall_model = new service_MemberMall_base();
        $member_mall_model->setUid( $this->memberInfo->uid );
        $member_mall_info = $member_mall_model->getMemberMallInfoByUid( $this->memberInfo->uid );
        //TODO  取出所有广告
        //$poster_array = $model->getPosterCustomList();
        //$poster_custom_array = Tmac::config( 'poster.poster_custom', APP_ADMIN_NAME );

        $status = Input::get( 'coupon_status', '' )->string();
        $query_string = Input::get( 'coupon_code', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $coupon_value_array = array();
        for($i=5;$i<=50;$i+=5){
            $coupon_value_array[] = $i;            
        }
        
        $searchParameter[ 'coupon_status' ] = $status;
        $searchParameter[ 'pagesize' ] = $pagesize;
        $searchParameter[ 'coupon_code' ] = $query_string;
        $array[ 'searchParameter' ] = json_encode( $searchParameter );

        $array[ 'coupon_value_array' ] = $coupon_value_array;
        $array[ 'coupon_money_created_total' ] = $coupon_money_created_total;
        $array[ 'member_mall_info' ] = $member_mall_info;
        $array[ 'coupon_money_credits' ] = $member_mall_info->mall_coupon - $coupon_money_created_total;

//        $this->apiReturn($array);die;
        $this->assign( $array );
        $this->V( 'coupon' );
    }

    public function get_list()
    {
        $coupon_status = isset( $_GET[ 'coupon_status' ] ) ? (int) $_GET[ 'coupon_status' ] : -1;
        $coupon_code = Input::get( 'coupon_code', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $model = new service_Coupon_manage();
        $model->setMemberInfo( $this->memberInfo );
        $model->setCoupon_status( $coupon_status );
        $model->setCoupon_code( trim( $coupon_code ) );
        $model->setPagesize( $pagesize );
        $res = $model->getCouponList();
        $this->apiReturn( $res );
    }

    /**
     * 广告管理 insert update => save()
     */
    public function create()
    {
        if ( empty( $_POST ) || count( $_POST ) < 1 ) {
            throw new ApiException( 'don\'t be evil' );
        }
        /* 初始化变量 */
        $coupon_num = Input::post( 'coupon_num', 0 )->required( '代金券数量不能为空' )->int();
        $coupon_value = Input::post( 'coupon_value', 0 )->required( '代金券面值不能为空' )->int();

        if ( $coupon_value > 50 ) {
            throw new ApiException( '代金券面值不能大于50' );
        }


        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $member_mall_model = new service_MemberMall_base();
        $member_mall_model->setUid( $this->memberInfo->uid );
        $member_mall_info = $member_mall_model->getMemberMallInfoByUid( $this->memberInfo->uid );

        $model = new service_Coupon_manage();
        $model->setMemberInfo( $this->memberInfo );

        $coupon_money_created_total = $model->getCouponCreatedSum();

        $coupon_money_credits = $member_mall_info->mall_coupon - $coupon_money_created_total;
        $coupon_money = $coupon_num * $coupon_value;
        if ( $coupon_money > $coupon_money_credits ) {
            throw new ApiException( '代金券剩余额度不足哟' );
        }

        $model->setCoupon_num( $coupon_num );
        $model->setCoupon_value( $coupon_value );
        $rs = $model->createCoupon();
        if ( $rs ) {
            $this->apiReturn();
        } else {
            throw new ApiException( '生成代金券失败' );
        }
    }

    /**
     * 批量操作
     */
    public function batch()
    {
        $act = Input::post( 'action', '' )->string();
        $aid = Input::post( 'id', 0 )->string();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();

        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            throw new ApiException( '请选择要操作的...' );
        }

        if ( $do == 'del' || $act == 'del' ) {
            $model = new service_Poster_manage();
            $model->setMall_uid( $this->memberInfo->uid );
            $rs = $model->deletePosterId( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->apiReturn();
            } else {
                throw new ApiException( '删除失败，请重试！' );
            }
        }
    }

}
