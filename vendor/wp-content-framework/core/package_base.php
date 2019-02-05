<?php
/**
 * WP_Framework Package Base
 *
 * @version 0.0.27
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Package_Base
 * @package WP_Framework
 */
abstract class Package_Base {

	/** @var Package_Base[] $_instances */
	private static $_instances = [];

	/**
	 * @var \WP_Framework $_app
	 */
	private $_app;

	/**
	 * @var array $_configs
	 */
	private $_configs = [];

	/**
	 * @var string $_version
	 */
	private $_version;

	/**
	 * @var string $_package
	 */
	protected $_package;

	/**
	 * @var string $_dir
	 */
	protected $_dir;

	/**
	 * @var string $_url
	 */
	protected $_url;

	/**
	 * @var string $_namespace
	 */
	protected $_namespace;

	/**
	 * @param \WP_Framework $app
	 * @param string $package
	 * @param string $dir
	 * @param string $version
	 *
	 * @return Package_Base
	 */
	public static function get_instance( \WP_Framework $app, $package, $dir, $version ) {
		if ( ! isset( self::$_instances[ $package ] ) ) {
			self::$_instances[ $package ] = new static( $app, $package, $dir, $version );
		} else {
			self::$_instances[ $package ]->setup( $app, $package, $dir, $version );
		}

		return self::$_instances[ $package ];
	}

