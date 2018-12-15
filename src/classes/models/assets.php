<?php
/**
 * @version 1.2.7
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.0
 * @since 1.2.7 Added: cache options
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Models;

/**
 * Class Assets
 * @package Marker_Animation\Classes\Models
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
		$this->localize_script( $this->app->slug_name . '-marker_animation', $this->get_marker_object_name(), $this->get_marker_options() );
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
	 * @return int
	 */
	public function get_preset_color_count() {
		return 3;
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
	 * @since 1.2.7
	 * @return string
	 */
	private function get_marker_options_cache_key() {
		return 'marker_options_cache';
	}

	/**
	 * @return string
	 */
	public function get_data_prefix() {
		return 'ma_';
	}

	/**
	 * @since 1.2.7 Added: cache options
	 * @return array
	 */
	public function get_marker_options() {
		if ( $this->apply_filters( 'is_valid_marker_options_cache' ) ) {
			$options = $this->app->get_option( $this->get_marker_options_cache_key() );
			if ( is_array( $options ) ) {
				return $options;
			}
			$options = $this->load_marker_options();
			$this->app->option->set( $this->get_marker_options_cache_key(), $options );
		} else {
			$options = $this->load_marker_options();
		}

		return $options;
	}

	/**
	 * @since 1.2.7
	 * @return array
	 */
	private function load_marker_options() {
		$options = [
			'version'            => $this->app->get_plugin_version(),
			'selector'           => $this->get_selector(),
			'prefix'             => $this->get_data_prefix(),
			'preset_color_count' => $this->get_preset_color_count(),
		];
		foreach ( $this->get_setting_details( 'front' ) as $key => $setting ) {
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
	 * clear cache when changed option
	 * @since 1.2.7
	 *
	 * @param string $key
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function changed_option( $key ) {
		if ( $this->app->utility->starts_with( $key, $this->get_filter_prefix() ) ) {
			$this->clear_options_cache();
		}
	}

	/**
	 * clear options cache
	 */
	private function clear_options_cache() {
		$this->app->option->delete( $this->get_marker_options_cache_key() );
	}

	/**
	 * @return array
	 */
	public function get_setting_keys() {
		return [
			'is_valid'       => [
				'form' => 'input/checkbox',
				'args' => [
					'target' => [ 'dashboard' ],
				],
			],
			'color'          => 'color',
			'color1'         => [
				'form' => 'color',
				'args' => [
					'ignore_editor' => true,
				],
			],
			'color2'         => [
				'form' => 'color',
				'args' => [
					'ignore_editor' => true,
				],
			],
			'color3'         => [
				'form' => 'color',
				'args' => [
					'ignore_editor' => true,
				],
			],
			'thickness'      => 'input/text',
			'duration'       => 'input/text',
			'delay'          => 'input/text',
			'function'       => [
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
			'bold'           => [
				'form' => 'input/checkbox',
				'args' => [
					'attributes' => [
						'data-option_name'        => 'font_weight',
						'data-option_value-true'  => 'bold',
						'data-option_value-false' => null,
					],
				],
			],
			'repeat'         => 'input/checkbox',
			'padding_bottom' => 'input/text',
		];
	}

	/**
	 * @param string $target
	 *
	 * @return array
	 */
	public function get_setting_details( $target ) {
		$args = [];
		foreach ( $this->get_setting_keys() as $key => $form ) {
			if ( is_array( $form ) && ! empty( $form['args']['target'] ) && ! in_array( $target, $form['args']['target'] ) ) {
				continue;
			}
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
			'detail'     => $detail,
		];
		if ( is_array( $form ) ) {
			$ret['form'] = $form['form'];
			$ret         = array_replace_recursive( $ret, isset( $form['args'] ) && is_array( $form['args'] ) ? $form['args'] : [] );
		} else {
			$ret['form'] = $form;
		}
		if ( $this->app->utility->array_get( $detail, 'type' ) === 'bool' ) {
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