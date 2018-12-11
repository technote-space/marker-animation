<?php
/**
 * Technote Classes Models Lib Setting
 *
 * @version 2.1.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.1.0 Added: edit_setting function
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Setting
 * @package Technote\Classes\Models\Lib
 */
class Setting implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/** @var array */
	private $groups = [];

	/** @var array */
	private $group_priority = [];

	/** @var array */
	private $settings = [];

	/** @var array */
	private $setting_priority = [];

	/**
	 * initialize
	 */
	protected function initialize() {
		$data = $this->apply_filters( 'initialize_setting', $this->app->config->load( 'setting' ) );
		ksort( $data );
		foreach ( $data as $group_priority => $groups ) {
			foreach ( $groups as $group => $setting_set ) {
				ksort( $setting_set );

				$this->groups[ $group_priority ][ $group ] = [];
				$this->group_priority[ $group ]            = $group_priority;
				foreach ( $setting_set as $setting_priority => $settings ) {

					$this->groups[ $group_priority ][ $group ] = array_merge( $this->groups[ $group_priority ][ $group ], array_keys( $settings ) );
					foreach ( $settings as $setting => $detail ) {
						$this->settings[ $setting_priority ][ $setting ] = $detail;
						$this->setting_priority[ $setting ]              = $setting_priority;
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function get_groups() {
		return $this->apply_filters( 'get_groups', array_keys( $this->group_priority ) );
	}

	/**
	 * @param string $group
	 *
	 * @return array
	 */
	public function get_settings( $group ) {
		if ( ! isset( $this->group_priority[ $group ], $this->groups[ $this->group_priority[ $group ] ] ) ) {
			return $this->apply_filters( 'get_settings', [], $group );
		}

		return $this->apply_filters( 'get_settings', $this->groups[ $this->group_priority[ $group ] ][ $group ], $group );
	}

	/**
	 * @param string $setting
	 * @param bool $detail
	 *
	 * @return array|false
	 */
	public function get_setting( $setting, $detail = false ) {
		if ( ! $this->is_setting( $setting ) ) {
			return $this->apply_filters( 'get_setting', false, $setting, $detail );
		}

		$data = $this->apply_filters( 'get_setting', $this->settings[ $this->setting_priority[ $setting ] ][ $setting ], $setting );
		if ( $detail ) {
			$data = $this->get_detail_setting( $setting, $data );
		}

		return $data;
	}

	/**
	 * @param string $setting
	 *
	 * @return bool
	 */
	public function remove_setting( $setting ) {
		if ( ! $this->is_setting( $setting ) ) {
			return true;
		}

		$priority = $this->setting_priority[ $setting ];
		unset( $this->settings[ $priority ][ $setting ] );
		unset( $this->setting_priority[ $setting ] );
		if ( empty( $this->settings[ $priority ] ) ) {
			unset( $this->settings[ $priority ] );
		}
		foreach ( $this->groups as $group_priority => $groups ) {
			foreach ( $groups as $group => $settings ) {
				$key = array_search( $setting, $settings );
				if ( false !== $key ) {
					unset( $this->groups[ $group_priority ][ $group ][ $key ] );
					if ( empty( $this->groups[ $group_priority ][ $group ] ) ) {
						unset( $this->groups[ $group_priority ][ $group ] );
						unset( $this->group_priority[ $group ] );
						if ( empty( $this->groups[ $group_priority ] ) ) {
							unset( $this->groups[ $group_priority ] );
						}
					}
					break;
				}
			}
		}

		return true;
	}

	/**
	 * @since 2.1.0
	 *
	 * @param string $setting
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function edit_setting( $setting, $key, $value ) {
		if ( ! $this->is_setting( $setting ) ) {
			return true;
		}
		$priority                                        = $this->setting_priority[ $setting ];
		$this->settings[ $priority ][ $setting ][ $key ] = $value;

		return true;
	}

	/**
	 * @param string $setting
	 * @param array $data
	 *
	 * @return array
	 */
	private function get_detail_setting( $setting, $data ) {
		$data['key'] = $setting;
		$type        = $this->app->utility->array_get( $data, 'type', '' );
		$default     = $this->app->utility->array_get( $data, 'default', '' );
		if ( is_callable( $default ) ) {
			$default = $default( $this->app );
		}
		$default = $this->get_expression( $default, $type );
		if ( ! empty( $data['translate'] ) ) {
			$default = $this->app->translate( $default );
		}
		$data['info'] = [];
		if ( '' !== $default ) {
			$data['info'][] = $this->app->translate( 'default' ) . ' = ' . $default;
		}
		if ( isset( $data['min'] ) ) {
			$data['info'][] = $this->app->translate( 'min' ) . ' = ' . $this->get_expression( $data['min'], $type );
		}
		if ( isset( $data['max'] ) ) {
			$data['info'][] = $this->app->translate( 'max' ) . ' = ' . $this->get_expression( $data['max'], $type );
		}
		$data['name']        = $this->get_filter_prefix() . $data['key'];
		$data['saved']       = $this->app->get_option( $data['name'] );
		$data['placeholder'] = $default;
		$value               = $this->apply_filters( $data['key'], $default );
		$data['value']       = $value;
		$data['used']        = $this->get_expression( $value, $type );

		return $data;
	}

	/**
	 * @param $value
	 * @param $type
	 *
	 * @return mixed
	 */
	private function get_expression( $value, $type ) {
		switch ( $type ) {
			case 'bool':
				return var_export( $value, true );
			case 'int':
				return $value;
			case 'float':
				return round( $value, 6 );
			default:
				return $value;
		}
	}

	/**
	 * @param string $setting
	 *
	 * @return bool
	 */
	public function is_setting( $setting ) {
		return isset( $this->setting_priority[ $setting ], $this->settings[ $this->setting_priority[ $setting ] ] );
	}

}
