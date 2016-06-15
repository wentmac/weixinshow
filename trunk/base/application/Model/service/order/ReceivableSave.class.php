<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: ReceivableSave.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_ReceivableSave_base extends service_Order_base
{

    protected $uid;
    protected $realname;
    protected $mobile;
    protected $postscript;
    protected $receivable_id;
    protected $receivable_info;    

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setRealname( $realname )
    {
        $this->realname = $realname;
    }

    function setMobile( $mobile )
    {
        $this->mobile = $mobile;
    }

    function setPostscript( $postscript )
    {
        $this->postscript = $postscript;
    }

    function setReceivable_id( $receivable_id )
    {
        $this->receivable_id = $receivable_id;
    }

    function setReceivable_info( $receivable_info )
    {
        $this->receivable_info = $receivable_info;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 订单保存
     * $this->uid;
     * $this->realname;
     * $this->mobile;
     * $this->postscript;
     * $this->receivable_id;
     * $this->receivable_info;     
     * $this->createOrder();
     */
    public function createOrder()
    {
        $order_info_dao = dao_factory_base::getOrderInfoDao();
        $order_goods_dao = dao_factory_base::getOrderGoodsDao();
        $order_action_dao = dao_factory_base::getOrderActionDao();

        $order_info_dao->getDb()->startTrans();
        //取下单用户信息
        if ( $this->getBuyerMemberInfo() == false ) {
            $order_info_dao->getDb()->rollback();
            return false;
        }
        $receivable_info = $this->receivable_info;
        $receivable_info instanceof entity_Receivable_base;

        //TODO 配送方式
        //order_info表        
        $entity_OrderInfo_base = new entity_OrderInfo_base();
        $entity_OrderInfo_base->order_sn = $this->get_order_sn();
        $entity_OrderInfo_base->uid = $this->uid;
        $entity_OrderInfo_base->order_status = service_Order_base::order_status_buyer_order_create;
        $entity_OrderInfo_base->shipping_status = 0;
        $entity_OrderInfo_base->pay_status = 0;
        $entity_OrderInfo_base->consignee = $this->realname;
        $entity_OrderInfo_base->mobile = $this->mobile;
        $entity_OrderInfo_base->address_id = 0;
        $entity_OrderInfo_base->country = 0;
        $entity_OrderInfo_base->province = 0;
        $entity_OrderInfo_base->city = 0;
        $entity_OrderInfo_base->district = 0;
        $entity_OrderInfo_base->address = '';
        $entity_OrderInfo_base->full_address = '';
        $entity_OrderInfo_base->postscript = $this->postscript;
        $entity_OrderInfo_base->weixin_id = '';
        $entity_OrderInfo_base->order_amount = $receivable_info->receivable_money;
        $entity_OrderInfo_base->commission_fee = 0;
        $entity_OrderInfo_base->shipping_fee = 0; //配送费用
        $entity_OrderInfo_base->referer = isset( $_SERVER [ 'HTTP_REFERER' ] ) ? filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING ) : '';
        $entity_OrderInfo_base->create_time = $this->now;
        $entity_OrderInfo_base->item_uid = $receivable_info->uid;
        $seller_member_setting = $this->getSellerMemberSetting( $receivable_info->uid );
        $entity_OrderInfo_base->shop_name = $seller_member_setting->shop_name;
        $entity_OrderInfo_base->supplier_mobile = $this->getSupplierMobile( $receivable_info->uid );
        $entity_OrderInfo_base->order_goods_detail = $this->getOrderGoodsDetail();
        $entity_OrderInfo_base->goods_uid = $receivable_info->uid;
        $entity_OrderInfo_base->order_type = service_Order_base::order_type_member;
        //判断用户扣手续费的开关
        if ( $this->memberInfo->fee_type == service_Member_base::fee_type_deduct ) {//扣手续费
            //直接收款和自营收入扣1.5%收取手续费
            $exchange_rate = service_Member_base::receivable_fee_type / 100;
            $entity_OrderInfo_base->commission_system_fee = round( $receivable_info->receivable_money * $exchange_rate, 2 );            
        }

        $order_id = $order_info_dao->insert( $entity_OrderInfo_base );


        //order_goods表更新
        $entity_OrderGoods_base = new entity_OrderGoods_base();
        $entity_OrderGoods_base->receivable_id = $receivable_info->receivable_id;
        $entity_OrderGoods_base->order_id = $order_id;
        $entity_OrderGoods_base->goods_id = 0;
        $entity_OrderGoods_base->item_id = 0;
        $entity_OrderGoods_base->item_name = $receivable_info->receivable_name;
        $entity_OrderGoods_base->item_number = 1;
        $entity_OrderGoods_base->item_price = $receivable_info->receivable_money;
        $entity_OrderGoods_base->outer_code = '';
        $entity_OrderGoods_base->goods_image_id = '';
        $entity_OrderGoods_base->goods_sku_id = 0;
        $entity_OrderGoods_base->goods_sku_name = '';
        $entity_OrderGoods_base->commission_fee = 0;
        $order_goods_dao->insert( $entity_OrderGoods_base );



        //order action 表
        $entity_OrderAction_base = new entity_OrderAction_base();
        $entity_OrderAction_base->order_id = $order_id;
        $entity_OrderAction_base->action_uid = $this->uid;
        $entity_OrderAction_base->action_username = $this->realname;
        $entity_OrderAction_base->order_status = 0;
        $entity_OrderAction_base->shipping_status = 0;
        $entity_OrderAction_base->pay_status = 0;
        $entity_OrderAction_base->action_note = "用户{{$this->realname}}下单成功";
        $entity_OrderAction_base->action_time = $this->now;
        $order_action_dao->insert( $entity_OrderAction_base );

        if ( $order_info_dao->getDb()->isSuccess() ) {
            $order_info_dao->getDb()->commit();
            return $entity_OrderInfo_base->order_sn;
        } else {
            $order_info_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 取分销商卖家的设置（商店名称｜库存设置）
     * $this->item_uid;
     * $this->getSellerMemberSetting();
     * @return type
     */
    public function getSellerMemberSetting( $item_uid )
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $item_uid );
        $dao->setField( 'shop_name' );
        return $dao->getInfoByPk();
    }

    /**
     * 取供应商卖家的手机号
     * $this->goods_uid;
     * $this->getSupplierMobile();
     * @return type
     */
    private function getSupplierMobile( $goods_uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $goods_uid );
        $dao->setField( 'mobile' );
        $supplier_info = $dao->getInfoByPk();
        if ( $supplier_info ) {
            return $supplier_info->mobile;
        }
        return '';
    }

    /**
     * 得到新订单号
     * @return  string
     */
    private function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand( (double) microtime() * 1000000 );

        return date( 'YmdHis' ) . str_pad( mt_rand( 1, 99999 ), 5, '0', STR_PAD_LEFT );
    }

    /**
     * 取下单用户的个人信息
     * $this->uid;
     * $this->realname;
     * $this->mobile;
     * $this->getBuyerMemberInfo();
     */
    private function getBuyerMemberInfo()
    {
        if ( !empty( $this->uid ) ) {
            return true;
        }
        $model = new service_account_Register_mobile();

        $model->setRealname( $this->realname );
        $model->setMobile( $this->mobile );
        $model->setSms_captcha( rand( 100000, 999999 ) );
        $model->setPassword( rand( 100000, 999999 ) );
        $model->setIsApi( false );
        $model->setNeed_password( FALSE );

        //注册新用户
        $checkMobile = $model->checkMobileRepeat();
        if ( $checkMobile != false ) {
            $this->uid = $checkMobile->uid;
            $this->realname = $checkMobile->realname;
            $this->mobile = $checkMobile->mobile;
            return true;
        }
        $entity_member = $model->createMember();
        if ( $entity_member == false ) {
            $this->errorMessage = $model->getErrorMessage();
            return false;
        }
        $this->uid = $entity_member->uid;
        $this->realname = $entity_member->realname;
        $this->mobile = $entity_member->mobile;
        return true;
    }

    private function getOrderGoodsDetail()
    {
        $order_goods_detail_array = array();

        $entity_OrderGoods_base = new stdClass();
        $entity_OrderGoods_base->receivable_id = $this->receivable_info->receivable_id;
        $entity_OrderGoods_base->item_id = 0;
        $entity_OrderGoods_base->item_name = $this->receivable_info->receivable_name;
        $entity_OrderGoods_base->item_number = 1;
        $entity_OrderGoods_base->item_price = $this->receivable_info->receivable_money;
        $entity_OrderGoods_base->goods_image_id = '';
        $entity_OrderGoods_base->goods_sku_name = '';
        $order_goods_detail_array[] = $entity_OrderGoods_base;

        return serialize( $order_goods_detail_array );
    }

}
