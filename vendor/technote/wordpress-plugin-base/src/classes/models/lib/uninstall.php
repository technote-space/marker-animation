<?php
/**
 * Technote Classes Models Lib Uninstall
 *
 * @version 2.0.2
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.0.2 Added: Uninstall priority
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
class Uninstall implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/** @var array $uninstall */
	private $uninstall = [];

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
	 * uninstall
	 */
	public function uninstall() {
		$uninstall = $this->uninstall;
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

		$this->uninstall = [];
	}

	/**
	 * @since 2.0.2 Added: Uninstall priority
	 *
	 * @param callable $callback
	 * @param int $priority
	 */
	public function add_uninstall( $callback, $priority = 10 ) {
		$this->uninstall[ $priority ][] = $callback;
	}

}
