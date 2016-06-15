<?php

/**
 * 后台管理员模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: entity.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class entityAction extends service_Controller_admin
{

    private $tmp_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('EntityCreate', APP_BASE_NAME);
//        $this->check_model->CheckPurview(10);                
    }

    /**
     * 管理员类别管理 首页
     */
    public function index()
    {
        //TODO  取出所有管理员
        $rs = $this->tmp_model->getTableArray();
        $this->assign('tableArray', $rs);
        $this->V('entity');
    }

    /**
     * 新增/修改管理员页面　保存　
     */
    public function create()
    {
        if (empty($_POST) || count($_POST) < 3) {
            $this->redirect('don\'t be evil');
            exit;
        }

        $tableArray = Input::post('id_a', '')->sql();
        if ($tableArray) {
            foreach ($tableArray AS $table) {
                $this->tmp_model->setTable_name($table);
                $this->tmp_model->entityCreate();
            }
        }
    }

    /**
     * 新增/修改管理员页面　保存　
     */
    public function table()
    {
        $table = Input::get('t', '')->required('要查看的表名不能为空')->string();
        $table_name = Input::get('tn', '')->string();

        if (Filter::getStatus() === false) {
            $this->redirect(Filter::getFailMessage());
        }

        $this->tmp_model->setTable_name($table);
        $result = $this->tmp_model->getTableInfo();                
        $this->assign('table',$table);
        $this->assign('table_name',$table_name);
        $this->assign('rs',$result);
        $this->V('entity_table');
    }

}
