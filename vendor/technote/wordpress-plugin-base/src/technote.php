<?php
/**
 * Technote
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0 Added: Feature to load library of latest version
 * @since 2.0.2 Fixed: Uninstall behavior
 * @since 2.1.0 Added: app_initialize action
 * @since 2.1.0 Added: argument to actions (app_initialized, app_activated, app_deactivated)
 * @since 2.1.0 Fixed: initialize process
 * @since 2.1.0 Changed: load textdomain from plugin data
 * @since 2.1.0 Added: check develop version
 * @since 2.1.0 Changed: set default value of check_update when the plugin is registered as official
 * @since 2.1.1 Fixed: check develop version
 * @since 2.3.0 Changed: property access exception type
 * @since 2.3.0 Added: get_plugin_version method
 * @since 2.3.1 Changed: not load test and uninstall if not required
 * @since 2.4.0 Added: upgrade feature
 * @since 2.4.1 Added: show plugin upgrade notices feature
 * @since 2.6.0 Changed: move setup_update method to upgrade
 * @since 2.7.0 Changed: log
 * @since 2.7.3 Fixed: suppress error when activate plugin
 * @since 2.7.4 Fixed: suppress error when uninstall plugin
 * @since 2.8.0 Added: social login, custom post
 * @since 2.8.1 Added: setup social login, custom post filters
 * @since 2.8.5 Added: capture fatal error
 * @since 2.9.0 Added: mail
 * @since 2.9.0 Improved: log
 * @since 2.9.12 Improved: shutdown log
 * @since 2.9.13 Changed: log settings
 * @since 2.9.13 Changed: moved shutdown function to log
 * @since 2.10.0 Changed: moved main program to lib/main
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
define( 'TECHNOTE_IS_MOCK', false );

/**
 * Class Technote
 * @property string $original_plugin_name
 * @property string $plugin_name
 * @property string $slug_name
 * @property string $plugin_file
 * @property \Technote\Classes\Models\Lib\Define $define
 * @property \Technote\Classes\Models\Lib\Config $config
 * @property \Technote\Classes\Models\Lib\Setting $setting
 * @property \Technote\Classes\Models\Lib\Option $option
 * @property \Technote\Classes\Models\Lib\Device $device
 * @property \Technote\Classes\Models\Lib\Minify $minify
 * @property \Technote\Classes\Models\Lib\Filter $filter
 * @property \Technote\Classes\Models\Lib\User $user
 * @property \Technote\Classes\Models\Lib\Post $post
 * @property \Technote\Classes\Models\Lib\Loader $loader
 * @property \Technote\Classes\Models\Lib\Log $log
 * @property \Technote\Classes\Models\Lib\Input $input
 * @property \Technote\Classes\Models\Lib\Db $db
 * @property \Technote\Classes\Models\Lib\Uninstall $uninstall
 * @property \Technote\Classes\Models\Lib\Session $session
 * @property \Technote\Classes\Models\Lib\Utility $utility
 * @property \Technote\Classes\Models\Lib\Test $test
 * @property \Technote\Classes\Models\Lib\Upgrade $upgrade
 * @property \Technote\Classes\Models\Lib\Social $social
 * @property \Technote\Classes\Models\Lib\Custom_Post $custom_post
 * @property \Technote\Classes\Models\Lib\Mail $mail
 * @method void main_init()
 * @method bool has_initialized()
 * @method string get_plugin_version()
 * @method string|false get_text_domain()
 * @method string translate( string $value )
 * @method mixed get_config( string $name, string $key, mixed $default = null )
 * @method mixed get_option( string $key, mixed $default = '' )
 * @method mixed get_session( string $key, mixed $default = '' )
 * @method mixed set_session( string $key, mixed $value, int | null $duration = null )
 * @method bool user_can( null | string | false $capability = null )
 * @method void log( string $message, mixed $context = null, string $level = '' )
 * @method void add_message( string $message, string $group = '', bool $error = false, bool $escape = true )
 * @method string get_page_slug( string $file )
 * @method mixed get_shared_object( string $key, string | null $target = null )
 * @method void set_shared_object( string $key, mixed $object, string | null $target = null )
 * @method bool isset_shared_object( string $key, string | null $target = null )
 * @method void delete_shared_object( string $key, string | null $target = null )
 * @method array|string get_plugin_data( string | null $key = null )
 */
