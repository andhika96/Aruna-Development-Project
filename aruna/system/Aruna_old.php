<?php

	// Include Common Functions
	require_once(BASEPATH.'/system/Common.php');

	$url = parse_url($_SERVER['REQUEST_URI']);

	foreach (explode('/', trim($url['path'], '/')) as $val)
	{
		$val = trim($val);

		if ($val !== '')
		{
			$segments[] = $val;
		}
	}

	// Total parameter for URL
	$total_parameter_allowed = 6;

	// Default parameter for URL
	for ($i = 0; $i < $total_parameter_allowed; $i++) 
	{
		$segments[$i] = isset($segments[$i]) ? $segments[$i] : NULL;
	}

	array_unshift($segments, NULL);
	unset($segments[0]);

	if (file_exists('modules/'.$segments[2].'/'.$segments[2].'.php')) 
	{
		$class_name = $segments[2];

		if (class_exists($class_name, FALSE) == FALSE) 
		{
			// Set default variable parameter 2 to index, update for PHP 8.1
			$segments[3] ?? 'index';

			include_once('modules/'.$segments[2].'/'.$segments[2].'.php');
		}
	}

	// Define VIEWPATH
	define('VIEWPATH', 'modules/'.$segments[2].'/views/');

	// Define MODELPATH
	define('MODELPATH', 'modules/'.$segments[2].'/models/');

	/**
	 * Auto access module class
	 */

	// Load Application
	$app = new $segments[2]();

	if (is_numeric($segments[3])) 
	{
		$app->index($segments[3], $segments[3]);
	}
	else 
	{
		if ( ! isset($segments[3])) 
		{
			$app->index();
		}
		elseif ( ! method_exists($segments[2], $segments[3])) 
		{
			echo 'Not found';
			exit;
		}
		else 
		{
			$app->{$segments[3]}($segments[4], $segments[5]);
		}
	}

	// if ($url !== '/') 
	// {
	// 	$url['query'] = isset($url['query']) ? $url['query'] : NULL;

	// 	parse_str($url['query'], $output);
		
	// 	print_r($output);
	// }

?>