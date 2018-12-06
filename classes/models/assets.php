<?php
/**
 * @version 1.1.3
 * @author technote-space
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
	private function setup_assets() {
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

		foreach ( $this->get_setting_details() as $key => $setting ) {
			if ( ! empty( $setting['ignore'] ) ) {
				continue;
			}

			$value = $setting['attributes']['data-value'];
			if ( ! empty( $setting['attributes']['data-option_name'] ) ) {
				$name = $setting['attributes']['data-option_name'];
			} else {
				$name = $key;
			}
			if ( 'input/checkbox' === $setting['form'] ) {
				if ( ! empty( $value ) ) {
					if ( array_key_exists( 'data-option_value-true', $setting['attributes'] ) ) {
						$value = $setting['attributes']['data-option_value-true'];
					} else {
						$value = true;
					}
				} else {
					if ( array_key_exists( 'data-option_value-false', $setting['attributes'] ) ) {
						$value = $setting['attributes']['data-option_value-false'];
					} else {
						$value = false;
					}
				}
			}
			$options[ $name ] = $value;
		}

		return $options;
	}

	/**
	 * @return array
	 */
	public function get_setting_keys() {
		return [
			'is_valid'        => [
				'form' => 'input/checkbox',
				'args' => [
					'ignore' => true,
				],
			],
			'color'           => 'color',
			'thickness'       => 'input/text',
			'duration'        => 'input/text',
			'delay'           => 'input/text',
			'function'        => [
				'form' => 'select',
				'args' => [
					'options' => [
						'ease'        => $this->translate( 'ease' ),
						'linear'      => $this->translate( 'linear' ),
						'ease-in'     => $this->translate( 'ease-in' ),
						'ease-out'    => $this->translate( 'ease-out' ),
						'ease-in-out' => $this->translate( 'ease-in-out' ),
					],
				],
			],
			'bold'            => [
				'form' => 'input/checkbox',
				'args' => [
					'attributes' => [
						'data-option_name'        => 'font_weight',
						'data-option_value-true'  => 'bold',
						'data-option_value-false' => null,
					],
				],
			],
			'repeat'          => 'input/checkbox',
			'padding_bottom'  => 'input/text',
		];
	}

	/**
	 * @return array
	 */
	public function get_setting_details() {
		$args = [];
		foreach ( $this->get_setting_keys() as $key => $form ) {
			$args[ $key ] = $this->get_setting( $key, $form );
		}

		return $args;
	}

	/**
	 * @param string $name
	 * @param string $form
	 *
	 * @return array
	 */
	private function get_setting( $name, $form ) {
		$detail = $this->app->setting->get_setting( $name, true );
		$value  = $detail['value'];
		$ret    = [
			'id'         => $this->get_id_prefix() . $name,
			'class'      => 'marker-animation-option',
			'name'       => $this->get_name_prefix() . $name,
			'value'      => $value,
			'label'      => $this->translate( $detail['label'] ),
			'attributes' => [
				'data-value'   => $value,
				'data-default' => $detail['default'],
			],
			'form'       => $form,
			'detail'     => $detail,
		];
		if ( is_array( $form ) ) {
			$ret['form'] = $form['form'];
			$ret         = array_replace_recursive( $ret, isset( $form['args'] ) && is_array( $form['args'] ) ? $form['args'] : [] );
		} else {
			$ret['form'] = $form;
		}
		if ( \Technote\Models\Utility::array_get( $detail, 'type' ) === 'bool' ) {
			$ret['value'] = 1;
			! empty( $value ) and $ret['attributes']['checked'] = 'checked';
		}
		if ( $ret['form'] === 'select' ) {
			$ret['selected'] = $value;
			if ( ! empty( $ret['options'] ) && ! isset( $ret['options'][ $value ] ) ) {
				$ret['options'][ $value ] = $value;
			}
		}

		return $ret;
	}

	/**
	 * @return string
	 */
	public function get_id_prefix() {
		return $this->app->slug_name . '-';
	}

	/**
	 * @return string
	 */
	public function get_name_prefix() {
		return $this->get_filter_prefix();
	}
}