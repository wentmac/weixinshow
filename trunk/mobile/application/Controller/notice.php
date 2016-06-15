<?php

/**
 * 前台 云端商品库 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: notice.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class noticeAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
        $this->checkSign();
    }

    public function index()
    {
        $notice_id = Input::get( 'id', 0 )->required( '消息不能为空' )->int();

        if ( Filter::getStatus() === false ) {
            self::no( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        $model = new service_Notice_api();
        $res = $model->getNoticeInfoById( $notice_id );
        if ( $res == false ) {
            self::no( '不存在的通知' );
        }
        $this->assign( 'notice_info', $res );
        $this->V( 'notice_detail' );
    }

}
