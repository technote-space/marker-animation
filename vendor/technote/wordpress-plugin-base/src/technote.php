<?php
/**
 * Technote
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0 Added: Feature to load library of latest version
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
 * @property array $plugin_data
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
 */
class Technote {

	/** @var array|\Technote[] */
	private static $instances = [];

	/** @var bool $lib_language_loaded */
	private static $lib_language_loaded = false;

	/** @var array $shared_object */
	private static $shared_object = [];

	/**
	 * @since 2.0.0
	 * @var string $latest_library_version
	 */
	private static $latest_library_version = null;

	/**
	 * @since 2.0.0
	 * @var string $latest_library_directory
	 */
	private static $latest_library_directory = null;

	/**
	 * @since 2.0.0
	 * @var bool $plugins_loaded
	 */
	private $plugins_loaded = false;

	/** @var bool $initialized */
	private $initialized = false;

	/**
	 * @since 2.0.0
	 * @var string $library_version
	 */
	private $library_version;

	/**
	 * @since 2.0.0
	 * @var string $library_directory
	 */
	private $library_directory;

	/** @var string $original_plugin_name */
	public $original_plugin_name;

	/** @var string $plugin_name */
	public $plugin_name;

	/** @var string $plugin_file */
	public $plugin_file;

	/** @var array $plugin_data */
	public $plugin_data;

	/** @var array $properties */
	private $properties = [
		'define'    => '\Technote\Classes\Models\Lib\Define',
		'config'    => '\Technote\Classes\Models\Lib\Config',
		'setting'   => '\Technote\Classes\Models\Lib\Setting',
		'option'    => '\Technote\Classes\Models\Lib\Option',
		'device'    => '\Technote\Classes\Models\Lib\Device',
		'minify'    => '\Technote\Classes\Models\Lib\Minify',
		'filter'    => '\Technote\Classes\Models\Lib\Filter',
		'user'      => '\Technote\Classes\Models\Lib\User',
		'post'      => '\Technote\Classes\Models\Lib\Post',
		'loader'    => '\Technote\Classes\Models\Lib\Loader',
		'log'       => '\Technote\Classes\Models\Lib\Log',
		'input'     => '\Technote\Classes\Models\Lib\Input',
		'db'        => '\Technote\Classes\Models\Lib\Db',
		'uninstall' => '\Technote\Classes\Models\Lib\Uninstall',
		'session'   => '\Technote\Classes\Models\Lib\Session',
		'utility'   => '\Technote\Classes\Models\Lib\Utility',
	];

	/** @var array $property_instances */
	private $property_instances = [];

	/**
	 * @param string $name
	 *
	 * @return \Technote\Interfaces\Singleton
	 * @throws \Exception
	 */
	public function __get( $name ) {
		if ( isset( $this->properties[ $name ] ) ) {
			if ( ! isset( $this->property_instances[ $name ] ) ) {
				/** @var \Technote\Interfaces\Singleton $class */
				$class                             = $this->properties[ $name ];
				$this->property_instances[ $name ] = $class::get_instance( $this );
			}

			return $this->property_instances[ $name ];
		}
		throw new \Exception( $name . ' is undefined.' );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return array_key_exists( $name, $this->properties );
	}

