<?php
/**
 * @version 1.0.5
 * @author technote
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
		foreach ( $this->get_setting_keys() as $key => $form ) {
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
		return [
			'setting'     => $this->get_setting_details(),
			'name_prefix' => $this->get_name_prefix(),
			'id_prefix'   => $this->get_id_prefix(),
		];
	}

	/**
	 * @return array
	 */
	private function get_setting_keys() {
		return [
			'is_valid'        => 'input/checkbox',
			'color'           => 'color',
			'thickness'       => 'input/text',
			'duration'        => 'input/text',
			'delay'           => 'input/text',
			'function'        => [
				'form' => 'select',
				'args' => [
					'options' => [
						'ease'        => 'ease',
						'linear'      => 'linear',
						'ease-in'     => 'ease-in',
						'ease-out'    => 'ease-out',
						'ease-in-out' => 'ease-in-out',
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
			'position_bottom' => 'input/text',
		];
	}

	/**
	 * @return array
	 */
	private function get_setting_details() {
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
			'label'      => $detail['label'],
			'attributes' => [
				'data-value'   => $value,
				'data-default' => $detail['default'],
			],
			'form'       => $form,
			'detail'     => $detail,
		];
		if ( is_array( $form ) ) {
			$ret['form'] = $form['form'];
			$ret         = array_replace_recursive( $ret, $form['args'] );
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
	private function get_id_prefix() {
		return $this->app->slug_name . '-';
	}

	/**
	 * @return string
	 */
	private function get_name_prefix() {
		return $this->get_filter_prefix();
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
