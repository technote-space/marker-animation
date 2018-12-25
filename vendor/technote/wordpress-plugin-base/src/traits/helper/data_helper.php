<?php
/**
 * Technote Traits Helper Data Helper
 *
 * @version 2.9.6
 * @author technote-space
 * @since 2.8.0
 * @since 2.8.3 Changed: move parse_db_type to utility
 * @since 2.9.0 Changed: move validation methods to Validate
 * @since 2.9.6 Fixed: return null if param = null (sanitize_input)
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits\Helper;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Data_Helper
 * @package Technote\Traits\Helper
 * @property \Technote $app
 */
trait Data_Helper {

	/**
	 * @param array $data
	 * @param string $key
	 *
	 * @return bool
	 */
	protected function convert_to_bool( $data, $key ) {
		return ! empty( $data[ $key ] ) && $data[ $key ] !== '0' && $data[ $key ] !== 'false';
	}

	/**
	 * @param string $str
	 *
	 * @return string
	 */
	protected function the_content( $str ) {
		return apply_filters( 'the_content', $str );
	}

	/**
	 * @since 2.9.6 Fixed: return null if $param = null
	 *
	 * @param mixed $param
	 * @param string $type
	 *
	 * @return mixed
	 */
	protected function sanitize_input( $param, $type ) {
		if ( is_null( $param ) ) {
			return $param;
		}
		switch ( $type ) {
			case 'int':
				if ( ! is_int( $param ) && ! ctype_digit( ltrim( $param, '-' ) ) ) {
					return null;
				}
				$param -= 0;
				$param = (int) $param;
				break;
			case 'float':
			case 'number':
				if ( ! is_numeric( $param ) && ! ctype_alpha( $param ) ) {
					return null;
				}
				$param -= 0;
				break;
			case 'bool':
				if ( is_string( $param ) ) {
					$param = strtolower( trim( $param ) );
					if ( $param === 'true' ) {
						$param = 1;
					} elseif ( $param === 'false' ) {
						$param = 0;
					} elseif ( $param === '0' ) {
						$param = 0;
					} else {
						$param = ! empty( $param );
					}
				} else {
					$param = ! empty( $param );
				}
				break;
			default:
				break;
		}

		return $param;
	}
}
