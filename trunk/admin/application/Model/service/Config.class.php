<?php

/**
 * 后台系统配置参数模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Config.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Config_admin extends Model
{

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        
    }

    /**
     * 保存
     * @param string $menusMain
     * return Boole
     */
    public function createConfig(entity_Sysconfig_base $entity_Sysconfig_base)
    {
        $dao = dao_factory_base::getSysconfigDao();
        return $dao->insert($entity_Sysconfig_base);
    }

    public function modifyConfig(entity_Sysconfig_base $entity_Sysconfig_base)
    {
        $dao = dao_factory_base::getSysconfigDao();
        $dao->setPk($entity_Sysconfig_base->sys_id);
        return $dao->updateByPk($entity_Sysconfig_base);
    }
    
    public function modifyConfigByVarame(entity_Sysconfig_base $entity_Sysconfig_base,$varname)
    {
        $dao = dao_factory_base::getSysconfigDao();
        $dao->setWhere("varname = '{$varname}'");
        return $dao->updateByWhere($entity_Sysconfig_base);
    }

    /**
     * 获取一个系统配置参数信息
     * @param int $class_id 栏目id
     * return array
     */
    public function getConfigInfo($id)
    {
        $dao = dao_factory_base::getSysconfigDao();
        $dao->setPk($id);
        $rs = $dao->getInfoByPk();
        return $rs;
    }

    /**
     * 获取所有资讯
     * return article_class,pages
     */
    public function getConfigList($url = null)
    {
        $dao = dao_factory_base::getSysconfigDao();
        $dao->setOrderby('sys_order DESC, sys_id ASC');
        $rs = $dao->getListByWhere();

        if (is_array($rs)) {
            foreach ($rs AS $k => $v) {
                if ($v->type == 'select') {
                    $select_option = null;
                    //接口地址
                    $cfg_apiurl_ary = UtilityConfig::cfg_apiurl_ary();
                    $select_option = Utility::Option($cfg_apiurl_ary, $v->value);
                    $rs[$k]->select = '<select name="' . $v->varname . '" id="' . $v->varname . '">' . $select_option . '</select>';
                } elseif ($v->type == 'radio') {
                    $radio_option_ary = explode('{|}', $v->item);
                    $cbox = null;
                    //遍历item值
                    foreach ($radio_option_ary AS $kk => $vv) {
                        $radio_option_array = explode(':', $vv);
                        $checked = $radio_option_array[1] == $v->value ? 'checked' : null;
                        $cbox .= "<input type='radio' name='{$v->varname}' value='{$radio_option_array[1]}' {$checked} {$v->nameaction} />&nbsp;{$radio_option_array[0]}";
                    }
                    $rs[$k]->radio = $cbox;
                }
            }
        }
        return $rs;
    }

    /**
     * 写静态配置文件config.cache.php
     */
    public function ReWriteConfig($configfile)
    {
        if (!is_file($configfile)) {
            file_put_contents($configfile, '', LOCK_EX);
        }
        if (!is_writeable($configfile)) {
            echo "配置文件'{$configfile}'不支持写入，无法修改系统配置参数！";
            exit();
        }
        $fp = fopen($configfile, 'w');
        flock($fp, 3);
        fwrite($fp, "<" . "?php\r\n");

        $dao = dao_factory_base::getSysconfigDao();
        $getconfig = $dao->getListByWhere();
        foreach ($getconfig as $k => $v) {
            fwrite($fp, "\$config['config']['{$v->varname}'] = '" . $v->value . "';\r\n");
        }
        fwrite($fp, "?" . ">");
        fclose($fp);
    }

}