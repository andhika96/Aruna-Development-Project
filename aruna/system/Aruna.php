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

	if (file_exists(APPPATH.'config/constants.php'))
	{
		require_once(APPPATH.'config/constants.php');
	}

	// Include Common Functions
	require_once(BASEPATH.'system/Common.php');

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
	 *  Set timezone from config
	 * ------------------------------------------------------
	 */

	date_default_timezone_set(config_item('timezone'));

	/*
	 * ------------------------------------------------------
	 *  Should we use a Composer autoloader?
	 * ------------------------------------------------------
	 */

	if ($composer_autoload = config_item('composer_autoload'))
	{
		if ($composer_autoload === TRUE)
		{
			file_exists(APPPATH.'vendor/autoload.php')
				? require_once(APPPATH.'vendor/autoload.php')
				: log_message('error', '$config[\'composer_autoload\'] is set to TRUE but '.APPPATH.'vendor/autoload.php was not found.');
		}
		elseif (file_exists($composer_autoload))
		{
			require_once($composer_autoload);
		}
		else
		{
			log_message('error', 'Could not find the specified $config[\'composer_autoload\'] path: '.$composer_autoload);
		}
	}

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
	 *  Start the timer... tick tock tick tock...
	 * ------------------------------------------------------
	 */

	$BM =& load_class('Benchmark', 'system');

	/*
	 * ------------------------------------------------------
	 *  Instantiate the UTF-8 class
	 * ------------------------------------------------------
	 */

	$UNI =& load_class('Utf8', 'system');

	/*
	 * ------------------------------------------------------
	 *  Instantiate the URI class
	 * ------------------------------------------------------
	 */

	$URI =& load_class('URI', 'system');

	/*
	 * ------------------------------------------------------
	 *  Instantiate the routing class and set the routing
	 * ------------------------------------------------------
	 */

	$RTR =& load_class('Router', 'system', NULL);

	/*
	 * ------------------------------------------------------
	 *  Load the Input class and sanitize globals
	 * ------------------------------------------------------
	 */
	
	$IN	=& load_class('Input', 'system');

	/*
	 * ------------------------------------------------------
	 *  Instantiate the output class
	 * ------------------------------------------------------
	 */
	
	$OUT =& load_class('Output', 'system');

	/*
	 * -----------------------------------------------------
	 * Load the security class for xss and csrf support
	 * -----------------------------------------------------
	 */
	
	$SEC =& load_class('Security', 'system');

	/*
	 * ------------------------------------------------------
	 *  Load the Language class
	 * ------------------------------------------------------
	 */

	$LANG =& load_class('Lang', 'system');

	// Default variable page
	$GLOBALS['page'] = isset($_GET['page']) ? $_GET['page'] : 1;

	// Load the base controller class
	require_once BASEPATH.'system/Controller.php';

	// Load the restFull API Library
	// We load manually because we customize the library, so if you trying update the restFull API Library
	// You can get the error and the API not work properly
	require_once BASEPATH.'system/chriskacerguis/codeigniter-restserver/src/Format.php';
	require_once BASEPATH.'system/chriskacerguis/codeigniter-restserver/src/RestController.php';

	function &get_instance()
	{
		return Aruna_Controller::get_instance();
	}
	
	if (count($URI->rsegments) > 0)
	{
		$URI->segments = $URI->rsegments;
	}
	
	// Total parameter for URL
	$total_parameter_allowed = 7;

	// Default parameter for URL
	for ($i = 0; $i < $total_parameter_allowed; $i++) 
	{
		$URI->segments[$i] = isset($URI->segments[$i]) ? $URI->segments[$i] : NULL;
	}

	// Register the parr variable into global variables to make it easier for developers
	// For parr variables are global segments, you can use the get_data_global function to use this method
	$GLOBALS['segments'] = $URI->segments;

	/**
	 * Untuk mendeteksi modul yang ada didalam folder modul ada atau tidak.
	 */

	$class_name = FALSE;

	/**
	 * Define default value of variable array $newParrArgs
	 */

	$newParrArgs = [];

	if (file_exists('modules/'.$URI->segments[0].'/'.$URI->segments[0].'.php')) 
	{
		$class_name = $URI->segments[0];

		if (class_exists($class_name, FALSE) == FALSE) 
		{
			// Set default variable parameter 2 to index, update for PHP 8.1
			$URI->segments[1] ??= 'index';

			include_once('modules/'.$URI->segments[0].'/'.$URI->segments[0].'.php');

			if (method_exists($URI->segments[0], $URI->segments[1]))
			{
				$reflection = new ReflectionMethod($URI->segments[0], $URI->segments[1]);

				if (count($URI->segments) > $total_parameter_allowed)
				{
					log_message('error', 'URL parameter exceeds allowed '.count($URI->segments));
					show_error("URL parameter exceeds allowed");
				}

				for ($i = 2; $i < 5; $i++) 
				{
					if (isset($URI->segments[$i]))
					{
						$newParrArgs[$i] = $URI->segments[$i];
					}
				}

				if ($reflection->getNumberOfParameters() > 0 && count($newParrArgs) > $reflection->getNumberOfParameters())
				{
					log_message('error', 'URL parameter exceeds allowed Argument Method at '.$URI->segments[0].'/'.$URI->segments[1]);
					
					$url_ext = load_ext(['url']);

					include_once(APPPATH.'/views/errors/html/error_404_custom.php');
					exit;	
				}

				if ( ! $reflection->isPublic())
				{
					log_message('error', 'The called method is not public.');
					show_error("The called method is not public.");
				}

				if ($reflection->isConstructor())
				{
					$url_ext = load_ext(['url']);

					include_once(APPPATH.'/views/errors/html/error_404_custom.php');
					exit;				
				}
			}

			if ( ! method_exists($URI->segments[0], '__construct'))
			{
				$url_ext = load_ext(['url']);

				include_once(APPPATH.'/views/errors/html/error_404_custom.php');
				exit;
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
				
				$URI->segments[0] = $class;
				$URI->segments[1] = $method;
			}
			else
			{
				show_error('Module '.$class.' doesn\'t exist');
			}
		}
	}

	// Define VIEWPATH
	define('VIEWPATH', 'modules/'.$URI->segments[0].'/views/');

	// Define MODELPATH
	define('MODELPATH', 'modules/'.$URI->segments[0].'/models/');

	/**
	 * Auto access module class
	 */

	// Load Application
	$app = new $URI->segments[0]();

	if (is_numeric($URI->segments[1])) 
	{
		$app->index($URI->segments[1], $URI->segments[2]);
	}
	else 
	{
		if ( ! isset($URI->segments[1])) 
		{
			$app->index();
		}
		elseif ( ! method_exists($URI->segments[0], $URI->segments[1])) 
		{
			$url_ext = load_ext(['url']);

			include_once(APPPATH.'/views/errors/html/error_404_custom.php');
			exit;
		}
		else 
		{
			$app->{$URI->segments[1]}($URI->segments[2], $URI->segments[3]);
		}
	}

?>