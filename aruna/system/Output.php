<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Source file from CodeIgniter v3.1.11
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 *	Information file: Output Class
	 */

defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]

class ARUNA_Output {

	/**
	 * Final output string
	 *
	 * @var	string
	 */

	public $final_output;

	/**
	 * List of server headers
	 *
	 * @var	array
	 */

	public $headers = array();

	/**
	 * List of mime types
	 *
	 * @var	array
	 */

	public $mimes =	array();

	/**
	 * Mime-type for the current page
	 *
	 * @var	string
	 */

	protected $mime_type = 'text/html';

	/**
	 * php.ini zlib.output_compression flag
	 *
	 * @var	bool
	 */

	protected $_zlib_oc = FALSE;

	/**
	 * CI output compression flag
	 *
	 * @var	bool
	 */

	protected $_compress_output = FALSE;

	/**
	 * mbstring.func_overload flag
	 *
	 * @var	bool
	 */

	protected static $func_overload;

	/**
	 * Class constructor
	 *
	 * Determines whether zLib output compression will be used.
	 *
	 * @return	void
	 */

	public function __construct()
	{
		$this->_zlib_oc = (bool) ini_get('zlib.output_compression');
		$this->_compress_output = (
			$this->_zlib_oc === FALSE
			&& config_item('compress_output') === TRUE
			&& extension_loaded('zlib')
		);

		isset(self::$func_overload) OR self::$func_overload = ( ! is_php('8.0') && extension_loaded('mbstring') && @ini_get('mbstring.func_overload'));

		// Get mime types for later
		$this->mimes =& get_mimes();

		log_message('info', 'Output Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Output
	 *
	 * Returns the current output string.
	 *
	 * @return	string
	 */

	public function get_output()
	{
		return $this->final_output;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Output
	 *
	 * Sets the output string.
	 *
	 * @param	string	$output	Output data
	 * @return	CI_Output
	 */

	public function set_output($output)
	{
		$this->final_output = $output;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Append Output
	 *
	 * Appends data onto the output string.
	 *
	 * @param	string	$output	Data to append
	 * @return	CI_Output
	 */

	public function append_output($output)
	{
		$this->final_output .= $output;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Header
	 *
	 * Lets you set a server header which will be sent with the final output.
	 *
	 * Note: If a file is cached, headers will not be sent.
	 * @todo	We need to figure out how to permit headers to be cached.
	 *
	 * @param	string	$header		Header
	 * @param	bool	$replace	Whether to replace the old header value, if already set
	 * @return	CI_Output
	 */

	public function set_header($header, $replace = TRUE)
	{
		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ($this->_zlib_oc && strncasecmp($header, 'content-length', 14) === 0)
		{
			return $this;
		}

		$this->headers[] = array($header, $replace);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Content-Type Header
	 *
	 * @param	string	$mime_type	Extension of the file we're outputting
	 * @param	string	$charset	Character set (default: NULL)
	 * @return	CI_Output
	 */

	public function set_content_type($mime_type, $charset = NULL)
	{
		if (strpos($mime_type, '/') === FALSE)
		{
			$extension = ltrim($mime_type, '.');

			// Is this extension supported?
			if (isset($this->mimes[$extension]))
			{
				$mime_type =& $this->mimes[$extension];

				if (is_array($mime_type))
				{
					$mime_type = current($mime_type);
				}
			}
		}

		$this->mime_type = $mime_type;

		if (empty($charset))
		{
			$charset = config_item('charset');
		}

		$header = 'Content-Type: '.$mime_type
			.(empty($charset) ? '' : '; charset='.$charset);

		$this->headers[] = array($header, TRUE);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Current Content-Type Header
	 *
	 * @return	string	'text/html', if not already set
	 */

	public function get_content_type()
	{
		for ($i = 0, $c = count($this->headers); $i < $c; $i++)
		{
			if (sscanf($this->headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1)
			{
				return $content_type;
			}
		}

		return 'text/html';
	}

	// --------------------------------------------------------------------

	/**
	 * Get Header
	 *
	 * @param	string	$header
	 * @return	string
	 */

	public function get_header($header)
	{
		// Combine headers already sent with our batched headers
		$headers = array();
		foreach ($this->headers as $value)
		{
			$headers[] = $value[0];
		}

		$headers = array_merge(
			$headers,
			headers_list()
		);

		if (empty($headers) OR empty($header))
		{
			return NULL;
		}

		// Count backwards, in order to get the last matching header
		for ($c = count($headers) - 1; $c > -1; $c--)
		{
			if (strncasecmp($header, $headers[$c], $l = self::strlen($header)) === 0)
			{
				return trim(self::substr($headers[$c], $l+1));
			}
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Set HTTP Status Header
	 *
	 * As of version 1.7.2, this is an alias for common function
	 * set_status_header().
	 *
	 * @param	int	$code	Status code (default: 200)
	 * @param	string	$text	Optional message
	 * @return	CI_Output
	 */

	public function set_status_header($code = 200, $text = '')
	{
		set_status_header($code, $text);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Display Output
	 *
	 * Processes and sends finalized output data to the browser along
	 * with any server headers and profile data. It also stops benchmark
	 * timers so the page rendering speed and memory usage can be shown.
	 *
	 * Note: All "view" data is automatically put into $this->final_output
	 *	 by controller class.
	 *
	 * @uses	CI_Output::$final_output
	 * @param	string	$output	Output data override
	 * @return	void
	 */

	public function _display($output = '')
	{
		// --------------------------------------------------------------------

		// Set the output data
		if ($output === '')
		{
			$output =& $this->final_output;
		}

		// --------------------------------------------------------------------

		// Are there any server headers to send?
		if (count($this->headers) > 0)
		{
			foreach ($this->headers as $header)
			{
				@header($header[0], $header[1]);
			}
		}

		// --------------------------------------------------------------------

		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if ($this->_compress_output === TRUE)
		{
			if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
			{
				header('Content-Encoding: gzip');
				header('Content-Length: '.self::strlen($output));
			}
			else
			{
				// User agent doesn't support gzip compression,
				// so we'll have to decompress our cache
				$output = gzinflate(self::substr($output, 10, -8));
			}
		}

		echo $output;
		log_message('info', 'Final output sent to browser');
		return;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache Header
	 *
	 * Set the HTTP headers to match the server-side file cache settings
	 * in order to reduce bandwidth.
	 *
	 * @param	int	$last_modified	Timestamp of when the page was last modified
	 * @param	int	$expiration	Timestamp of when should the requested page expire from cache
	 * @return	void
	 */

	public function set_cache_header($last_modified, $expiration)
	{
		$max_age = $expiration - $_SERVER['REQUEST_TIME'];

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $last_modified <= strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
			$this->set_status_header(304);
			exit;
		}

		header('Pragma: public');
		header('Cache-Control: max-age='.$max_age.', public');
		header('Expires: '.gmdate('D, d M Y H:i:s', $expiration).' GMT');
		header('Last-modified: '.gmdate('D, d M Y H:i:s', $last_modified).' GMT');
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */

	protected static function strlen($str)
	{
		return (self::$func_overload)
			? mb_strlen($str, '8bit')
			: strlen($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */

	protected static function substr($str, $start, $length = NULL)
	{
		if (self::$func_overload)
		{
			// mb_substr($str, $start, null, '8bit') returns an empty
			// string on PHP 5.3
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}

?>