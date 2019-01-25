<?php
/**
 * Technote Traits Singleton
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.0.2 Added: Uninstall priority
 * @since 2.3.0 Changed: implements readonly trait
 * @since 2.3.2 Fixed: ignore abstract class
 * @since 2.4.2 Added: is_filter_callable, filter_callback methods
 * @since 2.4.2 Deleted: add_filter method
 * @since 2.10.0 Changed: trivial change
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Singleton
 * @package TechnoteTraits
 * @property \Technote $app
 */
trait Singleton {

	use Readonly;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array|Singleton[] $_instances
	 */
	private static $_instances = [];

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array|string[] $_slugs
	 */
	private static $_slugs = [];

	/** @var \Technote $app */
	protected $app;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var string $_class_name
	 */
	private $_class_name;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var \ReflectionClass $_reflection
	 */
	private $_reflection;

	/**
	 * @since 2.0.2 Added: Uninstall priority
	 * @since 2.3.2 Fixed: ignore abstract class
	 * @since 2.10.0 Improved: performance
	 *
	 * @param \Technote $app
	 *
	 * @return \Technote\Traits\Singleton
	 */
	public static function get_instance( \Technote $app ) {
		$class = get_called_class();
		if ( false === $class ) {
			$class = get_class();
		}
		try {
			$key = static::is_shared_class() ? '' : $app->plugin_name;
			if ( empty( self::$_instances[ $key ] ) || ! array_key_exists( $class, self::$_instances[ $key ] ) ) {
				$reflection = new \ReflectionClass( $class );
				if ( $reflection->isAbstract() ) {
					self::$_instances[ $key ][ $class ] = null;
				} else {
					$instance = new static( $app, $reflection );
					if ( $app->is_uninstall() && $instance instanceof \Technote\Interfaces\Uninstall ) {
						$app->uninstall->add_uninstall( [ $instance, 'uninstall' ], $instance->get_uninstall_priority() );
					}
					self::$_instances[ $key ][ $class ] = $instance;
					$instance->set_allowed_access( true );
					$instance->initialize();
					$instance->set_allowed_access( false );
				}
			}

			return self::$_instances[ $key ][ $class ];
		} catch ( \Exception $e ) {
		}

		return null;
	}

	/**
	 * @since 2.10.0
	 * @return bool
	 */
	protected static function is_shared_class() {
		return false;
	}

	/**
	 * Singleton constructor.
	 *
	 * @param \Technote $app
	 * @param \ReflectionClass $reflection
	 */
	private function __construct( \Technote $app, $reflection ) {
		$this->init( $app, $reflection );
	}

	/**
	 * @param \Technote $app
	 * @param \ReflectionClass $reflection
	 */
	protected function init( \Technote $app, $reflection ) {
		$this->app         = $app;
		$this->_reflection = $reflection;
		$this->_class_name = $reflection->getName();
		if ( $this instanceof \Technote\Interfaces\Hook ) {
			if ( $app->has_initialized() ) {
				$this->initialized();
			} else {
				add_action( $this->get_filter_prefix() . 'app_initialized', function () {
					$this->initialized();
				} );
			}
		}
	}

	/**
	 * initialize
	 */
	protected function initialize() {

	}

	/**
	 * initialized
	 */
	protected function initialized() {

	}

	/**
	 * @param string $config_name
	 * @param string $suffix
	 *
	 * @return string
	 */
	public function get_slug( $config_name, $suffix = '-' ) {
		if ( ! isset( self::$_slugs[ $this->app->plugin_name ][ $config_name ] ) ) {
			$default = $this->app->slug_name . $suffix;
			$slug    = $this->app->get_config( 'slug', $config_name, $default );
			if ( empty( $slug ) ) {
				$slug = $default;
			}
			self::$_slugs[ $this->app->plugin_name ][ $config_name ] = $slug;
		}

		return self::$_slugs[ $this->app->plugin_name ][ $config_name ];
	}

	/**
	 * @param string $method
	 *
	 * @return bool
	 */
	public function is_filter_callable( $method ) {
		return method_exists( $this, $method ) && is_callable( [ $this, $method ] );
	}

	/**
	 * @param string $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function filter_callback( $method, $args ) {
		return call_user_func( [ $this, $method ], ...$args );
	}

	/**
	 * @return string
	 */
	protected function get_file_slug() {
		$class    = get_class( $this );
		$exploded = explode( '\\', $class );
		$slug     = end( $exploded );

		return strtolower( $slug );
	}

	/**
	 * @param string $name
	 * @param callable $func
	 *
	 * @return bool
	 */
	protected function lock_process( $name, $func ) {
		$name .= '__LOCK_PROCESS__';
		$this->app->option->reload_options();
		$check = $this->app->option->get( $name );
		if ( ! empty( $check ) ) {
			return false;
		}
		$rand = md5( uniqid() );
		$this->app->option->set( $name, $rand );
		$this->app->option->reload_options();
		if ( $this->app->option->get( $name ) != $rand ) {
			return false;
		}
		$func();
		$this->app->option->delete( $name );

		return true;
	}

	/**
	 * @return string
	 */
	public function get_class_name() {
		return $this->_class_name;
	}

	/**
	 * @return \ReflectionClass
	 */
	public function get_reflection() {
		return $this->_reflection;
	}
}
