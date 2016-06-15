<?php

/**
 * 后台系统配置参数模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: config.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class configAction extends service_Controller_admin
{

    private $tmp_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('Config');
        $check_model = $this->M('Check');
        $check_model->checkLogin();
        $check_model->CheckPurview('tb_admin');
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        //TODO  取出所有资讯
        $rs = $this->tmp_model->getConfigList();
        $this->assign('rs', $rs);
        $this->V('config');
    }

    /**
     * 新增/修改系统配置参数
     */
    public function add()
    {
        $sysid = Input::get('sysid', 0)->int();

        $entity_Sysconfig_base = new entity_Sysconfig_base();
        if ($sysid > 0) {
            $entity_Sysconfig_base = $this->tmp_model->getConfigInfo($sysid);
        }
        //取系统配置参数类型radiobutton数组
        $vartype_ary = UtilityConfig::vartype_ary();
        $vartype_ary_radio = Utility::RadioButton($vartype_ary, 'vartype', $entity_Sysconfig_base->type, 'onclick="typechange(this.value);"');

        $this->assign('vartype_ary_radio', $vartype_ary_radio);
        $this->assign('editinfo', $entity_Sysconfig_base);

        $this->V('config');
    }

    /**
     * 新增/修改系统配置参数页面　保存　
     */
    public function save()
    {
        if (empty($_POST) || count($_POST) < 4) {
            $this->redirect('don\'t be evil');
            exit;
        }

        /* 初始化变量 */
        $sysid = Input::post('sysid', 0)->int();
        $nvarname = Input::post('nvarname', '')->required('变量名称不能为空')->string();
        $nvarvalue = Input::post('nvarvalue', '')->string();
        $vartype = Input::post('vartype', '')->required('选择变量类型')->string();
        $nameaction = Input::post('nameaction', '')->string();
        $item = Input::post('item', '')->string();
        $varmsg = Input::post('varmsg', '')->sql();
        $help = Input::post('help', '')->string();
        $order = Input::post('order', 0)->int();


        if (Filter::getStatus() === false) {
            $this->redirect(Filter::getFailMessage());
        }
        // TODO goon to verify

        $entity_Sysconfig_base = new entity_Sysconfig_base();
        $entity_Sysconfig_base->varname = $nvarname;
        $entity_Sysconfig_base->value = $nvarvalue;
        $entity_Sysconfig_base->type = $vartype;
        $entity_Sysconfig_base->nameaction = $nameaction;
        $entity_Sysconfig_base->item = $item;
        $entity_Sysconfig_base->info = $varmsg;
        $entity_Sysconfig_base->help = $help;
        $entity_Sysconfig_base->sys_order = $order;

        if ($sysid > 0) {
            $config_page = HttpResponse::getCookie('config_page');
            $entity_Sysconfig_base->sys_id = $sysid;
            $rs = $this->tmp_model->modifyConfig($entity_Sysconfig_base);
            if ($rs) {
                $this->redirect('修改系统配置参数成功', PHP_SELF . '?m=config&page= ' . $config_page . '');
            } else {
                $this->redirect('修改系统配置参数失败');
            }
        } else {
            //insert save article_class
            $rs = $this->tmp_model->createConfig($entity_Sysconfig_base);
            if ($rs) {
                $this->redirect('添加系统配置参数成功', PHP_SELF . '?m=config.add');
            } else {
                $this->redirect('添加系统配置参数失败');
            }
        }
    }

    public function savelist()
    {
        if ($_POST['cfg_rewrite'] == 1) {
            /*
              $rewrite_search = $this->tmp_model->getConfigInfo(13);
              $rewrite_search = $rewrite_search['value'];
              $hotel_info_name = $this->tmp_model->getConfigInfo(14);
              $hotel_info_name = $hotel_info_name['value'];

              $htaccess_search = array('^' . $rewrite_search, '^' . $hotel_info_name);
              $htaccess_replace = array('^' . $_POST['cfg_rewrite_search'], '^' . $_POST['cfg_hotel_info']);
             */
            $htaccess = $_POST['cfg_rewrite_rule'];
            $htaccess_file = WEB_ROOT . '.htaccess';
//          $htaccess = str_replace($htaccess_search, $htaccess_replace, $htaccess);
            file_put_contents($htaccess_file, htmlspecialchars_decode(stripslashes($htaccess)), LOCK_EX);
            $_POST['cfg_rewrite_rule'] = $htaccess;
        }
        foreach ($_POST as $k => $v) {
            $entity_Sysconfig_base = new entity_Sysconfig_base();
            $value = $this->H($v);
            $entity_Sysconfig_base->value = $value;
            $rs = $this->tmp_model->modifyConfigByVarame($entity_Sysconfig_base, $k);
        }
        $configfile = TMAC_BASE_PATH . APP_WWW_NAME . DIRECTORY_SEPARATOR . APPLICATION . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'configcache.inc.php';

        $this->tmp_model->ReWriteConfig($configfile);

        if ($rs) {
            $this->redirect('修改系统配置参数成功', PHP_SELF . '?m=config');
        } else {
            $this->redirect('修改系统配置参数失败');
        }
    }

}