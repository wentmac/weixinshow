<?php

/**
 * 后台 所有的档案管理 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: archives.php 334 2016-06-02 09:33:10Z zhangwentao $
 * http://www.t-mac.org；
 */
class archivesAction extends service_Controller_admin
{

    private $tmp_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
        $this->tmp_model = Tmac::model( 'article' );
        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer' );
    }

    /**
     * 模型跳转 发布内容
     */
    public function catgoto()
    {
        $channelid = intval( $this->getParam( 'channelid' ) );
        $cat_id = intval( $this->getParam( 'cat_id' ) );
        //默认文章调用发布表单
        if ( empty( $cat_id ) && empty( $channelid ) ) {
            header( "location:" . PHP_SELF . "?m=article.add&channelid={$channelid}&cat_id={$cat_id}" );
            exit();
        }
        if ( !empty( $channelid ) ) {
            $channeltype_addcon = Tmac::config( 'channel.channeltype_addcon' );
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
        $channelid = intval( $this->getParam( 'channelid' ) );
        $cat_id = intval( $this->getParam( 'cat_id' ) );
        $page = intval( $this->getParam( 'page' ) );

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
            $channeltype_addcon = Tmac::config( 'channel.channeltype_addcon' );
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
        $channeltype = Tmac::config( 'channel.channeltype' );
        $this->assign( 'channeltype', $channeltype );
        $this->V( 'archives' );
    }

    /**
     * 模型跳转 发布内容
     */
    public function edit()
    {
        $channelid = intval( $this->getParam( 'channelid' ) );
        $aid = intval( $this->getParam( 'aid' ) );
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
            $channeltype_addcon = Tmac::config( 'channel.channeltype_addcon' );
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
