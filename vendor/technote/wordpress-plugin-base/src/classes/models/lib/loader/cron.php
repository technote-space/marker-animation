<?php
/**
 * Technote Classes Models Lib Loader Cron
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
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
 * Class Cron
 * @package Technote\Classes\Models\Lib\Loader
 */
class Cron implements \Technote\Interfaces\Loader {

	use \Technote\Traits\Loader;

	/**
	 * initialized
	 */
	protected function initialized() {
		$this->get_class_list();
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Classes\Crons\Base';
	}

	/**
	 * @return array
	 */
	public function get_cron_class_names() {
		$list = $this->get_class_list();

		return array_keys( $list );
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Classes\\Crons',
			$this->app->define->lib_namespace . '\\Classes\\Crons',
		];
	}
}
