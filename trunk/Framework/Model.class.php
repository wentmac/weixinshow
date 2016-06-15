<?php
/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Model.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class  Model extends Base {

	/**
	 * 构造函数 初始化
	 */
	public function  __construct() {
		parent::__construct();
	}

	/**
	 * 获取SQL执行次数
	 *
	 * @return int
	 */
	public final function getQueryNum() {
		return $this->db->getQueryNum();
	}
}