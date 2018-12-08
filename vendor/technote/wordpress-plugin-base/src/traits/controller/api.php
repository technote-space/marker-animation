<?php
/**
 * Technote Traits Controller Api
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits\Controller;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Api
 * @package Technote\Traits\Controller
 * @property \Technote $app
 */
trait Api {

	use \Technote\Traits\Controller;

	/**
	 * @return string
	 */
	public abstract function get_endpoint();

	/**
	 * @return string
	 */
	public abstract function get_call_function_name();

	/**
	 * @return string
	 */
	public abstract function get_method();

	/**
	 * @return array
	 */
	public function get_args_setting() {
		return [];
	}

	/**
	 * @return bool
	 */
	public function is_valid() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function is_only_admin() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function is_only_front() {
		return false;
	}

	/**
	 * @return false|string
	 */
	public function common_script() {
		return false;
	}

	/**
	 * @return false|string
	 */
	public function admin_script() {
		return $this->common_script();
	}

	/**
	 * @return false|string
	 */
	public function front_script() {
		return $this->common_script();
	}

	/**
	 * @param \WP_REST_Request|array $params
	 *
	 * @return int|\WP_Error|\WP_REST_Response
	 */
	public function callback(
		/** @noinspection PhpUnusedParameterInspection */
		$params
	) {
		return new \WP_REST_Response( null, 404 );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_not_empty( $var ) {
		if ( empty( $var ) || ( is_string( $var ) && empty( trim( $var ) ) ) ) {
			return new \WP_Error( 400, $this->app->translate( 'Value is required.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param int $min
	 * @param int $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_number( $var, $min = null, $max = null ) {
		if ( ! is_string( $var ) && empty( $var ) ) {
			return new \WP_Error( 400, $this->app->translate( 'Value is required.' ) );
		}
		if ( ! is_int( $var ) && ( ! is_string( $var ) || ! ctype_digit( str_replace( '-', '', $var ) ) ) ) {
			return new \WP_Error( 400, $this->app->translate( 'Invalid format.' ) );
		}
		if ( isset( $min ) && $var < $min ) {
			return new \WP_Error( 400, $this->app->translate( 'Outside the range of allowed values.' ) );
		}
		if ( isset( $max ) && $var > $max ) {
			return new \WP_Error( 400, $this->app->translate( 'Outside the range of allowed values.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 * @param int $min
	 * @param int $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_positive_number( $var, $include_zero = false, $min = null, $max = null ) {
		$validate = $this->validate_number( $var, $min, $max );
		if ( true === $validate ) {
			if ( ( ! $include_zero && $var <= 0 ) || ( $include_zero && $var < 0 ) ) {
				return new \WP_Error( 400, $this->app->translate( 'Invalid format.' ) );
			}
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 * @param int $min
	 * @param int $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_negative_number( $var, $include_zero = false, $min = null, $max = null ) {
		$validate = $this->validate_number( $var, $min, $max );
		if ( true === $validate ) {
			if ( ( ! $include_zero && $var >= 0 ) || ( $include_zero && $var > 0 ) ) {
				return new \WP_Error( 400, $this->app->translate( 'Invalid format.' ) );
			}
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param string $table
	 * @param string $id
	 * @param string $field
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_exists( $var, $table, $id = 'id', $field = '*' ) {
		$validate = $this->validate_positive_number( $var );
		if ( true === $validate ) {
			if ( $this->app->db->select_count( $table, $field, [
					$id => $var,
				] ) <= 0 ) {
				return new \WP_Error( 400, $this->app->translate( 'Data does not exist.' ) );
			}
		}

		return $validate;
	}

	/**
	 * @param string $capability
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_authority( $capability ) {
		if ( ! $this->app->user_can( $capability ) ) {
			return new \WP_Error( 400, $this->app->translate( 'You have no authority.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param string $target
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_confirmation( $var, $target, $request ) {
		$validate = $this->validate_not_empty( $var );
		if ( true === $validate ) {
			$compare = $request->get_param( $target );
			if ( $compare !== $var ) {
				return new \WP_Error( 400, $this->app->translate( 'The confirmation value does not match.' ) );
			}
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param string $pattern
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_regex( $var, $pattern ) {
		if ( is_string( $var ) && preg_match( $pattern, $var ) > 0 ) {
			return true;
		}

		return new \WP_Error( 400, $this->app->translate( 'Invalid format.' ) );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_date( $var ) {
		return $this->validate_regex( $var, '#^\d{4}(-|/)\d{2}(-|/)\d{2}$#' );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_time( $var ) {
		return $this->validate_regex( $var, '#^\d{2}:\d{2}$#' );
	}

	/**
	 * @param mixed $var
	 * @param int $len
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_string_length( $var, $len ) {
		if ( empty( $var ) ) {
			return true;
		}
		if ( ! is_string( $var ) ) {
			return new \WP_Error( 400, $this->app->translate( 'Invalid format.' ) );
		}

		$var = trim( $var );
		if ( strlen( $var ) >= $len ) {
			return new \WP_Error( 400, $this->app->translate( 'Input value is too long' ) );
		}

		return true;
	}
}
