<?php

/**
 * api 消息推送 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: PushMessage.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_PushMessage_base extends service_Model_base
{

    /**
     * 推送的消息类型
     * 订单消息
     */
    const message_type_order = 'order';

    /**
     * 推送的消息类型
     * 发货消息
     */
    const message_type_delivery = 'delivery';

    /**
     * 推送的消息类型
     * 确认收货消息
     * 只是push
     */
    const message_type_confirm_receipt = 'confirm_receipt';

    /**
     * 推送的消息类型
     * 申请退款
     */
    const message_type_refund = 'refund';

    /**
     * 推送的消息类型
     * 同意退款
     */
    const message_type_refund_yes = 'refund_yes';

    /**
     * 推送的消息类型
     * 拒绝退款
     */
    const message_type_refund_no = 'refund_no';

    /**
     * 推送的消息类型
     * 提现申请
     */
    const message_type_settle = 'settle';

    /**
     * 推送的消息类型
     * 提现成功
     */
    const message_type_settle_success = 'settle_success';

    /**
     * 推送的消息类型
     * 提现失败
     */
    const message_type_settle_fail = 'settle_fail';

    protected $orderInfo;
    protected $orderRefund;
    protected $settleInfo;
    protected $messageType;
    protected $push_queue;
    protected $errorMessage;
    protected $configCache;

    /**
     * 买家电话
     * @var type 
     */
    protected $buyerMobileArray = array( '13871903123', '18971670906', '15910986304', '13771023935', '15712137773', '13733526021', '13268591918', '15900463470', '13636074231', '18680910820', '15011420631', '13293359887', '13016417050', '13067870076', '13718527728', '13466612906', '18372675177', '15901484288', '13071298860', '13269561886', '18327611280', '18883656968', '15601178342', '13797151774', '13227134656', '13545079312', '13871913500', '15826850380', '15171317385', '18571126100' );

    /**
     * 供销店铺电话
     * @var type 
     */
    protected $itemMobileArray = array( '18871237223', '13037121286', '13797166332', '17003719075', '18610247767' );

    function setMessageType( $messageType )
    {
        $this->messageType = $messageType;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setOrderRefund( $orderRefund )
    {
        $this->orderRefund = $orderRefund;
    }

    function setOrderInfo( $orderInfo )
    {
        $this->orderInfo = $orderInfo;
    }

    function setSettleInfo( $settleInfo )
    {
        $this->settleInfo = $settleInfo;
    }

    public function __construct()
    {
        parent::__construct();
        $this->configcache = Tmac::config( 'configcache.config', APP_WWW_NAME, '.inc.php' );
    }

    /**
     * 执行消息推送
     * $this->setMessageType( $messageType );
     * $this->setOrderInfo( $orderInfo )；
     * $this->setOrderRefund ( $orderRefund );
     * $this->setSettleInfo ( $settleInfo );
     * $this->push();
     */
    public function push()
    {
        $orderInfo = $this->orderInfo;
        $orderInfo instanceof entity_OrderInfo_base;
        $orderRefund = $this->orderRefund;
        $orderRefund instanceof entity_OrderRefund_base;

        /**
        if ( $orderInfo->demo_order == service_Order_base::demo_order_yes ) {
            return true;
        }*/

        if ( !empty( $this->settleInfo ) ) {
            $settleInfo = $this->settleInfo;
            $settleInfo instanceof entity_Settle_base;

            $account_type_array = Tmac::config( 'bill.bill.account_type', APP_BASE_NAME );
            $account_type_text = $account_type_array[ $settleInfo->account_type ];
        }
        $sms_push = true;
        //Log::getInstance( 'push_message' )->write( $errorMessage . var_export( $rs, true ) . '|' . var_export( $_GET, true ) . var_export( $_POST, true ) );        
        switch ( $this->messageType )
        {
            case self::message_type_order:
                $order_goods_array = unserialize( $orderInfo->order_goods_detail );
                $order_goods_count = count( $order_goods_array );
                //$supplier_order_info = $this->handleFreeSupplierOrderShow( $orderInfo );
                //给供应商发送订单通知
                $supplier_array = array(
                    'uid' => $orderInfo->goods_uid,
                    'mobile' => $orderInfo->supplier_mobile,
                    'message' => "<担保交易>买家:{$orderInfo->consignee}，购买了{$order_goods_count}件商品，价格￥{$orderInfo->order_amount}，买家已成功付款。（订单号：{$orderInfo->order_sn}）。请您及时为买家发货"
                );
                $this->push_queue[] = $supplier_array;
                if ( $orderInfo->item_uid <> $orderInfo->goods_uid ) {
                    //给分销商发送订单通知
                    $seller_array = array(
                        'uid' => $orderInfo->item_uid,
                        'mobile' => $orderInfo->item_mobile,
                        'message' => "<代销订单>买家:{$orderInfo->consignee}，购买了{$order_goods_count}件商品，价格￥{$orderInfo->order_amount}，买家已成功付款。（订单号：{$orderInfo->order_sn}）。如果交易成功，您本次所得佣金￥{$orderInfo->commission_fee}"
                    );
                    $this->push_queue[] = $seller_array;
                }
                if ( $orderInfo->demo_order == service_Order_base::demo_order_no ) {
                    //给买家发送付款成功通知
                    $buyer_array = array(
                        'uid' => $orderInfo->uid,
                        'mobile' => $orderInfo->mobile,
                        'message' => "<担保交易>您好:{$orderInfo->consignee},您购买了{$order_goods_count}件商品，{$this->configCache['cfg_webname']}已收到您的付款￥{$orderInfo->order_amount}。（订单号：{$orderInfo->order_sn}）。请登录查看详情。交易中有任何问题，请联系<{$orderInfo->shop_name}>{$orderInfo->item_mobile}"
                    );
                    $this->push_queue[] = $buyer_array;
                }
                break;
            case self::message_type_delivery:
                if ( $orderInfo->item_uid <> $orderInfo->goods_uid ) {
                    //给分销商发送订单通知
                    $seller_array = array(
                        'uid' => $orderInfo->item_uid,
                        'mobile' => $orderInfo->item_mobile,
                        'message' => "<代销订单|发货>买家:{$orderInfo->consignee}的订单：{$orderInfo->order_sn}（价格￥{$orderInfo->order_amount}），供应商已经发货，{$orderInfo->express_name}:{$orderInfo->express_no}。有任何问题，请及时联系{$this->configCache['cfg_webname']}客服。"
                    );
                    $this->push_queue[] = $seller_array;
                }
                if ( $orderInfo->demo_order == service_Order_base::demo_order_no ) {
                    //给买家发送付款成功通知
                    $buyer_array = array(
                        'uid' => $orderInfo->uid,
                        'mobile' => $orderInfo->mobile,
                        'message' => "<发货>您好:{$orderInfo->consignee},您购买订单号：{$orderInfo->order_sn}的商品。卖家已经发货，{$orderInfo->express_name}:{$orderInfo->express_no}，请注意查收。有任何问题，请及时联系<{$orderInfo->shop_name}>{$orderInfo->item_mobile}或{$this->configCache['cfg_webname']}客服。"
                    );
                    $this->push_queue[] = $buyer_array;
                }
                break;
            case self::message_type_confirm_receipt:
                //$supplier_order_info = $this->handleFreeSupplierOrderShow( $orderInfo );
                //给供应商发送订单通知
                $supplier_array = array(
                    'uid' => $orderInfo->goods_uid,
                    'mobile' => $orderInfo->supplier_mobile,
                    'message' => "<确认收货>买家:{$orderInfo->consignee},购买订单号：{$orderInfo->order_sn}的商品，买家已确认收货。确认收货15天后，订单没有退款，订单货款将进入可账户余额中。"
                );
                $this->push_queue[] = $supplier_array;
                if ( $orderInfo->item_uid <> $orderInfo->goods_uid ) {
                    //给分销商发送订单通知
                    $seller_array = array(
                        'uid' => $orderInfo->item_uid,
                        'mobile' => $orderInfo->item_mobile,
                        'message' => "<代销订单|确认收货>买家:{$orderInfo->consignee}，购买订单号：{$orderInfo->order_sn}的商品，买家已确认收货。确认收货15天后，订单没有退款，订单佣金将进入可账户余额中。"
                    );
                    $this->push_queue[] = $seller_array;
                }
                $sms_push = false;
                break;
            case self::message_type_refund:
                if ( empty( $this->orderRefund ) ) {
                    $this->messageType = '退款详情不能为空';
                    return false;
                }
                $this->handleFreeSupplierRefundOrderShow( $orderRefund );
                //给供应商发送订单通知
                $supplier_array = array(
                    'uid' => $orderRefund->goods_uid,
                    'mobile' => $orderRefund->supplier_mobile,
                    'message' => "<买家申请退款>{$orderRefund->consignee}，购买订单号：{$orderRefund->order_sn}的商品。买家已申请退款，退款金额￥{$orderRefund->money}元。请您及时为买家处理退款申请。"
                );
                $this->push_queue[] = $supplier_array;
                if ( $orderRefund->item_uid <> $orderRefund->goods_uid ) {
                    //给分销商发送订单通知
                    $seller_array = array(
                        'uid' => $orderRefund->item_uid,
                        'mobile' => $orderRefund->item_mobile,
                        'message' => "<代销订单|买家申请退款>{$orderRefund->consignee}，购买订单号：{$orderRefund->order_sn}的商品。买家已申请退款，退款金额￥{$orderRefund->money}元。请您等待供应商为买家处理退款申请。"
                    );
                    $this->push_queue[] = $seller_array;
                }
                break;
            case self::message_type_refund_yes:
                if ( empty( $this->orderRefund ) ) {
                    $this->messageType = '退款详情不能为空';
                    return false;
                }
                //$this->handleFreeSupplierRefundOrderShow( $orderRefund );
                //给供应商发送订单通知
                $supplier_array = array(
                    'uid' => $orderRefund->goods_uid,
                    'mobile' => $orderRefund->supplier_mobile,
                    'message' => "<同意退款>您已经同意买家{$orderRefund->consignee}，订单号：{$orderRefund->order_sn}的退款申请，退款金额￥{$orderRefund->money}元。将原路退回买家的付款账户中。交易中有任何问题，请联系{$this->configCache['cfg_webname']}。"
                );
                $this->push_queue[] = $supplier_array;
                if ( $orderRefund->item_uid <> $orderRefund->goods_uid ) {
                    //给分销商发送订单通知
                    $seller_array = array(
                        'uid' => $orderRefund->item_uid,
                        'mobile' => $orderRefund->item_mobile,
                        'message' => "<代销订单|同意退款>供应商已经同意买家{$orderRefund->consignee}，订单号：{$orderRefund->order_sn}的退款申请，退款金额￥{$orderRefund->money}元。将原路退回买家的付款账户中。交易中有任何问题，请联系{$this->configCache['cfg_webname']}。"
                    );
                    $this->push_queue[] = $seller_array;
                }
                if ( $this->checkIsDemoOrder( $orderRefund ) == false ) {
                    //给买家发送付款成功通知
                    $buyer_array = array(
                        'uid' => $orderRefund->uid,
                        'mobile' => $orderRefund->mobile,
                        'message' => "<同意退款>卖家<{$orderRefund->shop_name}>{$orderRefund->item_mobile}已经同意订单号：{$orderRefund->order_sn}的退款申请，退款金额￥{$orderRefund->money}元，将原路退回您的付款账户中。交易中有任何问题，请联系{$this->configCache['cfg_webname']}"
                    );
                    $this->push_queue[] = $buyer_array;
                }
                break;
            case self::message_type_refund_no:
                if ( empty( $this->orderRefund ) ) {
                    $this->messageType = '退款详情不能为空';
                    return false;
                }
                //$this->handleFreeSupplierRefundOrderShow( $orderRefund );
                //给供应商发送订单通知
                $supplier_array = array(
                    'uid' => $orderRefund->goods_uid,
                    'mobile' => $orderRefund->supplier_mobile,
                    'message' => "<拒绝退款>您已经拒绝买家{$orderRefund->consignee}，订单号：{$orderRefund->order_sn}的退款申请（￥{$orderRefund->money}元）。交易中有任何问题，请联系{$this->configCache['cfg_webname']}。"
                );
                $this->push_queue[] = $supplier_array;
                if ( $orderRefund->item_uid <> $orderRefund->goods_uid ) {
                    //给分销商发送订单通知
                    $seller_array = array(
                        'uid' => $orderRefund->item_uid,
                        'mobile' => $orderRefund->item_mobile,
                        'message' => "<供销订单|拒绝退款>供应商拒绝买家{$orderRefund->consignee}，订单号：{$orderRefund->order_sn}的退款申请（￥{$orderRefund->money}元）。交易中有任何问题，请联系{$this->configCache['cfg_webname']}。"
                    );
                    $this->push_queue[] = $seller_array;
                }
                if ( $this->checkIsDemoOrder( $orderRefund ) == false ) {
                    //给买家发送付款成功通知
                    $buyer_array = array(
                        'uid' => $orderRefund->uid,
                        'mobile' => $orderRefund->mobile,
                        'message' => "<拒绝退款>卖家<{$orderRefund->shop_name}>{$orderRefund->item_mobile}已经拒绝订单号：{$orderRefund->order_sn}的退款申请（￥{$orderRefund->money}元）。您可以登录的会员中心，维权订单中查看详情，你还可以选择[取消退款]或[申请客服介入]。交易中有任何问题，请联系{$this->configCache['cfg_webname']}。"
                    );
                    $this->push_queue[] = $buyer_array;
                }
                break;
            case self::message_type_settle:
                if ( empty( $this->settleInfo ) ) {
                    $this->messageType = '提现详情不能为空';
                    return false;
                }
                if ( $settleInfo->account_type == service_settle_Save_base::default_account_type_bank ) {
                    $bank_id_array = Tmac::config( 'member.member_setting.bank_id', APP_MANAGE_NAME );
                    $bank_name = $bank_id_array[ $settleInfo->bank_id ];
                    $account_text = "提现银行：{$bank_name}，提现账号：{$settleInfo->bank_cardnum}，提现真实姓名：{$settleInfo->bank_account}";
                } else {
                    $account_text = "提现类型:[{$account_type_text}]，提现账号：{$settleInfo->alipay_account},提现真实姓名：{$settleInfo->alipay_username}";
                }

                $supplier_array = array(
                    'uid' => $settleInfo->uid,
                    'mobile' => $settleInfo->mobile,
                    'message' => "<申请提现>{$this->configCache['cfg_webname']}已经收到您申请提现￥{$settleInfo->money}元，{$account_text},请等待财务处理"
                );
                $this->push_queue[] = $supplier_array;
                break;
            case self::message_type_settle_success:
                if ( empty( $this->settleInfo ) ) {
                    $this->messageType = '提现详情不能为空';
                    return false;
                }
                if ( $settleInfo->account_type == service_settle_Save_base::default_account_type_bank ) {
                    $bank_id_array = Tmac::config( 'member.member_setting.bank_id', APP_MANAGE_NAME );
                    $bank_name = $bank_id_array[ $settleInfo->bank_id ];
                    $account_text = "{$this->configCache['cfg_webname']}已经向{$settleInfo->bank_account}的[{$account_type_text}]，提现账号：{$settleInfo->bank_cardnum}}";
                } else {
                    $account_text = "{$this->configCache['cfg_webname']}已经向{$settleInfo->alipay_username}的支付宝账号，提现账号：{$settleInfo->alipay_account}}";
                }

                $supplier_array = array(
                    'uid' => $settleInfo->uid,
                    'mobile' => $settleInfo->mobile,
                    'message' => "<提现成功>您的提现申请已经处理，{$account_text}存入了￥{$settleInfo->money}元。请注意查收。"
                );
                $this->push_queue[] = $supplier_array;
                break;
            case self::message_type_settle_fail:
                if ( empty( $this->settleInfo ) ) {
                    $this->messageType = '提现详情不能为空';
                    return false;
                }

                $supplier_array = array(
                    'uid' => $settleInfo->uid,
                    'mobile' => $settleInfo->mobile,
                    'message' => "<提现被拒绝>您申请提现￥{$settleInfo->money}元被拒绝，如有疑问请联系{$this->configCache['cfg_webname']}客服。"
                );
                $this->push_queue[] = $supplier_array;
                break;


            default:
                $this->messageType = '推送的消息类型不正确';
                return false;
        }

        $filterMobileArray = array_merge( $this->buyerMobileArray, $this->itemMobileArray );
        //执行推送 $this->push_queue;
        //设计模式观察者模式
        foreach ( $this->push_queue as $push ) {
            //过滤不发的短信
            if ( in_array( $push[ 'mobile' ], $filterMobileArray ) ) {
                continue;
            }
            //TODO app push
            //短信发送开始
            if ( $sms_push == false ) {
                $is_only_push = 1;
            } else {
                $is_only_push = 0;
            }

            //写到sms_log表中
            $entity_SmsLog_base = new entity_SmsLog_base();
            $entity_SmsLog_base->sms_type = service_Account_base::sms_type_message;
            $entity_SmsLog_base->sms_code = '';
            $entity_SmsLog_base->sms_mobile = $push[ 'mobile' ];
            $entity_SmsLog_base->sms_content = $push[ 'message' ];
            $entity_SmsLog_base->sms_time = $this->now;
            $entity_SmsLog_base->sms_linked_id = $push[ 'uid' ];
            $entity_SmsLog_base->sms_ip = Functions::get_client_ip();
            $entity_SmsLog_base->result_code = '';
            $entity_SmsLog_base->sms_success = 0;
            $entity_SmsLog_base->is_only_push = $is_only_push;

            $dao = dao_factory_base::getSmsLogDao();
            $res = $dao->insert( $entity_SmsLog_base );
            if ( !$res ) {
                Log::getInstance( 'sms_error' )->write( var_export( $entity_SmsLog_base, true ) );
            }
            //Log::getInstance( 'push_message' )->write( 'sms|' . $this->orderInfo . '|' . $this->messageType . '|' . $sms_model->getErrorMessage() . var_export( $push, true ) );            
        }

        return true;
    }

    private function checkIsDemoOrder( $orderRefund )
    {
        $orderRefund instanceof entity_OrderRefund_base;
        $dao = dao_factory_base::getOrderInfoDao();
        $dao->setPk( $orderRefund->order_id );
        $dao->setField( 'demo_order' );
        $orderInfo = $dao->getInfoByPk();
        if ( $orderInfo->demo_order == service_Order_base::demo_order_yes ) {
            return true;
        } else {
            return false;
        }
    }

    private function handleFreeSupplierOrderShow( $orderInfo )
    {
        $orderInfo instanceof entity_OrderInfo_base;
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $orderInfo->goods_uid );
        $dao->setField( 'member_type,member_class' );
        $memberInfo = $dao->getInfoByPk();

        $supplier_array = array();
        if ( $memberInfo->member_type == service_Member_base::member_type_supplier && $memberInfo->member_class == service_Member_base::member_class_supplier_free ) {
            if ( isset( $orderInfo->mobile ) ) {
                //免费供应商，不能看到手机号全部                
                $supplier_array[ 'mobile' ] = substr_replace( $orderInfo->mobile, '****', 3, 4 );
            }

            if ( isset( $orderInfo->full_address ) ) {
                $address_length = mb_strlen( $orderInfo->full_address, 'utf8' );
                $supplier_array[ 'full_address' ] = str_repeat( '*', $address_length ) . "(请联系{$this->configCache['cfg_webname']}客服4008-456-090查看该订单详情)";
            }
        }
        return $supplier_array;
    }

    private function handleFreeSupplierRefundOrderShow( $orderRefund )
    {
        $orderRefund instanceof entity_OrderRefund_base;
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $orderRefund->goods_uid );
        $dao->setField( 'member_type,member_class' );
        $memberInfo = $dao->getInfoByPk();

        if ( $memberInfo->member_type == service_Member_base::member_type_supplier && $memberInfo->member_class == service_Member_base::member_class_supplier_free ) {
            if ( isset( $orderRefund->mobile ) ) {
                //免费供应商，不能看到手机号全部                
                $orderRefund->mobile = substr_replace( $orderRefund->mobile, '****', 3, 4 );
            }
        }
        return $orderRefund;
    }

}
