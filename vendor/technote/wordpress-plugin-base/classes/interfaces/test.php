<?php
/**
 * Technote Interfaces Test
 *
 * @version 1.1.13
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
 * Interface Test
 * @package Technote\Interfaces
 */
interface Test extends Singleton, Hook {

	/**
	 * @return string
	 */
	public function get_test_slug();

	/**
	 * @return bool
	 */
	public function has_dump_objects();

	/**
	 * @return array
	 */
	public function get_dump_objects();

}
