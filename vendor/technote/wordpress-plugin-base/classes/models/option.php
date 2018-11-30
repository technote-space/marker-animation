<?php
/**
 * Technote Models Option
 *
 * @version 1.1.30
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Models;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Option
 * @package Technote\Models
 */
class Option implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Uninstall {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook, \Technote\Traits\Uninstall;

	/** @var array */
	private $options;

	/** @var bool $suspend_reload */
	private $suspend_reload = false;

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->reload_options();
	}

	/**
	 * reload options
	 */
	public function reload_options() {
		if ( $this->suspend_reload ) {
			return;
		}
		$this->options = wp_parse_args(
			$this->get_option(), []
		);
		$this->unescape_options();
	}

	/**
	 * @return array
	 */
	private function get_option() {
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		return get_option( $this->get_option_name(), [] );
	}

	/**
	 * @return string
	 */
	private function get_option_name() {
		return $this->apply_filters( 'get_option_name', $this->get_slug( 'option_name', '_options' ) );
	}

	/**
	 * unescape options
	 */
	private function unescape_options() {
		foreach ( $this->options as $key => $value ) {
			if ( is_string( $value ) ) {
				$this->options[ $key ] = stripslashes( htmlspecialchars_decode( $this->options[ $key ] ) );
			}
		}
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = '' ) {
		if ( array_key_exists( $key, $this->options ) ) {
			return $this->apply_filters( 'get_option', $this->options[ $key ], $key, $default );
		}

		return $this->apply_filters( 'get_option', $default, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function set( $key, $value ) {
		$this->reload_options();
		$suspend_reload        = $this->suspend_reload;
		$prev                  = isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
		$this->options[ $key ] = $value;
		if ( $prev !== $value ) {
			$this->suspend_reload = true;
			$this->do_action( 'changed_option', $key, $value, $prev );
			$this->suspend_reload = $suspend_reload;
		}

		return $this->save();
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function delete( $key ) {
		$this->reload_options();
		$suspend_reload = $this->suspend_reload;
		if ( array_key_exists( $key, $this->options ) ) {
			$prev = $this->options[ $key ];
			unset( $this->options[ $key ] );
			$this->suspend_reload = true;
			$this->do_action( 'deleted_option', $key, $prev );
			$this->suspend_reload = $suspend_reload;

			return $this->save();
		}

		return true;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return bool
	 */
	public function set_post_value( $key, $default = null ) {
		if ( ! isset( $_POST[ $key ] ) && ! isset( $default ) ) {
			return false;
		}
		$result = $this->set( $key, isset( $_POST[ $key ] ) ? $_POST[ $key ] : $default );
		$this->delete_hook_cache( preg_replace( '/^' . preg_quote( $this->get_filter_prefix(), '/' ) . '/', '', $key ) );

		return $result;
	}

	/**
	 * @return bool
	 */
	private function save() {
		$options = $this->options;
		foreach ( $options as $key => $value ) {
			if ( is_string( $value ) ) {
				$options[ $key ] = htmlspecialchars( $value );
			}
		}

		return update_option( $this->get_option_name(), $options );
	}

	/**
	 * clear option
	 */
	public function clear_option() {
		delete_option( $this->get_option_name() );
		$this->initialize();
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		delete_option( $this->get_option_name() );
	}
}
