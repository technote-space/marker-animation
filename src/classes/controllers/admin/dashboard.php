<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Classes\Controllers\Admin;

use Marker_Animation\Classes\Models\Assets;
use WP_Framework_Admin\Classes\Controllers\Admin\Base;

// @codeCoverageIgnoreStart
if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}
// @codeCoverageIgnoreEnd

/**
 * Class Dashboard
 * @package Marker_Animation\Classes\Controllers\Admin
 */
class Dashboard extends Base {

	use \WP_Framework_Admin\Traits\Dashboard;

	/**
	 * @return Assets|string
	 */
	private function get_assets() {
		return Assets::get_instance( $this->app );
	}

	/**
	 * @return array
	 */
	protected function get_setting_list() {
		return $this->app->array->map(
			$this->app->array->filter( $this->get_assets()->get_setting_keys(), function ( $data ) {
				return ! is_array( $data ) || empty( $data['args']['target'] ) || in_array( 'dashboard', $data['args']['target'], true );
			} ),
			function ( $data ) {
				if ( ! is_array( $data ) ) {
					return [
						'form' => $data,
					];
				}
				if ( isset( $data['args']['options'] ) ) {
					$data['options'] = $data['args']['options'];
				}

				return $data;
			}
		);
	}

	/**
	 * after update
	 */
	protected function after_update() {
		$this->get_assets()->clear_options_cache();
	}

	/**
	 * after delete
	 */
	protected function after_delete() {
		$this->get_assets()->clear_options_cache();
	}

	/**
	 * common
	 */
	protected function common_action() {
		$this->setup_color_picker();
		$this->get_assets()->enqueue_marker_animation();
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	protected function filter_view_args( array $args ) {
		$args['name_prefix']            = $this->get_filter_prefix();
		$args['id_prefix']              = $this->get_assets()->get_id_prefix();
		$args['target_selector']        = '#' . $this->id( false ) . '-content-wrap .marker-animation-option';
		$args['marker_target_selector'] = '.marker-setting-preview span';

		return $args;
	}

	/**
	 * @param array $detail
	 * @param string $name
	 * @param array $option
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function filter_detail(
		/** @noinspection PhpUnusedParameterInspection */
		$detail, $name, array $option
	) {
		$detail['class']                      = 'marker-animation-option';
		$detail['attributes']['data-value']   = $this->app->array->get( $detail, 'value' );
		$detail['attributes']['data-default'] = $this->app->array->get( $detail, 'default' );
		if ( isset( $option['args']['attributes'] ) ) {
			$detail['attributes'] = $option['args']['attributes'];
		}
		$detail['attributes']['data-nullable'] = $this->app->array->get( $option, 'nullable', false );

		return $detail;
	}
}
