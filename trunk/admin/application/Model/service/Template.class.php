<?php

/**
 * 后台友情链接模块 Model
 * ============================================================================
 * zhuna_php 住哪网酒店分销联盟程序php版　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Template.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://un.zhuna.cn；
 */
class service_Template_admin extends Model
{

    private $style_url;

    /**
     * 初始化变量　定义私有变量
     */
    public function _init()
    {
        //连接数据库
        //$this->connect();        
    }

    public function getStyle_url()
    {
        return $this->style_url;
    }

    public function setStyle_url($style_url)
    {
        $this->style_url = $style_url;
    }

    /**
     * 获得模版的信息
     *
     * @access  private
     * @param   string      $template_name      模版名
     * @param   string      $style_dir          模版风格路径
     * @return  array
     */
    public function get_template_info($template_name, $style_dir)
    {
        $info = array();
        $ext = array('png', 'gif', 'jpg', 'jpeg');

        $info['code'] = $template_name;
        $info['screenshot'] = '';

        foreach ($ext AS $val) {
            if (file_exists($style_dir . $template_name . "/screenshot.$val")) {
                $info['screenshot'] = $this->getStyle_url() . $template_name . "/screenshot.$val";
                break;
            }
        }

        $info['screenshot'] = str_replace('\\', '/', $info['screenshot']);
        $css_path = $style_dir . $template_name . '/style.css';
        if (file_exists($css_path) && !empty($template_name)) {
            $arr = array_slice(file($css_path), 0, 8);

            $template_name = explode(': ', $arr[1]);
            $template_uri = explode(': ', $arr[2]);
            $template_desc = explode(': ', $arr[3]);
            $template_version = explode(': ', $arr[4]);
            $template_author = explode(': ', $arr[5]);
            $author_uri = explode(': ', $arr[6]);


            $info['name'] = isset($template_name[1]) ? trim($template_name[1]) : '';
            $info['uri'] = isset($template_uri[1]) ? trim($template_uri[1]) : '';
            $info['desc'] = isset($template_desc[1]) ? trim($template_desc[1]) : '';
            $info['version'] = isset($template_version[1]) ? trim($template_version[1]) : '';
            $info['author'] = isset($template_author[1]) ? trim($template_author[1]) : '';
            $info['author_uri'] = isset($author_uri[1]) ? trim($author_uri[1]) : '';
        } else {
            $info['name'] = '';
            $info['uri'] = '';
            $info['desc'] = '';
            $info['version'] = '';
            $info['author'] = '';
        }

        return $info;
    }

    /**
     * 修改配置文件里的设置前台模板目录名
     * @param type $configfile 
     */
    public function editConfigTemplate($configfile, $template_dir)
    {
        $fp = fopen($configfile, 'r');
        $config_data = '';
        while (!feof($fp)) {
            $config_data .= fgets($fp, '1024');
        }
        fclose($fp); //完成对配置文件的读写操作

        $pattern = '/\$TmacConfig\[\'Template\']\[\'template_dir\'] = \'([a-zA-Z_0-9]+)\'/i';
        $replacement = '$TmacConfig[\'Template\'][\'template_dir\'] = \'' . $template_dir . '\'';
        $config_new_data = preg_replace($pattern, $replacement, $config_data);
        if (is_writable($configfile)) {
            $fp = fopen($configfile, 'w');
            if (fwrite($fp, $config_new_data) === FALSE) {
                return false;
            }
            fclose($fp); //完成对配置文件的读写操作
        } else {
            return false;
        }
        return true;
    }

    /**
     * 目录树的递归遍历
     * @param type $path
     * @return array 
     */
    public function recurDir($path)
    {
        $result = array();
        $temp = array();
        //检测文件夹有效或者可读
        if (!is_dir($path) || !is_readable($path)) {
            return false;
        }
        //得到目录下所有文件
        $allFiles = scandir($path);
        //遍历文件
        foreach ($allFiles AS $k => $filename) {
            //如果是.或..或.svn的话就忽略
            if (in_array($filename, array('.', '..', '.svn')))
                continue;
            //得到完整的名称
            $fullName = $path . '/' . $filename;
            $fullName = str_replace('/', '\\', $fullName);
            //如果 文件是目录的话就递归
            if (is_dir($fullName)) {
                $result[$filename] = $this->recurDir($fullName);
            } else {
                //文件存入临时数组里
                $temp[$k]['fullname'] = $fullName;
                $temp[$k]['name'] = $filename;
                $temp[$k]['edittime'] = date('Y-m-d H:i:s', filemtime($fullName));
            }
        }

        //把临时数组加入到结果数组，确保目录在前，文件在后
        foreach ($temp AS $f) {
            $result[] = $f;
        }
        return $result;
    }

    /**
     * 目录树的递归遍历
     * @param type $path
     * @return array 
     */
    public function recurTemDir($result, $path_dir, $dir_name, $level, $url)
    {
        global $dir_list_html; //定义全局变量 返回值 
        $prefix_space = "";
        for ($i = 0; $i < $level; $i++) {
            $prefix_space .= "　"; //第一级就增加一个缩进
        }
        $fuhao = $level == 0 ? '' : $prefix_space . '|-';
        foreach ($result AS $k => $v) {
            if (is_numeric($k)) {
                $file = str_replace($path_dir . '\\', '', $v['fullname']);
                $file = str_replace('\\', '/', $file);
                $dir_list_html .= '
      <tr onmouseout="this.style.background=\'#fff\'" onmouseover="this.style.background=\'#f6f9fd\'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      <td class="td_left"><a href="' . PHP_SELF . '?m=' . $url . '&dirname=' . $dir_name . '&dir=' . $file . '" title="' . $v['fullname'] . '">' . $fuhao . $prefix_space . $v['name'] . '</a></td>
      <td>' . $v['edittime'] . '</td>
      <td><a href="' . PHP_SELF . '?m=' . $url . '&dirname=' . $dir_name . '&dir=' . $file . '" title="' . $v['fullname'] . '">查看/修改</a></td>    
      </tr>             
        ';
            } else {
                $dir_list_html .= '
      <tr onmouseout="this.style.background=\'#fff\'" onmouseover="this.style.background=\'#f6f9fd\'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      <td class="td_left">' . $fuhao . $prefix_space . $k . '</a></td>
      <td></td>
      <td></td>    
      </tr>             
        ';
                $this->recurTemDir($v, $path_dir, $dir_name, $level + 1, $url);
            }
        }
        return $dir_list_html;
    }

    /**
     * 目录树的递归遍历文件
     * @param type $path
     * @param type $ignore 忽略的文件
     * @return array 
     */
    public function recurDirStyleFile($path)
    {
        $temp = array();
        //检测文件夹有效或者可读
        if (!is_dir($path) || !is_readable($path)) {
            return false;
        }
        //得到目录下所有文件
        $allFiles = scandir($path);


        //遍历文件
        foreach ($allFiles AS $k => $filename) {
            //得到完整的名称
            $fullName = $path . '/' . $filename;
            $fullName = str_replace('/', '\\', $fullName);
            if (!is_dir($fullName)) {
                $filename_ext_array = explode('.', $filename);
                $filename_ext = $filename_ext_array[1];
                if ($filename_ext == 'css' || $filename_ext == 'js') {
                    //文件存入临时数组里
                    $temp[$k]['fullname'] = $fullName;
                    $temp[$k]['name'] = $filename;
                    $temp[$k]['edittime'] = date('Y-m-d H:i:s', filemtime($fullName));
                }
            }
        }

        return $temp;
    }

}
