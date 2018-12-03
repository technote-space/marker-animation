<?php
/**
 * @version 1.0.0
 * @author technote
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Models;

/**
 * Class Editor
 * @package Marker_Animation\Models
 */
class Editor implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Presenter {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook, \Technote\Traits\Presenter;

	/**
	 * enqueue editor params
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_editor_params() {
		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$this->add_script_view( 'admin/script/editor', [
			'param_name' => 'marker_animation_params',
			'params'     => [
				'title' => $this->translate( 'Marker Animation' ),
				'text'  => $this->translate( 'Marker Animation' ),
				'class' => $assets->get_default_marker_animation_class(),
			],
		] );
	}

	/**
	 * @param array $external_plugins
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function mce_external_plugins( $external_plugins ) {
		$external_plugins['marker_animation_button_plugin'] = $this->get_assets_url( 'js/editor.js' );

		return $external_plugins;
	}

	/**
	 * @param array $mce_buttons
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function mce_buttons( $mce_buttons ) {
		$mce_buttons[] = 'marker_animation';

		return $mce_buttons;
	}

	/**
	 * @param array $stylesheets
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function editor_stylesheets( $stylesheets ) {
		$stylesheets[] = $this->get_assets_url( 'css/editor.css' );

		return $stylesheets;
	}
}