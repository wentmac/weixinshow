<?php

/**
 * 后台 文档 模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Archives.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Archives_admin extends Model
{

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        
    }

    /**
     * 检测频道ID
     * @param <type> $cat_id
     * @param <type> $channelid
     * @return <type>
     */
    public function checkChannel($cat_id, $channelid)
    {                
        $dao = dao_factory_base::getCategoryDao();
        $dao->setField('channeltype');
        $dao->setPk($cat_id);
        $cat_info = $dao->getInfoByPk();        
        
        if ($cat_info->channeltype != $channelid) {
            return false;
        } else {
            return true;
        }
    }

}
