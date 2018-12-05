<?php
/**
 * @version 1.1.3
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Controllers\Admin;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Marker_Animation\Controllers\Admin
 */
class Dashboard extends \Technote\Controllers\Admin\Base {

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
		/** @var \Marker_Animation\Models\Assets $assets */
		$assets = \Marker_Animation\Models\Assets::get_instance( $this->app );
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

		$this->enqueue_script( $this->app->slug_name . '-marker_animation', 'marker-animation.min.js', [
			'jquery',
		] );

		/** @var \Marker_Animation\Models\Assets $assets */
		$assets = \Marker_Animation\Models\Assets::get_instance( $this->app );
		wp_localize_script( $this->app->slug_name . '-marker_animation', $assets->get_marker_object_name(), [ 'selector' => '' ] );
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		/** @var \Marker_Animation\Models\Assets $assets */
		$assets = \Marker_Animation\Models\Assets::get_instance( $this->app );
		return [
			'setting'     => $assets->get_setting_details(),
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
		if ( \Technote\Models\Utility::array_get( $detail, 'type' ) === 'bool' ) {
			$default = 0;
		}

		return $this->app->option->set_post_value( $this->get_filter_prefix() . $name, $default );
	}
}
