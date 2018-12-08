<?php
/**
 * Technote Classes Models Lib Loader Uninstall
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.1.69
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib\Loader;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Uninstall
 * @package Technote\Classes\Models\Lib\Loader
 */
class Uninstall implements \Technote\Interfaces\Loader {

	use \Technote\Traits\Loader;

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace,
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Interfaces\Uninstall';
	}
}
