<?php
/**
 * Technote Classes Models Lib Upgrade
 *
 * @version 2.4.1
 * @author technote-space
 * @since 2.4.0
 * @since 2.4.1 Added: show_plugin_update_notices method
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Upgrade
 * @package Technote\Classes\Models\Lib
 */
class Upgrade implements \Technote\Interfaces\Loader {

	use \Technote\Traits\Loader;

	/**
	 * upgrade
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function upgrade() {
		if ( ! $this->is_required_upgrade() ) {
			return;
		}
		$last_version = $this->get_last_upgrade_version();
		$this->set_last_upgrade_version();

		$upgrades = [];
		foreach ( $this->get_class_list() as $class ) {
			/** @var \Technote\Interfaces\Upgrade $class */
			foreach ( $class->get_upgrade_methods() as $items ) {
				if ( ! is_array( $items ) ) {
					continue;
				}
				$version  = $this->app->utility->array_get( $items, 'version' );
				$callback = $this->app->utility->array_get( $items, 'callback' );
				if ( ! isset( $version ) || empty( $callback ) || ! is_string( $version ) ) {
					continue;
				}
				if ( $last_version && version_compare( $version, $last_version, '>=' ) ) {
					continue;
				}
				if ( ! is_callable( $callback ) && ( ! is_string( $callback ) || ! method_exists( $class, $callback ) || ! is_callable( [ $class, $callback ] ) ) ) {
					continue;
				}
				$upgrades[ $version ][] = is_callable( $callback ) ? $callback : [ $class, $callback ];
			}
		}
		if ( empty( $upgrades ) ) {
			return;
		}

		uksort( $upgrades, 'version_compare' );
		foreach ( $upgrades as $version => $items ) {
			foreach ( $items as $item ) {
				call_user_func( $item );
			}
		}
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace,
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Interfaces\Upgrade';
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

	/**
	 * show plugin upgrade notices
	 * @since 2.4.1
	 */
	public function show_plugin_update_notices() {
		add_action( 'in_plugin_update_message-' . $this->app->define->plugin_base_name, function ( $data ) {
			if ( ! empty( $data['upgrade_notice'] ) ) {
				$notices = (array) preg_split( '~[\r\n]+~', trim( $data['upgrade_notice'] ) );
				$this->get_view( 'admin/include/upgrade', [
					'notices' => $notices,
				], true );
			}
		}, 10, 2 );
	}
}
