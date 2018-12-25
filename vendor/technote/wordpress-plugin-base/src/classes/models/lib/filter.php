<?php
/**
 * Technote Classes Models Lib Filter
 *
 * @version 2.8.1
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.4.2 Improved: change timing to load filter target instance
 * @since 2.8.1 Improved: refactoring
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Filter
 * @package Technote\Classes\Models\Lib
 */
class Filter implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/** @var array $_target_app */
	private $_target_app = [];

	/**
	 * initialize
	 * @since 2.4.2 Improved: change timing to load filter target instance
	 */
	protected function initialize() {
		foreach ( $this->apply_filters( 'filter', $this->app->config->load( 'filter' ) ) as $class => $tags ) {
			$this->register_class_filter( $class, $tags );
		}
	}

	/**
	 * @param string $class
	 * @param array $tags
	 */
	public function register_class_filter( $class, $tags ) {
		if ( empty( $class ) || ! is_array( $tags ) ) {
			return;
		}
		foreach ( $tags as $tag => $methods ) {
			$this->register_filter( $class, $tag, $methods );
		}
	}

	/**
	 * @param string $class
	 * @param string $tag
	 * @param array $methods
	 */
	public function register_filter( $class, $tag, $methods ) {
		$tag = $this->app->utility->replace( $tag, [ 'prefix' => $this->get_filter_prefix() ] );
		if ( empty( $class ) || empty( $tag ) || ! is_array( $methods ) ) {
			return;
		}
		foreach ( $methods as $method => $params ) {
			if ( ! is_array( $params ) && is_string( $params ) ) {
				$method = $params;
				$params = [];
			}
			if ( empty( $method ) || ! is_string( $method ) ) {
				continue;
			}
			list( $priority, $accepted_args ) = $this->get_filter_params( $params );
			add_filter( $tag, function () use ( $class, $method ) {
				return $this->call_filter_callback( $class, $method, func_get_args() );
			}, $priority, $accepted_args );
		}
	}

	/**
	 * @since 2.4.2
	 *
	 * @param string $class
	 *
	 * @return false|\Technote|\Technote\Interfaces\Singleton
	 */
	private function get_target_app( $class ) {
		if ( ! isset( $this->_target_app[ $class ] ) ) {
			$app = false;
			if ( strpos( $class, '->' ) !== false ) {
				$app      = $this->app;
				$exploded = explode( '->', $class );
				foreach ( $exploded as $property ) {
					if ( isset( $app->$property ) ) {
						$app = $app->$property;
					} else {
						$app = false;
						break;
					}
				}
			} else {
				if ( isset( $this->app->$class ) ) {
					$app = $this->app->$class;
				}
			}
			if ( false === $app ) {
				if ( class_exists( $class ) && is_subclass_of( $class, '\Technote\Interfaces\Singleton' ) ) {
					try {
						/** @var \Technote\Interfaces\Singleton $class */
						$app = $class::get_instance( $this->app );
					} catch ( \Exception $e ) {
					}
				}
			}
			$this->_target_app[ $class ] = $app;
		}

		return $this->_target_app[ $class ];
	}

	/**
	 * @since 2.4.2
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	private function get_filter_params( $params ) {
		$priority      = 10;
		$accepted_args = 100;
		if ( is_array( $params ) ) {
			if ( count( $params ) >= 1 ) {
				$priority = $params[0];
			}
			if ( count( $params ) >= 2 ) {
				$accepted_args = $params[1];
			}
		}

		return [ $priority, $accepted_args ];
	}

	/**
	 * @since 2.4.2
	 *
	 * @param string $class
	 * @param string $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	private function call_filter_callback( $class, $method, $args ) {
		$result = empty( $args ) ? null : reset( $args );
		$app    = $this->get_target_app( $class );
		if ( empty( $app ) ) {
			return $result;
		}

		if ( $app->is_filter_callable( $method ) ) {
			return $app->filter_callback( $method, $args );
		}

		return $result;
	}
}
