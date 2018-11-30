<?php
/**
 * Technote Traits Singleton
 *
 * @version 1.1.62
 * @author technote-space
 * @since 1.0.0
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
 * @property string $class_name
 * @property \ReflectionClass $reflection
 */
trait Singleton {

	/** @var array */
	private static $instances = [];

	/** @var array */
	private static $slugs = [];

	/** @var \Technote */
	protected $app;

	/** @var string */
	public $class_name;

	/** @var \ReflectionClass */
	public $reflection;

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
		$this->app        = $app;
		$this->reflection = $reflection;
		$this->class_name = $reflection->getName();
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
			if ( ! isset( self::$instances[ $app->plugin_name ][ $class ] ) ) {
				$reflection = new \ReflectionClass( $class );
				$instance   = new static( $app, $reflection );
				if ( $instance instanceof \Technote\Interfaces\Uninstall && $app->uninstall ) {
					$app->uninstall->add_uninstall( [ $instance, 'uninstall' ] );
				}
				self::$instances[ $app->plugin_name ][ $class ] = $instance;
				$instance->initialize();
			}

			return self::$instances[ $app->plugin_name ][ $class ];
		} catch ( \Exception $e ) {
		}

		return null;
	}

	/**
	 * @param string $config_name
	 * @param string $suffix
	 *
	 * @return string
	 */
	public function get_slug( $config_name, $suffix = '-' ) {

		if ( ! isset( self::$slugs[ $this->app->plugin_name ][ $config_name ] ) ) {
			$default = $this->app->slug_name . $suffix;
			$slug    = $this->app->get_config( 'slug', $config_name, $default );
			if ( empty( $slug ) ) {
				$slug = $default;
			}
			self::$slugs[ $this->app->plugin_name ][ $config_name ] = $slug;
		}

		return self::$slugs[ $this->app->plugin_name ][ $config_name ];
	}

	/**
	 * @param string $tag
	 * @param string $method
	 * @param string $priority
	 * @param string $accepted_args
	 */
	public function add_filter( $tag, $method, $priority, $accepted_args ) {
		add_filter( $tag, function () use ( $method ) {
			return $this->$method( ...func_get_args() );
		}, $priority, $accepted_args );
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
}
