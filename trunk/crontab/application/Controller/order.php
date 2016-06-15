<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: order.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class orderAction extends service_Controller_crontab
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        
    }

    /**
     * 
     * /usr/local/php-5.4.20/bin/php -f /var/www/yph/release_1.0/crontab/wwwroot/cli.php "m=order.update_current_money"
     * 更新 确认收货后的7天订单的 收入状态
     * 
     */
    public function update_current_money()
    {
        $model = new service_order_Finish_crontab();
        $res = $model->executeMemberCurrentMoneyUpdate();

        print_r( $res );
    }

    /**
     * 
     * /usr/local/php-5.4.20/bin/php -f /var/www/yph/release_1.0/crontab/wwwroot/cli.php "m=order.update_receivable_current_money"
     * 更新 收银台 的待确认 状态到可提现余额中 收入状态
     * 
     */
    public function update_receivable_current_money()
    {
        $model = new service_order_Finish_crontab();
        $res = $model->executeMemberReceivableCurrentMoneyUpdate();

        print_r( $res );
    }

    /**
     * 订单自动确认
     * /usr/local/php-5.4.20/bin/php -f /var/www/yph/release_1.0/crontab/wwwroot/cli.php "m=order.auto_order_confirm"
     * 更新 确认发货后7天订单的
     */
    public function auto_order_confirm()
    {
        $model = new service_order_Confirm_crontab();
        $res = $model->executeOrderConfirm();

        print_r( $res );
    }

    /**
     * 消息push 短信发送
     * /usr/local/php-5.4.20/bin/php -f /var/www/yph/release_1.0/crontab/wwwroot/cli.php "m=order.push_message"
     * 1分钟执行一次
     */
    public function push_message()
    {        
        $model = new service_PushMessage_crontab();
        $res = $model->push_execute();

        print_r( $res );
    }

}
