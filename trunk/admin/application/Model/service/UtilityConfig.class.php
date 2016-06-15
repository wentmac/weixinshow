<?php

/**
 * 整个网站的Utility  Option Radio Checkbox 数组Config文件 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: UtilityConfig.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_UtilityConfig_admin extends Model
{

    /**
     * 文章中推荐
     * return Boole
     */
    public function state_radio_ary()
    {
        $rs = array('1' => '正常', '0' => '停止', '2' => '推荐', '3' => '图片推荐');
        return $rs;
    }

    /**
     * 广告类型
     */
    public function ad_type_radio_ary()
    {
        $rs = array('1' => '文字', '2' => '上传图片', '3' => 'Google百度等广告代码');
        return $rs;
    }

    /**
     * 广告状态
     */
    public function ad_state_radio_ary()
    {
        $rs = array('1' => '正常', '2' => '永久', '3' => '停止');
        return $rs;
    }

    /**
     * 友情链接类型
     */
    public function link_type_radio_ary()
    {
        $rs = array('1' => '文字', '2' => '上传图片', '3' => '外部图片链接');
        return $rs;
    }

    /**
     * 友情链接打开类型
     */
    public function link_target_radio_ary()
    {
        $rs = array('_blank' => '新窗口打开', '_top' => '不包含框架的当前窗口或标签', '_none' => '同一窗口或标签');
        return $rs;
    }

    /**
     * 系统配置参数变量类型
     */
    public function vartype_ary()
    {
        $rs = array('string' => '文本', 'select' => '下拉', 'radio' => '单选', 'bstring' => '多行文本');
        return $rs;
    }

    /**
     * 系统配置参数 接口地址
     */
    public function cfg_apiurl_ary()
    {
        $rs = array('http://cdn.api.zhuna.cn/api/utf-8/' => '智能:所有线路自动匹配', 'http://cnc.api.zhuna.cn/api/utf-8/' => '网通:所有网通负载均衡', 'http://tel.api.zhuna.cn/api/utf-8/' => '电信:所有电信负载均衡', 'http://un1.api.zhuna.cn/api/utf-8/' => '双线1:单机双线', 'http://un2.api.zhuna.cn/api/utf-8/' => '双线2:单机双线', 'http://un3.api.zhuna.cn/api/utf-8/' => '双线3:单机双线', 'http://un4.api.zhuna.cn/api/utf-8/' => '双线4:单机双线');
        return $rs;
    }

    /**
     * 评论操作类别
     */
    public function comment_do_ary()
    {
        $rs = array(''=>'＝管理操作＝', 'hidden'=>'隐藏选定', 'display'=>'显示选定', 'spam'=>'标记为垃圾评论', 'del'=>'删除选定');
        return $rs;
    }

    /**
     * 评论搜索类别
     */
    public function comment_type_ary()
    {
        $rs = array('10'=>'--全部--', '0'=>'已隐藏的留言', '1'=>'已显示的留言', '-1'=>'垃圾评论');
        return $rs;
    }

}
