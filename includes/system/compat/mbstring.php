<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Source file from CodeIgniter v3.1.9
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 *	Information file: PHP ext/mbstring compatibility package
	 */

defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

if (MB_ENABLED === TRUE)
{
	return;
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_strlen'))
{
	/**
	 * mb_strlen()
	 *
	 * WARNING: This function WILL fall-back to strlen()
	 * if iconv is not available!
	 *
	 * @link	http://php.net/mb_strlen
	 * @param	string	$str
	 * @param	string	$encoding
	 * @return	int
	 */
	function mb_strlen($str, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			return iconv_strlen($str, isset($encoding) ? $encoding : config_item('charset'));
		}

		log_message('debug', 'Compatibility (mbstring): iconv_strlen() is not available, falling back to strlen().');
		return strlen($str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_strpos'))
{
	/**
	 * mb_strpos()
	 *
	 * WARNING: This function WILL fall-back to strpos()
	 * if iconv is not available!
	 *
	 * @link	http://php.net/mb_strpos
	 * @param	string	$haystack
	 * @param	string	$needle
	 * @param	int	$offset
	 * @param	string	$encoding
	 * @return	mixed
	 */
	function mb_strpos($haystack, $needle, $offset = 0, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			return iconv_strpos($haystack, $needle, $offset, isset($encoding) ? $encoding : config_item('charset'));
		}

		log_message('debug', 'Compatibility (mbstring): iconv_strpos() is not available, falling back to strpos().');
		return strpos($haystack, $needle, $offset);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_substr'))
{
	/**
	 * mb_substr()
	 *
	 * WARNING: This function WILL fall-back to substr()
	 * if iconv is not available.
	 *
	 * @link	http://php.net/mb_substr
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int 	$length
	 * @param	string	$encoding
	 * @return	string
	 */
	function mb_substr($str, $start, $length = NULL, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			isset($encoding) OR $encoding = config_item('charset');
			return iconv_substr(
				$str,
				$start,
				isset($length) ? $length : iconv_strlen($str, $encoding), // NULL doesn't work
				$encoding
			);
		}

		log_message('debug', 'Compatibility (mbstring): iconv_substr() is not available, falling back to substr().');
		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}

?>