class Technote {

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array|\Technote[] $_instances
	 */
	private static $_instances = [];

	/**
	 * @since 2.0.0
	 * @since 2.10.0 Changed: trivial change
	 * @var string $_latest_library_version
	 */
	private static $_latest_library_version = null;

	/**
	 * @since 2.0.0
	 * @since 2.10.0 Changed: trivial change
	 * @var string $_latest_library_directory
	 */
	private static $_latest_library_directory = null;

	/**
	 * @since 2.0.0
	 * @since 2.10.0 Changed: trivial change
	 * @var string $_library_version
	 */
	private $_library_version;

	/**
	 * @since 2.0.0
	 * @since 2.10.0 Changed: trivial change
	 * @var string $_library_directory
	 */
	private $_library_directory;

	/**
	 * @since 2.0.0
	 * @since 2.10.0 Changed: trivial change
	 * @var bool $_plugins_loaded
	 */
	private $_plugins_loaded = false;

	/**
	 * @since 2.10.0
	 * @var \Technote\Classes\Models\Lib\Main $_main
	 */
	private $_main;

	/**
	 * @since 2.10.0
	 * @var bool $_is_uninstall
	 */
	private $_is_uninstall = false;

	/** @var string $original_plugin_name */
	public $original_plugin_name;

	/** @var string $plugin_name */
	public $plugin_name;

	/** @var string $plugin_file */
	public $plugin_file;

	/** @var string $slug_name */
	public $slug_name;

	/**
	 * Technote constructor.
	 *
	 * @param string $plugin_name
	 * @param string $plugin_file
	 * @param string|null $slug_name
	 */
	private function __construct( $plugin_name, $plugin_file, $slug_name ) {
		$this->original_plugin_name = $plugin_name;
		$this->plugin_file          = $plugin_file;
		$this->plugin_name          = strtolower( $this->original_plugin_name );
		$this->slug_name            = ! empty( $slug_name ) ? strtolower( $slug_name ) : $this->plugin_name;

		$this->setup_library_version();
		$this->setup_actions();
	}

	/**
	 * @param string $name
	 *
	 * @return \Technote\Interfaces\Singleton
	 * @throws \OutOfRangeException
	 */
	public function __get( $name ) {
		return $this->get_main()->__get( $name );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return $this->get_main()->__isset( $name );
	}

	/**
	 * @since 2.10.0
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		return $this->get_main()->$name( ...$arguments );
	}

	/**
	 * @param $name
	 * @param $arguments
	 */
	public static function __callStatic( $name, $arguments ) {
		if ( preg_match( '#register_uninstall_(.+)\z#', $name, $matches ) ) {
			$plugin_base_name = $matches[1];
			self::uninstall( $plugin_base_name );
		}
	}

