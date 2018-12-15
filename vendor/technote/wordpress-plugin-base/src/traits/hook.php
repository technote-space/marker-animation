<?php
/**
 * Technote Traits Hook
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Hook
 * @package Technote\Traits
 * @property \Technote $app
 */
trait Hook {

	/**
	 * load cache settings
	 */
	private function load_cache_settings() {
		if ( $this->app->isset_shared_object( 'is_valid_hook_cache' ) ) {
			return;
		}

		$this->app->set_shared_object( 'is_valid_hook_cache', ! empty( $this->app->get_config( 'config', 'cache_filter_result' ) ) );
		$prevent_cache = $this->app->get_config( 'config', 'cache_filter_exclude_list', [] );
		$prevent_cache = empty( $prevent_cache ) ? [] : array_combine(
			$prevent_cache,
			array_fill( 0, count( $prevent_cache ), true )
		);
		$this->app->set_shared_object( 'prevent_hook_cache', $prevent_cache );
		$this->app->set_shared_object( 'hook_cache', [] );
	}

	/**
	 * @return string
	 */
	protected function get_filter_prefix() {
		return $this->get_slug( 'filter_prefix', '' ) . '-';
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	private function add_hook_cache( $key, $value ) {
		$cache         = $this->app->get_shared_object( 'hook_cache' );
		$cache[ $key ] = $value;
		$this->app->set_shared_object( 'hook_cache', $cache );
	}

	/**
	 * @param string $key
	 */
	protected function delete_hook_cache( $key ) {
		$cache = $this->app->get_shared_object( 'hook_cache' );
		if ( ! empty( $cache ) && isset( $cache[ $key ] ) ) {
			unset( $cache[ $key ] );
			$this->app->set_shared_object( 'hook_cache', $cache );
		}
	}

	/**
	 * @param string $key
	 *
	 * @return array
	 */
	private function get_hook_cache( $key ) {
		$this->load_cache_settings();
		$prevent_cache  = $this->app->get_shared_object( 'prevent_hook_cache' );
		$is_valid_cache = ! isset( $prevent_cache[ $key ] ) && $this->app->get_shared_object( 'is_valid_hook_cache' );
		if ( ! $is_valid_cache ) {
			return [ false, null, $is_valid_cache ];
		}

		$cache = $this->app->get_shared_object( 'hook_cache' );
		if ( ! is_array( $cache ) || ! array_key_exists( $key, $cache ) ) {
			return [ false, null, $is_valid_cache ];
		}

		return [ true, $cache[ $key ], $is_valid_cache ];
	}

	/**
	 * @return mixed
	 */
	public function apply_filters() {
		$args = func_get_args();
		$key  = $args[0];

		list( $cache_is_valid, $cache, $is_valid_cache ) = $this->get_hook_cache( $key );
		if ( $cache_is_valid ) {
			return $cache;
		}

		$args[0] = $this->get_filter_prefix() . $key;
		if ( count( $args ) < 2 ) {
			$args[] = null;
		}
		$default = call_user_func_array( 'apply_filters', $args );

		if ( ! empty( $this->app->setting ) && $this->app->setting->is_setting( $key ) ) {
			$setting = $this->app->setting->get_setting( $key );
			$default = $this->app->utility->array_get( $setting, 'default', $default );
			if ( is_callable( $default ) ) {
				$default = $default( $this->app );
			}
			$value = $this->app->get_option( $args[0], null );
			if ( ! isset( $value ) || $value === '' ) {
				$value = $default;
			}

			$type = $this->app->utility->array_get( $setting, 'type', '' );
			if ( is_callable( [ $this, 'get_' . $type . '_value' ] ) ) {
				$value = call_user_func( [ $this, 'get_' . $type . '_value' ], $value, $default, $setting );
			}
			if ( ! empty( $setting['translate'] ) && $value === $default ) {
				$value = $this->app->translate( $value );
			}

			if ( $is_valid_cache ) {
				$this->add_hook_cache( $key, $value );
			}

			return $value;
		}

		if ( $is_valid_cache && count( $args ) <= 2 ) {
			$this->add_hook_cache( $key, $default );
		}

		return $default;
	}

	/**
	 * @param mixed $value
	 * @param mixed $default
	 * @param array $setting
	 *
	 * @return bool
	 */
	protected function get_bool_value(
		/** @noinspection PhpUnusedParameterInspection */
		$value, $default, $setting
	) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( 'true' === $value ) {
			return true;
		}
		if ( 'false' === $value ) {
			return false;
		}
		if ( isset( $value ) && (string) $value !== '' ) {
			return ! empty( $value );
		}

		return ! empty( $default );
	}

	/**
	 * @param mixed $value
	 * @param mixed $default
	 * @param array $setting
	 *
	 * @return int
	 */
	protected function get_int_value( $value, $default, $setting ) {
		$default = (int) $default;
		if ( is_numeric( $value ) ) {
			$value = (int) $value;
			if ( $value !== $default ) {
				if ( isset( $setting['min'] ) && $value < (int) $setting['min'] ) {
					$value = (int) $setting['min'];
				}
				if ( isset( $setting['max'] ) && $value > (int) $setting['max'] ) {
					$value = (int) $setting['max'];
				}
			} elseif ( isset( $setting['option'] ) ) {
				$default = isset( $setting['option_default'] ) ? (int) $setting['option_default'] : $default;
				$value   = (int) $this->app->get_option( $setting['option'], $default );
			}
		} else {
			$value = $default;
		}

		return $value;
	}

	/**
	 * @param mixed $value
	 * @param mixed $default
	 * @param array $setting
	 *
	 * @return float
	 */
	protected function get_float_value( $value, $default, $setting ) {
		$default = (float) $default;
		if ( is_numeric( $value ) ) {
			$value = (float) $value;
			if ( $value !== $default ) {
				if ( isset( $setting['min'] ) && $value < (float) $setting['min'] ) {
					$value = (float) $setting['min'];
				}
				if ( isset( $setting['max'] ) && $value > (float) $setting['max'] ) {
					$value = (float) $setting['max'];
				}
			} elseif ( isset( $setting['option'] ) ) {
				$default = isset( $setting['option_default'] ) ? (float) $setting['option_default'] : $default;
				$value   = (float) $this->app->get_option( $setting['option'], $default );
			}
		} else {
			$value = $default;
		}

		return $value;
	}

	/**
	 * do action
	 */
	public function do_action() {
		$args    = func_get_args();
		$args[0] = $this->get_filter_prefix() . $args[0];
		call_user_func_array( 'do_action', $args );
	}
}
