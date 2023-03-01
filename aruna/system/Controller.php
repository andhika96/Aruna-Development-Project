<?php

#[\AllowDynamicProperties]

class Aruna_Controller 
{
	/**
	 * Reference to the Aruna singleton
	 *
	 * @var	object
	 */

	private static $instance;

	/**
	 * Aruna_Loader
	 *
	 * @var	Aruna_Loader
	 */

	public $load;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */

	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (Aruna.php) to local class variables
		// so that Aruna can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'system');
		$this->load->initialize();

		log_message('info', 'Controller Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Aruna singleton
	 *
	 * @static
	 * @return	object
	 */

	public static function &get_instance()
	{
		return self::$instance;
	}
}

?>