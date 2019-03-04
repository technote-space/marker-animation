<?php
/**
 * @version 1.6.11
 * @author Technote
 * @since 1.4.0
 * @since 1.5.0 Changed: ライブラリの変更 (#37)
 * @since 1.6.0 Changed: Gutenbergへの対応 (#3)
 * @since 1.6.6 Changed: フレームワークの更新 (#76)
 * @since 1.6.11 #85
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Models\Custom_Post;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

/**
 * Class Setting
 * @package Marker_Animation\Classes\Models\Custom_Post
 */
class Setting implements \Marker_Animation\Interfaces\Models\Custom_Post, \WP_Framework_Upgrade\Interfaces\Upgrade {

	use \Marker_Animation\Traits\Models\Custom_Post, \WP_Framework_Upgrade\Traits\Upgrade;

	/**
	 * setup assets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_assets() {
		global $typenow;
		if ( empty( $typenow ) || $typenow !== $this->get_post_type() ) {
			return;
		}

		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
		$assets->enqueue_marker_animation();
		$this->add_script_view( 'admin/script/custom_post/setting_list' );
	}

	/**
	 * @param int $post_id
	 * @param bool $is_selector
	 *
	 * @return string
	 */
	public function get_default_class( $post_id, $is_selector = true ) {
		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );

