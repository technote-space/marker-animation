<?php
/**
 * Technote Models Config
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Models;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Config
 * @package Technote\Models
 */
class Config implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/** @var array */
	private static $configs = [];

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	public function load( $name ) {
		if ( ! isset( static::$configs[ $this->app->plugin_name ][ $name ] ) ) {
			$plugin_config                                       = $this->load_config_file( $this->app->define->plugin_configs_dir, $name );
			$lib_config                                          = $this->load_config_file( $this->app->define->lib_configs_dir, $name );
			static::$configs[ $this->app->plugin_name ][ $name ] = array_replace_recursive( $lib_config, $plugin_config );
		}

		return static::$configs[ $this->app->plugin_name ][ $name ];
	}

	/**
	 * @param string $name
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $name, $key, $default = null ) {
		return Utility::array_get( $this->load( $name ), $key, $default );
	}

	/**
	 * @param string $name
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $name, $key, $value ) {
		$this->load( $name );
		Utility::array_set( static::$configs[ $this->app->plugin_name ][ $name ], $key, $value );
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
