<?php

/**
 * 订单售后 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Comment.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_order_Comment_base extends service_Model_base
{

    protected $order_id;
    protected $uid;
    protected $username;
    protected $errorMessage;
    protected $orderInfo;
    protected $orderGoodsArray;

    function setOrder_id( $order_id )
    {
        $this->order_id = $order_id;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setUsername( $username )
    {
        $this->username = $username;
    }

    function setOrderInfo( $orderInfo )
    {
        $this->orderInfo = $orderInfo;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测买家的操作订单权限     
     * $this->uid;     
     * $this->createGoodsComment($param_array);
     * @return type
     * @throws TmacClassException
     */
    public function createGoodsComment( $param_array )
    {
        $this->checkBuyerPriview( $param_array );

        $order_goods_dao = dao_factory_base::getOrderGoodsDao();
        $order_comment_dao = dao_factory_base::getGoodsCommentDao();
        $goods_dao = dao_factory_base::getGoodsDao();
        $item_dao = dao_factory_base::getItemDao();

        $order_goods_dao->getDb()->startTrans();

        //判断是不是所有的商品订单都评价过
        $goods_comment_count = $this->getGoodsCommentedCount();
        $order_goods_count = count( unserialize( $this->orderInfo->order_goods_detail ) );
        $current_comment_count = count( $param_array ); //本次评价的总数
        if ( $order_goods_count - $goods_comment_count == $current_comment_count ) {//所有的商品已经评价
            $entity_OrderInfo_base = new entity_OrderInfo_base();
            $entity_OrderInfo_base->comment_status = 1;
            $order_info_dao = dao_factory_base::getOrderInfoDao();
            $order_info_dao->setPk( $this->orderInfo->order_id );
            $order_info_dao->updateByPk( $entity_OrderInfo_base );
        }

        $ip = Functions::get_client_ip();
        foreach ( $param_array as $order_goods_id => $value ) {
            $orderGoods = $this->orderGoodsArray[ $order_goods_id ];
            //写入goods_comment表
            $entity_GoodsComment_base = new entity_GoodsComment_base();
            $entity_GoodsComment_base->item_id = $orderGoods->item_id;
            $entity_GoodsComment_base->goods_id = $orderGoods->goods_id;
            $entity_GoodsComment_base->order_id = $orderGoods->order_id;
            $entity_GoodsComment_base->order_goods_id = $orderGoods->order_goods_id;
            $entity_GoodsComment_base->uid = $this->orderInfo->uid;
            $entity_GoodsComment_base->username = $this->username;
            $entity_GoodsComment_base->content = $value->content;
            $entity_GoodsComment_base->comment_rank = $value->rank;
            $entity_GoodsComment_base->add_time = $this->now;
            $entity_GoodsComment_base->ip_address = $ip;

            $order_comment_dao->insert( $entity_GoodsComment_base );

            //更新order_goods表的状态
            $entity_OrderGoods_base = new entity_OrderGoods_base();
            $entity_OrderGoods_base->comment_status = 1;
            $order_goods_dao->setPk( $order_goods_id );
            $order_goods_dao->updateByPk( $entity_OrderGoods_base );
            //更新 goods 表的评论总数
            $entity_Goods_base = new entity_Goods_base();
            $entity_Goods_base->comment_count = new TmacDbExpr( 'comment_count+1' );
            $goods_dao->setPk( $orderGoods->order_id );
            $goods_dao->updateByPk( $entity_Goods_base );
            //更新 item 表的评论总数
            $entity_Item_base = new entity_Item_base();
            $entity_Item_base->comment_count = new TmacDbExpr( 'comment_count+1' );
            $item_dao->setPk( $orderGoods->item_id );
            $item_dao->updateByPk( $entity_Item_base );
        }

        if ( $order_goods_dao->getDb()->isSuccess() ) {
            $order_goods_dao->getDb()->commit();
            return true;
        } else {
            $order_goods_dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 取订单已经评论过的商品总数
     */
    private function getGoodsCommentedCount()
    {
        $order_comment_dao = dao_factory_base::getGoodsCommentDao();
        $where = "order_id={$this->order_id} AND is_delete=0";
        $order_comment_dao->setWhere( $where );
        return $order_comment_dao->getCountByWhere();
    }

    /**
     * 检测买家的操作订单权限
     * $this->order_goods_id;
     * $this->uid;
     * $this->checkBuyerPriview();
     * @return type
     * @throws TmacClassException
     */
    private function checkBuyerPriview( $param_array )
    {
        $order_goods_id_array = array();
        foreach ( $param_array as $order_goods_id => $value ) {
            $order_goods_id_array[] = $order_goods_id;
        }
        $order_goods_id_string = implode( ',', $order_goods_id_array );

        $order_goods_dao = dao_factory_base::getOrderGoodsDao();
        $where = 'order_id=' . $this->order_id . ' AND ' . $order_goods_dao->getWhereInStatement( 'order_goods_id', $order_goods_id_string );
        $order_goods_dao->setWhere( $where );
        $order_goods_array = $order_goods_dao->getListByWhere();

        if ( empty( $order_goods_array ) ) {
            throw new TmacClassException( '只能评价自己的订单商品哟' );
        }

        foreach ( $order_goods_array as $order_goods_info ) {
            if ( $order_goods_info->comment_status == 1 ) {
                throw new TmacClassException( '订单商品已经评价过喽' );
            }
            $this->orderGoodsArray[ $order_goods_info->order_goods_id ] = $order_goods_info;
        }
        $orderInfo = $this->orderInfo;
        if ( $orderInfo->order_type == service_Order_base::order_type_member ) {
            throw new TmacClassException( '收银台商品不能评价' );
        }
        if ( $orderInfo->uid <> $this->uid ) {
            throw new TmacClassException( '只能评论自己订单哟' );
        }
        return true;
    }

    /**
     * 取订单所有未评价过的订单商品
     * $this->uid;
     * $this->order_id;
     * $this->getOrderGoodsUnCommentArray();
     */
    public function getOrderGoodsUnCommentArray()
    {
        $dao = dao_factory_base::getOrderGoodsDao();
        $dao->setWhere( "order_id={$this->order_id} AND comment_status=0" );
        $dao->setField( 'order_goods_id,item_id,item_name,item_number,item_price,goods_image_id,goods_sku_name' );
        $res = $dao->getListByWhere();
        if ( $res ) {
            foreach ( $res as $value ) {
                $value->goods_image_id = $this->getImage( $value->goods_image_id, '110', 'goods' );
            }
        }
        return $res;
    }

}
