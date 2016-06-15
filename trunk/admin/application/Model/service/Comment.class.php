<?php

/**
 * 后台首页小图模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Comment.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Comment_admin extends Model
{
    const comment_approved_hide = 0; //隐藏
    const comment_approved_show = 1; //显示
    const comment_approved_spam = -1; //垃圾评论
    /**
     * 初始化变量　定义私有变量
     */

    public function _init()
    {
        
    }

    public function createComment(entity_Comment_base $entity_Comment_base)
    {
        $dao = dao_factory_base::getCommentDao();
        return $dao->insert($entity_Comment_base);
    }

    public function modifyComment(entity_Comment_base $entity_Comment_base)
    {
        $dao = dao_factory_base::getCommentDao();
        $dao->setPk($entity_Comment_base->comment_id);
        return $dao->updateByPk($entity_Comment_base);
    }

    /**
     * 获取一个首页小图信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getCommentInfo($id)
    {
        $dao = dao_factory_base::getCommentDao();
        $dao->setPk($id);
        $rs = $dao->getInfoByPk();
        if ($rs) {
            $dao_article = dao_factory_base::getArticleDao();
            $dao_article->setPk($rs->article_id);
            $article_rs = $dao_article->getInfoByPk();
            $rs->title = $article_rs->title;
        }
        return $rs;
    }

    /**
     * 获取所有首页小图
     * return article_class,pages
     */
    public function getCommentList(entity_parameter_Comment_base $entity_parameter_Comment_base)
    {

        if ($entity_parameter_Comment_base->getUrl() != null) {
            $url = $entity_parameter_Comment_base->getUrl();
        } else {
            $url = PHP_SELF . '?m=comment';
        }

        if ($entity_parameter_Comment_base->getApproved() != null) {
            $url .= "&comment_approved={$entity_parameter_Comment_base->getApproved()}";
        }
        if ($entity_parameter_Comment_base->getQuery() != null) {
            $url .= "&search_keyword={$entity_parameter_Comment_base->getQuery()}";
        }
        $url .= '&page=';

        $dao = dao_factory_base::getCommentDao();

        $where = $dao->getListWhere($entity_parameter_Comment_base);
        $dao->setWhere($where);
        $count = $dao->getCountByWhere();

        $pages = $this->P('Pages');
        $pages->setTotal($count);
        $pages->setUrl($url);
        $pages->setPrepage($entity_parameter_Comment_base->getPagesize());
        $limit = $pages->getSqlLimit();

        $dao->setField('comment_id, comment_author, comment_author_email, comment_author_ip, comment_content, comment_approved, comment_time');
        $dao->setLimit($limit);
        $dao->setOrderby('comment_id DESC');

        $rs = $dao->getListByWhere();

        $type_array = array(
            '0' => '隐藏',
            '1' => '显示',
            '-1' => '垃圾评论'
        );
        if (is_array($rs)) {
            foreach ($rs AS $k => $v) {
                $rs[$k]->time = date('y/m/d H:i:s', $v->comment_time);
                $rs[$k]->type = $type_array[$v->comment_approved];
            }
        }

        //把文章的当前page写到cookies里
        HttpResponse::setCookie('comment_page', $pages->getNowPage());
        $ErrorMsg = '';
        if ($count == 0) {
            $ErrorMsg = "暂无评论!";
        }

        $result = array('rs' => $rs, 'pageCurrent' => $pages->getNowPage(), 'page' => $pages->show(), 'ErrorMsg' => $ErrorMsg);
        return $result;
    }

    /**
     * del
     * @param int $class_id
     */
    public function deleteByCommentId($id)
    {
        $dao = dao_factory_base::getCommentDao();

        $dao->getDb()->startTrans();
        if (strpos($id, ',') === false) {
            $dao->setPk($id);
            $dao->deleteByPk();
        } else {
            $dao->setWhere("comment_id IN({$id})");
            $dao->deleteByWhere();
        }

        if ($dao->getDb()->isSuccess()) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    public function modifyCommentByIds(entity_Comment_base $entity_Comment_base)
    {
        $dao = dao_factory_base::getCommentDao();
        $dao->getDb()->startTrans();
        if (strpos($entity_Comment_base->comment_id, ',') === false) {
            $dao->setPk($entity_Comment_base->comment_id);
            $dao->updateByPk($entity_Comment_base);
        } else {
            $dao->setWhere("comment_id IN({$entity_Comment_base->comment_id})");
            $dao->updateByPk($entity_Comment_base);
        }
        if ($dao->getDb()->isSuccess()) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

}