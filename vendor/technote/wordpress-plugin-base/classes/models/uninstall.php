<?php
/**
 * Technote Models Uninstall
 *
 * @version 1.1.39
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
 * Class Uninstall
 * @package Technote\Models
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
		if ( ! is_multisite() ) {
			foreach ( $this->uninstall as $item ) {
				if ( is_callable( $item ) ) {
					call_user_func( $item );
				}
			}
		} else {
			/** @var \wpdb $wpdb */
			global $wpdb;
			$current_blog_id = get_current_blog_id();
			$blog_ids        = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				foreach ( $this->uninstall as $item ) {
					if ( is_callable( $item ) ) {
						call_user_func( $item );
					}
				}
			}
			switch_to_blog( $current_blog_id );
		}

		$this->uninstall = [];
	}

	/**
	 * @param $callback
	 */
	public function add_uninstall( $callback ) {
		$this->uninstall[] = $callback;
	}

}
