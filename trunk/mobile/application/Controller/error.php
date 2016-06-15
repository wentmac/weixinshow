<?php

/**
 * 前台 404 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: error.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class errorAction extends service_Controller_mobile
{

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
    }

    /**
     * 城市地标 首页
     */
    public function index()
    {
        $this->assign( 'title', '页面没找到' );
        $this->V( '404' );
    }

    public function test()
    {        
        ini_set( 'memory_limit', '2048M' );
        set_time_limit( 0 );
die;
        $refund_model = new service_order_Refund_base();
        $order_refund_id = 446;
        $refund_model->setOrder_refund_id( $order_refund_id );
        $order_refund_info = $refund_model->getOrderRefundInfo();


        $member_monopoly_model = new service_order_money_MemberMonopoly_base();
        $member_monopoly_model->setTotal_fee( 18 );
        $member_monopoly_model->setBatch_no( '20160420141520_446' );
        $member_monopoly_model->setRefund_id( '2009762001201604200212124624' );
        $member_monopoly_model->setTrade_no( '4009762001201604184959481909' );
        $member_monopoly_model->setTrade_vendor( 2 );        
        $member_monopoly_model->setEntity_OrderRefund( $order_refund_info );
        $res = $member_monopoly_model->refund();
        var_dump($res);die;
        /**
          $register_model = new service_account_Register_mobile();
          $res = $register_model->updateMemberOauthAvatar( 5532 );
          echo '<pre>';
          print_r( $res );
          die;

          $memberInfo = new entity_Member_base();
          $memberInfo->uid = 75;

          $model = new service_member_TreeShow_base();
          $model->setRank_level( 0 );
          $res = $model->showAgentRankTree( 70 );
          echo '<Pre>';
          print_r( $res );
          die;
          $model = new service_member_Tree_base();
          $model->setMemberInfo( $memberInfo );
          $model->modifyAgentRankUid( 46 );
          //$model->getCommissionFeeRank( 5 );

          die; */
        die;
        $order_sn = '2016050523213662527';
        //执行付款后的操作
        $order_model = new service_order_Payment_mobile();
        $orderInfo = $order_model->getOrderInfoBySN( $order_sn );
        $order_model->setOrder_id( $orderInfo->order_id );

        $trade_no = '4009762001201604184959481909';
        $pay_time = $orderInfo->create_time + rand( 60, 400 );

        $total_fee = $orderInfo->order_amount;
        $pay_time = $orderInfo->create_time + rand( 60, 400 );
        //记录订单支付日志        
        $entity_PayLog_base = new entity_PayLog_base();
        $entity_PayLog_base->uid = $orderInfo->uid;
        $entity_PayLog_base->order_id = $orderInfo->order_id;
        $entity_PayLog_base->trade_no = $trade_no;
        $entity_PayLog_base->trade_vendor = service_Order_base::trade_vendor_alipay;
        $entity_PayLog_base->trade_fee = $total_fee;
        $entity_PayLog_base->pay_time = $pay_time;
        $entity_PayLog_base->pay_type = 0;
        $entity_PayLog_base->buyer_id = '';
        $entity_PayLog_base->buyer_email = '';
        $entity_PayLog_base->pay_class = service_Order_base::pay_class_wap;
        $entity_PayLog_base->order_note = '支付成功';
        $entity_PayLog_base->pay_status = service_Order_base::pay_status_success;
        $order_model->insertPayLog( $entity_PayLog_base );

        $order_model->setTrade_vendor( service_Order_base::trade_vendor_weixin );
        $order_model->setTrade_no( $trade_no );
        $order_model->setEntity_OrderInfo( $orderInfo );
        $order_model->setPay_time( $pay_time );
        $order_model->orderPaySuccess();
        die;


//        $model = new service_member_Tree_base();
//        $model->setMemberInfo( $memberInfo );
//        $model->modifyAgentRankUid();
//        //$model->getCommissionFeeRank( 5 );
//
//        die;
        $model = new service_member_TreeShow_base();
        $model->setRank_level( 0 );
        $res = $model->showAgentRankTree();
        echo '<Pre>';
        print_r( $res );

        die;
        $goods_desc = '1501134021103哈哈。<img src="http://www.baidu.com/150113402041.jpg">';
        $goods_desc = preg_replace( '/(1\d{2})\d{4}(\d{4})/', '${1}*****${2}', $goods_desc );
        var_dump( $goods_desc );

        die;
        include Tmac::findFile( 'array2xml', APP_API_NAME );


        $restaurant = array();
        $restaurant[ '@attributes' ] = array(
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:noNamespaceSchemaLocation' => 'http://www.example.com/schmema.xsd',
            'lastUpdated' => date( 'c' )  // dynamic values
        );

        $restaurant[ 'masterChef' ] = array( //empty node with attributes
            '@attributes' => array(
                'name' => 'Mr. Big C.'
            )
        );


        $restaurant[ 'menu' ] = array();
        $restaurant[ 'menu' ][ '@attributes' ] = array(
            'key' => 'english_menu',
            'language' => 'en_US',
            'defaultCurrency' => 'USD'
        );


// we have multiple image tags (without value)
        $restaurant[ 'menu' ][ 'assets' ][ 'image' ][] = array(
            '@attributes' => array(
                'info' => 'Logo',
                'height' => '100',
                'width' => '100',
                'url' => 'http://www.example.com/res/logo.png'
            )
        );
        $restaurant[ 'menu' ][ 'assets' ][ 'image' ][] = array(
            '@attributes' => array(
                'info' => 'HiRes Logo',
                'height' => '300',
                'width' => '300',
                'url' => 'http://www.example.com/res/hires_logo.png'
            )
        );

        $restaurant[ 'menu' ][ 'item' ] = array();
        $restaurant[ 'menu' ][ 'item' ][] = array(
            '@attributes' => array(
                'lastUpdated' => '2011-06-09T08:30:18-05:00',
                'available' => true  // boolean values will be converted to 'true' and not 1
            ),
            'category' => array( 'bread', 'chicken', 'non-veg' ), // we have multiple category tags with text nodes
            'keyword' => array( 'burger', 'chicken' ),
            'assets' => array(
                'title' => 'Zinger Burger',
                'desc' => array( '@cdata' => 'The Burger we all love >_< !' ),
                'image' => array(
                    '@attributes' => array(
                        'height' => '100',
                        'width' => '100',
                        'url' => 'http://www.example.com/res/zinger.png',
                        'info' => 'Zinger Burger'
                    )
                )
            ),
            'price' => array(
                array(
                    '@value' => 10, // will create textnode <price currency="USD">10</price>
                    '@attributes' => array(
                        'currency' => 'USD'
                    )
                ),
                array(
                    '@value' => 450, // will create textnode <price currency="INR">450</price>
                    '@attributes' => array(
                        'currency' => 'INR'
                    )
                )
            ),
            'trivia' => null  // will create empty node <trivia/>
        );
        $restaurant[ 'menu' ][ 'item' ][] = array(
            '@attributes' => array(
                'lastUpdated' => '2011-06-09T08:30:18-05:00',
                'available' => true  // boolean values will be preserved
            ),
            'category' => array( 'salad', 'veg' ),
            'keyword' => array( 'greek', 'salad' ),
            'assets' => array(
                'title' => 'Greek Salad',
                'desc' => array( '@cdata' => 'Chef\'s Favorites' ),
                'image' => array(
                    '@attributes' => array(
                        'height' => '100',
                        'width' => '100',
                        'url' => 'http://www.example.com/res/greek.png',
                        'info' => 'Greek Salad'
                    )
                )
            ),
            'price' => array(
                array(
                    '@value' => 20, // will create textnode <price currency="USD">20</price>
                    '@attributes' => array(
                        'currency' => 'USD'
                    )
                ),
                array(
                    '@value' => 900, // will create textnode <price currency="INR">900</price>
                    '@attributes' => array(
                        'currency' => 'INR'
                    )
                )
            ),
            'trivia' => 'Loved by the Greek!'
        );

        $xml = array2xml::createXML( 'restaurant', $restaurant );
        echo $xml->saveXML();

        die;
        $array2xml->setRootName( 'rss' );
        $array2xml->setRootAttrs( array( 'version' => '2.0' ) );
        $array2xml->setCDataKeys( array( 'description' => TRUE ) );
        $array2xml->setElementsAttrs( $rootAttrs = array( 'first_attr' => 'value_of_first_attr', 'second_atrr' => 'etc' ) );

        $data[ 'channel' ][ 'title' ] = 'News RSS';
        $data[ 'channel' ][ 'link' ] = 'http://yoursite.com/';
        $data[ 'channel' ][ 'description' ] = 'Amazing RSS News';
        $data[ 'channel' ][ 'language' ] = 'en';
        echo $array2xml->convert( $data );

        die;
        $method = 'qunar/city.getList';
        $timestamp = '1357899387';
        $agentKey = 'f0d1f3bcsbs4ab30';
        echo Tmac::model( 'check', APP_API_NAME )->generateSign( $method, $timestamp, $agentKey );
        die;
        $check_model = Tmac::model( 'check', APP_API_NAME );
        $check_model->test();
    }

    public function getSign()
    {
        $check_model = Tmac::model( 'Check' );
        $check_model instanceof service_Check_api;

        echo $timestamp = 1418530590 . '<br>';
        echo $check_model->generateSign( 'member.set_chat_last_read_time', $timestamp, 2 );
    }

    public function test1()
    {
        $string = '<select id="ed" name="ed" class="input-text wh">';
        for ( $i = 2; $i <= 100; $i++ ) {
            $string .= '<option value="' . $i . '">' . $i . '万</option>';
        }
        $string .= '</select>';
        echo $string;
    }

    public function refund()
    {
        die;
        $order_refund_id = 476;
        $res = array(
            'out_refund_no' => '20160430095755_476',
            'refund_fee' => '3800',
            'transaction_id' => '4005192001201604295349702014', //交易单号
            'refund_id' => '2005192001201604300222753788'//退款单号
        );
        $batch_no = $res[ 'out_refund_no' ];
        /**
         * 订单售后 退款流程     
         * $this->trade_no;
         * $this->trade_vendor;
         * $this->batch_no;
         * $this->refund_id;     
         * $this->order_refund_id；
         * $this->total_fee;     
         * $this->service_status;
         * $this->refund_status;
         * $this->return_status;  
         * $this->executeOrderRefund();
         * @return type
         */
        $refund_fee = $res[ 'refund_fee' ] / 100;
        $order_model = new service_order_Refund_base();
        $order_model->setBatch_no( $batch_no );
        $order_model->setTotal_fee( $refund_fee );
        $order_model->setTrade_no( $res[ 'transaction_id' ] );
        $order_model->setTrade_vendor( service_Order_base::trade_vendor_weixin );
        $order_model->setRefund_id( $res[ 'refund_id' ] );
        $order_model->setOrder_refund_id( $order_refund_id );

        //设置状态为：买家收到货后，买家申请退款，卖家同意            
        $check = $order_model->executeOrderRefund();
        var_dump( $check );
        var_dump( $order_model->getErrorMessage() );
    }

    public function pay()
    {
        require_once Tmac::findFile( 'payment/wechat_pay_transfers/WechatPayTransfers', APP_WWW_NAME );
        $wxpay_transfers_model = new WechatPayTransfers();

        $wxpay_transfers_model->setPartner_trade_no( 1 );
        $wxpay_transfers_model->setOpenid( 'ogCHGvryyN-Lg4KqpiNPLf_lS9rQ' );
        $wxpay_transfers_model->setCheck_name( 'NO_CHECK' );
        $wxpay_transfers_model->setAmount( 100 );
        $wxpay_transfers_model->setDesc( '付款测试' );
        try {
            $result = $wxpay_transfers_model->payToUser();
        } catch (TmacClassException $exc) {
            Log::getInstance( 'mobile_order_payment_wechatpay_transfers_error' )->write( $exc->getMessage() . '|' . var_export( $params, true ) );
            die( $exc->getMessage() );
        }


        var_dump( $result );
    }

}
