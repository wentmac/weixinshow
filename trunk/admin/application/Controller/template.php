<?php

/**
 * 后台 模板操作 模块 Controller
 * ============================================================================
 * zhuna_php 住哪网酒店分销联盟程序php版　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: template.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://un.zhuna.cn；
 */
class templateAction extends service_Controller_admin
{

    private $tmp_model;
    private $template_dir;
    private $style_dir;
    private $style_url;
    private $config_www_dir;
    private $dh;
    private $style_dh;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('Template');
        $check_model = $this->M('Check');
        $check_model->checkLogin();
        $check_model->CheckPurview('tb_admin');

        //前台模板路径
        $this->template_dir = TMAC_BASE_PATH . APP_WWW_NAME . DIRECTORY_SEPARATOR . APPLICATION . DIRECTORY_SEPARATOR . $GLOBALS['TmacConfig']['Template']['template'] . DIRECTORY_SEPARATOR;
        $this->style_dir = TMAC_BASE_PATH . 'Public' . DIRECTORY_SEPARATOR . APP_WWW_NAME . DIRECTORY_SEPARATOR;
        $this->style_url = STATIC_URL . APP_WWW_NAME.'/';
        $this->config_www_dir = TMAC_BASE_PATH . APP_WWW_NAME . DIRECTORY_SEPARATOR . APPLICATION . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'config.php';
        $this->dh = @dir($this->template_dir);
        $this->style_dh = @dir($this->style_dir);

