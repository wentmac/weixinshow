<?php

/**
 * 后台 所有的档案管理 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: archives.php 335 2016-06-02 09:34:09Z zhangwentao $
 * http://www.t-mac.org；
 */
class archivesAction extends service_Controller_manage
{

    private $tmp_model;
    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        parent::__construct();
        $this->checkLogin();
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
    }

    /**
     * 模型跳转 发布内容
     */
    public function catgoto()
    {
        $channelid = Input::get( 'channelid', 0 )->int();
        $cat_id = Input::get( 'cat_id', 0 )->int();
        //默认文章调用发布表单
        if ( empty( $cat_id ) && empty( $channelid ) ) {
            header( "location:" . PHP_SELF . "?m=article.add&channelid={$channelid}&cat_id={$cat_id}" );
            exit();
        }
        if ( !empty( $channelid ) ) {
            $channeltype_addcon = Tmac::config( 'channel.channeltype_addcon', APP_ADMIN_NAME );
        }
        $gurl = $channeltype_addcon[ $channelid ];
        if ( $gurl == "" ) {
            $this->redirect( "对不起，你指的栏目可能有误！", PHP_SELF . "?m=category" );
            exit();
        }

        //跳转并传递参数
        header( "location:" . PHP_SELF . "?m={$gurl}.add&channelid={$channelid}&cat_id={$cat_id}" );
        exit();
    }

    /**
     * 模型跳转 列表
     */
    public function arclist()
    {
        $channelid = Input::get( 'channelid', 0 )->int();
        $cat_id = Input::get( 'cat_id', 0 )->int();
        $page = Input::get( 'page', 0 )->int();

        if ( !empty( $page ) ) {
            $pageurl = '&page=' . $page;
        } else {
            $pageurl = '';
        }
        //默认文章调用发布表单
        if ( empty( $channelid ) ) {
            header( "location:" . PHP_SELF . "?m=article{$pageurl}&cat_id={$cat_id}{$pageurl}" );
            exit();
        }
        if ( !empty( $channelid ) ) {
            $channeltype_addcon = Tmac::config( 'channel.channeltype_addcon', APP_ADMIN_NAME );
        }
        $gurl = $channeltype_addcon[ $channelid ];
        if ( $gurl == "" ) {
            $this->redirect( "对不起，你指的栏目可能有误！", PHP_SELF . "?m=category" );
            exit();
        }

        //跳转并传递参数
        header( "location:" . PHP_SELF . "?m={$gurl}&channelid={$channelid}&cat_id={$cat_id}{$pageurl}" );
        exit();
    }

    /**
     * 发布档案
     */
    public function add()
    {
        $channeltype = Tmac::config( 'channel.channeltype', APP_ADMIN_NAME );
        $this->assign( 'channeltype', $channeltype );
        $this->V( 'archives' );
    }

    /**
     * 模型跳转 发布内容
     */
    public function edit()
    {
        $channelid = Input::get( 'channelid', 0 )->int();
        $aid = Input::get( 'aid', 0 )->int();
        if ( empty( $aid ) ) {
            $this->redirect( '请先选择要修改的文档' );
            exit();
        }
        //默认文章调用发布表单
        if ( empty( $channelid ) ) {
            header( "location:" . PHP_SELF . "?m=article.add&channelid={$channelid}&aid={$aid}" );
            exit();
        }
        if ( !empty( $channelid ) ) {
            $channeltype_addcon = Tmac::config( 'channel.channeltype_addcon', APP_ADMIN_NAME );
        }
        $gurl = $channeltype_addcon[ $channelid ];
        if ( $gurl == "" ) {
            $this->redirect( "对不起，你指的栏目可能有误！", PHP_SELF . "?m=archives.arclist" );
            exit();
        }
        //跳转并传递参数
        header( "location:" . PHP_SELF . "?m={$gurl}.add&channelid={$channelid}&aid={$aid}" );
        exit();
    }

}
