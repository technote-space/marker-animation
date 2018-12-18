<?php
/**
 * Technote Classes Models Lib Loader
 *
 * @version 2.6.1
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.3.0 Changed: public properties to readonly properties
 * @since 2.3.1 Changed: not load test and uninstall if not required
 * @since 2.6.1 Improved: refactoring
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Loader
 * @package Technote\Classes\Models\Lib
 * @property-read \Technote\Classes\Models\Lib\Loader\Controller\Admin $admin
 * @property-read \Technote\Classes\Models\Lib\Loader\Controller\Api $api
 * @property-read \Technote\Classes\Models\Lib\Loader\Cron $cron
 */
class Loader implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/**
	 * @since 2.3.0
	 * @var array $readonly_properties
	 */
	protected $readonly_properties = [
		'admin',
		'api',
		'cron',
	];

	/**
	 * initialize
	 */
	protected function initialize() {
		$scan_dir  = $this->app->define->lib_src_dir . DS . 'classes' . DS . 'models' . DS . 'lib' . DS . 'loader';
		$namespace = $this->app->define->lib_namespace . '\\Classes\\Models\\Lib\\Loader\\';
		foreach ( $this->app->utility->scan_dir_namespace_class( $scan_dir, false, $namespace ) as $class ) {
			if ( class_exists( $class ) && is_subclass_of( $class, '\Technote\Interfaces\Singleton' ) ) {
				try {
					/** @var \Technote\Traits\Singleton $class */
					$loader = $class::get_instance( $this->app );
					if ( $loader instanceof \Technote\Interfaces\Loader ) {
						/** @var \Technote\Interfaces\Loader $loader */
						$name        = $loader->get_loader_name();
						$this->$name = $loader;
					}
				} catch ( \Exception $e ) {
				}
			}
		}
	}
}
