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
	 *	Information file: Cookie Extension
	 */

	defined('BASEPATH') OR exit('No direct script access allowed');

	// ------------------------------------------------------------------------

	if ( ! function_exists('set_cookie'))
	{

		/**
		 * Set cookie
		 *
		 * Accepts seven parameters, or you can submit an associative
		 * array in the first parameter containing all the values.
			 *
		 * @param	mixed
		 * @param	string	the value of the cookie
		 * @param	string	the number of seconds until expiration
		 * @param	string	the cookie domain.  Usually:  .yourdomain.com
		 * @param	string	the cookie path
		 * @param	string	the cookie prefix
		 * @param	bool	true makes the cookie secure
		 * @param	bool	true makes the cookie accessible via http(s) only (no javascript)
		 * @return	void
		 */

		function set_cookie($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = NULL, $httponly = NULL)
		{
			// Load Library Input
			$Aruna =& get_instance();
			
			// Set the config file options
			$Aruna->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure, $httponly);
		}
	}

	// --------------------------------------------------------------------

	if ( ! function_exists('get_cookie'))
	{

		/**
		 * Fetch an item from the COOKIE array
		 *
		 * @param	string
		 * @param	bool
		 * @return	mixed
		 */

		function get_cookie($index, $xss_clean = NULL)
		{
			// Load Library Input
			$Aruna =& get_instance();

			is_bool($xss_clean) OR $xss_clean = (config_item('global_xss_filtering') === TRUE);
			$prefix = isset($_COOKIE[$index]) ? '' : config_item('cookie_prefix');
			return $Aruna->input->cookie($prefix.$index, $xss_clean);
		}
	}

	// --------------------------------------------------------------------

	if ( ! function_exists('delete_cookie'))
	{

		/**
		 * Delete a COOKIE
		 *
		 * @param	mixed
		 * @param	string	the cookie domain. Usually: .yourdomain.com
		 * @param	string	the cookie path
		 * @param	string	the cookie prefix
		 * @return	void
		 */

		function delete_cookie($name, $domain = '', $path = '/', $prefix = '')
		{
			set_cookie($name, '', '', $domain, $path, $prefix);
		}
	}

?>
