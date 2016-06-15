<?php

/**
 * session操作数据库类
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: SessionDb.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；  
  +--------------------------------------------
 * 数据库方式Session驱动
  CREATE TABLE tmac_session (
  session_id varchar(255) NOT NULL,
  session_expire int(11) NOT NULL,
  session_data blob,
  UNIQUE KEY `session_id` (`session_id`)
  );
  +--------------------------------------------
 */
class SessionDb
{

    /**
     * Session有效时间
     */
    protected $lifeTime = '';

    /**
     * session保存的数据库名
     */
    protected $sessionTable = '';

    /**
     * 数据库句柄
     */
    protected $hander;

    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
    public function open($savePath, $sessName)
    {
        $this->lifeTime = get_cfg_var("session.gc_maxlifetime");
        $this->sessionTable = $GLOBALS['TmacConfig']['Session']['table'];        
        $name = $GLOBALS['TmacConfig']['Common']['Database'];        
        $hander = mysql_connect($GLOBALS['db'][$name]['hostname'], $GLOBALS['db'][$name]['username'], $GLOBALS['db'][$name]['password']);
        $dbSel = mysql_select_db($GLOBALS['db'][$name]['database'], $hander);
        if (!$hander || !$dbSel)
            return false;
        $this->hander = $hander;
        return true;
    }

    /**
     * 关闭Session 
     * @access public 
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));        
        return mysql_close($this->hander);
    }

    /**
     * 读取Session 
     * @access public 
     * @param string $sessID 
     */
    public function read($sessID)
    {
        $res = mysql_query("SELECT session_data AS data FROM " . $this->sessionTable . " WHERE session_id = '$sessID'   AND session_expire >" . time(), $this->hander);
        if ($res) {
            $row = mysql_fetch_assoc($res);
            return $row['data'];
        }
        return "";
    }

    /**
     * 写入Session 
     * @access public 
     * @param string $sessID 
     * @param String $sessData  
     */
    public function write($sessID, $sessData)
    {
        $expire = time() + $this->lifeTime;
        mysql_query("REPLACE INTO  " . $this->sessionTable . " (  session_id, session_expire, session_data)  VALUES( '$sessID', '$expire',  '$sessData')", $this->hander);
        if (mysql_affected_rows($this->hander))
            return true;
        return false;
    }

    /**
     * 删除Session 
     * @access public 
     * @param string $sessID 
     */
    public function destroy($sessID)
    {
        mysql_query("DELETE FROM " . $this->sessionTable . " WHERE session_id = '$sessID'", $this->hander);
        if (mysql_affected_rows($this->hander))
            return true;
        return false;
    }

    /**
     * Session 垃圾回收
     * @access public 
     * @param string $sessMaxLifeTime 
     */
    public function gc($sessMaxLifeTime)
    {
        mysql_query("DELETE FROM " . $this->sessionTable . " WHERE session_expire < " . time(), $this->hander);
        return mysql_affected_rows($this->hander);
    }

    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
    public function execute()
    { 
        session_set_save_handler(array(&$this, "open"), array(&$this, "close"), array(&$this, "read"), array(&$this, "write"), array(&$this, "destroy"), array(&$this, "gc"));
    }

}