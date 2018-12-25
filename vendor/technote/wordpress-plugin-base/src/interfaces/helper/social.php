<?php
/**
 * Technote Interfaces Helper Social
 *
 * @version 2.8.0
 * @author technote-space
 * @since 2.8.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Interfaces\Helper;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Interface Social
 * @package Technote\Interfaces\Helper
 */
interface Social extends \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	/**
	 * @return string
	 */
	public function get_service_name();

	/**
	 * @return array
	 */
	public function get_link_args();

	/**
	 * @return string
	 */
	public function get_link_contents();

	/**
	 * @return array
	 */
	public function get_oauth_settings();

	/**
	 * @return string|false
	 */
	public function get_oauth_link();

	/**
	 * @param array $params
	 *
	 * @return bool|false|int
	 */
	public function check_state_params( $params );

	/**
	 * @param string $code
	 * @param string $client_id
	 * @param string $client_secret
	 *
	 * @return false|string
	 */
	public function get_access_token( $code, $client_id, $client_secret );

	/**
	 * @param $access_token
	 *
	 * @return array|null
	 */
	public function get_user_info( $access_token );

	/**
	 * @param array $user
	 */
	public function register_or_login_customer( $user );
}