        $this->tmp_model->setStyle_url($this->style_url);
    }

    /**
     * 选择模板
     */
    public function index()
    {
        //TODO  取出模板列表
        $filelists = Array();
        while (($filename = @$this->style_dh->read()) !== false) {
            if ($filename != '.' && $filename != '..' && $filename != '.svn')
                $filelists[] = $filename;
        }        
        $this->style_dh->close();        
        foreach ($filelists AS $k => $v) {            
            $info[] = $this->tmp_model->get_template_info($v, $this->style_dir);
        }
        //当前模板风格
        require($this->config_www_dir);
        $style_now = $this->tmp_model->get_template_info($GLOBALS['TmacConfig']['Template']['template_dir'], $this->style_dir);

        $this->assign('info', $info);
        $this->assign('style_now', $style_now);
        $this->V('template');
    }

    /**
     * 修改默认模板
     */
    public function ajaxSaveDefaultTemplate()
    {
        $template_dir = $this->H($this->getParam('template_dir'));
        if (empty($template_dir)) {
            $return_array['error'] = '请选择正确的模板风格！';
        }

        //前台配置文件路径
        $configfile = $this->config_www_dir;
        //重写配置 文件        
        $rs = $this->tmp_model->editConfigTemplate($configfile, $template_dir);
        if (!$rs) {
            $return_array['error'] = '操作失败请重试,确保' . $configfile . '文件有可写权限！';
        } else {
            $return_array['success'] = '启用模板成功！';
        }

        //当前模板风格
        $style_now = $this->tmp_model->get_template_info($template_dir, $this->style_dir);
        $return_array['style_now'] = $style_now;

        $template_return_string = json_encode($return_array);
        echo $template_return_string;
    }

    /**
     * 模板修改列表
     */
    public function edit()
    {
        //TODO  取出模板列表
        $filelists = Array();
        while (($filename = @$this->style_dh->read()) !== false) {
            if ($filename != '.' && $filename != '..' && $filename != '.svn')
                $filelists[] = $filename;
        }
        $this->style_dh->close();
        foreach ($filelists AS $k => $v) {
            $info[] = $this->tmp_model->get_template_info($v, $this->style_dir);
        }
        $this->assign('info', $info);
        $this->V('template');
    }

    /**
     * 目录模板文件列表
     */
    public function temlist()
    {
        $dir = $this->H($this->getParam('dir'));
        if (empty($dir)) {
            $this->redirect('请选择要查看/修改的模板目录！');
            exit;
        }
        $tmp_dir = $this->template_dir . $dir;
        //显示模板的相对路径
        $relative_tmp_dir = str_replace(TMAC_BASE_PATH, '', $tmp_dir);
        if (!$dh = @dir($tmp_dir)) {
            $this->redirect("没找到{$dir}的模板数据");
            exit;
        }
        $filelists = $this->tmp_model->recurDir($tmp_dir);
        $filelists_html = $this->tmp_model->recurTemDir($filelists, $tmp_dir, $dir, 0, 'template.show');
        if (!$filelists || count($filelists) == 0) {
            $ErrorMsg = '没有模板文件！';
        } else {
            $ErrorMsg = '';
        }

        $this->assign('tmp_dir', $tmp_dir);
        $this->assign('dir', $dir);
        $this->assign('relative_tmp_dir', $relative_tmp_dir);
        $this->assign('filelists', $filelists_html);
        $this->assign('ErrorMsg', $ErrorMsg);
        $this->V('template');
    }

    public function show()
    {
        $dir = $this->H($this->getParam('dir'));
        $dir = str_replace('..', '', $dir);
        $dirname = $this->H($this->getParam('dirname'));
        $template_file = $this->template_dir . $dirname . DIRECTORY_SEPARATOR . $dir;
        if (!is_file($template_file) || empty($dir)) {
            $this->redirect('请选择要查看/修改的模板文件！');
            exit;
        }
        $edittime = date('Y-m-d H:i:s', filemtime($template_file));
        $template_file_info = file_get_contents($template_file);

        $this->assign('template_file_info', $template_file_info);
        $this->assign('dirname', $dirname);
        $this->assign('edittime', $edittime);
        $this->assign('dir', $dir);
        $this->V('template');
    }

    public function showsave()
    {
        $dir = $this->H($this->getParam('dir'));
        $dir = str_replace('..', '', $dir);
        $dirname = $this->H($this->getParam('dirname'));
        $info = stripslashes($this->getParam('info'));
        if (empty($info)) {
            $this->redirect('对不起，模板的内容不能为空！');
            exit;
        }
        $template_file = $this->template_dir . $dirname . DIRECTORY_SEPARATOR . $dir;
        if (!is_file($template_file) || empty($dir)) {
            $this->redirect('请选择要查看/修改的模板文件！');
            exit;
        }
        if (is_writable($template_file)) {
            $fp = fopen($template_file, 'w');
            if (fwrite($fp, $info) === FALSE) {
                $this->redirect('操作失败请重试！');
                exit;
            }
            fclose($fp); //完成对配置文件的读写操作
        }

        $this->redirect('修改' . $dir . '成功！', PHP_SELF . '?m=template.temlist&dir=' . $dirname);
        exit;
    }

    /**
     * 样式修改
     */
    public function stylelist()
    {
        $dir = $this->H($this->getParam('dir'));
        $dir = str_replace('..', '', $dir);
        if (empty($dir)) {
            $this->redirect('请选择要查看/修改的样式目录！');
            exit;
        }
        $tmp_dir = $this->style_dir . $dir;
        //显示模板的相对路径
        $relative_tmp_dir = str_replace(TMAC_BASE_PATH, '', $tmp_dir);
        if (!$dh = @dir($tmp_dir)) {
            $this->redirect("没找到{$dir}的样式数据");
            exit;
        }
        $filelists = $this->tmp_model->recurDirStyleFile($tmp_dir);
        $filelists_html = $this->tmp_model->recurTemDir($filelists, $tmp_dir, $dir, 0, 'template.showstyle');
        if (!$filelists || count($filelists) == 0) {
            $ErrorMsg = '没有样式文件！';
        } else {
            $ErrorMsg = '';
        }

        $this->assign('tmp_dir', $tmp_dir);
        $this->assign('dir', $dir);
        $this->assign('relative_tmp_dir', $relative_tmp_dir);
        $this->assign('filelists', $filelists_html);
        $this->assign('ErrorMsg', $ErrorMsg);
        $this->V('template');
    }

    public function showstyle()
    {
        $dir = $this->H($this->getParam('dir'));
        $dir = str_replace('..', '', $dir);
        $dirname = $this->H($this->getParam('dirname'));
        $template_file = $this->style_dir . $dirname . DIRECTORY_SEPARATOR . $dir;
        if (!is_file($template_file) || empty($dir)) {
            $this->redirect('请选择要查看/修改的样式文件！');
            exit;
        }
        $edittime = date('Y-m-d H:i:s', filemtime($template_file));
        $template_file_info = file_get_contents($template_file);

        $this->assign('template_file_info', $template_file_info);
        $this->assign('dirname', $dirname);
        $this->assign('edittime', $edittime);
        $this->assign('dir', $dir);
        $this->V('template');
    }

    /**
     * 样式文件修改保存
     */
    public function showstylesave()
    {
        $dir = $this->H($this->getParam('dir'));
        $dir = str_replace('..', '', $dir);
        $dirname = $this->H($this->getParam('dirname'));
        $info = stripslashes($this->getParam('info'));
        if (empty($info)) {
            $this->redirect('对不起，样式的内容不能为空！');
            exit;
        }
        $template_file = $this->style_dir . $dirname . DIRECTORY_SEPARATOR . $dir;
        if (!is_file($template_file) || empty($dir)) {
            $this->redirect('请选择要查看/修改的样式文件！');
            exit;
        }
        if (is_writable($template_file)) {
            $fp = fopen($template_file, 'w');
            if (fwrite($fp, $info) === FALSE) {
                $this->redirect('操作失败请重试！');
                exit;
            }
            fclose($fp); //完成对配置文件的读写操作
        }

        $this->redirect('修改' . $dir . '成功！', PHP_SELF . '?m=template.stylelist&dir=' . $dirname);
        exit;
    }

}
