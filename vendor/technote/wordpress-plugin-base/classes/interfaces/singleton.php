<?php
/**
 * Technote Interfaces Singleton
 *
 * @version 1.1.70
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Interfaces;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Interface Singleton
 * @package Technote\Interfaces
 */
interface Singleton {

	/**
	 * @param \Technote $app
	 *
	 * @return \Technote\Traits\Singleton
	 */
	public static function get_instance( \Technote $app );

	/**
	 * @param string $config_name
	 * @param string $suffix
	 *
	 * @return string
	 */
	public function get_slug( $config_name, $suffix = '-' );

	/**
	 * @param string $tag
	 * @param string $method
	 * @param string $priority
	 * @param string $accepted_args
	 */
	public function add_filter( $tag, $method, $priority, $accepted_args );

}
