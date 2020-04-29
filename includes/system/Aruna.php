<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 */

	defined('BASEPATH') OR exit('No direct script access allowed');

	/*
	 * ------------------------------------------------------
	 *  Load the framework constants
	 * ------------------------------------------------------
	 */

	if (file_exists(BASEPATH.'config/constants.php'))
	{
		require_once(BASEPATH.'config/constants.php');
	}

	// Include Common Functions
	require_once(BASEPATH.'/system/Common.php');

	// Include Common Functions custom by user
	if (config_item('common_function') !== '')
	{
		if (file_exists(APPPATH.'common/function.php'))
		{
			require_once(APPPATH.'common/function.php');
		}
	}

	/*
	 * ------------------------------------------------------
	 * Security procedures
	 * ------------------------------------------------------
	 */

	if ( ! is_php('5.4'))
	{
		ini_set('magic_quotes_runtime', 0);

		if ((bool) ini_get('register_globals'))
		{
			$_protected = array(
				'_SERVER',
				'_GET',
				'_POST',
				'_FILES',
				'_REQUEST',
				'_SESSION',
				'_ENV',
				'_COOKIE',
				'GLOBALS',
				'HTTP_RAW_POST_DATA',
				'system_path',
				'application_folder',
				'includes_path',
				'module_path',
				'_protected',
				'_registered'
			);

			$_registered = ini_get('variables_order');
			foreach (array('E' => '_ENV', 'G' => '_GET', 'P' => '_POST', 'C' => '_COOKIE', 'S' => '_SERVER') as $key => $superglobal)
			{
				if (strpos($_registered, $key) === FALSE)
				{
					continue;
				}

				foreach (array_keys($$superglobal) as $var)
				{
					if (isset($GLOBALS[$var]) && ! in_array($var, $_protected, TRUE))
					{
						$GLOBALS[$var] = NULL;
					}
				}
			}
		}
	}

	/*
	 * ------------------------------------------------------
	 *  Define a custom error handler so we can log PHP errors
	 * ------------------------------------------------------
	 */
	
	set_error_handler('_error_handler');
	set_exception_handler('_exception_handler');
	register_shutdown_function('_shutdown_handler');

	/*
	 * ------------------------------------------------------
	 * Important charset-related stuff
	 * ------------------------------------------------------
	 *	
	 * Configure mbstring and/or iconv if they are enabled
	 * and set MB_ENABLED and ICONV_ENABLED constants, so
	 * that we don't repeatedly do extension_loaded() or
	 * function_exists() calls.
	 *
	 * Note: UTF-8 class depends on this. It used to be done
	 * in it's constructor, but it's _not_ class-specific.
	 *
	 */

	$charset = strtoupper(config_item('charset'));
	ini_set('default_charset', $charset);

	if (extension_loaded('mbstring'))
	{
		define('MB_ENABLED', TRUE);
		// mbstring.internal_encoding is deprecated starting with PHP 5.6
		// and it's usage triggers E_DEPRECATED messages.
		@ini_set('mbstring.internal_encoding', $charset);
		// This is required for mb_convert_encoding() to strip invalid characters.
		// That's utilized by AR_Utf8, but it's also done for consistency with iconv.
		mb_substitute_character('none');
	}
	else
	{
		define('MB_ENABLED', FALSE);
	}

	// There's an ICONV_IMPL constant, but the PHP manual says that using
	// iconv's predefined constants is "strongly discouraged".
	if (extension_loaded('iconv'))
	{
		define('ICONV_ENABLED', TRUE);
		// iconv.internal_encoding is deprecated starting with PHP 5.6
		// and it's usage triggers E_DEPRECATED messages.
		@ini_set('iconv.internal_encoding', $charset);
	}
	else
	{
		define('ICONV_ENABLED', FALSE);
	}

	if (is_php('5.6'))
	{
		ini_set('php.internal_encoding', $charset);
	}

	/*
	 * ------------------------------------------------------
	 *  Load compatibility features
	 * ------------------------------------------------------
	 */

	require_once(BASEPATH.'system/compat/mbstring.php');
	require_once(BASEPATH.'system/compat/hash.php');
	require_once(BASEPATH.'system/compat/password.php');
	require_once(BASEPATH.'system/compat/standard.php');

	/*
	 * ------------------------------------------------------
	 *  Instantiate the UTF-8 class
	 * ------------------------------------------------------
	 */

	$UNI =& load_class('Utf8', 'system');

	// Register variabel to global for global use
	$GLOBALS['UNI'] = $UNI;

	/*
	 * ------------------------------------------------------
	 *  Instantiate the URI class
	 * ------------------------------------------------------
	 */

	$URI =& load_class('URI', 'system');

	/*
	 * -----------------------------------------------------
	 * Load the security class for xss and csrf support
	 * -----------------------------------------------------
	 */
	
	$SEC =& load_class('Security', 'system');

	// Default variable page
	$GLOBALS['page'] = isset($_GET['page']) ? $_GET['page'] : 1;

	// Filtering URI String
	if (isset($_REQUEST['p'])) 
	{
		// Filter out control characters and trim slashes
		$_REQUEST['p'] = trim(remove_invisible_characters($_REQUEST['p'], FALSE), '/');
	}

	if ( ! isset($_REQUEST['p'])) 
	{
		if (config_item('main_page') !== '')
		{
			if (strstr(config_item('main_page'), '/')) 
			{
				$var_str = preg_split("#/#", config_item('main_page'));
				$main_page = $var_str[0];
				$second_page = $var_str[1];
			}
			else 
			{
				$main_page = config_item('main_page');
				$second_page = 'index';
			}
		}
		else
		{
			$main_page = 'home';
			$second_page = 'index';
		}

		$parr[1] = $main_page;
		$parr[2] = $second_page;
	}
	else 
	{
		/*
		 * ------------------------------------------------------
		 *  Instantiate the routing class and set the routing
		 * ------------------------------------------------------
		 */

		$RTR =& load_class('Router', 'system', $_REQUEST['p']);

		foreach (explode('/', trim($_REQUEST['p'], '/')) as $val)
		{
			$val = trim($val);

			// Filter segments for security
			$URI->filter_uri($val);

			if ($val !== '')
			{
				$parr[] = $val;
			}
		}
	}

	// Default parameter for URL
	for ($i = 0; $i < 4; $i ++) 
	{
		$parr[$i] = isset($parr[$i]) ? $parr[$i] : NULL;
	}

	array_unshift($parr, NULL);
	unset($parr[0]);

	// Register the parr variable into global variables to make it easier for developers
	// For parr variables are global segments, you can use the get_data_global function to use this method
	$GLOBALS['segments'] = $parr;

	/**
	 * Untuk mendeteksi modul yang ada didalam folder modul ada atau tidak.
	 */

	$class_name = FALSE;

	if (file_exists('modules/'.$parr[1].'/'.$parr[1].'.php')) 
	{
		$class_name = $parr[1];
		
		if (class_exists($class_name, FALSE) == FALSE) 
		{
			// Autoload if file function for the module if exist
			load_func($parr[1]);

			/*
			 * -----------------------------------------------------
			 * Load the security class for xss and csrf support
			 * -----------------------------------------------------
			 */
	
			$GLOBALS['SEC'] =& load_class('Security', 'system');

			include_once('modules/'.$parr[1].'/'.$parr[1].'.php');

			if (method_exists($parr[1], $parr[2]))
			{
				$reflection = new ReflectionMethod($parr[1], $parr[2]);

				if ( ! $reflection->isPublic())
				{
					show_error("The called method is not public.");
				}
			}
		}
	}

	if ($class_name === FALSE) 
	{
		if (config_item('error_page') !== '')
		{
			if (strstr(config_item('error_page'), '/')) 
			{
				$var_str_err = preg_split("#/#", config_item('error_page'));
				$error_page_1 = $var_str_err[0];
				$error_page_2 = $var_str_err[1];
			}
			else 
			{
				$error_page_1 = config_item('error_page');
				$error_page_2 = 'index';
			}
		}
		else
		{
			$error_page_1 = NULL;
			$error_page_2 = NULL;
		}

		if ($error_page_1 === NULL && $error_page_2 === NULL)
		{
			$url_ext = load_ext(['url']);

			include_once(APPPATH.'/views/errors/html/error_404_custom.php');
			exit;	
		}
		else
		{
			if (file_exists('modules/'.$error_page_1.'/'.$error_page_1.'.php')) 
			{
				include_once('modules/'.$error_page_1.'/'.$error_page_1.'.php');
				$parr[1] = $error_page_1;
				$parr[2] = $error_page_2;
			}
			else
			{
				show_error('Module '.$error_page_1.' doesn\'t exist');
			}
		}
	}

	/**
	 * Auto access module class
	 */

	// Load Application
	$app = new $parr[1]();

	if (is_numeric($parr[2])) 
	{
		$app->index($parr[2], $parr[3]);
	}
	else 
	{
		if ( ! isset($parr[2])) 
		{
			$app->index();
		}
		elseif ( ! method_exists($parr[1], $parr[2])) 
		{
			$url_ext = load_ext(['url']);

			include_once(APPPATH.'/views/errors/html/error_404_custom.php');
			exit;	
		}
		else 
		{
			$app->{$parr[2]}($parr[3], $parr[4]);
		}
	}

?>