<?php
/**
 * Technote Classes Controller Test
 *
 * @version 2.3.1
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.3.1 Changed: not load test if not required
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Controllers\Admin;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Test
 * @package Technote\Classes\Controllers\Admin
 */
class Test extends Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->app->test->is_valid() ? $this->apply_filters( 'test_page_priority', defined( 'WP_DEBUG' ) && WP_DEBUG ? 900 : - 1 ) : - 1;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return $this->apply_filters( 'test_page_title', 'Test' );
	}

	/**
	 * post
	 */
	protected function post_action() {
		$action = $this->app->input->post( 'action' );
		if ( method_exists( $this, $action ) && is_callable( [ $this, $action ] ) ) {
			$this->$action();
		}
	}

	/**
	 * @return array
	 */
	public function get_view_args() {
		return [
			'tests' => $this->app->test->get_test_class_names(),
		];
	}

	/**
	 * do test
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function do_test() {
		foreach ( $this->app->test->do_tests() as list( $success, $result ) ) {
			$this->app->add_message( $result, 'test', ! $success, false );
		}
	}
}
