<?php
/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: HttpRequest.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class HttpRequest {

	/**
	 * 设置参数并进行过滤
	 *
	 * @param array $request
	 */
	public function __construct($request) {
		global $TmacConfig;
		if($TmacConfig['Common']['autofilter']) {
			$this->filter($request);
			if (!get_magic_quotes_gpc()) {
				$this->filter($_GET);
				$this->filter($_POST);
				$this->filter($_COOKIE);
				$this->filter($_FILES);
			}
		}
		$param = $this->cleanArray($request);
        $_GET = array_merge($_GET, $param);
        unset($param);
        unset($request);
		unset($_ENV);
		unset($HTTP_ENV_VARS);
		unset($_REQUEST);
		unset($HTTP_POST_VARS);
		unset($HTTP_GET_VARS);
		unset($HTTP_POST_FILES);
		unset($HTTP_COOKIE_VARS);
	}
	/**
	 * 转义
	 *
	 * @param array $array 要过滤的数组
	 * @access protected
	 */
	protected function filter(&$array) {
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				is_array($value) ? $this->filter($value) : $array[$key] = addslashes($value);
			}
		}
	}

	/**
	 * 清理数组
	 *
	 * @param array $array
	 * @return array
	 */
	protected function cleanArray($array) {
		foreach ($array as $key => $value) {
           if (empty($key) && empty($value)) {
			   unset($array[$key]);
		   }
       }
       return $array;
	}
}
?>
