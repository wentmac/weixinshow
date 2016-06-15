<?php

/**
 * 后台 网站更新模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: update.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class updateAction extends service_Controller_admin
{

    private $tmp_model;
    private $updateHost;
    private $uptime;
    private $oktime;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('update');
        $check_model = $this->M('Check');
        $check_model->checkLogin();
        $check_model->CheckPurview('tb_admin');

        //升级服务器，如果有变动，请到 http://www.t-mac.org/ 查询
        $this->updateHost = 'http://update.zhuna/base-v2/';

        //当前软件版本锁定文件
        $verLockFile = VAR_ROOT . '/Data/ver.txt';

        $fp = fopen($verLockFile, 'r');
        $this->upTime = trim(fread($fp, 64));
        fclose($fp);
        $this->oktime = substr($this->upTime, 0, 4) . '-' . substr($this->upTime, 4, 2) . '-' . substr($this->upTime, 6, 2);
    }

    /**
     * 广告管理 首页
     */
    public function index()
    {
        //TODO  
//        $rs = $this->tmp_model->getAdList();
        $upUrl = $this->tmp_model->SpGetNewInfo();
        $this->assign('oktime', $this->oktime);
        $this->assign('upUrl', $upUrl);
        $this->V('update');
    }

    /**
     * check用AJAX获取最新版本信息
     */
    public function checkUpdate()
    {
        //下载远程数据
        $dhd = $this->P('Httpdown');
        $dhd->OpenUrl($this->updateHost . '/verinfo.txt');
        $verlist = trim($dhd->GetHtml());
        $dhd->Close();
        //TODO bg2312 to utf-8
        if ($GLOBALS['TmacConfig']['config']['soft_lang'] == 'utf-8') {
            $verlist = Functions::gb2utf8($verlist);
        }
        $verlist = ereg_replace("[\r\n]{1,}", "\n", $verlist);
        $verlists = explode("\n", $verlist);
        //分析数据
        $upTime = $this->upTime;
        $oktime = $this->oktime;
        $updateVers = array();
        $upitems = $lastTime = '';
        $n = 0;
        foreach ($verlists as $verstr) {
            if (empty($verstr) || ereg("^//", $verstr)) {
                continue;
            }
            list($vtime, $vlang, $issafe, $vmsg) = explode(',', $verstr);
            $vtime = trim($vtime);
            $vlang = trim($vlang);
            $issafe = trim($issafe);
            $vmsg = trim($vmsg);
            if ($vtime > $upTime && $vlang == $GLOBALS['TmacConfig']['config']['soft_lang']) {
                $updateVers[$n]['issafe'] = $issafe;
                $updateVers[$n]['vmsg'] = $vmsg;
                $upitems .= ( $upitems == '' ? $vtime : ',' . $vtime);
                $lastTime = $vtime;
                $updateVers[$n]['vtime'] = substr($vtime, 0, 4) . '-' . substr($vtime, 4, 2) . '-' . substr($vtime, 6, 2);
                $n++;
            }
        }

        //判断是否需要更新，并返回适合的结果
        if ($n == 0) {
            $offUrl = SpGetNewInfo();
            echo "<div class='updatedvt'><b>你系统版本最后更新时间为：{$oktime}，当前没有可用的更新</b></div>\r\n";
            echo "<iframe name='stafrm' src='{$offUrl}&uptime={$oktime}' frameborder='0' id='stafrm' width='100%' height='50'></iframe>";
        } else {
            echo "<div style='width:98%'><form name='fup' action='{PHP_SELF}?m=update.getlist' method='post' onsubmit='ShowWaitDiv()'>\r\n";
            echo "<input type='hidden' name='vtime' value='$lastTime' />\r\n";
            echo "<input type='hidden' name='upitems' value='$upitems' />\r\n";
            echo "<div class='upinfotitle'>你系统版本最后更新时间为：{$oktime}，当前可用的更新有：</div>\r\n";
            foreach ($updateVers as $vers) {
                $style = '';
                if ($vers['issafe'] == 1) {
                    $style = "color:red;";
                }
                echo "<div style='{$style
                }' class='verline'>【" . ($vers['issafe'] == 1 ? "安全更新" : "普通更新") . "】";
                echo $vers['vtime'] . "，更新说明：{$vers['vmsg']
                }</div>\r\n";
            }
            echo "<div style='line-height:32px'><input type='submit' name='sb1' value=' 点击此获取所有更新文件，然后选择安装 ' class='np coolbg' style='cursor:pointer' />\r\n";
            echo "</form></div>";
        }
        exit();
    }

    /**
     * 获取升级文件列表
     */
    public function getlist()
    {
        $updateHost = $this->updateHost;
        $vtime = $_POST['vtime'];
        $upitemsArr = explode(',', $_POST['upitems']);
        rsort($upitemsArr); //逆向排序

        $tmpdir = substr(md5($GLOBALS['TmacConfig']['Common']['cookiepre']), 0, 16);

        $dhd = $this->P('Httpdown');
        $fileArr = array();
        $f = 0;
        foreach ($upitemsArr as $upitem) {
            $durl = $updateHost . $GLOBALS['TmacConfig']['config']['soft_lang'] . '/' . $upitem . '.file.txt';
            $dhd->OpenUrl($durl);
            $filelist = $dhd->GetHtml();
            $filelist = trim(ereg_replace("[\r\n]{1,}", "\n", $filelist));
            if (!empty($filelist)) {
                $filelists = explode("\n", $filelist);
                foreach ($filelists as $filelist) {
                    $filelist = trim($filelist);
                    if (empty($filelist))
                        continue;
                    $fs = explode(',', $filelist);
                    if (empty($fs[1])) {
                        $fs[1] = $upitem . " 常规功能更新文件";
                    }
                    if (!isset($fileArr[$fs[0]])) {
                        $fileArr[$fs[0]] = $upitem . " " . trim($fs[1]);
                        $f++;
                    }
                }
            }
        }
        $dhd->Close();
        $file_info = var_export($durl, true);
        $ok = file_put_contents(TMAC_ROOT . "/file_info.txt", $file_info);

        $allFileList = '';
        if ($f == 0) {
            $allFileList = "<font color='green'><b>没发现可用的文件列表信息，可能是官方服务器存在问题，请稍后再尝试！</b></font>";
        } else {
            $allFileList .= "<div style='width:98%'><form name='fup' action='{PHP_SELF}?m=update.getfiles' method='post'>\r\n";
            $allFileList .= "<input type='hidden' name='vtime' value='$vtime' />\r\n";
            $allFileList .= "<input type='hidden' name='upitems' value='{$_POST['upitems']}' />\r\n";
            $allFileList .= "<div class='upinfotitle'>以下是需要下载的更新文件（路径相对于zun_cms的根目录）：</div>\r\n";
            $filelists = explode("\n", $filelist);
            foreach ($fileArr as $k => $v) {
                $allFileList .= "<div class='verline'><input type='checkbox' name='files[]' value='{$k}'  checked='checked' /> $k({$v})</div>\r\n";
            }
            $allFileList .= "<div class='verline'>";
            $allFileList .= "文件临时存放目录：/Var/Data/<input type='text' name='tmpdir' style='width:200px' value='$tmpdir' /><br />\r\n";
            $allFileList .= "<input type='checkbox' name='skipnodir' value='1'  checked='checked' /> 跳过系统中没有的文件夹(通常是可选模块的补丁)</div>\r\n";
            $allFileList .= "<div style='line-height:36px;background:#F8FEDA'>&nbsp;\r\n";
            $allFileList .= "<input type='submit' name='sb1' value=' 下载并应用这些补丁 ' class='np coolbg' style='cursor:pointer' />\r\n";
            $allFileList .="</form></div>";
        }


        $this->assign('allFileList', $allFileList);
        $this->V('update');
    }


    public function getfiles()
    {
        $cacheFiles = VAR_ROOT . '/Data/updatetmp.inc';
        $skipnodir = (isset($skipnodir) ? 1 : 0);
        $adminDir = ereg_replace("(.*)[/\\]", "", dirname(__FILE__));

        if (!isset($files)) {
            $doneStr = "<p align='center' style='color:red'><br />你没有指定任何需要下载更新的文件，是否跳过这些更新？<br /><br />";
            $doneStr .= "<a href='update_guide.php?dopost=skipback&vtime=$vtime' class='np coolbg'>[跳过这些更新]</a> &nbsp; ";
            $doneStr .= "<a href='index_body.php'  class='np coolbg'>[保留提示以后再进行操作]</a></p>";
        } else {
            $fp = fopen($cacheFiles, 'w');
            fwrite($fp, '<' . '?php' . "\r\n");
            fwrite($fp, '$tmpdir = "' . $tmpdir . '";' . "\r\n");
            fwrite($fp, '$vtime = ' . $vtime . ';' . "\r\n");
            $dirs = array();
            $i = -1;
            foreach ($files as $filename) {
                $tfilename = $filename;
                if (eregi("^dede/", $filename)) {
                    $tfilename = eregi_replace("^dede/", $adminDir . '/', $filename);
                }
                $curdir = GetDirName($tfilename);
                if (!isset($dirs[$curdir])) {
                    $dirs[$curdir] = TestIsFileDir($curdir);
                }
                if ($skipnodir == 1 && $dirs[$curdir]['isdir'] == false) {
                    continue;
                } else {
                    @mkdir($curdir, 0777);
                    $dirs[$curdir] = TestIsFileDir($curdir);
                }
                $i++;
                fwrite($fp, '$files[' . $i . '] = "' . $filename . '";' . "\r\n");
            }
            fwrite($fp, '$fileConut = ' . $i . ';' . "\r\n");

            $items = explode(',', $upitems);
            foreach ($items as $sqlfile) {
                fwrite($fp, '$sqls[] = "' . $sqlfile . '.sql";' . "\r\n");
            }
            fwrite($fp, '?' . '>');
            fclose($fp);

            $dirinfos = '';
            if ($i > -1) {
                $dirinfos = '<tr bgcolor="#ffffff"><td colspan="2">';
                $dirinfos .= "本次升级需要在下面文件夹写入更新文件，请注意文件夹是否有写入权限：<br />\r\n";
                foreach ($dirs as $curdir) {
                    $dirinfos .= $curdir['name'] . " 状态：" . ($curdir['writeable'] ? "[√正常]" : "<font color='red'>[×不可写]</font>") . "<br />\r\n";
                }
                $dirinfos .= "</td></tr>\r\n";
            }

            $doneStr = "<iframe name='stafrm' src='update_guide.php?dopost=getfilesstart' frameborder='0' id='stafrm' width='100%' height='100%'></iframe>\r\n";
        }

        $this->assign('doneStr', $doneStr);
        $this->V('update');
        include DedeInclude('templets/update_guide_getfiles.htm');
        exit();
    }

}