<?php

#[\AllowDynamicProperties]

class Aruna_Model
{
	public function __construct($group_db = 'default', $database_type = 'MySQL')
	{
		if (config_item('database_library') === 'Aruna' || config_item('database_library_alternative') === 'Aruna')
		{
			$this->db = load_db($group_db, $database_type, TRUE);
		}
	}

	/**
	 * __get magic
	 *
	 * Allows models to access Aruna's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string	$key
	 */
    
	public function __get($key)
	{
		// Debugging note:
		//	If you're here because you're getting an error message
		//	saying 'Undefined Property: aruna/system/Model.php', it's
		//	most likely a typo in your model code.
		return get_instance()->$key;
	}
}

?>