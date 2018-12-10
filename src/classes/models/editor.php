<?php
/**
 * @version 1.2.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Models;

/**
 * Class Editor
 * @package Marker_Animation\Classes\Models
 */
class Editor implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Presenter {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook, \Technote\Traits\Presenter;

	/**
	 * @since 1.1.9
	 * @var bool $_gutenberg
	 */
	private $_gutenberg = false;

	/** @var bool $_setup_params */
	private $_setup_params = false;

	/**
	 * @since 1.1.7
	 * @var bool $_enqueue_editor_stylesheets
	 */
	private $_enqueue_editor_stylesheets = false;

	/**
	 * @since 1.1.9
	 *
	 * @param array $preload_paths
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function block_editor_preload_paths( $preload_paths ) {
		$this->_gutenberg = true;

		return $preload_paths;
	}

	/**
	 * enqueue editor params
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_editor_params() {
		$this->add_style_view( 'admin/style/editor' );
		if ( $this->_gutenberg ) {
			return;
		}

		$this->add_script_view( 'admin/script/editor', [
			'param_name' => 'marker_animation_params',
			'params'     => $this->get_editor_params(),
		] );
		$this->_setup_params = true;
	}

	/**
	 * @param array $external_plugins
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function mce_external_plugins( $external_plugins ) {
		if ( $this->_setup_params || $this->_gutenberg ) {
			$external_plugins['marker_animation_button_plugin'] = $this->get_assets_url( 'js/editor.js' );
		}

		return $external_plugins;
	}

	/**
	 * @param array $mce_buttons
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function mce_buttons( $mce_buttons ) {
		if ( $this->_setup_params || $this->_gutenberg ) {
			$mce_buttons[] = 'marker_animation';
			$mce_buttons[] = 'marker_animation_detail';
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
	 * enqueue css for gutenberg
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_block_editor_assets() {
		$this->enqueue_style( 'marker_animation-editor', 'gutenberg.css' );
		$this->enqueue_script( 'marker_animation-editor', 'gutenberg.js', [
			'wp-blocks',
			'wp-element',
			'wp-rich-text',
		] );
		$this->localize_script( 'marker_animation-editor', 'marker_animation_params', $this->get_editor_params() );
	}

	/**
	 * @return array
	 */
	private function get_editor_params() {
		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		global $wp_version;

		return [
			'title'               => $this->translate( 'Marker Animation' ),
			'detail_title'        => $this->translate( 'Marker Animation (detail setting)' ),
			'class'               => $assets->get_default_marker_animation_class(),
			'details'             => $assets->get_setting_details(),
			'prefix'              => 'ma_',
			'colorpicker_enabled' => version_compare( $wp_version, '4.0', '>=' ),
		];
	}
}