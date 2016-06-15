<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Common.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of search
 *
 * @author Tracy McGrady
 */
class entity_parameter_Common_base
{

    //put your code here
    //供其他条件参数实体继承
    protected $page;
    protected $pagesize = 20;
    protected $query;
    protected $url;

    public function getPage()
    {
        return $this->page;
    }

    public function getPagesize()
    {
        return $this->pagesize;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function setPagesize($pagesize)
    {
        $this->pagesize = $pagesize;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

}
