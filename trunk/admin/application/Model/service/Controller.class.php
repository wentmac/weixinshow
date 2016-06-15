<?php

/**
 * 接口 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Controller.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Controller_admin extends Action
{

//    protected $check_model;
//    protected $userName;

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
        Tmac::session();
        $ip = Functions::get_client_ip();
        if ($ip !== '124.65.160.2' && $ip !== '119.161.158.115') {
            // die('禁止访问');
        }
//        $this->check_model = $this->M('Check');
//        $this->check_model->checkLogin();

//        $manager = $this->check_model->getSystem_manager();
//
//        $this->userName = $manager->getRealName();;
//        $this->assign('manager', $manager->obj2arr());
//        $this->assign('menua', Tmac::model('LeftMenu', APP_ADMIN_NAME)->getMenua());
    }

    /**
     * Api 返回值函数
     * @param type $data
     * @param type $debug
     * @param type $format 
     */
    public function apiReturn($data = array(), $debug = 0, $format = 'json')
    {
        $return = array(
            'status' => 0,
            'success' => true,
            'data' => $data
        );
        if ($debug == 1) {
            header("Content-type: text/html; charset=utf-8");
            echo '<pre>';
            print_r($return);
            echo '</pre>';
        } else {
            if ($format == 'json') {
                header("Content-type: application/json; charset=utf-8");
                echo json_encode($return);
                exit;
            }
        }
    }

    /**
     * 取用户背景图片
     * @param type $imageId
     * @param type $size
     * @return type 
     */
    protected function getImage($imageId, $type, $size = '')
    {
        if (empty($imageId)) {
            return '';
        }
        if (empty($size)) {
            return IMAGE_URL . $type . '/' . $imageId . '.jpg';
        }
        return THUMB_URL . $type . '_' . $size . '/' . $imageId . '.jpg';
    }

}
