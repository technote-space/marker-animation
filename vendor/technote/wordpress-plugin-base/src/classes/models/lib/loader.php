<?php
/**
 * Technote Classes Models Lib Loader
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
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
 * @property \Technote\Classes\Models\Lib\Loader\Controller\Admin $admin
 * @property \Technote\Classes\Models\Lib\Loader\Controller\Api $api
 * @property \Technote\Classes\Models\Lib\Loader\Test $test
 * @property \Technote\Classes\Models\Lib\Loader\Cron $cron
 * @property \Technote\Classes\Models\Lib\Loader\Uninstall $uninstall
 */
class Loader implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/** @var \Technote\Classes\Models\Lib\Loader\Controller\Admin $admin */
	public $admin;

	/** @var \Technote\Classes\Models\Lib\Loader\Controller\Api $api */
	public $api;

	/** @var \Technote\Classes\Models\Lib\Loader\Test $test */
	public $test;

	/** @var \Technote\Classes\Models\Lib\Loader\Cron $cron */
	public $cron;

	/** @var \Technote\Classes\Models\Lib\Loader\Uninstall $uninstall */
	public $uninstall;

	/**
	 * initialize
	 */
	protected function initialize() {
		$scan_dir = $this->app->define->lib_src_dir . DS . 'classes' . DS . 'models' . DS . 'lib' . DS . 'loader';
		foreach ( $this->get_relative_namespaces( $scan_dir ) as $namespace ) {
			$class = $this->get_class( $namespace );
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

	/**
	 * @param string $dir
	 * @param string $relative
	 *
	 * @return array
	 */
	private function get_relative_namespaces( $dir, $relative = '' ) {
		$list = [];
		if ( is_dir( $dir ) ) {
			foreach ( scandir( $dir ) as $file ) {
				if ( $file === '.' || $file === '..' ) {
					continue;
				}

				$path = rtrim( $dir, DS ) . DS . $file;
				if ( is_file( $path ) ) {
					$list[] = $relative . ucfirst( $this->app->get_page_slug( $file ) );
				}
				if ( is_dir( $path ) ) {
					$list = array_merge( $list, $this->get_relative_namespaces( $path, $relative . ucfirst( $file ) . '\\' ) );
				}
			}
		}

		return $list;
	}

	/**
	 * @param string $namespace
	 *
	 * @return string
	 */
	private function get_class( $namespace ) {
		return $this->app->define->lib_namespace . '\\Classes\\Models\\Lib\\Loader\\' . $namespace;
	}
}
