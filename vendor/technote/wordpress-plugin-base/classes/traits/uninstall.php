<?php
/**
 * Technote Traits Uninstall
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
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

}
