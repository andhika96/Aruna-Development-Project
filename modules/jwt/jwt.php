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

class jwt extends RestController
{
	// Accounts data for login model emulation
	protected $accounts = 
	[
		'aruna2022'	=>	'aruna2022!'
	];
	
	public function __construct() 
	{
		// Construct the parent class
		parent::__construct();

		// Setup URI String and Segment to active Limit
		// $this->_remap($this->uri->uri_string(), $this->uri->get_segment());
	}

	public function index()
	{
		return view('index');
	}

	// User JWT authentication to get the token
	public function token_post()
	{		
		$this->form_validation->set_data([
			'username' => $this->post('username'),
			'password' => $this->post('password'),
		]);

		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == TRUE)
		{
			if ($this->login($this->post('username'), $this->post('password')))
			{
				// Get Time
				$date = new DateTime();

				$token['iss'] = site_url();
				$token['aud'] = site_url();				
				$token['iat'] = $date->getTimestamp(); // Issued at
				$token['nbf'] = $date->getTimestamp()+10; // Not before in seconds
				$token['exp'] = $date->getTimestamp()+$this->config->item('jwt_token_expire'); // Expire time in seconds
				$token['data'] = ['username' => $this->post('username')];

				$output_data['status'] = $this::HTTP_OK;
				$output_data['data'] = ['token' => $this->jwt_encode($token)];
				$this->response($output_data, $this::HTTP_OK);
			}
			else
			{
				$output_data[$this->config->item('rest_status_field_name')] = $this::HTTP_UNAUTHORIZED;
				$output_data[$this->config->item('rest_message_field_name')] = "Invalid username or password!";
				$this->response($output_data, $this::HTTP_UNAUTHORIZED);
			}
		}
		else
		{
			$output_data[$this->config->item('rest_status_field_name')] = $this::HTTP_UNPROCESSABLE_ENTITY;
			$output_data[$this->config->item('rest_message_field_name')] = $this->form_validation->error_array();

			$this->response($output_data, $this::HTTP_UNPROCESSABLE_ENTITY);
		}
	}

	// Refresh the token with new expirey time
	public function token_refresh_get()
	{
		try
		{
			$decoded = $this->jwt_decode($this->jwt_token());

			if ($this->username_check($decoded['username']) == FALSE)
			{
				$output_data[$this->config->item('rest_status_field_name')] = $this::HTTP_UNAUTHORIZED;
				$output_data[$this->config->item('rest_message_field_name')] = "The token user id is not exist in the system!";
				$this->response($output_data, $this::HTTP_UNAUTHORIZED);
			}

			// Get Time
			$date = new DateTime();

			$token['iss'] = site_url();
			$token['aud'] = site_url();				
			$token['iat'] = $date->getTimestamp(); // Issued at
			$token['nbf'] = $date->getTimestamp()+10; // Not before in seconds
			$token['exp'] = $date->getTimestamp()+$this->config->item('jwt_token_expire'); // Expire time in seconds
			$token['data'] = ['username' => $decoded['username']];

			$output_data['status'] = $this::HTTP_OK;
			$output_data['data'] = ['token' => $this->jwt_encode($token)];
			$this->response($output_data, $this::HTTP_OK);
		}
		catch (Exception $e)
		{
			$output_data[$this->config->item('rest_status_field_name')] = $this::HTTP_UNAUTHORIZED;
			$output_data[$this->config->item('rest_message_field_name')] = $e->getMessage();
			$this->response($output_data, $this::HTTP_UNAUTHORIZED);
		}
	}

	// JWT test endpoint, it shows the token information (need token authorization)
	public function token_info_get()
	{
		try
		{
			$output_data['status'] = $this::HTTP_OK;
			$output_data['data'] = $this->jwt_decode($this->jwt_token());
			$this->response($output_data, $this::HTTP_OK);
		}
		catch (Exception $e)
		{
			$output_data[$this->config->item('rest_status_field_name')] = $this::HTTP_UNAUTHORIZED;
			$output_data[$this->config->item('rest_message_field_name')] = $e->getMessage();
			$this->response($output_data, $this::HTTP_UNAUTHORIZED);
		}
	}

	// Login model emulation, check if user exist on database
	private function username_check($username)
	{
		if (array_key_exists($username, $this->accounts))
		{
			return TRUE;
		}
		
		return FALSE;
	}

	// Login model emulation, login function
	private function login($username, $password)
	{
		if (array_key_exists($username, $this->accounts) AND $this->accounts[$username] === $password)
		{
			return TRUE;
		}
		
		return FALSE;
	}
}

?>