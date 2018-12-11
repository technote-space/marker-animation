<?php
/**
 * Technote Classes Models Lib Utility
 *
 * @version 2.1.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0 Changed: static function to non static function
 * @since 2.1.0 Added: starts_with, ends_with functions
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Utility
 * @package Technote\Classes\Models\Lib
 */
class Utility implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/**
	 * @param array $array
	 * @param bool $preserve_keys
	 *
	 * @return array
	 */
	public function flatten( array $array, $preserve_keys = false ) {
		$return = [];
		array_walk_recursive( $array, function ( $v, $k ) use ( &$return, $preserve_keys ) {
			if ( $preserve_keys ) {
				$return[ $k ] = $v;
			} else {
				$return[] = $v;
			}
		} );

		return $return;
	}

	/**
	 * @return string
	 */
	public function uuid() {
		$pid  = getmypid();
		$node = isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
		list( $timeMid, $timeLow ) = explode( ' ', microtime() );

		return sprintf( "%08x%04x%04x%02x%02x%04x%08x", (int) $timeLow, (int) substr( $timeMid, 2 ) & 0xffff,
			mt_rand( 0, 0xfff ) | 0x4000, mt_rand( 0, 0x3f ) | 0x80, mt_rand( 0, 0xff ), $pid & 0xffff, $node );
	}

	/**
	 * @param $c
	 *
	 * @return bool
	 */
	public function defined( $c ) {
		if ( defined( $c ) ) {
			$const = @constant( $c );
			if ( $const ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $c
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public function definedv( $c, $default = null ) {
		if ( defined( $c ) ) {
			$const = @constant( $c );

			return $const;
		}

		return $default;
	}

	/**
	 * @param array|object $obj
	 *
	 * @return array
	 */
	private function get_array_value( $obj ) {
		if ( $obj instanceof \stdClass ) {
			$obj = get_object_vars( $obj );
		} elseif ( ! is_array( $obj ) ) {
			if ( method_exists( $obj, 'to_array' ) ) {
				$obj = $obj->to_array();
			}
		}
		if ( ! is_array( $obj ) || empty( $obj ) ) {
			return [];
		}

		return $obj;
	}

	/**
	 * @param array|object $array
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function array_get( $array, $key, $default = null ) {
		$array = $this->get_array_value( $array );
		if ( array_key_exists( $key, $array ) ) {
			return $array[ $key ];
		}

		return $default;
	}

	/**
	 * @param array $array
	 * @param string $key
	 * @param mixed $value
	 */
	public function array_set( array &$array, $key, $value ) {
		$array[ $key ] = $value;
	}

	/**
	 * @param array|object $array
	 * @param string $key
	 * @param mixed $default
	 * @param bool $filter
	 *
	 * @return array
	 */
	public function array_pluck( $array, $key, $default = null, $filter = false ) {
		$array = $this->get_array_value( $array );

		return array_map( function ( $d ) use ( $key, $default ) {
			is_object( $d ) and $d = (array) $d;

			return is_array( $d ) && array_key_exists( $key, $d ) ? $d[ $key ] : $default;
		}, $filter ? array_filter( $array, function ( $d ) use ( $key ) {
			is_object( $d ) and $d = (array) $d;

			return is_array( $d ) && array_key_exists( $key, $d );
		} ) : $array );
	}

	/**
	 * @param array|object $array
	 * @param string $key
	 *
	 * @return array
	 */
	public function array_pluck_unique( $array, $key ) {
		return array_unique( $this->array_pluck( $array, $key, null, true ) );
	}

	/**
	 * @param array $array
	 * @param string $key
	 * @param string $value
	 *
	 * @return array
	 */
	public function array_combine( array $array, $key, $value = null ) {
		$keys   = $this->array_pluck( $array, $key );
		$values = empty( $value ) ? $array : $this->array_pluck( $array, $value );

		return array_combine( $keys, $values );
	}

	/**
	 * @param string $string
	 * @param array $data
	 *
	 * @return string
	 */
	public function replace( $string, $data ) {
		foreach ( $data as $k => $v ) {
			$string = str_replace( '${' . $k . '}', $v, $string );
		}

		return $string;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_time( $string ) {
		$time = $this->app->get_shared_object( '_replace_time', 'all' );
		if ( ! isset( $time ) ) {
			$time = [];
			foreach (
				[
					'Y',
					'y',
					'M',
					'm',
					'n',
					'D',
					'd',
					'H',
					'h',
					'i',
					'j',
					's',
				] as $t
			) {
				$time[ $t ] = date_i18n( $t );
			}
			$this->app->set_shared_object( '_replace_time', $time, 'all' );
		}

		return $this->replace( $string, $time );
	}

	/**
	 * @param string $data
	 * @param string $key
	 *
	 * @return false|string
	 */
	public function create_hash( $data, $key ) {
		return hash_hmac( function_exists( 'hash' ) ? 'sha256' : 'sha1', $data, $key );
	}

	/**
	 * @param string $command
	 *
	 * @return array
	 */
	public function exec( $command ) {
		$command .= ' 2>&1';
		$command = escapeshellcmd( $command );
		exec( $command, $output, $return_var );

		return [ $output, $return_var ];
	}

	/**
	 * @param string $command
	 */
	public function exec_async( $command ) {
		$command = escapeshellcmd( $command );
		if ( PHP_OS !== 'WIN32' && PHP_OS !== 'WINNT' ) {
			exec( $command . ' >/dev/null 2>&1 &' );
		} else {
			$fp = popen( 'start "" ' . $command, 'r' );
			pclose( $fp );
		}
	}

	/**
	 * @since 2.1.0
	 *
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
	public function starts_with( $haystack, $needle ) {
		if ( '' === $haystack || '' === $needle ) {
			return false;
		}
		if ( $haystack === $needle ) {
			return true;
		}

		return strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
	}

	/**
	 * @since 2.1.0
	 *
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
	public function ends_with( $haystack, $needle ) {
		if ( '' === $haystack || '' === $needle ) {
			return false;
		}
		if ( $haystack === $needle ) {
			return true;
		}

		return substr_compare( $haystack, $needle, - strlen( $needle ) ) === 0;
	}
}
