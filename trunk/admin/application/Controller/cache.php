<?php

/**
 * 后台 清理硬盘缓存 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: cache.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class cacheAction extends service_Controller_admin
{

    private $tmp_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('Clearcache');
        $check_model = $this->M('Check');
        $check_model->checkLogin();
        $check_model->CheckPurview('tb_admin');
    }

    /**
     * 管理员类别管理 首页
     */
    public function index()
    {
        @set_time_limit(0);
        //TODO  取出所有缓存情况
        $rs = $this->tmp_model->getCacheList();
        $this->assign($rs);
        $this->V('cache');
    }

    /**
     * 新增/修改管理员
     */
    public function del()
    {
        if (empty($_GET['folder'])) {
            $this->redirect('要删除的目录不能为空!');
            exit;
        }
        $folder = !empty($_GET['folder']) ? $_GET['folder'] : '';
        $folder = stripslashes($folder);
        $folder = TMAC_BASE_PATH . $folder;
        $rs = $this->tmp_model->delCacheList($folder);
        $this->V('cache');
    }

}