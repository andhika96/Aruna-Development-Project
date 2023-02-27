<?php

	/*
	 *	Aruna Development Project
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software and CodeIgniter Framework PHP
	 *	Website: https://www.aruna-dev.com
	 *	Created and developed by Andhika Adhitia N
	 *	Copyright 2022
	 */

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
	require_once(BASEPATH.'system/Common.php');
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
	 *  Set timezone from config
	 * ------------------------------------------------------
	 */

	date_default_timezone_set(config_item('timezone'));

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

	// Get REQUEST URI from URL based request user
	$REQUEST_URI = parse_url($_SERVER['REQUEST_URI']);

	/*
	 * ------------------------------------------------------
	 *  Instantiate the routing class and set the routing
	 * ------------------------------------------------------
	 */

	$RTR =& load_class('Router', 'system', $REQUEST_URI['path']);

	foreach (explode('/', trim($REQUEST_URI['path'], '/')) as $val)
	{
		$val = trim($val);

		// Filter segments for security
		$URI->filter_uri($val);

		if ($val !== '' && $val !== 'index.php')
		{
			$segments[] = $val;
		}
	}

	// Total parameter for URL
	$total_parameter_allowed = 7;

	// Default parameter for URL
	for ($i = 0; $i < $total_parameter_allowed; $i++) 
	{
		$segments[$i] = isset($segments[$i]) ? $segments[$i] : NULL;
	}

	if (empty($segments[0]) && empty($segments[1]))
	{
		if (config_item('main_page') !== '')
		{
			if (strstr(config_item('main_page'), '/')) 
			{
				$var_str 	= preg_split("#/#", config_item('main_page'));
				$class 		= $var_str[0];
				$method 	= $var_str[1];
			}
			else 
			{
				$class 		= config_item('main_page');
				$method 	= 'index';
			}
		}
		else
		{
			$class = 'home';
			$method = 'index';
		}

		$segments[0] = $class;
		$segments[1] = $method;
	}

	// Register the parr variable into global variables to make it easier for developers
	// For parr variables are global segments, you can use the get_data_global function to use this method
	$GLOBALS['segments'] = $segments;

	/**
	 * Untuk mendeteksi modul yang ada didalam folder modul ada atau tidak.
	 */

	$class_name = FALSE;

	/**
	 * Define default value of variable array $newParrArgs
	 */

	$newParrArgs = [];

	if (file_exists('modules/'.$segments[0].'/'.$segments[0].'.php')) 
	{
		$class_name = $segments[0];

		if (class_exists($class_name, FALSE) == FALSE) 
		{
			// Set default variable parameter 2 to index, update for PHP 8.1
			$segments[1] ??= 'index';

			include_once('modules/'.$segments[0].'/'.$segments[0].'.php');

			if (method_exists($segments[0], $segments[1]))
			{
				$reflection = new ReflectionMethod($segments[0], $segments[1]);

				if (count($segments) > $total_parameter_allowed)
				{
					log_message('error', 'URL parameter exceeds allowed '.count($segments));
					// show_error("URL parameter exceeds allowed ".count($segments));

					print_r($segments);
					exit;
				}

				// Check parameter 2 from url as a argument of method
				if (isset($segments[2]))
				{
					$newParrArgs[] = $segments[2];
				}
				
				// Check parameter 3 from url as a argument of method
				if (isset($segments[3]))
				{
					$newParrArgs[] = $segments[3];
				}

				if ($reflection->getNumberOfParameters() > 0 && count($newParrArgs) > $reflection->getNumberOfParameters())
				{
					log_message('error', 'URL parameter exceeds allowed Argument Method');
					show_error("URL parameter exceeds allowed Argument Method");
				}

				if ( ! $reflection->isPublic())
				{
					log_message('error', 'The called method is not public.');
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
				$var_str_err 	= preg_split("#/#", config_item('error_page'));
				$class 			= $var_str_err[0];
				$method 		= $var_str_err[1];
			}
			else 
			{
				$class 			= config_item('error_page');
				$method 		= 'index';
			}
		}
		else
		{
			$class 	= NULL;
			$method = NULL;
		}

		if ($class === NULL && $method === NULL)
		{
			$url_ext = load_ext(['url']);

			include_once(APPPATH.'/views/errors/html/error_404_custom.php');
			exit;	
		}
		else
		{
			if (file_exists('modules/'.$class.'/'.$class.'.php')) 
			{
				include_once('modules/'.$class.'/'.$class.'.php');
				
				$segments[0] = $class;
				$segments[1] = $method;
			}
			else
			{
				show_error('Module '.$class.' doesn\'t exist');
			}
		}
	}

	// Define VIEWPATH
	define('VIEWPATH', 'modules/'.$segments[0].'/views/');

	// Define MODELPATH
	define('MODELPATH', 'modules/'.$segments[0].'/models/');

	/**
	 * Auto access module class
	 */

	// Load Application
	$app = new $segments[0]();

	if (is_numeric($segments[1])) 
	{
		$app->index($segments[1], $segments[2]);
	}
	else 
	{
		if ( ! isset($segments[1])) 
		{
			$app->index();
		}
		elseif ( ! method_exists($segments[0], $segments[1])) 
		{
			$url_ext = load_ext(['url']);

			include_once(APPPATH.'/views/errors/html/error_404_custom.php');
			exit;
		}
		else 
		{
			$app->{$segments[1]}($segments[2], $segments[3]);
		}
	}

?>