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

namespace Aruna\Init;

defined('BASEPATH') OR exit('No direct script access allowed');

class Validation
{
	/**
	 * Stores custom error message to use
	 * during validation. Where 'key' is the alias.
	 *
	 * @var array
	 */

	protected $setErrors = [];

	protected function set_message($key, $val)
	{
		$this->setErrors[$key] = $val;
	}

	public function setCustomError($key)
	{
		$this->setErrors[$key] = isset($this->setErrors[$key]) ? $this->setErrors[$key] : false;
		return $this->setErrors[$key];
	}
}

?>