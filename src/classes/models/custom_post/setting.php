<?php
/**
 * @version 1.7.6
 * @author Technote
 * @since 1.4.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Models\Custom_Post;

use Marker_Animation\Classes\Models\Assets;
use Marker_Animation\Traits\Models\Custom_Post;
use WP_Framework_Db\Classes\Models\Query\Builder;
use WP_Framework_Upgrade\Traits\Upgrade;
use WP_Post;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

/**
 * Class Setting
 * @package Marker_Animation\Classes\Models\Custom_Post
 */
class Setting implements \Marker_Animation\Interfaces\Models\Custom_Post, \WP_Framework_Upgrade\Interfaces\Upgrade {

	use Custom_Post, Upgrade;

	/**
	 * insert presets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function insert_presets() {
		if ( $this->app->get_option( 'has_inserted_presets' ) ) {
			return;
		}
		$this->app->option->set( 'has_inserted_presets', true );

		if ( ! $this->is_empty() ) {
			return;
		}

		foreach ( $this->apply_filters( 'get_setting_presets', $this->app->get_config( 'preset' ) ) as $item ) {
			$item['post_title'] = $this->translate( $this->app->array->get( $item, 'name', '' ) );
			unset( $item['name'] );
			$this->insert( $item );
		}
	}

	/**
	 * setup assets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_assets() {
		global $typenow;
		if ( empty( $typenow ) || $typenow !== $this->get_post_type() ) {
			return;
		}

		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
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
		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );

		return ( $is_selector ? '.' : '' ) . $assets->get_default_marker_animation_class() . '-' . $post_id;
	}

	/**
	 * @param array $params
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	protected function filter_edit_form_params(
		/** @noinspection PhpUnusedParameterInspection */
		$params, $post
	) {
		/** @var Assets $assets */
		$assets          = Assets::get_instance( $this->app );
		$setting_details = $assets->get_setting_details( 'setting' );

		foreach ( $this->get_setting_list() as $key => $name ) {
			$args                              = $this->app->array->get( $setting_details, $name );
			$params['columns'][ $key ]['args'] = $args;
			unset( $params['columns'][ $key ]['args']['name'] );
			unset( $params['columns'][ $key ]['args']['value'] );
			unset( $params['columns'][ $key ]['args']['selected'] );
			unset( $params['columns'][ $key ]['args']['attributes']['checked'] );
			$params['columns'][ $key ]['args']['attributes']['data-default'] = $params['columns'][ $key ]['args']['attributes']['data-value'];
			$params['columns'][ $key ]['default']                            = $params['columns'][ $key ]['args']['attributes']['data-default'];
			if ( empty( $params['columns'][ $key ]['args']['attributes']['data-option_name'] ) ) {
				$params['columns'][ $key ]['args']['attributes']['data-option_name'] = $name;
			}
			$params['columns'][ $key ]['form_type'] = $this->app->array->get( $args, 'form' );
			$options                                = $this->app->array->get( $args, 'options' );
			$options and $params['columns'][ $key ]['options'] = $options;
			unset( $params['columns'][ $key ]['args']['options'] );
		}
		$params['columns']['selector']['args']['attributes']['data-default'] = $this->get_default_class( $post->ID );
		$params['columns']['selector']['default']                            = $params['columns']['selector']['args']['attributes']['data-default'];

		$params['name_prefix'] = $assets->get_name_prefix();
		$params['id_prefix']   = $assets->get_id_prefix();
		if ( ! $this->app->utility->can_use_block_editor() ) {
			unset( $params['columns']['is_valid_button_block_editor'] );
		}

		$params['target_selector']        = '.marker-animation-option';
		$params['marker_target_selector'] = '.marker-setting-preview .marker-animation';

		return $params;
	}

	/**
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
			'is_stripe'                    => 'stripe',
			'is_repeat'                    => 'repeat',
			'padding_bottom'               => 'padding_bottom',
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
					return $this->display_callback( $value, $data, $post );
				},
				'unescape' => true,
			],
			'others'   => [
				'name'     => $this->translate( 'others' ),
				'callback' => function (
					/** @noinspection PhpUnusedParameterInspection */
					$value, $data, $post
				) {
					return $this->others_callback( $value, $data, $post );
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
	 * @param mixed $value
	 * @param array $data
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	private function display_callback(
		/** @noinspection PhpUnusedParameterInspection */
		$value, $data, $post
	) {
		/** @var Assets $assets */
		$assets          = Assets::get_instance( $this->app );
		$setting_details = $assets->get_setting_details( 'front' );
		$attributes      = [];
		$details         = [];
		$translate       = [
			'Yes'     => $this->translate( 'Yes' ),
			'No'      => $this->translate( 'No' ),
			'default' => $this->translate( 'default' ),
		];
		$target          = $this->get_display_callback_target();
		foreach ( $this->get_setting_list() as $key => $name ) {
			$setting = $this->app->array->get( $setting_details, $name );
			if ( empty( $setting ) ) {
				continue;
			}
			$is_default = $this->is_default( $data[ $key ] );
			if ( in_array( $name, $target ) ) {
				list( $detail, $value ) = $this->get_display_detail( $key, $name, $data, $setting, $is_default, $translate );
				$details[ $setting['title'] ] = $detail;
			}
			$setting['attributes']['data-value'] = $is_default ? $value : $data[ $key ];
			list( $name, $value ) = $assets->parse_setting( $setting, $name );
			$attributes[] = "data-ma_{$name}=\"{$value}\"";
		}

		return $this->get_view( 'admin/custom_post/setting/preview', [
			'attributes' => $attributes,
			'details'    => $details,
		] );
	}

	/**
	 * @param string $key
	 * @param string $name
	 * @param array $data
	 * @param array $setting
	 * @param bool $is_default
	 * @param array $translate
	 *
	 * @return array
	 */
	private function get_display_detail( $key, $name, $data, $setting, $is_default, $translate ) {
		$value = $data[ $key ];
		if ( $is_default ) {
			if ( 'bool' === $setting['type'] ) {
				$value   = $this->app->array->get( $setting, 'attributes.checked' ) ? 1 : 0;
				$default = $value ? $translate['Yes'] : $translate['No'];
			} else {
				$value   = $setting['value'];
				$default = $value;
			}
			$detail = $translate['default'] . " ({$default})";
		} else {
			if ( 'function' === $name ) {
				$detail = $this->translate( $data[ $key ] );
			} elseif ( 'bool' === $setting['type'] ) {
				$detail = empty( $data[ $key ] ) ? $translate['No'] : $translate['Yes'];
			} else {
				$detail = $data[ $key ];
			}
		}

		return [ $detail, $value ];
	}

	/**
	 * @return array
	 */
	private function get_display_callback_target() {
		return [
			'color',
			'thickness',
			'duration',
			'delay',
			'function',
			'bold',
			'stripe',
			'repeat',
			'padding_bottom',
		];
	}

	/**
	 * @param mixed $value
	 * @param array $data
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	private function others_callback(
		/** @noinspection PhpUnusedParameterInspection */
		$value, $data, $post
	) {
		$details = [
			'is valid block editor button' => empty( $data['is_valid_button_block_editor'] ) ? $this->translate( 'No' ) : $this->translate( 'Yes' ),
			'selector'                     => $this->get_default_class( $post->ID ) . ( empty( $data['selector'] ) ? '' : ', ' . $data['selector'] ),
		];
		if ( ! $this->app->utility->can_use_block_editor() ) {
			unset( $details['is valid block editor button'] );
		}

		return $this->get_view( 'admin/custom_post/setting/others', [
			'details' => $details,
		] );
	}

	/**
	 * @param WP_Post $post
	 * @param array $params
	 */
	protected function before_output_edit_form(
		/** @noinspection PhpUnusedParameterInspection */
		$post, $params
	) {
		$this->setup_color_picker();

		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->enqueue_marker_animation();
	}

	/**
	 * @param WP_Post $post
	 */
	public function output_after_editor( WP_Post $post ) {
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
	 * @param WP_Post $post
	 * @param array $old
	 * @param array $new
	 */
	public function data_updated( $post_id, WP_Post $post, array $old, array $new ) {
		$this->clear_options_cache();
	}

	/**
	 * @param int $post_id
	 * @param WP_Post $post
	 * @param array $data
	 */
	public function data_inserted( $post_id, WP_Post $post, array $data ) {
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
		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->clear_options_cache();
	}

	/**
	 * @param string $target
	 *
	 * @return array
	 */
	public function get_settings( $target ) {
		/** @var Assets $assets */
		$assets          = Assets::get_instance( $this->app );
		$setting_details = $assets->get_setting_details( $target );
		$settings        = [];
		$setting_list    = $this->get_setting_list();
		foreach (
			$this->app->array->get( $this->get_list_data( function ( $query ) {
				/** @var Builder $query */
				$query->where( 'is_valid', 1 )
				      ->order_by( 'priority' );
			} ), 'data' ) as $data
		) {
			$options = [];
			foreach ( $setting_list as $key => $name ) {
				if ( 'is_valid_button_block_editor' === $name ) {
					$options[ $name ] = $data[ $key ];
					continue;
				}
				$setting = $this->app->array->get( $setting_details, $name );
				if ( empty( $setting ) ) {
					continue;
				}

				$setting['attributes']['data-value'] = $this->is_default( $data[ $key ] ) ? $this->app->array->get( $setting, 'detail.value' ) : $data[ $key ];
				list( $name, $value ) = $assets->parse_setting( $setting, $name );
				$options[ $name ] = $value;
			}
			/** @var WP_Post $post */
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
