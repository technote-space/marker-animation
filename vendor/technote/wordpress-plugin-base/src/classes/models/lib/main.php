<?php
/**
 * Technote Classes Models Lib Main
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0 Changed: directory structure
 * @since 2.1.0 Changed: load textdomain from plugin data
 * @since 2.3.0 Changed: public properties to readonly properties
 * @since 2.5.0 Changed: views directory
 * @since 2.10.0 Changed: moved main program
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Main
 * @package Technote\Classes\Models\Lib
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
 */
class Main implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var bool $_lib_language_loaded
	 */
	private static $_lib_language_loaded = false;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_shared_object
	 */
	private static $_shared_object = [];

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var bool $_initialized
	 */
	private $_initialized = false;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_plugin_data
	 */
	private $_plugin_data;

	/**
	 * @since 2.8.0 Added: social, custom_post
	 * @since 2.9.0 Added: mail
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_properties
	 */
	private $_properties = [
		'define'      => '\Technote\Classes\Models\Lib\Define',
		'config'      => '\Technote\Classes\Models\Lib\Config',
		'setting'     => '\Technote\Classes\Models\Lib\Setting',
		'option'      => '\Technote\Classes\Models\Lib\Option',
		'device'      => '\Technote\Classes\Models\Lib\Device',
		'minify'      => '\Technote\Classes\Models\Lib\Minify',
		'filter'      => '\Technote\Classes\Models\Lib\Filter',
		'user'        => '\Technote\Classes\Models\Lib\User',
		'post'        => '\Technote\Classes\Models\Lib\Post',
		'loader'      => '\Technote\Classes\Models\Lib\Loader',
		'log'         => '\Technote\Classes\Models\Lib\Log',
		'input'       => '\Technote\Classes\Models\Lib\Input',
		'db'          => '\Technote\Classes\Models\Lib\Db',
		'uninstall'   => '\Technote\Classes\Models\Lib\Uninstall',
		'session'     => '\Technote\Classes\Models\Lib\Session',
		'utility'     => '\Technote\Classes\Models\Lib\Utility',
		'test'        => '\Technote\Classes\Models\Lib\Test',
		'upgrade'     => '\Technote\Classes\Models\Lib\Upgrade',
		'social'      => '\Technote\Classes\Models\Lib\Social',
		'custom_post' => '\Technote\Classes\Models\Lib\Custom_Post',
		'mail'        => '\Technote\Classes\Models\Lib\Mail',
	];

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_property_instances
	 */
	private $_property_instances = [];

	/**
	 * @param string $name
	 *
	 * @return \Technote\Interfaces\Singleton
	 * @throws \OutOfRangeException
	 */
	public function __get( $name ) {
		if ( isset( $this->_properties[ $name ] ) ) {
			if ( ! isset( $this->_property_instances[ $name ] ) ) {
				/** @var \Technote\Interfaces\Singleton $class */
				$class                              = $this->_properties[ $name ];
				$this->_property_instances[ $name ] = $class::get_instance( $this->app );
			}

			return $this->_property_instances[ $name ];
		}
		throw new \OutOfRangeException( $name . ' is undefined.' );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return array_key_exists( $name, $this->_properties );
	}

	/**
	 * initialize
	 */
	protected function initialize() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->_plugin_data = get_plugin_data( $this->app->plugin_file, false, false );
	}

	/**
	 * @since 2.10.0 Improved: performance
	 *
	 * @param string $class
	 *
	 * @return bool
	 */
	public function load_class( $class ) {
		$class = ltrim( $class, '\\' );
		$dir   = null;

		if ( ! isset( $this->_property_instances['define'] ) ) {
			$namespace = ucfirst( TECHNOTE_PLUGIN );
			if ( preg_match( "#\A{$namespace}#", $class ) ) {
				$class = preg_replace( "#\A{$namespace}#", '', $class );
				$dir   = $this->app->get_library_directory() . DS . 'src';
			}
		} elseif ( preg_match( "#\A{$this->define->plugin_namespace}#", $class ) ) {
			$class = preg_replace( "#\A{$this->define->plugin_namespace}#", '', $class );
			$dir   = $this->define->plugin_src_dir;
		} elseif ( preg_match( "#\A{$this->define->lib_namespace}#", $class ) ) {
			$class = preg_replace( "#\A{$this->define->lib_namespace}#", '', $class );
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
	 * main init
	 * @since 2.10.0 Deleted: $uninstall parameter
	 */
	public function main_init() {
		if ( $this->_initialized ) {
			return;
		}
		$this->_initialized = true;

		$this->filter->do_action( 'app_initialize', $this );
		$this->setup_property();
		$this->setup_textdomain();
		$this->setup_settings();
		$this->filter->do_action( 'app_initialized', $this );
	}

	/**
	 * @since 2.8.1 Added: setup social login, custom post filters
	 * @since 2.10.0 Deleted: $uninstall parameter
	 */
	private function setup_property() {
		if ( $this->app->is_uninstall() ) {
			foreach ( $this->_properties as $name => $class ) {
				$this->$name;
			}
			$this->uninstall->get_class_list();
		} else {
			if ( $this->get_config( 'config', 'use_custom_post' ) ) {
				$this->custom_post;
			}
			if ( $this->get_config( 'config', 'use_social_login' ) ) {
				$this->social;
			}
		}
	}

	/**
	 * setup textdomain
	 */
	private function setup_textdomain() {
		if ( ! self::$_lib_language_loaded ) {
			self::$_lib_language_loaded = true;
			load_plugin_textdomain( $this->define->lib_textdomain, false, $this->define->lib_languages_rel_path );
		}

		$text_domain = $this->get_text_domain();
		if ( ! empty( $text_domain ) ) {
			load_plugin_textdomain( $text_domain, false, $this->define->plugin_languages_rel_path );
		}
	}

	/**
	 * setup settings
	 * @since 2.1.0 Changed: set default value of check_update when the plugin is registered as official
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
		if ( ! empty( $this->_plugin_data['PluginURI'] ) && $this->utility->starts_with( $this->_plugin_data['PluginURI'], 'https://wordpress.org' ) ) {
			$this->setting->edit_setting( 'check_update', 'default', false );
		}
		if ( ! $this->log->is_valid() ) {
			$this->setting->remove_setting( 'save___log_term' );
			$this->setting->remove_setting( 'delete___log_interval' );
		}
		if ( $this->get_config( 'config', 'prevent_use_log' ) ) {
			$this->setting->remove_setting( 'is_valid_log' );
			$this->setting->remove_setting( 'capture_shutdown_error' );
		}
	}

	/**
	 * @return bool
	 */
	public function has_initialized() {
		return $this->_initialized;
	}

	/**
	 * @since 2.10.0
	 *
	 * @param string|null $key
	 *
	 * @return array|string
	 */
	public function get_plugin_data( $key = null ) {
		return empty( $key ) ? $this->_plugin_data : $this->_plugin_data[ $key ];
	}

	/**
	 * @since
	 * @return string
	 */
	public function get_plugin_version() {
		return $this->get_plugin_data( 'Version' );
	}

	/**
	 * @since 1.1.73
	 * @since 2.1.0 Changed: load textdomain from plugin data
	 * @return string|false
	 */
	public function get_text_domain() {
		return $this->define->plugin_textdomain;
	}

	/**
	 * @param string $value
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

		return __( $value, $this->define->lib_textdomain );
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
	 * @param string $message
	 * @param mixed $context
	 * @param string $level
	 */
	public function log( $message, $context = null, $level = '' ) {
		if ( $message instanceof \Exception ) {
			$this->log->log( $message->getMessage(), isset( $context ) ? $context : $message->getTraceAsString(), empty( $level ) ? 'error' : $level );
		} elseif ( $message instanceof \WP_Error ) {
			$this->log->log( $message->get_error_message(), isset( $context ) ? $context : $message->get_error_data(), empty( $level ) ? 'error' : $level );
		} else {
			$this->log->log( $message, $context, $level );
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
	 * @param string $file
	 *
	 * @return string
	 */
	public function get_page_slug( $file ) {
		return basename( $file, '.php' );
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 *
	 * @return mixed
	 */
	public function get_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;

		return isset( self::$_shared_object[ $target ][ $key ] ) ? self::$_shared_object[ $target ][ $key ] : null;
	}

	/**
	 * @param string $key
	 * @param mixed $object
	 * @param string|null $target
	 */
	public function set_shared_object( $key, $object, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;
		self::$_shared_object[ $target ][ $key ] = $object;
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 *
	 * @return bool
	 */
	public function isset_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;

		return isset( self::$_shared_object[ $target ] ) && array_key_exists( $key, self::$_shared_object[ $target ] );
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 */
	public function delete_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;
		unset( self::$_shared_object[ $target ][ $key ] );
	}
}
