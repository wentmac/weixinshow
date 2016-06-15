<?php

/**
 * 处理数据的函数
 * ============================================================================
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Utility.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class Utility
{

    /**
     * 下拉列表<option>
     * @param <type> $a      数组
     * @param <type> $v      当前
     * @param <type> $type   类别 $type='db'从数据库中用getAll查出来的
     * @return string 
     */
    static public function Option($a = array(), $v = null, $type = null)
    {
        $option = null;
        if ($type == null) {
            foreach ($a AS $key => $value) {
                $selected = $v == $key ? 'selected' : null;
                $option .= "<option value='{$key}' {$selected}>" . strip_tags($value) . "</option>";
            }
        } else {
            $type_ary = explode(',', $type);
            foreach ($a AS $key => $value) {
                if (is_array($value)) {
                    $key = strval($value['' . $type_ary{'0'} . '']);
                    $value = strval($value['' . $type_ary{'1'} . '']);
                }
                $selected = $v == $key ? 'selected' : null;
                $option .= "<option value='{$key}' {$selected}>" . strip_tags($value) . "</option>";
            }
        }

        return $option;
    }

    /**
     * 下拉列表<option>  Object
     * @param <type> $a      数组
     * @param <type> $v      当前
     * @param <type> $type   类别 $type='db'从数据库中用getAll查出来的
     * @return string 
     */
    static public function OptionObject($a = array(), $v = null, $type = null)
    {
        $option = null;
        if ($type == null) {
            foreach ($a AS $key => $value) {
                $selected = $v == $key ? 'selected' : null;
                $option .= "<option value='{$key}' {$selected}>" . strip_tags($value) . "</option>";
            }
        } else {
            $type_ary = explode(',', $type);
            foreach ($a AS $key => $value) {
                if (is_object($value)) {
                    $key = strval($value->$type_ary[0]);
                    $value = strval($value->$type_ary[1]);
                }
                $selected = $v == $key ? 'selected' : null;
                $option .= "<option value='{$key}' {$selected}>" . strip_tags($value) . "</option>";
            }
        }
        return $option;
    }

    /**
     * 下拉列表<option>
     * @param <type> $a      数组
     * @param <type> $v      当前
     * @param <type> $type   类别 $type='db'从数据库中用getAll查出来的
     * @return string 
     */
    static public function newOption($a = array(), $v = null, $type = null)
    {
        $option = null;
        if ($type == null) {
            foreach ($a AS $key => $value) {
                //$selected = $v == $key ? 'selected' : null;
                $option .= "<li value='{$key}'  v='{$key}' >" . strip_tags($value) . "</li>";
            }
        } else {
            $type_ary = explode(',', $type);
            foreach ($a AS $key => $value) {
                if (is_array($value)) {
                    $key = strval($value['' . $type_ary{'0'} . '']);
                    $value = strval($value['' . $type_ary{'1'} . '']);
                }
                // $selected = $v == $key ? 'selected' : null;
                $option .= "<li value='{$key}' v='{$key}' >" . strip_tags($value) . "</li>";
            }
        }

        return $option;
    }

    /**
     * 单选Radio生成
     * @param <array> $a    数组
     * @param <string> $n   name
     * @param <string> $v   当前值
     * @param <string> $action onclick="X"
     * @param <type> $type  类别 $type='db'从数据库中用getAll查出来的
     * @return <type> 
     */
    static public function RadioButton($a = array(), $n, $v = -1, $action = null, $type = null)
    {
        $cbox = null;
        if ($type == null) {
            foreach ($a AS $key => $value) {
                $checked = $v == $key ? 'checked' : null;
                $cbox .= "<input type='radio' name='{$n}' value='{$key}' {$checked} {$action} />{$value}&nbsp;";
            }
        } else {
            $type_ary = explode(',', $type);
            foreach ($a AS $key => $value) {
                if (is_array($value)) {
                    $key = strval($value['' . $type_ary{'0'} . '']);
                    $value = strval($value['' . $type_ary{'1'} . '']);
                }
                $checked = $v == $key ? 'checked' : null;
                $cbox .= "<input type='radio' name='{$n}' value='{$key}' {$checked} {$action} />{$value}&nbsp;";
            }
        }
        return $cbox;
    }

    /**
     * 单选Radio生成
     * @param <array> $a    数组
     * @param <string> $n   name
     * @param <string> $v   当前值
     * @param <string> $action onclick="X"
     * @param <type> $type  类别 $type='db'从数据库中用getAll查出来的
     * @return <type> 
     */
    static public function RadioButton2($a = array(), $n, $v = -1, $action = null, $type = null)
    {
        $cbox = null;
        if ($type == null) {
            foreach ($a AS $key => $value) {
                $checked = $v == $key ? 'checked' : null;
                $cbox .= "<input type='radio' name='{$n}' value='{$key}' {$checked} {$action} class='radio'/><em>{$value}</em>&nbsp;";
            }
        } else {
            $type_ary = explode(',', $type);
            foreach ($a AS $key => $value) {
                if (is_array($value)) {
                    $key = strval($value['' . $type_ary{'0'} . '']);
                    $value = strval($value['' . $type_ary{'1'} . '']);
                }
                $checked = $v == $key ? 'checked' : null;
                $cbox .= "<input type='radio' name='{$n}' value='{$key}' {$checked} {$action} class='radio'/><em>{$value}</em>&nbsp;";
            }
        }
        return $cbox;
    }

    /**
     * checkbox生成
     *
     * @param type $a
     * @param type $b
     * @param type $c
     * @param type $d 
     * @param type $e 当前值
     * @return string 
     */
    static public function checkbox($a, $b = '', $c = '', $d = '', $e = 0)#**(数据数组，name =$[]的值，value值的字段value=$, checkbox中输出名<input />&, 匹配的值)**#
    {
        $optvalue = '';
        for ($i = 0; $i < count($a); $i++) {
            if ($a[$i][$c] != "") {
                $optval = '<input name="' . $b . '[]" type="checkbox" value="' . $a[$i][$c] . '"';
                if ($e != "") {
                    $group = explode(",", $e);
                    for ($j = 0; $j < count($group); $j++) {
                        if ($a[$i][$c] == $group[$j]) {
                            $optval .= 'checked="checked"';
                        }
                    }
                }
                $optval .= '/> ' . $a[$i][$d] . '';
                if (($i + 1) % 10 == 0) {
                    $optval .= "<br>";
                }
            }
            $optvalue .= $optval;
        }
        return $optvalue;
    }

}
