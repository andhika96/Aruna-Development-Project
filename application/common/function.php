<?php

	/*
	 *  Aruna Development Project
	 *  IS NOT FREE SOFTWARE
	 *  Codename: Ardev Cassandra
	 *  Source: Based on Sosiaku Social Networking Software
	 *  Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *  Created and developed by Andhika Adhitia N
	 */

	defined('APPPATH') OR exit('No direct script access allowed');

	// ------------------------------------------------------------------------

	/**
	 * Do Authentication
	 * 
	 * Berfungsi untuk otentikasi pengguna untuk mengakses halaman
	 *
	 * @return boolean
	 */
	
	function do_auth($uid = 0, $username = '', $token = '', $whitelist_role = array())
	{
		$roles = array();

		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select roles from ml_accounts where id = :id and username = :username and token = :token");
		$bindParam = $db->sql_bindParam(['id' => $uid, 'username' => $username, 'token' => $token], $res);
		while ($row = $db->sql_fetch_single($bindParam))
		{
			$roles[] = $row['roles'];
		}

		if (is_array($whitelist_role))
		{
			foreach ($whitelist_role as $key) 
			{
				if (in_array($key, $roles))
				{
					return TRUE;
				}
			}
		}
		else 
		{
			if (is_array($roles) && in_array($whitelist_role, $roles))
			{
				return TRUE;
			}
		}	

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Has Access
	 * 
	 * Berfungsi untuk otentikasi pengguna untuk mengakses halaman
	 * 
	 * @return string
	 */

	function has_access($whitelist_role = array())
	{
		$id 		= isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$token		= isset($_SESSION['token']) ? $_SESSION['token'] : NULL;
		$username	= isset($_SESSION['username']) ? $_SESSION['username'] : NULL;

		if ( ! do_auth($id, $username, $token, $whitelist_role))
		{
			section_close('<div class="card card-full-color card-full-warning" role="alert"><div class="card-body"><i class="fas fa-exclamation-triangle mr-1"></i> The page you requested cannot be displayed right now. It may be temporarily unavailable, the link you clicked on may be broken or expired, or you may not have permission to view this page.</div></div>');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Has Allow Access
	 * 
	 * Berfungsi untuk membatasi siapa saja pengguna yang dapat melihat fitur atau modul
	 *
	 * @return boolean
	 */

	function has_allow_access($whitelist_role = array())
	{
		$id 		= isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$token		= isset($_SESSION['token']) ? $_SESSION['token'] : NULL;
		$username	= isset($_SESSION['username']) ? $_SESSION['username'] : NULL;

		if ( ! do_auth($id, $username, $token, $whitelist_role))
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Has Login
	 * 
	 * Berfungsi untuk memeriksa pengguna sudah login atau tidak
	 * 
	 * @return boolean
	 */

	function has_login()
	{
		$url = load_ext('url');

		if (empty($_SESSION['id']) && empty($_SESSION['username']) && empty($_SESSION['token']))
		{
			redirect('auth/login');
		}
		else
		{
			$db = load_db('default', 'MySQL');

			$res_token = $db->sql_prepare("select token from ml_accounts where id = :id and username = :username");
			$bindParam_token = $db->sql_bindParam(['id' => $_SESSION['id'], 'username' => $_SESSION['username']], $res_token);
			$row_token = $db->sql_fetch_single($bindParam_token);

			if ($row_token['token'] != $_SESSION['token'])
			{
				$_SESSION['id'] = '';
				$_SESSION['username'] = '';
				$_SESSION['token'] = '';

				redirect('auth/login');
			}
			else
			{
				$res = $db->sql_prepare("select status from ml_accounts where id = :id and username = :username and token = :token");
				$bindParam = $db->sql_bindParam(['id' => $_SESSION['id'], 'username' => $_SESSION['username'], 'token' => $_SESSION['token']], $res);
				$row = $db->sql_fetch_single($bindParam);

				if ($row['status'] == 1 && uri_string() != 'dashboard/checkpoint')
				{
					redirect('dashboard/checkpoint');
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Avatar
	 * 
	 * Berfungsi untuk menampilkan foto avatar pengguna
	 *
	 * @return string
	 */

	function avatar($userid)
	{
		// Load URL Extension
		$url = load_ext('url');

		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select avatar from ml_user_information where user_id = :user_id");
		$bindParam = $db->sql_bindParam(['user_id' => $userid], $res);
		$row = $db->sql_fetch_single($bindParam);

		$row['avatar'] = isset($row['avatar']) ? $row['avatar'] : '';

		if ( ! $row['avatar'])
		{
			$avatar = '';
		}
		else 
		{
			$avatar = base_url($row['avatar']);
		}

		return $avatar;
	}

	// ------------------------------------------------------------------------

	/**
	 * Avatar Alternative
	 * 
	 * Berfungsi untuk menampilkan foto avatar pengguna
	 *
	 * @return string
	 */

	function avatar_alt($id, $size = '', $class = NULL, $border = NULL)
	{
		// Load URL Extension
		$url = load_ext('url');

		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select avatar from ml_user_information where user_id = :user_id");
		$bindParam = $db->sql_bindParam(['user_id' => $id], $res);
		$row = $db->sql_fetch_single($bindParam);

		$row['avatar'] = isset($row['avatar']) ? $row['avatar'] : '';

		if ($border == 1)
		{
			$border = 'border: 1px #fff solid;';
		}

		if (empty($size))
		{
			if ( ! $row['avatar'])
			{
				$resize = 'style="font-size: 5em;"';
			}
			else 
			{
				$resize = 'style="width: 70px;height: 70px;'.$border.'"';
			}
		}
		else
		{
			if ( ! $row['avatar'])
			{
				if ( ! is_numeric($size) && $size == 'small')
				{
					$resize = 'style="width: 32px;height: 32px;vertical-align: middle;"';
				}
				else 
				{
					$resize = 'style="width: '.$size.'px;height: '.$size.'px;font-size: '.$size.'px;vertical-align: middle;"';
				}
			}
			else 
			{
				if ( ! is_numeric($size) && $size == 'small')
				{
					$resize = 'style="width: 32px;height: 32px;'.$border.'"';
				}
				else 
				{
					$resize = 'style="width: '.$size.'px;height: '.$size.'px;'.$border.'"';
				}
			}
		}

		if ( ! $row['avatar'])
		{
			$avatar = '<i class="fas fa-user-circle '.$class.'" '.$resize.'></i>';
		}
		else 
		{
			$avatar = '<img src="'.base_url($row['avatar']).'" class="rounded-circle '.$class.'" '.$resize.'>';
		}

		return $avatar;
	}

	// ------------------------------------------------------------------------
	
	function get_status_gender($key = '')
	{
		if ($key == 1)
		{
			$output = 'Male';
		}
		elseif ($key == 2) 
		{
			$output = 'Female';	
		}
		else
		{
			$output = 'Unknown';
		}

		return $output;
	}

	// ------------------------------------------------------------------------

	function get_status_user($key = '')
	{
		if ($key == 0)
		{
			$output = '<span class="text-success">Active</span>';
		}
		elseif ($key == 1)
		{
			$output = '<span class="text-danger">Not active</span>';
		}
		else
		{
			$output = '<span class="text-muted">Unknown status</span>';
		}

		return $output;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Role
	 * 
	 * Berfungsi untuk mendapatkan atau menampilkan status atau peran akun pengguna.
	 *
	 * @return string
	 */

	function get_role($id = 0)
	{
		// Connected to the database
		$db = load_db('default', 'MySQL');

		// $res = $db->sql_prepare("select a.*, a.id as uid, r.* from ml_accounts as a join ml_roles as r on r.id = a.roles where r.code_name = a.role_code and a.id = :id");
		$res = $db->sql_prepare("select name from ml_roles where id = :id");
		$bindParam = $db->sql_bindParam(['id' => $id], $res);
		$row = $db->sql_fetch_single($bindParam);

		$row['name'] = isset($row['name']) ? $row['name'] : NULL;
	
		return $row['name'];
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Config Site
	 * 
	 * Berfungsi untuk mendapatkan atau menampilkan konfigurasi halaman situs
	 * seperti, nama situs, slogan, kata kunci, dsb.
	 *
	 * @return string
	 */

	function get_csite($key)
	{
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select * from ml_site_config where id = :id");
		$bindParam = $db->sql_bindParam(['id' => 1], $res);
		$row = $db->sql_fetch_single($bindParam);

		if ($key == 'site_thumbnail')
		{
			$row['site_thumbnail']  = ! empty($row['site_thumbnail']) ? base_url($row['site_thumbnail']) : base_url('assets/images/aruna_card_1200.jpg');
		
			return $row['site_thumbnail'];
		}
		else
		{
			$row[$key] = isset($row[$key]) ? $row[$key] : NULL;

			return $row[$key];
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Config Title
	 * 
	 * Berfungsi untuk mendapatkan judul halaman situs
	 * 
	 * @return string
	 */

	function get_ctitle()
	{
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select * from ml_site_config where id = :id");
		$bindParam = $db->sql_bindParam(['id' => 1], $res);
		$row = $db->sql_fetch_single($bindParam);

		if ( ! get_data_global('title'))
		{
			return $row['site_name'].' - '.$row['site_slogan'];
		}
		else 
		{
			return get_data_global('title').' - '.$row['site_name'];
		}	
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Client Value
	 * 
	 * Hampir sama fungsinya dengan fungsi get_client() fungsi ini menampilkan
	 * informasi data pengguna nama kolom yang dimasukkan.
	 * 
	 * @return string
	 */

	function get_client_value($key = '', $value = '',  $coloum = '')
	{
		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select * from ml_accounts where $key = :$key");
		$bindParam = $db->sql_bindParam([$key => $value], $res);
		$row = $db->sql_fetch_single($bindParam);

		$row[$coloum] = isset($row[$coloum]) ? $row[$coloum] : NULL;
	
		return $row[$coloum];
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Client
	 * 
	 * Hampir sama fungsinya dengan fungsi get_user() fungsi ini menampilkan
	 * informasi data pengguna per id akun bukan per session pengguna.
	 * 
	 * @return string
	 */

	function get_client($key = '',  $coloum = '')
	{
		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select * from ml_accounts where id = :id");
		$bindParam = $db->sql_bindParam(['id' => $key], $res);
		$row = $db->sql_fetch_single($bindParam);

		$row[$coloum] = isset($row[$coloum]) ? $row[$coloum] : NULL;
	
		return $row[$coloum];
	}

	function get_info_client($key = '',  $coloum = '')
	{
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select * from ml_user_information where user_id = :user_id");
		$bindParam = $db->sql_bindParam(['user_id' => $key], $res);
		$row = $db->sql_fetch_single($bindParam);

		$row[$coloum] = isset($row[$coloum]) ? $row[$coloum] : NULL;
	
		return $row[$coloum];
	}

	// ------------------------------------------------------------------------

	/**
	 * Get User
	 * 
	 * Berfungsi untuk mendapatkan informasi data pengguna per session
	 * 
	 * @return string
	 */

	function get_user($key)
	{
		$db 		= load_db('default', 'MySQL');
		$id 		= isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$token		= isset($_SESSION['token']) ? $_SESSION['token'] : NULL;
		$username	= isset($_SESSION['username']) ? $_SESSION['username'] : NULL;

		$res = $db->sql_prepare("select * from ml_accounts where id = :id and username = :username and token = :token");
		$bindParam = $db->sql_bindParam(['id' => $id, 'username' => $username, 'token' => $token], $res);
		$row = $db->sql_fetch_single($bindParam);

		$row[$key] = isset($row[$key]) ? $row[$key] : NULL;
	
		return $row[$key];
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Content Page
	 * 
	 * Berfungsi untuk mendapatkan konten untuk setiap masing-masing halaman depan
	 * bersifat dinamis karena bisa diganti melalui panel admin.
	 * 
	 * @return string
	 */

	function get_content_page($uri = '', $section = '', $column = '')
	{
		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select var from ml_pages where uri = :uri");
		$bindParam = $db->sql_bindParam(['uri' => $uri], $res);
		$row = $db->sql_fetch_single($bindParam);

		$json_decode = json_decode($row['var'], true);

		if ( empty($json_decode[$section]) || empty($json_decode[$section][$column]))
		{
			return '';
		}
		else
		{
			return $json_decode[$section][$column]['content'];
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Section Page
	 * 
	 * Berfungsi untuk mendapatkan konten untuk bagian header dan footer dihalaman depan
	 * bersifat dinamis karena bisa diganti melalui panel admin.
	 * 
	 * @return string
	 */

	function get_section_page($uri = '', $section = '', $column = '', $wlink = '')
	{
		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select var from ml_section where uri = :uri");
		$bindParam = $db->sql_bindParam(['uri' => $uri], $res);
		$row = $db->sql_fetch_single($bindParam);

		$json_decode = json_decode($row['var'], true);

		if ( empty($json_decode[$section]) || empty($json_decode[$section][$column]))
		{
			return '';
		}
		else
		{
			if ( ! isset($json_decode[$section][$column]['link']) && $wlink == 'wlink')
			{
				return '';
			}
			elseif (isset($json_decode[$section][$column]['link']) && $wlink == 'wlink')
			{
				return $json_decode[$section][$column]['link'];
			}
			else
			{
				return $json_decode[$section][$column]['content'];
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Category
	 * 
	 * Berfungsi untuk mendapatkan konten untuk setiap masing-masing halaman depan
	 * bersifat dinamis karena bisa digantin melalui panel admin.
	 * 
	 * @return string
	 */

	function get_category(int $cid = 0)
	{
		// Connected to the database
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select name from ml_blog_category where id = :id");
		$bindParam = $db->sql_bindParam(['id' => $cid], $res);
		$row = $db->sql_fetch_single($bindParam);

		return $row['name'];
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Maintenance Mode
	 * 
	 * Berfungsi untuk mengubah status website menjadi mode maintenance
	 * 
	 * @return string
	 */

	function get_maintenance_mode()
	{
		$url = load_ext('url');

		if ($exclude_uris = config_item('mt_exclude_uris'))
		{
			$target = FALSE;

			foreach ($exclude_uris as $excluded)
			{
			    // Convert wildcards to RegEx
			    $excluded = str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), $excluded);
			    
				if (preg_match('#^'.$excluded.'$#i'.(UTF8_ENABLED ? 'u' : ''), uri_string()) || get_user('roles') == 99 || get_user('roles') == 98 || get_user('roles') == 97 || get_user('roles') == 96)
				{
					$target = TRUE;
				}
			}

			if ($target !== TRUE)
			{
				if (get_csite('offline_mode') == 1)
				{
					include APPPATH.'views/maintenance/maintenance.php';
					exit;
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Language
	 * 
	 * Berfungsi untuk mendapatkan konfigurasi bahasa untuk pengguna
	 * 
	 * @return string
	 */

	function get_language()
	{
		$ext = load_ext(['cookie']);

		if (get_cookie('ml_language'))
		{
			$lang = get_cookie('ml_language');
		}
		else 
		{
			$lang = 'indonesian';
		}

		return $lang;
	}

	// ------------------------------------------------------------------------

	/**
	 * Init Language
	 * 
	 * 
	 * @return string
	 */

	function init_language()
	{
		$ext = load_ext(['cookie']);
		$allowed_language = ['english', 'indonesian'];

		if (get_cookie('ml_language'))
		{
			$lang = get_cookie('ml_language');
		}
		else 
		{
			$lang = 'indonesian';
		}

		foreach ($allowed_language as $key) 
		{
			if ($key == $lang)
			{
				$lang_list[] = '<i class="font-weight-bold">'.ucfirst($key).'</i>';
			}
			else
			{
				$lang_list[] = '<a href="'.site_url('awesome_admin/setlanguage?id='.$key).'">'.ucfirst($key).'</a>';
			}

			$lang_select = implode(' | ', $lang_list);
		}

		return $lang_select;
	}

	// ------------------------------------------------------------------------

	/**
	 * Init Language
	 * 
	 * 
	 * @return string
	 */

	function unselected_language()
	{
		$ext = load_ext(['cookie']);
		$allowed_language = ['english', 'indonesian'];

		if ( ! get_cookie('ml_language'))
		{
			$lang = 'indonesian';
		}
		else 
		{
			$lang = get_cookie('ml_language');
		}

		foreach ($allowed_language as $key) 
		{
			if ($key != $lang)
			{
				return '<a class="dropdown-item" href="'.site_url('home/setlanguage?id='.$key).'"><i class="fas fa-globe-europe fa-fw mr-1"></i> '.ucfirst($key).'</a>';
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Translate Function
	 * 
	 * Berfungsi untuk menterjemahkan bahasa yang diinginkan berdasarkan bahasa yang dipilih
	 * 
	 * @return string
	 */

	function t($str, $att1 = '', $att2 = '', $att3 = '', $godb = 0) 
	{
		// Connected to the database
		$db = load_db('default', 'MySQL');

		$dbstr = addslashes($str);
		$res = $db->sql_prepare("select * from ml_language where lang_from = :lang_from and lang = :lang limit 1");
		$bindParam = $db->sql_bindParam(['lang_from' => $dbstr, 'lang' => get_language()], $res);

		if ( ! $db->sql_counts($bindParam)) 
		{
			$insert_lang = [
				'lang_from'	=> $dbstr,
				'lang_to'	=> '',
				'lang'		=> get_language()
			];

			$db->sql_insert($insert_lang, 'ml_language');
		}
		else 
		{
			$row = $db->sql_fetch_single($bindParam);

			if (strlen($row['lang_to'])) 
			{
				$str = $row['lang_to'];
			}
			else 
			{
				$str = $row['lang_from'];
			}
		}

		if ($godb) 
		{
			$str = addslashes($str);
		}
	
		return str_replace(['{1}', '{2}', '{3}'], [$att1, $att2, $att3], $str);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Set Meta Function
	 * 
	 * Berfungsi untuk set meta title, description, dan image dengan global variable
	 * 
	 * @return string
	 */

	function set_meta($url, $title, $description, $image)
	{	
		$GLOBALS['meta']['url'] = $url;	
		$GLOBALS['meta']['title'] = $title;
		$GLOBALS['meta']['description'] = $description;
		$GLOBALS['meta']['image'] = $image;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Get Meta Description
	 * 
	 * Berfungsi untuk mendapatkan data meta description dari function set_meta()
	 * Jika value dari argument function get_meta() kosong atau global variable meta kosong otomatis akan mengambil meta
	 * dari database
	 * 
	 * @return string
	 */

	function get_meta($key)
	{
		$db = load_db('default', 'MySQL');

		$res = $db->sql_prepare("select * from ml_site_config where id = :id");
		$bindParam = $db->sql_bindParam(['id' => 1], $res);
		$row = $db->sql_fetch_single($bindParam);

		if ($key == 'url')
		{
			$getResTitle = (empty($key) || ! isset($GLOBALS['meta']) || empty($GLOBALS['meta']['url'])) ? site_url() : $GLOBALS['meta']['url'];

			return $getResTitle;
		}
		elseif ($key == 'title')
		{
			$getResTitle = (empty($key) || ! isset($GLOBALS['meta']) || empty($GLOBALS['meta']['title'])) ? $row['site_name'] : $GLOBALS['meta']['title'];

			return $getResTitle;
		}
		elseif ($key == 'description')
		{
			$getResDescription = (empty($key) || ! isset($GLOBALS['meta']) || empty($GLOBALS['meta']['description'])) ? $row['site_slogan'] : $GLOBALS['meta']['description'];

			return $getResDescription;
		}
		elseif ($key == 'image')
		{
			$getResImage = (empty($key) || ! isset($GLOBALS['meta']) || empty($GLOBALS['meta']['image'])) ? get_csite('site_thumbnail') : base_url($GLOBALS['meta']['image']);

			return $getResImage;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Date
	 * 
	 * Berfungsi untuk mengubah waktu sistem UNIX dan menampilkan waktu umum
	 * 
	 * @return string
	 */

	function get_date($timeo, $type = 'time') 
	{
		// Default set timezone is +7 for Jakarta, Indonesia
		$timezone = +7;

		// Default set for some settings
		$settings = [
			'time_format' 	 => 'g:i a',
			'date_format' 	 => 'M jS Y',
			'date_today' 	 => 'Today',
			'date_yesterday' => 'Yesterday'
		];

		$timeline = $timeo+$timezone*3600;
		$current = time()+$timezone*3600;
		$it_s = intval($current - $timeline);
		$it_m = intval($it_s/60);
		$it_h = intval($it_m/60);
		$it_d = intval($it_h/24);
		$it_y = intval($it_d/365);

		$timec = time()-$timeo;

		if ($timec < 3600 && $timec >= 0) 
		{
			return ceil($timec/60).' minute ago';
		}
		elseif ($timec < 12*3600 && $timec >= 0) 
		{
			return ceil($timec/3600).' hours ago';
		}
		else 
		{
			if ($type == 'time') 
			{
				return gmdate($settings['date_format'].', '.$settings['time_format'], $timeline);
			}
			else 
			{
				return gmdate($settings['date_format'], $timeline);
			}
		}

		if ($type == 'date') 
		{
			return gmdate($settings['date_format'], $timeline);
		}
		else 
		{
			if (gmdate("j", $timeline) == gmdate("j", $current)) 
			{
				return $settings['date_today'].', '.gmdate($settings['time_format'], $timeline);
			}
			elseif (gmdate("j", $timeline) == gmdate("j", ($current-3600*24))) 
			{
				return $settings['date_yesterday'].', '.gmdate($settings['time_format'], $timeline);
			}
			return gmdate($settings['date_format'].', '.$settings['time_format'], $timeline);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Format Size
	 * 
	 * Berfungsi untuk menampilkan ukuran berkas
	 * dalam satuan KB, MB, GB, TB, dsb.
	 * 
	 * @return string
	 */

	function format_size($file) 
	{
		if ( ! file_exists($file)) 
		{
			$bytes = '';
		}
		else {
			$bytes = filesize($file);
		}

		if ($bytes < 1024) 
		{
			return $bytes.' B';
		} 
		elseif ($bytes < 1048576) 
		{
			return round($bytes / 1024, 2).' KB';
		}
		elseif ($bytes < 1073741824) 
		{
			return round($bytes / 1048576, 2).' MB';
		}
		elseif ($bytes < 1099511627776) 
		{
			return round($bytes / 1073741824, 2).' GB';
		}
		elseif ($bytes < 1125899906842624) 
		{
			return round($bytes / 1099511627776, 2).' TB';
		}
		elseif ($bytes < 1152921504606846976) 
		{
			return round($bytes / 1125899906842624, 2).' PB';
		}
		elseif ($bytes < 1180591620717411303424) 
		{
			return round($bytes / 1152921504606846976, 2).' EB';
		}
		elseif ($bytes < 1208925819614629174706176) 
		{
			return round($bytes / 1180591620717411303424, 2).' ZB';
		}
		else 
		{
			return round($bytes / 1208925819614629174706176, 2).' YB';
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Register JS
	 * 
	 * Berfungsi untuk mendaftarkan file javascript per module
	 * 
	 * @return string
	 */

	function register_js($file = array())
	{
		$GLOBALS['register_js'] = implode("\r\n 			", $file);

		return $GLOBALS['register_js'];
	}

	// ------------------------------------------------------------------------

	/**
	 * Load JS
	 * 
	 * Berfungsi untuk menampilkan berkas javascript yang telah didaftarkan
	 * fungsi diletakan difolder tema
	 * 
	 * @return string
	 */

	function load_js()
	{
		return get_data_global('register_js');	
	}

	// ------------------------------------------------------------------------

	/**
	 * getNavMenu
	 * 
	 * Berfungsi untuk mendeteksi halaman, jika dihalaman yang lagi dibuka
	 * fungsi class pada menu navigasi akan aktif
	 * 
	 * @return string
	 */

	function getNavMenu(string $currect_page = '')
	{
		$ext = load_ext(['url']);

		return $currect_page === uri_string() ? 'active' : '';
	}

	// ------------------------------------------------------------------------

	function error_page($message = '', $class = '')
	{
		if (empty($message))
		{
			$message = '<h4 class="h5 font-weight-normal" style="line-height: 1.6">Sorry sweetheart, I can\'t find the page you requested <i class="far fa-frown ml-1 fa-lg"></i></h6><div class="mt-3"><a href="javascript:history.back();" class="text-white"><i class="fas fa-long-arrow-alt-left mr-2"></i> Back to Previous Page</a></div>';
		}

		section_content('<div class="bg-danger text-white text-center rounded p-4 '.$class.'">'.$message.'</div>');
		stop_here();
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('cs_offset'))
	{
		function cs_offset()
		{
			return cs_num_per_page()*(get_data_global('page')-1);
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('cs_num_per_page'))
	{
		function cs_num_per_page()
		{
			$cs_config = load_cs_config('config');

			return $cs_config->item('num_per_page_exam');
		}
	}

	// ------------------------------------------------------------------------

	if ( ! function_exists('breadcrumb'))
	{
		function breadcrumb($data = array(), $output = '')
		{
			if (is_array($data))
			{
				$output .= '
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb rounded" style="background-color: #e9ecef;padding: .75rem 1rem;">';

				foreach ($data as $key => $value) 
				{
					if (empty($value))
					{	
						$output .= '
							<li class="breadcrumb-item active" aria-current="page">'.$key.'</li>';
					}
					else
					{
						$output .= '
							<li class="breadcrumb-item active" aria-current="page"><a href="'.$value.'">'.$key.'</a></li>';
					}
				}

				$output .= '
					</ol>
				</nav>';

				return $output;
			}
			else
			{
				show_error('Invalid breadcrumb data.');
			}
		}
	}

?>