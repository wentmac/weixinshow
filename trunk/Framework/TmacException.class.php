<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: TmacException.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class TmacException extends Exception
{

    /**
     * 优化异常页面
     *
     * @var string
     */
    private $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>系统发生错误</title><meta http-equiv="content-type" content="text/html;charset=utf-8"/><meta name="Generator" content="EditPlus"/><style>body{	font-family: \'Microsoft Yahei\', Verdana, arial, sans-serif;	font-size:14px;}a{text-decoration:none;color:#174B73;}a:hover{ text-decoration:none;color:#FF6600;}h2{	border-bottom:1px solid #DDD;	padding:8px 0;    font-size:25px;}.title{	margin:4px 0;	color:#F60;	font-weight:bold;}.message,#trace{	padding:1em;	border:solid 1px #000;	margin:10px 0;	background:#FFD;	line-height:150%%;}.message{	background:#FFD;	color:#2E2E2E;		border:1px solid #E0E0E0;}#trace{	background:#E7F7FF;	border:1px solid #E0E0E0;	color:#535353;}.notice{    padding:10px;	margin:5px;	color:#666;	background:#FCFCFC;	border:1px solid #E0E0E0;}.red{	color:red;	font-weight:bold;}</style></head><body><div class="notice"><h2>系统发生错误 </h2><div >您可以选择 [ <A HREF="%s">重试</A> ] [ <A HREF="javascript:history.back()">返回</A> ] 或者 [ <A HREF="%s">回到首页</A> ]</div><p class="title">[ 错误信息 ]</p><p class="message">%s</p><p class="title">[ TRACE ]</p><p id="trace"><b>%s</b><br />%s<br /></p></div><div align="center" style="color:#FF3300;margin:5pt;font-family:Verdana"> TmacMVC<span style=\'color:silver\'> { Tmac MVC PHP Framework }</span></div></body></html>
';

    /**
     * 构造器
     *
     * @param string $message
     * @param int $code
     * @access public
     */
    public function __construct($message = 'Unknown Error', $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * 输出异常信息
     *
     * @return void
     * @access public
     */
    public function getError()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];        
        if ($GLOBALS['TmacConfig']['Common']['debug']) {            
            $trace = $this->getTrace();
            $this->class = $trace[0]['class'];
            $this->function = $trace[0]['function'];
            $this->line = $trace[0]['line'];
            $traceInfo = '';
            $time = date("y-m-d H:i:m");
            foreach ($trace as $t) {
                $traceInfo .= '[' . $time . '] ' . $t['file'] . ' (' . $t['line'] . ') ';
                $traceInfo .= $t['class'] . $t['type'] . $t['function'] . '(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")<br/>";
            }
            die(sprintf($this->html, $url, PHP_SELF, urldecode($this->getMessage()), '在[' . $this->getFile() . ']的第[' . $this->getLine() . ']行. ', $traceInfo));
        } else {
            die(sprintf($this->html, $url, PHP_SELF, urldecode($this->getMessage()), '请联系管理员', ''));
        }
    }

}

?>
