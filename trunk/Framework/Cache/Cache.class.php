<?php
/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Cache.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
abstract class Cache {
	
	/**
	 * 设置一个缓存变量
	 * 
	 * @param String $key    缓存Key
	 * @param mixed $value   缓存内容
	 * @param int $expire    缓存时间(秒)
	 * @return boolean       是否缓存成功
	 * @access public
	 * @abstract
	 */
    public abstract function set($key, $value, $expire = 60);

	/**
	 * 获取一个已经缓存的变量
	 * 
	 * @param String $key  缓存Key
	 * @return mixed       缓存内容
	 * @access public
	 * @abstract
	 */
	public abstract function get($key);

	/**
	 * 删除一个已经缓存的变量
	 * 
	 * @param  $key
	 * @return boolean       是否删除成功
	 * @access public
	 * @abstract
	 */
	public abstract function del($key);

	/**
	 * 删除全部缓存变量
	 *
	 * @return boolean       是否删除成功
	 * @access public
	 * @abstract
	 */
	public abstract function delAll();

	/**
	 * 检测是否存在对应的缓存
	 *
	 * @param string $key   缓存Key
	 * @return boolean      是否存在key
	 * @access public
	 * @abstract
	 */
	public abstract function has($key);
}