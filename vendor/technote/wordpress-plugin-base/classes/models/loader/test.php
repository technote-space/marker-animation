<?php
/**
 * Technote Models Loader Test
 *
 * @version 1.1.22
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Models\Loader;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Test
 * @package Technote\Models\Loader
 */
class Test implements \Technote\Interfaces\Loader {

	use \Technote\Traits\Loader;

	/** @var bool $is_valid */
	private $is_valid = false;

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

		$this->is_valid = true;
		\Technote\Tests\Base::set_app( $this->app );
	}

	/**
	 * @return bool
	 */
	public function is_valid() {
		return $this->is_valid;
	}

	/**
	 * @return array
	 */
	private function get_tests() {
		if ( ! $this->is_valid ) {
			return [];
		}

		return $this->get_class_list();
	}

	/**
	 * @return array
	 */
	public function get_test_class_names() {
		return \Technote\Models\Utility::array_pluck( $this->get_tests(), 'class_name' );
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Tests',
			$this->app->define->lib_namespace . '\\Tests',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Tests\Base';
	}

	/**
	 * @return array
	 */
	public function do_tests() {
		if ( ! $this->is_valid ) {
			return [];
		}

		$results = [];
		foreach ( $this->get_tests() as $slug => $class ) {
			$results[] = $this->do_test( $class );
		}

		return $results;
	}

	/**
	 * @param \Technote\Tests\Base $class
	 *
	 * @return array
	 */
	private function do_test( $class ) {
		$suite = new \PHPUnit_Framework_TestSuite( $class->class_name );
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
