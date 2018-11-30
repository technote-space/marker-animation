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
 * Class Assets
 * @package Marker_Animation\Models
 */
class Assets implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Presenter {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook, \Technote\Traits\Presenter;

	/**
	 * setup assets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function wp_enqueue_scripts() {
		if ( ! $this->is_valid() ) {
			return;
		}

		$this->enqueue_script( $this->app->slug_name . '-marker_animation', 'marker-animation.min.js', [
			'jquery',
		] );

		wp_localize_script( $this->app->slug_name . '-marker_animation', $this->get_marker_object_name(), $this->get_marker_options() );
	}

	/**
	 * @return bool
	 */
	private function is_valid() {
		return $this->apply_filters( 'is_valid' );
	}

	/**
	 * @return string
	 */
	public function get_default_marker_animation_class() {
		return 'marker-animation';
	}

	/**
	 * @return string
	 */
	private function get_selector() {
		$selector = trim( $this->apply_filters( 'selector' ) );
		if ( ! empty( $selector ) ) {
			$selector .= ', ';
		}
		$selector .= '.' . $this->get_default_marker_animation_class();

		return $selector;
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	private function get_string_param( $key ) {
		$param = $this->apply_filters( $key );
		$param = preg_replace( '/[\x00-\x1F\x7F]/', '', $param );
		$param = str_replace( [ ' ', '　' ], '', $param );
		$param = trim( $param );

		return $param;
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	private function get_bool_param( $key ) {
		return $this->apply_filters( $key );
	}

	/**
	 * @return string
	 */
	public function get_marker_object_name() {
		return 'marker_animation';
	}

	/**
	 * @return array
	 */
	public function get_marker_options() {
		$options = [
			'selector' => $this->get_selector(),
		];
		foreach ( [ 'color', 'thickness', 'duration', 'delay', 'function', 'padding_bottom', 'position_bottom' ] as $key ) {
			$param = $this->get_string_param( $key );
			if ( ! empty( $param ) ) {
				$options[ $key ] = $param;
			}
		}
		if ( ! $this->get_bool_param( 'bold' ) ) {
			$options['font_weight'] = null;
		}
		if ( $this->get_bool_param( 'repeat' ) ) {
			$options['repeat'] = true;
		}

		return $options;
	}
}