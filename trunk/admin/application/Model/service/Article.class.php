<?php

/**
 * 后台文章模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Article.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Article_admin extends Model
{

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        
    }

    /**
     * 保存
     * @param string $menusMain
     * return Boole
     */
    public function modifyArticleAll(entity_Article_base $entity_Article_base, entity_Addonarticle_base $entity_Addonarticle_base)
    {

        $dao = dao_factory_base::getArticleDao();

        $dao->getDb()->startTrans();

        $dao->setPk($entity_Article_base->article_id);
        $dao->updateByPk($entity_Article_base);

        $dao_addonarticle = dao_factory_base::getAddonarticleDao();
        $dao_addonarticle->setPk($entity_Addonarticle_base->article_id);
        $dao_addonarticle->updateByPk($entity_Addonarticle_base);

        if ($dao->getDb()->isSuccess()) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * insert
     * @param string $menusMain
     * return Boole
     */
    public function createArticleAll(entity_Article_base $entity_Article_base, entity_Addonarticle_base $entity_Addonarticle_base)
    {

        $dao = dao_factory_base::getArticleDao();

        $dao->getDb()->startTrans();
        $entity_Article_base->time = $this->now;
        $article_id = $dao->insert($entity_Article_base);

        $dao_addonarticle = dao_factory_base::getAddonarticleDao();
        $entity_Addonarticle_base->article_id = $article_id;
        $dao_addonarticle->insert($entity_Addonarticle_base);

        if ($dao->getDb()->isSuccess()) {
            $dao->getDb()->commit();
            return $article_id;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 获取一个资讯栏目信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getArticleInfo($aid)
    {
        $dao = dao_factory_base::getArticleDao();
        return $dao->getArticleInfoById($aid);
    }

    /**
     * 获取所有资讯
     * return article_class,pages
     */
    public function getArticleList(entity_parameter_Article_base $entity_parameter_Article_base)
    {

        if ($entity_parameter_Article_base->getUrl() == null) {
            $url = PHP_SELF . '?m=article';
        } else {
            $url = $entity_parameter_Article_base->getUrl();
        }
        if ($entity_parameter_Article_base->getCat_id() != null) {
            $cat_ids = $this->M('Category')->getSonTreeList($entity_parameter_Article_base->getCat_id());
            if ($cat_ids != $entity_parameter_Article_base->getCat_id()) {
                $entity_parameter_Article_base->setCat_ids($cat_ids);
            }
            $url .= "&cat_id={$entity_parameter_Article_base->getCat_id()}";
        }
        if ($entity_parameter_Article_base->getQuery() != null) {
            $url .= "&search_keyword={$entity_parameter_Article_base->getQuery()}";
        }
        if ($entity_parameter_Article_base->getChannelid() != null) {
            $url .= "&channelid={$entity_parameter_Article_base->getChannelid()}";
        }

        $dao = dao_factory_base::getArticleDao();
        $where = $dao->getListWhere($entity_parameter_Article_base);
        $dao->setWhere($where);
        $count = $dao->getCountByWhere();

        $pages = $this->P('Pages');
        $pages->setTotal($count);
        $pages->setUrl($url);
        $pages->setPrepage(20);
        $limit = $pages->getSqlLimit();

        $dao->setField('article_id, cat_id, title, time, click_count, channel, status');
        $dao->setOrderby('article_id DESC');
        $dao->setLimit($limit);

        $rs = $dao->getListByWhere();

        //取所有的资讯栏目 不用LEFT JOIN取class_name
        $dao_category = dao_factory_base::getCategoryDao();
        $rs_class = $dao_category->getListByWhere();

        //取内容模型
        $channeltype = Tmac::config('channel.channeltype');

        //取状态
        $status_array = Tmac::config('article.status.boolean');

        $category_name_array = array();
        //重组栏目category信息数组
        foreach ($rs_class AS $kk => $vv) {
            $category_name_array[$vv->cat_id] = $vv->cat_name;
        }
        //遍历通过class_id取class_name
        if (is_array($rs)) {
            foreach ($rs AS $k => $v) {
                $rs[$k]->cat_name = $category_name_array[$v->cat_id];
                $rs[$k]->time = date('Y/m/d H:i:s', $v->time);
                $rs[$k]->channeltype = $channeltype[$v->channel];
                $rs[$k]->status = $status_array[$v->status];
            }
        }

        $channelid = $entity_parameter_Article_base->getChannelid();
        $type = empty($channelid) ? '1' : $channelid;
        //把文章的当前page写到cookies里
        HttpResponse::setCookie('article_page_' . $channelid, $pages->getNowPage());

        $ErrorMsg = '';
        if ($count == 0) {
            $ErrorMsg = "暂无资讯文章!";
        }

        $result = array('rs' => $rs, 'pageCurrent' => $pages->getNowPage(), 'page' => $pages->show(), 'ErrorMsg' => $ErrorMsg);
        return $result;
    }

    /**
     * del
     * @param int $class_id
     */
    public function deleteByArticleId($id)
    {
        $dao = dao_factory_base::getArticleDao();
        $dao_addonarticle = dao_factory_base::getAddonarticleDao();

        $dao->getDb()->startTrans();
        if (strpos($id, ',') === false) {
            $dao->setPk($id);
            $dao->deleteByPk();
            $dao_addonarticle->setPk($id);
            $dao_addonarticle->deleteByPk();
        } else {
            $dao->setWhere("article_id IN({$id})");
            $dao->deleteByWhere();
            $dao_addonarticle->setWhere("article_id IN({$id})");
            $dao_addonarticle->deleteByWhere();
        }

        if ($dao->getDb()->isSuccess()) {
            $dao->getDb()->commit();
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
    }

    /**
     * 取用户Array
     * @param type $uid
     * @return type 
     */
    public function getUserArray($uid)
    {
        $dao = dao_factory_base::getUserDao();
        if ($uid > 1) {
            $dao->setPk($uid);
            $dao->setField('uid,nicename');
            $res = $dao->getInfoByPk();            
        } else {            
            $dao->setField('uid, nicename');
            $dao->setOrderby('uid ASC');
            $res = $dao->getListByWhere();            
        }        
        return $res;
    }

}
