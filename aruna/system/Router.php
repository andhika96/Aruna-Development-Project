<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 *	Description: Parses URIs and determines routing
	 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ARUNA_Router {

	/**
	 * List of routes
	 *
	 * @var	array
	 */

	public $routes = array();

	/**
	 * List of segments
	 *
	 * @var	array
	 */

	public $segments = array();

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Runs the route mapping function.
	 *
	 * @param	array	$routing
	 * @return	void
	 */

	public function __construct($routing = NULL)
	{
		$this->uri =& load_class('URI', 'system');

		// $this->segments[0] = $this->uri->segments;
		$this->_set_routing();

		log_message('info', 'Router Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Set route mapping
	 *
	 * Determines what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 *
	 * @return	void
	 */

	protected function _set_routing()
	{
		// Load the routes.php file. It would be great if we could
		// skip this for enable_query_strings = TRUE, but then
		// default_controller would be empty ...
		if (file_exists(APPPATH.'config/routes.php'))
		{
			include(APPPATH.'config/routes.php');
		}

		// Validate & get reserved routes
		if (isset($route) && is_array($route))
		{
			$this->routes = $route;
		}

		$this->_parse_routes();
	}

	// --------------------------------------------------------------------

	/**
	 * Set request route
	 *
	 * Takes an array of URI segments as input and sets the class/method
	 * to be called.
	 *
	 * @used-by	ARUNA_Router::_parse_routes()
	 * @param	array	$segments	URI segments
	 * @return	void
	 */

	protected function _set_request($segments = array())
	{
		if ( ! isset($segments[0]))
		{
			$segments[0] = 'home';
		}

		if ( ! isset($segments[1]))
		{
			$segments[1] = 'index';
		}

		$this->uri->rsegments = $segments;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse Routes
	 *
	 * Matches any routes that may exist in the config/routes.php file
	 * against the URI to determine if the class/method need to be remapped.
	 *
	 * @return	void
	 */

	protected function _parse_routes()
	{
		// Turn the segment array into a URI string
		$uri = implode('/', $this->uri->segments);

		// Get HTTP verb
		$http_verb = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'cli';

		// Loop through the route array looking for wildcards
		foreach ($this->routes as $key => $value)
		{
			$key = $key === '/' ? $key : ltrim($key, '/ ');

			// Check if route format is using HTTP verbs
			if (is_array($value))
			{
				$value = array_change_key_case($value, CASE_LOWER);
				if (isset($value[$http_verb]))
				{
					$value = $value[$http_verb];
				}
				else
				{
					continue;
				}
			}

			// Convert wildcards to RegEx
			$key = str_replace(array(':any', ':segment', ':num', ':alpha', ':alphanum', ':hash'), array('.*', '[^/]+', '[0-9]+', '[a-zA-Z]+', '[a-zA-Z0-9]+', '[^/]+'), $key);

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#u', $uri, $matches))
			{
				// Are we using callbacks to process back-references?
				if ( ! is_string($value) && is_callable($value))
				{
					// Remove the original string from the matches array.
					array_shift($matches);

					// Execute the callback using the valueues in matches as its parameters.
					$value = call_user_func_array($value, $matches);
				}
				
				if (strpos($value, '$') !== false && strpos($key, '(') !== false && strpos($key, '/') !== false)
				{
					$replacekey = str_replace('/(.*)', '', $key);
					$value = preg_replace('#^'.$key.'$#u', $value, $uri);
					$value = str_replace($replacekey, str_replace('/', '\\', $replacekey), $value);
				}
				// Are we using the default routing method for back-references?
				elseif (strpos($value, '$') !== FALSE && strpos($key, '(') !== FALSE)
				{
					$value = preg_replace('#^'.$key.'$#u', $value, $uri);
				}
				elseif (strpos($value, '/') !== false)
				{
					[$controller, $method] = explode('/', $value);

					// Only replace slashes in the controller, not in the method.
					$controller = str_replace('/', '\\', $controller);

					$value = $controller.'/'.$method;
				}

				$this->_set_request(explode('/', $value));
				return;
			}
		}

		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_set_request(array_values($this->uri->segments));
	}
}

?>