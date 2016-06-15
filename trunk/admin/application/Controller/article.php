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

    private $tmp_model;
    private $archives_model;
    private $tag_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
        $this->tmp_model = Tmac::model( 'Article' );
        $this->archives_model = Tmac::model( 'Archives' );
        $this->tag_model = Tmac::model( 'tag' );

        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer' );
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        $cat_id = Input::get( 'cat_id', 0 )->int();
        $channelid = Input::get( 'channelid', 0 )->int();

        $search_keyword = Input::get( 'search_keyword', '' )->string();

        $cat_id = empty( $cat_id ) ? 0 : $cat_id;
        $treers = $this->M( 'Category' )->getCategoryTreeList( 0, $cat_id );

        $entity_parameter_Article_base = new entity_parameter_Article_base();
        $entity_parameter_Article_base->setCat_id( $cat_id );
        $entity_parameter_Article_base->setChannelid( $channelid );
        $entity_parameter_Article_base->setQuery( $search_keyword );

        //TODO  取出所有资讯
        $rs = $this->tmp_model->getArticleList( $entity_parameter_Article_base );

        //取友情操作类型radiobutton数组
        $article_do_ary = Tmac::config( 'article.do' );
        $article_do_ary_option = Utility::Option( $article_do_ary, '' );

        $this->assign( 'channelid', $channelid );
        $this->assign( 'treers', $treers );
        $this->assign( 'search_keyword', $search_keyword );
        $this->assign( 'article_do_ary_option', $article_do_ary_option );
        $this->assign( $rs );
        $this->V( 'article' );
    }

    /**
     * 新增/修改文章页面
     */
    public function add()
    {
        $aid = Input::get( 'aid', 0 )->int();
        $cat_id = Input::get( 'cat_id', 0 )->int();
        $uid = $_SESSION[ 'admin_uid' ];
        $channeltype = 1;

        $entity_Article_base = new entity_Article_base();
        $entity_Article_base->cat_id = $cat_id;
        $entity_Article_base->status = 1;
        $entity_Article_base->comment_status = 1;
        $entity_Article_base->content = null;

        if ( $aid > 0 ) {
            $entity_Article_base = $this->tmp_model->getArticleInfo( $aid );
            $entity_Article_base->content = htmlspecialchars( $entity_Article_base->content );
            $link_image = $this->getImage( $entity_Article_base->thumb, 'article', '200x150' );
        }
        $entity_Article_base->photo_url = $link_image;
        //取文章栏目        
        //$treers = $this->tmp_model->article_cat_tree(0, 0, $editinfo['cat_id'], $channeltype);        
        $treers = $this->M( 'Category' )->getCategoryTreeList( 0, $entity_Article_base->cat_id );

        //取作者数组
        $user_array = $this->tmp_model->getUserArray( $uid );
        $user_array_option = Utility::OptionObject( $user_array, $entity_Article_base->uid, 'uid,nicename' );

        //取文章状态数组
        $status_array = Tmac::config( 'article.status' );
        $status_array_option = Utility::OptionObject( $status_array, $entity_Article_base->status );

        //取评论状态数组
        $comment_status_array = Tmac::config( 'article.comment_status' );
        $comment_status_array_option = Utility::OptionObject( $comment_status_array, $entity_Article_base->comment_status );

        //取所有的Tag_info Array
        $tag_info_array = $this->tag_model->getTagInfoArrayByArticle_id( $aid );

        //初始化一下    默认state_radio
        $entity_Article_base->author = !empty( $entity_Article_base->author ) ? $entity_Article_base->author : '默认管理员';

        $this->assign( 'editinfo', $entity_Article_base );
        $this->assign( 'treers', $treers );
        $this->assign( 'user_array_option', $user_array_option );
        $this->assign( 'user_array', $user_array );
        $this->assign( 'status_array_option', $status_array_option );
        $this->assign( 'comment_status_array_option', $comment_status_array_option );
        $this->assign( 'tag_info_array', $tag_info_array );
        //TODO　载入资讯类别添加表单
        $this->V( 'article' );
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
        $separator = '&lt;--more--&gt;';
        /* 初始化变量 */
        $aid = Input::post( 'aid', 0 )->int();
        $cat_id = Input::post( 'cat_id', 0 )->int();
        $title = Input::post( 'title', '' )->required( '标题不能力空' )->string();
        $author = Input::post( 'author', '' )->string();
        $article_order = Input::post( 'article_order', 0 )->int();
        $thumb = Input::post( 'thumb_image_id', '' )->string();
        $content = Input::post( 'content', '' )->required( '内容不能力空' )->sql();
        $keywords = Input::post( 'keywords', '' )->string();
        $description = Input::post( 'description', '' )->string();
        $uid = Input::post( 'uid', '' )->int();
        $status = Input::post( 'status', 0 )->int();
        $comment_status = Input::post( 'comment_status', 0 )->int();
        $name = Input::post( 'name', '' )->string();
        //判断有没有more的内容放到content_description
        if ( strpos( $content, $separator ) ) {
            list($content_descrption, $extand) = explode( $separator, $content, 2 );
            $content_descrption .= $separator;
        } else {
            $content_descrption = $content;
        }

        //Tags相关        
        $tag_id_array = Input::post( 'tag_id', '' )->sql();
        $tag_name_array = Input::post( 'tag_name', '' )->sql();

        $channelid = 1;

        if ( !$this->archives_model->checkChannel( $cat_id, $channelid ) ) {
            $this->redirect( "你所选择的栏目与当前模型不相符！" );
            exit();
        }

        if ( Filter::getStatus() === false ) {
            $this->redirect( Filter::getFailMessage() );
        }

        $entity_Article_base = new entity_Article_base();
        $entity_Article_base->cat_id = $cat_id;
        $entity_Article_base->channel = $channelid;
        $entity_Article_base->uid = $uid;
        $entity_Article_base->title = $title;
        $entity_Article_base->author = $author;
        $entity_Article_base->article_order = $article_order;
        $entity_Article_base->keywords = $keywords;
        $entity_Article_base->description = $description;
        $entity_Article_base->thumb = $thumb;
        $entity_Article_base->status = $status;
        $entity_Article_base->comment_status = $comment_status;
        $entity_Article_base->name = $name;
        $entity_Article_base->edit_time = $this->now;
        $entity_Article_base->content_descrption = $content_descrption;

        $entity_Addonarticle_base = new entity_Addonarticle_base();
        $entity_Addonarticle_base->article_id = $aid;
        $entity_Addonarticle_base->cat_id = $cat_id;
        $entity_Addonarticle_base->content = $content;

        $type_url = PHP_SELF . '?m=archives.arclist&channelid=' . $channelid . '&cat_id=' . $cat_id;

        if ( $aid > 0 ) {
            //update save article            
            $entity_Article_base->article_id = $aid;
            $rs = $this->tmp_model->modifyArticleAll( $entity_Article_base, $entity_Addonarticle_base );
            //插入Tag_info            
            if ( $rs ) {
                $this->tag_model->saveTagInfo( $aid, $tag_id_array, $tag_name_array );
                $this->redirect( '修改资讯文章成功', $type_url );
            } else {
                $this->redirect( '修改资讯文章失败' );
            }
        } else {
            //insert save article_class
            $article_id = $this->tmp_model->createArticleAll( $entity_Article_base, $entity_Addonarticle_base );
            if ( $article_id ) {
                //插入Tag_info
                $this->tag_model->saveTagInfo( $article_id, $tag_id_array, $tag_name_array );
                $this->redirect( '添加资讯文章成功', $type_url );
            } else {
                $this->redirect( '添加资讯文章失败，请联系技术支持检查原因！' );
            }
        }
    }

    /**
     * 批量操作
     */
    public function article_do()
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

}
