<?php
/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: CacheApc.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class CacheApc extends Cache {

	/**
	 * 构造器
	 * 检测apc扩展是否开启
	 *
	 * @access public
	 */
	public function __construct() {
		if (!extension_loaded('apc')) {
            throw new TmacException('Apc扩展没有开启!');
        }
	}
	
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
    public function set($key, $value, $expire = 60) {
		return apc_store($key, $value, $expire);
	}

	/**
	 * 获取一个已经缓存的变量
	 *
	 * @param String $key  缓存Key
	 * @return mixed       缓存内容
	 * @access public
	 */
	public function get($key) {
		return apc_fetch($key);
	}

	/**
	 * 删除一个已经缓存的变量
	 *
	 * @param  $key
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function del($key) {
		return apc_delete($key);
	}

	/**
	 * 删除全部缓存变量
	 *
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function delAll() {
		return apc_clear_cache();
	}

	/**
	 * 检测是否存在对应的缓存
	 *
	 * @param string $key   缓存Key
	 * @return boolean      是否存在key
	 * @access public
	 */
	public function has($key) {
		return (apc_fetch($key) === false ? false : true);
	}
}
