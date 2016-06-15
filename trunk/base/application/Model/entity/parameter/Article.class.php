<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Article.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of search
 *
 * @author Tracy McGrady
 */
class entity_parameter_Article_base
{

    //put your code here
    private $page;
    private $pagesize;
    private $cat_id;
    private $cat_ids;
    private $channelid;
    private $query;
    private $url;

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPagesize()
    {
        return $this->pagesize;
    }

    public function setPagesize($pagesize)
    {
        $this->pagesize = $pagesize;
    }

    public function getCat_id()
    {
        return $this->cat_id;
    }

    public function setCat_id($cat_id)
    {
        $this->cat_id = $cat_id;
    }

    public function getCat_ids()
    {
        return $this->cat_ids;
    }

    public function setCat_ids($cat_ids)
    {
        $this->cat_ids = $cat_ids;
    }

    public function getChannelid()
    {
        return $this->channelid;
    }

    public function setChannelid($channelid)
    {
        $this->channelid = $channelid;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

}
