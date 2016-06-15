<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_DownloadStatistic_base extends service_Model_base
{

    /**
     * App类型
     * Android
     */
    const app_type_android = 1;

    /**
     * App类型
     * Ios
     */
    const app_type_ios = 2;

    /**
     * App渠道联盟
     * 智慧推
     */
    const union_zhihuitui = 1;

    /**
     * 渠道联盟
     * 百度
     */
    const union_bd = 2;

    protected $app_type = self::app_type_android;
    protected $union_id = self::union_zhihuitui;

    function setApp_type( $app_type )
    {
        $this->app_type = $app_type;
    }

    function setUnion_id( $union_id )
    {
        $this->union_id = $union_id;
    }

    protected $errorMessage;

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function createDownloadStatistic()
    {
        $entity_DownloadStatistic_base = new entity_DownloadStatistic_base();
        $entity_DownloadStatistic_base->app_type = $this->app_type;
        $entity_DownloadStatistic_base->download_ip = Functions::get_client_ip();
        $entity_DownloadStatistic_base->download_time = $this->now;
        $entity_DownloadStatistic_base->union_id = $this->union_id;
        $entity_DownloadStatistic_base->referer_url = isset( $_SERVER [ 'HTTP_REFERER' ] ) ? filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING ) : '';
        $entity_DownloadStatistic_base->download_date = date( 'Y-m-d', $this->now );        
        $dao = dao_factory_base::getDownloadStatisticDao();
        return $dao->insert( $entity_DownloadStatistic_base );
    }

}
