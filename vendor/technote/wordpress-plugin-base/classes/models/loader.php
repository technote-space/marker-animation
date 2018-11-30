<?php
/**
 * Technote Models Loader
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Models;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Loader
 * @package Technote\Models
 * @property \Technote\Models\Loader\Controller\Admin $admin
 * @property \Technote\Models\Loader\Controller\Api $api
 * @property \Technote\Models\Loader\Test $test
 * @property \Technote\Models\Loader\Cron $cron
 */
class Loader implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/** @var \Technote\Models\Loader\Controller\Admin $admin */
	public $admin;
	/** @var \Technote\Models\Loader\Controller\Api $api */
	public $api;
	/** @var \Technote\Models\Loader\Test $test */
	public $test;
	/** @var \Technote\Models\Loader\Cron $cron */
	public $cron;

	/**
	 * initialize
	 */
	protected function initialize() {
		foreach ( $this->get_relative_namespaces( $this->app->define->lib_classes_dir . DS . 'models' . DS . 'loader' ) as $namespace ) {
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
		return $this->app->define->lib_namespace . '\\Models\\Loader\\' . $namespace;
	}
}
