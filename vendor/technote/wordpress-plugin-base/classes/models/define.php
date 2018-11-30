<?php
/**
 * Technote Models Define
 *
 * @version 1.1.37
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
 * Class Define
 * @package Technote\Models
 * @property string $plugin_name
 * @property string $plugin_file
 * @property string $plugin_namespace
 * @property string $plugin_dir
 * @property string $plugin_dir_name
 * @property string $plugin_base_name
 * @property string $lib_name
 * @property string $lib_namespace
 * @property string $lib_dir
 * @property string $lib_assets_dir
 * @property string $lib_classes_dir
 * @property string $lib_configs_dir
 * @property string $lib_views_dir
 * @property string $lib_language_dir
 * @property string $lib_language_rel_path
 * @property string $lib_vendor_dir
 * @property string $lib_assets_url
 * @property string $plugin_assets_dir
 * @property string $plugin_classes_dir
 * @property string $plugin_configs_dir
 * @property string $plugin_views_dir
 * @property string $plugin_languages_dir
 * @property string $plugin_languages_rel_path
 * @property string $plugin_logs_dir
 * @property string $plugin_url
 * @property string $plugin_assets_url
 */
class Define implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/** @var string $plugin_name */
	public $plugin_name;
	/** @var string $plugin_file */
	public $plugin_file;
	/** @var string $plugin_namespace */
	public $plugin_namespace;
	/** @var string $plugin_dir */
	public $plugin_dir;
	/** @var string $plugin_dir_name */
	public $plugin_dir_name;
	/** @var string $plugin_base_name */
	public $plugin_base_name;
	/** @var string $lib_name */
	public $lib_name;
	/** @var string $lib_namespace */
	public $lib_namespace;
	/** @var string $lib_dir */
	public $lib_dir;
	/** @var string $lib_assets_dir */
	public $lib_assets_dir;
	/** @var string $lib_classes_dir */
	public $lib_classes_dir;
	/** @var string $lib_configs_dir */
	public $lib_configs_dir;
	/** @var string $lib_views_dir */
	public $lib_views_dir;
	/** @var string $lib_language_dir */
	public $lib_language_dir;
	/** @var string $lib_language_rel_path */
	public $lib_language_rel_path;
	/** @var string $lib_vendor_dir */
	public $lib_vendor_dir;
	/** @var string $lib_assets_url */
	public $lib_assets_url;
	/** @var string $plugin_assets_dir */
	public $plugin_assets_dir;
	/** @var string $plugin_classes_dir */
	public $plugin_classes_dir;
	/** @var string $plugin_configs_dir */
	public $plugin_configs_dir;
	/** @var string $plugin_views_dir */
	public $plugin_views_dir;
	/** @var string $plugin_languages_dir */
	public $plugin_languages_dir;
	/** @var string $plugin_languages_rel_path */
	public $plugin_languages_rel_path;
	/** @var string $plugin_logs_dir */
	public $plugin_logs_dir;
	/** @var string $plugin_url */
	public $plugin_url;
	/** @var string $plugin_assets_url */
	public $plugin_assets_url;

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->plugin_name = $this->app->plugin_name;
		$this->plugin_file = $this->app->plugin_file;

		$this->plugin_namespace = ucwords( $this->plugin_name, '_' );
		$this->plugin_dir       = dirname( $this->plugin_file );
		$this->plugin_dir_name  = basename( $this->plugin_dir );
		$this->plugin_base_name = plugin_basename( $this->plugin_file );

		$cache = $this->app->get_shared_object( 'lib_defines_cache', 'all' );
		if ( ! isset( $cache ) ) {
			$cache                          = [];
			$cache['lib_name']              = TECHNOTE_PLUGIN;
			$cache['lib_dir']               = dirname( TECHNOTE_BOOTSTRAP );
			$cache['lib_namespace']         = ucfirst( $cache['lib_name'] );
			$cache['lib_assets_dir']        = $cache['lib_dir'] . DS . 'assets';
			$cache['lib_classes_dir']       = $cache['lib_dir'] . DS . 'classes';
			$cache['lib_configs_dir']       = $cache['lib_dir'] . DS . 'configs';
			$cache['lib_views_dir']         = $cache['lib_dir'] . DS . 'views';
			$cache['lib_language_dir']      = $cache['lib_dir'] . DS . 'languages';
			$cache['lib_language_rel_path'] = ltrim( str_replace( WP_PLUGIN_DIR, '', $cache['lib_language_dir'] ), DS );
			$cache['lib_vendor_dir']        = $cache['lib_dir'] . DS . 'vendor';
			$cache['lib_assets_url']        = plugins_url( 'assets', TECHNOTE_BOOTSTRAP );
			$this->app->set_shared_object( 'lib_defines_cache', $cache, 'all' );
		}
		foreach ( $cache as $k => $v ) {
			$this->$k = $v;
		}

		$this->plugin_assets_dir         = $this->plugin_dir . DS . 'assets';
		$this->plugin_classes_dir        = $this->plugin_dir . DS . 'classes';
		$this->plugin_configs_dir        = $this->plugin_dir . DS . 'configs';
		$this->plugin_views_dir          = $this->plugin_dir . DS . 'views';
		$this->plugin_languages_dir      = $this->plugin_dir . DS . 'languages';
		$this->plugin_languages_rel_path = ltrim( str_replace( WP_PLUGIN_DIR, '', $this->plugin_languages_dir ), DS );
		$this->plugin_logs_dir           = $this->plugin_dir . DS . 'logs';

		$this->plugin_url        = plugins_url( '', $this->plugin_file );
		$this->plugin_assets_url = $this->plugin_url . '/assets';
	}

}
