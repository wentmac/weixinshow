<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: article.php 345 2016-06-07 14:50:24Z zhangwentao $
 * http://www.t-mac.org；
 */
class articleAction extends service_Controller_manage
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 取订单列表
     */
    public function index()
    {
        $cat_id = Input::get( 'cat_id', 0 )->int();
        $channelid = Input::get( 'channelid', 0 )->int();
        $pagesize = Input::get( 'pagesize', 10 )->int();
        $query_string = Input::get( 'query', '' )->string();

        $treers = $this->M( 'Category' )->getCategoryTreeList( 0, $cat_id );

        $searchParameter[ 'channelid' ] = $channelid;
        $searchParameter[ 'cat_id' ] = $cat_id;
        $searchParameter[ 'pagesize' ] = $pagesize;
        $searchParameter[ 'query' ] = $query_string;

        $array[ 'searchParameter' ] = json_encode( $searchParameter );
        $array[ 'category_tree' ] = $treers;
        $this->assign( $array );
        $this->V( 'article_list' );
    }

    /**
     * 取订单列表
     */
    public function get_list()
    {
        $cat_id = Input::get( 'cat_id', 0 )->int();
        $channelid = Input::get( 'channelid', 0 )->int();
        $pagesize = Input::get( 'pagesize', 20 )->int();
        $query_string = Input::get( 'query', '' )->string();

        $entity_parameter_Article_base = new entity_parameter_Article_base();
        $entity_parameter_Article_base->setCat_id( $cat_id );
        $entity_parameter_Article_base->setChannelid( $channelid );
        $entity_parameter_Article_base->setQuery( $query_string );
        $entity_parameter_Article_base->setPagesize( $pagesize );

        $model = new service_Article_manage();
        //TODO  取出所有资讯
        $rs = $model->getArticleList( $entity_parameter_Article_base );
        $this->apiReturn( $rs );
    }

    /**
     * 订单详情
     */
    public function add()
    {
        $article_id = Input::get( 'id', 0 )->int();
        $entity_Article_base = new entity_Article_base();
        $entity_Article_base->content = '';
        $entity_Article_base->article_image_url = '';
        $model = new service_Article_manage();       
        //$article_id = 29;        

        if ( $article_id > 0 ) {
            $entity_Article_base = $model->getArticleInfo( $article_id );
            if ( $entity_Article_base == false ) {
                die( '商品不存在' );
            }
        }
        //获取商品分类        
        $category_list_option = $this->M( 'Category' )->getCategoryTreeList( 0, $entity_Article_base->cat_id );


        $array[ 'article_id' ] = $article_id;
        $array[ 'uid' ] = $this->memberInfo->uid;
        $array[ 'category_list_option' ] = $category_list_option;
        $array[ 'editinfo' ] = $entity_Article_base;
//        echo '<pre>';
//        print_r( $array );
//        echo '<pre>';
//        die;
        $this->assign( $array );
        $this->V( 'article_add' );
    }

    public function save()
    {
        if ( empty( $_POST ) || count( $_POST ) < 1 ) {
            $this->redirect( 'don\'t be evil' );
            exit;
        }
        $separator = '&lt;--more--&gt;';
        /* 初始化变量 */
        $aid = Input::post( 'article_id', 0 )->int();
        $cat_id = Input::post( 'cat_id', 0 )->int();
        $title = Input::post( 'title', '' )->required( '标题不能为空' )->string();
        $author = Input::post( 'author', '' )->string();
        $article_order = Input::post( 'article_order', 0 )->int();
        $article_image_id = Input::post( 'article_image_id', '' )->imageId();
        $content = Input::post( 'content', '' )->required( '内容不能力空' )->sql();
        $keywords = Input::post( 'keywords', '' )->string();
        $description = Input::post( 'description', '' )->string();
        $uid = Input::post( 'uid', '' )->int();
        $status = Input::post( 'status', 0 )->int();
        $comment_status = Input::post( 'comment_status', 0 )->int();

        //判断有没有more的内容放到content_description
        if ( strpos( $content, $separator ) ) {
            list($content_descrption, $extand) = explode( $separator, $content, 2 );
            $content_descrption .= $separator;
        } else {
            $content_descrption = $content;
        }
        $channelid = 1;

        $archives_model = new service_Archives_manage();
        if ( !$archives_model->checkChannel( $cat_id, $channelid ) ) {
            throw new ApiException( "你所选择的栏目与当前模型不相符！" );
        }

        if ( Filter::getStatus() === false ) {
            throw new ApiException(  Filter::getFailMessage() );
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
        $entity_Article_base->article_image_id = $article_image_id;
        $entity_Article_base->status = $status;
        $entity_Article_base->comment_status = $comment_status;        
        $entity_Article_base->edit_time = $this->now;        
        $entity_Article_base->content_descrption = $content_descrption;

        $entity_AddArticle_base = new entity_AddArticle_base();
        $entity_AddArticle_base->article_id = $aid;
        $entity_AddArticle_base->cat_id = $cat_id;
        $entity_AddArticle_base->content = $content;
                
        $article_model = new service_Article_manage();
        if ( $aid > 0 ) {
            //update save article            
            $entity_Article_base->article_id = $aid;
            $rs = $article_model->modifyArticleAll( $entity_Article_base, $entity_AddArticle_base );
            //插入Tag_info            
            if ( $rs ) {                
                $this->apiReturn();
            } else {
                throw new ApiException( '修改资讯文章失败' );
            }
        } else {
            //insert save article_class
            $article_id = $article_model->createArticleAll( $entity_Article_base, $entity_AddArticle_base );
            if ( $article_id ) {                
                $this->apiReturn();
            } else {
                throw new ApiException( '添加资讯文章失败，请联系技术支持检查原因！' );
            }
        }
    }

    /**
     * 批量操作
     */
    public function batch()
    {
        $act = Input::get( 'action', '' )->string();
        $aid = Input::get( 'id', 0 )->int();

        $do = Input::post( 'do', '' )->string();
        $id_a = Input::post( 'id_a', '' )->intString();

        if ( strpos( $id_a, ',' ) !== false ) {
            $id = $id_a;
        } elseif ( !empty( $aid ) ) {
            $id = $aid;
        } else {
            throw new ApiException( '请选择要操作的...' );
        }

        if ( $do == 'del' || $act == 'del' ) {
            $model = new service_Article_manage();
            $rs = $model->deleteByArticleId( $id );
            // TODO DEL该分类下的所有资讯
            if ( $rs ) {
                $this->apiReturn();
            } else {
                throw new ApiException( '删除留言失败，请重试！' );
            }
        }
    }

}
