<?php

/**
 * 后台文章模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: member.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class memberAction extends service_Controller_admin
{

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );

        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer,tb_customer_manager' );
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        $member_type = Input::get( 'member_type', 0 )->int();
        $member_class = Input::get( 'member_class', 0 )->int();
        $page = Input::get( 'page', 0 )->int();
        if ( !isset( $_GET[ 'member_class' ] ) && $member_class == 0 ) {
            $member_class = -1;
        }

        $query_string = Input::get( 'query_string', '' )->string();
        $start_date = Input::get( 'start_date', '' )->string();
        $end_date = Input::get( 'end_date', '' )->string();
        $member_export = Input::get( 'member_export', 0 )->int();
        $agent_lock = Input::get( 'agent_lock', 0 )->int();

        $entity_parameter_Member_admin = new entity_parameter_Member_admin();
        $entity_parameter_Member_admin->member_type = $member_type;
        $entity_parameter_Member_admin->member_class = $member_class;
        $entity_parameter_Member_admin->query_string = $query_string;
        $entity_parameter_Member_admin->start_date = $start_date;
        $entity_parameter_Member_admin->end_date = $end_date;
        $entity_parameter_Member_admin->member_export = $member_export;
        $entity_parameter_Member_admin->agent_lock = $agent_lock;
        $entity_parameter_Member_admin->setPagesize( 20 );


        $model = new service_Member_admin();
        if ( $member_export <> service_Member_admin::member_export_default ) {
            set_time_limit( 0 );
            $model->exportMemberArray( $entity_parameter_Member_admin );
            exit;
        }
        //TODO  取出所有资讯
        $rs = $model->getMemberArray( $entity_parameter_Member_admin );


        //取友情操作类型radiobutton数组
        $member_type_array = Tmac::config( 'member.member.member_type', APP_BASE_NAME );
        $member_type_option = Utility::Option( $member_type_array, $member_type );
        //取 导出类型 类型radiobutton数组
        $member_export_array = Tmac::config( 'member.member.export', APP_BASE_NAME );
        $member_export_option = Utility::Option( $member_export_array, $member_export );
        //取 导出类型 类型radiobutton数组
        $agent_lock_array = Tmac::config( 'member.member.agent_lock', APP_BASE_NAME );
        $agent_lock_option = Utility::Option( $agent_lock_array, $agent_lock );

        //取友情操作类型radiobutton数组
        $member_class_array = Tmac::config( 'member.member.member_class', APP_BASE_NAME );
        $member_class_option = '';
        if ( !empty( $member_class_array[ $member_type ] ) ) {
            $member_class_option = Utility::Option( $member_class_array[ $member_type ], $member_class );
        }

        //取友情操作类型radiobutton数组
        $article_do_ary = Tmac::config( 'article.do' );
        $article_do_ary_option = Utility::Option( $article_do_ary, '' );

        $array[ 'member_type' ] = $member_type;
        $array[ 'member_class' ] = $member_class;
        $array[ 'start_date' ] = $start_date;
        $array[ 'end_date' ] = $end_date;
        $array[ 'query_string' ] = $query_string;
        $array[ 'article_do_ary_option' ] = $article_do_ary_option;
        $array[ 'member_type_option' ] = $member_type_option;
        $array[ 'member_class_option' ] = $member_class_option;
        $array[ 'member_export_option' ] = $member_export_option;
        $array[ 'agent_lock_option' ] = $agent_lock_option;
        $array[ 'member_class_json' ] = json_encode( $member_class_array, true );
        $array[ 'pages' ] = $page;

        $this->assign( $array );
        $this->assign( $rs );

//        var_dump( $array );
//        var_dump( $rs );
//        die;
        $this->V( 'member' );
    }

    /**
     * 新增/修改文章页面
     */
    public function detail()
    {
        $uid = Input::get( 'uid', 0 )->required( '用户不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $model = new service_Member_admin();
        $memberInfo = $model->getMemberInfoByUid( $uid );
        $memberSettingInfo = $model->getMemberSettingInfoByUid( $uid );

        $member = $model->handelMemberInfo( $memberInfo, $memberSettingInfo );
        $this->assign( $member );
        //TODO　载入资讯类别添加表单
        $this->V( 'member' );
    }

    /**
     * 新增/修改文章页面
     */
    public function add()
    {
        $uid = Input::get( 'uid', 0 )->int();

        $entity_Member_base = new entity_Member_base();
        $model = new service_Member_admin();
        if ( $uid > 0 ) {
            $entity_Member_base = $model->getMemberInfoByUid( $uid );
        }

        $array[ 'member_class_json' ] = json_encode( array(), true );
        $this->assign( $array );
        $this->assign( 'editinfo', $entity_Member_base );
        //TODO　载入资讯类别添加表单
        $this->V( 'member' );
    }

    /**
     * 新增/修改栏目页面　保存　
     */
    public function save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 1 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }

        /* 初始化变量 */
        $uid = Input::post( 'uid', 0 )->required( '标题不能力空' )->int();
        $idcard_verify = Input::post( 'idcard_verify', 0 )->int();
        $member_type = Input::post( 'member_type', 0 )->int();
        $member_class = Input::post( 'member_class', 0 )->int();
        $locked_type = Input::post( 'locked_type', 0 )->int();
        $fee_type = Input::post( 'fee_type', 0 )->int();
        $promotion_type = Input::post( 'promotion_type', 0 )->int();
        $seller_count_variable = Input::post( 'seller_count_variable', 0 )->int();
        $collect_count_variable = Input::post( 'collect_count_variable', 0 )->int();
        $security_deposit = Input::post( 'security_deposit', 0 )->float();
        $shop_sort = Input::post( 'shop_sort', 0 )->int();
        $username = Input::post( 'username', '' )->string();
        $realname = Input::post( 'realname', '' )->string();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        if ( !empty( $member_class ) && empty( $member_type ) ) {
            $this->redirect( '用户级别和用户类型不能全为空' );
        }

        if ( $member_class == -1 ) {
            $member_class = 0;
        }

        $model = new service_Member_admin();
        $model->setUid( $uid );
        if ( !empty( $member_type ) ) {
            $entity_Member_base = new entity_Member_base();
            $entity_Member_base->username = $username;
            $entity_Member_base->realname = $realname;
            empty( $member_type ) || $entity_Member_base->member_type = $member_type;
            $entity_Member_base->member_class = $member_class;
            $entity_Member_base->locked_type = $locked_type;
            $entity_Member_base->fee_type = $fee_type;
            $entity_Member_base->promotion_type = $promotion_type;
            $model->updateMemberInfo( $entity_Member_base );

            if ( $member_type == service_Member_base::member_type_supplier ) {
                $goods_save_model = new service_goods_Save_admin();
                $goods_save_model->updateGoodsSupplier( $uid );
            }
        }

        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->idcard_verify = $idcard_verify;
        $entity_MemberSetting_base->security_deposit = $security_deposit;
        $entity_MemberSetting_base->seller_count_variable = $seller_count_variable;
        $entity_MemberSetting_base->collect_count_variable = $collect_count_variable;
        $entity_MemberSetting_base->shop_sort = $shop_sort;

        $model->updateMemberSettingInfo( $entity_MemberSetting_base );

        $this->redirect( '修改成功', PHP_SELF . '?m=member.detail&uid=' . $uid );
    }

    /**
     * 新增/修改栏目页面　保存　
     */
    public function create_save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 1 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }

        /* 初始化变量 */
        $mobile = Input::post( 'mobile', '' )->required( '手机号不能为空' )->tel();
        $password = Input::post( 'pwd', 0 )->required( '密码不能为空' )->password();
        $username = Input::post( 'username', 0 )->string();
        $agent_mobile = Input::post( 'agent_mobile', 0 )->tel();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $model = new service_account_Register_manage();

        $model->setUsername( $username );
        $model->setMobile( $mobile );
        $model->setSms_captcha( '' );
        $model->setPassword( $password );
        $model->setNeed_password( false );
        $model->setIsApi( FALSE );
        $model->setAgent_mobile( $agent_mobile );

        //注册新用户
        $reg_info = $model->createMember();
        if ( $reg_info == false ) {
            $this->redirect( $model->getErrorMessage() );
        }

        $this->redirect( '修改成功', PHP_SELF . '?m=member' );
    }

    /**
     * 批量操作
     */
    public function batch_do()
    {
        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'aid', 0 )->int();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->sql();

        if ( is_array( $id_a ) ) {
            $id = implode( ',', $id_a );
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            $this->redirect( '请选择要操作的...' );
            exit;
        }

        if ( $do == 'del' || $act == 'del' ) {
            $rs = $this->tmp_model->deleteByArticleId( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->redirect( '删除资讯成功' );
            } else {
                $this->redirect( '删除资讯失败，请重试！' );
            }
        }
    }

    public function level()
    {
        $uid = Input::get( 'uid', 0 )->required( '用户UID不能为空' )->int();
        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $model = new service_Member_admin();
        $memberInfo = $model->getMemberInfoByUid( $uid );
        if ( !$memberInfo ) {
            die( "不存在的会员" );
        }
        $memberInfo->member_image_id = empty( $memberInfo->member_image_id ) ? '' : $this->getImage( $memberInfo->member_image_id, 'avatar', 110 );

        $member_tree_show_model = new service_member_TreeShow_base();
        $member_tree_show_model->setRank_level( service_member_TreeShow_base::tree_level_count );
        $tree_array = $member_tree_show_model->showAgentRankTree( $uid );
        $bread_crumb_array = $member_tree_show_model->getBreadCrumbArray( $uid );
//        echo '<pre>';
//        print_r($bread_crumb_array);die;
        $member_info_map = $member_tree_show_model->getMemberInfoMap();
        //取出所有的粉丝
        $member_agent_model = new service_member_Agent_mobile();
        $agent_array = $member_agent_model->getAgentAll( $uid );

        if ( $bread_crumb_array ) {
            $member_id_string = implode( ',', $bread_crumb_array );
        } else {
            $member_id_string = '';
        }
        $array[ 'tree_array' ] = $tree_array;
        $array[ 'member_info_map' ] = $member_info_map;
        $array[ 'agent_array' ] = $agent_array;
        $array[ 'memberInfo' ] = $memberInfo;
        $array[ 'bread_crumb_array' ] = $bread_crumb_array;
        $array[ 'bread_crumb_map' ] = $member_tree_show_model->getMemberInfoArrayByIds( $member_id_string );

//        echo '<Pre>';
//        print_r($array);die;
        $this->assign( $array );

        $this->V( 'agent_level' );
    }

}
