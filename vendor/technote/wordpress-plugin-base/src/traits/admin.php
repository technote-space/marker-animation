<?php
/**
 * Technote Traits Admin
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Admin
 * @package Technote\Traits\Controller
 * @property \Technote $app
 */
trait Admin {

	/**
	 * @return null|string|false
	 */
	public function get_capability() {
		return $this->app->get_config( 'capability', 'admin_capability', 'manage_options' );
	}

}
