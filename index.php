<?php

	/*
	 *	Aruna Development Project
<<<<<<< HEAD
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software and CodeIgniter Framework PHP
	 *	Website: https://www.aruna-dev.com
	 *	Created and developed by Andhika Adhitia N
	 *	Copyright 2022
=======
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207
	 */

	/*
	 *---------------------------------------------------------------
<<<<<<< HEAD
	 * APPLICATION ENVIRONMENT
	 *---------------------------------------------------------------
	 *
	 * You can load different configurations depending on your
	 * current environment. Setting the environment also influences
	 * things like logging and error reporting.
	 *
	 * This can be set to anything, but default usage is:
	 *
	 *     development
	 *     testing
	 *     production
	 *
	 * NOTE: If you change these, also change the error_reporting() code below
	 */
	
	define('ENVIRONMENT', isset($_SERVER['AR_ENV']) ? $_SERVER['AR_ENV'] : 'development');

	/*
	 *---------------------------------------------------------------
	 * ERROR REPORTING
	 *---------------------------------------------------------------
	 *
	 * Different environments will require different levels of error reporting.
	 * By default development will show errors but testing and live will hide them.
	 */
	
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(-1);
			ini_set('display_errors', 1);
		break;

		case 'testing':
		case 'production':
			ini_set('display_errors', 0);

			if (version_compare(PHP_VERSION, '5.3', '>='))
			{
				error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
			}
			else
			{
				error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
			}
		break;

		default:
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			echo 'The application environment is not set correctly.';
			exit(1); // EXIT_ERROR
	}

	/*
	 *---------------------------------------------------------------
=======
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207
	 * SYSTEM DIRECTORY NAME
	 *---------------------------------------------------------------
	 *
	 * This variable must contain the name of your "system" directory.
	 * Set the path if it is not in the same directory as this file.
	 */

		$system_path = 'system';

	/*
	 *---------------------------------------------------------------
	 * APPLICATION DIRECTORY NAME
	 *---------------------------------------------------------------
	 *
	 * If you want this front controller to use a different "application"
	 * directory than the default one you can set its name here. The directory
	 * can also be renamed or relocated anywhere on your server. If you do,
	 * use an absolute (full) server path.
	 * For more info please see the user guide:
	 *
	 *
	 * NO TRAILING SLASH!
	 */

<<<<<<< HEAD
		$application_folder = 'application';

	/*
	 *---------------------------------------------------------------
	 * ARUNA DIRECTORY NAME
	 *---------------------------------------------------------------
	 *
	 * This variable must contain the name of your "aruna" directory.
	 * Set the path if it is not in the same directory as this file.
	 */

		$aruna_path = 'aruna';
=======
		$application_folder = 'app';

	/*
	 *---------------------------------------------------------------
	 * INCLUDES DIRECTORY NAME
	 *---------------------------------------------------------------
	 *
	 * This variable must contain the name of your "includes" directory.
	 * Set the path if it is not in the same directory as this file.
	 */

		$includes_path = 'includes';
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207

	/*
	 *---------------------------------------------------------------
	 * MODULES DIRECTORY NAME
	 *---------------------------------------------------------------
	 *
	 * If you want to create a module, make sure you have uploaded
	 * the module into folder "modules".
	 */

		$module_path = 'modules';

	/*
	 *---------------------------------------------------------------
	 * THEMES DIRECTORY NAME
	 *---------------------------------------------------------------
	 *
	 * Default theme directory by Aruna Development Project
	 */

		$themes_path = 'themes';

	/*
	 * ---------------------------------------------------------------
	 *  Resolve the system path for increased reliability
 	 * ---------------------------------------------------------------
	 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

<<<<<<< HEAD
	if (($_temp = realpath($aruna_path)) !== FALSE)
	{
		$aruna_path = $_temp.DIRECTORY_SEPARATOR;
=======
	if (($_temp = realpath($includes_path)) !== FALSE)
	{
		$includes_path = $_temp.DIRECTORY_SEPARATOR;
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207
	}
	else
	{
		// Ensure there's a trailing slash
<<<<<<< HEAD
		$aruna_path = strtr(
			rtrim($aruna_path, '/\\'),
=======
		$includes_path = strtr(
			rtrim($includes_path, '/\\'),
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}

	// Is the system path correct?
<<<<<<< HEAD
	if ( ! is_dir($aruna_path))
=======
	if ( ! is_dir($includes_path))
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your includes folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}

	/*
	 * -------------------------------------------------------------------
	 *  Now that we know the path, set the main path constants
	 * -------------------------------------------------------------------
	 */

	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the includes directory
<<<<<<< HEAD
	define('BASEPATH', $aruna_path);
=======
	define('BASEPATH', $includes_path);
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207

	// Path to the theme directory
	define('THEMEPATH', $themes_path);

	// Path to the modules directory
	define('MODULEPATH', $module_path.DIRECTORY_SEPARATOR);

	// The path to the "app" directory
	if (is_dir($application_folder))
	{
		if (($_temp = realpath($application_folder)) !== FALSE)
		{
			$application_folder = $_temp;
		}
		else
		{
			$application_folder = strtr(
				rtrim($application_folder, '/\\'),
				'/\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}
	elseif (is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
	{
		$application_folder = BASEPATH.strtr(
			trim($application_folder, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

<<<<<<< HEAD
	include './aruna/system/Aruna.php';
=======
	function microtime_float() 
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	$time_start = microtime_float();

	// Load for System URL
	include './includes/system/Aruna.php';
>>>>>>> 1386a17d022721e68cfb6c08b18317e8f983f207

	// Load theme structure
	display_content();

?>