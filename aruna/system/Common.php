<?php

	/*
	 *	Aruna Development Project
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software and CodeIgniter Framework PHP
	 *	Website: https://www.aruna-dev.com
	 *	Created and developed by Andhika Adhitia N
	 *	Copyright 2022
	 */

	defined('BASEPATH') OR exit('No direct script access allowed');


	// ------------------------------------------------------------------------

	/**
	 * Untuk mendeteksi class yang ada didalam berkas modul ada atau tidak.
	 */

	// Since the default file extensions are searched
	// in order of .inc then .php, but we always use .php,
	// put the .php extension first to eek out a bit
	// better performance.
	// http://php.net/manual/en/function.spl-autoload.php#78053
	spl_autoload_extensions('.php, .inc');

	spl_autoload_register(function($params) 
	{
	    $path = 'modules/'.$params.'/'.$params.'.php';

		if (file_exists($path))
		{
			// Check to see whether the include declared the class
			if ( ! class_exists($params, TRUE)) 
			{
				log_message('error', 'Non-existent class: '.$params);
				show_error('Non-existent class: '.$params);
			}
		}
	},
	true, // Throw exception
	true // Prepend
	);

	// ------------------------------------------------------------------------

	if ( ! function_exists('is_php'))
	{

		/**
		 * Determines if the current version of PHP is equal to or greater than the supplied value
		 *
		 * @param	string
		 * @return	bool	TRUE if the current version is $version or higher
		 */

		function is_php($version)
		{
			static $_is_php;
			$version = (string) $version;

			if ( ! isset($_is_php[$version]))
			{
				$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
			}

			return $_is_php[$version];
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('is_really_writable'))
	{

		/**
		 * Tests for file writability
		 *
		 * is_writable() returns TRUE on Windows servers when you really can't write to
		 * the file, based on the read-only attribute. is_writable() is also unreliable
		 * on Unix servers if safe_mode is on.
		 *
		 * @link	https://bugs.php.net/bug.php?id=54709
		 * @param	string
		 * @return	bool
		 */

		function is_really_writable($file)
		{
			// If we're on a Unix server with safe_mode off we call is_writable
			if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') OR ! ini_get('safe_mode')))
			{
				return is_writable($file);
			}

			/* For Windows servers and safe_mode "on" installations we'll actually
			 * write a file then read it. Bah...
			 */

			if (is_dir($file))
			{
				$file = rtrim($file, '/').'/'.md5(mt_rand());
				if (($fp = @fopen($file, 'ab')) === FALSE)
				{
					return FALSE;
				}

				fclose($fp);
				@chmod($file, 0777);
				@unlink($file);
				return TRUE;
			}
			elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('load_class'))
	{

		/**
		 * Class registry
		 *
		 * This function acts as a singleton. If the requested class does not
		 * exist it is instantiated and set to a static variable. If it has
		 * previously been instantiated the variable is returned.
		 *
		 * @param	string	the class name being requested
		 * @param	string	the directory where the class should be found
		 * @param	mixed	an optional argument to pass to the class constructor
		 * @return	object
		 */

		function &load_class($class, $directory = 'libraries', $param = NULL)
		{
			static $_classes = array();

			// Does the class exist? If so, we're done...
			if (isset($_classes[$class]))
			{
				return $_classes[$class];
			}

			$name = FALSE;
			
			// Look for the class first in the local application/libraries folder
			// then in the native aruna/libraries folder
			foreach (array(APPPATH, BASEPATH) as $path)
			{
				if (file_exists($path.$directory.'/'.$class.'.php'))
				{
					$name = 'ARUNA_'.$class;

					if (class_exists($name, FALSE) === FALSE)
					{
						require_once($path.$directory.'/'.$class.'.php');
					}

					break;
				}
			}

			// Did we find the class?
			if ($name === FALSE)
			{
				// Note: We use exit() rather than show_error() in order to avoid a
				// self-referencing loop with the Exceptions class
				set_status_header(503);
				echo 'Unable to locate the specified class: '.$class.'.php';
				exit(5); // EXIT_UNK_CLASS
			}

			// Keep track of what we just loaded
			is_loaded($class);

			$_classes[$class] = isset($param)
				? new $name($param)
				: new $name();
			return $_classes[$class];
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('load_loader'))
	{
		function load_loader()
		{
			include_once(BASEPATH.'system/Loader.php');

			$load_class = new ARUNA_Loader();

			return $load_class;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('load_lib'))
	{

		/**
		 * Library Loader
		 *
		 * Loads and instantiates libraries.
		 * Designed to be called from application libraries.
		 *
		 * @param	mixed	$library	Library name
		 * @param	array	$params		Optional parameters to pass to the library class constructor
		 * @param	string	$dir	An optional directory name if you want to load files from other folders
		 * @return	object
		 */

		function load_lib($class, $param = NULL)
		{
			// Get the class name, and while we're at it trim any slashes.
			// The directory path can be included as part of the class name,
			// but we don't want a leading slash
			$class = str_replace('.php', '', trim($class, '/'));

			// Was the path included with the class name?
			// We look for a slash to determine this
			if (($last_slash = strrpos($class, '/')) !== FALSE)
			{
				// Extract the path
				$subdir = substr($class, 0, ++$last_slash);

				// Get the filename from the path
				$class = substr($class, $last_slash);
			}
			else
			{
				$subdir = '';
			}

			if ($class == 'uri' || $class == 'Uri')
			{
				$class = strtoupper($class);
			}
			else
			{
				$class = ucfirst($class);
			}

			if (file_exists(BASEPATH.'system/'.$subdir.$class.'.php'))
			{
				$allowed_lib_system = ['Benchmark', 'Output', 'Security', 'URI', 'Utf8', 'Lang'];

				if (in_array($class, $allowed_lib_system))
				{	
					$load_class =& load_class($class, $subdir.'system', $param);
					return $load_class;
				}
				else
				{
					// If we got this far we were unable to find the requested class.
					log_message('error', 'Unable to load the requested class: '.$class);
					show_error('Unable to load the requested class: '.$class);
				}
			}
			else
			{
				$load_class =& load_class($class, 'libraries', $param);
				return $load_class;
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('load_ext'))
	{
		/**
		 * Extension Loader
		 *
		 * @param	string|string[]	$extensions	Extension name(s)
		 * @return	object
		 */

		function load_ext($extension = array())
		{
			// If VIEWPATH is undefined we can set VIEWPATH with empty value
			if ( ! defined('VIEWPATH')) define('VIEWPATH', '');

			// If MODELPATH is undefined we can set MODELPATH with empty value
			if ( ! defined('MODELPATH')) define('MODELPATH', '');

			$access_class = load_loader()->extension($extension);

			return $access_class;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('load_db'))
	{
		/**
		 * Initialize the database
		 */

		function load_db($active_group = '', $db_type = '', $return = FALSE)
		{
			$access_class = load_loader()->database($active_group, $db_type, $return);

			return $access_class;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('load_model'))
	{
		/**
		 * Initialize the database
		 */

		function load_model($model, $name = '', $db_conn = FALSE)
		{
			$access_class = load_loader()->model($model, $name, $db_conn);

			return $access_class;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('load_extend_view'))
	{

		/**
		 * Load Extend View
		 *
		 * Berfungsi untuk memuat tampilan antarmuka kedua dari tampilan antarmuka utama
		 * fungsi ini hanya berfungsi dengan sempurna jika diletakkan di modul
		 */

		function load_extend_view($theme, $part)
		{
			$access_class = load_loader()->init_extend_view($theme, $part);

			return $access_class;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('view'))
	{

		/**
		 * Load Extend View
		 *
		 * Berfungsi untuk memuat tampilan antarmuka kedua dari tampilan antarmuka utama
		 * fungsi ini hanya berfungsi dengan sempurna jika diletakkan di modul
		 */

		function view($view, $vars = array(), $return = FALSE)
		{
			$access_class = load_loader()->load_view($view, $vars, $return);

			return $access_class;
		}
	}

	// --------------------------------------------------------------------

	if ( ! function_exists('is_loaded'))
	{

		/**
		 * Keeps track of which libraries have been loaded. This function is
		 * called by the load_class() function above
		 *
		 * @param	string
		 * @return	array
		 */

		function &is_loaded($class = '')
		{
			static $_is_loaded = array();

			if ($class !== '')
			{
				$_is_loaded[strtolower($class)] = $class;
			}

			return $_is_loaded;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('get_config'))
	{

		/**
		 * Loads the main config.php file
		 *
		 * This function lets us grab the config file even if the Config class
		 * hasn't been instantiated yet
		 *
		 * @param	array
		 * @return	array
		 */

		function &get_config(Array $replace = array())
		{
			static $config;

			if (empty($config))
			{	
				$file_path = APPPATH.'config/config.php';
				$found = FALSE;

				if (file_exists($file_path))
				{
					$found = TRUE;
					require($file_path);
				}

				// Does the $config array exist in the file?
				if ( ! isset($config) OR ! is_array($config))
				{
					set_status_header(503);
					echo 'Your config file does not appear to be formatted correctly.';
					exit(3); // EXIT_CONFIG
				}
			}

			// Are any values being dynamically added or replaced?
			foreach ($replace as $key => $val)
			{
				$config[$key] = $val;
			}

			return $config;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('config_item'))
	{

		/**
		 * Returns the specified config item
		 *
		 * @param	string
		 * @return	mixed
		 */

		function config_item($item)
		{
			static $_config;

			if (empty($_config))
			{
				// references cannot be directly assigned to static variables, so we use an array
				$_config[0] =& get_config();
			}

			return isset($_config[0][$item]) ? $_config[0][$item] : NULL;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('get_mimes'))
	{

		/**
		 * Returns the MIME types array from config/mimes.php
		 *
		 * @return	array
		 */

		function &get_mimes()
		{
			static $_mimes;

			if (empty($_mimes))
			{
				$_mimes = file_exists(APPPATH.'config/mimes.php')
					? include(APPPATH.'config/mimes.php')
					: array();
			}

			return $_mimes;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('is_https'))
	{

		/**
		 * Is HTTPS?
		 *
		 * Determines if the application is accessed via an encrypted
		 * (HTTPS) connection.
		 *
		 * @return	bool
		 */

		function is_https()
		{
			if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
			{
				return TRUE;
			}
			elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
			{
				return TRUE;
			}
			elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
			{
				return TRUE;
			}

			return FALSE;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('is_cli'))
	{

		/**
		 * Is CLI?
		 *
		 * Test to see if a request was made from the command line.
		 *
		 * @return 	bool
		 */

		function is_cli()
		{
			return (PHP_SAPI === 'cli' OR defined('STDIN'));
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('show_error'))
	{

		/**
		 * Error Handler
		 *
		 * This function lets us invoke the exception class and
		 * display errors using the standard error template located
		 * in application/views/errors/error_general.php
		 * This function will send the error page directly to the
		 * browser and exit.
		 *
		 * @param	string
		 * @param	int
		 * @param	string
		 * @return	void
		 */

		function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
		{
			$status_code = abs($status_code);
			if ($status_code < 100)
			{
				$exit_status = $status_code + 9; // 9 is EXIT__AUTO_MIN
				$status_code = 500;
			}
			else
			{
				$exit_status = 1; // EXIT_ERROR
			}

			$_error =& load_class('Exceptions', 'system');
			echo $_error->show_error($heading, $message, 'error_general', $status_code);
			exit($exit_status);
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('show_404'))
	{

		/**
		 * 404 Page Handler
		 *
		 * This function is similar to the show_error() function above
		 * However, instead of the standard error template it displays
		 * 404 errors.
		 *
		 * @param	string
		 * @param	bool
		 * @return	void
		 */

		function show_404($page = '', $log_error = TRUE)
		{
			$_error =& load_class('Exceptions', 'system');
			$_error->show_404($page, $log_error);
			exit(4); // EXIT_UNKNOWN_FILE
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('log_message'))
	{

		/**
		 * Error Logging Interface
		 *
		 * We use this as a simple mechanism to access the logging
		 * class and send messages to be logged.
		 *
		 * @param	string	the error level: 'error', 'debug' or 'info'
		 * @param	string	the error message
		 * @return	void
		 */

		function log_message($level, $message)
		{
			static $_log;

			if ($_log === NULL)
			{
				// references cannot be directly assigned to static variables, so we use an array
				$_log[0] =& load_class('Log', 'system');
			}

			$_log[0]->write_log($level, $message);
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('set_status_header'))
	{

		/**
		 * Set HTTP Status Header
		 *
		 * @param	int	the status code
		 * @param	string
		 * @return	void
		 */

		function set_status_header($code = 200, $text = '')
		{
			if (is_cli())
			{
				return;
			}

			if (empty($code) OR ! is_numeric($code))
			{
				show_error('Status codes must be numeric', 500);
			}

			if (empty($text))
			{
				is_int($code) OR $code = (int) $code;
				$stati = array(
					100	=> 'Continue',
					101	=> 'Switching Protocols',
					103	=> 'Early Hints',

					200	=> 'OK',
					201	=> 'Created',
					202	=> 'Accepted',
					203	=> 'Non-Authoritative Information',
					204	=> 'No Content',
					205	=> 'Reset Content',
					206	=> 'Partial Content',
					207	=> 'Multi-Status',

					300	=> 'Multiple Choices',
					301	=> 'Moved Permanently',
					302	=> 'Found',
					303	=> 'See Other',
					304	=> 'Not Modified',
					305	=> 'Use Proxy',
					307	=> 'Temporary Redirect',
					308	=> 'Permanent Redirect',

					400	=> 'Bad Request',
					401	=> 'Unauthorized',
					402	=> 'Payment Required',
					403	=> 'Forbidden',
					404	=> 'Not Found',
					405	=> 'Method Not Allowed',
					406	=> 'Not Acceptable',
					407	=> 'Proxy Authentication Required',
					408	=> 'Request Timeout',
					409	=> 'Conflict',
					410	=> 'Gone',
					411	=> 'Length Required',
					412	=> 'Precondition Failed',
					413	=> 'Request Entity Too Large',
					414	=> 'Request-URI Too Long',
					415	=> 'Unsupported Media Type',
					416	=> 'Requested Range Not Satisfiable',
					417	=> 'Expectation Failed',
					421	=> 'Misdirected Request',
					422	=> 'Unprocessable Entity',
					426	=> 'Upgrade Required',
					428	=> 'Precondition Required',
					429	=> 'Too Many Requests',
					431	=> 'Request Header Fields Too Large',
					451	=> 'Unavailable For Legal Reasons',

					500	=> 'Internal Server Error',
					501	=> 'Not Implemented',
					502	=> 'Bad Gateway',
					503	=> 'Service Unavailable',
					504	=> 'Gateway Timeout',
					505	=> 'HTTP Version Not Supported',
					511	=> 'Network Authentication Required',
				);

				if (isset($stati[$code]))
				{
					$text = $stati[$code];
				}
				else
				{
					show_error('No status text available. Please check your status code number or supply your own message text.', 500);
				}
			}

			if (strpos(PHP_SAPI, 'cgi') === 0)
			{
				header('Status: '.$code.' '.$text, TRUE);
				return;
			}

			$server_protocol = (isset($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], array('HTTP/1.0', 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0'), TRUE))
				? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
			header($server_protocol.' '.$code.' '.$text, TRUE, $code);
		}
	}

	// --------------------------------------------------------------------

	if ( ! function_exists('_error_handler'))
	{

		/**
		 * Error Handler
		 *
		 * This is the custom error handler that is declared at the (relative)
		 * top of CodeIgniter.php. The main reason we use this is to permit
		 * PHP errors to be logged in our own log files since the user may
		 * not have access to server logs. Since this function effectively
		 * intercepts PHP errors, however, we also need to display errors
		 * based on the current error_reporting level.
		 * We do that with the use of a PHP error template.
		 *
		 * @param	int	$severity
		 * @param	string	$message
		 * @param	string	$filepath
		 * @param	int	$line
		 * @return	void
		 */

		function _error_handler($severity, $message, $filepath, $line)
		{
			$is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

			// When an error occurred, set the status header to '500 Internal Server Error'
			// to indicate to the client something went wrong.
			// This can't be done within the $_error->show_php_error method because
			// it is only called when the display_errors flag is set (which isn't usually
			// the case in a production environment) or when errors are ignored because
			// they are above the error_reporting threshold.
			if ($is_error)
			{
				set_status_header(500);
			}

			// Should we ignore the error? We'll get the current error_reporting
			// level and add its bits with the severity bits to find out.
			if (($severity & error_reporting()) !== $severity)
			{
				return;
			}

			$_error =& load_class('Exceptions', 'system');
			$_error->log_exception($severity, $message, $filepath, $line);

			// Should we display the error?
			if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors')))
			{
				$_error->show_php_error($severity, $message, $filepath, $line);
			}

			// If the error is fatal, the execution of the script should be stopped because
			// errors can't be recovered from. Halting the script conforms with PHP's
			// default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
			if ($is_error)
			{
				exit(1); // EXIT_ERROR
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('_exception_handler'))
	{

		/**
		 * Exception Handler
		 *
		 * Sends uncaught exceptions to the logger and displays them
		 * only if display_errors is On so that they don't show up in
		 * production environments.
		 *
		 * @param	Exception	$exception
		 * @return	void
		 */

		function _exception_handler($exception)
		{
			$_error =& load_class('Exceptions', 'system');
			$_error->log_exception('error', 'Exception: '.$exception->getMessage(), $exception->getFile(), $exception->getLine());

			is_cli() OR set_status_header(500);

			// Should we display the error?
			if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors')))
			{
				$_error->show_exception($exception);
			}

			exit(1); // EXIT_ERROR
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('_shutdown_handler'))
	{

		/**
		 * Shutdown Handler
		 *
		 * This is the shutdown handler that is declared at the top
		 * of CodeIgniter.php. The main reason we use this is to simulate
		 * a complete custom exception handler.
		 *
		 * E_STRICT is purposively neglected because such events may have
		 * been caught. Duplication or none? None is preferred for now.
		 *
		 * @link	http://insomanic.me.uk/post/229851073/php-trick-catching-fatal-errors-e-error-with-a
		 * @return	void
		 */

		function _shutdown_handler()
		{
			$last_error = error_get_last();
			if (isset($last_error) &&
				($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
			{
				_error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
			}
		}
	}

	// --------------------------------------------------------------------

	if ( ! function_exists('remove_invisible_characters'))
	{

		/**
		 * Remove Invisible Characters
		 *
		 * This prevents sandwiching null characters
		 * between ascii characters, like Java\0script.
		 *
		 * @param	string
		 * @param	bool
		 * @return	string
		 */

		function remove_invisible_characters($str, $url_encoded = TRUE)
		{
			$non_displayables = array();

			// every control character except newline (dec 10),
			// carriage return (dec 13) and horizontal tab (dec 09)
			if ($url_encoded)
			{
				$non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
				$non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
				$non_displayables[] = '/%7f/i';	// url encoded 127
			}

			$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

			do
			{
				$str = preg_replace($non_displayables, '', $str, -1, $count);
			}
			while ($count);

			return $str;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('html_escape'))
	{

		/**
		 * Returns HTML escaped variable.
		 *
		 * @param	mixed	$var		The input string or array of strings to be escaped.
		 * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
		 * @return	mixed			The escaped string or array of strings as a result.
		 */

		function html_escape($var, $double_encode = TRUE)
		{
			if (empty($var))
			{
				return $var;
			}

			if (is_array($var))
			{
				foreach (array_keys($var) as $key)
				{
					$var[$key] = html_escape($var[$key], $double_encode);
				}

				return $var;
			}

			return htmlspecialchars($var, ENT_QUOTES, config_item('charset'), $double_encode);
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('_stringify_attributes'))
	{

		/**
		 * Stringify attributes for use in HTML tags.
		 *
		 * Helper function used to convert a string, array, or object
		 * of attributes to a string.
		 *
		 * @param	mixed	string, array, object
		 * @param	bool
		 * @return	string
		 */

		function _stringify_attributes($attributes, $js = FALSE)
		{
			$atts = NULL;

			if (empty($attributes))
			{
				return $atts;
			}

			if (is_string($attributes))
			{
				return ' '.$attributes;
			}

			$attributes = (array) $attributes;

			foreach ($attributes as $key => $val)
			{
				$atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
			}

			return rtrim($atts, ',');
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('function_usable'))
	{

		/**
		 * Function usable
		 *
		 * Executes a function_exists() check, and if the Suhosin PHP
		 * extension is loaded - checks whether the function that is
		 * checked might be disabled in there as well.
		 *
		 * This is useful as function_exists() will return FALSE for
		 * functions disabled via the *disable_functions* php.ini
		 * setting, but not for *suhosin.executor.func.blacklist* and
		 * *suhosin.executor.disable_eval*. These settings will just
		 * terminate script execution if a disabled function is executed.
		 *
		 * The above described behavior turned out to be a bug in Suhosin,
		 * but even though a fix was committed for 0.9.34 on 2012-02-12,
		 * that version is yet to be released. This function will therefore
		 * be just temporary, but would probably be kept for a few years.
		 *
		 * @link	http://www.hardened-php.net/suhosin/
		 * @param	string	$function_name	Function to check for
		 * @return	bool	TRUE if the function exists and is safe to call,
		 *			FALSE otherwise.
		 */

		function function_usable($function_name)
		{
			static $_suhosin_func_blacklist;

			if (function_exists($function_name))
			{
				if ( ! isset($_suhosin_func_blacklist))
				{
					$_suhosin_func_blacklist = extension_loaded('suhosin')
						? explode(',', trim(ini_get('suhosin.executor.func.blacklist')))
						: array();
				}

				return ! in_array($function_name, $_suhosin_func_blacklist, TRUE);
			}

			return FALSE;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('set_ob_start'))
	{
		function set_ob_start($content = '')
		{
			if (config_item('set_header_cache') === TRUE)
			{
				$Aruna =& get_instance();
				$Aruna->output->set_cache_header(time(), time()+(config_item('set_header_cache_expire')*60));
			}

			ob_start();

			echo $content;

			// Return the file data if requested
			if (get_data_global('_ar_return') === TRUE)
			{
				$buffer = ob_get_contents();
				@ob_end_clean();

				return $buffer;
			}

			/*
			 * Flush the buffer... or buff the flusher?
			 *
			 * In order to permit views to be nested within
			 * other views, we need to flush the content back out whenever
			 * we are beyond the first level of output buffering so that
			 * it can be seen and included properly by the first included
			 * template and any subsequent ones. Oy!
			 */

			if (ob_get_level() > ob_get_level() + 1)
			{
				ob_end_flush();
			}
			else
			{							
				$buffer = ob_get_contents();
				@ob_end_clean();
			}

			echo $buffer;
			return;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('get_data_global'))
	{
		function get_data_global($val)
		{
			if ($val == 'segments')
			{
				show_error('You cannot use the variable name "'.$val.'" with this function, please use the get_segment function.');
			}

			$GLOBALS[$val] = isset($GLOBALS[$val]) ? $GLOBALS[$val] : NULL;

			return $GLOBALS[$val];
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('set_title'))
	{
		/**
		 * Set title per page
		 * @param string
		 * @return string
		 */

		function set_title($title)
		{
			$GLOBALS['title'] = $title;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('section_header'))
	{
		/**
		 * Set content header with this function
		 *
		 * @var string $content
		 * @return string
		 */

		function section_header($content)
		{
			$GLOBALS['section_header'] = isset($GLOBALS['section_header']) ? $GLOBALS['section_header'] : NULL;

			if ( ! strlen($content)) 
			{
				return $GLOBALS['section_header'];
			}
			else 
			{
				$GLOBALS['section_header'] .= $content;
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('section_content'))
	{

		/**
		 * Set content data
		 *
		 * @var string $content
		 * @return string
		 */

		function section_content($content = FALSE)
		{
			$GLOBALS['section_content'] = isset($GLOBALS['section_content']) ? $GLOBALS['section_content'] : NULL;

			if ( ! strlen($content)) 
			{
				return $GLOBALS['section_content'];
			}
			else 
			{
				$GLOBALS['section_content'] .= $content;
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('section_footer'))
	{
		function section_footer($content)
		{
			/**
			 * Set content footer with this function
			 *
			 * @var string $content
			 * @return string
			 */

			$GLOBALS['section_footer'] = isset($GLOBALS['section_footer']) ? $GLOBALS['section_footer'] : NULL;

			if ( ! strlen($content)) 
			{
				return $GLOBALS['section_footer'];
			}
			else 
			{
				$GLOBALS['section_footer'] .= $content;
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('display_content'))
	{

		/**
		 * Set display content data
		 *
		 * @var string
		 * @return string from GLOBAL VARIABLE
		 */

		function display_content()
		{
			$url = load_ext(['url']);
			
			$output = NULL;

			$GLOBALS['app_content'] = isset($GLOBALS['app_content']) ? $GLOBALS['app_content'] : NULL;

			$output .= get_data_global('section_header');
			$output .= get_data_global('section_content');
			$output .= get_data_global('section_footer');

			$GLOBALS['app_content'] .= $output;

			include './themes/default/page.tpl.php';
			exit;
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('display_application_header'))
	{
		/**
		 * Set display application header for themes
		 *
		 * @var string
		 * @return string content
		 */

		function display_application_header($content)
		{
			if (get_data_global('status_extend_view_header') === 1)
			{
				return false;
			}
			else
			{
				set_ob_start($content);
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('display_application_content'))
	{

		/**
		 * Set display application content for themes
		 *
		 * @var string
		 * @return string app_content
		 */

		function display_application_content()
		{	
			set_ob_start($GLOBALS['app_content']);
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('display_application_footer'))
	{
		/**
		 * Set display application footer for themes
		 *
		 * @var string
		 * @return string content
		 */

		function display_application_footer($content)
		{
			if (get_data_global('status_extend_view_footer') === 1)
			{
				return false;
			}
			else
			{
				set_ob_start($content);
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('offset'))
	{
		function offset()
		{
			if (empty(get_data_global('page')))
			{
				return 0;
			}
			else
			{
				return config_item('num_per_page')*(get_data_global('page')-1);
			}
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('num_per_page'))
	{
		function num_per_page()
		{
			return config_item('num_per_page');
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('page'))
	{
		function page()
		{
			return get_data_global('page');
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('pagination'))
	{
		/**
		 * Returns Pagination
		 *
		 * @param	array	$parameters array data
		 * @param	string	$url set url for url pagination
		 * @param	string	$style_class set style in class for styling alignment pagenumber bar
		 * @return	objects
		 */

		function pagination($config = array())
		{
			$pagination = load_lib('pagination', $config['total_rows']);
			$pagination->paras = site_url($config['base_url']);

			return $pagination->whole_num_bar($config['style_class']);
		}
	}

?>