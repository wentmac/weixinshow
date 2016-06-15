<?php

/**
 * 后台友情链接模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: User.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_User_admin extends Model
{

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        
    }

    public function createUser(entity_User_base $entity_User_base)
    {
        $dao = dao_factory_base::getUserDao();
        return $dao->insert($entity_User_base);
    }

    public function modifyUser(entity_User_base $entity_User_base)
    {
        $dao = dao_factory_base::getUserDao();
        $dao->setPk($entity_User_base->uid);
        return $dao->updateByPk($entity_User_base);
    }

    /**
     * 获取一个管理员信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getUserInfo($id)
    {
        $dao = dao_factory_base::getUserDao();
        $dao->setPk($id);
        return $rs = $dao->getInfoByPk();
    }

    public function checkUserName($username, $id)
    {
        $dao = dao_factory_base::getUserDao();
        $where = "username='{$username}' ";
        if (!empty($id)) {
            $where .= "AND uid<>{$id}";
        }
        $dao->setWhere($where);
        return $rs = $dao->getInfoByWhere();
    }

    /**
     * 获取所有管理员
     * return article_class,pages
     */
    public function getUserList(entity_parameter_Common_base $entity_parameter_Common_base)
    {
        if ($entity_parameter_Common_base->getUrl() == null) {
            $url = PHP_SELF . '?m=user';
        } else {
            $url = $entity_parameter_Common_base->getUrl();
        }

        $url .="&page=";

        $dao = dao_factory_base::getUserDao();
        $count = $dao->getCountByWhere();

        $pages = $this->P('Pages');
        $pages->setTotal($count);
        $pages->setUrl($url);
        $pages->setPrepage($entity_parameter_Common_base->getPagesize());
        $limit = $pages->getSqlLimit();

        $dao->setOrderby('uid ASC');
        $dao->setLimit($limit);
        $rs = $dao->getListByWhere();

        //取管理员类型option数组        
        $admintype_ary = $this->getAdminType();
        $admintype_array = array();
        foreach ($admintype_ary AS $vv) {
            $admintype_array[$vv->rank] = $vv->type_name;
        }

        if (is_array($rs)) {
            foreach ($rs AS $k => $v) {
                $rs[$k]->typename = $admintype_array[$v->rank];
                $rs[$k]->time = date('Y-m-d H:i:s', $v->reg_time);
            }
        }
        //把文章的当前page写到cookies里
        HttpResponse::setCookie('user_page', $pages->getNowPage());

        if ($count == 0) {
            $ErrorMsg = "暂无管理员!   <a href='" . PHP_SELF . "?m=user.add' class='link_a'>点我来添加新友情链接</a>";
        } else {
            $ErrorMsg = '';
        }

        $result = array('rs' => $rs, 'pageCurrent' => $pages->getNowPage(), 'page' => $pages->show(), 'ErrorMsg' => $ErrorMsg);
        return $result;
    }

    /**
     * del
     * @param int $class_id
     */
    public function deleteByUid($id)
    {
        $dao = dao_factory_base::getUserDao();

        $dao->getDb()->startTrans();
        if (strpos($id, ',') === false) {
            $dao->setPk($id);
            $dao->deleteByPk();
        } else {
            $dao->setWhere("uid IN({$id})");
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

    public function getAdminType()
    {
        $dao_user_type = dao_factory_base::getUserTypeDao();
        $dao_user_type->setOrderby('rank ASC');
        return $dao_user_type->getListByWhere();
    }

}
