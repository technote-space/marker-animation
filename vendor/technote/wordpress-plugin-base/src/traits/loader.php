<?php
/**
 * Technote Traits Loader
 *
 * @version 2.3.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.3.0 Changed: property access to getter access
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Loader
 * @package Technote\Traits\Controller
 * @property \Technote $app
 */
trait Loader {

	use Singleton, Hook, Presenter;

	/** @var array */
	private $list = null;

	/** @var array */
	private $cache = [];

	/**
	 * @return string
	 */
	public function get_loader_name() {
		return $this->get_file_slug();
	}

	/**
	 * @param string $namespace
	 *
	 * @return string|false
	 */
	private function namespace_to_dir( $namespace ) {
		$namespace = ltrim( $namespace, '\\' );
		$dir       = null;
		if ( preg_match( "#^{$this->app->define->plugin_namespace}#", $namespace ) ) {
			$namespace = preg_replace( "#^{$this->app->define->plugin_namespace}#", '', $namespace );
			$dir       = $this->app->define->plugin_src_dir;
		} elseif ( preg_match( "#^{$this->app->define->lib_namespace}#", $namespace ) ) {
			$namespace = preg_replace( "#^{$this->app->define->lib_namespace}#", '', $namespace );
			$dir       = $this->app->define->lib_src_dir;
		}

		if ( isset( $dir ) ) {
			$namespace = ltrim( $namespace, '\\' );
			$namespace = strtolower( $namespace );
			$path      = $dir . DS . str_replace( '\\', DS, $namespace );
			$path      = rtrim( $path, DS );
			if ( is_dir( $path ) ) {
				return $path;
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function get_class_list() {
		if ( ! isset( $this->list ) ) {
			$this->list = [];
			$sort       = [];
			/** @var \Technote\Traits\Singleton $class */
			foreach ( $this->get_namespaces() as $namespace ) {
				foreach ( $this->get_classes( $this->namespace_to_dir( $namespace ), $this->get_instanceof() ) as $class ) {
					$slug = $class->get_class_name();
					if ( ! isset( $this->list[ $slug ] ) ) {
						$this->list[ $slug ] = $class;
						if ( method_exists( $class, 'get_load_priority' ) ) {
							$sort[ $slug ] = $class->get_load_priority();
							if ( $sort[ $slug ] < 0 ) {
								unset( $this->list[ $slug ] );
								unset( $sort[ $slug ] );
							}
						}
					}
				}
			}
			if ( ! empty( $sort ) ) {
				uasort( $this->list, function ( $a, $b ) use ( $sort ) {
					/** @var \Technote\Traits\Singleton $a */
					/** @var \Technote\Traits\Singleton $b */
					$pa = isset( $sort[ $a->get_class_name() ] ) ? $sort[ $a->get_class_name() ] : 10;
					$pb = isset( $sort[ $b->get_class_name() ] ) ? $sort[ $b->get_class_name() ] : 10;

					return $pa == $pb ? 0 : ( $pa < $pb ? - 1 : 1 );
				} );
			}
		}

		return $this->list;
	}

	/**
	 * @return int
	 */
	public function get_loaded_count() {
		return count( $this->get_class_list() );
	}

	/**
	 * @param string $dir
	 * @param string $add_namespace
	 *
	 * @return \Generator
	 */
	protected function get_class_settings( $dir, $add_namespace = '' ) {
		$dir = rtrim( $dir, DS );
		if ( is_dir( $dir ) ) {
			foreach ( scandir( $dir ) as $file ) {
				if ( $file == '.' || $file == '..' ) {
					continue;
				}
				if ( is_file( $dir . DS . $file ) && preg_match( "/^[^\\.].*\\.php$/", $file ) ) {
					yield $this->get_class_setting( $this->app->get_page_slug( $file ), $add_namespace );
				} elseif ( is_dir( $dir . DS . $file ) ) {
					foreach ( $this->get_class_settings( $dir . DS . $file, $add_namespace . ucfirst( $file ) . '\\' ) as $class_setting ) {
						yield $class_setting;
					}
				}
			}
		}
	}

	/**
	 * @param string $dir
	 * @param string $instanceof
	 * @param bool $return_instance
	 *
	 * @return \Generator
	 */
	protected function get_classes( $dir, $instanceof, $return_instance = true ) {
		foreach ( $this->get_class_settings( $dir ) as $class_setting ) {
			$instance = $this->get_class_instance( $class_setting, $instanceof );
			if ( false !== $instance ) {
				if ( $return_instance ) {
					yield $instance;
				} else {
					yield $class_setting;
				}
			}
		}
	}

	/**
	 * @since 2.3.0 Changed: method name
	 *
	 * @param string $page
	 *
	 * @return string
	 */
	private function get_page_class_name( $page ) {
		return $this->apply_filters( 'get_page_class_name', ucfirst( str_replace( DS, '\\', $page ) ), $page );
	}

	/**
	 * @param string $page
	 * @param string $add_namespace
	 *
	 * @return false|array
	 */
	protected function get_class_setting( $page, $add_namespace = '' ) {
		if ( 'base' === $page ) {
			return false;
		}
		if ( isset( $this->cache[ $add_namespace . $page ] ) ) {
			return $this->cache[ $add_namespace . $page ];
		}
		$namespaces = $this->get_namespaces();
		if ( ! empty( $namespaces ) ) {
			foreach ( $namespaces as $namespace ) {
				$class = rtrim( $namespace, '\\' ) . '\\' . $add_namespace . $this->get_page_class_name( $page );
				if ( class_exists( $class ) ) {
					$this->cache[ $add_namespace . $page ] = [ $class, $add_namespace ];

					return $this->cache[ $add_namespace . $page ];
				}
			}
		}

		return false;
	}

	/**
	 * @param array $class_setting
	 * @param string $instanceof
	 *
	 * @return bool|Singleton
	 */
	protected function get_class_instance( $class_setting, $instanceof ) {
		if ( false !== $class_setting && class_exists( $class_setting[0] ) && is_subclass_of( $class_setting[0], '\Technote\Interfaces\Singleton' ) ) {
			try {
				/** @var Singleton[] $class_setting */
				$instance = $class_setting[0]::get_instance( $this->app );
				if ( $instance instanceof $instanceof ) {
					if ( $instance instanceof \Technote\Interfaces\Controller\Admin ) {
						$instance->set_relative_namespace( $class_setting[1] );
					}

					return $instance;
				}
			} catch ( \Exception $e ) {
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	protected abstract function get_namespaces();

	/**
	 * @return string
	 */
	protected abstract function get_instanceof();

}
