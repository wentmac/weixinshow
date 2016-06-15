<?php

/**
 * 后台管理员模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: user.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class userAction extends service_Controller_admin
{

    private $tmp_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
        $this->tmp_model = Tmac::model( 'User' );
        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer' );
    }

    /**
     * 管理员类别管理 首页
     */
    public function index()
    {
        //TODO  取出所有管理员
        $entity_parameter_Common_base = new entity_parameter_Common_base();
        $entity_parameter_Common_base->setPagesize( 10 );
        $rs = $this->tmp_model->getUserList( $entity_parameter_Common_base );
        $this->assign( $rs );
        $this->V( 'user' );
    }

    /**
     * 新增/修改管理员
     */
    public function add()
    {
        $id = Input::get( 'uid', 0 )->int();
        $editinfo = array();

        $entity_User_base = new entity_User_base();

        if ( $id > 0 ) {
            $entity_User_base = $this->tmp_model->getUserInfo( $id );
        }

        //取管理员类型option数组
        $admintype_ary = $this->tmp_model->getAdminType();
        $admin_type_option = Utility::OptionObject( $admintype_ary, $entity_User_base->rank, 'rank,type_name' );

        $this->assign( 'editinfo', $entity_User_base );
        $this->assign( 'admin_type_option', $admin_type_option );
        $this->V( 'user' );
    }

    /**
     * 新增/修改管理员页面　保存　
     */
    public function save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 3 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }

        /* 初始化变量 */
        $uid = Input::post( 'uid', 0 )->int();
        $username = Input::post( 'username', '' )->required( '管理员登录不能为空' )->string();
        $nicename = Input::post( 'nicename', '' )->required( '管理员真实姓名不能为空' )->string();
        $password = Input::post( 'password', '' )->required( '管理员真实姓名不能为空' )->password();
        $email = Input::post( 'email', '' )->email();
        $rank = Input::post( 'rank', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        // TODO goon to verify
        //验证id和admin不能重复
        $userinfo = $this->tmp_model->checkUserName( $username, $uid );
        if ( $userinfo ) {
            $this->redirect( '管理员名称重复了' );
        }

        $entity_User_base = new entity_User_base();
        $entity_User_base->username = $username;
        $entity_User_base->password = md5( md5( $password ) );
        $entity_User_base->nicename = $nicename;
        $entity_User_base->email = $email;
        $entity_User_base->rank = $rank;
        $entity_User_base->reg_ip = Functions::get_client_ip();
        $entity_User_base->reg_time = $this->now;

        if ( $uid > 0 ) {
            $user_page = HttpResponse::getCookie( 'user_page' );
            $entity_User_base->uid = $uid;
            //update save article            
            $rs = $this->tmp_model->modifyUser( $entity_User_base );
            if ( $rs ) {
                $this->redirect( '修改管理员成功', PHP_SELF . '?m=user&page= ' . $user_page . '' );
            } else {
                $this->redirect( '修改管理员失败' );
            }
        } else {
            //insert save article_class
            $rs = $this->tmp_model->createUser( $entity_User_base );
            if ( $rs ) {
                $this->redirect( '添加管理员成功', PHP_SELF . '?m=user' );
            } else {
                $this->redirect( '添加管理员失败' );
            }
        }
    }

    /**
     * 批量操作
     */
    public function user_do()
    {
        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'uid', 0 )->int();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();

        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            $this->redirect( '请选择要操作的...' );
        }

        if ( $do == 'del' || $act == 'del' ) {
            $rs = $this->tmp_model->deleteByUid( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->redirect( '删除管理员成功' );
            } else {
                $this->redirect( '删除管理员失败，请重试！' );
            }
        }
    }

}