	/**
	 * @since 2.10.0
	 * @return \Technote\Classes\Models\Lib\Main|\Technote\Traits\Singleton
	 */
	private function get_main() {
		if ( ! isset( $this->_main ) ) {
			$required = [
				'Interfaces\Readonly',
				'Interfaces\Singleton',
				'Traits\Readonly',
				'Traits\Singleton',
				'Classes\Models\Lib\Main',
			];
			$dir      = self::$_latest_library_directory . DS . 'src';
			foreach ( $required as $item ) {
				$path = $dir . DS . str_replace( '\\', DS, strtolower( $item ) ) . '.php';
				if ( is_readable( $path ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once $path;
				}
			}
			$this->_main = \Technote\Classes\Models\Lib\Main::get_instance( $this );
		}

		return $this->_main;
	}

	/**
	 * @param string $plugin_name
	 * @param string $plugin_file
	 * @param string|null $slug_name
	 *
	 * @return Technote
	 */
	public static function get_instance( $plugin_name, $plugin_file, $slug_name = null ) {
		if ( ! isset( self::$_instances[ $plugin_name ] ) ) {
			$instances                        = new static( $plugin_name, $plugin_file, $slug_name );
			self::$_instances[ $plugin_name ] = $instances;

			$latest  = self::$_latest_library_version;
			$version = $instances->_library_version;
			if ( ! isset( $latest ) || version_compare( $latest, $version, '<' ) ) {
				self::$_latest_library_version   = $version;
				self::$_latest_library_directory = $instances->_library_directory;
			}
		}

		return self::$_instances[ $plugin_name ];
	}

	/**
	 * @since 2.0.0
	 * setup library version
	 */
	private function setup_library_version() {
		$library_directory = dirname( $this->plugin_file ) . DS . 'vendor' . DS . 'technote' . DS . 'wordpress-plugin-base';
		$config_path       = $library_directory . DS . 'configs' . DS . 'config.php';

		if ( is_readable( $config_path ) ) {
			/** @noinspection PhpIncludeInspection */
			$config = include $config_path;
			if ( ! is_array( $config ) || empty( $config['library_version'] ) ) {
				$library_version = '0.0.0';
			} else {
				$library_version = $config['library_version'];
			}
		} else {
			$library_version   = '0.0.0';
			$library_directory = dirname( TECHNOTE_BOOTSTRAP );
		}
		$this->_library_version   = $library_version;
		$this->_library_directory = $library_directory;
	}

	/**
	 * setup actions
	 * @since 2.0.0
	 * @since 2.7.3 Fixed: suppress error when activate plugin
	 */
	private function setup_actions() {
		add_action( 'plugins_loaded', function () {
			$this->plugins_loaded();
		} );

		add_action( 'init', function () {
			$this->main_init();
		}, 1 );

		add_action( 'activated_plugin', function ( $plugin ) {
			$this->plugins_loaded();
			$this->main_init();
			if ( $this->define->plugin_base_name === $plugin ) {
				$this->filter->do_action( 'app_activated', $this );
			}
		} );

		add_action( 'deactivated_plugin', function ( $plugin ) {
			if ( $this->define->plugin_base_name === $plugin ) {
				$this->filter->do_action( 'app_deactivated', $this );
			}
		} );
	}

	/**
	 * load basic files
	 */
	private function plugins_loaded() {
		if ( $this->_plugins_loaded ) {
			return;
		}
		$this->_plugins_loaded = true;

		spl_autoload_register( function ( $class ) {
			return $this->get_main()->load_class( $class );
		} );

		$this->load_functions();
	}

	/**
	 * load functions file
	 */
	private function load_functions() {
		$functions = $this->define->plugin_dir . DS . 'functions.php';
		if ( is_readable( $functions ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once $functions;
		}
	}

	/**
	 * @since 2.0.2 Fixed: Uninstall behavior
	 *
	 * @param string $plugin_base_name
	 */
	private static function uninstall( $plugin_base_name ) {
		$app = self::find_plugin( $plugin_base_name );
		if ( ! isset( $app ) ) {
			return;
		}

		$app->_is_uninstall = true;
		$app->plugins_loaded();
		$app->main_init();
		$app->uninstall->uninstall();
	}

	/**
	 * @since 2.7.4 Fixed: suppress error when uninstall plugin
	 *
	 * @param string $plugin_base_name
	 *
	 * @return \Technote|null
	 */
	private static function find_plugin( $plugin_base_name ) {
		/** @var \Technote $instance */
		foreach ( self::$_instances as $plugin_name => $instance ) {
			$instance->plugins_loaded();
			if ( $instance->define->plugin_base_name === $plugin_base_name ) {
				return $instance;
			}
		}

		return null;
	}

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_library_directory() {
		return self::$_latest_library_directory;
	}

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_library_version() {
		return self::$_latest_library_version;
	}

	/**
	 * @since 2.10.0
	 * @return bool
	 */
	public function is_uninstall() {
		return $this->_is_uninstall;
	}
}

if ( ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
	require_once __DIR__ . DS . 'classes' . DS . 'wp-rest-request.php';
	require_once __DIR__ . DS . 'classes' . DS . 'wp-rest-response.php';
}
