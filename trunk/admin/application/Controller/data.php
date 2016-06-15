<?php

/**
 * 后台 数据库备份还原相关模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: data.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class dataAction extends service_Controller_admin
{

    private $tmp_model;
    private $bkdir;
    private $dh;
    private $dbinfo;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('Data');
        $check_model = $this->M('Check');
        $check_model->checkLogin();
        $check_model->CheckPurview('tb_admin');

        $this->dbinfo = $GLOBALS['db'][$GLOBALS['TmacConfig']['Common']['Database']];
        $cfg_db_language = $this->dbinfo['char_set'];
        $this->bkdir = VAR_ROOT . 'Data/' . $GLOBALS['TmacConfig']['config']['backup_dir'] . '/';
        $this->dh = @dir($this->bkdir);
        $this->assign('php_self', PHP_SELF);
    }

    /**
     * 备份 首页
     */
    public function back()
    {
        //TODO  取出所有广告
        $rs = $this->tmp_model->getTableAry();

        $this->assign('dname', $this->dbinfo['database']);
        $this->assign('mysql_version', mysql_get_server_info());
        $this->assign('tableAry', $rs);
        $this->V('data_back');
    }

    /**
     * 执行备份
     */
    public function dobak()
    {
        @set_time_limit(0);
        $tablearr = empty($_POST['tablearr']) ? '' : $_POST['tablearr'];  //tablearr
        $startpos = empty($_POST['startpos']) ? '0' : $_POST['startpos'];  //startpos一个表的分表娄
        $isstruct = empty($_POST['isstruct']) ? '0' : $_POST['isstruct'];  //isstruct
        $fsize = empty($_POST['fsize']) ? '2048' : $_POST['fsize'];  //fsize
        $datatype = empty($_POST['datatype']) ? '4.1' : $_POST['datatype'];  //datatype
        $start_count = empty($_POST['start_count']) ? '0' : $_POST['start_count'];  //start_count
        $limit_do_count = empty($_POST['limit_do_count']) ? '0' : $_POST['limit_do_count'];  //limit_do_count
        if (empty($tablearr)) {
            $this->redirect('你没选中任何表！');
            exit();
        }

        $this->tmp_model->doBackupData($tablearr, $startpos, $isstruct, $fsize, $datatype, $start_count, $limit_do_count);
    }

    /**
     * 还原页面
     */
    public function revert()
    {
        $filelists = Array();
        while (($filename = @$this->dh->read()) !== false) {
            if ($filename != '.' && $filename != '..')
                $filelists[] = $filename;
        }
        $this->dh->close();
        $this->assign('filelists', $filelists);
        $this->assign('action', '');
        $this->V('data_revert');
    }

    /**
     * 还原表list
     */
    public function revertlist()
    {
        $path = $_GET['path'];
        $bkdir = $this->bkdir . '/' . $path;
        if ($path == "") {
            $this->redirect("请选择还原日期");
            exit;
        }
        if (!$dh = @dir($bkdir)) {
            $this->redirect("没找到{$path}的备份数据");
            exit;
        }
        $structfile = "没找到数据结构文件";
        $filelists = array();
        while (($filename = $dh->read()) !== false) {
            if (!preg_match('/txt$/', $filename)) {
                continue;
            }
            if (preg_match('/tables_struct/', $filename)) {
                $structfile = $filename;
            } else if (filesize("$bkdir/$filename") > 0) {
                $filelists[] = $filename;
            }
        }


        $dh->close();
        $this->assign('path', $path);
        $this->assign('filelists', $filelists);
        $this->assign('structfile', $structfile);
        $this->assign('action', 'do');
        $this->V('data_revert');
    }

    /**
     * 执行还原
     */
    public function dorevert()
    {
        @set_time_limit(0);
        $this->tmp_model->doRevertData();
    }

    /**
     * 执行删除
     */
    public function delete()
    {
        $this->tmp_model->delData();
    }

}