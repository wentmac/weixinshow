<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Pages_Ajax.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */

/**
 * @example
 * class testAction extends Action {
 *     public function execute() {
 *         $page = Punny::p('Pages');
 * 		$page->setTotal(1000);
 *      $page->setUrl(url);
 * 		$page->setPrepage(20);
 * 		echo $page->show();
 *     }
 * }
 */
class Pages_Ajax
{

    /**
     * 每页的记录数
     *
     * @var int
     */
    private $perpage = 20;
    /**
     * 总记录数
     *
     * @var int
     */
    private $total;
    /**
     * 当前页
     *
     * @var int
     */
    private $currentPage = 1;
    /**
     * 总页数
     *
     * @var int
     */
    private $pages;
    /**
     * url参数
     *
     * @var string
     */
    private $param;

    /**
     * 构造器
     *
     */
    public function __construct($pagenow = '')
    {
        $this->getCurrentPage($pagenow);
    }

    /**
     * 设置总记录数
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = (int) $total;        
    }

    /**
     * 设置每页的记录数
     *
     * @param int $prepage
     */
    public function setPrepage($prepage)
    {
        $this->perpage = (int) $prepage;
    }

    /**
     * 获取当前页数
     *
     */
    private function getCurrentPage($pagenow)
    {
        if (empty($pagenow)) {
            isset($_GET['page']) ? $page = (int) $_GET['page'] : $page = 1;
        } else {
            $page = $pagenow;
        }
        $page >= 1 && $this->currentPage = $page;   
    }

    /**
     * 获取当前页数
     * 
     * @return int
     */
    public function getNowPage()
    {
        return $this->currentPage;
    }

    /**
     * 获取MySQL分页SQL的LIMIT语句
     * 
     * @return string
     */
    public function getSqlLimit()
    {
        $this->pages = ceil($this->total / $this->perpage);
        //如果当前currentPage 大于 总pages的话就显示最后一页
        $this->currentPage > $this->pages && $this->currentPage = $this->pages;
        return ($this->currentPage - 1) * $this->perpage . ',' . $this->perpage;
    }

    /**
     * 获取url参数
     *
     */
    public function setUrl($url)
    {
//        unset($_GET['page']);        
        $this->param = $url;
    }

    /**
     * 创建连接
     *
     * @param int $page
     * @return string
     */
    private function getLink($page)
    {                
        $param_array = $this->param;
        $page_action = $param_array[0];
        array_shift($param_array);
        $page_param = implode(',',$param_array);
        return "javascript:$page_action($page_param,$page)";
    }

    /**
     * 获取第一页
     *
     * @return string
     */
    private function getFirstPage()
    {
        if ($this->currentPage == 1) {
            return "<span>&lt;&lt;</span>";
        } else {
            return '<a href="' . $this->getLink(1) . '">&lt;&lt;</a>';
        }
    }

    /**
     * 获取最后一页
     *
     * @return string
     */
    private function getLastPage()
    {
        if ($this->currentPage == $this->pages) {
            return "<span>&gt;&gt;</span>";
        } else {
            return '<a href="' . $this->getLink($this->pages) . '">&gt;&gt;</a>';
        }
    }

    /**
     * 获取上一页
     *
     * @return string
     */
    private function getPrePage()
    {
        if ($this->currentPage == 1) {
            return " <span>&lt;</span> ";
        } else {
            return ' <a href="' . $this->getlink($this->currentPage - 1) . '">&lt;</a>';
        }
    }

    /**
     * 获取下一页
     *
     * @return string
     */
    private function getNextPage()
    {
        if ($this->currentPage == $this->pages) {
            return "<span>&gt;</span>";
        } else {
            return '<a href="' . $this->getlink($this->currentPage + 1) . '">&gt;</a>';
        }
    }

    /**
     * 显示分页
     *
     * @return <string>
     */
    public function show()
    {
        if (!isset($this->total)) {
            throw new TmacException('无法找到总记录数!');
        }

        
        if ($this->currentPage < 5) {
            $begin = 1;
            $end = 10;
        } else if ($this->currentPage > $this->pages - 10) {
            $begin = $this->pages - 10;
            $end = $this->pages;
        } else {
            $begin = $this->currentPage - 4;
            $end = $this->currentPage + 5;
        }
        $begin < 1 && $begin = 1;
        $end > $this->pages && $end = $this->pages;
        if ($this->total > $this->perpage) {
            $page = '<div id="pages"><em>' . $this->total . '</em>';
            $page .= $this->getFirstPage();
//            if ($this->currentPage > 1) { //取消注释首页还能看到首页的位置(无链接)
                $page .= $this->getPrePage();
//            }
            for ($i = $begin; $i <= $end; $i++) {
                if ($i == $this->currentPage) {
                    $page .= " <strong>$i</strong> ";
                } else {
                    $page .= ' <a href="' . $this->getlink($i) . '">' . $i . '</a> ';
                }
            }
//            if ($this->currentPage < $this->pages) {
                $page .= $this->getNextPage();
                $page .= $this->getLastPage();
//            }
            $page .= '</div>';
        } else {
//            $page .= $this->getFirstPage();
              $page = '';
        }

        return $page;
    }

}