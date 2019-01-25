<?php
/**
 * Technote Classes Models Lib Test
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.0.1 Fixed: change timing to set $app instance
 * @since 2.0.1 Changed: hide menu if there is no tests
 * @since 2.3.0 Changed: property access to getter access
 * @since 2.3.1 Changed: not load test if not required
 * @since 2.10.0 Changed: trivial change
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Test
 * @package Technote\Classes\Models\Lib
 */
class Test implements \Technote\Interfaces\Loader {

	use \Technote\Traits\Loader;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var bool $_is_valid
	 */
	private $_is_valid = false;

	/**
	 * initialize
	 */
	protected function initialize() {
		if ( ! class_exists( '\PHPUnit_TextUI_Command' ) ) {
			$autoload = $this->app->define->lib_vendor_dir . DS . 'autoload.php';
			if ( ! file_exists( $autoload ) ) {
				return;
			}
			/** @noinspection PhpIncludeInspection */
			require_once $this->app->define->lib_vendor_dir . DS . 'autoload.php';

			if ( ! class_exists( '\PHPUnit_TextUI_Command' ) ) {
				return;
			}
		}

		$this->_is_valid = true;
	}

	/**
	 * @return bool
	 */
	public function is_valid() {
		return $this->_is_valid && count( $this->get_tests() ) > 0;
	}

	/**
	 * @return array
	 */
	private function get_tests() {
		if ( ! $this->_is_valid ) {
			return [];
		}

		return $this->get_class_list();
	}

	/**
	 * @return array
	 */
	public function get_test_class_names() {
		return $this->app->utility->array_map( $this->get_tests(), 'get_class_name' );
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Classes\\Tests',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Classes\Tests\Base';
	}

	/**
	 * @return array
	 */
	public function do_tests() {
		if ( ! $this->_is_valid ) {
			return [];
		}

		\Technote\Classes\Tests\Base::set_app( $this->app );
		$results = [];
		foreach ( $this->get_tests() as $slug => $class ) {
			$results[] = $this->do_test( $class );
		}

		return $results;
	}

	/**
	 * @param \Technote\Classes\Tests\Base $class
	 *
	 * @return array
	 */
	private function do_test( $class ) {
		$suite = new \PHPUnit_Framework_TestSuite( $class->get_class_name() );
		$suite->setBackupGlobals( false );
		$result = $suite->run();

		$dump = [];
		foreach ( $result->topTestSuite()->tests() as $item ) {
			if ( $item instanceof \Technote\Interfaces\Test ) {
				$dump = array_merge( $dump, $item->get_dump_objects() );
			} elseif ( $item instanceof \PHPUnit_Framework_TestSuite_DataProvider ) {
				foreach ( $item->tests() as $item2 ) {
					if ( $item2 instanceof \Technote\Interfaces\Test ) {
						$dump = array_merge( $dump, $item2->get_dump_objects() );
					}
				}
			}
		}

		return [
			$result->wasSuccessful(),
			$this->get_view( 'admin/include/test_result', [
				'result' => $result,
				'class'  => $class,
				'dump'   => $dump,
			] ),
		];
	}
}
