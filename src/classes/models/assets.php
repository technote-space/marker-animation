<?php
/**
 * @version 2.0.0
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
 * Class Assets
 * @package Marker_Animation\Classes\Models
 */
class Assets implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * setup assets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}

		$this->enqueue_marker_animation();
	}

	/**
	 * clear cache when changed option
	 *
	 * @param string $key
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function changed_option( $key ) {
		if ( $this->app->string->starts_with( $key, $this->get_filter_prefix() ) ) {
			$this->clear_options_cache();
		}
	}

	/**
	 * enqueue marker animation
	 */
	public function enqueue_marker_animation() {
		$this->enqueue_script( $this->app->slug_name . '-marker_animation', 'marker-animation.min.js', [
			'jquery',
		] );
		$this->localize_script( $this->app->slug_name . '-marker_animation', $this->get_marker_object_name(), $this->get_marker_options() );
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
	 * @return array
	 */
	public function get_marker_options() {
		if ( $this->apply_filters( 'is_valid_marker_options_cache' ) ) {
			$options = $this->app->get_option( $this->get_marker_options_cache_key() );
			if ( is_array( $options ) && ! empty( $options['version'] ) && version_compare( $options['version'], $this->app->get_plugin_version(), '>=' ) ) {
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
	 * @return array
	 */
	private function load_marker_options() {
		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );
		$options = [
			'version'  => $this->app->get_plugin_version(),
			'selector' => $this->get_selector(),
			'prefix'   => $this->get_data_prefix(),
			'settings' => $setting->get_settings( 'front' ),
			'default'  => [],
		];
		foreach ( $this->get_setting_details( 'front' ) as $key => $setting ) {
			list( $name, $value ) = $this->parse_setting( $setting, $key );
			$options['default'][ $name ] = $value;
		}

		return $options;
	}

	/**
	 * @param array $setting
	 * @param string $key
	 *
	 * @return array
	 */
	public function parse_setting( $setting, $key ) {
		$value = $setting['attributes']['data-value'];
		if ( ! empty( $setting['attributes']['data-option_name'] ) ) {
			$name = $setting['attributes']['data-option_name'];
		} else {
			$name = $key;
		}
		if ( 'input/checkbox' === $setting['form'] ) {
			if ( ! empty( $value ) ) {
				if ( array_key_exists( 'data-option_value-1', $setting['attributes'] ) ) {
					$value = $setting['attributes']['data-option_value-1'];
				} else {
					$value = true;
				}
			} else {
				if ( array_key_exists( 'data-option_value-0', $setting['attributes'] ) ) {
					$value = $setting['attributes']['data-option_value-0'];
				} else {
					$value = false;
				}
			}
		}

		return [ $name, $value ];
	}

	/**
	 * clear options cache
	 */
	public function clear_options_cache() {
		$this->app->option->delete( $this->get_marker_options_cache_key() );
	}

	/**
	 * @return array
	 */
	public function get_animation_functions() {
		return $this->apply_filters( 'animation_functions', [
			'ease'        => $this->translate( 'ease' ),
			'linear'      => $this->translate( 'linear' ),
			'ease-in'     => $this->translate( 'ease-in' ),
			'ease-out'    => $this->translate( 'ease-out' ),
			'ease-in-out' => $this->translate( 'ease-in-out' ),
		] );
	}

	/**
	 * @return array
	 */
	public function get_setting_keys() {
		return [
			'is_valid'                     => [
				'form' => 'input/checkbox',
				'args' => [
					'target' => [ 'dashboard', 'setting' ],
				],
			],
			'color'                        => 'color',
			'thickness'                    => 'input/text',
			'duration'                     => 'input/text',
			'delay'                        => 'input/text',
			'function'                     => [
				'form'     => 'select',
				'args'     => [
					'options' => $this->get_animation_functions(),
				],
				'nullable' => true,
			],
			'bold'                         => [
				'form'     => 'input/checkbox',
				'args'     => [
					'attributes' => [
						'data-option_name'    => 'font_weight',
						'data-option_value-1' => 'bold',
						'data-option_value-0' => null,
					],
				],
				'nullable' => true,
			],
			'stripe'                       => [
				'form'     => 'input/checkbox',
				'nullable' => true,
			],
			'repeat'                       => [
				'form'     => 'input/checkbox',
				'nullable' => true,
			],
			'padding_bottom'               => 'input/text',
			'is_valid_button_block_editor' => [
				'form'   => 'input/checkbox',
				'args'   => [
					'target' => [ 'setting' ],
				],
				'detail' => [
					'value' => 1,
					'label' => $this->translate( 'show' ),
				],
			],
		];
	}

	/**
	 * @param string $target
	 * @param null|string $prefix
	 *
	 * @return array
	 */
	public function get_setting_details( $target, $prefix = null ) {
		$args = [];
		foreach ( $this->get_setting_keys() as $key => $form ) {
			if ( is_array( $form ) && ! empty( $form['args']['target'] ) && ! in_array( $target, $form['args']['target'] ) ) {
				continue;
			}
			$args[ $key ] = $this->get_setting( $key, $form, $prefix, $target );
		}

		return $args;
	}

	/**
	 * @param string $name
	 * @param string|array $form
	 * @param null|string $prefix
	 * @param string $target
	 *
	 * @return array
	 */
	private function get_setting( $name, $form, $prefix = null, $target = '' ) {
		$detail                             = $this->app->array->get( is_array( $form ) ? $form : [], 'detail', $this->app->setting->get_setting( $name, true ) );
		$value                              = $this->app->array->get( $detail, 'value' );
		$ret                                = [
			'id'         => $this->get_id_prefix() . $name,
			'class'      => 'marker-animation-option',
			'name'       => ( isset( $prefix ) ? $prefix : $this->get_name_prefix() ) . $name,
			'value'      => $value,
			'label'      => $this->translate( $this->app->array->get( $detail, 'label', $name ) ),
			'attributes' => [
				'data-value'   => $value,
				'data-default' => $this->app->array->get( $detail, 'default' ),
			],
			'detail'     => $detail,
			'type'       => $this->app->array->get( $detail, 'type', 'string' ),
			'nullable'   => is_array( $form ) && $this->app->array->get( $form, 'nullable' ),
		];
		$ret['title']                       = $ret['label'];
		$ret['attributes']['data-nullable'] = $ret['nullable'];

		if ( is_array( $form ) ) {
			$ret['form'] = $form['form'];
			$ret         = array_replace_recursive( $ret, isset( $form['args'] ) && is_array( $form['args'] ) ? $form['args'] : [] );
			if ( 'setting' === $target && $ret['nullable'] ) {
				$ret['form'] = 'select';
				empty( $ret['options'] ) and $ret['options'] = [];
				$ret['options'] = array_merge( [ null => 'default' ], $ret['options'] );
			}
		} else {
			$ret['form'] = $form;
		}
		if ( $this->app->array->get( $detail, 'type' ) === 'bool' ) {
			if ( $ret['form'] === 'select' ) {
				$ret['options']['1'] = 'Valid';
				$ret['options']['0'] = 'Invalid';
			} else {
				$ret['value'] = 1;
				! empty( $value ) and $ret['attributes']['checked'] = 'checked';
				$ret['label'] = $this->translate( 'Valid' );
			}
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
		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );

		return $setting->get_post_field_name_prefix();
	}
}