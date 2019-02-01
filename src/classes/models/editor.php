<?php
/**
 * @version 1.5.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.0
 * @since 1.2.4
 * @since 1.2.6 Updated: use library method to determine whether gutenberg editor is used
 * @since 1.2.6 Changed: variable name
 * @since 1.3.0 Added: preset color
 * @since 1.4.0 Deleted: preset color
 * @since 1.4.0 Added: marker setting feature
 * @since 1.5.0 Changed: ライブラリの変更 (#37)
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Models;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

/**
 * Class Editor
 * @package Marker_Animation\Classes\Models
 */
class Editor implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Common\Traits\Package;

	/** @var bool $_setup_params */
	private $_setup_params = false;

	/**
	 * @since 1.1.7
	 * @var bool $_enqueue_editor_stylesheets
	 */
	private $_enqueue_editor_stylesheets = false;

	/**
	 * enqueue editor params
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_editor_params() {
		$this->add_style_view( 'admin/style/editor' );
		if ( $this->app->utility->is_block_editor() ) {
			return;
		}

		$this->add_script_view( 'admin/script/params', [
			'param_name' => 'marker_animation_params',
			'params'     => $this->get_editor_params(),
		] );
		$this->add_script_view( 'admin/script/classic-editor' );
		$this->_setup_params = true;
	}

	/**
	 * @param array $external_plugins
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function mce_external_plugins( $external_plugins ) {
		if ( $this->_setup_params || $this->app->utility->is_block_editor() ) {
			$external_plugins['marker_animation_button_plugin'] = $this->get_assets_url( 'js/editor.js' );
		}

		return $external_plugins;
	}

	/**
	 * @since 1.3.1 Added: style button
	 *
	 * @param array $mce_buttons
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function mce_buttons( $mce_buttons ) {
		if ( $this->_setup_params || $this->app->utility->is_block_editor() ) {
			$mce_buttons[] = 'marker_animation';
			$mce_buttons[] = 'marker_animation_detail';

			/** @var Custom_Post\Setting $setting */
			$setting = Custom_Post\Setting::get_instance( $this->app );
			foreach ( $setting->get_settings( 'editor' ) as $setting ) {
				if ( $setting['options']['is_valid_button'] ) {
					$mce_buttons[] = 'marker_animation-' . $setting['id'];
				}
			}

			if ( ! in_array( 'styleselect', $mce_buttons ) ) {
				array_splice( $mce_buttons, 1, 0, 'styleselect' );
			}
		}

		return $mce_buttons;
	}

	/**
	 * @param array $stylesheets
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function editor_stylesheets( $stylesheets ) {
		if ( $this->_setup_params ) {
			$stylesheets[]                     = $this->get_assets_url( 'css/editor.css' );
			$this->_enqueue_editor_stylesheets = true;
		}

		return $stylesheets;
	}

	/**
	 * @since 1.1.7
	 *
	 * @param string $mce_css
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function mce_css( $mce_css ) {
		if ( $this->_setup_params && ! $this->_enqueue_editor_stylesheets ) {
			$mce_css .= ',' . $this->get_assets_url( 'css/editor.css' );
		}

		return $mce_css;
	}

	/**
	 * @since 1.3.0
	 * @since 1.3.1 Fixed: setup only when required parameter has loaded
	 *
	 * @param array $tinymce_settings
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function tiny_mce_before_init( $tinymce_settings ) {
		if ( $this->_setup_params || $this->app->utility->is_block_editor() ) {
			$style_formats = ! empty( $tinymce_settings['style_formats'] ) ? json_decode( $tinymce_settings['style_formats'], true ) : [];

			/** @var Custom_Post\Setting $setting */
			$setting         = Custom_Post\Setting::get_instance( $this->app );
			$marker_settings = [];
			foreach ( $setting->get_settings( 'editor' ) as $setting ) {
				if ( $setting['options']['is_valid_style'] ) {
					$marker_settings[] = [
						'title'  => $setting['title'],
						'inline' => 'span',
						'icon'   => 'icon highlight-icon',
						'cmd'    => 'marker_animation_preset_color' . $setting['id'],
					];
				}
			}
			if ( ! empty( $marker_settings ) ) {
				$style_formats[]                   = [
					'title' => $this->translate( 'Marker Animation' ),
					'items' => $marker_settings,
				];
				$tinymce_settings['style_formats'] = json_encode( $style_formats );
			}
		}

		return $tinymce_settings;
	}

	/**
	 * enqueue css for gutenberg
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_block_editor_assets() {
		$this->enqueue_style( 'marker_animation-editor', 'gutenberg.css' );
		$this->enqueue_script( 'marker_animation-editor', 'gutenberg.js', [
			'wp-blocks',
			'wp-element',
			'wp-rich-text',
			'jquery',
		] );
		$this->localize_script( 'marker_animation-editor', 'marker_animation_params', $this->get_editor_params() );
		$this->add_script_view( 'admin/script/classic-editor' );
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
			'title'                 => $this->translate( 'Marker Animation' ),
			'detail_title'          => $this->translate( 'Marker Animation (detail setting)' ),
			'class'                 => $assets->get_default_marker_animation_class(),
			'details'               => $assets->get_setting_details( 'editor' ),
			'settings'              => $setting->get_settings( 'editor' ),
			'prefix'                => $assets->get_data_prefix(),
			'is_valid_color_picker' => $this->app->utility->is_valid_tinymce_color_picker(),
		];
	}
}