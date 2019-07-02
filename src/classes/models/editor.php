<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;

// @codeCoverageIgnoreStart
if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}
// @codeCoverageIgnoreEnd

/**
 * Class Editor
 * @package Marker_Animation\Classes\Models
 */
class Editor implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * enqueue css for gutenberg
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function enqueue_block_editor_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}

		$handle  = 'marker_animation-editor';
		$depends = [
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-dom-ready',
			'wp-editor',
			'wp-element',
			'wp-format-library',
			'wp-hooks',
			'wp-i18n',
			'wp-rich-text',
			'wp-server-side-render',
			'wp-url',
		];
		foreach ( $depends as $key => $depend ) {
			if ( ! $this->app->editor->is_support_editor_package( $depend ) ) {
				unset( $depends[ $key ] );
			}
		}
		$depends[] = 'lodash';
		$this->enqueue_style( $handle, 'gutenberg.css' );
		$this->enqueue_script( $handle, 'gutenberg.min.js', $depends );
		$this->localize_script( $handle, 'markerAnimationParams', $this->get_editor_params() );
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
			'class'                => $assets->get_default_marker_animation_class(),
			'details'              => $assets->get_setting_details( 'editor' ),
			'settings'             => $setting->get_settings( 'editor' ),
			'prefix'               => $assets->get_data_prefix(),
			'defaultIcon'          => $this->get_img_url( 'icon-24x24.png' ),
			'isValidDetailSetting' => $this->app->utility->compare_wp_version( '5.2', '>=' ),
			'translate'            => $this->get_translate_data( [
				'Marker Animation',
				'Marker Animation (detail setting)',
				'Reset',
			] ),
		];
	}
}
