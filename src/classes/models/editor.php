<?php
/**
 * @version 2.0.3
 * @author Technote
 * @since 1.0.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

/**
 * Class Editor
 * @package Marker_Animation\Classes\Models
 */
class Editor implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * enqueue css for gutenberg
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_block_editor_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}
		$this->enqueue_style( 'marker_animation-editor', 'gutenberg.css' );
		$this->enqueue_script( 'marker_animation-editor', 'marker-animation-gutenberg.min.js', [
			'wp-blocks',
			'wp-element',
			'wp-rich-text',
			'wp-components',
			'lodash',
		] );
		$this->localize_script( 'marker_animation-editor', 'markerAnimationParams', $this->get_editor_params() );
	}

	/**
	 * @return array
	 */
	private function get_editor_params() {
		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );

		return [
			'title'                      => $this->translate( 'Marker Animation' ),
			'detail_title'               => $this->translate( 'Marker Animation (detail setting)' ),
			'class'                      => $assets->get_default_marker_animation_class(),
			'details'                    => $assets->get_setting_details( 'editor' ),
			'settings'                   => $setting->get_settings( 'editor' ),
			'prefix'                     => $assets->get_data_prefix(),
			'default_icon'               => $this->get_img_url( 'icon-24x24.png' ),
			'append_nav_item_class_name' => true,
			'is_valid_detail_setting'    => $this->app->utility->compare_wp_version( '5.2', '>=' ),
		];
	}
}