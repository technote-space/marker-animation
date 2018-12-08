<?php
/**
 * Technote Interfaces Controller Api
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Interfaces\Controller;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Interface Api
 * @package Technote\Interfaces\Controller
 */
interface Api extends \Technote\Interfaces\Controller {

	/**
	 * @return string
	 */
	public function get_endpoint();

	/**
	 * @return string
	 */
	public function get_call_function_name();

	/**
	 * @return string
	 */
	public function get_method();

	/**
	 * @return array
	 */
	public function get_args_setting();

	/**
	 * @return bool
	 */
	public function is_valid();

	/**
	 * @return bool
	 */
	public function is_only_admin();

	/**
	 * @return bool
	 */
	public function is_only_front();

	/**
	 * @return false|string
	 */
	public function common_script();

	/**
	 * @return false|string
	 */
	public function admin_script();

	/**
	 * @return false|string
	 */
	public function front_script();

	/**
	 * @param \WP_REST_Request|array $params
	 *
	 * @return int|\WP_Error|\WP_REST_Response
	 */
	public function callback( $params );

}
