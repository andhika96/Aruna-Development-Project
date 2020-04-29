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
	 *	Information file: URI Class
	 *	Description: Parses URIs and determines routing
	 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ARUNA_URI {

	/**
	 * List of cached URI segments
	 *
	 * @var	array
	 */

	public $keyval = array();

	/**
	 * Current URI string
	 *
	 * @var	string
	 */

	public $uri_string = '';

	/**
	 * List of URI segments
	 *
	 * Starts at 1 instead of 0.
	 *
	 * @var	array
	 */

	public $segments = array();

	/**
	 * List of routed URI segments
	 *
	 * Starts at 1 instead of 0.
	 *
	 * @var	array
	 */

	public $rsegments = array();

	/**
	 * Permitted URI chars
	 *
	 * PCRE character group allowed in URI segments
	 *
	 * @var	string
	 */

	protected $_permitted_uri_chars;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */

	public function __construct()
	{
		$this->config =& load_class('Config', 'system');

		log_message('info', 'URI Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Remove relative directory (../) and multi slashes (///)
	 *
	 * Do some final cleaning of the URI and return it, currently only used in self::_parse_request_uri()
	 *
	 * @param	string	$uri
	 * @return	string
	 */

	protected function _remove_relative_directory($uri)
	{
		$uris = array();
		$tok = strtok($uri, '/');
		while ($tok !== FALSE)
		{
			if (( ! empty($tok) OR $tok === '0') && $tok !== '..')
			{
				$uris[] = $tok;
			}
			$tok = strtok('/');
		}

		return implode('/', $uris);
	}

	// --------------------------------------------------------------------

	/**
	 * Filter URI
	 *
	 * Filters segments for malicious characters.
	 *
	 * @param	string	$str
	 * @return	void
	 */

	public function filter_uri(&$str)
	{
		if ( ! empty($str) && ! empty($this->_permitted_uri_chars) && ! preg_match('/^['.$this->_permitted_uri_chars.']+$/i'.(UTF8_ENABLED ? 'u' : ''), $str))
		{
			show_error('The URI you submitted has disallowed characters.', 400);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch URI Segment
	 *
	 * @see		ARUNA_URI::$segments
	 * @param	int		$n		Index
	 * @param	mixed		$no_result	What to return if the segment index is not found
	 * @return	mixed
	 */

	public function get_segment($val = 0)
	{
		if (FALSE === is_int($val)) 
		{
			show_error('setInteger expected Argument 1 to be Integer'); 
		}

		$GLOBALS['segments'][$val] = isset($GLOBALS['segments'][$val]) ? $GLOBALS['segments'][$val] : NULL;

		return $GLOBALS['segments'][$val];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch URI string
	 *
	 * @return	string	ARUNA_URI::$uri_string
	 */

	public function uri_string()
	{
		$_REQUEST['p'] = isset($_REQUEST['p']) ? $_REQUEST['p'] : 'home';
		
		return $_REQUEST['p'];
	}

}

?>