<?php

/**
 * 前台 首页 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: index.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class indexAction extends service_Controller_www {

	public function index() {
		$this -> V('index');
		exit();

	}

	public function about() {
		$this -> V('index_about');
		exit();
	}
	//club是俱乐部
	public function club() {
		$this -> V('index_club');
		exit();
	}
	public function classroom() {
		$this -> V('index_classroom');
		exit();
	}
	//contact这个是联系我们
	public function contact(){
		$this -> V('index_contact');
		exit();
	}
	//supplier这个是联系成为供应商
	public function supplier(){
		$this -> V('index_supplier');
		exit();
	}
	

}
