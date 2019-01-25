<?php
/**
 * Technote Classes Models Lib Config
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.10.0 Changed: trivial change
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Config
 * @package Technote\Classes\Models\Lib
 */
class Config implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_configs
	 */
	private $_configs = [];

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	public function load( $name ) {
		if ( ! isset( $this->_configs[ $name ] ) ) {
			$plugin_config           = $this->load_config_file( $this->app->define->plugin_configs_dir, $name );
			$lib_config              = $this->load_config_file( $this->app->define->lib_configs_dir, $name );
			$this->_configs[ $name ] = array_replace_recursive( $lib_config, $plugin_config );
		}

		return $this->_configs[ $name ];
	}

	/**
	 * @param string $name
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $name, $key, $default = null ) {
		return $this->app->utility->array_get( $this->load( $name ), $key, $default );
	}

	/**
	 * @param string $name
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $name, $key, $value ) {
		$this->load( $name );
		$this->app->utility->array_set( $this->_configs[ $name ], $key, $value );
	}

	/**
	 * @param string $dir
	 * @param string $name
	 *
	 * @return array|mixed
	 */
	private function load_config_file( $dir, $name ) {
		$path = rtrim( $dir, DS ) . DS . $name . '.php';
		if ( ! file_exists( $path ) ) {
			return [];
		}
		/** @noinspection PhpIncludeInspection */
		$config = include $path;
		if ( ! is_array( $config ) ) {
			$config = [];
		}

		return $config;
	}
}
