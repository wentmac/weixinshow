<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_goods_Agent_base extends service_Model_base
{

    protected $uid;
    protected $errorMessage;
    protected $goods_agent_array;
    protected $goods_map;
    protected $memberInfo;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 取出计算后的售价     
     * $this->setUid($uid);     
     * $goodsArray中必须有goods_id,goods_price|price,commission_fee
     * $this->getHandleGoodsPrice($goodsArray);     
     */
    public function getHandleGoodsAgentPrice( $goodsArray )
    {
        if ( empty( $goodsArray ) ) {
            return $goodsArray;
        }
        $this->memberInfo = $this->getMemberAgent();
        if ( is_array( $goodsArray ) ) {
            foreach ( $goodsArray as $goodsInfo ) {
                $this->getGoodsPrice( $goodsInfo );
            }
        } else {
            $goodsArray = $this->getGoodsPrice( $goodsArray );
        }
        return $goodsArray;
    }

    /**
     * 取一组sku商品的特殊佣金
     * @param type $goodsInfo
     * @return type
     */
    public function getHandleSkuAgentPrice( $goodsArray )
    {
        if ( empty( $goodsArray ) ) {
            return $goodsArray;
        }
        $this->memberInfo = $this->getMemberAgent();
        $goods_id_array = array();
        foreach ( $goodsArray as $goods_id => $goods_sku_array ) {
            $goods_id_array[] = $goods_id;
        }
        $goods_id_string = implode( ',', $goods_id_array );
        $goods_array = $this->getGoodsArray( $goods_id_string );
        foreach ( $goodsArray as $goods_id => $goods_sku_array ) {
            $goods_info = $goods_array[ $goods_id ];
            foreach ( $goods_sku_array as $goods_sku ) {
                $goods_sku->goods_agent = $goods_info->goods_agent;
                $this->getGoodsPrice( $goods_sku );
            }
        }
        //var_dump( $goodsArray );
        //die;
        return $goodsArray;
    }

    /**
     * 取一条sku商品的特殊佣金
     * @param type $goodsInfo
     * @return type
     */
    public function getHandleSkuDetailAgentPrice( $goods_info )
    {
        if ( empty( $goods_info ) ) {
            return $goods_info;
        }
        $this->memberInfo = $this->getMemberAgent();
        $goods_id_string = $goods_info->goods_id;
        $goods_array = $this->getGoodsArray( $goods_id_string );
        $goods_detail_info = $goods_array[ $goods_info->goods_id ]; //包含goods的uid和goods_cat_id的        

        $goods_info->goods_agent = $goods_detail_info->goods_agent;
        $this->getGoodsPrice( $goods_info );
        return $goods_info;
    }

    /**
     * 取一条商品的特殊价
     * @param type $goodsInfo
     * @return type
     */
    private function getGoodsPrice( $goodsInfo )
    {
        //处理一下后台特殊佣金设置
        if ( empty( $goodsInfo->goods_agent ) ) {
            return $goodsInfo;
        }
        if ( $this->memberInfo->member_type <> service_Member_base::member_type_mall ) {
            return $goodsInfo;
        }
        $goods_agent = json_decode( $goodsInfo->goods_agent, true );
        if ( empty( $goods_agent ) ) {
            return $goodsInfo;
        }
        $goods_agent_map = $this->getGoodsAgentMap( $goods_agent );
        //var_dump( $goodsInfo );
        //var_dump( $goods_agent_map[ $memberInfo->member_class ] );
        if ( !empty( $goods_agent_map[ $this->memberInfo->member_class ] ) ) {
            $price_info = $goods_agent_map[ $this->memberInfo->member_class ];
            if ( isset( $goodsInfo->goods_price ) ) {
                $source_price = $goodsInfo->goods_price;
                $goodsInfo->price_source = $source_price;
                $goodsInfo->goods_price = $price_info[ 'price' ];
                $goodsInfo->commission_fee_source = $goodsInfo->commission_fee;
                $goodsInfo->commission_fee = $source_price - $price_info[ 'price' ];
            } else if ( isset( $goodsInfo->price ) && isset( $price_info[ 'sku_array' ][ $goodsInfo->goods_sku_id ] ) ) {//sku的          
                $price_sku_info = $price_info[ 'sku_array' ][ $goodsInfo->goods_sku_id ];
                $source_price = $goodsInfo->price;
                if ( !empty( $price_sku_info[ 'price' ] ) ) {
                    $goodsInfo->price_source = $source_price;
                    $goodsInfo->price = $price_sku_info[ 'price' ];
                    $goodsInfo->commission_fee_source = $goodsInfo->commission_fee;
                    $goodsInfo->commission_fee = $source_price - $price_sku_info[ 'price' ];
                }
            }
            //减的价格从佣金中扣除

            $goodsInfo->commission_fee < 0 ? 0 : $goodsInfo->commission_fee;
        }
        //var_dump( $goodsInfo );
        //die;        
        return $goodsInfo;
    }

    private function getGoodsArray( $goods_id_string )
    {

        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id,uid,goods_cat_id,goods_agent' );
        $where = $dao->getWhereInStatement( 'goods_id', $goods_id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $goods_array_map = array();
        if ( $res ) {
            foreach ( $res as $value ) {
                $goods_array_map[ $value->goods_id ] = $value;
            }
        }
        return $goods_array_map;
    }

    private function getGoodsAgentMap( $goods_agent )
    {
        $res = $agent = array();
        foreach ( $goods_agent as $value ) {
            $agent = $value;
            $sku_res = array();
            foreach ( $value[ 'sku_array' ] as $sku ) {
                $sku_res[ $sku[ 'sku_id' ] ] = $sku;
            }
            $agent[ 'sku_array' ] = $sku_res;
            $res [ $value[ 'member_agent' ] ] = $agent;
        }

        return $res;
    }

    /**
     * 取用户的代理商级别
     */
    private function getMemberAgent()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'member_type,member_class' );
        $dao->setPk( $this->uid );
        $memberInfo = $dao->getInfoByPk();
        return $memberInfo;
    }

}
