<?php
/**
 * WP_Framework_Upgrade Classes Models Upgrade
 *
 * @version 0.0.14
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Upgrade\Classes\Models;

use Exception;
use WP_Framework_Core\Interfaces\Singleton;
use WP_Framework_Core\Traits\Loader;
use WP_Framework_Upgrade\Traits\Package;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Upgrade
 * @package WP_Framework_Upgrade\Classes\Models
 */
class Upgrade implements \WP_Framework_Core\Interfaces\Loader {

	use Loader, Package;

	/**
	 * upgrade
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function upgrade() {
		if ( ! $this->is_required_upgrade() ) {
			return;
		}
		$this->app->lock_process( 'upgrade', function () {
			$this->do_framework_action( 'start_upgrade' );
			$last_version = $this->get_last_upgrade_version();
			$this->set_last_upgrade_version();
			$plugin_version = $this->app->get_plugin_version();
			if ( empty( $last_version ) || version_compare( $last_version, $plugin_version, '>=' ) ) {
				$this->do_framework_action( 'finished_upgrade' );

				return;
			}

			$this->app->log( sprintf( $this->translate( 'upgrade: %s to %s' ), $last_version, $plugin_version ) );

			try {
				$upgrades = [];
				$count    = 0;
				foreach ( $this->get_class_list() as $class ) {
					/** @var \WP_Framework_Upgrade\Interfaces\Upgrade|Singleton $class */
					foreach ( $class->get_upgrade_methods() as $items ) {
						if ( ! is_array( $items ) ) {
							continue;
						}
						$version  = $this->app->array->get( $items, 'version' );
						$callback = $this->app->array->get( $items, 'callback' );
						if ( ! isset( $version ) || empty( $callback ) || ! is_string( $version ) ) {
							continue;
						}
						if ( version_compare( $version, $last_version, '<=' ) ) {
							continue;
						}
						if ( ! $this->is_closure( $callback ) && ! $class->is_filter_callable( $callback ) ) {
							continue;
						}
						$upgrades[ $version ][] = $this->is_closure( $callback ) ? $callback : function () use ( $class, $callback ) {
							$class->filter_callback( $callback, [] );
						};
						$count ++;
					}
				}

				$this->app->log( sprintf( $this->translate( 'total upgrade process count: %d' ), $count ) );

				if ( empty( $upgrades ) ) {
					$this->do_framework_action( 'finished_upgrade' );

					return;
				}

				uksort( $upgrades, 'version_compare' );
				foreach ( $upgrades as $version => $items ) {
					$this->app->log( sprintf( $this->translate( 'upgrade process count of version %s: %d' ), $version, count( $items ) ) );
					foreach ( $items as $item ) {
						call_user_func( $item );
					}
				}
			} catch ( Exception $e ) {
				$this->app->log( $e );
			}
			$this->do_framework_action( 'finished_upgrade' );
		} );
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
	 * @return bool
	 */
	protected function is_common_cache_class_settings() {
		return true;
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\WP_Framework_Upgrade\Interfaces\Upgrade';
	}

	/**
	 * @return string
	 */
	private function get_last_upgrade_version_option_key() {
		return 'last_upgrade_version';
	}

	/**
	 * @return mixed
	 */
	private function get_last_upgrade_version() {
		return $this->app->get_option( $this->get_last_upgrade_version_option_key() );
	}

	/**
	 * @return bool
	 */
	private function set_last_upgrade_version() {
		return $this->app->option->set( $this->get_last_upgrade_version_option_key(), $this->app->get_plugin_version() );
	}

	/**
	 * @return bool
	 */
	private function is_required_upgrade() {
		$version = $this->get_last_upgrade_version();

		return empty( $version ) || version_compare( $version, $this->app->get_plugin_version(), '<' );
	}
}
