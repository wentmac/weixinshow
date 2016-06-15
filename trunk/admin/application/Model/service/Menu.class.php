<?php

/**
 * Menu
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Menu.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Menu_admin extends Model
{

    private $check_model;

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        //连接数据库
        $this->check_model = $this->M('Check');
    }

    /**
     * 取出菜单数组
     * @param string $menusMain
     * return array $menua
     */
    public function getMenua($menusMain)
    {        
        $menuaAry = $this->MenusMakeAry($menusMain);
        $menua = array();
        foreach ($menuaAry AS $value) {
            $menua[] = $value;
        }
        return $menua;
    }

    /**
     * 取出Meun里的TOP
     * @param string $menubody
     * return array
     */
    public function getMeunTop($menubody)
    {
        preg_match_all("/mapitem='(.*)' name='(.*)' rank='(.*)'/isU", $menubody, $result);
        return $result;
    }

    /**
     * 取出Meun里的Item
     * @param string innerbody
     * return array
     */
    public function getMeunItem($innerbody)
    {
        preg_match_all("/ name='(.*)' link='(.*)' rank='(.*)' target='(.*)' /isU", $innerbody, $reitem);
        return $reitem;
    }

    /**
     * ######## 生成菜单数组函数#######   MenusMakeAry($MenusMain字符串)
     * @param array $MenusMain
     * return array
     */
    public function MenusMakeAry($MenusMain)
    {
        $MenusAry = preg_match_all("/<m:top(.*)>(.*)<\/m:top>/isU", $MenusMain, $result);
        $MenusArray = array();
        foreach ($result[1] AS $k => $v) {
            $mapitemAry = $this->getMeunTop($v);
            $mapitem = $mapitemAry[1][0];      //顺序 ID
            $name = $mapitemAry[2][0];       //news
            $rank = $mapitemAry[3][0];       //rank
            $inner = $result[2][$k];      //item
            if ($this->check_model->CheckPurviewMenu($rank) != false)
                continue;
            $MenusItem = preg_match_all("/<m:item(.*)\/>/isU", $inner, $rsinner);
            $MenusArray[$k]['id'] = $mapitem;
            $MenusArray[$k]['title'] = $name;
            $MenusArray[$k]['subname'] = array();        //初始化数组
            foreach ($rsinner[1] AS $vv) {
                $linkbody = $this->getMeunItem($vv);                
                $nametype = $linkbody[1][0];
                $linktype = $linkbody[2][0];
                $ranktype = $linkbody[3][0];
                $targettype = $linkbody[4][0];
                if ($this->check_model->CheckPurviewMenu($ranktype) != false)
                    continue;
//                $MenusArray[$k]['subname'][] = "name='".$nametype."' link='".$linktype."' rank='".$ranktype."' target='".$targettype."'";
                //加上rank 权限判断后 !important;
                $MenusArray[$k]['subname'][] = "<a href=" . $linktype . " target=" . $targettype . ">" . $nametype . "</a>";
            }
        }
        return $MenusArray;
    }

    public function gdversion()
    {
        dao_factory_base::getUserDao();
        //没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
        if (!function_exists('phpinfo')) {
            if (function_exists('imagecreate'))
                return '2.0';
            else
                return 0;
        }
        else {
            ob_start();
            phpinfo(8);
            $module_info = ob_get_contents();
            ob_end_clean();
            if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches)) {
                $gdversion_h = $matches[1];
            } else {
                $gdversion_h = 0;
            }
            return $gdversion_h;
        }
    }

}