<?php

/**
 * 后台 登录 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: login.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class loginAction extends service_Controller_admin
{

    private $tmp_model;
    private $check_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign('action', $_GET['TMAC_ACTION']);
        $this->tmp_model = Tmac::model('Login');
    }

    /**
     * 显示登录 首页
     */
    public function index()
    {
        $this->V('login');
    }

    /**
     * 检查登录账号密码 处理
     */
    public function check()
    {        
        $username = empty($_POST['username']) ? '' : $_POST['username'];  //hotel_id
        $password = empty($_POST['password']) ? '' : $_POST['password'];  //hotel_id
        $yzm = empty($_POST['yzm']) ? '' : $_POST['yzm'];  //hotel_id

        $this->tmp_model->check($username, $password, $yzm);
    }

    public function out()
    {
        $this->tmp_model->out();
    }

}