<?php
/**
 * Technote Interfaces Controller Admin
 *
 * @version 1.1.68
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Interfaces\Controller;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Interface Admin
 * @package Technote\Interfaces\Controller
 */
interface Admin extends \Technote\Interfaces\Controller, \Technote\Interfaces\Nonce {

	/**
	 * @return string
	 */
	public function get_page_title();

	/**
	 * @return string
	 */
	public function get_menu_name();

	/**
	 * @param string $relative_namespace
	 */
	public function set_relative_namespace( $relative_namespace );

	/**
	 * @return string
	 */
	public function get_page_slug();

	/**
	 * @return string
	 */
	public function presenter();

	/**
	 * setup help
	 */
	public function setup_help();

}
