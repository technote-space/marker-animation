<?php
/**
 * Technote Traits Upgrade
 *
 * @version 2.4.0
 * @author technote-space
 * @since 2.4.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Upgrade
 * @package Technote\Traits
 */
trait Upgrade {

	/**
	 * @return array {
	 *     array {
	 *         @type string $version target version
	 *         @type callable|string $callback upgrade callback or method name
	 *     }
	 * }
	 */
	public abstract function get_upgrade_methods();

}
