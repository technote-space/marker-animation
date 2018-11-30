<?php
/**
 * Technote Interfaces Loader
 *
 * @version 1.1.43
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
 * Interface Loader
 * @package Technote\Interfaces
 */
interface Loader extends Singleton, Hook, Presenter {

	/**
	 * @return string
	 */
	public function get_loader_name();

	/**
	 * @return array
	 */
	public function get_class_list();

	/**
	 * @return int
	 */
	public function get_loaded_count();

}
