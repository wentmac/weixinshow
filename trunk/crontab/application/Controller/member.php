<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: member.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class memberAction extends service_Controller_crontab
{

    public function __construct()
    {
        parent::__construct();
        set_time_limit( 0 );
    }

    public function index()
    {
        
    }

    /**
     * 
     * /usr/local/php-5.4.20/bin/php -f /var/www/www.090.cn/release_1.0/crontab/wwwroot/cli.php "m=member.update_seller_count"
     * 更新供应商店铺的多少个在卖     
     */
    public function update_seller_count()
    {
        $model = new service_shop_Variable_crontab();
        $res = $model->updateShopSellerCount();
        print_r( $res );
    }

    /**
     * 
     * /usr/local/php-5.4.20/bin/php -f /var/www/www.090.cn/release_1.0/crontab/wwwroot/cli.php "m=member.update_collect_count"
     * 更新供应商店铺的多少个在卖     
     */
    public function update_collect_count()
    {
        $model = new service_shop_Variable_crontab();
        $res = $model->updateShopCollectCount();
        print_r( $res );
    }

    /**
     * 
     * /usr/local/php-5.4.20/bin/php -f /var/www/yph/release_1.0/crontab/wwwroot/cli.php "m=member.get_member_temp"
     * 抓取老会员     
     */
    public function get_member_temp()
    {
        $model = new service_MemberTemp_crontab();
        $res = $model->getMemberTempArray();
        print_r( $res );
    }

    /**
     * 
     * /usr/local/php-5.4.20/bin/php -f /var/www/yph/release_1.0/crontab/wwwroot/cli.php "m=member.update_member_temp"
     * 抓取老会员     
     */
    public function update_member_temp()
    {
        ini_set( 'memory_limit', '2048M' );


        $model = new service_MemberImport_crontab();
        //从member_temp表中导入到member表
        //$model->importMember();
        //excel中导入原来的级别和第一次购买会员的时间
        //$res = $model->updateMemberTemp();
        //更新member表中的AgentUid
        //$model->updateMemberAgentUid();
        //die;
        /**
         * 开始设置排位
         * 根据第一次购买lv1的时间来设置Rank的排位
         */
        $model->setMemberAgentRank();
        print_r( $res );
    }

}
