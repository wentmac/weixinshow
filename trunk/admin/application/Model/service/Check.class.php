<?php

/**
 * 后台权限验证模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Check.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Check_admin extends Model
{
    
    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {        
    }

    /**
     * 检查是否登录
     * @param string $info
     * return Boole
     */
    public function checkLogin()
    {
        if (empty($_SESSION['admin'])) {
            if (isset($_COOKIE['cookie_remember']) && $_COOKIE['cookie_remember'] == "yes") { //如果存在cookie 如果cookie_remember是正确的
                $username = $_COOKIE['cookie_username'];
                $password = $_COOKIE['cookie_password'];
                $dao = dao_factory_base::getUserDao();
                $info = $dao->getUserInfoByUsername($username);
                if ($info) {
                    if (md5(md5($password)) == $info->password) { //因客户需要 取消 密码的MD5加密
                        if ($_SESSION['admin'] != '') {
                            unset($_SESSION['admin']);
                        }
                        $_SESSION['admin'] = $username;
                        $_SESSION["admin_uid"] = $info->uid;
                        $_SESSION['admin_purviews'] = $info->type_purviews;

                        $uid = $info->uid;
                        $time = time();
                        $logip = $_SERVER["REMOTE_ADDR"];
                        
                        $entity_UserLog_base = new entity_UserLog_base();
                        $entity_UserLog_base->rank=$info->rank;
                        $entity_UserLog_base->uid=$uid;
                        $entity_UserLog_base->username=$info->username;
                        $entity_UserLog_base->logip=$logip;
                        $entity_UserLog_base->logtime=$time;
                        
                        $dao_userlog = dao_factory_base::getUserLogDao();
                        $rs = $dao_userlog->insert($entity_UserLog_base);
                        
                        $entity_User_base = new entity_User_base();
                        $entity_User_base->login_ip=$logip;
                        $entity_User_base->login_time=$time;
                        $entity_User_base->logincount=new TmacDbExpr('logincount+1');
                        //更新用户上次登录时间，IP，登录次数
                        $dao->setPk($uid);
                        $rs = $dao->updateByPk($entity_User_base);
                                                
                        if ($rs) {
                            $this->redirect("成功登录，正在转向后台！", PHP_SELF . "?m=admin");
                            exit();
                        }
                    } else {
                        $this->redirect("Access Denied! 密码错误", PHP_SELF . "?m=login");
                        exit();
                    }
                } else {
                    $this->redirect("Access Denied! 没有此用户", PHP_SELF . "?m=login");
                    exit();
                }
            }
            $this->redirect("Access Denied! 请您登录", PHP_SELF . "?m=login");
            exit;
        }
    }

    /**
     * 取得当前用户所有的权限组
     * @return <type>
     */
    public function getPurview()
    {
        $purview = isset($_SESSION['admin_purviews']) ? $_SESSION['admin_purviews'] : '';
        return $purview;
    }

    /**
     * 检验用户是否有权使用某功能
     * @param <type> $n
     * @return boolean
     */
    public function TestPurview($n)
    {
        $rs = false;
        $purview = $this->getPurview();
        if ($n == '') {
            return true;
        }
        if (!isset($GLOBALS['groupRanks'])) {
            $GLOBALS['groupRanks'] = explode(' ', $purview);
        }
        $ns = explode(',', $n);
        foreach ($ns as $n) {
            //只要找到一个匹配的权限，即可认为用户有权访问此页面
            if ($n == '') {
                continue;
            }
            if (in_array($n, $GLOBALS['groupRanks'])) {
                $rs = true;
                break;
            }
        }
        return $rs;
    }

    /**
     * 页面上面的check
     * @param <type> $n
     */
    public function CheckPurview($n)
    {
        if (!$this->TestPurview($n)) {
            $this->redirect("对不起，你没有权限执行此操作！返回上一页");
            exit();
        }
    }

    /**
     * menu上面的check
     * @param <type> $n
     * @return boolean
     */
    public function CheckPurviewMenu($n)
    {
        if (!$this->TestPurview($n)) {
            $a = true;
        } else {
            $a = false;
        }
        return $a;
    }

}