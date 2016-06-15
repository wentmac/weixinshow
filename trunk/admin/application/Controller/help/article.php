<?php

/**
 * 后台文章模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: article.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class articleAction extends service_Controller_admin
{

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );

        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer' );
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        $help_cat_id = Input::get( 'cat_id', 0 )->int();
        $query_string = Input::get( 'query_string', '' )->string();
        $help_recommend = Input::get( 'help_recommend', 0 )->int();


        $treers = $this->M( 'help/Category' )->getCategoryTreeList( 0, $help_cat_id );

        $model = new service_help_Article_admin();
        $model->setHelp_cat_id( $help_cat_id );
        $model->setQuery_string( $query_string );

        //TODO  取出所有资讯
        $rs = $model->getHelpArticleList();

        //取友情操作类型radiobutton数组
        $article_do_ary = Tmac::config( 'article.do_index' );
        $article_do_ary_option = Utility::Option( $article_do_ary, '' );
        //取友情操作类型radiobutton数组
        $help_recommend_array = Tmac::config( 'help.help_recommend', APP_ADMIN_NAME );
        $help_recommend_option = Utility::Option( $help_recommend_array, $help_recommend );

        $this->assign( 'help_cat_id', $help_cat_id );
        $this->assign( 'treers', $treers );
        $this->assign( 'query_string', $query_string );
        $this->assign( 'article_do_ary_option', $article_do_ary_option );
        $this->assign( 'help_recommend_option', $help_recommend_option );
        $this->assign( $rs );
        $this->V( 'help/article' );
    }

    /**
     * 新增/修改文章页面
     */
    public function add()
    {
        $aid = Input::get( 'id', 0 )->int();
        $help_cat_id = Input::get( 'cat_id', 0 )->int();

        $entity_HelpArticle_base = new entity_HelpArticle_base();
        $entity_HelpArticle_base->help_cat_id = $help_cat_id;

        $model = new service_help_Article_admin();

        if ( $aid > 0 ) {
            $entity_HelpArticle_base = $model->getHelpArticleInfo( $aid );
        }
        //取文章栏目        
        //$treers = $this->tmp_model->article_cat_tree(0, 0, $editinfo['cat_id'], $channeltype);        
        $treers = $this->M( 'help/Category' )->getCategoryTreeList( 0, $entity_HelpArticle_base->help_cat_id );

        $help_recommend_array = Tmac::config( 'help.help_recommend', APP_ADMIN_NAME );
        $help_recommend_option = Utility::Option( $help_recommend_array, $entity_HelpArticle_base->help_recommend );
        

        $this->assign( 'editinfo', $entity_HelpArticle_base );
        $this->assign( 'help_recommend_option', $help_recommend_option );
        $this->assign( 'treers', $treers );
        //TODO　载入资讯类别添加表单
        $this->V( 'help/article' );
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
        $aid = Input::post( 'id', 0 )->int();
        $help_cat_id = Input::post( 'help_cat_id', 0 )->int();
        $help_title = Input::post( 'help_title', '' )->required( '标题不能为空' )->string();
        $help_keywords = Input::post( 'help_keywords', '' )->required( 'keyworkd不能空' )->string();
        $help_description = Input::post( 'help_description', '' )->required( 'description不能为空' )->string();
        $help_content = Input::post( 'help_content', '' )->required( '内容不能为空' )->sql();
        $help_sort = Input::post( 'help_sort', 0 )->int();
        $help_recommend = Input::post( 'help_recommend', 0 )->int();

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $entity_HelpArticle_base = new entity_HelpArticle_base();
        $entity_HelpArticle_base->help_cat_id = $help_cat_id;
        $entity_HelpArticle_base->help_title = $help_title;
        $entity_HelpArticle_base->help_keywords = $help_keywords;
        $entity_HelpArticle_base->help_description = $help_description;
        $entity_HelpArticle_base->help_content = $help_content;
        $entity_HelpArticle_base->help_sort = $help_sort;
        $entity_HelpArticle_base->help_recommend = $help_recommend;

        $type_url = PHP_SELF . '?m=help/article.index';

        $model = new service_help_Article_admin();
        if ( $aid > 0 ) {
            //update save article            
            $entity_HelpArticle_base->help_article_id = $aid;
            $rs = $model->modifyHelpArticle( $entity_HelpArticle_base );
            //插入Tag_info            
            if ( $rs ) {
                $this->redirect( '修改帮助文章成功', $type_url );
            } else {
                $this->redirect( '修改帮助文章失败' );
            }
        } else {
            //insert save article_class
            $article_id = $model->createHelpArticle( $entity_HelpArticle_base );
            if ( $article_id ) {
                //插入Tag_info                
                $this->redirect( '添加帮助文章成功', $type_url );
            } else {
                $this->redirect( '添加帮助文章失败，请联系技术支持检查原因！' );
            }
        }
    }

    /**
     * 批量操作
     */
    public function article_do()
    {
        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'id', 0 )->int();

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
            $model = new service_help_Article_admin();
            $rs = $model->deleteHelpArticleId( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->redirect( '删除资讯成功' );
            } else {
                $this->redirect( '删除资讯失败，请重试！' );
            }
        }
    }

}
