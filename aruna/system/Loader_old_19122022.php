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
	 * List of paths to load models from
	 *
	 * @var	array
	 */

	protected $_ar_model_paths = array(MODELPATH);

	/**
	 * List of paths to load extensions from
	 *
	 * @var	array
	 */

	protected $extension_paths = array(APPPATH, BASEPATH);

	/**
	 * List of loaded models
	 *
	 * @var	array
	 */

	protected $_ar_models =	array();

	/**
	 * List of loaded classes
	 *
	 * @var	array
	 */

	protected $_classes = array();

	/**
	 * List of loaded classes
	 *
	 * @var	array
	 */

	protected $_ar_classes = array();

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
		$this->_ar_classes =& is_loaded();

		log_message('info', 'Loader Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initializer
	 *
	 * @todo Figure out a way to move this to the constructor
	 * without breaking *package_path*() methods.
	 * 
	 * @uses CI_Loader::_ci_autoloader()
	 * 
	 * @used-by	CI_Controller::__construct()
	 * @return void
	 */
	
	public function initialize()
	{
		$this->autoloader();
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
	 * Model Loader
	 *
	 * Loads and instantiates models.
	 *
	 * @param	mixed	$model		Model name
	 * @param	string	$name		An optional object name to assign to
	 * @param	bool	$db_conn	An optional database connection configuration to initialize
	 * @return	object
	 */

	public function model($model, $name = '', $db_conn = FALSE)
	{
		if (empty($model))
		{
			return $this;
		}
		elseif (is_array($model))
		{
			foreach ($model as $key => $value)
			{
				is_int($key) ? $this->model($value, '', $db_conn) : $this->model($key, $value, $db_conn);
			}

			return $this;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($model, 0, ++$last_slash);

			// And the model name behind it
			$model = substr($model, $last_slash);
		}

		if (empty($name))
		{
			$name = $model;
		}

		// Note: All of the code under this condition used to be just:
		//
		//       load_class('Model', 'core');
		//
		//       However, load_class() instantiates classes
		//       to cache them for later use and that prevents
		//       MY_Model from being an abstract class and is
		//       sub-optimal otherwise anyway.
		if ( ! class_exists('Aruna_Model', FALSE))
		{
			$app_path = BASEPATH.'system'.DIRECTORY_SEPARATOR;

			if (file_exists($app_path.'Model.php'))
			{
				require_once($app_path.'Model.php');

				if ( ! class_exists('Aruna_Model', FALSE))
				{
					throw new RuntimeException($app_path."Model.php exists, but doesn't declare class Aruna_Model");
				}

				log_message('info', 'Aruna_Model class loaded');
			}
			elseif ( ! class_exists('Aruna_Model', FALSE))
			{
				require_once(BASEPATH.'system'.DIRECTORY_SEPARATOR.'Model.php');
			}

			$class = config_item('subclass_prefix').'Model';

			if (file_exists($app_path.$class.'.php'))
			{
				require_once($app_path.$class.'.php');

				if ( ! class_exists($class, FALSE))
				{
					throw new RuntimeException($app_path.$class.".php exists, but doesn't declare class ".$class);
				}

				log_message('info', config_item('subclass_prefix').'Model class loaded');
			}
		}

		$model = ucfirst($model);

		if ( ! class_exists($model, FALSE))
		{
			foreach ($this->_ar_model_paths as $mod_path)
			{
				if ( ! file_exists($mod_path.$path.$model.'.php'))
				{
					continue;
				}

				require_once($mod_path.$path.$model.'.php');

				if ( ! class_exists($model, FALSE))
				{
					throw new RuntimeException($mod_path.$path.$model.".php exists, but doesn't declare class ".$model);
				}

				break;
			}

			if ( ! class_exists($model, FALSE))
			{
				throw new RuntimeException('Unable to locate the model you have specified: '.$model);
			}
		}
		elseif ( ! is_subclass_of($model, 'Aruna_Model'))
		{
			throw new RuntimeException("Class ".$model." already exists and doesn't extend Aruna_Model");
		}

		$model = new $model();
		log_message('info', 'Model "'.get_class($model).'" initialized');
		return $model;
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

		$output = load_lib('output');

		/*
		 * Extract variables
		 *
		 * You can either set variables using the dedicated $this->load->vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */

		extract($_ar_vars);

		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be post-processed by
		 *	the output class. Why do we need post processing? For one thing,
		 *	in order to show the elapsed page load time. Unless we can
		 *	intercept the content right before it's sent to the browser and
		 *	then stop the timer it won't be accurate.
		 */

		ob_start();

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

		$GLOBALS['view_content'] ??= '';

		// Return the file data if requested
		if ($_ar_return === TRUE)
		{
			$buffer = ob_get_contents();
			@ob_end_clean();

			$GLOBALS['view_content'] .= $buffer;
			
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 */

		if (ob_get_level() > $this->_ar_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{			
			$GLOBALS['view_content'] .= ob_get_contents();
			@ob_end_clean();
		}

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
	 * Aruna Autoloader
	 *
	 * Loads component listed in the config/autoload.php file.
	 *
	 * @used-by	Aruna_Loader::initialize()
	 * @return	void
	 */

	protected function autoloader()
	{
		if (file_exists(APPPATH.'config/autoload.php'))
		{
			include(APPPATH.'config/autoload.php');
		}

		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/autoload.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/autoload.php');
		}

		if ( ! isset($autoload))
		{
			return;
		}

		// Autoload packages
		/*
		if (isset($autoload['packages']))
		{
			foreach ($autoload['packages'] as $package_path)
			{
				$this->add_package_path($package_path);
			}
		}
		*/

		// Load any custom config file
		/*
		if (count($autoload['config']) > 0)
		{
			foreach ($autoload['config'] as $val)
			{
				$this->config($val);
			}
		}
		*/

		// Autoload helpers and languages
		/*
		foreach (array('helper', 'language') as $type)
		{
			if (isset($autoload[$type]) && count($autoload[$type]) > 0)
			{
				$this->$type($autoload[$type]);
			}
		}
		*/

		// Autoload drivers
		/*
		if (isset($autoload['drivers']))
		{
			$this->driver($autoload['drivers']);
		}
		*/

		// Load libraries
		if (isset($autoload['libraries']) && count($autoload['libraries']) > 0)
		{
			// Load the database driver.
			/*
			if (in_array('database', $autoload['libraries']))
			{
				$this->database();
				$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
			}
			*/

			// Load all other libraries
			$this->library($autoload['libraries']);
		}

		// Autoload models
		/*
		if (isset($autoload['model']))
		{
			$this->model($autoload['model']);
		}
		*/
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

	// --------------------------------------------------------------------

	/**
	 * Is Loaded
	 *
	 * A utility method to test if a class is in the self::$_ci_classes array.
	 *
	 * @used-by	Mainly used by Form Helper function _get_validation_object().
	 *
	 * @param 	string		$class	Class name to check for
	 * @return 	string|bool	Class object name if loaded or FALSE
	 */

	public function is_loaded2($class)
	{
		return array_search(ucfirst($class), $this->_ar_classes, TRUE);
	}

}

?>