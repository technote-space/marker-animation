<?php
/**
 * Technote Traits Uninstall
 *
 * @version 2.0.2
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.0.2 Added: Uninstall priority
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Uninstall
 * @package Technote\Traits
 */
trait Uninstall {

	/**
	 * uninstall
	 */
	public abstract function uninstall();

	/**
	 * @since 2.0.2 Added: Uninstall priority
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 10;
	}
}
