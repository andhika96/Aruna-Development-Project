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

	/*
	 *---------------------------------------------------------------
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

	if (($_temp = realpath($includes_path)) !== FALSE)
	{
		$includes_path = $_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		// Ensure there's a trailing slash
		$includes_path = strtr(
			rtrim($includes_path, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}

	// Is the system path correct?
	if ( ! is_dir($includes_path))
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
	define('BASEPATH', $includes_path);

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

	function microtime_float() 
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	$time_start = microtime_float();

	// Load for System URL
	include './includes/system/Aruna.php';

	// Load theme structure
	display_content();

?>