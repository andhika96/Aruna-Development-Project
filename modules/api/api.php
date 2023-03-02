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

defined('MODULEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class api extends RestController 
{
	function __construct()
	{
		parent::__construct();
	}

	public function users_get()
	{
		// Users from a data store e.g. database
		$users = 
		[
			['id' => 0, 'name' => 'John', 'email' => 'john@example.com'],
			['id' => 1, 'name' => 'Jim', 'email' => 'jim@example.com'],
		];

		$id = $this->get('id');

		if ( $id === null )
		{
			// Check if the users data store contains users
			if ( $users )
			{
				// Set the response and exit
				$this->response($users, 200);
			}
			else
			{
				// Set the response and exit
				$this->response(
				[
					'status' => false,
					'message' => 'No users were found'
				], 404);
			}
		}
		else
		{
			if ( array_key_exists( $id, $users ) )
			{
				$this->response( $users[$id], 200 );
			}
			else
			{
				$this->response(
				[
					'status' => false,
					'message' => 'No such user found'
				], 404);
			}
		}
	}
}

?>