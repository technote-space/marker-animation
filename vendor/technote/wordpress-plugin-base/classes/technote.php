<?php
/**
 * Technote
 *
 * @version 1.1.69
 * @author technote-space
 * @since 1.0.0
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
 * @property \Technote\Models\Define $define
 * @property \Technote\Models\Config $config
 * @property \Technote\Models\Setting $setting
 * @property \Technote\Models\Option $option
 * @property \Technote\Models\Device $device
 * @property \Technote\Models\Minify $minify
 * @property \Technote\Models\Filter $filter
 * @property \Technote\Models\User $user
 * @property \Technote\Models\Post $post
 * @property \Technote\Models\Loader $loader
 * @property \Technote\Models\Log $log
 * @property \Technote\Models\Input $input
 * @property \Technote\Models\Db $db
 * @property \Technote\Models\Uninstall $uninstall
 * @property \Technote\Models\Session $session
 */
class Technote {

	/** @var array|\Technote[] */
	private static $instances = [];

	/** @var bool $lib_language_loaded */
	private static $lib_language_loaded = false;

	/** @var array $shared_object */
	private static $shared_object = [];

	/** @var bool $initialized */
	private $initialized = false;

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
		'define'    => '\Technote\Models\Define',
		'config'    => '\Technote\Models\Config',
		'setting'   => '\Technote\Models\Setting',
		'option'    => '\Technote\Models\Option',
		'device'    => '\Technote\Models\Device',
		'minify'    => '\Technote\Models\Minify',
		'filter'    => '\Technote\Models\Filter',
		'user'      => '\Technote\Models\User',
		'post'      => '\Technote\Models\Post',
		'loader'    => '\Technote\Models\Loader',
		'log'       => '\Technote\Models\Log',
		'input'     => '\Technote\Models\Input',
		'db'        => '\Technote\Models\Db',
		'uninstall' => '\Technote\Models\Uninstall',
		'session'   => '\Technote\Models\Session',
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
	 * Technote constructor.
	 *
	 * @param string $plugin_name
	 * @param string $plugin_file
	 * @param string|null $slug_name
	 */
	private function __construct( $plugin_name, $plugin_file, $slug_name ) {
		require_once __DIR__ . DS . 'traits' . DS . 'singleton.php';
		require_once __DIR__ . DS . 'interfaces' . DS . 'singleton.php';
		require_once __DIR__ . DS . 'models' . DS . 'define.php';

		$this->original_plugin_name = $plugin_name;
		$this->plugin_file          = $plugin_file;
		$this->plugin_name          = strtolower( $this->original_plugin_name );
		$this->slug_name            = ! empty( $slug_name ) ? strtolower( $slug_name ) : $this->plugin_name;

		add_action( 'init', function () {
			$this->initialize();
		}, 1 );

		add_action( 'plugins_loaded', function () {
			$this->load_functions();
		} );

		add_action( 'activated_plugin', function ( $plugin ) {
			if ( ! $this->initialized ) {
				if ( did_action( 'plugins_loaded' ) ) {
					$this->load_functions();
				}
				if ( did_action( 'init' ) ) {
					$this->initialize();
				}
			}
			if ( $this->define->plugin_base_name === $plugin ) {
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
	 * @param string $plugin_name
	 * @param string $plugin_file
	 * @param string|null $slug_name
	 *
	 * @return Technote
	 */
	public static function get_instance( $plugin_name, $plugin_file, $slug_name = null ) {
		if ( ! isset( self::$instances[ $plugin_name ] ) ) {
			self::$instances[ $plugin_name ] = new static( $plugin_name, $plugin_file, $slug_name );
		}

		return self::$instances[ $plugin_name ];
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
	private function setup_property( $uninstall ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->plugin_data = get_plugin_data( $this->plugin_file, false, false );
		spl_autoload_register( [ $this, 'load_class' ] );

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
	 * setup textdomain
	 */
	private function setup_textdomain() {
		if ( ! static::$lib_language_loaded ) {
			static::$lib_language_loaded = true;
			load_plugin_textdomain( $this->define->lib_name, false, $this->define->lib_language_rel_path );
		}

		$text_domain = $this->get_config( 'config', 'text_domain' );
		if ( ! empty( $text_domain ) ) {
			load_plugin_textdomain( $text_domain, false, $this->define->plugin_languages_rel_path );
		}
	}

	/**
	 * setup settings
	 */
	private function setup_settings() {
		if ( ! class_exists( '\WP_REST_Request' ) ) {
			$this->setting->remove_setting( 'use_admin_ajax' );
		}
		if ( $this->loader->api->get_loaded_count() <= 1 ) {
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
		$text_domain = $this->get_config( 'config', 'text_domain' );
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
		if ( preg_match( "#^{$this->define->plugin_namespace}#", $class ) ) {
			$class = preg_replace( "#^{$this->define->plugin_namespace}#", '', $class );
			$dir   = $this->define->plugin_classes_dir;
		} elseif ( preg_match( "#^{$this->define->lib_namespace}#", $class ) ) {
			$class = preg_replace( "#^{$this->define->lib_namespace}#", '', $class );
			$dir   = $this->define->lib_classes_dir;
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

require_once __DIR__ . DS . 'wp-rest-request.php';
require_once __DIR__ . DS . 'wp-rest-response.php';