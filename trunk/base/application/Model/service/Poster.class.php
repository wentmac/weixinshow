<?php

/**
 * WEB 后管理 学校模块
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhuqiang
 * $Id: School.class.php 6 2014-10-01 15:13:57Z 
 * http://www.t-mac.org；
 */
class service_Poster_base extends service_Model_base
{

    /**
     * 广告类型
     * 文字广告
     */
    const poster_type_radio_text = 1;

    /**
     * 广告类型
     * 图片文字类型广告
     */
    const poster_type_radio_image = 2;

    /**
     * 广告期限
     * 正常
     */
    const poster_state_radio_general = 1;

    /**
     * 广告期限
     * 永久
     */
    const poster_state_radio_permanent = 2;

    /**
     * 广告期限
     * 暂停
     */
    const poster_state_radio_pause = 3;

    protected $errorMessage;
    protected $mall_uid;
    protected $image_size = '110';

    function setMall_uid( $mall_uid )
    {
        $this->mall_uid = $mall_uid;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getPosterInfo( $poster_id )
    {
        $dao = dao_factory_base::getPosterDao();
        $dao->setPk( $poster_id );
        return $dao->getInfoByPk();
    }

    /**
     * 取详细调用的数据
     * $poster_model->setMall_uid($uid);
     * @param type $poster_id
     * @return type
     */
    public function getPosterDetail( $poster_name, $image_size = '600x200' )
    {
        $res = array();
        $dao = dao_factory_base::getPosterDao();
        if ( !empty( $this->mall_uid ) ) {
            $where = "uid={$this->mall_uid} AND poster_name='{$poster_name}' AND is_delete=0";
            $dao->setWhere( $where );
            $res = $dao->getInfoByWhere();
        }

        if ( empty( $res ) ) {
            $where = "uid=0 AND poster_name='{$poster_name}' AND is_delete=0";
            $dao->setWhere( $where );
            $res = $dao->getInfoByWhere();
        }
        if ( empty( $res ) ) {
            $this->errorMessage = '广告不存在';
            return FALSE;
        }

        $img = $this->getCfgBody( 'img', $res->poster_imgurls );
        $imgurl_array = $img[ 5 ];
        $self_field_array = $img[ 4 ];
        $sort_array = $img[ 3 ];
        $thumburl_array = $img[ 2 ];
        $thumbtitle_array = $img[ 1 ];

        arsort( $sort_array );
        $poster_array = $res = array();
        foreach ( $sort_array as $key => $value ) {
            if ( $value > 100 ) {
                continue;
            }
            $res[ 'url' ] = $thumburl_array[ $key ];
            $res[ 'title' ] = $thumbtitle_array[ $key ];
            $res[ 'self_field' ] = $self_field_array[ $key ];
            $res[ 'img_url' ] = $this->getImage( $imgurl_array[ $key ], $image_size, 'poster' );
            $poster_array[] = $res;
        }


        return $poster_array;
    }

    public function createPoster( entity_Poster_base $entity_Poster_base )
    {
        $dao = dao_factory_base::getPosterDao();
        return $dao->insert( $entity_Poster_base );
    }

    public function modifyPoster( entity_Poster_base $entity_Poster_base )
    {
        $dao = dao_factory_base::getPosterDao();
        $dao->setPk( $entity_Poster_base->poster_id );
        return $dao->updateByPk( $entity_Poster_base );
    }

    /**
     * del
     * @param int $class_id
     */
    public function deletePosterId( $poster_name )
    {
        $dao = dao_factory_base::getPosterDao();

        $dao->getDb()->startTrans();
        $entity_Poster_base = new entity_Poster_base();
        $entity_Poster_base->is_delete = 1;
        $dao->setWhere( "uid={$this->mall_uid} AND poster_name='{$poster_name}'" );
        $dao->updateByWhere( $entity_Poster_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    //取出configbody字段里的值
    public function getCfgBody( $value, $configbody )
    {
        preg_match_all( '/{' . $value . ' key="(.*)" url="(.*)" sort="(.*)" self_field="(.*)"}(.*){\/' . $value . '}/isU', $configbody, $result );
        return $result;
    }

    /**
     * 
     * @param type $value
     * @param type $configbody
     * @return type
     */
    public function getImageUrlArray( $image_id_array )
    {
        $image_url_array = array();
        if ( empty( $image_id_array ) ) {
            return $image_url_array;
        }
        foreach ( $image_id_array as $value ) {
            $image_url_array[] = $this->getImage( $value, '600x200', 'poster' );
        }
        return $image_url_array;
    }

    /**
     * 取出poster广告位中商品的详细数据
     * @param type $poster_array
     */
    public function getPosterGoodsArray( $poster_array, $mall_uid, $image_size = 300 )
    {
        $goods_id_array = array();
        foreach ( $poster_array as $value ) {
            if ( preg_match_all( '/goods\/(\d+).html/isU', $value[ 'url' ], $result ) ) {
                $goods_id_array[] = $result[ 1 ][ 0 ];
            }
        }
        $res = array();
        if ( empty( $goods_id_array ) ) {
            return $res;
        }


        $goods_id_string = implode( ',', $goods_id_array );
        $dao = dao_factory_base::getGoodsDao();
        $dao->setField( 'goods_id,goods_name,promote_price,goods_price,goods_image_id,commission_fee,goods_country_id' );
        $where = $dao->getWhereInStatement( 'goods_id', $goods_id_string );
        $dao->setWhere( $where );
        $dao->setOrderby( 'goods_sort DESC,sales_volume DESC' );
        $res = $dao->getListByWhere();
        $goods_country_id_array = Tmac::config( 'goods.goods.goods_country_id', APP_BASE_NAME );
        foreach ( $res as $value ) {
            $value->goods_image_url = $this->getImage( $value->goods_image_id, $image_size, 'goods' );
            $value->goods_country_id_name = empty( $value->goods_country_id ) ? '' : $goods_country_id_array[ $value->goods_country_id ];
        }

        $goods_price_model = new service_goods_Price_base();
        $goods_price_model->setUid( $mall_uid );
        $goods_price_model->getHandleGoodsPrice( $res );
        return $res;
    }

}
