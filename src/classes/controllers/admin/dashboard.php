<?php
/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.0
 * @since 1.3.0 Added: preset color
 * @since 1.4.0 Improved: refactoring
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Controllers\Admin;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Marker_Animation\Classes\Controllers\Admin
 */
class Dashboard extends \Technote\Classes\Controllers\Admin\Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return 0;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return 'Dashboard';
	}

	/**
	 * post
	 */
	protected function post_action() {
		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
		foreach ( $assets->get_setting_keys() as $key => $form ) {
			$this->update_setting( $key );
		}
		$this->app->add_message( 'Settings updated.', 'setting' );
	}

	/**
	 * common
	 */
	protected function common_action() {
		$this->setup_color_picker();

		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
		$assets->enqueue_marker_animation();
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );

		return [
			'setting'     => $assets->get_setting_details( 'dashboard' ),
			'name_prefix' => $assets->get_name_prefix(),
			'id_prefix'   => $assets->get_id_prefix(),
		];
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	private function update_setting( $name ) {
		$detail  = $this->app->setting->get_setting( $name, true );
		$default = null;
		if ( $this->app->utility->array_get( $detail, 'type' ) === 'bool' ) {
			$default = 0;
		}

		return $this->app->option->set_post_value( $this->get_filter_prefix() . $name, $default );
	}
}
