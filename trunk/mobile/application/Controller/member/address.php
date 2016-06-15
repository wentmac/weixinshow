<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: address.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class addressAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();        
        $this->checkLogin();        
    }

    /**
     * 我的收货地址
     */
    public function index()
    {
        $backurl = Input::get( 'backurl', '' )->string();

        $uid = $this->memberInfo->uid;
        $address_model = new service_member_Address_mobile;
        $address_model->setUid( $uid );
        $address_list = $address_model->getMemberAddressList();

        $array[ 'address_list' ] = $address_list;
        // echo '<pre>';
        // print_r( $array );
        // echo '</pre>';
        //die;
        $this->assign( $array );
        $this->assign( 'backurl', $backurl );
        $this->V( 'member/address_list' );
    }

    /**
     * 选择已有的收货地址
     */
    public function select()
    {
        $backurl = Input::get( 'backurl', '' )->string();
        $address_id = Input::get( 'address_id', 0 )->int();

        $uid = $this->memberInfo->uid;
        $address_model = new service_member_Address_mobile;
        $address_model->setUid( $uid );
        $address_list = $address_model->getMemberAddressList();

        $array[ 'address_list' ] = $address_list;
        $array[ 'address_id' ] = $address_id;
        // echo '<pre>';
        // print_r( $array );
        // echo '</pre>';
        //die;
        $this->assign( $array );
        $this->assign( 'backurl', $backurl );
        $this->V( 'member/address_select' );
    }

    /**
     * 收货地址增加
     */
    public function add()
    {
        $address_id = Input::get( 'id', 0 )->int();
        $backurl = Input::get( 'backurl', '' )->string();

        $entity_MemberAddress_base = new entity_MemberAddress_base();


        $model = new service_member_Address_mobile();
        //$goods_id = 29;
        $model->setAddress_id( $address_id );
        $model->setUid( $this->memberInfo->uid );

        if ( $address_id > 0 ) {
            $check_purview = $model->checkPurview();
            if ( $check_purview == false ) {
                $this->redirect( $model->getErrorMessage() );
            }
            $entity_MemberAddress_base = $model->getMemberAddressInfoById();
        }
        //取城市联运信息
        $region_model = Tmac::model( 'Region', APP_BASE_NAME );
        $region_model instanceof service_Region_base;
        $province_array = $region_model->getRegionListByPid( 1 );
        $city_option = '';
        $district_option = '';

        $province_option = Utility::OptionObject( $province_array, $entity_MemberAddress_base->province, 'region_id,region_name' );
        if ( $entity_MemberAddress_base->city > 0 ) {
            $city_array = $region_model->getRegionListByPid( $entity_MemberAddress_base->province );
            $city_option = Utility::OptionObject( $city_array, $entity_MemberAddress_base->province, 'region_id,region_name' );
        }
        if ( $entity_MemberAddress_base->district > 0 ) {
            $district_array = $region_model->getRegionListByPid( $entity_MemberAddress_base->city );
            $district_option = Utility::OptionObject( $district_array, $entity_MemberAddress_base->city, 'region_id,region_name' );
        }
        $array = array();
        $array[ 'province_option' ] = $province_option;
        $array[ 'city_option' ] = $city_option;
        $array[ 'district_option' ] = $district_option;

        $array[ 'address_id' ] = $address_id;
        $array[ 'editinfo' ] = $entity_MemberAddress_base;
        $array[ 'editinfo_json' ] = json_encode( $entity_MemberAddress_base, true );
        $array[ 'backurl' ] = $backurl;

        // echo '<pre>';
        // print_r( $array );
        // echo '</pre>';
        //die;
        $this->assign( $array );
        $this->V( 'member/address_add' );
    }

    /**
     * 收货地址保存
     */
    public function save()
    {
        $uid = $this->memberInfo->uid;
        $address_id = Input::post( 'address_id', 0 )->int();
        $address_name = Input::post( 'address_name', '' )->string();
        $consignee = Input::post( 'consignee', '' )->required( '收货人姓名不能为空' )->string();
        $mobile = Input::post( 'mobile', 0 )->required( '电话号码不能为空' )->tel();
        $email = Input::post( 'email', '' )->email();
        $country = Input::post( 'country', 1 )->int();
        $province = Input::post( 'province', 0 )->required( '省不能为空' )->int();
        $city = Input::post( 'city', 0 )->required( '城市不能为空' )->int();
        $district = Input::post( 'district', 0 )->required( '行政区不能为空' )->int();
        $address = Input::post( 'address', '' )->required( '收货人详细地址不能为空' )->string();
        $zipcode = Input::post( 'zipcode', 0 )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }

        $entity_MemberAddress_base = new entity_MemberAddress_base();
        $entity_MemberAddress_base->address_name = $address_name;
        $entity_MemberAddress_base->consignee = $consignee;
        $entity_MemberAddress_base->mobile = $mobile;
        $entity_MemberAddress_base->email = $email;
        $entity_MemberAddress_base->country = $country;
        $entity_MemberAddress_base->province = $province;
        $entity_MemberAddress_base->city = $city;
        $entity_MemberAddress_base->district = $district;
        $entity_MemberAddress_base->address = $address;
        $entity_MemberAddress_base->zipcode = $zipcode;
        $entity_MemberAddress_base->uid = $uid;

        //取城市联运信息
        $member_address_model = new service_member_Address_mobile();
        if ( $address_id > 0 ) {
            $member_address_model->setAddress_id( $address_id );
            $member_address_model->setUid( $this->memberInfo->uid );
            $check_purview = $member_address_model->checkPurview();
            if ( $check_purview == false ) {
                throw new ApiException( $member_address_model->getErrorMessage() );
            }
            //update data            
            $entity_MemberAddress_base->address_id = $address_id;
            $rs = $member_address_model->modifyMemberAddress( $entity_MemberAddress_base );
            if ( $rs ) {
                $this->apiReturn( $address_id );
            } else {
                throw new ApiException( '修改失败！' );
            }
        } else {
            //insert data
            $staff_id = $member_address_model->createMemberAddress( $entity_MemberAddress_base );
            if ( $staff_id ) {
                $this->apiReturn( $staff_id );
            } else {
                throw new ApiException( '插入收货地址失败！,请联系技术支持检查原因！' );
            }
        }
    }

    /**
     * 默认收货地址设置
     */
    public function default_setting()
    {
        $address_id = Input::post( 'address_id', 0 )->required( '收货地址不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() );
        }
        $member_address_model = new service_member_Address_mobile();

        $member_address_model->setAddress_id( $address_id );
        $member_address_model->setUid( $this->memberInfo->uid );
        $check_purview = $member_address_model->checkPurview();
        if ( $check_purview == false ) {
            throw new ApiException( $member_address_model->getErrorMessage() );
        }
        $res = $member_address_model->updateMemberAddressDefault();
        if ( $res ) {
            $this->apiReturn( array() );
        } else {
            throw new ApiException( '设置失败，请联系客服' );
        }
    }

    /**
     * 收货地址删除
     */
    public function delete()
    {
        $aid = Input::get( 'id', 0 )->int();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();

        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            throw new ApiException( '请选择要操作的...' );
        }
        $member_address_model = new service_member_Address_mobile();
        $rs = $member_address_model->deleteByMemberAddressById( $id );
        // TODO DEL该分类下的所有资讯
        if ( $rs ) {
            $this->apiReturn( array() );
        } else {
            throw new ApiException( '删除失败，请重试！' );
        }
    }

}
