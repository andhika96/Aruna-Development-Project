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

	defined('BASEPATH') OR exit('No direct script access allowed');

	/*
	|--------------------------------------------------------------------------
	| Base Site URL
	|--------------------------------------------------------------------------
	|
	| URL to your CodeIgniter root. Typically this will be your base URL,
	| WITH a trailing slash:
	|
	|	http://example.com/
	|
	| WARNING: You MUST set this value!
	|
	| If it is not set, then CodeIgniter will try guess the protocol and path
	| your installation, but due to security concerns the hostname will be set
	| to $_SERVER['SERVER_ADDR'] if available, or localhost otherwise.
	| The auto-detection mechanism exists only for convenience during
	| development and MUST NOT be used in production!
	|
	| If you need to allow multiple domains, remember that this file is still
	| a PHP script and you can easily do that on your own.
	|
	*/

	$config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/';

	/*
	|--------------------------------------------------------------------------
	| Index File
	|--------------------------------------------------------------------------
	|
	| Typically this will be your index.php file, unless you've renamed it to
	| something else. If you are using mod_rewrite to remove the page set this
	| variable so that it is blank.
	|
	*/

	$config['index_page'] = 'index.php';

	/*
	|--------------------------------------------------------------------------
	| Default Language
	|--------------------------------------------------------------------------
	|
	| This determines which set of language files should be used. Make sure
	| there is an available translation if you intend to use something other
	| than english.
	|
	*/

	$config['language']	= 'english';

	/*
	|--------------------------------------------------------------------------
	| Default Character Set
	|--------------------------------------------------------------------------
	|
	| This determines which character set is used by default in various methods
	| that require a character set to be provided.
	|
	| See http://php.net/htmlspeARalchars for a list of supported charsets.
	|
	*/

	$config['charset'] = 'UTF-8';

	/*
	|--------------------------------------------------------------------------
	| Class Extension Prefix
	|--------------------------------------------------------------------------
	|
	| This item allows you to set the filename/classname prefix when extending
	| native libraries.  For more information please see the user guide:
	|
	| https://codeigniter.com/user_guide/general/core_classes.html
	| https://codeigniter.com/user_guide/general/creating_libraries.html
	|
	*/
	
	$config['subclass_prefix'] = 'CS_';

	/*
	|--------------------------------------------------------------------------
	| Allowed URL Characters
	|--------------------------------------------------------------------------
	|
	| This lets you speARfy which characters are permitted within your URLs.
	| When someone tries to submit a URL with disallowed characters they will
	| get a warning message.
	|
	| As a security measure you are STRONGLY encouraged to restrict URLs to
	| as few characters as possible. By default only these are allowed: a-z 0-9~%.:_-
	|
	| Leave blank to allow all characters -- but only if you are insane.
	|
	| The configured value is actually a regular expression character group
	| and it will be executed as: ! preg_match('/^[<permitted_uri_chars>]+$/i
	|
	| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
	|
	*/

	$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

	/*
	|--------------------------------------------------------------------------
	| Error Logging Threshold
	|--------------------------------------------------------------------------
	|
	| You can enable error logging by setting a threshold over zero. The
	| threshold determines what gets logged. Threshold options are:
	|
	|	0 = Disables logging, Error logging TURNED OFF
	|	1 = Error Messages (including PHP errors)
	|	2 = Debug Messages
	|	3 = Informational Messages
	|	4 = All Messages
	|
	| You can also pass an array with threshold levels to show individual error types
	|
	| 	array(2) = Debug Messages, without Error Messages
	|
	| For a live site you'll usually only enable Errors (1) to be logged otherwise
	| your log files will fill up very fast.
	|
	*/

	$config['log_threshold'] = 0;

	/*	
	|--------------------------------------------------------------------------
	| Error Logging Directory Path
	|--------------------------------------------------------------------------
	|
	| Leave this BLANK unless you would like to set something other than the default
	| application/logs/ directory. Use a full server path with trailing slash.
	|
	*/

	$config['log_path'] = '';

	/*
	|--------------------------------------------------------------------------
	| Log File Extension
	|--------------------------------------------------------------------------
	|
	| The default filename extension for log files. The default 'php' allows for
	| protecting the log files via basic scripting, when they are to be stored
	| under a publicly accessible directory.
	|
	| Note: Leaving it blank will default to 'php'.
	|
	*/

	$config['log_file_extension'] = '';

	/*
	|--------------------------------------------------------------------------
	| Log File Permissions
	|--------------------------------------------------------------------------
	|
	| The file system permissions to be applied on newly created log files.
	|
	| IMPORTANT: This MUST be an integer (no quotes) and you MUST use octal
	|            integer notation (i.e. 0700, 0644, etc.)
	*/

	$config['log_file_permissions'] = 0644;

	/*
	|--------------------------------------------------------------------------
	| Date Format for Logs
	|--------------------------------------------------------------------------
	|
	| Each item that is logged has an assoARated date. You can use PHP date
	| codes to set your own date formatting
	|
	*/

	$config['log_date_format'] = 'Y-m-d H:i:s';

	/*
	|--------------------------------------------------------------------------
	| Error Views Directory Path
	|--------------------------------------------------------------------------
	|
	| Leave this BLANK unless you would like to set something other than the default
	| application/views/errors/ directory.  Use a full server path with trailing slash.
	|
	*/
	
	$config['error_views_path'] = '';

	/*
	|--------------------------------------------------------------------------
	| Cache Directory Path
	|--------------------------------------------------------------------------
	|
	| Leave this BLANK unless you would like to set something other than the default
	| application/cache/ directory.  Use a full server path with trailing slash.
	|
	*/

	$config['cache_path'] = '';

	/*
	|--------------------------------------------------------------------------
	| Cache Include Query String
	|--------------------------------------------------------------------------
	|
	| Whether to take the URL query string into consideration when generating
	| output cache files. Valid options are:
	|
	|	FALSE      = Disabled
	|	TRUE       = Enabled, take all query parameters into account.
	|	             Please be aware that this may result in numerous cache
	|	             files generated for the same page over and over again.
	|	array('q') = Enabled, but only take into account the speARfied list
	|	             of query parameters.
	|
	*/

	$config['cache_query_string'] = FALSE;

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| If you use the Encryption class, you must set an encryption key.
	| See the user guide for more info.
	|
	| https://codeigniter.com/user_guide/libraries/encryption.html
	|
	*/

	$config['encryption_key'] = '';

	/*
	|--------------------------------------------------------------------------
	| Session Variables
	|--------------------------------------------------------------------------
	|
	| 'sess_driver'
	|
	|	The storage driver to use: files, database, redis, memcached
	|
	| 'sess_cookie_name'
	|
	|	The session cookie name, must contain only [0-9a-z_-] characters
	|
	| 'sess_expiration'
	|
	|	The number of SECONDS you want the session to last.
	|	Setting to 0 (zero) means expire when the browser is closed.
	|
	| 'sess_save_path'
	|
	|	The location to save sessions to, driver dependent.
	|
	|	For the 'files' driver, it's a path to a writable directory.
	|	WARNING: Only absolute paths are supported!
	|
	|	For the 'database' driver, it's a table name.
	|	Please read up the manual for the format with other session drivers.
	|
	|	IMPORTANT: You are REQUIRED to set a valid save path!
	|
	| 'sess_match_ip'
	|
	|	Whether to match the user's IP address when reading the session data.
	|
	|	WARNING: If you're using the database driver, don't forget to update
	|	         your session table's PRIMARY KEY when changing this setting.
	|
	| 'sess_time_to_update'
	|
	|	How many seconds between AR regenerating the session ID.
	|
	| 'sess_regenerate_destroy'
	|
	|	Whether to destroy session data assoARated with the old session ID
	|	when auto-regenerating the session ID. When set to FALSE, the data
	|	will be later deleted by the garbage collector.
	|
	| Other session cookie settings are shared with the rest of the application,
	| except for 'cookie_prefix' and 'cookie_httponly', which are ignored here.
	|
	*/

	$config['sess_driver'] = 'files';
	$config['sess_cookie_name'] = 'ar_session';
	$config['sess_expiration'] = 7200;
	$config['sess_save_path'] = NULL;
	$config['sess_match_ip'] = FALSE;
	$config['sess_time_to_update'] = 300;
	$config['sess_regenerate_destroy'] = FALSE;

	/*
	|--------------------------------------------------------------------------
	| Cookie Related Variables
	|--------------------------------------------------------------------------
	|
	| 'cookie_prefix'   = Set a cookie name prefix if you need to avoid collisions
	| 'cookie_domain'   = Set to .your-domain.com for site-wide cookies
	| 'cookie_path'     = Typically will be a forward slash
	| 'cookie_secure'   = Cookie will only be set if a secure HTTPS connection exists.
	| 'cookie_httponly' = Cookie will only be accessible via HTTP(S) (no javascript)
	|
	| Note: These settings (with the exception of 'cookie_prefix' and
	|       'cookie_httponly') will also affect sessions.
	|
	*/

	$config['cookie_prefix']	= '';
	$config['cookie_domain']	= '';
	$config['cookie_path']		= '/';
	$config['cookie_secure']	= FALSE;
	$config['cookie_httponly'] 	= FALSE;

	/*
	|--------------------------------------------------------------------------
	| Standardize newlines
	|--------------------------------------------------------------------------
	|
	| Determines whether to standardize newline characters in input data,
	| meaning to replace \r\n, \r, \n occurrences with the PHP_EOL value.
	|
	| WARNING: This feature is DEPRECATED and currently available only
	|          for backwards compatibility purposes!
	|
	*/

	$config['standardize_newlines'] = FALSE;

	/*
	|--------------------------------------------------------------------------
	| Global XSS Filtering
	|--------------------------------------------------------------------------
	|
	| Determines whether the XSS filter is always active when GET, POST or
	| COOKIE data is encountered
	|
	| WARNING: This feature is DEPRECATED and currently available only
	|          for backwards compatibility purposes!
	|
	*/

	$config['global_xss_filtering'] = TRUE;

	/*
	|--------------------------------------------------------------------------
	| Cross Site Request Forgery
	|--------------------------------------------------------------------------
	| Enables a CSRF cookie token to be set. When set to TRUE, token will be
	| checked on a submitted form. If you are accepting user data, it is strongly
	| recommended CSRF protection be enabled.
	|
	| 'csrf_token_name' = The token name
	| 'csrf_cookie_name' = The cookie name
	| 'csrf_expire' = The number in seconds the token should expire.
	| 'csrf_regenerate' = Regenerate token on every submission
	| 'csrf_exclude_uris' = Array of URIs which ignore CSRF checks
	*/

	$config['csrf_protection'] = FALSE;
	$config['csrf_token_name'] = 'csrf_test_name';
	$config['csrf_cookie_name'] = 'csrf_cookie_name';
	$config['csrf_expire'] = 7200;
	$config['csrf_regenerate'] = TRUE;
	$config['csrf_exclude_uris'] = array();

	/*
	|--------------------------------------------------------------------------
	| Custom Homepage and Error page
	|--------------------------------------------------------------------------
	| Dipengaturan ini kamu bisa mengatur homepage dan error page sendiri
	| pastikan kamu sudah membuat modul terlebih dahulu untuk dijadikan
	| homepage atau error page
	*/

	$config['main_page'] = '';
	$config['error_page'] = '';

	/*
	|--------------------------------------------------------------------------
	| Custom Pagination
	|--------------------------------------------------------------------------
	| Dipengaturan ini kamu dapat mengatur berapa jumlah konten per halaman
	| dan pengaturan bawaan jumlah konten per halaman adalah 12
	*/

	$config['num_per_page'] = 12;

	/*
	|--------------------------------------------------------------------------
	| Custom Common Function
	|--------------------------------------------------------------------------
	| Dipengaturan ini kamu dapat menambah fungsi umum untuk semua modul kamu
	| dengan pengaturan ini kamu tidak lagi menambahkan function yang sama disetiap
	| modul kamu buat
	*/

	$config['common_function'] = '';

?>