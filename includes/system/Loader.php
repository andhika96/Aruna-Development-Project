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
	 *	Information file: Loader Class
	 *	Description: Loads framework components.
	 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ARUNA_Loader {

	// All these are set automatically. Don't mess with them.
	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var	int
	 */

	protected $_ar_ob_level;

	/**
	 * List of cached variables
	 *
	 * @var	array
	 */
	protected $_ar_cached_vars = array();

	/**
	 * List of paths to load views from
	 *
	 * @var	array
	 */

	protected $_ar_view_paths =	array(VIEWPATH => TRUE);

	/**
	 * List of paths to load libraries from
	 *
	 * @var	array
	 */

	protected $library_paths = array(APPPATH, BASEPATH);

	/**
	 * List of paths to load extensions from
	 *
	 * @var	array
	 */

	protected $extension_paths = array(APPPATH, BASEPATH);

	/**
	 * List of loaded classes
	 *
	 * @var	array
	 */

	protected $_classes = array();

	/**
	 * is_loaded
	 *
	 * @var	array
	 */

	protected $_is_loaded = array();

	/**
	 * List of loaded extensions
	 *
	 * @var	array
	 */

	protected $_extensions = array();

	protected $ar_class_name;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Sets component load paths, gets the initial output buffering level.
	 *
	 * @return	void
	 */

	public function __construct()
	{
		$this->_ar_ob_level = ob_get_level();

		log_message('info', 'Loader Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Library Loader
	 *
	 * Loads and instantiates libraries.
	 * Designed to be called from application controllers.
	 *
	 * @param	mixed	$library	Library name
	 * @param	array	$params		Optional parameters to pass to the library class constructor
	 * @return	object
	 */

	public function library($library, $params = NULL)
	{
		if (empty($library))
		{
			log_message('error', 'You haven\'t entered the library');
			show_error('You haven\'t entered the library');
		}
		elseif (is_array($library))
		{
			log_message('error', 'Array does not support');
			show_error('Array does not support');
		}

		if ($params !== NULL && ! is_array($params))
		{
			$params = NULL;
		}

		return $this->load_library($library, $params);
	}

	// --------------------------------------------------------------------

	/**
	 * Database Loader
	 *
	 * @param	mixed	$params		Database configuration options
	 * @param	bool	$return 	Whether to return the database object
	 * @param	bool	$query_builder	Whether to enable Query Builder
	 *					(overrides the configuration setting)
	 *
	 * @return	object|bool	Database object if $return is set to TRUE,
	 *					FALSE on failure, CI_Loader instance in any other case
	 */

	public function database($active_group = '', $db_type = '')
	{
		require_once BASEPATH.'libraries/Database.php';

		$database[$db_type] = new ARUNA_Database($active_group);

		return $database[$db_type];
	}

	
	// --------------------------------------------------------------------

	/**
	 * Extension Loader
	 *
	 * @param	string|string[]	$extensions	Extension name(s)
	 * @return	object
	 */

	public function extension($extensions = array())
	{
		is_array($extensions) OR $extensions = array($extensions);
		foreach ($extensions as &$extension) 
		{
			$filename 	= basename($extension);
			$filepath 	= ($filename === $extension) ? '' : substr($extension, 0, strlen($extension) - strlen($filename));
			$filename 	= strtolower(preg_replace('#(_extension)?(\.php)?$#i', '', $filename)).'_extension';
			$extension  = $filepath.$filename;

			if (isset($this->_extensions[$extension]))
			{
				continue;
			}

			// Is this a extension extension request?
			$ext_extension = config_item('subclass_prefix').$filename;
			$ext_loaded = FALSE;
			foreach ($this->extension_paths as $path)
			{
				if (file_exists($path.'extension/'.$ext_extension.'.php'))
				{
					include_once($path.'extension/'.$ext_extension.'.php');
					$ext_loaded = TRUE;
				}
			}

			// If we have loaded extensions - check if the base one is here
			if ($ext_loaded === TRUE)
			{
				$base_extension = BASEPATH.'extension/'.$extension.'.php';
				if ( ! file_exists($base_extension))
				{
					show_error('Unable to load the requested file: extension/'.$extension.'.php');
				}

				include_once($base_extension);
				$this->_extensions[$extension] = TRUE;
				log_message('info', 'Extension loaded: '.$extension);
				continue;
			}

			// No extensions found ... try loading regular extensions and/or overrides
			foreach ($this->extension_paths as $path)
			{
				if (file_exists($path.'extension/'.$extension.'.php'))
				{
					include_once($path.'extension/'.$extension.'.php');

					$this->_extensions[$extension] = TRUE;
					log_message('info', 'Extension loaded: '.$extension);
					break;
				}
			}

			// unable to load the extension
			if ( ! isset($this->_extensions[$extension]))
			{
				show_error('Unable to load the requested file: extension/'.$extension.'.php');
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * View Loader
	 *
	 * Loads "view" files.
	 *
	 * @param	string	$view	View name
	 * @param	array	$vars	An associative array of data
	 *				to be extracted for use in the view
	 * @param	bool	$return	Whether to return the view output
	 *				or leave it to the Output class
	 * @return	object|string
	 */
	
	public function load_view($view, $vars = array(), $return = FALSE)
	{
		return $this->_ar_load(array('_ar_view' => $view, '_ar_vars' => $this->_ar_prepare_view_vars($vars), '_ar_return' => $return));
	}

	// --------------------------------------------------------------------

	/**
	 * Internal AR Data Loader
	 *
	 * Used to load views and files.
	 *
	 * Variables are prefixed with _ar_ to avoid symbol collision with
	 * variables made available to view files.
	 *
	 * @used-by	AR_Loader::load_view()
	 * @param	array	$_ar_data	Data to load
	 * @return	object
	 */

	protected function _ar_load($_ar_data)
	{
		// Set the default data variables
		foreach (array('_ar_view', '_ar_vars', '_ar_path', '_ar_return') as $_ar_val)
		{
			$$_ar_val = isset($_ar_data[$_ar_val]) ? $_ar_data[$_ar_val] : FALSE;
		}

		$file_exists = FALSE;

		// Set the path to the requested file
		if (is_string($_ar_path) && $_ar_path !== '')
		{
			$_ar_x = explode('/', $_ar_path);
			$_ar_file = end($_ar_x);
		}
		else
		{
			$_ar_ext = pathinfo($_ar_view, PATHINFO_EXTENSION);
			$_ar_file = ($_ar_ext === '') ? $_ar_view.'.php' : $_ar_view;

			foreach ($this->_ar_view_paths as $_ar_view_file => $cascade)
			{
				if (file_exists($_ar_view_file.$_ar_file))
				{
					$_ar_path = $_ar_view_file.$_ar_file;
					$file_exists = TRUE;
					break;
				}

				if ( ! $cascade)
				{
					break;
				}
			}
		}

		if ( ! $file_exists && ! file_exists($_ar_path))
		{
			show_error('Unable to load the requested file: '.$_ar_file);
		}

		/*
		 * Extract variables
		 *
		 * You can either set variables using the dedicated $this->load->vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */

		extract($_ar_vars);

		// Return the file data if requested
		if ($_ar_return === TRUE)
		{
			return $this;
		}

		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		if ( ! is_php('5.4') && ! ini_get('short_open_tag') && config_item('rewrite_short_tags') === TRUE)
		{
			echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($_ar_path))));
		}
		else
		{
			include($_ar_path); // include() vs include_once() allows for multiple views with the same name
		}

		log_message('info', 'File loaded: '.$_ar_path);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Prepare variables for _ar_vars, to be later extract()-ed inside views
	 *
	 * Converts objects to associative arrays and filters-out internal
	 * variable names (i.e. keys prefixed with '_ar_').
	 *
	 * @param	mixed	$vars
	 * @return	array
	 */
	
	public function _ar_prepare_view_vars($vars)
	{
		if ( ! is_array($vars))
		{
			$vars = is_object($vars)
				? get_object_vars($vars)
				: array();
		}

		foreach (array_keys($vars) as $key)
		{
			if (strncmp($key, '_ar_', 4) === 0)
			{
				unset($vars[$key]);
			}
		}

		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Extend View
	 *
	 *
	 * @param	string $theme 		name of the theme folder
	 * @param	string $section 	theme file section
	 * @return 	void
	 */

	public function init_extend_view($theme, $section)
	{
		if (is_array($section))
		{
			$status_extend_view_header = ($section[0] == 'header' || strrpos($section[0], 'header_') !== FALSE) ? TRUE : FALSE;
			$status_extend_view_footer = ($section[1] == 'footer' || strrpos($section[1], 'footer_') !== FALSE) ? TRUE : FALSE;
		}
		else
		{
			$status_extend_view_header = ($section == 'header' || strrpos($section, 'header_') !== FALSE) ? TRUE : FALSE;
			$status_extend_view_footer = ($section == 'footer' || strrpos($section, 'footer_') !== FALSE) ? TRUE : FALSE;
		}

		if ($status_extend_view_header === TRUE)
		{
			// Send status to global variable status_extend_view_header
			$GLOBALS['status_extend_view_header'] = 1;
		}

		if ($status_extend_view_footer === TRUE)
		{
			// Send status to global variable status_extend_view_footer
			$GLOBALS['status_extend_view_footer'] = 1;
		}

		if (empty($theme))
		{
			show_error('Empty theme');
			log_message('error', 'Empty theme');
		}

		if ( ! is_dir('./themes/'.$theme))
		{
			show_error('Unable to load the requested file: '.$theme);
			log_message('error', 'Unable to load the requested file: '.$theme);
		}

		if (is_array($section))
		{
			foreach ($section as $key => $value) 
			{
				if (empty($value))
				{
					show_error('Empty section');
					log_message('error', 'Empty section');
				}

				if ( ! file_exists('./themes/'.$theme.'/extends/'.$value.'.php'))
				{
					show_error('Unable to load the requested file: '.$value);
					log_message('error', 'Unable to load the requested file: '.$value);
				}

				log_message('info', 'Section theme loaded: '.$value.'.php');
				include './themes/'.$theme.'/extends/'.$value.'.php';
			}
		}
		else
		{
			if (empty($section))
			{
				show_error('Empty section');
				log_message('error', 'Empty section');
			}

			if ( ! file_exists('./themes/'.$theme.'/extends/'.$section.'.php'))
			{
				show_error('Unable to load the requested file: '.$section);
				log_message('error', 'Unable to load the requested file: '.$section);
			}

			log_message('info', 'Section theme loaded: '.$section.'.php');
			include './themes/'.$theme.'/extends/'.$section.'.php';			
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Internal ARUNA Library Loader
	 *
	 * @used-by	ARUNA_Loader::library()
	 * @uses	ARUNA_Loader::init_library()
	 *
	 * @param	string	$class		Class name to load
	 * @param	mixed	$params		Optional parameters to pass to the class constructor
	 * @return	void
	 */

	protected function load_library($class, $params = NULL)
	{
		// Get the class name, and while we're at it trim any slashes.
		// The directory path can be included as part of the class name,
		// but we don't want a leading slash
		$class = str_replace('.php', '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			// Extract the path
			$subdir = substr($class, 0, ++$last_slash);

			// Get the filename from the path
			$class = substr($class, $last_slash);
		}
		else
		{
			$subdir = '';
		}

		$class = ucfirst($class);

		$new_prefix = [
			'ARUNA_',
			config_item('subclass_prefix')
		];
		
		// Is this a stock library? There are a few special conditions if so ...
		if (file_exists(BASEPATH.'libraries/'.$subdir.$class.'.php'))
		{
			return $this->load_stock_library($class, $subdir, $params);
		}

		// Safety: Was the class already loaded by a previous call?
		if (class_exists($class, FALSE))
		{	
			return $this->init_library($class, $new_prefix, $params);
		}

		// Let's search for the requested library file and load it.
		foreach ($this->library_paths as $path)
		{
			// BASEPATH has already been checked for
			if ($path === BASEPATH)
			{
				continue;
			}

			$filepath = $path.'libraries/'.$subdir.$class.'.php';

			// Does the file exist? No? Bummer...
			if ( ! file_exists($filepath))
			{
				continue;
			}

			require_once($filepath);
			return $this->init_library($class, $new_prefix, $params);
		}

		// One last attempt. Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir === '')
		{
			return $this->load_library($class.'/'.$class, $params);
		}

		// If we got this far we were unable to find the requested class.
		log_message('error', 'Unable to load the requested class: '.$class);
		show_error('Unable to load the requested class: '.$class);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Internal ARUNA Stock Library Loader
	 *
	 * @used-by	ARUNA_Loader::load_library()
	 * @uses	ARUNA_Loader::init_library()
	 *
	 * @param	string	$library_name	Library name to load
	 * @param	string	$file_path	Path to the library filename, relative to libraries/
	 * @param	mixed	$params		Optional parameters to pass to the class constructor
	 * @return	void
	 */

	protected function load_stock_library($library_name, $file_path, $params)
	{
		// $prefix = 'ARUNA_';
		$prefix_array = ['ARUNA_'];

		$paths = $this->library_paths;
		array_pop($paths); // BASEPATH
		array_pop($paths); // APPPATH (needs to be the first path checked)
		array_unshift($paths, APPPATH);

		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'libraries/'.$file_path.$library_name.'.php'))
			{
				// Override
				include_once($path);
				if (class_exists($prefix_array[0].$library_name, FALSE))
				{
					return $this->init_library($library_name, $prefix_array, $params);
				}

				log_message('debug', $path.' exists, but does not declare '.$prefix_array[0].$library_name);
			}
		}

		include_once(BASEPATH.'libraries/'.$file_path.$library_name.'.php');

		return $this->init_library($library_name, $prefix_array, $params);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal ARUNA Library Instantiator
	 *
	 * @used-by	ARUNA_Loader::load_stock_library()
	 * @used-by	ARUNA_Loader::load_library()
	 *
	 * @param	string			$class		Class name
	 * @param	array|null|bool	$prefix		Class name prefix
	 * @param	array|null|bool	$config		Optional configuration to pass to the class constructor:
	 *						FALSE to skip;
	 *						NULL to search in config paths;
	 *						array containing configuration data
	 * @return	void
	 */

	protected function init_library($class, $prefixs, $config = FALSE)
	{
		// Does the class exist? If so, we're done...
		if (isset($this->_classes[$class]))
		{
			return $this->_classes[$class];
		}

		if (is_array($prefixs))
		{
			foreach ($prefixs as $prefix) 
			{
				$prefix_name = $prefix;
			}
		}

		$class_name = $prefix_name.$class;

		// Is the class name valid?
		if ( ! class_exists($class_name, FALSE))
		{
			log_message('error', 'Non-existent class: '.$class_name);
			show_error('Non-existent class: '.$class_name);
		}

		// Keep track of what we just loaded
		$this->is_loaded($class);

		$this->_classes[$class] = isset($config)
			? new $class_name($config)
			: new $class_name();
		return $this->_classes[$class];
	}

	// --------------------------------------------------------------------

	/**
	 * Keeps track of which libraries have been loaded. This function is
	 * called by the init_library() function above
	 *
	 * @param	string
	 * @return	array
	 */

	protected function is_loaded($class = '')
	{
		if ($class !== '')
		{
			$this->_is_loaded[strtolower($class)] = $class;
		}

		return $this->_is_loaded;
	}

}

?>