		return ( $is_selector ? '.' : '' ) . $assets->get_default_marker_animation_class() . '-' . $post_id;
	}

	/**
	 * @param array $params
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	protected function filter_edit_form_params(
		/** @noinspection PhpUnusedParameterInspection */
		$params, $post
	) {
		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets          = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
		$setting_details = $assets->get_setting_details( 'setting' );
		foreach ( $this->get_setting_list() as $key => $name ) {
			$params['columns'][ $key ]['args'] = $this->app->utility->array_get( $setting_details, $name );
			unset( $params['columns'][ $key ]['args']['name'] );
			unset( $params['columns'][ $key ]['args']['value'] );
			unset( $params['columns'][ $key ]['args']['selected'] );
			unset( $params['columns'][ $key ]['args']['attributes']['checked'] );
			$params['columns'][ $key ]['args']['attributes']['data-default'] = $params['columns'][ $key ]['args']['attributes']['data-value'];
			$params['columns'][ $key ]['default']                            = $params['columns'][ $key ]['args']['attributes']['data-default'];
			if ( empty( $params['columns'][ $key ]['args']['attributes']['data-option_name'] ) ) {
				$params['columns'][ $key ]['args']['attributes']['data-option_name'] = $name;
			}
		}
		$params['columns']['selector']['args']['attributes']['data-default'] = $this->get_default_class( $post->ID );
		$params['columns']['selector']['default']                            = $params['columns']['selector']['args']['attributes']['data-default'];

		$params['columns']['color']['form_type']    = 'color';
		$params['columns']['function']['form_type'] = 'select';
		$params['columns']['function']['options']   = $assets->get_animation_functions();
		$params['name_prefix']                      = $assets->get_name_prefix();
		if ( ! $this->app->utility->can_use_block_editor() ) {
			unset( $params['columns']['is_valid_button_block_editor'] );
		}

		return $params;
	}

	/**
	 * @since 1.6.0 #3
	 * @return array
	 */
	private function get_setting_list() {
		return [
			'is_valid'                     => 'is_valid',
			'color'                        => 'color',
			'thickness'                    => 'thickness',
			'duration'                     => 'duration',
			'delay'                        => 'delay',
			'function'                     => 'function',
			'is_font_bold'                 => 'bold',
			'is_repeat'                    => 'repeat',
			'padding_bottom'               => 'padding_bottom',
			'is_valid_button'              => 'is_valid_button',
			'is_valid_style'               => 'is_valid_style',
			'is_valid_button_block_editor' => 'is_valid_button_block_editor',
		];
	}

	/**
	 * @return null|string
	 */
	protected function get_post_column_title() {
		return $this->translate( 'Setting name' );
	}

	/**
	 * @since 1.6.0 #3
	 * @since 1.6.6 #76
	 * @return array
	 */
	protected function get_manage_posts_columns() {
		return [
			'is_valid' => function ( $value ) {
				return ! empty( $value ) ? $this->translate( 'Valid' ) : $this->translate( 'Invalid' );
			},
			'display'  => [
				'name'     => $this->translate( 'display' ),
				'callback' => function (
					/** @noinspection PhpUnusedParameterInspection */
					$value, $data, $post
				) {
					/** @var \Marker_Animation\Classes\Models\Assets $assets */
					$assets          = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
					$setting_details = $assets->get_setting_details( 'front' );
					$attributes      = [];
					$details         = [];
					foreach ( $this->get_setting_list() as $key => $name ) {
						$setting = $this->app->utility->array_get( $setting_details, $name );
						if ( empty( $setting ) ) {
							continue;
						}
						$is_default = '' === (string) ( $data[ $key ] );
						if ( in_array( $name, [
							'color',
							'thickness',
							'duration',
							'delay',
							'function',
							'bold',
							'padding_bottom',
						] ) ) {
							if ( $is_default ) {
								$details[ $setting['label'] ] = $this->translate( 'default' ) . " ({$setting['value']})";
							} else {
								if ( 'function' === $name ) {
									$details[ $setting['label'] ] = $this->translate( $data[ $key ] );
								} elseif ( 'bold' === $name ) {
									$details[ $setting['label'] ] = empty( $data[ $key ] ) ? $this->translate( 'No' ) : $this->translate( 'Yes' );
								} else {
									$details[ $setting['label'] ] = $data[ $key ];
								}
							}
						}
						$setting['attributes']['data-value'] = $is_default ? $setting['value'] : $data[ $key ];
						list( $name, $value ) = $assets->parse_setting( $setting, $name );
						$attributes[] = "data-ma_{$name}=\"{$value}\"";
					}

					return $this->get_view( 'admin/custom_post/setting/preview', [
						'attributes' => $attributes,
						'details'    => $details,
					] );
				},
				'unescape' => true,
			],
			'others'   => [
				'name'     => $this->translate( 'others' ),
				'callback' => function (
					/** @noinspection PhpUnusedParameterInspection */
					$value, $data, $post
				) {
					$details = [
						'repeat'                       => empty( $data['repeat'] ) ? $this->translate( 'No' ) : $this->translate( 'Yes' ),
						'is valid button'              => empty( $data['is_valid_button'] ) ? $this->translate( 'No' ) : $this->translate( 'Yes' ),
						'is valid style'               => empty( $data['is_valid_style'] ) ? $this->translate( 'No' ) : $this->translate( 'Yes' ),
						'is valid block editor button' => empty( $data['is_valid_button_block_editor'] ) ? $this->translate( 'No' ) : $this->translate( 'Yes' ),
						'selector'                     => $this->get_default_class( $post->ID ) . ( empty( $data['selector'] ) ? '' : ', ' . $data['selector'] ),
					];
					if ( ! $this->app->utility->can_use_block_editor() ) {
						unset( $details['is valid block editor button'] );
					}

					return $this->get_view( 'admin/custom_post/setting/others', [
						'details' => $details,
					] );
				},
				'unescape' => true,
			],
			'priority' => [
				'name'         => $this->translate( 'priority' ),
				'value'        => '',
				'sortable'     => true,
				'default_sort' => true,
				'hide'         => true,
			],
		];
	}

	/**
	 * @param \WP_Post $post
	 * @param array $params
	 */
	protected function before_output_edit_form(
		/** @noinspection PhpUnusedParameterInspection */
		$post, $params
	) {
		$this->setup_color_picker();

		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
		$assets->enqueue_marker_animation();
	}

	/**
	 * @param \WP_Post $post
	 */
	public function output_after_editor( \WP_Post $post ) {
		$this->get_view( 'admin/custom_post/setting/test', [], true );
	}

	/**
	 * @param string $key
	 *
	 * @return null|string
	 */
	protected function get_table_column_name( $key ) {
		if ( $key === 'post_title' ) {
			return $this->get_post_column_title();
		}

		return null;
	}

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $old
	 * @param array $new
	 */
	public function data_updated( $post_id, \WP_Post $post, array $old, array $new ) {
		$this->clear_options_cache();
	}

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $data
	 */
	public function data_inserted( $post_id, \WP_Post $post, array $data ) {
		$this->clear_options_cache();
	}

	/**
	 * @param int $post_id
	 */
	public function trash_post( $post_id ) {
		$this->clear_options_cache();
	}

	/**
	 * clear options cache
	 */
	private function clear_options_cache() {
		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
		$assets->clear_options_cache();
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $default
	 * @param array|null $post_array
	 *
	 * @return mixed
	 */
	protected function filter_post_field(
		/** @noinspection PhpUnusedParameterInspection */
		$key, $value, $default, $post_array
	) {
		if ( 'is_valid_button_block_editor' === $key ) {
			if ( ! $this->app->utility->can_use_block_editor() ) {
				return $this->app->input->post( $this->get_post_field_name( 'is_valid_button' ) );
			}
		}

		return $value;
	}

	/**
	 * @since 1.6.0 #3
	 *
	 * @param string $target
	 *
	 * @return array
	 */
	public function get_settings( $target ) {
		/** @var \Marker_Animation\Classes\Models\Assets $assets */
		$assets          = \Marker_Animation\Classes\Models\Assets::get_instance( $this->app );
		$setting_details = $assets->get_setting_details( $target );
		$settings        = [];
		foreach (
			$this->list_data( true, null, 1, [
				'is_valid' => 1,
			], [
				'priority' => 'ASC',
			] )['data'] as $data
		) {
			$options = [];
			foreach ( $this->get_setting_list() as $key => $name ) {
				$is_default = '' === (string) ( $data[ $key ] );
				if ( 'is_valid_button' === $name || 'is_valid_style' === $name || 'is_valid_button_block_editor' === $name ) {
					$options[ $name ] = $data[ $key ];
					continue;
				}
				$setting = $this->app->utility->array_get( $setting_details, $name );
				if ( empty( $setting ) ) {
					continue;
				}

				$setting['attributes']['data-value'] = $is_default ? $setting['value'] : $data[ $key ];
				list( $name, $value ) = $assets->parse_setting( $setting, $name );
				$options[ $name ] = $value;
			}
			/** @var \WP_Post $post */
			$post                = $data['post'];
			$options['selector'] = $this->get_default_class( $post->ID );
			$options['class']    = $this->get_default_class( $post->ID, false );
			! empty( $data['selector'] ) and $options['selector'] .= ', ' . $data['selector'];
			$settings[] = [
				'id'      => $post->ID,
				'options' => $options,
				'title'   => $post->post_title,
			];
		}

		return $settings;
	}

	/**
	 * @return array
	 */
	public function get_upgrade_methods() {
		return [
			[
				'version'  => '1.4.0',
				'callback' => function () {
					foreach (
						[
							1 => '#ff99b4',
							2 => '#99e3ff',
							3 => '#99ffa8',
						] as $k => $v
					) {
						$color = $this->app->get_option( $this->get_filter_prefix() . 'color' . $k, $v );
						empty( $color ) and $color = $v;
						$this->insert( [
							'post_title' => $this->translate( 'preset color' . $k ),
							'color'      => $color,
							'selector'   => ".marker-animation[data-ma_color{$k}]",
							'priority'   => 10 + $k,
						] );
					}
				},
			],
		];
	}
}
