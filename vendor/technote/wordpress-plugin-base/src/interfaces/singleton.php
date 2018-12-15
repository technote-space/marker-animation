<?php
/**
 * Technote Interfaces Singleton
 *
 * @version 2.4.2
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.4.2 Added: is_filter_callable, filter_callback methods
 * @since 2.4.2 Deleted: add_filter method
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
interface Singleton extends Readonly {

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
	 * @param string $name
	 *
	 * @return bool
	 */
	public function is_filter_callable( $name );

	/**
	 * @param string $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function filter_callback( $method, $args );

}
