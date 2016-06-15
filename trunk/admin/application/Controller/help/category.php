<?php

/**
 * 后台 文章栏目模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: category.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class categoryAction extends service_Controller_admin
{

    private $tmp_model;
    private $check_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
        $this->tmp_model = Tmac::model( 'help/Category', APP_ADMIN_NAME );
        $this->check_model = $this->M( 'Check' );
        $this->check_model->checkLogin();
        $this->check_model->CheckPurview( 'tb_admin,tb_editer' );
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        //TODO  取出所有资讯栏目        
        $parent = 0;
        $category_list = $this->tmp_model->getCategoryList( $parent );
        $category_list = empty( $category_list ) ? '<tr><td colspan="4">请先添加分类</td></tr>' : $category_list;
        $this->assign( 'category_list', $category_list );
        $this->V( 'help/category' );
    }

    /**
     * 新增/修改栏目页面
     */
    public function add()
    {
        $help_cat_id = Input::get( 'cat_id', 0 )->int();
        $button = '添加分类';

        $entity_HelpCategory_base = new entity_HelpCategory_base();
        if ( $help_cat_id > 0 ) {
            $entity_HelpCategory_base = $this->tmp_model->getCategoryInfo( $help_cat_id );
            $button = '保存修改';
        }


        //栏目父级 select
        $category_tree_list = $this->tmp_model->getCategoryTreeList( 0, $entity_HelpCategory_base->cat_pid );

        $cat_type_array = Tmac::config( 'help.help.cat_type', APP_ADMIN_NAME );
        $cat_type_option = Utility::Option( $cat_type_array, $entity_HelpCategory_base->cat_type );

        $this->assign( 'editinfo', $entity_HelpCategory_base );
        $this->assign( 'category_tree_list', $category_tree_list );
        $this->assign( 'cat_type_option', $cat_type_option );
        $this->assign( 'cat_id', $help_cat_id );
        $this->assign( 'button', $button );
        $this->V( 'help/category' );
    }

    /**
     * 新增/修改栏目页面　保存　
     */
    public function save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 3 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }

        $help_cat_id = Input::post( 'help_cat_id', 0 )->int();
        $cat_pid = Input::post( 'cat_pid', 0 )->int();
        //修改的时候父级栏目不能为自己
        if ( ($help_cat_id > 0) && ($cat_pid == $help_cat_id) ) {
            $this->redirect( '所属栏目父级不能为自己!' );
            exit;
        }

        $cat_name = Input::post( 'cat_name', '' )->required( '请填写标题！' )->string();
        $cat_keywords = Input::post( 'cat_keywords', '' )->string();
        $cat_description = Input::post( 'cat_description', '' )->string();
        $cat_sort = Input::post( 'cat_sort', 0 )->int();
        $cat_type = Input::post( 'cat_type', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $entity_HelpCategory_base = new entity_HelpCategory_base();
        $entity_HelpCategory_base->cat_pid = $cat_pid;
        $entity_HelpCategory_base->cat_name = $cat_name;
        $entity_HelpCategory_base->cat_keywords = $cat_keywords;
        $entity_HelpCategory_base->cat_description = $cat_description;
        $entity_HelpCategory_base->cat_sort = $cat_sort;
        $entity_HelpCategory_base->cat_type = $cat_type;

        $check_cat_info = $this->tmp_model->checkCategoryName( $cat_name, $cat_pid );
        if ( $check_cat_info && $check_cat_info->is_delete == 0 && $check_cat_info->help_cat_id <> $help_cat_id ) {
            $this->redirect( '分类"' . $cat_name . '"已经存在' );
            exit;
        }
        if ( $check_cat_info && $check_cat_info->is_delete == 1 ) {
            $entity_HelpCategory_base->help_cat_id = $check_cat_info->help_cat_id;
            $entity_HelpCategory_base->is_delete = 0;
            $rs = $this->tmp_model->modifyCategoryByPk( $entity_HelpCategory_base );
            if ( $rs ) {
                $this->redirect( '修改新分类成功', PHP_SELF . '?m=help/category' );
            } else {
                $this->redirect( '修改新分类失败' );
            }
        }
        if ( $help_cat_id > 0 ) {
            //update save article_class            
            $entity_HelpCategory_base->help_cat_id = $help_cat_id;
            $rs = $this->tmp_model->modifyCategoryByPk( $entity_HelpCategory_base );
            if ( $rs ) {
                $this->redirect( '修改新分类成功', PHP_SELF . '?m=help/category' );
            } else {
                $this->redirect( '修改新分类失败' );
            }
        } else {
            //insert save article_class
            $rs = $this->tmp_model->createCategory( $entity_HelpCategory_base );
            if ( $rs ) {
                $this->redirect( '添加新分类成功', PHP_SELF . '?m=help/category' );
            } else {
                $this->redirect( '添加新分类失败' );
            }
        }
    }

    /**
     * del
     * @param int $class_id
     */
    public function del()
    {
        //权限
        $this->check_model->CheckPurview( 'tb_admin' );
        $cat_id = empty( $_GET[ 'cat_id' ] ) ? 0 : (int) $_GET[ 'cat_id' ];
        if ( empty( $cat_id ) ) {
            $this->redirect( '请选择要删除的分类，请重试！' );
            exit;
        }
        $rs = $this->tmp_model->deleteCategoryById( $cat_id );
        // TODO DEL该分类下的所有资讯
        if ( $rs ) {
            $this->redirect( '删除分类成功', PHP_SELF . '?m=help/category' );
        } else {
            $this->redirect( '删除分类失败，请重试！' );
        }
    }

}
