<?php
/**
 * Technote Classes Models Lib Uninstall
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.0.2 Added: Uninstall priority
 * @since 2.3.1 Changed: not load uninstall if not required
 * @since 2.6.0 Fixed: search uninstall file namespace
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
 * Class Uninstall
 * @package Technote\Classes\Models\Lib
 */
class Uninstall implements \Technote\Interfaces\Loader {

	use \Technote\Traits\Loader;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_uninstall
	 */
	private $_uninstall = [];

	/**
	 * register uninstall
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_uninstall() {
		register_uninstall_hook( $this->app->define->plugin_base_name, [
			"\Technote",
			"register_uninstall_" . $this->app->define->plugin_base_name,
		] );
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Classes',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Interfaces\Uninstall';
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$uninstall = $this->_uninstall;
		ksort( $uninstall );
		if ( ! is_multisite() ) {
			foreach ( $uninstall as $priority => $items ) {
				foreach ( $items as $item ) {
					if ( is_callable( $item ) ) {
						call_user_func( $item );
					}
				}
			}
		} else {
			/** @var \wpdb $wpdb */
			global $wpdb;
			$current_blog_id = get_current_blog_id();
			$blog_ids        = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				foreach ( $uninstall as $priority => $items ) {
					foreach ( $items as $item ) {
						if ( is_callable( $item ) ) {
							call_user_func( $item );
						}
					}
				}
			}
			switch_to_blog( $current_blog_id );
		}

		$this->_uninstall = [];
	}

	/**
	 * @since 2.0.2 Added: Uninstall priority
	 *
	 * @param callable $callback
	 * @param int $priority
	 */
	public function add_uninstall( $callback, $priority = 10 ) {
		$this->_uninstall[ $priority ][] = $callback;
	}
}
