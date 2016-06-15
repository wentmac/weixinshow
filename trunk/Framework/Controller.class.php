<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Controller.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class Controller
{

    /**
     * URL参数
     *
     * @var array
     */
    private $param;
    private $url;

    /**
     * 构造函数.
     *
     * @return void
     * @access public
     *
     */
    public function __construct()
    {
        $this->parsePath();
        $this->getControllerFile();
        $this->getControllerClass();
    }

    /**
     * 解析URL路径
     *
     * @return void
     * @access private
     *
     */
    private function parsePath()
    {
        global $TmacConfig;
        if ($TmacConfig['Common']['url_case_insensitive']) {
            // URL地址中M不区分大小写
            if (!empty($_GET['M']) && empty($_GET['m']))
                $_GET['m'] = strtolower($_GET['M']);
        }
        //确定Controller以及Action
        if (empty($_GET['m'])) {
            //如果没有参数任何参数            
            $this->param['TMAC_CONTROLLER_FILE'] = $this->param['TMAC_CONTROLLER'] = $this->param['TMAC_ACTION'] = 'index';
            return true;
        }
        $queryString = $_GET['m'];
        unset($_GET['m']);
        $action = '';
        if (($urlSeparatorPosition = strrpos($queryString, $TmacConfig['Common']['urlseparator'])) > 0) {//如果query_string中有url separator就来取controller和method
            $controller = substr($queryString, 0, $urlSeparatorPosition);
            $action = substr($queryString, $urlSeparatorPosition + 1);
        } else {
            $controller = $queryString;
        }
        //Controller的第一个字符必须为字母
        if ($this->isLetter($controller) === false) {
            $message = "错误的Controller请求";
            $message .= $GLOBALS['TmacConfig']['Common']['debug'] ? ": [{$controller}]" : "";
            throw new TmacException($message);
        }
        if (empty($action))
            $action = 'index';
        $this->param['TMAC_CONTROLLER_FILE'] = $controller;
        $this->param['TMAC_CONTROLLER'] = basename($controller);
        $this->param['TMAC_ACTION'] = $action;
        return true;
    }

    /**
     * 根据解析的URL获取Controller文件
     *
     * @return void
     * @access private
     *
     */
    private function getControllerFile()
    {
        $controllerFile = APPLICATION_ROOT . 'Controller' . DIRECTORY_SEPARATOR . $this->param['TMAC_CONTROLLER_FILE'] . '.php';
        //有文件先执行目录下的文件
        if (is_file($controllerFile)) {
            require($controllerFile);
        } else {
            $message = "错误的请求，找不到Controller文件";
            $message .= $GLOBALS['TmacConfig']['Common']['debug'] ? ":[$controllerFile]" : "";
            throw new TmacException($message);
        }
    }

    /**
     * 根据Controller文件名获取Controller类名并且执行
     *
     * @return void
     * @access private
     *
     */
    private function getControllerClass()
    {
        $controllerClass = $this->param['TMAC_CONTROLLER'] . 'Action';
        if (class_exists($controllerClass, false)) {
            //取的Controller中的所有方法
            $methods = get_class_methods($controllerClass);
            //判断是否存在Action
            if (!in_array($this->param['TMAC_ACTION'], $methods)) {
                $message = "错误的请求，找不到Action";
                $message .= $GLOBALS['TmacConfig']['Common']['debug'] ? ":[{$this->param['TMAC_ACTION']}]" : "";
                throw new TmacException($message);
            }
            new HttpRequest($this->param);
            $action = new $controllerClass();
            //执行初始化Action
            in_array('_init', $methods) && $action->_init();
            $action->{$this->param['TMAC_ACTION']}();
        } else {
            $message = "错误的请求，找不到Controller类";
            $message .= $GLOBALS['TmacConfig']['Common']['debug'] ? ":[$controllerClass]" : "";
            throw new TmacException($message);
        }
    }

    /**
     * 判断第一个字符是否为字母
     *
     * @param string $char
     * @return boolean
     */
    private function isLetter($char)
    {
        $ascii = ord($char{0});
        return ($ascii >= 65 && $ascii <= 90) || ($ascii >= 97 && $ascii <= 122);
    }

}