	/**
	 * Main constructor.
	 *
	 * @param \WP_Framework $app
	 * @param string $package
	 * @param string $dir
	 * @param string $version
	 */
	private function __construct( $app, $package, $dir, $version ) {
		$this->setup( $app, $package, $dir, $version );
		$this->initialize();
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $package
	 * @param string $dir
	 * @param string $version
	 */
	private function setup( $app, $package, $dir, $version ) {
		$this->_app     = $app;
		$this->_package = $package;
		$this->_dir     = $dir;
		$this->_version = $version;

		$this->_namespace = null;
		$this->_configs   = [];
		$this->_url       = null;
	}

	/**
	 * @return string
	 */
	public function get_package() {
		return $this->_package;
	}

	/**
	 * @return string
	 */
	public function get_namespace() {
		! isset( $this->_namespace ) and $this->_namespace = 'WP_Framework_' . ucwords( $this->_package, '_' );

		return $this->_namespace;
	}

	/**
	 * initialize
	 */
	protected function initialize() {

	}

	/**
	 * @return int
	 */
	public abstract function get_priority();

	/**
	 * @return array
	 */
	public function get_configs() {
		return [];
	}

	/**
	 * @param $name
	 * @param \WP_Framework $app
	 *
	 * @return array
	 */
	public function get_config( $name, $app = null ) {
		if ( ! isset( $this->_configs[ $name ] ) ) {
			if ( ! in_array( $name, $this->get_configs() ) ) {
				$this->_configs[ $name ] = [];
			} else {
				$this->_configs[ $name ] = $this->load_package_config( $name );
			}
		}

		$config = $this->_configs[ $name ];
		if ( $app ) {
			$config = array_replace_recursive( $config, $this->load_plugin_config( $name, $app ) );
		}

		return $config;
	}

	/**
	 * @param string $class
	 *
	 * @return bool
	 */
	public function load_class( $class ) {
		$class = $this->trim_namespace( $class );
		if ( $class ) {
			$class = strtolower( $class );
			$path  = $this->get_dir() . DS . 'src' . DS . str_replace( '\\', DS, $class ) . '.php';
			if ( is_readable( $path ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once $path;

				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $namespace
	 *
	 * @return array
	 */
	public function namespace_to_dir( $namespace ) {
		$relative = $this->trim_namespace( $namespace );
		if ( $relative ) {
			return [ $this->get_dir() . DS . 'src', $relative ];
		}

		return [ null, null ];
	}

	/**
	 * @param string $string
	 *
	 * @return string|false
	 */
	protected function trim_namespace( $string ) {
		$namespace = $this->get_namespace();
		$string    = ltrim( $string, '\\' );
		if ( preg_match( "#\A{$namespace}\\\\#", $string ) ) {
			return preg_replace( "#\A{$namespace}\\\\#", '', $string );
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function get_dir() {
		return $this->_dir;
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return $this->_version;
	}

	/**
	 * @return string
	 */
	public function get_url() {
		if ( ! isset( $this->_url ) ) {
			$url        = $this->_app->is_theme ? get_template_directory_uri() : plugins_url( '', $this->_app->plugin_file );
			$relative   = str_replace( DS, '/', $this->_app->relative_path );
			$vendor     = WP_FRAMEWORK_VENDOR_NAME;
			$this->_url = "{$url}/{$relative}vendor/{$vendor}/{$this->_package}";
		}

		return $this->_url;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_assets() {
		return false;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_view() {
		return false;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_translate() {
		return false;
	}

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	protected function load_package_config( $name ) {
		$package_config = $this->load_config_file( $this->get_dir() . DS . 'configs', $name );

		return apply_filters( 'wp_framework/load_config', $package_config, $name, $package_config );
	}

	/**
	 * @param string $name
	 * @param \WP_Framework $app
	 *
	 * @return array
	 */
	protected function load_plugin_config( $name, $app ) {
		$plugin_config = $this->load_config_file( $app->plugin_dir . DS . 'configs' . DS . $name, $this->get_package() );

		return apply_filters( 'wp_framework/load_config', $plugin_config, $name, $plugin_config, $app );
	}

	/**
	 * @param string $dir
	 * @param string $name
	 *
	 * @return array
	 */
	protected function load_config_file( $dir, $name ) {
		$path = rtrim( $dir, DS ) . DS . $name . '.php';
		if ( ! file_exists( $path ) ) {
			return [];
		}
		/** @noinspection PhpIncludeInspection */
		$config = include $path;
		if ( ! is_array( $config ) ) {
			$config = [];
		}

		return $config;
	}

	/**
	 * @param bool $allow_multiple
	 *
	 * @return array
	 */
	public function get_assets_settings( $allow_multiple = false ) {
		if ( 'view' === $this->_package ) {
			return [ $this->get_assets_dir() => $this->get_assets_url() ];
		}

		if ( $this->_app->is_valid_package( 'view' ) ) {
			$view = $this->_app->get_package_instance( 'view' )->get_assets_settings();
		} else {
			$view = [];
		}
		if ( ! $this->is_valid_assets() ) {
			return $view;
		}

		if ( $allow_multiple ) {
			$settings                            = $view;
			$settings[ $this->get_assets_dir() ] = $this->get_assets_url();
		} else {
			$settings                            = [];
			$settings[ $this->get_assets_dir() ] = $this->get_assets_url();
			foreach ( $view as $k => $v ) {
				$settings[ $k ] = $v;
			}
		}

		return $settings;
	}

	/**
	 * @return array
	 */
	public function get_views_dirs() {
		if ( 'view' === $this->_package ) {
			return [ $this->get_views_dir() ];
		}

		if ( $this->_app->is_valid_package( 'view' ) ) {
			$view = $this->_app->get_package_instance( 'view' )->get_views_dirs();
		} else {
			$view = [];
		}
		if ( ! $this->is_valid_view() ) {
			return $view;
		}

		$dirs   = [];
		$dirs[] = $this->get_views_dir();
		foreach ( $view as $dir ) {
			$dirs[] = $dir;
		}

		return $dirs;
	}

	/**
	 * @return array
	 */
	public function get_translate_settings() {
		if ( 'common' === $this->_package ) {
			return [ $this->get_textdomain() => $this->get_language_directory() ];
		}

		$common = $this->_app->get_package_instance( 'common' );
		if ( ! $this->is_valid_translate() ) {
			return $common->get_translate_settings();
		}

		$settings                            = [];
		$settings[ $this->get_textdomain() ] = $this->get_language_directory();
		foreach ( $common->get_translate_settings() as $k => $v ) {
			$settings[ $k ] = $v;
		}

		return $settings;
	}

	/**
	 * @return string
	 */
	protected function get_textdomain() {
		return 'wp_framework-' . $this->_package;
	}

	/**
	 * @return string
	 */
	protected function get_assets_dir() {
		return $this->get_dir() . DS . 'assets';
	}

	/**
	 * @return string
	 */
	protected function get_assets_url() {
		return $this->get_url() . '/assets';
	}

	/**
	 * @return string
	 */
	protected function get_views_dir() {
		return $this->get_dir() . DS . 'src' . DS . 'views';
	}

	/**
	 * @return string
	 */
	protected function get_language_directory() {
		return $this->get_dir() . DS . 'languages';
	}
}
