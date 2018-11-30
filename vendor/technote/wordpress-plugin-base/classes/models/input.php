<?php
/**
 * Technote Models Input
 *
 * @version 1.1.58
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
 * Class Input
 * @package Technote\Models
 */
class Input implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/** @var array */
	private static $input = null;

	/** @var string */
	private static $php_input = null;

	/**
	 * @return array
	 */
	public function all() {
		if ( ! isset( self::$input ) ) {
			self::$input = array_merge( $_GET, $_POST );
		}

		return static::$input;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key = null, $default = null ) {
		return func_num_args() === 0 ? $_GET : Utility::array_get( $_GET, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function post( $key = null, $default = null ) {
		return func_num_args() === 0 ? $_POST : Utility::array_get( $_POST, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function request( $key = null, $default = null ) {
		return func_num_args() === 0 ? $_REQUEST : Utility::array_get( $_REQUEST, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function file( $key = null, $default = null ) {
		return func_num_args() === 0 ? $_FILES : Utility::array_get( $_FILES, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function cookie( $key = null, $default = null ) {
		return func_num_args() === 0 ? $_COOKIE : Utility::array_get( $_COOKIE, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function server( $key = null, $default = null ) {
		return func_num_args() === 0 ? $_SERVER : Utility::array_get( $_SERVER, strtoupper( $key ), $default );
	}

	/**
	 * @param string $default
	 *
	 * @return string
	 */
	public function ip( $default = '0.0.0.0' ) {
		return $this->server( 'HTTP_X_FORWARDED_FOR', $this->server( 'REMOTE_ADDR', $default ) );
	}

	/**
	 * @param string $default
	 *
	 * @return string
	 */
	public function user_agent( $default = '' ) {
		return $this->server( 'HTTP_USER_AGENT', $default );
	}

	/**
	 * @param string $default
	 *
	 * @return string
	 */
	public function method( $default = 'GET' ) {
		return strtoupper( $this->server( 'REQUEST_METHOD', $this->request( '_method', $default ) ) );
	}

	/**
	 * @return bool
	 */
	public function is_post() {
		return ! in_array( $this->method(), [
			'GET',
			'HEAD',
		] );
	}

	/**
	 * @return bool|string
	 */
	public function php_input() {
		if ( ! isset( self::$php_input ) ) {
			self::$php_input = file_get_contents( 'php://input' );
		}

		return self::$php_input;
	}

	/**
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_current_url( $args = [] ) {
		$url = $this->get_current_host() . $this->get_current_path();
		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	/**
	 * @return string
	 */
	public function get_current_host() {
		return ( is_ssl() ? "https://" : "http://" ) . $this->app->input->server( 'HTTP_HOST' );
	}

	/**
	 * @return string
	 */
	public function get_current_path() {
		return $this->app->input->server( 'REQUEST_URI' );
	}
}
