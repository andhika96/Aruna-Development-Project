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
	 *	Information file: Session Driver Class
	 */

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class ARUNA_Session_driver implements SessionHandlerInterface {

	protected $_config;

	/**
	 * Data fingerprint
	 *
	 * @var	bool
	 */

	protected $_fingerprint;

	/**
	 * Lock placeholder
	 *
	 * @var	mixed
	 */

	protected $_lock = FALSE;

	/**
	 * Read session ID
	 *
	 * Used to detect session_regenerate_id() calls because PHP only calls
	 * write() after regenerating the ID.
	 *
	 * @var	string
	 */

	protected $_session_id;

	/**
	 * Success and failure return values
	 *
	 * Necessary due to a bug in all PHP 5 versions where return values
	 * from userspace handlers are not handled properly. PHP 7 fixes the
	 * bug, so we need to return different values depending on the version.
	 *
	 * @see	https://wiki.php.net/rfc/session.user.return-value
	 * @var	mixed
	 */

	protected $_success, $_failure;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */

	public function __construct(&$params)
	{
		$this->_config =& $params;

		if (is_php('7'))
		{
			$this->_success = TRUE;
			$this->_failure = FALSE;
		}
		else
		{
			$this->_success = 0;
			$this->_failure = -1;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * PHP 5.x validate ID
	 *
	 * Enforces session.use_strict_mode
	 *
	 * @return	void
	 */

	public function php5_validate_id()
	{
		if (isset($_COOKIE[$this->_config['cookie_name']]) && ! $this->validateSessionId($_COOKIE[$this->_config['cookie_name']]))
		{
			unset($_COOKIE[$this->_config['cookie_name']]);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Cookie destroy
	 *
	 * Internal method to force removal of a cookie by the client
	 * when session_destroy() is called.
	 *
	 * @return	bool
	 */

	protected function _cookie_destroy()
	{
		return setcookie(
			$this->_config['cookie_name'],
			NULL,
			1,
			$this->_config['cookie_path'],
			$this->_config['cookie_domain'],
			$this->_config['cookie_secure'],
			TRUE
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock
	 *
	 * A dummy method allowing drivers with no locking functionality
	 * (databases other than PostgreSQL and MySQL) to act as if they
	 * do acquire a lock.
	 *
	 * @param	string	$session_id
	 * @return	bool
	 */

	protected function _get_lock($session_id)
	{
		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock
	 *
	 * @return	bool
	 */

	protected function _release_lock()
	{
		if ($this->_lock)
		{
			$this->_lock = FALSE;
		}

		return TRUE;
	}
}

?>