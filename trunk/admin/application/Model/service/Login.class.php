<?php

/**
 * 后台 登录模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Login.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Login_admin extends Model
{

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        
    }

    /**
     * 登录验证
     * @param <type> $admin_name
     * @param <type> $admin_psw
     * @param <type> $yzm
     * @param <type> $remember 
     */
    public function check($admin_name, $admin_psw, $yzm, $remember = 0)
    {
        if (trim(md5($yzm)) != trim($_SESSION['valid'])) {
            $this->redirect("效验码输入错误!", PHP_SELF . "?m=login");
            exit();
        }
        if (trim($admin_name) == "") {
            $this->redirect("请输入用户名!");
            exit();
        }
        if (trim($admin_psw) == "") {
            $this->redirect("请输入密码!");
            exit();
        }
        $dao = dao_factory_base::getUserDao();
        $info = $dao->getUserInfoByUsername($admin_name);
        if ($info) {
            if (md5(md5($admin_psw)) == $info->password) {
                if (!empty($_SESSION['admin'])) {
                    unset($_SESSION['admin']);
                }
                if (!empty($_SESSION['admin_uid'])) {
                    unset($_SESSION['admin_uid']);
                }
                if (!empty($_SESSION['admin_purviews'])) {
                    unset($_SESSION['admin_purviews']);
                }
                //注册session
                $_SESSION['admin'] = $info->username;
                $_SESSION['admin_uid'] = $info->uid;
                $_SESSION['admin_purviews'] = $info->type_purviews;
                $uid = $info->uid;
                $time = time();
                $logip = $_SERVER["REMOTE_ADDR"];
                //插adminlog 登录日志
                $entity_UserLog_base = new entity_UserLog_base();
                $entity_UserLog_base->rank = $info->rank;
                $entity_UserLog_base->uid = $uid;
                $entity_UserLog_base->username = $info->username;
                $entity_UserLog_base->logip = $logip;
                $entity_UserLog_base->logtime = $time;

                $dao_userlog = dao_factory_base::getUserLogDao();
                $rs = $dao_userlog->insert($entity_UserLog_base);

                $entity_User_base = new entity_User_base();
                $entity_User_base->login_ip = $logip;
                $entity_User_base->login_time = $time;
                $entity_User_base->logincount = new TmacDbExpr('logincount+1');
                //更新用户上次登录时间，IP，登录次数
                $dao->setPk($uid);
                $rs = $dao->updateByPk($entity_User_base);                

                if ($remember == 1) {//选中了 记住密码
                    HttpResponse::setcookie("cookie_remember", "yes", time() + 3600 * 24 * 30);
                    HttpResponse::setcookie("cookie_username", $admin_name, time() + 3600 * 24 * 30);
                    HttpResponse::setcookie("cookie_password", $admin_psw, time() + 3600 * 24 * 30);
                } else {
                    HttpResponse::setcookie("cookie_remember", '');
                    HttpResponse::setcookie("cookie_username", '');
                    HttpResponse::setcookie("cookie_password", '');
                }
                $this->redirect("成功登录，正在转向管理管理主页！", PHP_SELF);
                unset($_SESSION['valid']);
                exit();
            } else {
                $this->redirect("Access Denied! 密码错误", PHP_SELF . "?m=login");
                exit();
            }
        } else {
            $this->redirect("Access Denied! 没有此用户", PHP_SELF . "?m=login");
            exit();
        }
    }

    public function out()
    {
        if (phpversion() < '4.3.0') {
            session_unregister('admin');
            session_unregister('admin_uid');
            session_unregister('admin_purviews');
            session_unregister('valid');
        } else {
            unset($_SESSION['admin']);
            unset($_SESSION['admin_uid']);
            unset($_SESSION['admin_purviews']);
            unset($_SESSION['valid']);
        }
        $this->redirect("退出/注销成功!", PHP_SELF);
    }

}