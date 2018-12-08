<?php
/**
 * Technote Classes Controller Admin Setting
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0 Changed: translation
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Controllers\Admin;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Setting
 * @package Technote\Classes\Controllers\Admin
 */
class Setting extends Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->apply_filters( 'setting_page_priority', 0 );
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return $this->apply_filters( 'setting_page_title', 'Dashboard' );
	}

	/**
	 * post action
	 */
	protected function post_action() {
		foreach ( $this->app->setting->get_groups() as $group ) {
			foreach ( $this->app->setting->get_settings( $group ) as $setting ) {
				$this->app->option->set_post_value( $this->app->utility->array_get( $this->app->setting->get_setting( $setting, true ), 'name', '' ) );
			}
		}
		$this->app->add_message( 'Settings updated.', 'setting' );
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		$settings = [];
		foreach ( $this->app->setting->get_groups() as $group ) {
			foreach ( $this->app->setting->get_settings( $group ) as $setting ) {
				$settings[ $group ][ $setting ] = $this->app->setting->get_setting( $setting, true );
			}
		}

		return [
			'settings' => $settings,
		];
	}

	/**
	 * @return array
	 */
	protected function get_help_contents() {
		return [
			[
				'title' => 'The procedure for editing the help of dashboard',
				'view'  => 'setting1',
			],
			[
				'title' => 'The procedure for deleting this help if it is not needed',
				'view'  => 'setting2',
			],
			[
				'title' => 'The procedure for editing the sidebar',
				'view'  => 'setting3',
			],
		];
	}

	/**
	 * @return array
	 */
	protected function get_help_content_params() {
		return [
			'prefix' => $this->get_filter_prefix(),
		];
	}

	/**
	 * @return false|string
	 */
	protected function get_help_sidebar() {
		if (
			! empty( $this->app->get_config( 'config', 'contact_url' ) ) ||
			! empty( $this->app->get_config( 'config', 'twitter' ) ) ||
			! empty( $this->app->get_config( 'config', 'github' ) )
		) {
			return 'setting';
		}

		return false;
	}

}
