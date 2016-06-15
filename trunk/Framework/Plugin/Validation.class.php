<?php
/**
 * 检测字符串类
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Validation.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class Validation {

	/**
	 * 检测字符串是否只包含[A-Za-z0-9]
	 * 
	 * @param string $string
	 * @return bool
	 */
	public function isAlphaNum($string) {
		return ctype_alnum((string)$string);
	}

	/**
	 * 检测字符串是否只包含[A-Za-z]
	 * 
	 * @param string $string
	 * @return bool
	 */
	public function isAlpha($string) {
		return ctype_alpha((string)$string);
	}

	/**
	 * 检测字符串是否只包含数字
	 * 
	 * @param string $string
	 * @return bool
	 */
	public function isNum($string) {
		return ctype_digit((string)$string);
	}

	/**
	 * 检查是否所有的字符都是英文字母，并且都是小写的
	 * 
	 * @param string $string
	 * @return bool
	 */
	public function isLower($string) {
		return ctype_lower((string)$string);
	}

	/**
	 * 检查是否所有的字符都是英文字母，并且都是大写的
	 * 
	 * @param string $string
	 * @return bool
	 */
	public function isUpper($string) {
		return ctype_upper((string)$string);
	}

	/**
	 * 检查是否是16进制的字符串，只能包括“0123456789abcdef”
	 *
	 * @param string $string
	 * @return bool
	 */
	public function isHex($string) {
		return ctype_xdigit((string)$string);
	}
}