	/**
	 * @param string $plugin_name
	 * @param string $plugin_file
	 * @param string|null $slug_name
	 *
	 * @return Technote
	 */
	public static function get_instance( $plugin_name, $plugin_file, $slug_name = null ) {
		if ( ! isset( self::$instances[ $plugin_name ] ) ) {
			$instances                       = new static( $plugin_name, $plugin_file, $slug_name );
			self::$instances[ $plugin_name ] = $instances;

			$latest  = self::$latest_library_version;
			$version = $instances->library_version;
			if ( ! isset( $latest ) || version_compare( $latest, $version, '<' ) ) {
				self::$latest_library_version   = $version;
				self::$latest_library_directory = $instances->library_directory;
			}
		}

		return self::$instances[ $plugin_name ];
	}

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
		$this->library_version   = $library_version;
		$this->library_directory = $library_directory;
	}

	/**
	 * @since 2.0.0
	 * setup actions
	 */
	private function setup_actions() {
		add_action( 'plugins_loaded', function () {
			$this->plugins_loaded();
		} );

		add_action( 'init', function () {
			$this->initialize();
		}, 1 );

		add_action( 'activated_plugin', function ( $plugin ) {
			if ( $this->define->plugin_base_name === $plugin ) {
				if ( ! $this->initialized ) {
					if ( did_action( 'plugins_loaded' ) ) {
						$this->plugins_loaded();
					}
					if ( did_action( 'init' ) ) {
						$this->initialize();
					}
				}
				$this->filter->do_action( 'app_activated' );
			}
		} );

		add_action( 'deactivated_plugin', function ( $plugin ) {
			if ( $this->define->plugin_base_name === $plugin ) {
				$this->filter->do_action( 'app_deactivated' );
			}
		} );
	}

	/**
	 * load basic files
	 */
	private function plugins_loaded() {
		if ( $this->plugins_loaded ) {
			return;
		}
		$this->plugins_loaded = true;

		spl_autoload_register( [ $this, 'load_class' ] );
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
	 * @param bool $uninstall
	 */
	private function initialize( $uninstall = false ) {
		if ( $this->initialized ) {
			return;
		}
		$this->initialized = true;
		$this->setup_property( $uninstall );
		$this->setup_update();
		$this->setup_textdomain();
		$this->setup_settings();
		$this->filter->do_action( 'app_initialized' );
	}

	/**
	 * @return bool
	 */
	public function has_initialized() {
		return $this->initialized;
	}

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_library_directory() {
		return self::$latest_library_directory;
	}

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_library_version() {
		return self::$latest_library_version;
	}

	/**
	 * @param bool $uninstall
	 */
	private function setup_property( $uninstall ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->plugin_data = get_plugin_data( $this->plugin_file, false, false );

		if ( $uninstall ) {
			foreach ( $this->properties as $name => $class ) {
				$this->$name;
			}
			$this->loader->uninstall->get_class_list();
		}
	}

	/**
	 * setup update checker
	 */
	private function setup_update() {
		$update_info_file_url = $this->get_config( 'config', 'update_info_file_url' );
		if ( ! empty( $update_info_file_url ) ) {
			$key   = $this->plugin_name . '-setup_update';
			$check = get_transient( $key );
			if ( ! $check ) {
				\Puc_v4_Factory::buildUpdateChecker(
					$update_info_file_url,
					$this->plugin_file,
					$this->plugin_name
				);
				set_transient( $key, true, 15 * 60 );
			}
		} else {
			$this->setting->remove_setting( 'check_update' );
		}
	}

	/**
	 * @since 1.1.73
	 * @return mixed
	 */
	public function get_text_domain() {
		return $this->get_config( 'config', 'text_domain' );
	}

	/**
	 * setup textdomain
	 */
	private function setup_textdomain() {
		if ( ! static::$lib_language_loaded ) {
			static::$lib_language_loaded = true;
			load_plugin_textdomain( $this->define->lib_name, false, $this->define->lib_language_rel_path );
		}

		$text_domain = $this->get_text_domain();
		if ( ! empty( $text_domain ) ) {
			load_plugin_textdomain( $text_domain, false, $this->define->plugin_languages_rel_path );
		}
	}

	/**
	 * setup settings
	 */
	private function setup_settings() {
		if ( defined( 'TECHNOTE_MOCK_REST_REQUEST' ) && TECHNOTE_MOCK_REST_REQUEST ) {
			$this->setting->remove_setting( 'use_admin_ajax' );
		}
		if ( $this->loader->api->is_empty() ) {
			$this->setting->remove_setting( 'use_admin_ajax' );
			$this->setting->remove_setting( 'get_nonce_check_referer' );
			$this->setting->remove_setting( 'check_referer_host' );
		}
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function translate( $value ) {
		$text_domain = $this->get_text_domain();
		if ( ! empty( $text_domain ) ) {
			$translated = __( $value, $text_domain );
			if ( $value !== $translated ) {
				return $translated;
			}
		}

		return __( $value, $this->define->lib_name );
	}

	/**
	 * @param string $name
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_config( $name, $key, $default = null ) {
		return $this->config->get( $name, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = '' ) {
		return $this->option->get( $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_session( $key, $default = null ) {
		return $this->session->get( $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int|null $duration
	 */
	public function set_session( $key, $value, $duration = null ) {
		$this->session->set( $key, $value, $duration );
	}

	/**
	 * @param null|string|false $capability
	 *
	 * @return bool
	 */
	public function user_can( $capability = null ) {
		return $this->user->user_can( $capability );
	}

	/**
	 * @param mixed $message
	 */
	public function log( $message ) {
		if ( $message instanceof \Exception ) {
			$this->log->log( $message->getMessage() );
			$this->log->log( $message->getTraceAsString() );
		} else {
			$this->log->log( $message );
		}
	}

	/**
	 * @param string $message
	 * @param string $group
	 * @param bool $error
	 * @param bool $escape
	 */
	public function add_message( $message, $group = '', $error = false, $escape = true ) {
		if ( ! isset( $this->loader->admin ) ) {
			add_action( 'admin_notices', function () use ( $message, $group, $error, $escape ) {
				$this->loader->admin->add_message( $message, $group, $error, $escape );
			}, 9 );
		} else {
			$this->loader->admin->add_message( $message, $group, $error, $escape );
		}
	}

	/**
	 * @param $class
	 *
	 * @return bool
	 */
	public function load_class( $class ) {
		$class = ltrim( $class, '\\' );
		$dir   = null;
		if ( empty( $this->define ) ) {
			$namespace = ucfirst( TECHNOTE_PLUGIN );
			$class     = preg_replace( "#^{$namespace}#", '', $class );
			$dir       = self::$latest_library_directory . DS . 'src';
		} elseif ( preg_match( "#^{$this->define->plugin_namespace}#", $class ) ) {
			$class = preg_replace( "#^{$this->define->plugin_namespace}#", '', $class );
			$dir   = $this->define->plugin_src_dir;
		} elseif ( preg_match( "#^{$this->define->lib_namespace}#", $class ) ) {
			$class = preg_replace( "#^{$this->define->lib_namespace}#", '', $class );
			$dir   = $this->define->lib_src_dir;
		}

		if ( isset( $dir ) ) {
			$class = ltrim( $class, '\\' );
			$class = strtolower( $class );
			$path  = $dir . DS . str_replace( '\\', DS, $class ) . '.php';
			if ( is_readable( $path ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once $path;

				return true;
			}
		}

		return false;
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public function get_page_slug( $file ) {
		return basename( $file, '.php' );
	}

	/**
	 * @param $name
	 * @param $arguments
	 */
	public static function __callStatic( $name, $arguments ) {
		if ( preg_match( '#register_uninstall_(.+)$#', $name, $matches ) ) {
			$plugin_base_name = $matches[1];
			self::uninstall( $plugin_base_name );
		}
	}

	/**
	 * @param string $plugin_base_name
	 */
	private static function uninstall( $plugin_base_name ) {
		$app = self::find_plugin( $plugin_base_name );
		if ( ! isset( $app ) ) {
			return;
		}
		$app->initialize( true );
		$app->uninstall->uninstall();
	}

	/**
	 * @param string $plugin_base_name
	 *
	 * @return \Technote|null
	 */
	private static function find_plugin( $plugin_base_name ) {

		/** @var \Technote $instance */
		foreach ( self::$instances as $plugin_name => $instance ) {
			if ( $instance->define->plugin_base_name === $plugin_base_name ) {
				return $instance;
			}
		}

		return null;
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 *
	 * @return mixed
	 */
	public function get_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->plugin_name;

		return isset( self::$shared_object[ $target ][ $key ] ) ? self::$shared_object[ $target ][ $key ] : null;
	}

	/**
	 * @param string $key
	 * @param mixed $object
	 * @param string|null $target
	 */
	public function set_shared_object( $key, $object, $target = null ) {
		! isset( $target ) and $target = $this->plugin_name;
		self::$shared_object[ $target ][ $key ] = $object;
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 *
	 * @return bool
	 */
	public function isset_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->plugin_name;

		return isset( self::$shared_object[ $target ] ) && array_key_exists( $key, self::$shared_object[ $target ] );
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 */
	public function delete_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->plugin_name;
		unset( self::$shared_object[ $target ][ $key ] );
	}
}

if ( ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
	require_once __DIR__ . DS . 'classes' . DS . 'wp-rest-request.php';
	require_once __DIR__ . DS . 'classes' . DS . 'wp-rest-response.php';
}
